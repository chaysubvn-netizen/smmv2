<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $table = 'platforms';

    protected $fillable = [
        'icon',
        'sort_order',
        'code',
        'name',
        'status'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class, 'platform_id', 'id');
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'LIKE', '%' . $keyword . '%');
    }

    // filter
    public function scopeFilter($query, $filters)
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['sort_order'])) {
            $query->orderBy('sort_order', $filters['sort_order']);
        }

        return $query;
    }

    public function getIconAttribute($value)
    {
        if ($value === null || $value === '') return null;
        if (is_string($value) && (str_starts_with($value, 'preset:') || str_starts_with($value, 'http://') || str_starts_with($value, 'https://'))) {
            return $value;
        }

        return '/uploads/platforms/' . $value;
    }
}
