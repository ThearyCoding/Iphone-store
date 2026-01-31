<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Helpers\KHQRData;

class CheckoutController extends Controller
{
    public function index()
    {
        try {
            $itemIds = session('checkout_items', []);
            if (empty($itemIds)) {
                return redirect()->route('cart.index')->with('error', 'Please select items first');
            }

            $selectedItems = auth()->user()->cart()
                ->with('product.specs')
                ->whereIn('id', $itemIds)
                ->get();

            if ($selectedItems->isEmpty()) {
                session()->forget('checkout_items');
                return redirect()->route('cart.index')->with('error', 'Items no longer available');
            }

            $subtotal = $selectedItems->sum(fn($i) => ($i->product->discount_price ?? $i->product->price) * $i->quantity);
            $tax = $subtotal * 0.08;
            $total = round($subtotal + $tax, 2);

            return view('checkout.index', compact('selectedItems', 'subtotal', 'tax', 'total'));
        } catch (\Throwable $e) {
            Log::error('Checkout index error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            abort(500, 'Checkout error');
        }
    }

    public function placeOrder(Request $request)
    {
        Log::info('▶️ placeOrder start', [
            'user_id' => auth()->id(),
            'session_checkout_items' => session('checkout_items', []),
        ]);

        try {
            $itemIds = session('checkout_items', []);
            if (empty($itemIds)) {
                Log::warning('⛔ No checkout_items in session');
                return back()->with('error', 'No items to checkout');
            }

            $user = auth()->user();

            $cartItems = $user->cart()
                ->with('product')
                ->whereIn('id', $itemIds)
                ->get();

            Log::info('🛒 Cart items loaded', [
                'count' => $cartItems->count(),
                'ids' => $cartItems->pluck('id')->values(),
            ]);

            if ($cartItems->isEmpty()) {
                Log::warning('⛔ Cart items empty (not found)');
                return back()->with('error', 'Cart items not found');
            }

            $subtotal = $cartItems->sum(fn($i) => ($i->product->discount_price ?? $i->product->price) * $i->quantity);
            $tax = $subtotal * 0.08;
            $total = round($subtotal + $tax, 2);

            Log::info('💰 Totals calculated', compact('subtotal', 'tax', 'total'));

            // ✅ Do DB work in transaction, return ONLY order_id
            $orderId = DB::transaction(function () use ($user, $cartItems, $total) {

                Log::info('🧾 Creating order...');

                $order = Order::create([
                    'user_id'        => $user->id,
                    'total'          => $total,
                    'status'         => 'processing',
                    'payment_status' => 'pending',
                    'transaction_id' => 'txn_' . Str::random(15),
                ]);

                Log::info('✅ Order created', ['order_id' => $order->id]);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => ($item->product->discount_price ?? $item->product->price),
                    ]);
                }

                Log::info('📦 Order items inserted', ['order_id' => $order->id]);

                $accountId = env('BAKONG_ACCOUNT_ID');
                if (!$accountId) {
                    Log::error('❌ Missing BAKONG_ACCOUNT_ID');
                    throw new \Exception('Missing BAKONG_ACCOUNT_ID');
                }

                $merchantName = env('BAKONG_MERCHANT_NAME', 'Merchant');
                $merchantCity = env('BAKONG_MERCHANT_CITY', 'Phnom Penh');

                $currency = env('BAKONG_CURRENCY', 'USD') === 'KHR'
                    ? KHQRData::CURRENCY_KHR
                    : KHQRData::CURRENCY_USD;

                $info = new IndividualInfo(
                    bakongAccountID: $accountId,
                    merchantName: $merchantName,
                    merchantCity: $merchantCity,
                    currency: $currency,
                    amount: $total
                );

                Log::info('⚙️ Generating KHQR...', ['order_id' => $order->id]);

                $result = BakongKHQR::generateIndividual($info);

                Log::info('📤 KHQR result', [
                    'order_id' => $order->id,
                    'has_qr' => isset($result->data['qr']),
                    'has_md5' => isset($result->data['md5']),
                ]);

                if (!isset($result->data['qr'], $result->data['md5'])) {
                    Log::error('❌ KHQR returned no qr/md5', ['order_id' => $order->id]);
                    throw new \Exception('KHQR failed');
                }

                $order->update([
                    'khqr_qr'  => $result->data['qr'],
                    'khqr_md5' => $result->data['md5'],
                ]);

                Log::info('✅ KHQR saved', [
                    'order_id' => $order->id,
                    'md5' => $order->khqr_md5,
                    'qr_len' => strlen($order->khqr_qr ?? ''),
                ]);

                return $order->id;
            });

