<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'brand_id', 'description', 'price', 'discount_price', 'stock', 'image'];

public function brand()
{
    return $this->belongsTo(Category::class, 'brand_id');
}

    public function specs()
    {
        return $this->hasOne(ProductSpec::class);
    }

    public function inWishlist()
    {
        if (!auth()->check()) return false;
        return auth()->user()->wishlist()->where('product_id', $this->id)->exists();
    }
    public function wishlist()
    {
        return $this->belongsToMany(User::class, 'wishlist');
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlist', 'product_id', 'user_id');
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
}
