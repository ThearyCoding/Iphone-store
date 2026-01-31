@extends('layouts.app')

@section('title', $product->name)

@php
  use Illuminate\Support\Str;

  $url = url()->current();

  $rawImage = $product->image;

  if ($rawImage) {
    if (Str::startsWith($rawImage, ['http://', 'https://'])) {
      $image = $rawImage;
    } else {
      $image = url('/' . ltrim($rawImage, '/'));
    }
  } else {
    $image = 'https://via.placeholder.com/1200x630/1e293b/ffffff?text=' . urlencode($product->name);
  }

  // Description for preview
  $desc = trim(strip_tags($product->description ?? ''));
  if ($desc === '') {
    $desc = ($product->specs?->color ? ($product->specs->color . ' • ') : '') . ($product->specs?->storage ?? '');
    $desc = trim($desc) ?: 'View product details and price.';
  }
  $desc = Str::limit($desc, 180);

  $price = $product->discount_price ?? $product->price;
  $basePrice = (float) ($product->price ?? 0);
$discountPrice = (float) ($product->discount_price ?? 0);
$hasDiscount = $discountPrice > 0 && $discountPrice < $basePrice;

$displayPrice = $hasDiscount ? $discountPrice : $basePrice;
$saveAmount = max(0, $basePrice - $discountPrice);

@endphp

@push('head')
  {{-- Canonical --}}
  <link rel="canonical" href="{{ $url }}" />

  {{-- Open Graph (Facebook + Telegram preview) --}}
  <meta property="og:type" content="product" />
  <meta property="og:title" content="{{ $product->name }}" />
  <meta property="og:description" content="{{ $desc }}" />
  <meta property="og:url" content="{{ $url }}" />
  <meta property="og:image" content="{{ $image }}" />
  <meta property="og:image:secure_url" content="{{ $image }}" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:site_name" content="{{ config('app.name') }}" />

  {{-- Optional: Product price (nice for some scrapers) --}}
  <meta property="product:price:amount" content="{{ (float) $price }}" />
  <meta property="product:price:currency" content="USD" />

  {{-- Twitter/X --}}
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{ $product->name }}" />
  <meta name="twitter:description" content="{{ $desc }}" />
  <meta name="twitter:image" content="{{ $image }}" />

  {{-- Basic SEO --}}
  <meta name="description" content="{{ $desc }}" />
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

  <!-- Back -->
  <div class="mb-6">
    <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">
      ← Back
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

    <!-- LEFT: Image -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="aspect-square flex items-center justify-center p-6 bg-gray-50">
        <img
          src="{{ $product->image
                ? (Str::startsWith($product->image, ['http://','https://']) ? $product->image : url('/' . ltrim($product->image, '/')))
                : ('https://via.placeholder.com/800x800/1e293b/ffffff?text=' . urlencode($product->name)) }}"
          alt="{{ $product->name }}"
          class="max-w-full max-h-full object-contain"
        />
      </div>
    </div>

    <!-- RIGHT: Details -->
    <div class="space-y-6">

      <!-- Title + Share -->
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl sm:text-4xl font-semibold text-gray-900">
            {{ $product->name }}
          </h1>
          <p class="text-sm text-gray-600 mt-2">
            {{ $product->specs?->color ?? 'Multiple Colors' }}
            <span class="mx-2">•</span>
            {{ $product->specs?->storage ?? '128GB+' }}
          </p>
        </div>

        <button
          type="button"
          id="shareBtn"
          class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition"
          aria-label="Share product"
          title="Share"
        >
          {{-- Share icon --}}
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 8a3 3 0 10-2.83-4H13a3 3 0 000 6h.17A3 3 0 0016 8zM6 14a3 3 0 102.83 4H9a3 3 0 000-6H8.83A3 3 0 006 14zm10-2l-8 4m8-8l-8 4" />
          </svg>
        </button>
      </div>

      <!-- Price -->
<div class="border-t border-b border-gray-200 py-4">
  @if($hasDiscount)
    <div class="flex items-baseline gap-3 flex-wrap">
      <span class="text-3xl font-semibold text-gray-900">
        ${{ number_format($discountPrice, 2) }}
      </span>

      <span class="text-lg line-through text-gray-400">
        ${{ number_format($basePrice, 2) }}
      </span>

      <span class="text-sm text-red-600 bg-red-50 border border-red-100 px-2 py-1 rounded-lg">
        Save ${{ number_format($saveAmount, 2) }}
      </span>
    </div>
  @else
    <span class="text-3xl font-semibold text-gray-900">
      ${{ number_format($basePrice, 2) }}
    </span>
  @endif
