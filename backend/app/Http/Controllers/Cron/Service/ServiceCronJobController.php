<?php

namespace App\Http\Controllers\Cron\Service;

use App\Http\Controllers\Controller;
use App\Library\SmmApiCustom;
use App\Models\ApiProvider;
use App\Models\Category;
use App\Models\ChildPanel;
use App\Models\Config;
use App\Models\Order;
use App\Models\Platform;
use App\Models\RateUpdate;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceCronJobController extends Controller
{
    public function index(Request $request)
    {
        // Keep the legacy endpoint compatible while using the provider-aware
        // synchronization rules from the unified upsert implementation.
        return $this->autoAdd($request);

        $apiProviders = ApiProvider::where('status', 'active')->get();
        foreach ($apiProviders as $apiProvider) {
            $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
            $resultServices = $smm->services();

            if ($resultServices && is_array($resultServices)) {
                $services = Service::where('provider', $apiProvider->id)->where('domain', env('MAIN_SITE'))->get();
                $resultServicesById = collect($resultServices)->keyBy('service');

                foreach ($services as $sv) {
                    $service = $resultServicesById->get($sv->provider_id);
                    if ($service) {
                        $rate = $service['rate'];
                        $min = $service['min'];
                        $max = $service['max'];
                        $refill = $service['refill'] ?? false;
                        $cancel = $service['cancel'] ?? false;
                        $dripfeed = true;
                        $percent_rate = $apiProvider->increase_rate;
                        $description = $service['description'] ?? $service['desc'] ?? $sv->description;

                        // warranty
                        $attributes = [];
                        $warrantyMappings = warrantyMappingsFC();

                        $found = false;
                        foreach ($warrantyMappings as $key => $phrases) {
                            foreach ($phrases as $phrase) {
                                if (stripos(strtolower($service['name']), strtolower($phrase)) !== false) {
                                    $attributes[] = $key;
                                    $found = true;
                                    break 2;
                                }
                            }
                        }

                        if ($found == false) {
                            $attributes[] = 'no_refill';
                        }

                        $toPercent = function ($val) {
                            return $val / 100;
                        };

                        if ($apiProvider->currency == 'USD') {
                            $rateVN = round($rate * $apiProvider->exchange_rate, 3);
                        } else {
                            $rateVN = $rate;
                        }

                        $price_collaborator = round($rateVN * (1 + $toPercent($apiProvider->increase_rate_collaborator)), 3);
                        $price_agency = round($rateVN * (1 + $toPercent($apiProvider->increase_rate_agency)), 3);
                        $price_distributor = round($rateVN * (1 + $toPercent($apiProvider->increase_rate_distributor)), 3);

                        $rateNew = round($rateVN * (1 + $percent_rate / 100), 3);

                        if (
                            $sv->rate_original != $rateVN ||
                            $sv->rate != $rateNew ||
                            $sv->price_collaborator != $price_collaborator ||
                            $sv->price_agency != $price_agency ||
                            $sv->price_distributor != $price_distributor ||
                            $sv->status != 'active'
                        ) {
                            RateUpdate::create([
                                'service_id' => $sv->id,
                                'rate_old' => $sv->rate,
                                'rate_new' => $rateNew,
                                'type' => $rateNew > $sv->rate ? 'increase' : 'decrease',
                                'domain' => $sv->domain,
                            ]);

                            $sv->rate = $rateNew;
                            $sv->price_collaborator = $price_collaborator;
                            $sv->price_agency = $price_agency;
                            $sv->price_distributor = $price_distributor;
                            $sv->rate_original = $rateVN;
                            $sv->type = $service['type'] ?? 'Default';
                            $sv->min = $min;
                            $sv->max = $max;
                            $sv->refill = $refill;
                            $sv->cancel = $cancel;
                            $sv->dripfeed = $dripfeed;

                            $currentAttributes = is_array($sv->attributes) ? $sv->attributes : [];
                            $warrantyKeys = array_keys($warrantyMappings);
                            $nonWarrantyAttributes = array_filter($currentAttributes, function ($attr) use ($warrantyKeys) {
                                return ! in_array($attr, $warrantyKeys) && $attr !== 'no_refill';
                            });
                            $sv->attributes = array_values(array_unique(array_merge($nonWarrantyAttributes, $attributes)));

                            $sv->status = 'active';
                            $sv->save();
                        }
                    } else {
                        if ($sv->status != 'inactive') {
                            RateUpdate::create([
                                'service_id' => $sv->id,
                                'rate_old' => $sv->rate,
                                'rate_new' => 0,
                                'type' => 'delete',
                                'domain' => $sv->domain,
                            ]);
                            $sv->status = 'inactive';
                            $sv->save();
                        }
                    }
                }
            }
        }
    }

    public function schedule(Request $request)
    {
        return $this->autoAdd($request);

        $apiProviders = ApiProvider::where('status', 'active')->get();
        foreach ($apiProviders as $apiProvider) {
            $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
            $resultServices = $smm->services();
            if ($resultServices) {
                $categories = Category::where('provider', $apiProvider->id)->get();
                if ($categories->count() > 0) {
                    foreach ($categories as $key => $category) {
                        $servicesFromCategory = collect($resultServices)->where('category', $category->name)->values();
                        // dd($category->name);
                        if ($servicesFromCategory->count() > 0) {
                            foreach ($servicesFromCategory as $key => $service) {
                                $serviceFromCategory = Service::where('provider', $apiProvider->id)->where('provider_id', $service['service'])->where('domain', env('MAIN_SITE'))->first();
                                if ($serviceFromCategory) {
                                    continue;
                                } else {
                                    // tạo server mới
                                    $serviceID = $service['service'];
                                    $name = $service['name'];
                                    $rate = $service['rate'];
                                    $min = $service['min'];
                                    $max = $service['max'];
                                    $type = $service['type'] ?? 'Default';
                                    $refill = $service['refill'] ?? false;
                                    $cancel = $service['cancel'] ?? false;
                                    $dripfeed = true;
                                    $percent_rate = $apiProvider->increase_rate;
                                    $description = $service['description'] ?? $service['desc'] ?? '';
                                    // warranty

                                    $attributes = [];
                                    $warrantyMappings = warrantyMappingsFC();

                                    $found = false;
                                    foreach ($warrantyMappings as $key => $phrases) {
                                        foreach ($phrases as $phrase) {
                                            if (stripos(strtolower($service['name']), strtolower($phrase)) !== false) {
                                                $attributes[] = $key;
                                                $found = true;
                                                break 2; // Thoát cả hai vòng lặp nếu tìm thấy
                                            }
                                        }
                                    }

                                    if (! $found) {
                                        $attributes[] = 'no_refill';
                                    }

                                    // % tăng giá
                                    $percent_rate = $apiProvider->increase_rate;

                                    // chuyển rate usd sang rate vn
                                    if ($apiProvider->currency == 'USD') {
                                        $rateVN = round($rate * $apiProvider->exchange_rate, 3);
                                    } else {
                                        $rateVN = $rate;
                                    }

                                    // tính giá dịch vụ theo % tăng giá
                                    $rateNew = round($rateVN * (1 + $percent_rate / 100), 3);

                                    $toPercent = function ($val) {
                                        return $val / 100;
                                    };
                                    $price_collaborator = round($rateVN * (1 + $toPercent($apiProvider->increase_rate_collaborator)), 3);
                                    $price_agency = round($rateVN * (1 + $toPercent($apiProvider->increase_rate_agency)), 3);
                                    $price_distributor = round($rateVN * (1 + $toPercent($apiProvider->increase_rate_distributor)), 3);

                                    $serviceFromCategory = new Service;
                                    $serviceFromCategory->category_id = $category->id;
                                    $serviceFromCategory->name = $name;
                                    $serviceFromCategory->description = $description;
                                    $serviceFromCategory->code = Str::random(10);
                                    $serviceFromCategory->mode = 'option';
                                    $serviceFromCategory->rate_original = $rateVN;
                                    $serviceFromCategory->rate = $rateNew;
                                    $serviceFromCategory->price_collaborator = $price_collaborator;
                                    $serviceFromCategory->price_agency = $price_agency;
                                    $serviceFromCategory->price_distributor = $price_distributor;
                                    $serviceFromCategory->min = $min;
                                    $serviceFromCategory->max = $max;
                                    $serviceFromCategory->provider = $apiProvider->id;
                                    $serviceFromCategory->provider_id = $serviceID;
                                    $serviceFromCategory->type = $type;
                                    $serviceFromCategory->status = 'active';
                                    $serviceFromCategory->warranty = 0;
                                    $serviceFromCategory->refill = $refill;
                                    $serviceFromCategory->cancel = $cancel;
                                    $serviceFromCategory->dripfeed = $dripfeed;
                                    $serviceFromCategory->attributes = $attributes;
                                    $serviceFromCategory->main_domain = env('MAIN_SITE');
                                    $serviceFromCategory->domain = env('MAIN_SITE');
                                    $serviceFromCategory->save();

                                    RateUpdate::create([
                                        'service_id' => $serviceFromCategory->id,
                                        'rate_old' => $serviceFromCategory->rate,
                                        'rate_new' => $rateNew,
                                        'type' => 'add',
                                        'domain' => env('MAIN_SITE'),
                                    ])->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function updateAvgTime(Request $request)
    {
        $services = Service::where('status', 'active')
            ->where('domain', env('MAIN_SITE'))
            ->where('updated_at', '<=', now()->subHours(3))
            ->limit(100) // Reduced limit for better per-run performance
            ->get();

        if ($services->isEmpty()) {
            return;
        }

        // Fetch completed orders for all these services at once
        $orders = Order::whereIn('service_id', $services->pluck('id'))
            ->where('status', 'Completed')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy('service_id');

        foreach ($services as $sv) {
            $serviceOrders = $orders->get($sv->id) ? $orders->get($sv->id)->take(10) : collect();

            if ($serviceOrders->count() >= 10) {
                $time = 0;
                foreach ($serviceOrders as $order) {
                    $time += $order->updated_at->diffInSeconds($order->created_at);
                }
                $avg_seconds = round($time / $serviceOrders->count(), 0);

                $h = floor($avg_seconds / 3600);
                $m = floor(($avg_seconds % 3600) / 60);
                $s = $avg_seconds % 60;
                $avg_time = sprintf('%02d:%02d:%02d', $h, $m, $s);

                $sv->average_time = $avg_time;
            } else {
                $sv->average_time = null;
            }
            $sv->save();
        }
    }

    // for child panel
    // cập nhật lại dịch vụ cho child panel
    public function updateService(Request $request)
    {
        $childPanels = ChildPanel::where('status', 'active')->get();
        if ($childPanels->count() > 0) {
            foreach ($childPanels as $childpn) {
                $configSite = Config::where('domain', $childpn->name)->first();
                $userSite = $configSite->user;
                if ($configSite && $userSite) {
                    $servicesMain = Service::where('domain', $childpn->domain)->get();
                    if ($servicesMain->count() > 0) {
                        foreach ($servicesMain as $serviceMain) {
                            $service = Service::where('provider', $serviceMain->id)->where('provider_id', $serviceMain->id)->where('domain', $childpn->name)->first();
                            $rate_update_percent = $configSite->rate_update_percent ?? 0;
                            $auto_update_service = $configSite->auto_update_service ?? 'inactive';
                            if ($service) {
                                if ($auto_update_service == 'active') {
                                    $rateMain = load_rate($serviceMain->rate, $userSite->level);
                                    $rateChild = $service->rate_original;
                                    if (
                                        $rateChild != $rateMain
                                        || $service->min != $serviceMain->min
                                        || $service->max != $serviceMain->max
                                        || $service->refill != $serviceMain->refill
                                        || $service->cancel != $serviceMain->cancel
                                        || $service->status != $serviceMain->status
                                        || $service->name != $serviceMain->name
                                        || $service->description != $serviceMain->description
                                        || $service->type != $serviceMain->type
                                        || $service->attributes != $serviceMain->attributes
                                        || $service->average_time != $serviceMain->average_time
                                        || $service->dripfeed != $serviceMain->dripfeed
                                    ) {
                                        $rateChild = round(ceil($rateMain * (1 + $rate_update_percent / 100)), 3);
                                        $service->rate = ($rateChild);
                                        $service->rate_original = $rateMain;
                                        $service->min = $serviceMain->min;
                                        $service->max = $serviceMain->max;
                                        $service->refill = $serviceMain->refill;
                                        $service->cancel = $serviceMain->cancel;
                                        $service->dripfeed = $serviceMain->dripfeed;
                                        $service->status = $serviceMain->status;
                                        // $service->name = $serviceMain->name;
                                        // $service->description = $serviceMain->description;
                                        $service->type = $serviceMain->type;
                                        $service->attributes = $serviceMain->attributes;
                                        $service->average_time = $serviceMain->average_time;
                                        $service->save();
                                    }
                                }

                                continue;
                            } else {
                                // tạo mới
                                $rateMain = load_rate($serviceMain->rate, $userSite->level);
                                $rateChild = $serviceMain->rate_original;
                                $rateChild = round(ceil($rateMain * (1 + $rate_update_percent / 100)), 3);
                                $service = new Service;
                                $service->category_id = $serviceMain->category_id;

                                $service->provider = $serviceMain->id;
                                $service->provider_id = $serviceMain->id;
                                $service->main_domain = $serviceMain->domain;
                                $service->domain = $childpn->name;
                                $service->rate = ($rateChild);
                                $service->rate_original = $rateMain;
                                $service->min = $serviceMain->min;
                                $service->max = $serviceMain->max;
                                $service->refill = $serviceMain->refill;
                                $service->cancel = $serviceMain->cancel;
                                $service->dripfeed = $serviceMain->dripfeed;
                                $service->warranty = $serviceMain->warranty;
                                $service->status = $serviceMain->status;
                                $service->name = $serviceMain->name;
                                $service->description = $serviceMain->description;
                                $service->type = $serviceMain->type;
                                $service->attributes = $serviceMain->attributes;
                                $service->average_time = $serviceMain->average_time;
                                $service->save();
                            }
                        }
                    }
                    // cập nhật lại các trường còn lại
                }
            }
        }
    }

    public function autoAdd(Request $request)
    {
        $apiProviders = ApiProvider::where('status', 'active')->where('sync_services', 1)
            ->when($request->filled('provider_id'), fn ($query) => $query->whereKey($request->integer('provider_id')))
            ->get();
        $domain = $request->getHost();
        if (str_starts_with($domain, 'api.')) {
            $domain = substr($domain, 4);
        }
        $summary = ['providers' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($apiProviders as $apiProvider) {
            try {
                $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
                $resultServices = $smm->services();

                if (! is_array($resultServices) || isset($resultServices['error'])) {
                    $summary['failed']++;
                    Log::warning('Provider service source unavailable', [
                        'provider_id' => $apiProvider->id,
                        'error' => $resultServices['error'] ?? 'Invalid response',
                    ]);

                    continue;
                }

                $summary['providers']++;
                foreach ($resultServices as $source) {
                    try {
                        if (! is_array($source) || empty($source['service']) || empty($source['category']) || empty($source['name'])) {
                            $summary['skipped']++;

                            continue;
                        }

                        $categoryName = trim((string) $source['category']);
                        $category = Category::where('name', $categoryName)->where('provider', $apiProvider->id)->first();
                        if (! $category) {
                            $platform = $this->resolveSourcePlatform($categoryName);
                            if (! $platform) {
                                $summary['skipped']++;
                                continue;
                            }
                            $baseCode = Str::slug($categoryName) ?: 'category';
                            $code = $baseCode.'-'.$apiProvider->id;
                            while (Category::where('code', $code)->exists()) {
                                $code = $baseCode.'-'.$apiProvider->id.'-'.Str::lower(Str::random(4));
                            }
                            $icon = $platform->getRawOriginal('icon');
                            if ($icon) {
                                $sourcePath = public_path('uploads/platforms/'.basename((string) $icon));
                                $targetDirectory = public_path('uploads/categories');
                                $targetPath = $targetDirectory.'/'.basename((string) $icon);
                                if (is_file($sourcePath)) {
                                    if (! is_dir($targetDirectory)) {
                                        mkdir($targetDirectory, 0777, true);
                                    }
                                    if (! is_file($targetPath)) {
                                        copy($sourcePath, $targetPath);
                                    }
                                }
                            }
                            $category = Category::create([
                                'platform_id' => $platform->id,
                                'name' => $categoryName,
                                'provider' => $apiProvider->id,
                                'status' => 'active',
                                'code' => $code,
                                'sort_order' => 0,
                                'icon' => $icon,
                            ]);
                        }

                        $name = trim((string) $source['name']);
                        $description = (string) ($source['description'] ?? $source['desc'] ?? '');
                        if ($apiProvider->filter_html) {
                            $name = trim(strip_tags($name));
                            $description = trim(strip_tags($description));
                        }

                        $sourceRate = (float) ($source['rate'] ?? 0);
                        $ratePer = max(1, (int) ($apiProvider->rate_per ?: 1000));
                        $normalizedRate = $sourceRate * (1000 / $ratePer);
                        $rateOriginal = strtoupper((string) $apiProvider->currency) === 'USD'
                            ? round($normalizedRate * (float) $apiProvider->exchange_rate, 3)
                            : round($normalizedRate, 3);
                        $prices = [
                            'rate' => round($rateOriginal * (1 + (float) $apiProvider->increase_rate / 100), 3),
                            'price_collaborator' => round($rateOriginal * (1 + (float) $apiProvider->increase_rate_collaborator / 100), 3),
                            'price_agency' => round($rateOriginal * (1 + (float) $apiProvider->increase_rate_agency / 100), 3),
                            'price_distributor' => round($rateOriginal * (1 + (float) $apiProvider->increase_rate_distributor / 100), 3),
                        ];
                        $attributes = $this->warrantyAttributes($name);
                        $sourceId = (string) $source['service'];
                        $existing = Service::where('provider', $apiProvider->id)
                            ->where('provider_id', $sourceId)
                            ->where('domain', $domain)
                            ->first();

                        if (! $existing) {
                            $newService = Service::create([
                                'category_id' => $category->id,
                                'name' => $name,
                                'description' => $description,
                                'code' => Str::random(10),
                                'mode' => 'option',
                                'rate_original' => $rateOriginal,
                                ...$prices,
                                'min' => (int) ($source['min'] ?? 0),
                                'max' => (int) ($source['max'] ?? 0),
                                'provider' => $apiProvider->id,
                                'provider_id' => $sourceId,
                                'type' => (string) ($source['type'] ?? 'Default'),
                                'status' => 'active',
                                'warranty' => 0,
                                'refill' => (bool) ($source['refill'] ?? false),
                                'cancel' => (bool) ($source['cancel'] ?? false),
                                'dripfeed' => (bool) ($source['dripfeed'] ?? false),
                                'attributes' => $attributes,
                                'main_domain' => $domain,
                                'domain' => $domain,
                            ]);
                            RateUpdate::create(['service_id' => $newService->id, 'rate_old' => 0, 'rate_new' => $prices['rate'], 'type' => 'add', 'domain' => $domain]);
                            $summary['created']++;

                            continue;
                        }

                        $oldRate = (float) $existing->rate;
                        $updates = [
                            'category_id' => $category->id,
                            'min' => (int) ($source['min'] ?? 0),
                            'max' => (int) ($source['max'] ?? 0),
                            'type' => (string) ($source['type'] ?? 'Default'),
                            'refill' => (bool) ($source['refill'] ?? false),
                            'cancel' => (bool) ($source['cancel'] ?? false),
                            'dripfeed' => (bool) ($source['dripfeed'] ?? false),
                            'attributes' => $attributes,
                            'status' => 'active',
                        ];
                        if ($apiProvider->auto_update_price) {
                            $updates = [...$updates, 'rate_original' => $rateOriginal, ...$prices];
                        }
                        if ($apiProvider->auto_update_name) {
                            $updates['name'] = $name;
                            $updates['description'] = $description;
                        }

                        $existing->fill($updates);
                        if (! $existing->isDirty()) {
                            $summary['unchanged']++;

                            continue;
                        }
                        $existing->save();
                        if ($apiProvider->auto_update_price && $oldRate !== (float) $existing->rate) {
                            RateUpdate::create([
                                'service_id' => $existing->id,
                                'rate_old' => $oldRate,
                                'rate_new' => $existing->rate,
                                'type' => (float) $existing->rate > $oldRate ? 'increase' : 'decrease',
                                'domain' => $domain,
                            ]);
                        }
                        $summary['updated']++;
                    } catch (\Throwable $e) {
                        $summary['failed']++;
                        Log::error('Failed to sync provider service', ['provider_id' => $apiProvider->id, 'service_id' => $source['service'] ?? null, 'error' => $e->getMessage()]);
                    }
                }
            } catch (\Throwable $e) {
                $summary['failed']++;
                Log::error('Failed to sync provider', ['provider_id' => $apiProvider->id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json(['status' => $summary['failed'] === 0, 'data' => $summary]);
    }

    private function warrantyAttributes(string $name): array
    {
        foreach (warrantyMappingsFC() as $key => $phrases) {
            foreach ($phrases as $phrase) {
                if (stripos($name, (string) $phrase) !== false) {
                    return [$key];
                }
            }
        }

        return ['no_refill'];
    }

    private function resolveSourcePlatform(string $categoryName): ?Platform
    {
        $normalized = Str::lower(Str::ascii($categoryName));
        // Reload so a platform added while a long provider sync is running can
        // immediately receive its source categories in the same cron pass.
        $currentPlatforms = Platform::where('status', 'active')->get();
        $existing = $currentPlatforms->first(function (Platform $platform) use ($normalized) {
            $name = trim(Str::lower(Str::ascii((string) $platform->name)));
            return $name !== '' && str_contains($normalized, $name);
        });
        if ($existing && Platform::whereKey($existing->id)->where('status', 'active')->exists()) {
            return $existing;
        }

        return null;
    }
}
