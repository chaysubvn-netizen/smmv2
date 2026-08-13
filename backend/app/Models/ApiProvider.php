<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiProvider extends Model
{
    protected $table = 'api_providers';

    protected $fillable = [
        'name',
        'type',
        'code',
        'api_url',
        'api_key',
        'balance',
        'currency',
        'exchange_rate',
        'increase_rate',
        'rate_per',
        'status',
        'note',
        'sync_services',
        'auto_update_price',
        'increase_rate_collaborator',
        'increase_rate_agency',
        'increase_rate_distributor',
        'price_format',
        'auto_update_name',
        'filter_html',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:0',
        'increase_rate' => 'integer',
        'rate_per' => 'integer',
        'sync_services' => 'boolean',
        'auto_update_price' => 'boolean',
        'increase_rate_collaborator' => 'integer',
        'increase_rate_agency' => 'integer',
        'increase_rate_distributor' => 'integer',
        'auto_update_name' => 'boolean',
        'filter_html' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'provider');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%$search%")
                ->orWhere('type', 'LIKE', "%$search%")
                ->orWhere('api_url', 'LIKE', "%$search%")
                ->orWhere('api_key', 'LIKE', "%$search%")
                ->orWhere('status', 'LIKE', "%$search%");
        });
    }

    public function scopeFilter($builder, $filters)
    {

        if ($filters['status'] ?? false) {
            $builder->where('status', $filters['status']);
        }

        if ($filters['type'] ?? false) {
            $builder->where('type', $filters['type']);
        }
    }
}
