@extends('layouts.minimal')
@section('title', 'Order Details')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">

  <div class="mb-6">
    <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back</a>
  </div>

  @php
    $status = $order->status ?? 'pending';
    $pay = $order->payment_status ?? 'unpaid';

    $statusClass = match($status) {
      'completed' => 'bg-green-50 text-green-700 border-green-100',
      'processing' => 'bg-blue-50 text-blue-700 border-blue-100',
      'cancelled' => 'bg-red-50 text-red-700 border-red-100',
      default => 'bg-gray-50 text-gray-700 border-gray-100'
    };

    $payClass = match($pay) {
      'paid' => 'bg-green-50 text-green-700 border-green-100',
      'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
      default => 'bg-gray-50 text-gray-700 border-gray-100'
    };
  @endphp

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Items --}}
    <div class="lg:col-span-2">
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-600 mt-1">
              Placed on {{ $order->created_at->format('M d, Y • h:i A') }}
            </p>
          </div>

          <div class="flex items-center gap-2 flex-wrap justify-end">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-xl border {{ $statusClass }}">
              {{ ucfirst($status) }}
            </span>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-xl border {{ $payClass }}">
              {{ ucfirst($pay) }}
            </span>
          </div>
        </div>

        <div class="mt-5 divide-y divide-gray-200">
          @foreach($order->items as $it)
            @php
              $p = $it->product;
              $img = $p?->image
                ? $p->image
                : 'https://via.placeholder.com/600x750/1e293b/ffffff?text=' . urlencode($p?->name ?? 'Product');

              $line = $it->price * $it->quantity;
            @endphp

            <div class="py-4 flex gap-4">
              <div class="w-20 shrink-0">
                <div class="aspect-[4/5] bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center p-2 overflow-hidden">
                  <img src="{{ $img }}" alt="{{ $p?->name }}" class="max-w-full max-h-full object-contain">
                </div>
              </div>

              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 line-clamp-2">
                  {{ $p?->name ?? 'Product removed' }}
                </p>
                <p class="text-xs text-gray-600 mt-1">
                  {{ $p?->specs?->color ?? 'Various' }}
                  <span class="mx-1.5">•</span>
                  {{ $p?->specs?->storage ?? '128GB+' }}
                </p>
                <p class="text-xs text-gray-500 mt-2">
                  Qty: <span class="font-semibold text-gray-700">{{ $it->quantity }}</span>
                </p>
              </div>

              <div class="text-right shrink-0">
                <p class="text-sm font-semibold text-gray-900">
                  ${{ number_format($line, 2) }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  ${{ number_format($it->price, 2) }} each
                </p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- RIGHT: Summary --}}
    <div class="lg:col-span-1">
      <div class="bg-white border border-gray-200 rounded-2xl p-5 sticky top-24">
        <h2 class="text-base font-semibold text-gray-900">Summary</h2>

        <div class="mt-4 space-y-2 text-sm">
          <div class="flex justify-between text-gray-600">
            <span>Total</span>
            <span class="font-semibold text-gray-900">${{ number_format($order->total, 2) }}</span>
          </div>

          @if($order->transaction_id)
            <div class="flex justify-between text-gray-600">
              <span>Transaction</span>
              <span class="font-semibold text-gray-900">{{ $order->transaction_id }}</span>
            </div>
          @endif

          @if($order->paid_at)
            <div class="flex justify-between text-gray-600">
              <span>Paid at</span>
              <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($order->paid_at)->format('M d, Y') }}</span>
            </div>
          @endif

          @if($order->paid_from_account)
            <div class="flex justify-between text-gray-600">
              <span>From</span>
              <span class="font-semibold text-gray-900">{{ $order->paid_from_account }}</span>
            </div>
          @endif

          @if($order->paid_to_account)
            <div class="flex justify-between text-gray-600">
              <span>To</span>
              <span class="font-semibold text-gray-900">{{ $order->paid_to_account }}</span>
            </div>
          @endif
        </div>

        {{-- Optional actions: Pay / View QR --}}
        @if(($order->payment_status ?? 'unpaid') !== 'paid' && $order->khqr_qr)
          <a href="{{ route('checkout.pay', $order) }}"
             class="mt-5 w-full inline-flex items-center justify-center bg-gray-900 text-white py-3 rounded-xl font-semibold hover:bg-black transition">
            Pay Now
          </a>
        @endif

        <a href="{{ route('orders.index') }}"
           class="mt-3 w-full inline-flex items-center justify-center border border-gray-300 text-gray-900 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
          Back to Orders
        </a>
      </div>
    </div>

  </div>
</div>
@endsection
