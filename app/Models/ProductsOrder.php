<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsOrder extends Model
{
        protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = ['status'];

    public function product() {
        return $this->belongsTo(Product::class)->select([
            'id', 'title', 'price', 'images', 'quantity'
        ]);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
