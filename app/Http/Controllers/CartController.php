<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Product $product)
    {
        if (!auth()->check()) return redirect()->route('login');

        $cartItem = auth()->user()->cart()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            auth()->user()->cart()->create([
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        return back()->with('success', 'Added to cart!');
    }
public function updateQty(Request $request, Cart $cart)
{
    $qty = max(1, (int) $request->quantity);
    $cart->update(['quantity' => $qty]);
    return back();
}


    public function index(Request $request)
    {
        $cartItems = auth()->user()->cart()->with('product.specs')->get();

        $selectedItems = (array) session('checkout_items', []);

        return view('cart', compact('cartItems', 'selectedItems'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $items = (array) $request->input('items', []);

        if ($action === 'update' || $action === 'checkout') {
            session(['checkout_items' => $items]);
        }

        if ($action === 'checkout') {
            if (empty($items)) {
                return back()->with('error', 'Please select at least one item');
            }
            return redirect()->route('checkout.index');
        }

        return back();
    }

    public function remove(Cart $cart)
    {
        $cart->delete();
        return back();
    }
}