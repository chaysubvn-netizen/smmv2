<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IpBlockController extends Controller
{
    public function index()
    {
        $ips = BlockedIp::where(function($query) {
            $query->where('domain', request()->getHost())->orWhereNull('domain');
        })->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $ips
        ]);
    }

    public function store(Request $request)
    {
        if (status_demo()) {
            return response()->json(['success' => false, 'message' => 'Demo mode enabled.']);
        }

        $valid = Validator::make($request->all(), [
            'ip' => 'required|ip',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        BlockedIp::create([
            'ip' => $request->ip,
            'reason' => $request->reason,
            'domain' => $request->getHost(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'IP đã được thêm vào danh sách chặn.'
        ]);
    }

    public function destroy($id)
    {
        if (status_demo()) {
            return response()->json(['success' => false, 'message' => 'Demo mode enabled.']);
        }

        $ip = BlockedIp::where('id', $id)->where('domain', request()->getHost())->first();
        if ($ip) {
            $ip->delete();
            return response()->json(['success' => true, 'message' => 'Đã bỏ chặn IP.']);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy IP.']);
    }
}
