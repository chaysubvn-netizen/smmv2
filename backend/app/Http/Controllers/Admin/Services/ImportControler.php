<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Library\SmmApiCustom;
use App\Models\ApiProvider;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;


class ImportControler extends Controller
{
    public function categories(Request $request)
    {
        
        if ($request->getHost() == env("MAIN_SITE")) {
            $platforms = Platform::where('status', 'active')->get();
            $categories = Category::where('status', 'active')->get();
            $providers = ApiProvider::where('status', 'active')->get();

            $platform_id = $request->get('platform_id');
            $keyword = $request->get('keyword');
            $provider_id = $request->get('provider_id');

            $ListServices = [];
            $errMsg = "Chưa nhập dịch vụ";
            if ($platform_id && $provider_id && $keyword) {
                $platform = Platform::find($platform_id);
                $provider = ApiProvider::find($provider_id);
                if ($platform && $provider) {
                    $cacheKey = 'provider_services_' . $provider->id;
                    if ($request->has('refresh')) {
                        Cache::forget($cacheKey);
                    }
                    $ss = Cache::remember($cacheKey, 300, function() use ($provider) {
                        $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                        return $smm->services();
                    });

                    if (isset($ss) && !isset($ss['error'])) {
                        if (count($ss) == 0) {
                            $errMsg = 'Không có dịch vụ nào';
                        } else {
                            foreach ($ss as $key => $value) {
                                $category = $value['category'];
                                $platform_name = $value['category'];

                                if (strpos(strtolower($category), strtolower($keyword)) !== false) {
                                    $category_name = $value['category'];

                                    $ListServices[] = [
                                        'platform' => $platform_name,
                                        'category' => $value['category'],
                                        'category_name' => $category_name,
                                    ];
                                    // }
                                }
                            }
                            if (count($ListServices) == 0) {
                                $errMsg = 'Không có dịch vụ nào';
                                
                            } else {
                                $errMsg = '';
                            }
                            $ListServices = array_values(array_unique($ListServices, SORT_REGULAR));
                        }
                    } else {
                        $providerError = $ss['error'];
                        if (is_array($providerError)) {
                            $code = (int) ($providerError['code'] ?? 0);
                            $detail = (string) ($providerError['message'] ?? 'Không thể tải dữ liệu từ Provider');
                            $errMsg = $code === 429
                                ? 'API Provider đang giới hạn lượt truy cập. Vui lòng chờ 1–5 phút rồi thử lại.'
                                : $detail;
                        } else {
                            $errMsg = (string) $providerError;
                        }
                    }
                }
            } else {
                $errMsg = 'Chưa đầy đủ thông tin';
            }

            return view('admin.services.import.categories', compact('platforms', 'categories', 'providers', 'ListServices', 'errMsg'));
        } else {
            return abort(404);
        }
    }