</div>


      <!-- Actions -->
      <div class="space-y-3">
        <form action="{{ route('cart.add', $product) }}" method="POST">
          @csrf
          <button class="w-full bg-gray-900 text-white py-3.5 rounded-xl font-semibold hover:bg-black transition">
            Add to Cart
          </button>
        </form>

        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
          @csrf
          <button type="submit"
            class="w-full border border-gray-300 text-gray-800 py-3.5 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg"
              fill="{{ $product->inWishlist() ? 'currentColor' : 'none' }}"
              viewBox="0 0 24 24" stroke="currentColor"
              class="w-5 h-5 {{ $product->inWishlist() ? 'text-red-600' : 'text-gray-700' }}">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            {{ $product->inWishlist() ? 'Remove from Wishlist' : 'Add to Wishlist' }}
          </button>
        </form>

        <!-- Share Links -->
        <div class="flex flex-wrap gap-2 pt-2">
          <a
            class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50"
            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
            target="_blank" rel="noopener"
          >
            Facebook
          </a>

          <a
            class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50"
            href="https://t.me/share/url?url={{ urlencode($url) }}&text={{ urlencode($product->name) }}"
            target="_blank" rel="noopener"
          >
            Telegram
          </a>

          <button
            type="button"
            id="copyLinkBtn"
            class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50"
          >
            Copy link
          </button>
        </div>
      </div>

      <!-- Details -->
      <div class="space-y-3">
        <h3 class="text-base font-semibold text-gray-900">Details</h3>
        <p class="text-sm text-gray-700 leading-relaxed">
          {{ $product->description ?? 'No description available.' }}
        </p>

        <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
          <li>Fast delivery</li>
          <li>14-day returns</li>
          <li>1-year warranty</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Related -->
  @if($related->count() > 0)
    <div class="mt-14">
      <h2 class="text-2xl font-semibold text-gray-900 mb-6">
        You Might Also Like
      </h2>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($related as $item)
          @php
            $itemImg = $item->image
              ? (Str::startsWith($item->image, ['http://','https://']) ? $item->image : url('/' . ltrim($item->image, '/')))
              : 'https://via.placeholder.com/600x750';
          @endphp

          <a href="{{ route('product.show', $item) }}" class="block">
            <div class="border border-gray-200 rounded-2xl overflow-hidden hover:border-gray-300 transition bg-white">
              <div class="bg-gray-50 aspect-[4/5] flex items-center justify-center p-4">
                <img src="{{ $itemImg }}" alt="{{ $item->name }}" class="max-w-full max-h-full object-contain" />
              </div>
              <div class="p-4">
                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">
                  {{ $item->name }}
                </h3>
                @php
                    $basePrice = (float) ($item->price ?? 0);
                    $discountPrice = (float) ($item->discount_price ?? 0);
                    $hasDiscount = $discountPrice > 0 && $discountPrice < $basePrice;
                  @endphp

                  @if($hasDiscount)
                    <div class="flex items-baseline gap-2 mt-2">
                      <p class="text-lg font-semibold text-gray-900">
                        ${{ number_format($discountPrice, 2) }}
                      </p>
                      <p class="text-sm line-through text-gray-400">
                        ${{ number_format($basePrice, 2) }}
                      </p>
                    </div>
                  @else
                    <p class="text-lg font-semibold text-gray-900 mt-2">
                      ${{ number_format($basePrice, 2) }}
                    </p>
                  @endif

              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  @endif
</div>

<script>
  (function () {
    const shareBtn = document.getElementById('shareBtn');
    const copyBtn  = document.getElementById('copyLinkBtn');

    const shareData = {
      title: @json($product->name),
      text: @json($desc),
      url: @json($url),
    };

    if (shareBtn) {
      shareBtn.addEventListener('click', async () => {
        try {
          if (navigator.share) {
            await navigator.share(shareData);
          } else {
            await navigator.clipboard.writeText(shareData.url);
            alert('Link copied!');
          }
        } catch (e) {}
      });
    }

    if (copyBtn) {
      copyBtn.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(shareData.url);
          alert('Link copied!');
        } catch (e) {
          prompt('Copy link:', shareData.url);
        }
      });
    }
  })();
</script>
@endsection
