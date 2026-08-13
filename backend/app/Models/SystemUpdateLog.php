<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUpdateLog extends Model
{
    protected $fillable = [
        'from_version',
        'to_version',
        'status',
        'file_name',
        'migrations',
        'admin_name',
        'message',
        'log',
    ];

    protected $casts = ['log' => 'array'];
}
