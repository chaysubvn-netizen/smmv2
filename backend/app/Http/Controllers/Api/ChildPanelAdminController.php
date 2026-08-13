<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\CloudflareCustom;
use App\Library\CpanelCustom;
use App\Models\ChildPanel;
use App\Models\Recharge;
use App\Models\Order;
use App\Models\Config;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChildPanelAdminController extends Controller
{
    private function siteHost(Request $request): string
    {
        $host = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $host = preg_replace('/:\d+$/', '', $host) ?: $request->getHost();

        return str_starts_with($host, 'api.') ? substr($host, 4) : $host;
    }

    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status', 'all');
        $perPage  = $request->input('per_page', 20);
        $level    = $request->input('level', 'all');

        $site = Config::where('domain', $this->siteHost($request))->first();
        if (!$site) {
            return response()->json(['status' => 'error', 'message' => 'Site config not found'], 404);
        }

        $viewingSiteId = $site->domain;
        $isMainSite = true;

        $query = ChildPanel::with(['user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('child_panels.domain', 'like', "%{$search}%")
                    ->orWhere('child_panels.data->admin_email', 'like', "%{$search}%")
                    ->orWhere('child_panels.data->admin_username', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('username', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== 'all') {
            $query->where('child_panels.status', $status);
        }

        $panels = $query->orderBy('child_panels.created_at', 'desc')->paginate($perPage);

        foreach ($panels as $panel) {
            if ($panel->name) {
                $panel->total_orders   = Order::withoutGlobalScope('site')->where('domain', $panel->name)->count();
                $panel->total_spent    = Order::withoutGlobalScope('site')->where('domain', $panel->name)->sum('total');
                $panel->total_deposits = Recharge::withoutGlobalScope('site')->where('domain', $panel->name)->where('status', 'success')->sum('amount');
                $panel->total_users    = User::withoutGlobalScope('site')->where('domain', $panel->name)->count();
            } else {
                $panel->total_orders = $panel->total_spent = $panel->total_deposits = $panel->total_users = 0;
            }
            
            // Append virtual properties
            $panel->hierarchy_label = $panel->hierarchy_label;
            $panel->hierarchy_color = $panel->hierarchy_color;
            $panel->hierarchy_icon = $panel->hierarchy_icon;
            $panel->hierarchy_level = $panel->hierarchy_level;
            $panel->status_display_name = $panel->status_display_name;
            $panel->status_badge_color = $panel->status_badge_color;
            $panel->days_remaining = $panel->days_remaining;
            $panel->formatted_expires_at = $panel->expires_at ? $panel->expires_at->format('d/m/Y') : null;
        }

        $statsBase = fn() => ChildPanel::where('main_domain', $viewingSiteId);
        $stats = [
            'total'         => $statsBase()->count(),
            'active'        => $statsBase()->where('status', ChildPanel::STATUS_ACTIVE)->count(),
            'pending'       => $statsBase()->where('status', ChildPanel::STATUS_PENDING)->count(),
            'expired'       => $statsBase()->where('status', ChildPanel::STATUS_EXPIRED)->count(),
            'total_revenue' => $statsBase()->where('main_domain', $viewingSiteId)->get()->sum('monthly_price'),
            'direct_child'  => $statsBase()->where('main_domain', $viewingSiteId)->count(),
            'grandchild'    => 0,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'panels' => $panels,
                'stats' => $stats,
                'site_settings' => [
                    'child_panel_enabled' => (bool) $site->child_panel_enabled,
                    'child_panel_monthly_price' => $site->child_panel_monthly_price,
                    'cloudflare_email' => $site->cloudflare_email,
                    'cloudflare_global_key' => $site->cloudflare_global_key,
                    'cloudflare_account_id' => $site->cloudflare_account_id,
                    'cloudflare_token' => $site->cloudflare_token,
                    'cloudflare_ip_host' => $site->cloudflare_ip_host,
                    'hosting_panel_type' => $site->hosting_panel_type ?? 'cpanel',
                    'cpanel_host' => $site->cpanel_host,
                    'cpanel_user' => $site->cpanel_username,
                    'cpanel_pass' => $site->cpanel_password,
                    'cpanel_username' => $site->cpanel_username,
                    'cpanel_password' => $site->cpanel_password,
                ],
                'isMainSite' => true
            ]
        ]);
    }

    public function updateSettings(Request $request)
    {
        $site = Config::where('domain', $this->siteHost($request))->first();
        if (!$site) {
            return response()->json(['status' => 'error', 'message' => 'Website config not found.'], 404);
        }

        $rules = [
            'child_panel_enabled' => 'nullable',
            'child_panel_monthly_price' => 'nullable|numeric|min:0',
            'cloudflare_email' => 'nullable|string|max:255',
            'cloudflare_global_key' => 'nullable|string|max:255',
            'cloudflare_account_id' => 'nullable|string|max:255',
            'cloudflare_token' => 'nullable|string|max:255',
            'cloudflare_ip_host' => 'nullable|string|max:255',
            'hosting_panel_type' => 'nullable|string|in:cpanel,aapanel',
            'cpanel_host' => 'nullable|string|max:255',
            'cpanel_user' => 'nullable|string|max:255',
            'cpanel_pass' => 'nullable|string|max:255',
            'cpanel_username' => 'nullable|string|max:255',
            'cpanel_password' => 'nullable|string|max:255',
        ];

        $request->validate($rules);

        $site->child_panel_enabled = $request->input('child_panel_enabled') ? 1 : 0;
        if ($request->has('child_panel_monthly_price')) {
            $site->child_panel_monthly_price = $request->input('child_panel_monthly_price');
        }
        $site->cloudflare_email = $request->input('cloudflare_email');
        $site->cloudflare_global_key = $request->input('cloudflare_global_key');
        $site->cloudflare_account_id = $request->input('cloudflare_account_id');
        $site->cloudflare_token = $request->input('cloudflare_token');
        $site->cloudflare_ip_host = $request->input('cloudflare_ip_host');
        
        $site->hosting_panel_type = $request->input('hosting_panel_type', 'cpanel');
        $site->cpanel_host = $request->input('cpanel_host');
        $site->cpanel_username = $request->input('cpanel_username', $request->input('cpanel_user'));
        if ($request->filled('cpanel_password') || $request->filled('cpanel_pass')) {
            $site->cpanel_password = $request->input('cpanel_password', $request->input('cpanel_pass'));
        }

        $site->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật cấu hình thành công',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $panel = ChildPanel::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,active,expired,cancelled']);
        
        $panel->status = $request->status;

        if ($request->status === ChildPanel::STATUS_ACTIVE) {
            if (!$panel->expires_at) {
                $panel->expires_at = Carbon::now()->addMonth();
            }
            if ($panel->name) {
                $site = Config::where('domain', $panel->name)->first();
                if ($site) {
                    $site->status = 'active';
                    $site->save();
                }
            }
        } elseif (in_array($request->status, [ChildPanel::STATUS_EXPIRED, ChildPanel::STATUS_CANCELLED])) {
            if ($panel->name) {
                $site = Config::where('domain', $panel->name)->first();
                if ($site) {
                    $site->status = 'inactive';
                    $site->save();
                }
            }
        }
        $panel->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái thành công',
        ]);
    }

    public function destroy($id)
    {
        $panel = ChildPanel::findOrFail($id);
        $errors = [];

        try {
            $cloudflare = new CloudflareCustom();
            $cfInfo = $cloudflare->findDomain($panel->domain);
            if (!empty($cfInfo) && isset($cfInfo['zone_id'])) {
                $cfResult = $cloudflare->deleteDomain($cfInfo['zone_id']);
                if (($cfResult['status'] ?? '') !== 'success') {
                    $errors[] = 'Cloudflare: ' . ($cfResult['message'] ?? 'Lỗi không xác định');
                }
            }
        } catch (\Exception $e) {
            $errors[] = 'Cloudflare: ' . $e->getMessage();
        }

        try {
            $cpanel = new CpanelCustom();
            $cpResult = $cpanel->deleteDomain($panel->domain);
            $cpData = $cpResult['cpanelresult'] ?? $cpResult;
            $cpSuccess = false;
            if (isset($cpData['data'][0]['result'])) {
                $cpSuccess = (int)$cpData['data'][0]['result'] === 1;
            } elseif (isset($cpData['result']['status'])) {
                $cpSuccess = (int)$cpData['result']['status'] === 1;
            }
            if (!$cpSuccess) {
                $errors[] = 'cPanel: ' . ($cpData['data'][0]['reason'] ?? $cpData['result']['error'] ?? 'Lỗi không xác định');
            }
        } catch (\Exception $e) {
            $errors[] = 'cPanel: ' . $e->getMessage();
        }

        if ($panel->name) {
            User::withoutGlobalScope('site')->where('domain', $panel->name)->delete();
            $site = Config::where('domain', $panel->name)->first();
            if ($site) {
                $site->delete();
            }
        }

        $panel->delete();

        $message = 'Child Panel đã được xóa hoàn toàn.';
        if (!empty($errors)) {
            $message .= ' (Cảnh báo: ' . implode('; ', $errors) . ')';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function statistics($id)
    {
        $panel = ChildPanel::with(['user'])->findOrFail($id);

        $stats = [
            'domain' => $panel->domain,
            'status' => $panel->status_display_name,
            'status_color' => $panel->status_badge_color,
            'owner' => $panel->user ? $panel->user->username : 'N/A',
            'admin_username' => $panel->admin_username,
            'admin_email' => $panel->admin_email,
            'created_at' => $panel->created_at->format('d/m/Y H:i'),
            'expires_at' => $panel->expires_at ? $panel->expires_at->format('d/m/Y H:i') : 'N/A',
            'days_remaining' => $panel->days_remaining ?? 0,
            'monthly_price' => $panel->monthly_price ?? 0,
            'cloudflare_status' => $panel->cloudflare_status ?? 'unknown',
            'nameserver1' => $panel->nameserver1,
            'nameserver2' => $panel->nameserver2,
        ];

        if ($panel->name) {
            $siteId = $panel->name;

            $totalOrders = Order::withoutGlobalScope('site')->where('domain', $siteId)->count();
            $completedOrders = Order::withoutGlobalScope('site')->where('domain', $siteId)->where('status', 'Completed')->count();
            $pendingOrders = Order::withoutGlobalScope('site')->where('domain', $siteId)->whereIn('status', ['Pending', 'Processing', 'In progress'])->count();
            $cancelledOrders = Order::withoutGlobalScope('site')->where('domain', $siteId)->whereIn('status', ['Canceled', 'Partial'])->count();

            $totalSpent = Order::withoutGlobalScope('site')->where('domain', $siteId)->sum('total');
            $totalDeposits = Recharge::withoutGlobalScope('site')->where('domain', $siteId)->where('status', 'success')->sum('amount');
            $pendingDeposits = Recharge::withoutGlobalScope('site')->where('domain', $siteId)->where('status', 'pending')->sum('amount');

            $totalUsers = User::withoutGlobalScope('site')->where('domain', $siteId)->count();
            $adminUsers = User::withoutGlobalScope('site')->where('domain', $siteId)->where('role', 'admin')->count();
            $clientUsers = User::withoutGlobalScope('site')->where('domain', $siteId)->where('role', '!=', 'admin')->count();
            $totalBalance = User::withoutGlobalScope('site')->where('domain', $siteId)->sum('balance');

            $recentOrders = Order::withoutGlobalScope('site')->where('domain', $siteId)->where('created_at', '>=', now()->subDays(7))->count();
            $recentRevenue = Order::withoutGlobalScope('site')->where('domain', $siteId)->where('created_at', '>=', now()->subDays(7))->sum('total');

            $stats['orders'] = [
                'total' => $totalOrders,
                'completed' => $completedOrders,
                'pending' => $pendingOrders,
                'cancelled' => $cancelledOrders,
                'recent_7d' => $recentOrders,
            ];
            $stats['finance'] = [
                'total_spent' => $totalSpent,
                'total_deposits' => $totalDeposits,
                'pending_deposits' => $pendingDeposits,
                'total_balance' => $totalBalance,
                'recent_revenue_7d' => $recentRevenue,
                'profit_estimate' => $totalDeposits - $totalSpent,
            ];
            $stats['users'] = [
                'total' => $totalUsers,
                'admins' => $adminUsers,
                'clients' => $clientUsers,
            ];
        } else {
            $stats['orders'] = ['total' => 0, 'completed' => 0, 'pending' => 0, 'cancelled' => 0, 'recent_7d' => 0];
            $stats['finance'] = ['total_spent' => 0, 'total_deposits' => 0, 'pending_deposits' => 0, 'total_balance' => 0, 'recent_revenue_7d' => 0, 'profit_estimate' => 0];
            $stats['users'] = ['total' => 0, 'admins' => 0, 'clients' => 0];
        }

        return response()->json(['status' => 'success', 'data' => $stats]);
    }

    public function loginAsAdmin($id)
    {
        $panel = ChildPanel::findOrFail($id);
        $site = Config::where('domain', $panel->name)->first();

        if (!$site) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy website'], 404);
        }

        $token = Str::random(64);
        $site->master_login_token = $token;
        $site->master_login_token_expires = now()->addMinutes(5);
        $site->save();

        $protocol = request()->secure() ? 'https://' : 'http://';
        $loginUrl = $protocol . $site->domain . '/auth/master-login?token=' . $token;

        return response()->json(['status' => 'success', 'url' => $loginUrl]);
    }
}
