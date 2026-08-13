<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CampaignRun extends Model { protected $fillable=['campaign_id','video_id','video_url','results','status']; protected $casts=['results'=>'array']; }
