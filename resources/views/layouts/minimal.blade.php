{{-- resources/views/layouts/minimal.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>iPhoneStore @yield('title', 'Home')</title>

  {{-- ✅ allow per-page meta (OG/Twitter/canonical) --}}
  @stack('head')

  {{-- ✅ Always load CSS --}}
  @vite('resources/css/app.css')

  {{-- ❌ DO NOT load app.js on KHQR pay page --}}
  @if(!Route::is('checkout.pay'))
    @vite('resources/js/app.js')
  @endif

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

  {{-- Navigation --}}
  @if(Route::is('home'))
    @include('layouts.navigation')
  @else
    @include('layouts.navigation-simple')
  @endif

  {{-- Flash messages (optional but recommended) --}}
  @if(session('success') || session('error') || $errors->any())
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 mt-4">
      @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl px-4 py-3 text-sm">
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-4 py-3 text-sm mt-3">
          {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-4 py-3 text-sm mt-3">
          <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
  @endif

  {{-- Page Content --}}
  <main class="flex-1 w-full">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="bg-gray-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-300">
          © {{ date('Y') }} iPhoneStore. All rights reserved.
        </p>

        <div class="flex items-center gap-4 text-sm">
          <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition">Home</a>
          <a href="{{ route('cart.index') }}" class="text-gray-300 hover:text-white transition">Cart</a>
          <a href="{{ route('wishlist.index') }}" class="text-gray-300 hover:text-white transition">Wishlist</a>
          @auth
            <a href="{{ route('orders.index') }}" class="text-gray-300 hover:text-white transition">My Orders</a>
          @endauth
        </div>
      </div>
    </div>
  </footer>

  {{-- Page scripts --}}
  @stack('scripts')
</body>
</html>
