<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sort_order',
        'name',
        'price',
        'description',
        'short_description',
        'status',
        'slug',
        'data_input',
        'image',
        'domain'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function order_products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
    }

    public function scopeFilter($query, $filters)
    {
        if ($filters['category'] ?? false) {
            $query->where('category_id', $filters['category']);
        }

        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }

        if ($filters['sort_order'] ?? false) {
            $query->orderBy('sort_order', $filters['sort_order']);
        }
    }

    public function getImageAttribute($value)
    {
        return $value ? '/uploads/images/' . $value : null;
    }

}
