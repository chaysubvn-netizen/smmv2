<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use DB;

class LicenseServerController extends Controller
{
    // Public: Verify License (Remote Check)
    public function check(Request $request)
    {
        $key = $request->input('key') ?? $request->input('license_key');
        $domain = $request->input('domain');

        if (!$key || !$domain) {
            return response()->json(['status' => 'error', 'message' => 'Missing parameters'], 400);
        }

        $license = License::where('license_key', $key)->first();

        if (!$license) {
            return response()->json(['status' => 'error', 'message' => 'Invalid license key'], 404);
        }

        // Optional: Check domain strictly or loosely
        // If domain is not set on license, maybe bind it now? 
        // Or if it IS set, key must match.
        if ($license->domain && $license->domain !== $domain) {
            return response()->json(['status' => 'error', 'message' => 'Invalid domain for this license'], 403);
        }

        if ($license->status !== 'active') {
             return response()->json(['status' => 'error', 'message' => 'License is ' . $license->status], 403);
        }

        // If domain was empty, bind it (First use activation) - Optional logic, often desired
        if (empty($license->domain)) {
            $license->update(['domain' => $domain]);
        }

        return response()->json([
            'status' => 'active',
            'message' => 'License is valid',
            'data' => [
                'product' => $license->product->name ?? 'Unknown',
                'version' => $license->product->version ?? '1.0.0',
            ]
        ]);
    }

    // Auth: Buy License
    public function buy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'domain' => 'required|string|min:3' // simplistic domain validation
        ]);

        $user = Auth::user();
        $product = Product::find($request->product_id);

        if ($user->balance < $product->price) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient balance']);
        }

        // Check if domain already has a license for this product?
        // Let's assume one license per domain per product is unique.
        $exists = License::where('domain', $request->domain)
                         ->where('product_id', $product->id)
                         ->exists();
        
        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'License for this domain and product already exists']);
        }

        try {
            DB::beginTransaction();

            // Deduct balance
            $user->decrement('balance', $product->price);

            // Create License
            $license = License::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'license_key' => 'MINSMM-' . strtoupper(Str::random(16)), // Format match
                'domain' => $request->domain,
                'status' => 'active',
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mua bản quyền thành công!',
                'data' => $license
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'System error: ' . $e->getMessage()]);
        }
    }

    // Auth: Get My Licenses
    public function getLicenses(Request $request)
    {
        $licenses = License::with('product')
                           ->where('user_id', Auth::id())
                           ->latest()
                           ->get()
                           ->map(function ($license) {
                               // Flatten/Add fields to match legacy/expected format if needed
                               $license->price = $license->product->price ?? 0;
                               return $license;
                           });

        return response()->json([
            'status' => 'success',
            'message' => 'Licenses fetched',
            'data' => $licenses
        ]);
    }
    
    // Auth: Get User Info
    public function getUser(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'message' => 'User info',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'balance' => $user->balance,
                'role' => $user->role,
            ]
        ]);
    }

    // Auth: Get Products (if needed specifically for license page)
    public function getProducts()
    {
        // Maybe filter by category or a flag 'is_software' if you have one.
        // For now, return all or specifically categorized ones.
        // $products = Product::where('status', 'test')->get(); // Example filter
        
        // If you want ALL products, just Product::all() or paginate.
        // User snippet used `get_all_products()`. I'll assume all active.
        $products = Product::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Products fetched',
            'data' => $products
        ]);
    }
}
