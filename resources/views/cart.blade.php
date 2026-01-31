@extends('layouts.minimal')
@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Shopping Cart</h1>
      <p class="text-sm text-gray-600 mt-1">Review items and checkout when ready.</p>
    </div>

    <a href="{{ route('home') }}"
       class="hidden sm:inline-flex items-center justify-center border border-gray-300 text-gray-900 px-5 py-2.5 rounded-xl font-semibold hover:bg-gray-50 transition">
      Continue Shopping
    </a>
  </div>

  @if($cartItems->isEmpty())
    <div class="text-center py-16 bg-white border border-gray-200 rounded-2xl">
      <p class="text-lg font-semibold text-gray-900">Your cart is empty</p>
      <p class="text-sm text-gray-600 mt-1">Browse products and add to cart.</p>

      <a href="{{ route('home') }}"
         class="mt-6 inline-flex items-center justify-center bg-gray-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-black transition">
        Browse Products
      </a>
    </div>
  @else

    {{-- ✅ Hidden form used ONLY for selecting/unselecting items (prevents nested-form bug / 404) --}}
    <form id="selectForm" action="{{ route('cart.bulkAction') }}" method="POST" class="hidden">
      @csrf
      <input type="hidden" name="action" value="update">
      <div id="selectedInputs"></div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- LEFT: Items --}}
      <div class="lg:col-span-2 space-y-4">

        @foreach($cartItems as $item)
          @php
            $product = $item->product;

            $basePrice = (float) ($product->price ?? 0);
            $discountPrice = (float) ($product->discount_price ?? 0);
            $hasDiscount = $discountPrice > 0 && $discountPrice < $basePrice;

            $unitPrice = $hasDiscount ? $discountPrice : $basePrice;
            $lineTotal = $unitPrice * (int) $item->quantity;

            $img = $product->image
              ? $product->image
              : 'https://via.placeholder.com/600x750/1e293b/ffffff?text=' . urlencode($product->name);
          @endphp

          <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 hover:border-gray-300 transition">
            <div class="flex gap-4">

              {{-- ✅ Checkbox (no form submit here, JS will submit selectForm) --}}
              <div class="pt-2">
                <input
                  type="checkbox"
                  value="{{ $item->id }}"
                  class="cart-check w-5 h-5 rounded text-gray-900 border-gray-300 focus:ring-0"
                  {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}
                  onchange="submitSelectedItems()"
                >
              </div>

              {{-- Image --}}
              <div class="w-24 sm:w-28 shrink-0">
                <div class="aspect-[4/5] bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center p-2 overflow-hidden">
                  <img src="{{ $img }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
                </div>
              </div>

              {{-- Info --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <a href="{{ route('product.show', $product) }}"
                       class="text-sm sm:text-base font-semibold text-gray-900 hover:text-gray-700 line-clamp-2">
                      {{ $product->name }}
                    </a>

                    <p class="text-xs text-gray-600 mt-1">
                      {{ $product->specs?->color ?? 'Various' }}
                      <span class="mx-1.5">•</span>
                      {{ $product->specs?->storage ?? '128GB+' }}
                    </p>
                  </div>

                  {{-- Remove --}}
                  <form action="{{ route('cart.remove', $item) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 hover:bg-gray-50 transition"
                            title="Remove">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700"
                           viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0l1-2h6l1 2" />
                      </svg>
                    </button>
                  </form>
                </div>

                {{-- Price + Qty row --}}
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                  {{-- ✅ Price --}}
                  <div>
                    @if($hasDiscount)
                      <div class="flex items-baseline gap-2">
                        <span class="text-base font-semibold text-gray-900">
                          ${{ number_format($discountPrice, 2) }}
                        </span>
                        <span class="text-xs line-through text-gray-400">
                          ${{ number_format($basePrice, 2) }}
                        </span>
                      </div>
                    @else
                      <span class="text-base font-semibold text-gray-900">
                        ${{ number_format($basePrice, 2) }}
                      </span>
                    @endif

                    <p class="text-xs text-gray-500 mt-1">
                      Line total: ${{ number_format($lineTotal, 2) }}
                    </p>
                  </div>

                  {{-- Qty controls --}}
                  <div class="flex items-center gap-2">

                    {{-- Decrease --}}
                    <form action="{{ route('cart.updateQty', $item) }}" method="POST">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                      <button type="submit"
                              class="w-10 h-10 rounded-xl border border-gray-200 hover:bg-gray-50 transition
                                     {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                              {{ $item->quantity <= 1 ? 'disabled' : '' }}
                              title="Decrease">
                        <span class="text-lg text-gray-900">−</span>
                      </button>
                    </form>

                    {{-- Qty input --}}
                    <form action="{{ route('cart.updateQty', $item) }}" method="POST" class="flex">
                      @csrf
                      @method('PATCH')
                      <input
                        type="number"
                        min="1"
                        name="quantity"
                        value="{{ $item->quantity }}"
                        class="w-16 h-10 text-center rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-200"
                        onchange="this.form.submit()"
                      >
                    </form>

                    {{-- Increase --}}
                    <form action="{{ route('cart.updateQty', $item) }}" method="POST">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                      <button type="submit"
                              class="w-10 h-10 rounded-xl border border-gray-200 hover:bg-gray-50 transition"
                              title="Increase">
                        <span class="text-lg text-gray-900">+</span>
                      </button>
                    </form>

                  </div>
                </div>
              </div>

            </div>
          </div>
        @endforeach

      </div>

      {{-- RIGHT: Summary --}}
      @php
        $total = $cartItems->whereIn('id', $selectedItems)->sum(function ($i) {
          $p = $i->product;
          $base = (float) ($p->price ?? 0);
          $disc = (float) ($p->discount_price ?? 0);
          $hasDisc = $disc > 0 && $disc < $base;
          $unit = $hasDisc ? $disc : $base;
          return $unit * (int) $i->quantity;
        });
      @endphp

      <div class="lg:col-span-1">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sticky top-24">
          <h2 class="text-base font-semibold text-gray-900">Order Summary</h2>

          <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Selected items</span>
              <span>{{ count($selectedItems) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span>${{ number_format($total, 2) }}</span>
            </div>
            <div class="border-t border-gray-200 my-3"></div>
            <div class="flex justify-between">
              <span class="font-semibold text-gray-900">Total</span>
              <span class="font-semibold text-gray-900">${{ number_format($total, 2) }}</span>
            </div>
          </div>

          {{-- Checkout --}}
          <form action="{{ route('cart.bulkAction') }}" method="POST" class="mt-5">
            @csrf
            <input type="hidden" name="action" value="checkout">
            @foreach($selectedItems as $id)
              <input type="hidden" name="items[]" value="{{ $id }}">
            @endforeach

            <button type="submit"
                    class="w-full bg-gray-900 text-white py-3 rounded-xl font-semibold hover:bg-black transition
                           {{ $total <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $total <= 0 ? 'disabled' : '' }}>
              Checkout
            </button>

            <a href="{{ route('home') }}"
               class="mt-3 w-full inline-flex items-center justify-center border border-gray-300 text-gray-900 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
              Continue Shopping
            </a>
          </form>

          <p class="text-xs text-gray-500 mt-4">
            Tip: select items to checkout. Quantity updates instantly.
          </p>
        </div>
      </div>

    </div>

    {{-- ✅ JS submit selected items --}}
    <script>
      function submitSelectedItems() {
        const form = document.getElementById('selectForm');
        const wrap = document.getElementById('selectedInputs');

        wrap.innerHTML = '';

        document.querySelectorAll('.cart-check:checked').forEach(cb => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'items[]';
          input.value = cb.value;
          wrap.appendChild(input);
        });

        form.submit();
      }
    </script>

  @endif
</div>
@endsection
