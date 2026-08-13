<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildPanel extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'status_api',
        'data',
        'api_key',
        'main_domain',
        'domain',
        'expired_at',
    ];

    protected $casts = [
        'data' => 'array',
        'expired_at' => 'datetime',
    ];

    // Map properties from top1smm to the data JSON column in smmv2
    public function getAdminUsernameAttribute() { return $this->data['admin_username'] ?? ''; }
    public function setAdminUsernameAttribute($value) { $data = $this->data ?? []; $data['admin_username'] = $value; $this->data = $data; }

    public function getAdminPasswordAttribute() { return $this->data['admin_password'] ?? ''; }
    public function setAdminPasswordAttribute($value) { $data = $this->data ?? []; $data['admin_password'] = $value; $this->data = $data; }

    public function getAdminEmailAttribute() { return $this->data['admin_email'] ?? ''; }
    public function setAdminEmailAttribute($value) { $data = $this->data ?? []; $data['admin_email'] = $value; $this->data = $data; }

    public function getAdminPhoneAttribute() { return $this->data['admin_phone'] ?? ''; }
    public function setAdminPhoneAttribute($value) { $data = $this->data ?? []; $data['admin_phone'] = $value; $this->data = $data; }

    public function getMonthlyPriceAttribute() { return $this->data['monthly_price'] ?? 0; }
    public function setMonthlyPriceAttribute($value) { $data = $this->data ?? []; $data['monthly_price'] = $value; $this->data = $data; }

    public function getCloudflareStatusAttribute() { return $this->data['cloudflare_status'] ?? ''; }
    public function setCloudflareStatusAttribute($value) { $data = $this->data ?? []; $data['cloudflare_status'] = $value; $this->data = $data; }

    public function getNameserver1Attribute() { return $this->data['nameserver1'] ?? ''; }
    public function setNameserver1Attribute($value) { $data = $this->data ?? []; $data['nameserver1'] = $value; $this->data = $data; }

    public function getNameserver2Attribute() { return $this->data['nameserver2'] ?? ''; }
    public function setNameserver2Attribute($value) { $data = $this->data ?? []; $data['nameserver2'] = $value; $this->data = $data; }

    public function getSiteIdAttribute() { return $this->data['site_id'] ?? null; }
    public function setSiteIdAttribute($value) { $data = $this->data ?? []; $data['site_id'] = $value; $this->data = $data; }

    public function getCreatedSiteIdAttribute() { return $this->data['created_site_id'] ?? null; }
    public function setCreatedSiteIdAttribute($value) { $data = $this->data ?? []; $data['created_site_id'] = $value; $this->data = $data; }

    // Map expires_at to expired_at
    public function getExpiresAtAttribute() { return $this->expired_at; }
    public function setExpiresAtAttribute($value) { $this->expired_at = $value; }

    public function getStatusDisplayNameAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Đang hoạt động',
            self::STATUS_PENDING => 'Đang chờ',
            self::STATUS_EXPIRED => 'Hết hạn',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_EXPIRED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'light',
        };
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->expired_at) return 0;
        return max(0, now()->diffInDays($this->expired_at, false));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('main_domain', 'like', "%$search%")
                    ->orWhere('domain', 'like', "%$search%");
            });
        }
    }

    public function scopeFilter($query, $filters)
    {
        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }

        if ($filters['user_id'] ?? false) {
            $query->where('user_id', $filters['user_id']);
        }
    }
}
