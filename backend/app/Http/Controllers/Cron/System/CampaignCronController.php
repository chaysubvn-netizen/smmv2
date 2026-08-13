<?php
namespace App\Http\Controllers\Cron\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\OrderClientController;
use App\Models\Campaign;
use App\Models\CampaignRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class CampaignCronController extends Controller
{
    public function index(Request $request)
    {
        $summary=['checked'=>0,'videos'=>0,'orders'=>0,'failed'=>0];
        Campaign::where('status','active')->with(['config.items.service','user'])->chunkById(50,function($campaigns)use(&$summary){
            foreach($campaigns as $campaign){
                if($campaign->expires_at->isPast()){ $campaign->update(['status'=>'expired']); continue; }
                $lock=Cache::lock('campaign:'.$campaign->id,240); if(!$lock->get())continue;
                try{ $summary['checked']++; $latest=$this->latestVideo($campaign->channel_id); $campaign->forceFill(['last_checked_at'=>now(),'last_error'=>null])->save();
                    if(!$latest)continue;
                    if(!$campaign->last_video_id){$campaign->update(['last_video_id'=>$latest['id']]);continue;}
                    if($campaign->last_video_id===$latest['id'] || CampaignRun::where('campaign_id',$campaign->id)->where('video_id',$latest['id'])->exists())continue;
                    $results=[]; foreach($campaign->config->items as $item){$payload=['service'=>$item->service_id,'link'=>$latest['url'],'quantity'=>$item->quantity]; if($item->loop_count>0){$payload+=['loop'=>1,'loop_quantity'=>$item->loop_count,'loop_spacing'=>$item->loop_spacing];}
                        if($item->delay_minutes>0){$payload+=['is_schedule_time'=>true,'schedule_time'=>now()->addMinutes($item->delay_minutes)->format('Y-m-d H:i:s')];} $orderRequest=Request::create('https://'.$campaign->domain.'/api/request-order','POST',$payload); $orderRequest->headers->set('Host',$campaign->domain); $orderRequest->headers->set('Api-Key',$campaign->user->api_key);
                        $response=app(OrderClientController::class)->requestOrder($orderRequest); $body=$response->getData(true); $ok=(bool)($body['success']??false); if($ok)$summary['orders']++; else $summary['failed']++; $results[]=['service_id'=>$item->service_id,'success'=>$ok,'message'=>$body['message']??null,'order_id'=>data_get($body,'data.order_id')]; }
                    $success=collect($results)->where('success',true)->count(); CampaignRun::create(['campaign_id'=>$campaign->id,'video_id'=>$latest['id'],'video_url'=>$latest['url'],'results'=>$results,'status'=>$success===count($results)?'success':($success?'partial':'failed')]);
                    $campaign->update(['last_video_id'=>$latest['id']]); $summary['videos']++;
                }catch(Throwable $e){report($e);$campaign->update(['last_checked_at'=>now(),'last_error'=>$e->getMessage()]);$summary['failed']++;}finally{$lock->release();}
            }
        });
        return response()->json(['status'=>'success','message'=>'Campaign scan completed.']+$summary);
    }
    private function latestVideo(string $channelId): ?array
    {
        $xml=Http::connectTimeout(5)->timeout(15)->get('https://www.youtube.com/feeds/videos.xml',['channel_id'=>$channelId])->throw()->body();
        $feed=simplexml_load_string($xml); if(!$feed || !isset($feed->entry[0]))return null; $entry=$feed->entry[0]; $ns=$entry->getNamespaces(true); $yt=$entry->children($ns['yt']??'http://www.youtube.com/xml/schemas/2015'); $id=(string)$yt->videoId; return $id?['id'=>$id,'url'=>'https://www.youtube.com/watch?v='.$id]:null;
    }
}
