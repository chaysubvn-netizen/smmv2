<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToolsAdminController extends Controller
{
    private function checkAuth(Request $request) {
        $key = $request->input('api_key');
        if (!$key) {
            return false;
        }
        $user = User::where('api_key', $key)->where('role', 'admin')->first();
        if ($user) {
            return $user;
        }
        return false;
    }

    public function listManual(Request $request)
    {
       
        
        $services = Service::where('domain', $request->getHost())
            ->where('mode', 'manual')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $services->count(),
            'data' => $services
        ]);
    }

    public function updateService(Request $request)
    {
        // Auth check removed as requested

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:services,id',
            'name' => 'nullable|string',
            'rate' => 'nullable|numeric|min:0',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $service = Service::where('domain', $request->getHost())
            ->where('id', $request->id)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found or does not belong to this domain'
            ], 404);
        }

        // Update fields if they are present
        if ($request->has('name')) $service->name = $request->name;
        if ($request->has('rate')) $service->rate = $request->rate;
        if ($request->has('min')) $service->min = $request->min;
        if ($request->has('max')) $service->max = $request->max;
        if ($request->has('status')) $service->status = $request->status;
        if ($request->has('note')) $service->note = $request->note;
        
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }
    public function listOrders(Request $request)
    {
        // Auth check removed as requested
        
        $status = $request->input('status'); // Optional filter by status

        // Query orders for Manual services
        $query = \App\Models\Order::where('domain', $request->getHost())
            ->whereHas('service', function($q) {
                $q->where('mode', 'manual');
            })
            ->with(['service:id,name']);

        if ($status) {
            $query->where('status', $status);
        } else {
            // Default to Pending/Processing/In Progress if not specified
            $query->whereIn('status', ['Pending', 'Processing', 'In Progress']);
        }

        $orders = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'count' => $orders->count(),
            'data' => $orders
        ]);
    }

    public function updateOrder(Request $request)
    {
        // Auth check removed as requested

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:orders,id',
            'status' => 'nullable|in:Pending,Processing,In Progress,Completed,Partial,Canceled',
            'start_count' => 'nullable|integer',
            'remains' => 'nullable|integer',
            'note' => 'nullable|string' 
        ]);

        if ($validator->fails()) {
             return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $order = \App\Models\Order::where('domain', $request->getHost())
            ->where('id', $request->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($request->has('status')) $order->status = $request->status;
        if ($request->has('start_count')) $order->start_count = $request->start_count;
        if ($request->has('remains')) $order->remains = $request->remains;
        if ($request->has('note')) $order->note = $request->note;

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order
        ]);
    }
}
