<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'price',
        'total',
        'discount',
        'coupon',
        'status',
        'data',
        'data_input',
        'domain'
    ];

    protected $casts = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'order_product_id');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('product_id', 'LIKE', "%$search%")
                    ->orWhere('user_id', 'LIKE', "%$search%")
                    ->orWhere('quantity', 'LIKE', "%$search%")
                    ->orWhere('price', 'LIKE', "%$search%")
                    ->orWhere('total', 'LIKE', "%$search%")
                    ->orWhere('discount', 'LIKE', "%$search%")
                    ->orWhere('coupon', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%");
            });
        }
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter['status'] ?? false) {
            $query->where('status', $filter['status']);
        }
        if ($filter['product_id'] ?? false) {
            $query->where('product_id', $filter['product_id']);
        }
        if ($filter['user_id'] ?? false) {
            $query->where('user_id', $filter['user_id']);
        }
    }
}
