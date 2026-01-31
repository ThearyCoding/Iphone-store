<div class="max-w-4xl mx-auto px-6 py-20">

    <h1 class="text-4xl font-bold text-left mb-10">Shopping Cart</h1>

    @if($cartItems->isEmpty())
        <div class="text-center py-32">
            <p class="text-2xl text-gray-500 mb-8">Your cart is empty</p>
            <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-12 py-5 rounded-2xl text-xl font-medium">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($cartItems as $item)
                <div class="bg-white rounded-2xl shadow-sm flex items-center space-x-5 p-6">
                    <input type="checkbox" 
                           wire:model.live="selectedItems" 
                           value="{{ $item->id }}"
                           class="w-6 h-6 rounded text-indigo-600 focus:ring-0">

                    <img src="{{ $item->product->image ?? 'https://via.placeholder.com/100' }}"
                         class="w-28 h-28 rounded-xl object-cover bg-gray-50">

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->product->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $item->product->specs?->color }} • {{ $item->product->specs?->storage }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-xl font-bold text-indigo-600">
                            ${{ number_format($item->product->discount_price ?? $item->product->price, 0) }}
                        </p>
                        <p class="text-sm text-gray-500">× {{ $item->quantity }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Auto-updating Total & Checkout -->
        <div class="mt-12 flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm">
            <div>
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-3xl font-bold text-gray-900">
                    ${{ number_format($total, 0) }}
                </p>
            </div>

            <button wire:click="checkout"
                    class="bg-indigo-600 text-white font-medium text-lg px-12 py-5 rounded-2xl transition
                           {{ $total == 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700' }}"
                    {{ $total == 0 ? 'disabled' : '' }}>
                Checkout
            </button>
        </div>
    @endif
</div>