<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateRef extends Model
{
    protected $fillable = [
        'user_id',
        'ref_user_id',
        'referral_code',
        'total_deposit',
        'commission',
        'domain',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'ref_user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function refUser()
    {
        return $this->belongsTo(User::class, 'ref_user_id');
    }

}
