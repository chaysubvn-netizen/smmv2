<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CampaignConfig extends Model { protected $fillable=['user_id','name','domain']; public function items(){return $this->hasMany(CampaignConfigItem::class);} public function campaigns(){return $this->hasMany(Campaign::class);} }
