<?php

namespace App\Http\Controllers\Admin\Website;

use AmrShawky\Currency\Currency as CurrencyCurrency;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index()
    {

        $currencies = Currency::orderBy('id', 'desc')->get();
        $data = [
            'currencies' => $currencies,
        ];
        return view('admin.website.currencies.index', $data);
    }

    public function create(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string',
            'symbol' => 'required|string',
            'position' => 'required|in:left,right',
            'thousand_separator' => 'required|in:dot,comma,space',
            'decimal_separator' => 'required|in:dot,comma',
            'decimal_places' => 'required|integer',
            'exchange_rate' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        } else {

            $currency = Currency::where('code', $request->code)->first();
            if ($currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã tiền tệ đã tồn tại',
                ]);
            } else {
                try {
                    $currency = new Currency();
                    $currency->name = $request->name;
                    $currency->code = $request->code;
                    $currency->symbol = $request->symbol;
                    $currency->position = $request->position;
                    $currency->thousand_separator = $request->thousand_separator;
                    $currency->decimal_separator = $request->decimal_separator;
                    $currency->decimal_places = $request->decimal_places;
                    $currency->exchange_rate = $request->exchange_rate;
                    if ($currency->save()) {
                        // Clear all caches to ensure fresh currency data on client pages
                        \Illuminate\Support\Facades\Cache::flush();
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Thêm tiền tệ thành công',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Thêm tiền tệ thất bại',
                        ]);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function edit($id){
        $currency = Currency::find($id);
        if ($currency) {
            return view('admin.website.currencies.edit', [
                'currency' => $currency,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tiền tệ',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string',
            'symbol' => 'required|string',
            'position' => 'required|in:left,right',
            'thousand_separator' => 'required|in:dot,comma,space',
            'decimal_separator' => 'required|in:dot,comma',
            'decimal_places' => 'required|integer',
            'exchange_rate' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        } else {
            try {
                $currency = Currency::find($id);
                if ($currency) {
                    $currency->name = $request->name;
                    $currency->code = $request->code;
                    $currency->symbol = $request->symbol;
                    $currency->position = $request->position;
                    $currency->thousand_separator = $request->thousand_separator;
                    $currency->decimal_separator = $request->decimal_separator;
                    $currency->decimal_places = $request->decimal_places;
                    $currency->exchange_rate = $request->exchange_rate;
                    if ($currency->save()) {
                        // Clear all caches to ensure fresh currency data on client pages
                        \Illuminate\Support\Facades\Cache::flush();
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Cập nhật tiền tệ thành công',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cập nhật tiền tệ thất bại',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy tiền tệ',
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
    public function delete($id)
    {
        try {
            $currency = Currency::find($id);
            if ($currency) {
                if ($currency->delete()) {
                    return redirect()->back()->with('success', 'Xóa tiền tệ thành công');
                } else {
                    return redirect()->back()->with('error', 'Xóa tiền tệ thất bại');
                }
            } else {
                return redirect()->back()->with('error', 'Không tìm thấy tiền tệ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
