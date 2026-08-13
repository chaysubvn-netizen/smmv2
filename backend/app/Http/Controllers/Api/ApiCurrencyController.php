<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiCurrencyController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(request()->user()?->role === 'admin', 403, 'Bạn không có quyền quản trị.');
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $limit = $request->input('limit', 10);
        $search = $request->input('search', '');
        
        $query = Currency::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        
        $currencies = $query->orderBy('id', 'asc')->paginate($limit);
        
        return response()->json([
            'status' => true,
            'data' => $currencies
        ]);
    }
    
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies',
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric',
            'thousand_separator' => 'required|string|max:10',
            'decimal_separator' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:10',
            'position' => 'nullable|string|in:left,right',
            'status' => 'nullable|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        
        $currency = Currency::create($request->all());
        
        return response()->json([
            'status' => true,
            'message' => 'Thêm tiền tệ thành công',
            'data' => $currency
        ]);
    }
    
    public function show($id)
    {
        $this->authorizeAdmin();
        $currency = Currency::find($id);
        
        if (!$currency) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tiền tệ'
            ]);
        }
        
        return response()->json([
            'status' => true,
            'data' => $currency
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $currency = Currency::find($id);
        
        if (!$currency) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tiền tệ'
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code,' . $id,
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric',
            'thousand_separator' => 'required|string|max:10',
            'decimal_separator' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:10',
            'position' => 'nullable|string|in:left,right',
            'status' => 'nullable|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        
        $currency->update($request->all());
        
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật tiền tệ thành công',
            'data' => $currency
        ]);
    }
    
    public function destroy($id)
    {
        $this->authorizeAdmin();
        $currency = Currency::find($id);
        
        if (!$currency) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tiền tệ'
            ]);
        }
        
        $currency->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Xóa tiền tệ thành công'
        ]);
    }
}
