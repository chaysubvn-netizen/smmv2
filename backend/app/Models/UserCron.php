<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCron extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'server_id',
        'url',
        'method',
        'loop_time',
        'status',
        'price_paid',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function server()
    {
        return $this->belongsTo(CronServer::class);
    }
}
