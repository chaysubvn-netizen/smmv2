<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'username',
        'event',
        'success',
        'ip_address',
        'user_agent',
        'domain',
        'message',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
