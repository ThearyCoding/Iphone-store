{{-- resources/views/layouts/navigation-simple.blade.php --}}
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="h-20 flex items-center justify-between">

      {{-- Logo --}}
      <a href="{{ route('home') }}" class="text-2xl font-black text-indigo-600">
        iPhoneStore
      </a>

      {{-- Desktop Right --}}
      <div class="hidden sm:flex items-center gap-5">

        {{-- Wishlist --}}
        <a href="{{ route('wishlist.index') }}" class="relative group">
          <svg class="w-7 h-7 text-gray-700 group-hover:text-indigo-600 transition"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
          </svg>
          @auth
            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[11px] rounded-full w-6 h-6 flex items-center justify-center font-bold">
              {{ auth()->user()->wishlist()->count() }}
            </span>
          @endauth
          <span class="sr-only">Wishlist</span>
        </a>

        {{-- Orders --}}
        @auth
          <a href="{{ route('orders.index') }}" class="relative group">
            <svg class="w-7 h-7 text-gray-700 group-hover:text-indigo-600 transition"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5h6M9 10h6M9 14h6M9 18h4" />
            </svg>
            <span class="sr-only">My Orders</span>
          </a>
        @endauth

        {{-- Cart --}}
        <a href="{{ route('cart.index') }}" class="relative group">
          <svg class="w-7 h-7 text-gray-700 group-hover:text-indigo-600 transition"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          @auth
            <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-[11px] rounded-full w-6 h-6 flex items-center justify-center font-bold">
              {{ auth()->user()->cart()->sum('quantity') }}
            </span>
          @endauth
          <span class="sr-only">Cart</span>
        </a>

        {{-- Auth --}}
        @auth
          <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
            <span class="text-sm font-medium text-gray-700 hidden md:block">
              {{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="text-sm text-red-600 hover:text-red-800 font-semibold">
                Logout
              </button>
            </form>
          </div>
        @else
          <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">
            Login
          </a>
          <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition">
            Register
          </a>
        @endauth

      </div>

      {{-- Mobile Right --}}
      <div class="sm:hidden flex items-center gap-3">
        <a href="{{ route('wishlist.index') }}" class="relative">
          <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
          </svg>
        </a>

        <a href="{{ route('cart.index') }}" class="relative">
          <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </a>

        <button id="simpleMenuBtn"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 hover:bg-gray-50 transition"
                aria-label="Open menu">
          <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

    </div>

    {{-- Mobile dropdown --}}
    <div id="simpleMenu" class="sm:hidden hidden pb-5">
      <div class="border-t border-gray-200 pt-4 space-y-3">

        @auth
          <div class="text-sm font-semibold text-gray-900">
            {{ auth()->user()->name }}
          </div>

          <a href="{{ route('orders.index') }}"
             class="block bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            My Orders
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-red-600 hover:bg-gray-50 text-left">
              Logout
            </button>
          </form>
        @else
          <a href="{{ route('login') }}"
             class="block bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Login
          </a>
          <a href="{{ route('register') }}"
             class="block bg-indigo-600 text-white rounded-xl px-4 py-3 text-sm font-semibold hover:bg-indigo-700 transition">
            Register
          </a>
        @endauth

      </div>
    </div>

    <script>
      (function () {
        const btn = document.getElementById('simpleMenuBtn');
        const menu = document.getElementById('simpleMenu');
        if (!btn || !menu) return;
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
      })();
    </script>

  </div>
</nav>
