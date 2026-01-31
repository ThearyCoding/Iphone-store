<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpec extends Model
{
    protected $fillable = ['product_id', 'ram', 'storage', 'color', 'battery', 'chipset', 'screen_size'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}