<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Discount::query();

            // Search
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where('code', 'like', "%{$search}%");
            }

            $totalRecords = Discount::count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            if ($length < 0) {
                $length = 1000;
            }
            
            // Order (Simple default latest)
            $query->latest();

            $data = $query->skip($start)->take($length)->get();

            $mappedData = $data->map(function ($row) {
                $actionBtn = '<button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editModel" data-id="'.$row->id.'" data-code="'.$row->code.'" data-type="'.$row->type.'" data-amount="'.$row->amount.'" data-min_order="'.$row->min_order_amount.'" data-max_discount="'.$row->max_discount_amount.'" data-limit="'.$row->usage_limit.'" data-expired="'.$row->expired_at.'" data-status="'.$row->status.'"><i class="bx bx-edit"></i></button>';
                $actionBtn .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteDiscount('.$row->id.')"><i class="bx bx-trash"></i></button>';

                return [
                    'id' => $row->id,
                    'code' => $row->code,
                    'type' => $row->type == 'percent' 
                        ? '<span class="badge bg-info">Phần trăm (%)</span>' 
                        : '<span class="badge bg-warning">Số tiền (VNĐ)</span>',
                    'amount' => $row->type == 'percent' 
                        ? $row->amount . '%' 
                        : number_format($row->amount),
                    'min_order_amount' => number_format($row->min_order_amount),
                    'max_discount_amount' => number_format($row->max_discount_amount),
                    'used_count' => $row->used_count . '/' . ($row->usage_limit > 0 ? $row->usage_limit : '∞'),
                    'expired_at' => $row->expired_at ? date('H:i d/m/Y', strtotime($row->expired_at)) : 'Vĩnh viễn',
                    'status' => $row->status == 'active' 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>',
                    'action' => $actionBtn
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $mappedData
            ]);
        }
        return view('admin.discounts.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code',
            'type' => 'required|in:percent,fixed',
            'amount' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->except('_token');
        
        // Fix datetime format if present
        if (!empty($data['expired_at'])) {
            $data['expired_at'] = str_replace('T', ' ', $data['expired_at']);
        } else {
            $data['expired_at'] = null;
        }

        Discount::create($data);

        return response()->json(['status' => 'success', 'message' => 'Thêm mã giảm giá thành công!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code,'.$id,
            'type' => 'required|in:percent,fixed',
            'amount' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy mã giảm giá!']);
        }

        $data = $request->except('_token', '_method');

        // Fix datetime format if present
        if (!empty($data['expired_at'])) {
             $data['expired_at'] = str_replace('T', ' ', $data['expired_at']);
        } else {
             $data['expired_at'] = null;
        }

        $discount->update($data);

        return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công!']);
    }

    public function destroy($id)
    {
        Discount::find($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Xóa thành công!']);
    }
}
