<?php
// app/Http/Controllers/WishlistController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Product $product)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->wishlist()->where('product_id', $product->id)->exists()) {
            $user->wishlist()->detach($product->id);
            $message = 'Removed from wishlist';
        } else {
            $user->wishlist()->attach($product->id);
            $message = 'Added to wishlist';
        }

        return back()->with('success', $message);
    }

    public function index()
    {
        $wishlistItems = auth()->check()
            ? auth()->user()->wishlist()->with('specs')->get()
            : collect();

        return view('wishlist', compact('wishlistItems'));
    }
}