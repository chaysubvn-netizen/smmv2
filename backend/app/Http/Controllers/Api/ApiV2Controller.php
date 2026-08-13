<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\SmmApiCustom;
use App\Models\ApiProvider;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ApiV2Controller extends Controller
{
    public function handle(Request $request)
    {
        $key = $request->input('key') ?: $request->route('key');
        $action = $request->input('action');
        if (!$action) {
            return response()->json([
                'error' => "Incorrect request"
            ]);
        }

        if (!$key) {
            return response()->json([
                'error' => "Invalid key 1"
            ]);
        }

        $user = $this->user($key, $request);
        if (!$user) {
            return response()->json([
                'error' => "Invalid key 2"
            ]);
        }

        switch ($action) {
            case 'balance':
                return $this->balance($user);
                break;
            case 'services':
                return $this->services($user, $request);
                break;
            case 'status':
                return $this->status($user, $request);
                break;
            case 'add':
                return $this->add($user, $request);
                break;
            case 'refill':
                return $this->refill($user, $request);
                break;
            case 'refill_status':
                return $this->refill_status($user, $request);
                break;
            default:
                return response()->json([
                    'error' => "Incorrect request"
                ]);
        }
    }

    protected function  balance(User $user)
    {
        $balanceUsd = convert_currency($user->balance, 'USD');
        return response()->json([
            'balance' => $balanceUsd,
            'currency' => "USD",
        ]);
    }

    protected function services(User $user, Request $request)
    {
        $domains = $this->tenantDomains($request);
        $services = Service::with('category:id,name')
            ->where('status', 'active')
            ->where(function ($query) use ($domains) {
                $query->whereIn('domain', $domains)
                    ->orWhereNull('domain')
                    ->orWhere('domain', '');
            })
            ->get();
        $servicesArray = [];
        foreach ($services as $service) {
            $servicesArray[] = [
                'service' => $service->id,
                "name" => $service->name,
                "type" => $service->type,
                "rate" => convert_currency(load_rate($service, $user->level), 'USD'),
                "avg_time" => $service->average_time,
                "min" => $service->min,
                "max" => $service->max,
                "dripfeed" => $service->dripfeed ?? false,
                "refill" => $service->refill,
                "cancel" => $service->cancel,
                "description" => $service->description,
                "status" => true,
                "category" => $service->category->name,
            ];
        }
        return response()->json($servicesArray);
    }

    protected function status(User $user, Request $request)
    {
        $listOrderId = $request->has('orders') ? $request->input('orders') : $request->input('order');
        if (!$listOrderId) {
            return response()->json([
                'error' => "Incorrect request"
            ]);
        }

        $listOrderId = explode(',', $listOrderId);
        $orders = [];
        $listOrder = Order::whereIn('id', $listOrderId)
            ->where('user_id', $user->id)
            ->whereIn('domain', $this->tenantDomains($request))
            ->get();
        foreach ($listOrder as $order) {
            $orders[$order->id] = [
                'charge' => convert_currency($order->total, 'USD'),
                'start_count' => $order->start_count,
                'remains' => $order->remains,
                "status" => $order->status,
            ];
        }
        return response()->json($orders);
    }

    protected function add(User $user, Request $request)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://' . $request->getHost() . '/api/request-order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($request->all()),
            CURLOPT_HTTPHEADER => [
                'Api-key: ' . $user->api_key,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        if ($response === false) {
            return response()->json([
                'error' => "Incorrect request"
            ]);
        }
        $result = json_decode($response, true);
        if (isset($result['success']) && $result['success'] == true) {
            return response()->json([
                'order' => $result['data']['order_id'],
            ]);
        } else {
            // dd($result);
            return response()->json([
                'error' => $result['message'] ?? "Incorrect request"
            ]);
        }
    }

    public function refill(User $user, Request $request)
    {
        $orderIds = $request->has('orders') ? $request->input('orders') : $request->input('order');
        if (!$orderIds) {
            return response()->json([
                'error' => "Incorrect order id"
            ]);
        }

        $orderIds = explode(',', $orderIds);
        $orders = Order::whereIn('id', $orderIds)
            ->where('user_id', $user->id)
            ->whereIn('domain', $this->tenantDomains($request))
            ->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'error' => "Incorrect order id"
            ]);
        }


        $responseArr = [];
        foreach ($orders as $order) {
            $service = $order->service;
            if (!$service) {
                $responseArr[] = [
                    'order' => $order->id,
                    'refill' => [
                        "error" => "Service not found"
                    ]
                ];
                continue;
            }

            if ($order->refill == false) {
                $responseArr[] = [
                    'order' => $order->id,
                    'refill' => [
                        "error" => "Refill not available"
                    ]
                ];
                continue;
            } else {
                $provider = ApiProvider::where('id', $service->provider)->first();
                if (!$provider) {
                    $responseArr[] = [
                        'order' => $order->id,
                        'refill' => [
                            "error" => "Incorrect order"
                        ]
                    ];
                    continue;
                }

                $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                $result = $smm->refill($order->root_id);
                if (isset($result['refill']) && $result['refill'] == true) {
                    $responseArr[] = [
                        'order' => $order->id,
                        'refill' => true
                    ];
                } else {
                    $responseArr[] = [
                        'order' => $order->id,
                        'refill' => [
                            "error" => $result['error'] ?? "Incorrect order"
                        ]
                    ];
                }
            }
        }

        return response()->json($responseArr);
    }

    public function refill_status(User $user, Request $request)
    {
        $refillIds = $request->has('refills') ? $request->input('refills') : $request->input('refill');
        if (!$refillIds) {
            return response()->json([
                'error' => "Incorrect refill id"
            ]);
        }
        $refillIds = explode(',', $refillIds);
        $orders = Order::whereIn('id', $refillIds)
            ->where('user_id', $user->id)
            ->whereIn('domain', $this->tenantDomains($request))
            ->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'error' => "Incorrect refill id"
            ]);
        }
        $responseArr = [];
        foreach ($orders as $order) {
            $service = $order->service;
            if (!$service) {
                $responseArr[] = [
                    'refill' => $order->id,
                    "status" => [
                        "error" => "Incorrect ID"
                    ]
                ];
                continue;
            }

            if ($order->refill == false) {
                $responseArr[] = [
                    'refill' => $order->id,
                    "status" => [
                        "error" => "Incurrect ID"
                    ]
                ];
                continue;
            } else {
                $provider = ApiProvider::where('id', $service->provider)->first();
                if (!$provider) {
                    $responseArr[] = [
                        'refill' => $order->id,
                        "status" => [
                            "error" => "Incurrect ID"
                        ]
                    ];
                    continue;
                }

                $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                $result = $smm->refillStatus($order->root_id);
                if (isset($result['status']) && !is_array($result['status'])) {
                    $responseArr[] = [
                        'refill' => $order->id,
                        "status" => $result['status']
                    ];
                } else {
                    $responseArr[] = [
                        'refill' => $order->id,
                        "status" => [
                            "error" => $result['error'] ?? "Incorrect ID"
                        ]
                    ];
                }
            }
        }
        return response()->json($responseArr);
    }

    protected function user($key, Request $request)
    {
        return User::where('api_key', $key)
            ->where(function ($query) use ($request) {
                $query->whereIn('domain', $this->tenantDomains($request))
                    ->orWhereNull('domain')
                    ->orWhere('domain', '');
            })
            ->first();
    }

    private function tenantDomains(Request $request): array
    {
        $host = strtolower(preg_replace('/:\d+$/', '', $request->getHost()));
        $siteHost = str_starts_with($host, 'api.') ? substr($host, 4) : $host;

        return array_values(array_unique([$siteHost, 'api.'.$siteHost]));
    }
}
