<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'data',
        'status',
        'buyer_id',
        'domain'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('product', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }
    public function scopeFilter($query, $filters)
    {
        if ($filters['product_id'] ?? false) {
            $query->where('product_id', $filters['product']);
        }

        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }

        if ($filters['buyer_id'] ?? false) {
            $query->where('buyer_id', $filters['buyer']);
        }
    }
}
