<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'payment_status',
        'transaction_id',
        'khqr_qr',
        'khqr_md5',
        'bakong_hash',
        'paid_from_account',
        'paid_to_account',
        'paid_at',
    ];


    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helper: Get formatted order number
    public function getOrderNumberAttribute()
    {
        return 'ORD-' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }
}