            Log::info('➡️ Transaction committed, redirecting to pay', ['order_id' => $orderId]);

            // ✅ redirect OUTSIDE transaction (guaranteed committed)
            return redirect()->route('checkout.pay', ['order' => $orderId]);
        } catch (\Throwable $e) {
            Log::error('🔥 placeOrder error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', $e->getMessage() ?: 'Order failed');
        }
    }


    public function pay(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);

        if (!$order->khqr_md5 || !$order->khqr_qr) {
            return redirect()->route('checkout.index')
                ->with('error', 'QR not generated');
        }

        return view('checkout.pay', compact('order'));
    }


    public function confirmPaid(Request $request, Order $order, TelegramNotificationService $telegram)
    {
        try {
            if ($order->user_id !== auth()->id()) abort(403);

            $request->validate(['md5' => 'required|string']);

            if ($order->khqr_md5 !== $request->md5) {
                return response()->json(['ok' => false, 'message' => 'MD5 mismatch'], 422);
            }

            if ($order->payment_status === 'paid') {
                return response()->json([
                    'ok' => true,
                    'redirect' => route('checkout.success', $order),
                ]);
            }

            $token = env('BAKONG_TOKEN');
            if (!$token) return response()->json(['ok' => false, 'message' => 'Missing BAKONG_TOKEN'], 500);

            $http = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ]);

            if (app()->environment('local')) {
                $http = $http->withOptions(['verify' => false]);
            }

            $res = $http->post('https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5', [
                'md5' => $order->khqr_md5,
            ]);

            if (!$res->successful()) {
                Log::error('Bakong API error', ['status' => $res->status(), 'body' => $res->body()]);
                return response()->json(['ok' => false, 'message' => 'Bakong API error'], 500);
            }

            $result = $res->json();
            $responseCode = $result['responseCode'] ?? null;

            if ($responseCode !== 0 || empty($result['data'])) {
                return response()->json(['ok' => false, 'pending' => true, 'message' => 'Payment pending']);
            }

            $bakong = $result['data'];

            return DB::transaction(function () use ($order, $bakong, $telegram) {

                $order->update([
                    'payment_status'    => 'paid',
                    'bakong_hash'       => $bakong['hash'] ?? ($bakong['transactionHash'] ?? null),
                    'paid_from_account' => $bakong['fromAccountId'] ?? null,
                    'paid_to_account'   => $bakong['toAccountId'] ?? null,
                    'paid_at'           => now(),
                ]);

                $itemIds = session('checkout_items', []);
                auth()->user()->cart()->whereIn('id', $itemIds)->delete();
                session()->forget('checkout_items');

                $items = $order->items()->with('product')->get()->map(function ($it) {
                    return [
                        'name'     => optional($it->product)->name ?? 'Product',
                        'quantity' => $it->quantity,
                        'price'    => $it->price,
                    ];
                })->toArray();

                $telegram->sendOrderNotification([
                    'items'            => $items,
                    'total'            => $order->total,
                    'paid_from_account' => $order->paid_from_account,
                    'paid_to_account'  => $order->paid_to_account,
                    'date'             => now()->format('d M Y, h:i A'),
                    'customer_name'    => auth()->user()->name ?? auth()->user()->username ?? 'Customer',
                    'email'            => auth()->user()->email ?? '-',
                    'phone'            => auth()->user()->phone ?? null,
                    'address'          => auth()->user()->address ?? '-',
                ]);

                return response()->json([
                    'ok'       => true,
                    'redirect' => route('checkout.success', $order),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('🔥 confirmPaid error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'message' => 'Server error'], 500);
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        return view('checkout.success', compact('order'));
    }
}
