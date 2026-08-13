<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_code',
        'type',
        'balance_before',
        'balance_after',
        'amount',
        'description',
        'status',
        'domain'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($builder, $query)
    {
        if ($query) {
            return $builder->where(function ($q) use ($query) {
                $q->where('description', 'LIKE', "%$query%")
                    ->orWhere('type', 'LIKE', "%$query%");
            });
        }
        return $builder;
    }

    public function scopeFilter($builder, $filter)
    {
        if ($filter['type'] ?? false) {
            $builder->where('type', $filter['type']);
        }

        if ($filter['status'] ?? false) {
            $builder->where('status', $filter['status']);
        }

        if ($filter['start_date'] ?? false && $filter['end_date'] ?? false) {
            $builder->whereBetween('created_at', [$filter['start_date'], $filter['end_date']]);
        }

        return $builder;
    }
}
