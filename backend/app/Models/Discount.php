<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    
    protected $fillable = [
        'code',
        'type',
        'amount',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'amount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];
}
