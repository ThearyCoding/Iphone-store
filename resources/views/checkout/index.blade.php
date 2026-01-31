{{-- resources/views/checkout/index.blade.php --}}
@extends('layouts.minimal')
@section('title', 'Checkout')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Checkout</h1>
      <p class="text-sm text-gray-600 mt-1">Confirm your selected items and place order.</p>
    </div>

    <a href="{{ route('cart.index') }}"
       class="hidden sm:inline-flex items-center justify-center border border-gray-300 text-gray-900 px-5 py-2.5 rounded-xl font-semibold hover:bg-gray-50 transition">
      ← Back to Cart
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Items --}}
    <div class="lg:col-span-2">
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div class="flex items-center justify-between gap-3">
          <h2 class="text-base font-semibold text-gray-900">
            Your Order
          </h2>
          <span class="text-sm text-gray-600">
            {{ $selectedItems->count() }} item(s)
          </span>
        </div>

        <div class="mt-5 divide-y divide-gray-200">
          @foreach($selectedItems as $item)
            @php
              $p = $item->product;

              $img = $p->image
                ? $p->image
                : 'https://via.placeholder.com/600x750/1e293b/ffffff?text=' . urlencode($p->name);

              $unit = $p->discount_price ?? $p->price;
              $line = $unit * $item->quantity;
            @endphp

            <div class="py-4 flex gap-4">
              {{-- Image --}}
              <div class="w-20 shrink-0">
                <div class="aspect-[4/5] bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center p-2 overflow-hidden">
                  <img src="{{ $img }}" alt="{{ $p->name }}" class="max-w-full max-h-full object-contain">
                </div>
              </div>

              {{-- Info --}}
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 line-clamp-2">
                  {{ $p->name }}
                </p>
                <p class="text-xs text-gray-600 mt-1">
                  {{ $p->specs?->color ?? 'Various' }}
                  <span class="mx-1.5">•</span>
                  {{ $p->specs?->storage ?? '128GB+' }}
                </p>

                <div class="mt-2 text-xs text-gray-500">
                  Qty: <span class="font-semibold text-gray-700">{{ $item->quantity }}</span>
                </div>
              </div>

              {{-- Price --}}
              <div class="text-right shrink-0">
                <p class="text-sm font-semibold text-gray-900">
                  ${{ number_format($line, 2) }}
                </p>

                @if($p->discount_price)
                  <p class="text-xs text-gray-500 mt-1">
                    <span class="line-through">${{ number_format($p->price, 2) }}</span>
                    <span class="mx-1">→</span>
                    <span>${{ number_format($p->discount_price, 2) }}</span>
                  </p>
                @else
                  <p class="text-xs text-gray-500 mt-1">
                    ${{ number_format($p->price, 2) }} each
                  </p>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- RIGHT: Summary --}}
    <div class="lg:col-span-1">
      <div class="bg-white border border-gray-200 rounded-2xl p-5 sticky top-24">
        <h2 class="text-base font-semibold text-gray-900">Order Summary</h2>

        <div class="mt-4 space-y-2 text-sm">
          <div class="flex justify-between text-gray-600">
            <span>Subtotal</span>
            <span>${{ number_format($subtotal, 2) }}</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Shipping</span>
            <span class="text-green-600 font-semibold">FREE</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Tax</span>
            <span>${{ number_format($tax, 2) }}</span>
          </div>

          <div class="border-t border-gray-200 my-3"></div>

          <div class="flex justify-between">
            <span class="font-semibold text-gray-900">Total</span>
            <span class="font-semibold text-gray-900">${{ number_format($total, 2) }}</span>
          </div>
        </div>

        <form action="{{ route('checkout.place') }}" method="POST" class="mt-5">
          @csrf
          @foreach(session('checkout_items', []) as $id)
            <input type="hidden" name="items[]" value="{{ $id }}">
          @endforeach

          <button type="submit"
                  class="w-full bg-gray-900 text-white py-3 rounded-xl font-semibold hover:bg-black transition
                         {{ $selectedItems->count() == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                  {{ $selectedItems->count() == 0 ? 'disabled' : '' }}>
            Order Now • ${{ number_format($total, 2) }}
          </button>

          <a href="{{ route('cart.index') }}"
             class="mt-3 w-full inline-flex items-center justify-center border border-gray-300 text-gray-900 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
            Back to Cart
          </a>
        </form>

        <p class="text-xs text-gray-500 mt-4">
          By placing your order, you agree to our terms.
        </p>
      </div>
    </div>

  </div>
</div>
@endsection