    public function add(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        $valid = Validator::make($request->all(), [
            'type' => 'required|string',
            'service_ids' => 'required|array',
            'service_ids.*' => 'required|string',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first()
            ]);
        } else {
            if ($request->type == 'categories') {
                $platform = Platform::find($request->platform_id);
                $provider = ApiProvider::find($request->provider_id);
                if (!$platform || !$provider) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nền tảng hoặc nhà cung cấp không tồn tại'
                    ]);
                }

                $cacheKey = 'provider_services_' . $provider->id;
                $ss = Cache::get($cacheKey);
                if ($ss === null) {
                    $ss = (new SmmApiCustom($provider->api_url, $provider->api_key))->services();
                    if (!isset($ss['error'])) {
                        Cache::put($cacheKey, $ss, 300);
                    }
                }
                $count = 0;
                foreach ($request->input('service_ids') as $key => $value) {
                    $check = Category::where('platform_id', $platform->id)->where('name', $value)->first();
                    if ($check) {
                        continue;
                    } else {
                        $icon = $platform->icon;
                        $filename = null;
                        if ($icon) {
                            $filename = time() . '_' . $platform->id . '_' . $key . '.' . pathinfo($icon, PATHINFO_EXTENSION);
                            $path = public_path($icon);
                            if (file_exists($path)) {
                                $destinationPath = public_path('uploads/categories');
                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0777, true);
                                }
                                $destinationPath = public_path('uploads/categories/' . $filename);
                                copy($path, $destinationPath);
                            } else {
                                $filename = null;
                            }
                        }

                        try {
                            $category = new Category();
                            $category->platform_id = $platform->id;
                            $category->icon = $filename;
                            $category->sort_order = 0;
                            $category->code = Str::random(10);
                            $category->name = $value;
                            $category->provider = $provider->id;
                            $category->status = 'active';
                            $category->save();

                            if (isset($ss) && !isset($ss['error'])) {
                                foreach ($ss as $key => $val) {
                                    $category_name = $val['category'];

                                    if (strpos(strtolower($value), strtolower($category_name)) !== false) {
                                        $name = $val['name'];
                                        $serviceID = $val['service'];
                                        $rate = $val['rate'];
                                        $refill = $val['refill'] ?? false;
                                        $cancel = $val['cancel'] ?? false;
                                        $min = $val['min'] ?? 100;
                                        $max = $val['max'] ?? 100000000;
                                        $description = $val['description'] ?? null;
                                        $type = $val['type'] ?? 'Default';
                                        $dripfeed = true;

                                        if ($description == null) {
                                            $description = $val['desc'] ?? "";
                                        }

                                        $checkService = Service::where('category_id', $category->id)
                                            ->where('provider', $provider->id)
                                            ->where('provider_id', $serviceID)
                                            ->where('domain', env("MAIN_SITE"))
                                            ->first();
                                        if (!$checkService) {
                                            $attributes = [];
                                            $warrantyMappings = warrantyMappingsFC();

                                            $found = false;
                                            foreach ($warrantyMappings as $key => $phrases) {
                                                foreach ($phrases as $phrase) {
                                                    if (stripos(strtolower($val['name']), strtolower($phrase)) !== false) {
                                                        $attributes[] = $key;
                                                        $found = true;
                                                        break 2; // Thoát cả hai vòng lặp nếu tìm thấy
                                                    }
                                                }
                                            }

                                            if (!$found) {
                                                $attributes[] = 'no_refill';
                                            }

                                            // % tăng giá
                                            $percent_rate = $provider->increase_rate;

                                            // chuyển rate usd sang rate vn
                                            if ($provider->currency == "USD") {
                                                $rateVN = round($rate * $provider->exchange_rate, 3);
                                            } else {
                                                $rateVN = $rate;
                                            }

                                            // tính giá dịch vụ theo % tăng giá
                                            $rateNew = round($rateVN * (1 + $percent_rate / 100), 3);

                                            $toPercent = function($val) { return $val / 100; };
                                            $price_collaborator = round($rateVN * (1 + $toPercent($provider->increase_rate_collaborator)), 3);
                                            $price_agency       = round($rateVN * (1 + $toPercent($provider->increase_rate_agency)), 3);
                                            $price_distributor  = round($rateVN * (1 + $toPercent($provider->increase_rate_distributor)), 3);

                                            $service = new Service();
                                            $service->category_id = $category->id;
                                            $service->name = $name;
                                            $service->description = $description;
                                            $service->code = Str::random(10);
                                            $service->mode = "option";
                                            $service->rate_original = $rateVN;
                                            $service->rate = $rateNew;
                                            $service->price_collaborator = $price_collaborator;
                                            $service->price_agency = $price_agency;
                                            $service->price_distributor = $price_distributor;
                                            $service->min = $min;
                                            $service->max = $max;
                                            $service->provider = $provider->id;
                                            $service->provider_id = $serviceID;
                                            $service->type = $type;
                                            $service->status = 'active';
                                            $service->warranty = 0;
                                            $service->refill = $refill;
                                            $service->cancel = $cancel;
                                            $service->dripfeed = $dripfeed;
                                            $service->attributes = $attributes;
                                            $service->main_domain = env("MAIN_SITE");
                                            $service->domain = env("MAIN_SITE");
                                            $service->save();
                                            $count++;
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Nhập thành công danh mục',
                    'count' => $count,
                    'redirect' => route('admin.import.categories.index')
                ]);
            }
        }
    }

    public function services(Request $request)
    {
        if (request()->getHost() == env("MAIN_SITE")) {
            $categories = Category::where('status', 'active')->get();
            $providers = ApiProvider::where('status', 'active')->get();

            $services = [];
            if ($request->has('category_id') && $request->has('provider_id')) {
                $category = Category::find($request->get('category_id'));
                $provider = ApiProvider::find($request->get('provider_id'));

                if ($category && $provider) {
                    $cacheKey = 'provider_services_' . $provider->id;
                    if ($request->has('refresh')) {
                        Cache::forget($cacheKey);
                    }
                    $ss = Cache::remember($cacheKey, 300, function() use ($provider) {
                        $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                        return $smm->services();
                    });

                    if (isset($ss) && !isset($ss['error'])) {
                        foreach ($ss as $key => $value) {

                            $rate = $value['rate'];
                            $rate_vnd = $rate * $provider->exchange_rate;

                            $services[] = [
                                'id' => $value['service'],
                                'name' => $value['name'],
                                'service' => $value['service'],
                                'category' => $value['category'],
                                'type' => $value['type'],
                                'rate' => $rate,
                                'rate_vnd' => $rate_vnd,
                                'rate_format_vnd' => convert_currency($rate_vnd, "VND", false),
                                'rate_format_usd' => convert_currency($rate_vnd, "USD", false),
                                'min' => $value['min'],
                                'max' => $value['max'],
                                'description' => $value['description'] ?? "",
                            ];
                        }
                    }
                }
            }

            return view('admin.services.import.services', compact('categories', 'providers', 'services'));
        } else {
            return abort(404);
        }
    }

    public function addServices(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'provider_id' => 'required|integer',
            'category_id' => 'required|integer',
            'ids' => 'required|array',
            'ids.*' => 'required|string',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first()
            ]);
        } else {
            $requestHost = trim((string) $request->header('X-Site-Host', $request->getHost()));
            $requestHost = preg_replace('/:\d+$/', '', $requestHost);
            $domain = str_starts_with($requestHost, 'api.') ? substr($requestHost, 4) : $requestHost;
            $category = Category::find($request->category_id);
            $provider = ApiProvider::find($request->provider_id);
            if (!$category || !$provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nền tảng hoặc nhà cung cấp không tồn tại'
                ]);
            }

            $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
            $ss = $smm->services();
            $count = 0;
            foreach ($request->input('ids') as $key => $value) {
                $checkService = Service::where('category_id', $category->id)
                    ->where('provider', $provider->id)
                    ->where('provider_id', $value)
                    ->where('domain', $domain)
                    ->first();
                if (!$checkService) {
                    foreach ($ss as $key => $val) {
                        if ($val['service'] == $value) {
                            try {
                                $name = $val['name'];
                                $serviceID = $val['service'];
                                $rate = $val['rate'];
                                $refill = $val['refill'] ?? false;
                                $cancel = $val['cancel'] ?? false;
                                $min = $val['min'] ?? 100;
                                $max = $val['max'] ?? 100000000;
                                $description = $val['description'] ?? null;
                                if ($description == null) {
                                    $description = "";
                                }
                                // % tăng giá
                                // chuyển rate usd sang rate vn
                                if ($provider->currency == "USD") {
                                    $rateVN = round($rate * $provider->exchange_rate, 3);
                                } else {
                                    $rateVN = round($rate, 3);
                                }

                                // tính giá dịch vụ theo % tăng giá
                                $rateNew = round($rateVN * (1 + $provider->increase_rate / 100), 3);
                                
                                $toPercent = function($val) { return $val / 100; };
                                $price_collaborator = round($rateVN * (1 + $toPercent($provider->increase_rate_collaborator)), 3);
                                $price_agency       = round($rateVN * (1 + $toPercent($provider->increase_rate_agency)), 3);
                                $price_distributor  = round($rateVN * (1 + $toPercent($provider->increase_rate_distributor)), 3);
                                $attributes = [];
                                $warrantyMappings = warrantyMappingsFC();
                                $found = false;
                                foreach ($warrantyMappings as $key => $phrases) {
                                    foreach ($phrases as $phrase) {
                                        if (stripos(strtolower($val['name']), strtolower($phrase)) !== false) {
                                            $attributes[] = $key;
                                            $found = true;
                                            break 2; // Thoát cả hai vòng lặp nếu tìm thấy
                                        }
                                    }
                                }
                                if (!$found) {
                                    $attributes[] = 'no_refill';
                                }
                                $service = new Service();
                                $service->category_id = $category->id;
                                $service->name = $name;
                                $service->description = $description;
                                $service->code = Str::random(10);
                                $service->mode = "option";
                                $service->rate_original = $rateVN;
                                $service->rate = $rateNew;
                                $service->price_collaborator = $price_collaborator;
                                $service->price_agency = $price_agency;
                                $service->price_distributor = $price_distributor;
                                $service->min = $min;
                                $service->max = $max;
                                $service->provider = $provider->id;
                                $service->provider_id = $serviceID;
                                $service->type = $val['type'];
                                $service->status = 'active';
                                $service->warranty = 0;
                                $service->refill = $refill;
                                $service->cancel = $cancel;
                                $service->dripfeed = true;
                                $service->attributes = $attributes;
                                $service->main_domain = $domain;
                                $service->domain = $domain;
                                $service->save();
                                $count++;
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Lỗi khi thêm dịch vụ: ' . $e->getMessage()
                                ]);
                            }
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Nhập thành công dịch vụ',
                        'count' => $count,
                        'redirect' => route('admin.import.services.index')
                    ]);
                }
            }
        }
    }
}
