<?php

namespace App\Http\Controllers\Auth;

use Akaunting\Money\Money;
use AmrShawky\Currency\Facade\Currency as CurrencyCurrency;
use App\Http\Controllers\Controller;
use App\Models\AffiliateRef;
use App\Models\ChildPanel;
use App\Models\Config;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class AppAuthController extends Controller
{
    public function login(Request $request)
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        return view('auth.register');
    }

    public function DoRegister(Request $request)
    {
        
        $valid = Validator::make($request->all(), [
            'username' => 'required|string|min:6|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
            'condition' => 'required|accepted',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            if ($request->hasCookie('ref_by_code')) {
                $ref_by_code = $request->cookie('ref_by_code');
                $user_ref = User::where('referral_code', $ref_by_code)->where('domain', $request->getHost())->first();
                if ($user_ref) {
                    $request->merge(['ref_by_code' => $user_ref->referral_code]);
                } else {
                    $request->merge(['ref_by_code' => null]);
                }
            }

            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'user';
            $host = $request->getHost();
            if (strpos($host, 'api.') === 0) {
                $host = substr($host, 4);
            }
            $user->domain = $host;
            $user->timezone = 'Asia/Ho_Chi_Minh';
            $user->language = 'vi';
            $user->currency = 'USD';
            $user->status = 'active';
            $user->referral_code = $this->generateReferralCode();
            $user->ref_by_code = $request->ref_by_code ?? null;
            $user->api_key = $this->generateApiKey();
            $user->save();

            if ($request->ref_by_code) {
                $user_ref = User::where('referral_code', $request->ref_by_code)->where('domain', $request->getHost())->first();
                if ($user_ref) {
                    try {
                        $affiliateRef = new AffiliateRef();
                        $affiliateRef->user_id = $user->id;
                        $affiliateRef->ref_user_id = $user_ref->id;
                        $affiliateRef->referral_code = $user_ref->referral_code;
                        $affiliateRef->domain = request()->getHost();
                        $affiliateRef->save();
                        cookie('ref_by_code', null, -1);
                        $request->session()->forget('ref_by_code');
                        $request->session()->save();
                    } catch (\Exception $e) {
                        cookie('ref_by_code', null, -1);
                        $request->session()->forget('ref_by_code');
                        $request->session()->save();
                    }
                }
            }

            return redirect()->route('auth.login')->with('success', 'Đăng ký thành công');
        }
    }

    public function DoLogin(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'username' => 'required|string|min:6|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            // return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            return response()->json([
                'status' => false,
                'message' => $valid->errors()->first(),
            ]);
        } else {
            $host = $request->getHost();
            if (strpos($host, 'api.') === 0) {
                $host = substr($host, 4);
            }
            $user = User::where('username', $request->username)->where('domain', $host)->first();
            if ($user) {
                if ($user->status != 'active') {
                    // return redirect()->back()->with('error', 'Tài khoản của bạn đã bị khóa')->withInput();
                    return response()->json([
                        'status' => false,
                        'message' => 'Tài khoản của bạn đã bị khóa',
                    ]);
                }

                if (Hash::check($request->password, $user->password)) {
                    if ($user->two_factor_secret != null) {
                        if ($request->two_factor_code == null) {
                            return response()->json([
                                'status' => true,
                                'message' => 'Tài khoản của bạn đã được kích hoạt xác thực 2 yếu tố',
                                'two_factor_auth' => true,
                            ]);
                        } else {
                            $google2fa = new Google2FA();
                            $valid = $google2fa->verifyKey($user->two_factor_secret, $request->two_factor_code);
                            if (!$valid) {
                                // return redirect()->back()->with('error', 'Mã xác thực không đúng')->withInput();
                                return response()->json([
                                    'status' => true,
                                    'message' => 'Mã xác thực không đúng',
                                    'two_factor_auth' => true,
                                ]);
                            }
                        }
                    }
                }

                if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->has('remeber'))) {

                    $user->last_login = now();
                    $user->save();
                    Auth::login($user);

                    // return redirect()->route('new')->with('success', 'Đăng nhập thành công');
                    return response()->json([
                        'status' => true,
                        'message' => 'Đăng nhập thành công',
                        'redirect' => route('new'),
                    ]);
                } else {
                    // return redirect()->back()->with('error', 'Tài khoản hoặc mật khẩu không đúng')->withInput();
                    return response()->json([
                        'status' => false,
                        'message' => 'Tài khoản hoặc mật khẩu không đúng',
                    ]);
                }
            } else {
                // return redirect()->back()->with('error', 'Tài khoản không tồn tại')->withInput();
                return response()->json([
                    'status' => false,
                    'message' => 'Tài khoản không tồn tại',
                ]);
            }
        }
    }

    public function DoInstall(Request $request)
    {
        // Security Fix: Prevent re-installation if already configured
        if (Config::where('domain', request()->getHost())->exists()) {
            return redirect()->route('auth.login')->with('error', 'Hệ thống đã được cài đặt. Không thể cài đặt lại!');
        }

        if ($request->getHost() == env("MAIN_SITE")) {
            $valid = Validator::make($request->all(), [
                'username' => 'required|string|min:6|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password',
                'condition' => 'required|accepted',
            ]);
            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {
                $user = new User();
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = 'admin';
                $user->domain = env('MAIN_SITE');
                $user->timezone = 'Asia/Ho_Chi_Minh';
                $user->language = 'vi';
                $user->status = 'active';
                $user->currency = 'USD';
                $user->referral_code = $this->generateReferralCode();
                $user->ref_by_code = null;
                $user->api_key = $this->generateApiKey();
                $user->save();

                $config = Config::where('domain', request()->getHost())->first();
                if (!$config) {
                    $config = new Config();
                    $config->domain_main = env('MAIN_SITE');
                    $config->domain = request()->getHost();
                    $config->timezone = 'Asia/Ho_Chi_Minh';
                    $config->currency = 'VND';
                    $config->status = 'active';
                    $config->save();
                }

                $currencyVN = Currency::where('code', 'VND')->first();
                $currencyUSD = Currency::where('code', 'USD')->first();
                if (!$currencyUSD) {
                    $exchange_rate = 1;
                    $exchange_rate = round($exchange_rate, 2);
                    $currencyUSD = new Currency();
                    $currencyUSD->code = 'USD';
                    $currencyUSD->name = 'Đô la Mỹ';
                    $currencyUSD->symbol = '$';
                    $currencyUSD->exchange_rate = $exchange_rate;
                    $currencyUSD->thousand_separator = 'dot';
                    $currencyUSD->decimal_separator = 'comma';
                    $currencyUSD->decimal_places = 2;
                    $currencyUSD->position = 'left';
                    $currencyUSD->status = 'active';
                    $currencyUSD->save();
                }
                if (!$currencyVN) {
                    // $exchange_rate = CurrencyCurrency::convert()->from('USD')->to("VND")->amount(1)->get();
                    $exchange_rate = get_currency('VND');
                    $exchange_rate = round($exchange_rate, 2);
                    $currencyVN = new Currency();
                    $currencyVN->code = 'VND';
                    $currencyVN->name = 'Việt Nam Đồng';
                    $currencyVN->symbol = '₫';
                    $currencyVN->exchange_rate = $exchange_rate;
                    $currencyVN->thousand_separator = 'dot';
                    $currencyVN->decimal_separator = 'comma';
                    $currencyVN->decimal_places = 0;
                    $currencyVN->position = 'right';
                    $currencyVN->status = 'active';
                    $currencyVN->save();
                }

                $config->user_id = $user->id;
                $config->title = "Chưa cấu hình";
                $config->description = "Chưa cấu hình";
                $config->status = "active";
                $config->save();

                return redirect()->route('auth.login')->with('success', 'Cài đặt thành công');
            }
        } else {
            $valid = Validator::make($request->all(), [
                'api_key' => 'required|string',
                'username' => 'required|string|min:6|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password',
                'condition' => 'required|accepted',
            ]);
            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {

                $childpanel = ChildPanel::where('api_key', $request->api_key)->where('name', request()->getHost())->first();
                if (!$childpanel) {
                    return redirect()->back()->with('error', 'API Key không đúng')->withInput();
                }
                if ($childpanel->status == 'active') {
                    $user = new User();
                    $user->username = $request->username;
                    $user->email = $request->email;
                    $user->password = Hash::make($request->password);
                    $user->role = 'admin';
                    $user->domain = request()->getHost();
                    $user->timezone = 'Asia/Ho_Chi_Minh';
                    $user->language = 'vi';
                    $user->status = 'active';
                    $user->currency = 'USD';
                    $user->referral_code = $this->generateReferralCode();
                    $user->ref_by_code = null;
                    $user->api_key = $this->generateApiKey();
                    $user->save();

                    $config = Config::where('domain', request()->getHost())->first();
                    if (!$config) {
                        $config = new Config();
                        $config->user_id = $user->id;
                        $config->domain_main = $childpanel->domain;
                        $config->domain = request()->getHost();
                        $config->timezone = 'Asia/Ho_Chi_Minh';
                        $config->currency = 'VND';
                        $config->status = 'active';
                        $config->last_payment_time = now();
                        $config->save();
                    }

                    $config->user_id = $user->id;
                    $config->title = "Đây Là Hệ Thống Smm Pane Của Bùi Văn Quyết";
                    $config->description = "Đây Là Hệ Thống Smm Pane Của Bùi Văn Quyết";
                    $config->status = "active";
                    $config->save();

                    $childpanel->status = 'active';
                    $childpanel->save();

                    return redirect()->route('auth.login')->with('success', 'Cài đặt thành công');
                } else {
                    return redirect()->back()->with('error', 'Tên miền đã được cài đặt')->withInput();
                }
            }
        }
    }

 


    public function ref(Request $request, $username)
    {
        $user = User::where('username', $username)->where('domain', $request->getHost())->first();
        if ($user) {

            // lưu vào phiên bản cookie
            $cookie = cookie('ref_by_code', $user->referral_code, 60 * 24 * 30); // 30 days
            $request->session()->put('ref_by_code', $user->referral_code);
            $request->session()->save();
            return redirect()->route('auth.register')->withCookie($cookie);
        } else {
            return redirect()->route('auth.register');
        }
    }

    private function generateReferralCode()
    {
        return strtoupper(Str::random(8));
    }

    private function generateApiKey()
    {
        return bin2hex(random_bytes(16));
    }

    public function install()
    {
        // Security Fix: Prevent re-installation if already configured
        if (Config::where('domain', request()->getHost())->exists()) {
            return redirect()->route('auth.login')->with('error', 'Hệ thống đã được cài đặt.');
        }

        return view('auth.install');
    }
}
