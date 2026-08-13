<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateUpdate extends Model
{
    //
    protected $fillable = [
        'service_id',
        'rate_old',
        'rate_new',
        'type',
        'domain'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeSearch($builder, $search)
    {
        if ($search) {
            $builder->where(function ($query) use ($search) {
                $query->where('rate_old', 'like', '%' . $search . '%')
                    ->orWhere('rate_new', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });
        }
    }

    public function scopeFilter($builder, $filters)
    {
        if ($filters['service'] ?? false) {
            $builder->where('service_id', $filters['service']);
        }
        if ($filters['type'] ?? false) {
            $builder->where('type', $filters['type']);
        }
        if ($filters['domain'] ?? false) {
            $builder->where('domain', $filters['domain']);
        }
    }
}
