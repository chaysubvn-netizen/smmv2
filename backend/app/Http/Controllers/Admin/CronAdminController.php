<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CronServer;
use App\Models\UserCron;

class CronAdminController extends Controller
{
    // === Server Management ===
    public function serverIndex()
    {
        $servers = CronServer::latest()->get();
        return view('admin.cron.server', compact('servers'));
    }

    public function serverStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'region' => 'nullable',
        ]);

        CronServer::create($request->all());
        return redirect()->back()->with('success', 'Thêm server thành công');
    }

    public function serverUpdate(Request $request, $id)
    {
        $server = CronServer::findOrFail($id);
        $server->update($request->all());
        return redirect()->back()->with('success', 'Cập nhật thành công');
    }

    public function serverDelete($id)
    {
        CronServer::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Xóa thành công');
    }

    // === User Cron Management ===
    public function index()
    {
        $crons = UserCron::with(['user', 'server'])->latest()->paginate(20);
        return view('admin.cron.list', compact('crons'));
    }

    public function delete($id)
    {
        UserCron::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Xóa cron thành công');
    }
}
