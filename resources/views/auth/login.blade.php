@extends('layouts.app')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-8 text-indigo-600">Login</h2>
        <form method="POST" action="/login">
            @csrf
            <div class="mb-6">
                <input type="email" name="email" placeholder="Email" required 
                       class="w-full px-5 py-4 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600">
            </div>
            <div class="mb-6">
                <input type="password" name="password" placeholder="Password" required 
                       class="w-full px-5 py-4 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-lg font-bold hover:bg-indigo-700 transition">
                Login
            </button>
        </form>
        <p class="text-center mt-6 text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Register</a>
        </p>
    </div>
</div>
@endsection