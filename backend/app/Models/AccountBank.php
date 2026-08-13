<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBank extends Model
{
    //
    protected $fillable = [
        'bank_name',
        'icon',
        'bank_code',
        'account_number',
        'account_name',
        'branch',
        'status',
        'domain'
    ];

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%");
            });
        }
        return $query;
    }
    public function scopeFilter($query, $filters)
    {
        if (isset($filters['status'])) {
            return $query->where('status', $filters['status']);
        }
        return $query;
    }

    public function getQR($trans_code){
        if($this->bank_name == 'viettinbank'){
            $this->bank_name = 'vietinbank';
        }

        $url = "https://img.vietqr.io/image/{$this->bank_name}-{$this->account_number}-compact3.jpg";
        $url .= "?amount=&addInfo={$trans_code}&accountName={$this->account_name}";
        return $url;
    }

    public function getIconAttribute($value)
    {
        return $value ? "/uploads/{$value}" : null;
    }
}
