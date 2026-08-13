<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'order_id',
        'type',
        'status',
        'domain',
    ];

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
    }
    public function scopeFilter($query, $filter)
    {
        if ($filter['status'] ?? null) {
            $query->where('status', $filter['status']);
        }
        if ($filter['type'] ?? null) {
            $query->where('type', $filter['type']);
        }
    }
}
