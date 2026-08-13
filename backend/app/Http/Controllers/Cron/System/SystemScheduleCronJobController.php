<?php

namespace App\Http\Controllers\Cron\System;

use App\Http\Controllers\Controller;
use App\Models\ChildPanel;
use App\Models\Config;
use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemScheduleCronJobController extends Controller
{
    public function index(Request $request)
    {
        

        // tự động xoá người dùng không hoạt động
        $this->removeUsers();

        // tự động lên cấp cho tài khoản
        $this->updateLevelUsers();

        // thanh toán Hạn mức trả thưởng hoa hồng cho người dùng
        $this->payCommissionLimit();

        // tự động thanh toán childpanel hàng tháng
        $this->scheduleChildPanel();

        // Tự động xoá cache hệ thống định kỳ
        // RunAll keeps its lock, interval timestamps and report in cache.
        // Clearing the entire cache here would break overlap protection.
    }

    public function scheduleChildPanel()
    {
        $childPanels = ChildPanel::where('status', 'active')->get();
        if ($childPanels->count() > 0) {
            foreach ($childPanels as $childPanel) {
                $config = Config::where('domain', $childPanel->domain)->first();
                $configChild = Config::where('domain', $childPanel->name)->first();
                if ($config && $configChild) {
                    $last_payment_time = Carbon::parse($childPanel->last_payment_time);
                    $current_time = Carbon::now();
                    // kiểm tra xem đã đến thời gian thanh toán chưa là 1 tháng
                    if ($last_payment_time->diffInDays($current_time) >= 30) {
                        // tính toán số tiền thanh toán
                        $fee_childpanel = $config->fee_childpanel;

                        $user = $childPanel->user;
                        if ($user) {
                            // trừ tiền trong tài khoản của người dùng
                            $balancePayment = $user->balance - $fee_childpanel;
                            if ($balancePayment < $fee_childpanel) {
                                // nếu số dư không đủ thì chuyển sang trạng thái hết hạn
                                $childPanel->status = 'expired';
                                $childPanel->save();
                                $configChild->status = 'expired';
                                $configChild->save();
                            } else {
                                // cập nhật lại thời gian thanh toán
                                $childPanel->last_payment_time = Carbon::now();
                                $childPanel->save();
                                // trừ tiền trong tài khoản của người dùng
                                $user->balance = $balancePayment;
                                $user->save();
                                // transaction
                                $user->transactions()->create([
                                    'transaction_code' => 'payment_childpanel',
                                    'type' => 'sub',
                                    'balance_before' => $user->balance + $fee_childpanel,
                                    'balance_after' => $user->balance,
                                    'amount' => $fee_childpanel,
                                    'description' => 'Thanh toán gia hạn cho Website' . $childPanel->name,
                                    'status' => 'success',
                                    'domain' => $user->domain
                                ]);
                            }
                        } else {
                            $childPanel->status = 'expired';
                            $childPanel->save();
                            $configChild->status = 'expired';
                            $configChild->save();
                        }
                    }
                }
            }
        }
    }

    public function payCommissionLimit()
    {
        $configs = Config::where('status', 'active')->get();
        foreach ($configs as $config) {
            $users = User::where('commission', '>=', $config->affiliate_min)
                ->where('domain', $config->domain)
                ->get();
            if ($users) {
                $users->each(function ($user) use ($config) {
                    $commission = $user->commission;
                    $user->update(['commission' => 0]);
                    $user->update(['total_commission' => $user->total_commission + $commission]);
                });
            }
        }
    }

   

    public function removeUsers()
    {
        if (env("USER_INACTIVE")) {
            $inactiveUsers = User::where('balance', '==', 0)
                ->where('total_deposit', '==', 0)
                ->where('last_login', '<', now()->subDays(env("USER_INACTIVE")))->get();
            foreach ($inactiveUsers as $user) {
                $user->delete();
            }
        }
    }

    public function updateLevelUsers()
    {
        $configs = Config::where('status', 'active')->get();
        foreach ($configs as $config) {
            $user = User::where('domain', $config->domain)->get();
            if ($user) {
                // tự động lên mốc
                $silver = $config->up_silver;
                $gold = $config->up_gold;
                $platinum = $config->up_platinum;
                $diamond = $config->up_diamond;

                $user->each(function ($user) use ($silver, $gold, $platinum, $diamond) {
                    if ($user->total_deposit >= $silver && $user->total_deposit < $gold) {
                        $user->update(['level' => 'silver']);
                    } elseif ($user->total_deposit >= $gold && $user->total_deposit < $platinum) {
                        $user->update(['level' => 'gold']);
                    } elseif ($user->total_deposit >= $platinum && $user->total_deposit < $diamond) {
                        $user->update(['level' => 'platinum']);
                    } elseif ($user->total_deposit >= $diamond) {
                        $user->update(['level' => 'diamond']);
                    }
                });
            }
        }
    }

    public function autoUpdate()
    {
        \Illuminate\Support\Facades\Artisan::call('system:auto-update');
        return response()->json([
            'status' => 'success',
            'message' => 'Auto update check completed',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    }
}
