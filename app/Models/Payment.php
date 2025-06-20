<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PurchasedProduct;

class Payment extends Model
{
protected $fillable = ['user_id', 'price', 'status', 'cart_data'];


    protected $casts = [
        'cart_data' => 'array', // auto-cast JSON to array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchasedProducts()
    {
        return $this->hasMany(PurchasedProduct::class);
    }

    // No direct product() relationship because products are inside cart_data JSON
}
