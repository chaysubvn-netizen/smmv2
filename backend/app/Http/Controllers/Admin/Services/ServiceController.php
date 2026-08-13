<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\Category;
use App\Models\RateUpdate;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        if (request()->getHost() == env("MAIN_SITE")) {
            return view('admin.services.index');
        } else {
            return view('admin.services.index_childpanel');
        }
    }

    public function create()
    {
        
        $categories = Category::where('status', 'active')->orderBy('sort_order', 'asc')->get();
        $providers = ApiProvider::where('status', 'active')->get();
        return view('admin.services.create', compact('categories', 'providers'));
    }

    public function store(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        $valid = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'mode' => 'required|in:option,manual',
            'provider' => 'required_if:mode,option',
            'provider_id' => 'required_if:mode,option',
            'type' => 'required',
            'min' => 'required|integer|min:0',
            'max' => 'required|integer|min:0',
            'rate' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'refill' => 'required|boolean',
            'cancel' => 'required|boolean',
            'warranty' => 'required|integer',
            'attributes' => 'nullable|array',
            'attributes.*' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first()
            ]);
        } else {

            $category = Category::find($request->category_id);
            if ($category->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Danh mục không khả dụng'
                ]);
            }

            if ($request->mode == 'option') {
                $provider = ApiProvider::where('id', $request->provider)->where('status', 'active')->first();
                if (!$provider) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Api Provider không khả dụng'
                    ]);
                }
            }

            try {
                $service = new Service();
                $service->category_id = $request->category_id;
                $service->name = $request->name;
                $service->description = $request->description;
                $service->icon = null;
                    $service->code = Str::random(10);
                $service->mode = $request->mode;
                $service->rate_original = $request->rate;
                $service->rate = $request->rate;
                $service->min = $request->min;
                $service->max = $request->max;
                $service->provider = $request->provider;
                $service->provider_id = $request->provider_id;
                $service->type = $request->type;
                $service->status = $request->status;
                $service->refill = $request->refill;
                $service->cancel = $request->cancel;
                $service->warranty = $request->warranty;
                $service->attributes = $request->input('attributes', []);
                $service->note = $request->note;
                $service->main_domain = $request->getHost();
                $service->domain = $request->getHost();
                $service->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Thêm dịch vụ thành công',
                    'redirect' => route('admin.services.index')
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function edit($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return redirect()->route('admin.services.index')->with('error', 'Dịch vụ không tồn tại');
        }

        $categories = Category::where('status', 'active')->orderBy('sort_order', 'asc')->get();
        $providers = ApiProvider::where('status', 'active')->get();
        return view('admin.services.edit', compact('service', 'categories', 'providers'));
    }

    public function update(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        if ($request->getHost() == env("MAIN_SITE")) {
            $valid = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string',
                'mode' => 'required|in:option,manual',
                'provider' => 'required_if:mode,option',
                'provider_id' => 'required_if:mode,option',
                'type' => 'required',
                'min' => 'required|integer|min:0',
                'max' => 'required|integer|min:0',
                'rate' => 'required|numeric',
                'status' => 'required|in:active,inactive',
                'refill' => 'required|boolean',
                'cancel' => 'required|boolean',
                'warranty' => 'required|integer',
                'attributes' => 'nullable|array',
                'attributes.*' => 'required|string',
                'description' => 'nullable|string',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            } else {

                $category = Category::find($request->category_id);
                if ($category->status != 'active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Danh mục không khả dụng'
                    ]);
                }

                if ($request->mode == 'option') {
                    $provider = ApiProvider::where('id', $request->provider)->where('status', 'active')->first();
                    if (!$provider) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Api Provider không khả dụng'
                        ]);
                    }
                }

                try {
                    $service = Service::find($id);
                    if (!$service) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Dịch vụ không tồn tại'
                        ]);
                    }

                    $service->category_id = $request->category_id;
                    $service->name = $request->name;
                    $service->description = $request->description;
                    $service->icon = null;
                        $service->code = Str::random(10);
                    $service->mode = $request->mode;
                    $service->rate_original = $request->rate;
                    $service->rate = $request->rate;
                    $service->min = $request->min;
                    $service->max = $request->max;
                    $service->provider = $request->provider;
                    $service->provider_id = $request->provider_id;
                    $service->type = $request->type;
                    $service->status = $request->status;
                    $service->refill = $request->refill;
                    $service->cancel = $request->cancel;
                    $service->warranty = $request->warranty;
                    $service->attributes = $request->input('attributes', []);
                    $service->note = $request->note;
                    $service->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Cập nhật dịch vụ thành công',
                        'redirect' => route('admin.services.index')
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
            }
        } else {
            $valid = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string',
                'type' => 'required',
                'min' => 'required|integer|min:0',
                'max' => 'required|integer|min:0',
                'rate' => 'required|numeric',
                'status' => 'required|in:active,inactive',
                'refill' => 'required|boolean',
                'cancel' => 'required|boolean',
                'warranty' => 'required|integer',
                'attributes' => 'nullable|array',
                'attributes.*' => 'required|string',
                'description' => 'nullable|string',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ]);
            } else {

                $category = Category::find($request->category_id);
                if ($category->status != 'active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Danh mục không khả dụng'
                    ]);
                }

                try {
                    $service = Service::find($id);
                    if (!$service) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Dịch vụ không tồn tại'
                        ]);
                    }

                    $service->category_id = $request->category_id;
                    $service->name = $request->name;
                    $service->description = $request->description;
                    $service->icon = null;
                        $service->code = Str::random(10);
                    $service->mode = $request->mode;
                    $service->rate = $request->rate;
                    $service->min = $request->min;
                    $service->max = $request->max;
                    $service->type = $request->type;
                    $service->status = $request->status;
                    $service->refill = $request->refill;
                    $service->cancel = $request->cancel;
                    $service->warranty = $request->warranty;
                    $service->attributes = $request->input('attributes', []);
                    $service->note = $request->note;
                    $service->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Cập nhật dịch vụ thành công',
                        'redirect' => route('admin.services.index')
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    public function editRate($id)
    {
        $service = Service::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$service) {
            return redirect()->route('admin.services.index')->with('error', 'Dịch vụ không tồn tại');
        }

        return view('admin.services.edit-rate', compact('service'));
    }

    public function updateRate(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        $valid = Validator::make($request->all(), [
            'rate_new' => 'required|numeric',
            'type' => 'required|in:increase,decrease',
        ]);
        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first()
            ]);
        } else {
            try {
                $service = Service::where('domain', request()->getHost())->where('id', $id)->first();
                if (!$service) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dịch vụ không tồn tại'
                    ]);
                }

                RateUpdate::create([
                    'service_id' => $service->id,
                    'rate_old' => $service->rate,
                    'rate_new' => $request->rate_new,
                    'type' => $request->type,
                    'domain' => request()->getHost()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật tỷ lệ thành công',
                    'redirect' => route('admin.services.update-rates')
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function updateRates()
    {
        $services = Service::where('domain', request()->getHost())->get();
        return view('admin.services.update-rates', compact('services'));
    }

    public function updateAttributes(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        $ids = $request->get('ids'); // 1,2,3,4

        $services = Service::whereIn('id', explode(',', $ids))->where('domain', request()->getHost())->get();
        return view('admin.services.update-attributes', compact('services'));
    }

    public function updateDescription(Request $request)
    {
        $ids = $request->get('ids'); // 1,2,3,4

        $services = Service::whereIn('id', explode(',', $ids))->where('domain', request()->getHost())->get();
        return view('admin.services.update-description', compact('services'));
    }

    public function updateServices(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        $action = $request->input('action');
        $ids = $request->input('ids'); // 1,2,3,4
        $services = Service::whereIn('id', explode(',', $ids))->where('domain', request()->getHost())->get();
        if ($action == 'attributes') {
            $attributes = $request->input('attributes');
            foreach ($services as $service) {
                $service->attributes = $attributes;
                $service->save();
            }
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thuộc tính thành công',
                'redirect' => route('admin.services.index')
            ]);
        }
        if ($action == 'description') {
            $description = $request->input('description');
            foreach ($services as $service) {
                $service->description = $description;
                $service->save();
            }
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật mô tả thành công',
                'redirect' => route('admin.services.index')
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Hành động không hợp lệ'
        ]);
    }
}
