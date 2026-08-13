<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'image',
        'status',
        'domain',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($builder, $query)
    {
        if ($query) {
            return $builder->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            });
        }
    }

    public function scopeFilter($builder, $filters)
    {
        if ($filters['user'] ?? false) {
            $builder->where('user_id', $filters['user']);
        }
    }

    public function getImageAttribute($value)
    {
        return $value ? '/uploads/images/' . $value : null;
    }
}
