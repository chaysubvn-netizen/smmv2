<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id',
        'service_id',
        'category_id',
        'link',
        'quantity',
        'rate',
        'payment',
        'total',
        'discount',
        'coupon',
        'input_data',
        'response_data',
        'root_id',
        'next_id',
        'start_count',
        'remains',
        'refill',
        'status_refill',
        'cancel',
        'dripfeed',
        'dripfeed_status',
        'run',
        'status',
        'provider',
        'loop_quantity',
        'loop_spacing',
        'schedule_time',
        'start_time',
        'success_time',
        'note',
        'domain'
    ];

    protected $casts = [
        'input_data' => 'array',
        'response_data' => 'array',
        'loop_quantity' => 'integer',
        'loop_spacing' => 'integer',
        'start_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function provider()
    {
        return $this->belongsTo(ApiProvider::class, 'provider');
    }

    public function scopeSearch($builder, $query)
    {
        if ($query) {
            $builder->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('orders.id', 'like', "%{$query}%")
                    ->orWhere('orders.link', 'like', "%{$query}%")
                    ->orWhere('orders.quantity', 'like', "%{$query}%")
                    ->orWhere('orders.rate', 'like', "%{$query}%")
                    ->orWhere('orders.payment', 'like', "%{$query}%")
                    ->orWhere('orders.total', 'like', "%{$query}%")
                    ->orWhere('orders.discount', 'like', "%{$query}%")
                    ->orWhere('orders.start_count', 'like', "%{$query}%")
                    ->orWhere('orders.remains', 'like', "%{$query}%")
                    ->orWhere('orders.status', 'like', "%{$query}%");
            });
        }
        return $builder;
    }

    public function scopeFilter($builder, $filters)
    {
        // Existing filters
        if ($filters['status'] ?? false) {
            $builder->where('orders.status', $filters['status']);
        }

        if ($filters['user_id'] ?? false) {
            $builder->where('orders.user_id', $filters['user_id']);
        }

        if ($filters['service_id'] ?? false) {
            $builder->where('orders.service_id', $filters['service_id']);
        }

        if ($filters['start_time'] ?? false) {
            $builder->where('orders.start_time', '>=', $filters['start_time']);
        }

        if($filters['status_refill'] ?? false) {
            $builder->where('orders.status_refill', $filters['status_refill']);
        }

        // New advanced filters
        // Filter by username
        if ($filters['filter_username'] ?? false) {
            $builder->whereHas('user', function($query) use ($filters) {
                $query->where('username', 'like', '%' . $filters['filter_username'] . '%');
            });
        }

        // Filter by order ID
        if ($filters['filter_order_id'] ?? false) {
            $builder->where('orders.id', $filters['filter_order_id']);
        }

        // Filter by service
        if ($filters['filter_service'] ?? false) {
            $builder->where('orders.service_id', $filters['filter_service']);
        }

        // Filter by status (from dropdown)
        if ($filters['filter_status'] ?? false) {
            $builder->where('orders.status', $filters['filter_status']);
        }

        // Filter by date range
        if ($filters['filter_date_from'] ?? false) {
            $builder->whereDate('orders.created_at', '>=', $filters['filter_date_from']);
        }

        if ($filters['filter_date_to'] ?? false) {
            $builder->whereDate('orders.created_at', '<=', $filters['filter_date_to']);
        }
    }

    public function scopeDate($builder, $date)
    {
        return $builder->whereDate('created_at', $date);
    }

    public function scopeDateRange($builder, $startDate, $endDate)
    {
        return $builder->whereBetween('created_at', [$startDate, $endDate]);
    }
}
