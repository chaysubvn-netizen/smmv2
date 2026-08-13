<?php

namespace App\Http\Controllers\Admin\Licensing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;

class LicenseAdminController extends Controller
{
    // List all licenses
    public function index()
    {
        $licenses = License::with(['user', 'product'])->latest()->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Admin license list',
            'data' => $licenses
        ]);
        // Note: Ideally this would return a View, but sticking to API response 
        // style to match the request snippet "send_response".
        // In a real Admin panel, you'd likely use data tables.
    }

    // Update status (active, inactive, banned)
    public function updateStatus(Request $request, $id)
    {
        $license = License::find($id);
        if (!$license) {
            return response()->json(['status' => 'error', 'message' => 'License not found']);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,banned'
        ]);

        $license->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'License status updated'
        ]);
    }
}
