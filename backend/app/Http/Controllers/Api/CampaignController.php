<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignConfig;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    private function domain(Request $request): string { $host=strtolower(preg_replace('/:\d+$/','',$request->getHost())); return str_starts_with($host,'api.')?substr($host,4):$host; }
    private function configQuery(Request $r){ return CampaignConfig::where('user_id',$r->user()->id)->where('domain',$this->domain($r)); }
    private function campaignQuery(Request $r){ return Campaign::where('user_id',$r->user()->id)->where('domain',$this->domain($r)); }

    public function index(Request $r){
        return response()->json(['status'=>true,'data'=>[
            'configs'=>$this->configQuery($r)->with('items.service:id,name,rate,min,max,type')->latest()->get(),
            'campaigns'=>$this->campaignQuery($r)->with('config.items.service:id,name')->latest()->get(),
        ]]);
    }
    public function storeConfig(Request $r){
        $data=$r->validate(['name'=>['required','string','max:120'],'items'=>['required','array','min:1','max:20'],'items.*.service_id'=>['required','integer','distinct'],'items.*.quantity'=>['required','integer','min:1'],'items.*.delay_minutes'=>['nullable','integer','min:0','max:10080'],'items.*.loop_count'=>['nullable','integer','min:0','max:100'],'items.*.loop_spacing'=>['nullable','integer','min:0','max:10080']]);
        $domain=$this->domain($r); $ids=collect($data['items'])->pluck('service_id');
        $valid=Service::whereIn('id',$ids)->where('status','active')->where(fn($q)=>$q->where('domain',$domain)->orWhereNull('domain')->orWhere('domain',''))->get()->keyBy('id');
        foreach($data['items'] as $item){ $service=$valid->get($item['service_id']); abort_unless($service,422,'Dịch vụ không hợp lệ.'); abort_unless($item['quantity'] >= $service->min && $item['quantity'] <= $service->max,422,"Số lượng dịch vụ #{$service->id} phải từ {$service->min} đến {$service->max}."); }
        $config=DB::transaction(function()use($r,$data,$domain){$c=CampaignConfig::create(['user_id'=>$r->user()->id,'name'=>$data['name'],'domain'=>$domain]);$c->items()->createMany(collect($data['items'])->map(fn($i)=>['service_id'=>$i['service_id'],'quantity'=>$i['quantity'],'delay_minutes'=>$i['delay_minutes']??0,'loop_count'=>$i['loop_count']??0,'loop_spacing'=>$i['loop_spacing']??0])->all());return $c;});
        return response()->json(['status'=>true,'message'=>'Đã tạo cấu hình.','data'=>$config->load('items.service')],201);
    }
    public function updateConfig(Request $r, CampaignConfig $config){ abort_unless($config->user_id===$r->user()->id && $config->domain===$this->domain($r),404); $this->destroyConfig($r,$config); return $this->storeConfig($r); }
    public function destroyConfig(Request $r, CampaignConfig $config){ abort_unless($config->user_id===$r->user()->id && $config->domain===$this->domain($r),404); abort_if($config->campaigns()->exists(),422,'Cấu hình đang được chiến dịch sử dụng.'); $config->delete(); return response()->json(['status'=>true,'message'=>'Đã xóa cấu hình.']); }

    public function storeCampaign(Request $r){
        $data=$r->validate(['channel'=>['required','string','max:500'],'campaign_config_id'=>['required','integer'],'expires_at'=>['required','date','after:now']]);
        $config=$this->configQuery($r)->findOrFail($data['campaign_config_id']); $channel=$this->channelId($data['channel']);
        if(!$channel) return response()->json(['status'=>false,'message'=>'Hãy nhập Channel ID bắt đầu bằng UC hoặc URL /channel/UC...'],422);
        $campaign=Campaign::create(['user_id'=>$r->user()->id,'campaign_config_id'=>$config->id,'channel_id'=>$channel,'channel_url'=>'https://www.youtube.com/channel/'.$channel,'expires_at'=>$data['expires_at'],'status'=>'active','domain'=>$this->domain($r)]);
        return response()->json(['status'=>true,'message'=>'Đã tạo chiến dịch.','data'=>$campaign->load('config')],201);
    }
    public function status(Request $r, Campaign $campaign){ abort_unless($campaign->user_id===$r->user()->id && $campaign->domain===$this->domain($r),404); $data=$r->validate(['status'=>['required',Rule::in(['active','paused'])]]); $campaign->update($data); return response()->json(['status'=>true,'message'=>'Đã cập nhật chiến dịch.']); }
    public function destroyCampaign(Request $r, Campaign $campaign){ abort_unless($campaign->user_id===$r->user()->id && $campaign->domain===$this->domain($r),404); $campaign->delete(); return response()->json(['status'=>true,'message'=>'Đã xóa chiến dịch.']); }
    private function channelId(string $value): ?string { $value=trim($value); if(preg_match('/^(UC[A-Za-z0-9_-]{20,})$/',$value,$m))return $m[1]; if(preg_match('~/channel/(UC[A-Za-z0-9_-]{20,})~i',$value,$m))return $m[1]; return null; }
}
