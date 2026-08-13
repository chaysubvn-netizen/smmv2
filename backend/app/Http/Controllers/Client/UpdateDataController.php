<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Library\CloudflareCustom;
use App\Models\ChildPanel;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

class UpdateDataController extends Controller
{

    public function requestAction(Request $request)
    {
       
        $action = $request->input('action');
        if (!$action) {
            return response()->json([
                'success' => false,
                'message' => 'Thao tác không hợp lệ'
            ]);
        }

        if ($action == "change-currency") {
            $currency = $request->input('code');
            if (!$currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn loại tiền tệ'
                ]);
            }
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại'
                ]);
            }
            $currency = strtoupper($currency);
            $check = \App\Models\Currency::where('code', $currency)->first();
            if (!$check) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Loại tiền tệ không hợp lệ'
                ]);
            }
            $user->currency = $currency;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi loại tiền tệ thành công'
            ]);
        }

        if ($action == 'change-language') {
            $language = $request->input('code');
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn ngôn ngữ'
                ]);
            }
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại'
                ]);
            }
            $user->language = $language == "en" ? "en" : "vi";
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi ngôn ngữ thành công'
            ]);
        }
        
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        if ($action == "enable-2fa") {
            $valid = Validator::make($request->all(), [
                'secret' => 'required',
                'code' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại'
                ]);
            }

            $google2fa = new Google2FA();

            $secret = $request->input('secret');
            $code = $request->input('code');
            $valid = $google2fa->verifyKey($secret, $code);
            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã xác thực không hợp lệ'
                ]);
            }
            $user->two_factor_secret = $secret;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Kích hoạt 2FA thành công'
            ]);
        }

        if ($action == "disable-2fa") {
            $valid = Validator::make($request->all(), [
                'code' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại'
                ]);
            }

            $google2fa = new Google2FA();

            $code = $request->input('code');
            $valid = $google2fa->verifyKey($user->two_factor_secret, $code);
            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã xác thực không hợp lệ'
                ]);
            }
            $user->two_factor_secret = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Tắt 2FA thành công'
            ]);
        }
        




       

        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        if ($action == 'create-childpanel') {
            $valid = Validator::make($request->all(), [
                'website' => 'required',
                'admin_username' => 'required',
                'admin_password' => 'required',
                'panel_name' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            }

            $domain = $request->input('website');

            if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên miền không hợp lệ'
                ]);
            }
            if (preg_match('/[^a-zA-Z0-9.-]/', $domain)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên miền không hợp lệ'
                ]);
            }

            // chỉ lấy miền         // chỉ lấy tên miền chính và .com, .net, .org, .io và .vn nếu không phải thông báo về
            // if (!preg_match('/^[a-zA-Z0-9.-]+\.(com|net|org|io|vn)$/', $domain)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Tên miền chỉ được sử dụng với tên miền chính và .com, .net, .org, .io và .vn'
            //     ]);
            // }

            $user = Auth::user();

            $childpanel = ChildPanel::where('name', $domain)->first();
            if ($childpanel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên miền đã tồn tại'
                ]);
            }

            if ($user->balance < s("fee_childpanel")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số dư không đủ để tạo childpanel bạn cần nạp thêm'
                ]);
            }

            // Bypass Cloudflare for localhost
            if ($domain == 'localhost') {
                $add = [
                    'status' => 'success',
                    'data' => [
                        'zone_id' => 'local_zone_id',
                        'zone_name' => 'localhost',
                        'zone_status' => 'active',
                        'dns_id' => 'local_dns_id',
                        'nameserver1' => 'ns1.localhost',
                        'nameserver2' => 'ns2.localhost',
                    ]
                ];
            } else {
                $clf = new CloudflareCustom();
                $add = $clf->addDomain($domain);
            }

            // dd($add);
            if ($add['status'] == 'success' || (isset($add['code']) && $add['code'] == 1061)) {
                
                // Nếu tên miền đã tồn tại trên Cloudflare (code 1061), thử tìm zone_id
                if (isset($add['code']) && $add['code'] == 1061) {
                    $clf = new CloudflareCustom();
                    $existingDomain = $clf->findDomain($domain);
                    if ($existingDomain) {
                        $add['status'] = 'success';
                        $add['data'] = [
                            'zone_id' => $existingDomain['zone_id'],
                            'zone_name' => $existingDomain['zone_name'],
                            'zone_status' => $existingDomain['zone_status'],
                            'nameserver1' => $existingDomain['name_servers'][0] ?? null,
                            'nameserver2' => $existingDomain['name_servers'][1] ?? null,
                        ];
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Tên miền này đã tồn tại trên một tài khoản Cloudflare khác. Vui lòng xóa nó khỏi tài khoản đó trước khi tiếp tục.'
                        ]);
                    }
                }

                $childpanel = new ChildPanel();
                $childpanel->user_id = $user->id;
                $childpanel->name = $domain;
                $childpanel->status = 'pending';
                $childpanel->status_api = 'pending';

                // Auto active for localhost test
                if ($domain == 'localhost') {
                    $childpanel->status = 'active';
                    $childpanel->status_api = 'active';
                }

                $childpanel->data = array_merge($add['data'] ?? [], [
                    'admin_username' => $request->input('admin_username'),
                    'admin_password' => $request->input('admin_password'),
                    'panel_name' => $request->input('panel_name'),
                ]);
                $childpanel->api_key = md5($domain . time());
                $childpanel->main_domain = s('domain_main');
                $childpanel->domain = $request->getHost();
                $childpanel->expired_at = now()->addMonth();
                $childpanel->save();

                // Create Config record for localhost
                if ($domain == 'localhost') {
                    try {
                        Config::updateOrCreate(
                            ['domain' => $domain],
                            [
                                'user_id' => $childpanel->user_id,
                                'status' => 'active',
                                'domain_main' => $childpanel->domain,
                                'last_payment_time' => now(),
                                'title' => 'Local Child Panel'
                            ]
                        );
                    } catch (\Exception $e) {
                        \Log::error('Localhost Config Creation Failed: ' . $e->getMessage());
                    }
                }

                $user->balance -= s("fee_childpanel");
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'transaction_code' => 'PAY_' . Str::random(10),
                    'type' => 'sub',
                    'balance_before' => $user->balance + s("fee_childpanel"),
                    'balance_after' => $user->balance,
                    'amount' => s("fee_childpanel"),
                    'description' => 'Tạo childpanel: ' . $domain,
                    'status' => 'success',
                    'domain' => $domain
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Tạo childpanel thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra trong quá trình tạo childpanel: ' . ($add['message'] ?? 'Unknown Error')
                ]);
            }
            // } catch (\Throwable $e) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Có lỗi xảy ra trong quá trình tạo childpanel'
            //     ]);
            // }
        }

        if ($action == 'key-childpanel') {
            $valid = Validator::make($request->all(), [
                'password' => 'required',
                'domain' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            }

            $domain = $request->input('domain');
            $password = $request->input('password');
            if (Hash::check($password, Auth::user()->password)) {
                $childpanel = ChildPanel::where('name', $domain)->first();
                if ($childpanel) {

                    return response()->json([
                        'success' => true,
                        'message' => 'Lấy key kích hoạt childpanel thành công',
                        'data' => [
                            'key' => $childpanel->api_key,
                            'domain' => $childpanel->name,
                            'status' => $childpanel->status,
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tên miền không tồn tại'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu không đúng'
                ]);
            }
        }

        if ($action == 'renew-childpanel') {
            $valid = Validator::make($request->all(), [
                'id' => 'required|exists:child_panels,id',
            ]);
            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            }

            $user = Auth::user();
            $childpanel = ChildPanel::where('id', $request->id)->where('user_id', $user->id)->first();
            if (!$childpanel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Childpanel không tồn tại hoặc không thuộc quyền sở hữu của bạn'
                ]);
            }

            $fee = s("fee_childpanel") ?? 0;
            if ($user->balance < $fee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số dư không đủ để gia hạn childpanel, bạn cần nạp thêm'
                ]);
            }

            // Gia hạn
            if ($childpanel->expired_at && $childpanel->expired_at->isFuture()) {
                $childpanel->expired_at = $childpanel->expired_at->addMonth();
            } else {
                $childpanel->expired_at = now()->addMonth();
            }
            
            $childpanel->status = 'active'; 
            $childpanel->save();

            $user->balance -= $fee;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => 'RENEW_' . Str::random(10),
                'type' => 'sub',
                'balance_before' => $user->balance + $fee,
                'balance_after' => $user->balance,
                'amount' => $fee,
                'description' => 'Gia hạn childpanel: ' . $childpanel->name,
                'status' => 'success',
                'domain' => $childpanel->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gia hạn childpanel thành công. Hạn mới: ' . $childpanel->expired_at->format('d/m/Y')
            ]);
        }

        if ($action == 'change-api-key') {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại'
                ]);
            }

            $api_key = bin2hex(random_bytes(16));

            $user->api_key = $api_key;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi API Key thành công',
                'data' => [
                    'api_key' => $api_key
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Thao tác không hợp lệ'
        ]);
    }

    public function changePassword(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $valid = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first()
            ]);
        } else {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại'
                ]);
            }
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Đổi mật khẩu thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu cũ không đúng'
                ]);
            }
        }
    }
}