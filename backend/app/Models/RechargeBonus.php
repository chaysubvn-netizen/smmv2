<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeBonus extends Model
{
    //
    protected $fillable = [
        'type',
        'bonus',
        'min_amount',
        'status',
        'domain',
    ];

    public function scopeSearch($builder, $search)
    {
        if ($search) {
            $builder->where(function ($query) use ($search) {
                $query->where('type', 'LIKE', "%{$search}%")
                    ->orWhere('bonus', 'LIKE', "%{$search}%")
                    ->orWhere('min_amount', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }
    }

    public function scopeFilter($builder, $filter)
    {
        if (isset($filter['status'])) {
            $builder->where('status', $filter['status']);
        }
    }
}
