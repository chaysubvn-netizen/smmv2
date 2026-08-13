<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'icon',
        'code',
        'mode',
        'rate_original',
        'rate_original',
        'rate',
        'price_collaborator',
        'price_agency',
        'price_distributor',
        'min',
        'max',
        'provider',
        'provider_id',
        'type',
        'status',
        'sold',
        'warranty',
        'refill',
        'cancel',
        'dripfeed',
        'average_time',
        'attributes',
        'note',
        'sort_order',
        'main_domain',
        'domain'
    ];

    protected $casts = [
        'attributes' => 'array',
        'refill' => 'boolean',
        'cancel' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Nếu sử dụng khóa ngoại 'provider'
    // public function provider()
    // {
    //     return $this->belongsTo(ApiProvider::class, 'provider');
    // }

    // Hoặc nếu đặt tên là apiProvider
    public function apiProvider()
    {
        return $this->belongsTo(ApiProvider::class, 'provider');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
    }

    public function scopeFilter($query, $filters)
    {
        if ($filters['category_id'] ?? false) {
            $query->where('category_id', $filters['category_id']);
        }

        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }
    }
}
