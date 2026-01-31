@extends('layouts.app')

@section('content')

{{-- HERO (simple, clean like detail page) --}}
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div class="max-w-xl">
            <p class="text-sm font-semibold text-gray-500 tracking-wide">New Collection</p>
            <h1 class="mt-3 text-4xl sm:text-5xl font-semibold text-gray-900">iPhone 15 Pro Max</h1>
            <p class="mt-4 text-lg text-gray-600">
                Experience the future with our latest iPhone collection
            </p>

            <div class="mt-7 flex gap-3">
                <a href="#products"
                    class="inline-flex items-center justify-center bg-gray-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-black transition">
                    Shop Now
                </a>
                <a href="#products"
                    class="inline-flex items-center justify-center border border-gray-300 text-gray-900 px-6 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Browse
                </a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="aspect-[16/10] bg-gray-50 flex items-center justify-center p-6">
                <img
                    src="https://cdn.neowin.com/news/images/uploaded/2023/09/1694604511_iphone_15_and_15_pro_story.jpg"
                    alt="iPhone 15 Pro Max"
                    class="max-w-full max-h-full object-contain" />
            </div>
        </div>
    </div>
</div>

{{-- PRODUCTS --}}
<div id="products" class="max-w-7xl mx-auto px-6 py-12">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">Featured Products</h2>
            <p class="text-sm text-gray-600 mt-1">Clean picks, best price, fast delivery.</p>
        </div>

        <form action="{{ route('home') }}" method="GET" class="flex items-center gap-2">
            {{-- keep existing filters --}}
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif

            <select name="sort"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded-xl px-4 py-2.5 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-gray-200">
                <option value="featured" {{ request('sort','featured')=='featured' ? 'selected' : '' }}>Sort by: Featured</option>
                <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>Newest</option>
                <option value="price_asc" {{ request('sort')=='price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
        </form>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <a href="{{ route('product.show', $product) }}" class="block group">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-gray-300 hover:shadow-lg transition">

                {{-- Image --}}
                <div class="relative bg-gray-50">
                    <div class="aspect-[4/5] flex items-center justify-center p-4">
                        <img
                            src="{{ $product->image ?? 'https://via.placeholder.com/600x750/1e293b/ffffff?text=' . urlencode($product->name) }}"
                            alt="{{ $product->name }}"
                            class="max-w-full max-h-full object-contain" />
                    </div>

                    {{-- Wishlist --}}
                    <form
                        action="{{ route('wishlist.toggle', $product) }}"
                        method="POST"
                        class="absolute top-3 right-3 z-10"
                        onsubmit="event.preventDefault(); event.stopPropagation(); this.submit();">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white/90 backdrop-blur hover:bg-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="{{ $product->inWishlist() ? 'currentColor' : 'none' }}"
                                viewBox="0 0 24 24" stroke="currentColor"
                                class="w-5 h-5 {{ $product->inWishlist() ? 'text-red-600' : 'text-gray-700' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </form>

                    {{-- Discount badge --}}
                    @php
                    $price = (float) ($product->price ?? 0);
                    $discount = (float) ($product->discount_price ?? 0);
                    @endphp

                    @if($discount > 0 && $discount < $price)

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

                            @php
                            $price = (float) ($product->price ?? 0);
                            $discount = (float) ($product->discount_price ?? 0);

                            $hasDiscount = $discount > 0 && $discount < $price;
                                @endphp

                                <div class="mt-3">
                                @if($hasDiscount)
                                <div class="flex items-baseline gap-2 flex-wrap">
                                    <span class="text-lg font-semibold text-gray-900">
                                        ${{ number_format($discount, 2) }}
                                    </span>
                                    <span class="text-sm line-through text-gray-400">
                                        ${{ number_format($price, 2) }}
                                    </span>
                                </div>
                                @else
                                <span class="text-lg font-semibold text-gray-900">
                                    ${{ number_format($price, 2) }}
                                </span>
                                @endif
                        </div>


                        {{-- Add to cart --}}
                        <form
                            action="{{ route('cart.add', $product) }}"
                            method="POST"
                            class="mt-4"
                            onsubmit="event.preventDefault(); event.stopPropagation(); this.submit();">
                            @csrf
                            <button
                                class="w-full bg-gray-900 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-black transition">
                                Add to Cart
                            </button>
                        </form>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @if($products->hasMorePages())
    <div class="text-center mt-12">
        <a href="{{ $products->nextPageUrl() }}"
            class="inline-flex items-center justify-center border border-gray-300 text-gray-900 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
            Load More Products
        </a>
    </div>
    @endif

</div>

@endsection