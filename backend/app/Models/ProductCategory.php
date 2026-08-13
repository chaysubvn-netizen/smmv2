<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{

    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'status',
        'sort_order',
        'domain'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%$search%")
                     ->orWhere('description', 'like', "%$search%");
    }

    public function scopeFilter($query, $filters)
    {
        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }

        if ($filters['sort'] ?? false) {
            $query->orderBy('sort_order', $filters['sort']);
        }
    }

    public function getImageAttribute($value)
    {
        return $value ? ('/uploads/images/' . $value) : null;
    }
}
