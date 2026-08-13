<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'thousand_separator',
        'decimal_separator',
        'decimal_places',
        'position',
        'status'
    ];
    protected $casts = [
        'exchange_rate' => 'decimal:2',
        'decimal_places' => 'integer',
    ];


}
