<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Campaign extends Model { protected $fillable=['user_id','campaign_config_id','channel_id','channel_url','expires_at','status','last_video_id','last_checked_at','last_error','domain']; protected $casts=['expires_at'=>'datetime','last_checked_at'=>'datetime']; public function config(){return $this->belongsTo(CampaignConfig::class,'campaign_config_id');} public function user(){return $this->belongsTo(User::class);} public function runs(){return $this->hasMany(CampaignRun::class);} }