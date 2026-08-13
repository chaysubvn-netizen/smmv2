<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAdminController extends Controller
{
    public function index()
    {

        $totalUsers = User::where('domain', request()->getHost())->count();
        $totalBalance = User::where('domain', request()->getHost())->sum('balance');
        $totalDeposit = User::where('domain', request()->getHost())->sum('total_deposit');

        return view('admin.users.index', [
            
            'totalUsers' => $totalUsers,
            'totalBalance' => $totalBalance,
            'totalDeposit' => $totalDeposit,
        ]);
    }

    public function edit($id){
        
        $user = User::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Tài khoản không tồn tại');
        }

        $transactions = Transaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(2000)->get();

        return view('admin.users.edit', [
            'user' => $user,
            'transactions' => $transactions,
        ]);
    }

    public function update(Request $request, $id){
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $user = User::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản không tồn tại',
            ]);
        }

        if(!empty($request->new_password)){
            $user->password = Hash::make($request->new_password);
        }
        if(!empty($request->level)){
            $user->level = $request->level;
        }
        if(!empty($request->status)){
            $user->status = $request->status;
        }
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tài khoản thành công',
        ]);
    }

    public function updateBalance(Request $request, $id){
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $user = User::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản không tồn tại',
            ]);
        }

        if($request->action == 'add'){
            $user->balance += $request->balance;
            $user->total_deposit += $request->balance;
            $reason = 'Cộng tiền tài khoản';
        }elseif($request->action == 'sub'){
            $user->balance -= $request->balance;
            $reason = 'Trừ tiền tài khoản';
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Hành động không hợp lệ',
            ]);
        }


        if(!empty($request->reason)){
            $reason = $request->reason;
        }

        Transaction::create([
            'user_id' => $user->id,
            'transaction_code' => Str::random(10),
            'type' => $request->action,
            'balance_before' => $user->balance - $request->balance,
            'balance_after' => $user->balance,
            'amount' => $request->balance,
            'description' => $reason,
            'status' => 'success',
            'domain' => request()->getHost(),
        ]);

        $user->save();

        // Auto upgrade level
        if ($request->action == 'add') {
             $user->upgradeLevel($user->total_deposit);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tài khoản thành công',
        ]);
    }
}

