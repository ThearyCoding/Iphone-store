@extends('layouts.minimal')
@section('title', 'My Orders')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">My Orders</h1>
      <p class="text-sm text-gray-600 mt-1">Your purchase history and payment status.</p>
    </div>

    <a href="{{ route('home') }}"
       class="hidden sm:inline-flex items-center justify-center border border-gray-300 text-gray-900 px-5 py-2.5 rounded-xl font-semibold hover:bg-gray-50 transition">
      Continue Shopping
    </a>
  </div>

  @if($orders->count() === 0)
    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center">
      <p class="text-lg font-semibold text-gray-900">No orders yet</p>
      <p class="text-sm text-gray-600 mt-1">When you place an order, it will show here.</p>
      <a href="{{ route('home') }}"
         class="mt-6 inline-flex items-center justify-center bg-gray-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-black transition">
        Browse Products
      </a>
    </div>
  @else

    <div class="space-y-4">
      @foreach($orders as $order)
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

        <a href="{{ route('orders.show', $order) }}" class="block">
          <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-gray-300 hover:shadow-lg transition">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div>
                <p class="text-sm font-semibold text-gray-900">
                  {{ $order->order_number }}
                </p>
                <p class="text-xs text-gray-600 mt-1">
                  Placed on {{ $order->created_at->format('M d, Y • h:i A') }}
                  <span class="mx-2">•</span>
                  {{ $order->items_count }} item(s)
                </p>
              </div>

              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-xl border {{ $statusClass }}">
                  {{ ucfirst($status) }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-xl border {{ $payClass }}">
                  {{ ucfirst($pay) }}
                </span>
                <span class="text-sm font-semibold text-gray-900">
                  ${{ number_format($order->total, 2) }}
                </span>
              </div>
            </div>

            @if($order->transaction_id)
              <div class="mt-3 text-xs text-gray-600">
                Transaction: <span class="font-semibold text-gray-800">{{ $order->transaction_id }}</span>
              </div>
            @endif

          </div>
        </a>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $orders->links() }}
    </div>

  @endif
</div>
@endsection
