<?php

namespace App\Http\Controllers\Admin\Provider;

use App\Http\Controllers\Controller;
use App\Library\SmmApiCustom;
use App\Models\ApiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = ApiProvider::all();

        return view('admin.providers.index', compact('providers'));
    }

    public function create()
    {
        return view('admin.providers.create');
    }

    public function store(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|string',
            'api_url' => 'required|string|url',
            'api_key' => 'required|string',
            'currency' => 'required|in:USD,VND',
            'exchange_rate' => 'nullable|required_if:currency,USD|numeric|min:1',
            'increase_rate' => 'nullable|numeric',
            'increase_rate_collaborator' => 'nullable|numeric',
            'increase_rate_agency' => 'nullable|numeric',
            'increase_rate_distributor' => 'nullable|numeric',
            'rate_per' => 'required|in:1,1000',
            'status' => 'nullable|in:active,inactive',
            'note' => 'nullable|string',
            'sync_services' => 'nullable|boolean',
            'auto_update_price' => 'nullable|boolean',
            'price_format' => 'nullable|string',
            'auto_update_name' => 'nullable|boolean',
            'filter_html' => 'nullable|boolean',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $balance = 0;
        $currency = $request->currency;
        $balanceWarning = null;

        try {
            $smm = new SmmApiCustom($request->api_url, $request->api_key);
            $getBalance = $smm->balance();

            if (is_array($getBalance) && ! isset($getBalance['error']) && array_key_exists('balance', $getBalance)) {
                $balance = (float) $getBalance['balance'];
            } else {
                $error = is_array($getBalance) ? ($getBalance['error'] ?? null) : null;
                $balanceWarning = is_array($error)
                    ? (string) ($error['message'] ?? 'API chưa trả về số dư hợp lệ.')
                    : ((string) $error ?: 'API chưa phản hồi JSON hợp lệ; số dư tạm đặt là 0.');
            }
        } catch (\Throwable $e) {
            report($e);
            $balanceWarning = 'Chưa thể kết nối API để lấy số dư; số dư tạm đặt là 0.';
        }

        try {
            $provider = new ApiProvider;
            $provider->name = $request->name;
            $provider->type = $request->type;
            $provider->code = Str::random(8);
            $provider->balance = $balance;
            $provider->currency = $currency;
            $provider->api_url = $request->api_url;
            $provider->api_key = $request->api_key;
            $provider->exchange_rate = $request->currency === 'VND' ? 1 : $request->exchange_rate;
            $provider->increase_rate = $request->increase_rate ?? 0;
            $provider->increase_rate_collaborator = $request->increase_rate_collaborator ?? 0;
            $provider->increase_rate_agency = $request->increase_rate_agency ?? 0;
            $provider->increase_rate_distributor = $request->increase_rate_distributor ?? 0;
            $provider->rate_per = $request->rate_per;
            $provider->status = $request->status ?? 'active';
            $provider->note = $request->note;
            $provider->sync_services = $request->sync_services ?? 0;
            $provider->auto_update_price = $request->auto_update_price ?? 0;
            $provider->price_format = $request->price_format ?? '1000';
            $provider->auto_update_name = $request->auto_update_name ?? 0;
            $provider->filter_html = $request->filter_html ?? 0;
            $provider->save();

            return response()->json([
                'success' => true,
                'message' => $balanceWarning
                    ? 'Đã thêm nhà cung cấp. '.$balanceWarning
                    : 'Thêm nhà cung cấp thành công',
                'warning' => $balanceWarning,
                'redirect' => route('admin.providers.index'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $provider = ApiProvider::find($id);
        if (! $provider) {
            return redirect()->route('admin.providers.index')->with('error', 'API Provider không tồn tại');
        }

        return view('admin.providers.edit', compact('provider'));
    }

    public function update(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|string',
            'api_url' => 'required|string|url',
            'api_key' => 'required|string',
            'currency' => 'required|in:USD,VND',
            'exchange_rate' => 'nullable|required_if:currency,USD|numeric|min:1',
            'increase_rate' => 'nullable|numeric',
            'increase_rate_collaborator' => 'nullable|numeric',
            'increase_rate_agency' => 'nullable|numeric',
            'increase_rate_distributor' => 'nullable|numeric',
            'rate_per' => 'required|in:1,1000',
            'status' => 'required|in:active,inactive',
            'note' => 'nullable|string',
            'sync_services' => 'nullable|boolean',
            'auto_update_price' => 'nullable|boolean',
            'price_format' => 'nullable|string',
            'auto_update_name' => 'nullable|boolean',
            'filter_html' => 'nullable|boolean',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        $provider = ApiProvider::find($id);
        if (! $provider) {
            return response()->json(['success' => false, 'message' => 'API Provider không tồn tại']);
        }

        $balance = $provider->balance;
        $currency = $request->currency;

        try {
            $smm = new SmmApiCustom($request->api_url, $request->api_key);
            $getBalance = $smm->balance();

            if (isset($getBalance) && ! isset($getBalance['error'])) {
                $balance = $getBalance['balance'];
                $currency = $request->currency;
            }
        } catch (\Exception $e) {
            // Log error but continue update
            \Illuminate\Support\Facades\Log::warning("Could not update balance for provider {$id}: ".$e->getMessage());
        }

        try {
            $provider->name = $request->name;
            $provider->type = $request->type;
            $provider->balance = $balance;
            $provider->currency = $currency;
            $provider->api_url = $request->api_url;
            $provider->api_key = $request->api_key;
            $provider->exchange_rate = $request->currency === 'VND' ? 1 : $request->exchange_rate;
            $provider->increase_rate = $request->increase_rate ?? 0;
            $provider->increase_rate_collaborator = $request->increase_rate_collaborator ?? 0;
            $provider->increase_rate_agency = $request->increase_rate_agency ?? 0;
            $provider->increase_rate_distributor = $request->increase_rate_distributor ?? 0;
            $provider->rate_per = $request->rate_per;
            $provider->status = $request->status;
            $provider->note = $request->note;
            $provider->sync_services = $request->sync_services ?? 0;
            $provider->auto_update_price = $request->auto_update_price ?? 0;
            $provider->price_format = $request->price_format ?? '1000';
            $provider->auto_update_name = $request->auto_update_name ?? 0;
            $provider->filter_html = $request->filter_html ?? 0;
            $provider->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhà cung cấp thành công',
                'redirect' => route('admin.providers.index'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updatePrices($id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $provider = ApiProvider::find($id);
        if (! $provider) {
            return response()->json(['success' => false, 'message' => 'API Provider không tồn tại']);
        }

        try {
            $services = $provider->services()->where('domain', env('MAIN_SITE'))->get();
            $count = 0;

            $toPercent = function ($val) {
                return $val / 100;
            };

            foreach ($services as $sv) {
                $rateVN = $sv->rate_original;
                $rateNew = round($rateVN * (1 + $provider->increase_rate / 100), 3);

                $price_collaborator = round($rateVN * (1 + $toPercent($provider->increase_rate_collaborator)), 3);
                $price_agency = round($rateVN * (1 + $toPercent($provider->increase_rate_agency)), 3);
                $price_distributor = round($rateVN * (1 + $toPercent($provider->increase_rate_distributor)), 3);

                if (
                    $sv->rate != $rateNew ||
                    $sv->price_collaborator != $price_collaborator ||
                    $sv->price_agency != $price_agency ||
                    $sv->price_distributor != $price_distributor
                ) {
                    $sv->rate = $rateNew;
                    $sv->price_collaborator = $price_collaborator;
                    $sv->price_agency = $price_agency;
                    $sv->price_distributor = $price_distributor;
                    $sv->save();
                    $count++;
                }
            }

            return response()->json(['success' => true, 'message' => "Đã cập nhật lại giá cho {$count} dịch vụ"]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateBalance($id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $provider = ApiProvider::find($id);
        if (! $provider) {
            return response()->json(['success' => false, 'message' => 'API Provider không tồn tại']);
        }

        try {
            $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
            $getBalance = $smm->balance();

            if (! is_array($getBalance)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API nhà cung cấp không phản hồi hoặc trả dữ liệu không hợp lệ.',
                ], 502);
            }

            if (isset($getBalance['error'])) {
                $error = $getBalance['error'];
                $code = is_array($error) ? (int) ($error['code'] ?? 0) : 0;
                $errorMessage = is_array($error) ? (string) ($error['message'] ?? '') : (string) $error;

                if ($code === 429 || stripos($errorMessage, 'Too Many Requests') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'API nhà cung cấp đang giới hạn lượt truy cập. Vui lòng thử lại sau ít phút.',
                        'code' => 429,
                    ], 429);
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage !== '' ? $errorMessage : 'Không thể lấy số dư từ API nhà cung cấp.',
                ], $code >= 400 && $code < 600 ? $code : 502);
            }

            if (! array_key_exists('balance', $getBalance)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phản hồi API không có thông tin số dư.',
                ], 502);
            }

            $provider->balance = $getBalance['balance'];
            $provider->save();

            return response()->json(['success' => true, 'message' => 'Cập nhật số dư thành công']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $provider = ApiProvider::find($id);
        if (! $provider) {
            return response()->json(['success' => false, 'message' => 'API Provider không tồn tại']);
        }

        try {
            $provider->delete();

            return response()->json(['success' => true, 'message' => 'Xóa nhà cung cấp thành công']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
