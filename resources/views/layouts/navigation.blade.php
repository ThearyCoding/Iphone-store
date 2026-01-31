{{-- resources/views/layouts/navigation.blade.php --}}
@php
    $categories = \Cache::remember('nav_categories', 3600, function () {
        return \App\Models\Category::orderBy('name')->get();
    });
@endphp

<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="h-20 flex items-center justify-between gap-4">

            {{-- LEFT: Logo --}}
            <a href="{{ route('home') }}" class="text-2xl font-black text-indigo-600 shrink-0">
                iPhoneStore
            </a>

            {{-- DESKTOP: Categories --}}
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}"
                   class="font-semibold pb-1 {{ !request('category') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-700 hover:text-indigo-600' }} transition">
                    All
                </a>

                @foreach($categories as $category)
                    <a href="{{ route('home') }}?category={{ $category->id }}"
                       class="font-medium transition {{ request('category') == $category->id ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : 'text-gray-700 hover:text-indigo-600' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- DESKTOP: Search --}}
            <div class="hidden md:block flex-1 max-w-xl">
                <form action="{{ route('home') }}" method="GET" class="relative">
                    <input type="text"
                           name="search"
                           placeholder="Search iPhone models, accessories..."
                           value="{{ request('search') }}"
                           class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-200 text-gray-700 text-sm">
                    <button type="submit"
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- RIGHT: Icons + Auth (Desktop) --}}
            <div class="hidden md:flex items-center gap-5 shrink-0">

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
                <a href="{{ route('orders.index') }}" class="relative group">
                    <svg class="w-7 h-7 text-gray-700 group-hover:text-indigo-600 transition"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5h6M9 10h6M9 14h6M9 18h4" />
                    </svg>
                    <span class="sr-only">My Orders</span>
                </a>

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
                        <span class="text-sm font-medium text-gray-700 hidden lg:block">
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
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition">
                        Register
                    </a>
                @endauth
            </div>

            {{-- MOBILE: Right icons + hamburger --}}
            <div class="md:hidden flex items-center gap-3">

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

                <button id="mobileMenuBtn"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 hover:bg-gray-50 transition"
                        aria-label="Open menu">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- MOBILE PANEL --}}
        <div id="mobileMenu" class="md:hidden hidden pb-6">
            {{-- Mobile Search --}}
            <form action="{{ route('home') }}" method="GET" class="relative mt-2">
                <input type="text"
                       name="search"
                       placeholder="Search..."
                       value="{{ request('search') }}"
                       class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-200 text-gray-700 text-sm">
                <button type="submit"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>

            {{-- Mobile Categories --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('home') }}"
                   class="px-3 py-2 rounded-xl border text-sm font-semibold
                          {{ !request('category') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                    All
                </a>

                @foreach($categories as $category)
                    <a href="{{ route('home') }}?category={{ $category->id }}"
                       class="px-3 py-2 rounded-xl border text-sm font-semibold
                              {{ request('category') == $category->id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Mobile quick links --}}
            <div class="mt-5 grid grid-cols-3 gap-3">
                <a href="{{ route('wishlist.index') }}" class="bg-white border border-gray-200 rounded-xl p-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Wishlist
                </a>
                <a href="{{ route('orders.index') }}" class="bg-white border border-gray-200 rounded-xl p-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Orders
                </a>
                <a href="{{ route('cart.index') }}" class="bg-white border border-gray-200 rounded-xl p-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Cart
                </a>
            </div>

            {{-- Mobile auth --}}
            <div class="mt-5 border-t border-gray-200 pt-4">
                @auth
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold text-gray-900">
                            {{ auth()->user()->name }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm text-red-600 hover:text-red-800 font-semibold">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="flex gap-3">
                        <a href="{{ route('login') }}"
                           class="flex-1 inline-flex items-center justify-center border border-gray-300 text-gray-900 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="flex-1 inline-flex items-center justify-center bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Tiny JS --}}
    <script>
        (function () {
            const btn = document.getElementById('mobileMenuBtn');
            const menu = document.getElementById('mobileMenu');
            if (!btn || !menu) return;

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        })();
    </script>
</nav>
