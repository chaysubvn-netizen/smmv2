<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCron;

class CronApiController extends Controller
{
    /**
     * Get list of active cron jobs to run.
     * Tools will call this to know what to execute.
     */
    public function getJobs(Request $request)
    {
        $this->authorizeWorker($request);

        $jobs = UserCron::whereIn('status', ['active', 'stopped'])
            // ->where('expired_at', '>', now()) // Allow expired to be sent for reporting
            ->with('server') 
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $jobs
        ]);
    }

    /**
     * Update cron job status or log result.
     */
    public function updateJob(Request $request, $id)
    {
        $this->authorizeWorker($request);
        $data = $request->validate([
            'status' => ['nullable', 'in:active,stopped'],
            'status_code' => ['nullable', 'integer', 'min:0', 'max:599'],
            'duration' => ['nullable', 'numeric', 'min:0', 'max:86400'],
            'response_body' => ['nullable', 'string', 'max:65535'],
        ]);
        $cron = UserCron::find($id);
        
        if (!$cron) {
            return response()->json(['status' => false, 'message' => 'Not found'], 404);
        }

        // Example: If tool reports failure or wants to disable it
        if (array_key_exists('status', $data)) {
            $cron->status = $data['status'];
        }

        // Update stats on UserCron
        if ($request->has('status_code')) {
            $cron->last_status_code = $request->status_code;
        }
        if ($request->has('duration')) {
            $cron->last_duration = $request->duration;
        }
        $cron->last_run_at = now();
        $cron->save();

        // Save detailed log
        \Illuminate\Support\Facades\DB::table('cron_logs')->insert([
            'user_cron_id' => $cron->id,
            'status_code' => $request->status_code,
            'duration' => $request->duration,
            'response_body' => $request->response_body ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Cleanup old logs (keep last 20)
        // This is a simple cleanup strategy. For high volume, use a separate cron cleanup task.
        $logCount = \Illuminate\Support\Facades\DB::table('cron_logs')->where('user_cron_id', $cron->id)->count();
        if ($logCount > 20) {
             \Illuminate\Support\Facades\DB::table('cron_logs')
                ->where('user_cron_id', $cron->id)
                ->orderBy('id', 'asc')
                ->limit($logCount - 20)
                ->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully'
        ]);
    }
    private function authorizeWorker(Request $request): void
    {
        $expected = (string) config('cron.secret', '');
        $provided = (string) ($request->bearerToken() ?: $request->header('X-Cron-Secret', ''));
        abort_unless($expected !== '' && hash_equals($expected, $provided), 403, 'Invalid cron worker credentials.');
    }
}
