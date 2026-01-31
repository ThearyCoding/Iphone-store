@extends('layouts.minimal')
@section('title', 'Order Success')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-32 text-center">
    <div class="text-8xl mb-8">Checkmark</div>
    <h1 class="text-5xl font-black mb-6">Order Placed Successfully!</h1>
    <p class="text-2xl text-gray-600 mb-4">Order #{{ $order->id }}</p>
    <p class="text-xl text-gray-500 mb-12">
        Total paid: <span class="font-bold text-indigo-600">${{ number_format($order->total, 0) }}</span>
    </p>
    <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-12 py-6 rounded-2xl text-2xl font-bold hover:bg-indigo-700 transition">
        Continue Shopping
    </a>
</div>
@endsection