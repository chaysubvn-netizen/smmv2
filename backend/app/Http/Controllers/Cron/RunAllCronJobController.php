<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Cron\Order\StatusOrderCronJobController;
use App\Http\Controllers\Cron\Recharge\BinanceCronJobController;
use App\Http\Controllers\Cron\Recharge\RechargeCronJobController;
use App\Http\Controllers\Cron\Recharge\RechargeUsdtCronJobController;
use App\Http\Controllers\Cron\Recharge\Trc20CronJobController;
use App\Http\Controllers\Cron\Service\ServiceCronJobController;
use App\Http\Controllers\Cron\System\FbTokenCronController;
use App\Http\Controllers\Cron\System\SystemScheduleCronJobController;
use App\Models\AccountBank;
use App\Models\ApiProvider;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

use function Illuminate\Support\defer;

class RunAllCronJobController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->authorizeRequest($request);

        if ($request->boolean('status')) {
            return response()->json(Cache::get('cron:run-all:status', [
                'status' => 'idle', 'message' => 'Cron chưa có lịch sử chạy.',
            ]));
        }

        $lock = Cache::lock('cron:run-all', max(60, (int) config('cron.lock_seconds', 1800)));
        if (! $lock->get()) {
            $current = Cache::get('cron:run-all:status', []);
            $startedAt = data_get($current, 'started_at');
            $isStale = $startedAt && Carbon::parse($startedAt)->lt(now()->subSeconds((int) config('cron.stale_seconds', 300)));
            if ($isStale) {
                $lock->forceRelease();
                $lock = Cache::lock('cron:run-all', max(60, (int) config('cron.lock_seconds', 1800)));
                if ($lock->get()) return $this->runJobs($lock, $request->boolean('force'));
            }
            return response()->json([
                'status' => 'running',
                'message' => 'Một lượt cron tổng đang chạy. Yêu cầu này đã được bỏ qua.',
                'current' => $current,
            ], 423);
        }

        if (! $request->boolean('async')) {
            return $this->runJobs($lock, $request->boolean('force'));
        }

        $startedAt = now();
        Cache::put('cron:run-all:status', ['status' => 'running', 'started_at' => $startedAt->toIso8601String()], now()->addDay());
        defer(fn () => $this->runJobs($lock, $request->boolean('force')), 'cron:run-all', true);

        return response()->json([
            'status' => 'accepted', 'message' => 'Cron đang được xử lý nền.',
            'started_at' => $startedAt->toIso8601String(),
        ], 202);
    }

    private function runJobs($lock, bool $force = false): JsonResponse
    {
        @set_time_limit(0);
        ignore_user_abort(true);
        $startedAt = now();
        $startedTimer = microtime(true);
        $steps = [];

        try {
            foreach ($this->jobs() as $name => $job) {
                $type = $job['type'] ?? $name;
                Cache::put('cron:run-all:status', [
                    'status' => 'running', 'started_at' => $startedAt->toIso8601String(),
                    'current_job' => $type, 'completed_jobs' => array_column($steps, 'type'),
                ], now()->addDay());

                $interval = $job['interval'];
                $lastRun = Cache::get('cron:job:last-run:'.$name);
                // Carbon returns a signed difference by default. The last run is
                // normally in the past, so use the absolute elapsed duration.
                $elapsed = $lastRun ? abs(now()->diffInSeconds($lastRun)) : null;
                if (! $force && $elapsed !== null && $elapsed < $interval) {
                    $steps[] = [
                        'type' => $type, 'interval_seconds' => $interval, 'ran' => false,
                        ...(isset($job['provider']) ? ['provider' => $job['provider']] : []),
                        'skipped' => true, 'duration_ms' => 0, 'next_run_in_seconds' => $interval - $elapsed,
                        'result' => ['status' => 'skipped', 'message' => 'Skipped by interval policy for DB/API optimization.'],
                    ];
                    continue;
                }

                $jobStarted = microtime(true);
                try {
                    $response = $job['callback']();
                    $result = is_object($response) && method_exists($response, 'getData')
                        ? $response->getData(true)
                        : ['status' => 'success', 'message' => 'Task completed.'];
                    $steps[] = [
                        'type' => $type, 'interval_seconds' => $interval, 'ran' => true,
                        ...(isset($job['provider']) ? ['provider' => $job['provider']] : []),
                        'skipped' => false, 'duration_ms' => (int) round((microtime(true) - $jobStarted) * 1000),
                        'result' => $result,
                    ];
                    Cache::put('cron:job:last-run:'.$name, now(), now()->addDays(7));
                } catch (Throwable $error) {
                    report($error);
                    $steps[] = [
                        'type' => $type, 'interval_seconds' => $interval, 'ran' => true,
                        ...(isset($job['provider']) ? ['provider' => $job['provider']] : []),
                        'skipped' => false, 'duration_ms' => (int) round((microtime(true) - $jobStarted) * 1000),
                        'result' => ['status' => 'error', 'message' => $error->getMessage()],
                    ];
                }
            }
        } finally {
            $lock->release();
        }

        $failed = collect($steps)->filter(fn ($step) => data_get($step, 'result.status') === 'error')->count();
        $payload = [
            'status' => $failed === 0 ? 'success' : 'error',
            'message' => $failed === 0 ? 'All cron tasks executed.' : "Cron completed with {$failed} failed task(s).",
            'force' => $force,
            'providers_total' => ApiProvider::count(),
            'banks_total' => AccountBank::count(),
            'duration_ms' => (int) round((microtime(true) - $startedTimer) * 1000),
            'steps' => $steps,
        ];
        Cache::put('cron:run-all:status', $payload, now()->addDay());

        return response()->json($payload);
    }

    private function jobs(): array
    {
        $request = request();
        $order = app(StatusOrderCronJobController::class);
        $service = app(ServiceCronJobController::class);
        $system = app(SystemScheduleCronJobController::class);

        $jobs = [];
        foreach (ApiProvider::where('status', 'active')->get() as $provider) {
            $providerRequest = clone $request;
            $providerRequest->merge(['provider_id' => $provider->id]);
            $meta = fn (string $type, int $interval, $callback) => ['type' => $type, 'interval' => $interval, 'provider' => $provider->name, 'callback' => $callback];
            $jobs["status.{$provider->id}"] = $meta('status', 60, fn () => $order->index($providerRequest));
            $jobs["scheduled.{$provider->id}"] = $meta('scheduled', 60, fn () => $order->schedule($providerRequest));
            $jobs["dripfeed.{$provider->id}"] = $meta('dripfeed', 60, fn () => $order->dripfeed($providerRequest));
            $jobs["refill-status.{$provider->id}"] = $meta('refill-status', 300, fn () => $order->refill($providerRequest));
            $jobs["service-update.{$provider->id}"] = $meta('service-update', 60, fn () => $service->autoAdd($providerRequest));
        }

        return $jobs + [
            'average-completion-time' => ['interval' => 3600, 'callback' => fn () => $service->updateAvgTime($request)],
            'sync-child-panel-services' => ['interval' => 600, 'callback' => fn () => $service->updateService($request)],
            'deposit-usdt' => ['interval' => 20, 'callback' => fn () => app(RechargeUsdtCronJobController::class)->index($request)],
            'deposit-ocb' => ['interval' => 20, 'callback' => fn () => app(RechargeCronJobController::class)->bank((clone $request)->merge(['bank' => 'ocb']))],
            'deposit-acb' => ['interval' => 20, 'callback' => fn () => app(RechargeCronJobController::class)->bank((clone $request)->merge(['bank' => 'acb']))],
            'deposit-mbbank' => ['interval' => 20, 'callback' => fn () => app(RechargeCronJobController::class)->bank((clone $request)->merge(['bank' => 'mbbank']))],
            'deposit-vcb' => ['interval' => 20, 'callback' => fn () => app(RechargeCronJobController::class)->bank((clone $request)->merge(['bank' => 'vcb']))],
            'deposit-viettinbank' => ['interval' => 20, 'callback' => fn () => app(RechargeCronJobController::class)->bank((clone $request)->merge(['bank' => 'viettinbank']))],
            'deposit-binance' => ['interval' => 20, 'callback' => fn () => app(BinanceCronJobController::class)->index($request)],
            'deposit-trc20' => ['interval' => 20, 'callback' => fn () => app(Trc20CronJobController::class)->index($request)],
            'login-notifications' => ['interval' => 15, 'callback' => fn () => app(FbTokenCronController::class)->index($request)],
            'system-auto-update' => ['interval' => 60, 'callback' => fn () => $system->autoUpdate()],
            'system-schedule' => ['interval' => 60, 'callback' => fn () => $system->index($request)],
        ];
    }

    private function authorizeRequest(Request $request): void
    {
        $expected = (string) config('cron.secret', '');
        $provided = (string) $request->query('secret', '');
        abort_unless($expected !== '' && hash_equals($expected, $provided), 403, 'Cron secret không hợp lệ.');
    }
}
