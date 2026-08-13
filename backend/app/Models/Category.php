<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'platform_id',
        'name',
        'code',
        'icon',
        'sort_order',
        'is_discount',
        'provider',
        'status'
    ];
    protected $casts = [
        'is_discount' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'like', '%' . $keyword . '%')
            ->orWhere('code', 'like', '%' . $keyword . '%');
    }

    public function scopeSort($query, $sort)
    {
        return $query->orderBy('id', $sort);
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['platform_id'])) {
            $query->whereHas('platform', function ($q) use ($filters) {
                $q->where('id', $filters['platform_id']);
            });
        }

        if (isset($filters['is_discount'])) {
            $query->where('is_discount', $filters['is_discount']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    public function getIconAttribute($value)
    {
        if (is_string($value) && (str_starts_with($value, 'preset:') || str_starts_with($value, 'http://') || str_starts_with($value, 'https://'))) {
            return $value;
        }
        return $value ? '/uploads/categories/' . $value : null;
    }
}
