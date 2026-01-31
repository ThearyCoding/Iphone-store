<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>iPhoneStore @yield('title', 'Home')</title>

    @stack('head') {{-- ✅ ADD THIS LINE --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen">

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p>&copy; 2025 iPhoneStore. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>