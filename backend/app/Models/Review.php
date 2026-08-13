<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_product_id',
        'parent_id',
        'rating',
        'comment',
        'status',
        'domain'
    ];

    public function order_product()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Review::class, 'parent_id')->where('status', 'active')->latest();
    }

    public function parent()
    {
        return $this->belongsTo(Review::class, 'parent_id');
    }
}
