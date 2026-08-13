<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestLicenseController extends Controller
{
    /**
     * Mock verification endpoint.
     */
    public function verify(Request $request)
    {
        $key = $request->input('key');
        $domain = $request->input('domain');

        // Simple Mock Rule:
        // Key is VALID if it starts with "MINSMM-" (e.g., MINSMM-TEST-123)
        // You can also add specific logic to check $domain if needed.

        // Mock Rule: Always invalid unless you add real check logic
        return response()->json([
            'status' => 'invalid',
            'message' => 'License key invalid (Mock)'
        ], 200);
    }

    /**
     * Mock update check endpoint.
     */
    public function checkUpdate(Request $request) {
        // Mock response: No update available (safest for loop)
        return response()->json([
            'ok' => true,
            'update_available' => false,
            'message' => 'You are on the latest version (Mock).'
        ]);

        /* 
        // Uncomment to test update found:
        return response()->json([
            'ok' => true,
            'update_available' => true,
            'latest_version' => '1.0.3',
            'file_url' => 'https://example.com/update.zip', // Fake URL
            'changelog' => 'New features added.'
        ]);
        */
    }
}

