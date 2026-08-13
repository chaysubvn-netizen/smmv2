<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Http\Controllers\Controller;
use App\Models\AccountBank;
use App\Models\RechargeBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountBankController extends Controller
{
    // Hiển thị danh sách tài khoản ngân hàng
    public function index()
    {
        $accounts = AccountBank::where('domain', request()->getHost())->get();
        return view('admin.bank.account-bank.index', compact('accounts'));
    }

    // Tạo tài khoản ngân hàng mới
    public function create(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank_name' => 'required|string',
            'bank_code' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'branch' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '.' . $file->extension();
            $file->move(public_path('uploads'), $filename);
        }

        try {
            $accountBank = new AccountBank();
            $accountBank->icon = $filename ?? null;
            $accountBank->bank_name = $request->bank_name;
            $accountBank->bank_code = $request->bank_code;
            $accountBank->account_number = $request->account_number;
            $accountBank->account_name = $request->account_name;
            $accountBank->branch = $request->branch;
            $accountBank->status = $request->status;
            $accountBank->domain = request()->getHost();
            $accountBank->save();

            return response()->json([
                'success' => true,
                'message' => 'Tạo thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo thất bại: ' . $th->getMessage(),
            ]);
        }
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $accountBank = AccountBank::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$accountBank) {
            return redirect()->route('admin.banks.accounts.index')->with('error', 'Không tìm thấy tài khoản ngân hàng');
        }

        return view('admin.bank.account-bank.edit', compact('accountBank'));
    }

    // Cập nhật tài khoản ngân hàng
    public function update(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank_name' => 'required|string',
            'bank_code' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'branch' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $accountBank = AccountBank::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$accountBank) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản ngân hàng',
            ]);
        }

        if ($request->hasFile('icon')) {
            if ($accountBank->icon && file_exists(public_path($accountBank->icon))) {
                unlink(public_path($accountBank->icon));
            }
            $file = $request->file('icon');
            $filename = time() . '.' . $file->extension();
            $file->move(public_path('uploads'), $filename);
            $accountBank->icon = $filename;
        }

        $accountBank->bank_name = $request->bank_name;
        $accountBank->bank_code = $request->bank_code;
        $accountBank->account_number = $request->account_number;
        $accountBank->account_name = $request->account_name;
        $accountBank->branch = $request->branch;
        $accountBank->status = $request->status;
        $accountBank->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
        ]);
    }

    // Xóa tài khoản ngân hàng
    public function delete($id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $accountBank = AccountBank::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$accountBank) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản ngân hàng',
            ]);
        }

        try {
            if ($accountBank->icon && file_exists(public_path($accountBank->icon))) {
                unlink(public_path($accountBank->icon));
            }
            $accountBank->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa thất bại: ' . $th->getMessage(),
            ]);
        }
    }

    // Quản lý bonus nạp tiền
    public function bonus()
    {
        $bonuses = RechargeBonus::where('domain', request()->getHost())->get();
        return view('admin.bank.account-bank.bonus', compact('bonuses'));
    }

    public function bonusCreate(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'type' => 'required|in:banking,usdt,binance',
            'min_amount' => 'required|integer',
            'bonus' => 'required|integer',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        try {
            $bonus = new RechargeBonus();
            $bonus->type = $request->type;
            $bonus->min_amount = $request->min_amount;
            $bonus->bonus = $request->bonus;
            $bonus->status = $request->status;
            $bonus->domain = request()->getHost();
            $bonus->save();

            return response()->json([
                'success' => true,
                'message' => 'Tạo thành công thưởng nạp tiền',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo thất bại: ' . $th->getMessage(),
            ]);
        }
    }
}