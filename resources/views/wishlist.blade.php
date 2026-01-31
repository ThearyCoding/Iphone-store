@extends('layouts.minimal')

@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">My Wishlist</h1>
            <p class="text-sm text-gray-600 mt-1">Your saved items in one place.</p>
        </div>

        <a href="{{ route('home') }}"
            class="hidden sm:inline-flex items-center justify-center border border-gray-300 text-gray-900 px-5 py-2.5 rounded-xl font-semibold hover:bg-gray-50 transition">
            Continue Shopping
        </a>
    </div>

    @if($wishlistItems->count() == 0)
    <div class="text-center py-16 bg-white border border-gray-200 rounded-2xl">
        <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <p class="text-lg font-semibold text-gray-900">Your wishlist is empty</p>
        <p class="text-sm text-gray-600 mt-1">Start saving products you love.</p>

        <a href="{{ route('home') }}"
            class="mt-6 inline-flex items-center justify-center bg-gray-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-black transition">
            Browse Products
        </a>
    </div>
    @else

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($wishlistItems as $product)

        <a href="{{ route('product.show', $product) }}" class="block group">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-gray-300 hover:shadow-lg transition">

                {{-- Image --}}
                <div class="relative bg-gray-50">
                    <div class="aspect-[4/5] flex items-center justify-center p-4">
                        <img
                            src="{{ $product->image
                    ? $product->image
                    : 'https://via.placeholder.com/600x750/1e293b/ffffff?text=' . urlencode($product->name) }}"
                            alt="{{ $product->name }}"
                            class="max-w-full max-h-full object-contain" />
                    </div>

                    {{-- Remove from wishlist --}}
                    <form action="{{ route('wishlist.toggle', $product) }}"
                        method="POST"
                        class="absolute top-3 right-3 z-10"
                        onsubmit="event.preventDefault(); event.stopPropagation(); this.submit();">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white/90 backdrop-blur hover:bg-white transition"
                            title="Remove from wishlist">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                                class="w-5 h-5 text-red-600">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </form>

                    @php
                    $basePrice = (float) ($product->price ?? 0);
                    $discountPrice = (float) ($product->discount_price ?? 0);
                    $hasDiscount = $discountPrice > 0 && $discountPrice < $basePrice;
                        @endphp

                        @if($hasDiscount)
                        <div class="absolute left-3 top-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-xl bg-red-50 text-red-700 border border-red-100">
                            Sale
                        </span>
                </div>
                @endif

            </div>

            {{-- Info --}}
            <div class="p-4">
                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-gray-700 transition">
                    {{ $product->name }}
                </h3>

                <p class="text-xs text-gray-600 mt-1">
                    {{ $product->specs?->color ?? 'Various' }}
                    <span class="mx-1.5">•</span>
                    {{ $product->specs?->storage ?? '128GB+' }}
                </p>

                <div class="mt-3">
                    @if($hasDiscount)
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <span class="text-lg font-semibold text-gray-900">
                            ${{ number_format($discountPrice, 2) }}
                        </span>
                        <span class="text-sm line-through text-gray-400">
                            ${{ number_format($basePrice, 2) }}
                        </span>
                    </div>
                    @else
                    <span class="text-lg font-semibold text-gray-900">
                        ${{ number_format($basePrice, 2) }}
                    </span>
                    @endif
                </div>


                {{-- Add to cart --}}
                <form action="{{ route('cart.add', $product) }}"
                    method="POST"
                    class="mt-4"
                    onsubmit="event.preventDefault(); event.stopPropagation(); this.submit();">
                    @csrf
                    <button class="w-full bg-gray-900 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-black transition">
                        Add to Cart
                    </button>
                </form>
            </div>

    </div>
    </a>

    @endforeach
</div>

@endif
</div>
@endsection