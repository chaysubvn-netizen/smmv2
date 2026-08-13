<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateRef;
use Illuminate\Http\Request;

class AffiliateAdminController extends Controller
{
    public function affiliates()
    {

        $affiliates = AffiliateRef::where('domain', request()->getHost());

        return view('admin.affiliates.index', [
            'affiliates' => $affiliates,
        ]);
    }
}
