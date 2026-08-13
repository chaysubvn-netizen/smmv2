<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'data',
        'uid',
        'name',
        'note',
        'status',
        'domain',
    ];
}
