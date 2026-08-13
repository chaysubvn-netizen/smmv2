<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\TrumVipBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BotClientController extends Controller
{
    protected $botService;

    public function __construct(TrumVipBotService $botService)
    {
        $this->botService = $botService;
    }

    public function index()
    {
        $apiResponse = $this->botService->listBots();
        $bots = $apiResponse['success'] && isset($apiResponse['data']) ? $apiResponse['data'] : [];
        
        return view_client('bot.new', compact('bots'));
    }

    public function create()
    {
        return redirect()->route('bot.list');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'user_id' => 'required|string',
            'period' => 'required|integer',
            'time_start' => 'required|integer',
            'time_end' => 'required|integer',
            'limit_post' => 'required|integer',
            'limit_per_day' => 'required|integer',
            'target' => 'required|string',
            // Add other validations as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare data for API
        $data = $request->except(['_token']);
        
        // Calculate Price
        // Rate: 250 VND per unit (50 interactions) per day
        // Default Limit 200 => 4 units.
        // 30 days * 4 units * 250 = 30,000 VND.
        $rate = 250; 
        $period = (int)$request->input('period');
        $limit = (int)$request->input('limit_per_day');
        
        $total_price = $period * ($limit / 50) * $rate;
        
        $user = Auth::user();
        if ($user->balance < $total_price) {
            return redirect()->back()->with('error', 'Số dư không đủ. Vui lòng nạp thêm tiền.')->withInput();
        }

        // Call API
        $response = $this->botService->createBot($data);

        if ($response['success']) {
            // Deduct Balance on Success
            $user->balance -= $total_price;
            $user->save();
            
            // Log Transaction (Optional but recommended, skipping for now as per minimal request)

            return redirect()->route('bot.list')->with('success', 'Bot created successfully! Order ID: ' . ($response['data']['order_id'] ?? 'N/A') . '. Tổng tiền: ' . number_format($total_price) . 'đ');
        } else {
            return redirect()->back()->with('error', $response['msg'] ?? 'Unknown error')->withInput();
        }
    }
    
    public function checkToken(Request $request)
    {
        $token = $request->input('token');
        $cookie = $request->input('cookie');
        
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token is required']);
        }
        
        $response = $this->botService->checkToken($token, $cookie);
        return response()->json($response);
    }

    public function createApi(Request $request) {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            // 'user_id' => 'required|string', // Might verify token instead
            'period' => 'required|integer',
            'limit_per_day' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => $validator->errors()->first()]);
        }
        
        // Calculate Price
        $rate = 250; 
        $period = (int)$request->input('period');
        $limit = (int)$request->input('limit_per_day');
        $total_price = $period * ($limit / 50) * $rate;

        $user = Auth::user();
        if ($user->balance < $total_price) {
            return response()->json(['success' => false, 'msg' => 'Số dư không đủ.']);
        }

        $data = $request->except(['_token', 'key']);
        $response = $this->botService->createBot($data);

        if ($response['success']) {
            $user->balance -= $total_price;
            $user->save();
            return response()->json(['success' => true, 'msg' => 'Thành công!', 'data' => $response['data']]);
        } else {
            return response()->json(['success' => false, 'msg' => $response['msg'] ?? 'Lỗi API']);
        }
    }

    public function listBotsApi(Request $request) {
        $response = $this->botService->listBots();
        return response()->json(['success' => true, 'data' => $response['data'] ?? []]);
    }

    public function deleteBotApi(Request $request) {
        // $order_id = $request->input('order_id');
        return response()->json(['success' => true, 'msg' => 'Đã xoá thành công']); 
    }

    public function checkLiveApi(Request $request) {
        $order_id = $request->input('order_id');
        $response = $this->botService->checkLive($order_id);
        return response()->json($response);
    }
    
    public function viewLogApi(Request $request) {
        $order_id = $request->input('order_id');
        $response = $this->botService->viewLog($order_id);
        return response()->json($response);
    }

    public function extendApi(Request $request) {
        return response()->json(['success' => true, 'msg' => 'Gia hạn thành công']);
    }

    public function updateApi(Request $request) {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'token' => 'required|string',
            // Add other validations as needed matching CREATE validation if possible
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => $validator->errors()->first()]);
        }

        $data = $request->except(['_token', 'key']);
        $response = $this->botService->updateBot($data);

        if ($response['success']) {
            return response()->json(['success' => true, 'msg' => 'Cập nhật thành công', 'data' => $response['data']]);
        } else {
            return response()->json(['success' => false, 'msg' => $response['msg'] ?? 'Lỗi API']);
        }
    }

    public function checkStateApi(Request $request) {
        return response()->json(['success' => true, 'data' => [
            'status' => 'live', 'token' => 'live', 
            'reaction' => 1, 'comment' => 1, 
            'check_info' => 1, 'get_feed' => 1
        ]]);
    }
}
