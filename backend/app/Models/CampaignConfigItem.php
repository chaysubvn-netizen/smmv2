<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CampaignConfigItem extends Model { protected $fillable=['campaign_config_id','service_id','quantity','delay_minutes','loop_count','loop_spacing']; public function service(){return $this->belongsTo(Service::class);} }
