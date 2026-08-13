<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'name',
        'phone',
        'username',
        'password',
        'role',
        'level',
        'balance',
        'total_deposit',
        'commission',
        'total_commission',
        'total_withdraw',
        'timezone',
        'language',
        'currency',
        'avatar',
        'google_id',
        'status',
        'two_factor_secret',
        'two_factor_method',
        'telegram_chat_id',
        'referral_code',
        'ref_by_code',
        'api_key',
        'last_login',
        'domain',
        'theme_layout',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'telegram_chat_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeSearch($builder, $search)
    {
        if ($search) {
            return $builder->where(function ($query) use ($search) {
                $query->where('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }
        return $builder;
    }
    public function scopeFilter($builder, $filters)
    {
        if ($filters['status'] ?? false) {
            $builder->where('status', $filters['status']);
        }
        if ($filters['role'] ?? false) {
            $builder->where('role', $filters['role']);
        }
        if ($filters['level'] ?? false) {
            $builder->where('level', $filters['level']);
        }
        return $builder;
    }

    // attribute balance_format
    public function getBalanceFormatAttribute()
    {
        return number_format($this->attributes['balance'], 0, '.', ',');
    }
    // attribute total_deposit_format
    public function getTotalDepositFormatAttribute()
    {
        return number_format($this->attributes['total_deposit'], 0, '.', ',');
    }

    public function upgradeLevel($total_deposit) {
        $limit_silver = s('up_silver') ?? 0;
        $limit_gold = s('up_gold') ?? 0;
        $limit_platinum = s('up_platinum') ?? 0;
        $limit_diamond = s('up_diamond') ?? 0;

        // Convert limits to numeric
        $limit_silver = (float)str_replace([',', '.'], '', $limit_silver);
        $limit_gold = (float)str_replace([',', '.'], '', $limit_gold);
        $limit_platinum = (float)str_replace([',', '.'], '', $limit_platinum);
        $limit_diamond = (float)str_replace([',', '.'], '', $limit_diamond);

        $new_level = 'member';
        if ($total_deposit >= $limit_silver) $new_level = 'silver';
        if ($total_deposit >= $limit_gold) $new_level = 'gold';
        if ($total_deposit >= $limit_platinum) $new_level = 'platinum';
        if ($total_deposit >= $limit_diamond) $new_level = 'diamond';

        $level_priority = [
            'member' => 0,
            'silver' => 1,
            'gold' => 2,
            'platinum' => 3,
            'diamond' => 4,
            'admin' => 99 // Prevent changing admin role check if needed, though usually role != level
        ];

        $current_priority = $level_priority[$this->level] ?? 0;
        $new_priority = $level_priority[$new_level] ?? 0;

        if ($new_priority > $current_priority) {
            $this->level = $new_level;
            $this->save();
        }
    }
}
