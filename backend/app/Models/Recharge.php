<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'request_id',
        'method',
        'type',
        'amount',
        'real_amount',
        'bonus',
        'description',
        'status',
        'expired_at',
        'domain'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('transaction_id', 'LIKE', "%{$search}%")
                ->orWhere('request_id', 'LIKE', "%{$search}%")
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('username', 'LIKE', "%{$search}%");
                });
        });
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['user_id'] ?? false) {
            $query->where('user_id', $filters['user_id']);
        }

        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }

        if ($filters['method'] ?? false) {
            $query->where('method', $filters['method']);
        }

        if ($filters['type'] ?? false) {
            $type = $filters['type'];
            if ($type == 'manual') {
                $query->where('method', 'Cộng tay');
            } elseif ($type == 'bank') {
                $query->where(function ($q) {
                    $q->where('type', 'bank')->orWhere('method', 'banking');
                });
            } else {
                $query->where('type', $type);
            }
        }
    }
    public function scopeFilterByDate($query, array $filters)
    {
        $query->when($filters['start_date'] ?? false, function ($query, $startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        });

        $query->when($filters['end_date'] ?? false, function ($query, $endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        });
    }
}
