<?php

namespace App\Http\Middleware;

use App\Models\Config;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        try{
            $isInstallRequest = $request->is('auth/install', 'api/install', 'api/install/status');
            $host = $request->getHost();
            $site = Config::where('domain', $host)->first();

            // If not found, try stripping 'api.' prefix for site lookup
            if (!$site && strpos($host, 'api.') === 0) {
                $baseDomain = substr($host, 4);
                $site = Config::where('domain', $baseDomain)->first();
            }

            if($site){
                if($site->status == 'active'){
                    // nếu trang web đang hoạt động mà đang truy cập vào auth/install thì trả về 404
                    if($request->path() == 'auth/install'){
                        return abort(404);
                    }
                }else{
                    Auth::logout();
                    if($site->status == 'delete'){
                        return abort(404);
                    }

                    if($site->status == 'inactive'){
                        return abort(403, 'Unauthorized action.');
                    }

                    if($site->status == 'expired'){
                        return abort(403, "Site Has Expired");
                    }

                    // Kiểm tra hết hạn từ bảng child_panels
                    $childPanel = \App\Models\ChildPanel::where('name', $request->getHost())->first();
                    if ($childPanel) {
                        if ($childPanel->status == 'scam') {
                            return response()->view('errors.scam', [
                                'domain' => $childPanel->name,
                                'parent_domain' => $childPanel->domain
                            ], 403);
                        }

                        if ($childPanel->expired_at && $childPanel->expired_at->isPast()) {
                            return abort(403, "Site Has Expired. Please renew your subscription.");
                        }
                    }

                    if($site->status == 'scam'){
                        return response()->view('errors.scam', [
                            'domain' => $site->domain,
                            'parent_domain' => $site->domain_main
                        ], 403);
                    }

                    if($site->status == 'pending'){
                        // nếu trang web đang trong quá trình cài đặt
                        // nếu đường dẫn không phải là auth/install thì chuyển hướng về auth/install
                        if(!$isInstallRequest){
                            return redirect()->route('auth.install');
                        }
                    } else {
                        return abort(403, 'Unauthorized action.');
                    }
                }
            }else{
                if(env("MAIN_SITE") == $request->getHost()){
                    Auth::logout();
                    if(!$isInstallRequest){
                        return redirect()->route('auth.install');
                    }
                } else {
                    return abort(404);
                }
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $th) {
            \Log::error('AuthSite Error: ' . $th->getMessage());
            return abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
