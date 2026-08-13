@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa dịch vụ')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card custom-card">
            <div class="card-header">
                <h4 class="card-title">Chỉnh sửa dịch vụ</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.update', ['id' => $service->id]) }}" method="POST"
                    data-ajax="true">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="category_id" class="form-label">Phân loại</label>
                            <select name="category_id" id="category_id" class="form-control">
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $category->id == $service->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="name" class="form-label">Tên dịch vụ</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $service->name }}"
                                placeholder="Nhập tên dịch vụ">
                        </div>
                        @if (request()->getHost() == env('MAIN_SITE'))
                        <div class="col-md-12 form-group mb-3">
                            <label for="mode" class="form-label">Chế độ</label>
                            <select name="mode" id="mode" class="form-control">
                                <option value="option" {{ $service->mode == 'option' ? 'selected' : '' }}>Option
                                </option>
                                <option value="manual" {{ $service->mode == 'manual' ? 'selected' : '' }}>Manual
                                </option>
                            </select>
                        </div>
                        <div class="col-md-12" id="option-html">
                            <div class="form-group mb-3">
                                <label for="provider" class="form-label">API PROVIDER</label>
                                <select name="provider" id="provider" class="form-control">
                                    <option value="">Chọn nhà cung cấp</option>
                                    @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}"
                                        {{ $provider->id == $service->provider ? 'selected' : '' }}>
                                        {{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="provider_id" class="form-label">ID Dịch vụ</label>
                                <input type="text" name="provider_id" id="provider_id" class="form-control"
                                    value="{{ $service->provider_id }}" placeholder="Nhập ID dịch vụ">
                            </div>
                        </div>
                        @endif
                        <div class="col-md-12 form-group mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="Default" {{ $service->type == 'Default' ? 'selected' : '' }}>Default
                                </option>
                                <option value="Package" {{ $service->type == 'Package' ? 'selected' : '' }}>Package
                                </option>
                                <option value="SEO" {{ $service->type == 'SEO' ? 'selected' : '' }}>SEO</option>
                                <option value="Custom Comments"
                                    {{ $service->type == 'Custom Comments' ? 'selected' : '' }}>Custom Comments
                                </option>
                                <option value="Mentions with Hashtags"
                                    {{ $service->type == 'Mentions with Hashtags' ? 'selected' : '' }}>Mentions with
                                    Hashtags</option>
                                <option value="Mentions Custom List"
                                    {{ $service->type == 'Mentions Custom List' ? 'selected' : '' }}>Mentions Custom
                                    List</option>
                                <option value="Mentions Hashtag"
                                    {{ $service->type == 'Mentions Hashtag' ? 'selected' : '' }}>Mentions Hashtag
                                </option>
                                <option value="Mentions User Followers"
                                    {{ $service->type == 'Mentions User Followers' ? 'selected' : '' }}>Mentions User
                                    Followers</option>
                                <option value="Mentions Media Likers"
                                    {{ $service->type == 'Mentions Media Likers' ? 'selected' : '' }}>Mentions Media
                                    Likers</option>
                                <option value="Custom Comments Package"
                                    {{ $service->type == 'Custom Comments Package' ? 'selected' : '' }}>Custom Comments
                                    Package</option>
                                <option value="Comment Likes" {{ $service->type == 'Comment Likes' ? 'selected' : '' }}>
                                    Comment Likes</option>
                                <option value="Poll" {{ $service->type == 'Poll' ? 'selected' : '' }}>Poll</option>
                                <option value="Comment Replies"
                                    {{ $service->type == 'Comment Replies' ? 'selected' : '' }}>Comment Replies
                                </option>
                                <option value="Invites from Groups"
                                    {{ $service->type == 'Invites from Groups' ? 'selected' : '' }}>Invites from Groups
                                </option>
                                <option value="Subscriptions" {{ $service->type == 'Subscriptions' ? 'selected' : '' }}>
                                    Subscriptions</option>
                                <option value="Web Traffic" {{ $service->type == 'Web Traffic' ? 'selected' : '' }}>Web
                                    Traffic</option>
                                <option value="Special (1DG)" {{ $service->type == 'Special (1DG)' ? 'selected' : '' }}>
                                    Special (1DG)</option>
                                <option value="Special1 (1DG)"
                                    {{ $service->type == 'Special1 (1DG)' ? 'selected' : '' }}>Special1 (1DG)</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group mb-3">
                            <label for="min" class="form-label">Mua tối thiểu</label>
                            <input type="number" name="min" id="min" class="form-control" value="{{ $service->min }}"
                                placeholder="Nhập số lượng tối thiểu">
                        </div>
                        <div class="col-md-6 col-lg-4 form-group mb-3">
                            <label for="max" class="form-label">Mua tối đa</label>
                            <input type="number" name="max" id="max" class="form-control" value="{{ $service->max }}"
                                placeholder="Nhập số lượng tối đa">
                        </div>
                        <div class="col-md-6 col-lg-4 form-group mb-3">
                            <label for="rate" class="form-label">Giá trên 1000/Lượt</label>
                            <input type="text" name="rate" id="rate" class="form-control" value="{{ $service->rate }}"
                                placeholder="Nhập giá trên 1000 lượt">
                        </div>
                     <div class="col-md-12 form-group mb-3">
    <label for="status" class="form-label">Trạng thái</label>

    <select name="status" id="status" class="form-control">
        <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>
            Hoạt động
        </option>

        <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>
            Ngừng hoạt động
        </option>
    </select>
</div>
                        <div class="col-md-6 col-lg-3 form-group mb-3">
                            <label for="refill" class="form-label">Refill</label>
                            <select name="refill" id="refill" class="form-control">
                                <option value="0" {{ $service->refill == 0 ? 'selected' : '' }}>Không</option>
                                <option value="1" {{ $service->refill == 1 ? 'selected' : '' }}>Có</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 form-group mb-3">
                            <label for="cancel" class="form-label">Cancel</label>
                            <select name="cancel" id="cancel" class="form-control">
                                <option value="0" {{ $service->cancel == 0 ? 'selected' : '' }}>Không</option>
                                <option value="1" {{ $service->cancel == 1 ? 'selected' : '' }}>Có</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 form-group mb-3">
                            <label for="warranty" class="form-label">Bảo hành</label>
                            <input type="text" name="warranty" id="warranty" class="form-control"
                                value="{{ $service->warranty }}" placeholder="Nhập số ngày bảo hành">
                            <small class="text-danger">Nhập 0 thì sẽ không bảo hành</small>
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="attributes" class="form-label">Thêm thuộc tính</label>
                            <select name="attributes[]" id="attributes" class="form-control" multiple>
                                @foreach (attribute_views(null, false, true) ?? [] as $key => $attribute)
                                <option value="{{ $key }}"
                                    {{ in_array($key, $service->attributes ?? []) ? 'selected' : '' }}>
                                    {{ $attribute[0] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="description" class="form-label">Ghi chú</label>
                            <textarea name="description" id="description" class="form-control"
                                placeholder="Nhập ghi chú">{{ $service->description }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <button type="submit" class="btn btn-primary btn-primary-gradient">
                                <i class="bx bxs-save"></i>
                                Thêm mới
                            </button>
                            <a href="{{ route('admin.services.index') }}"
                                class="btn btn-outline-secondary btn-secondary-gradient">
                                <i class="bx bx-arrow-back"></i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
CKEDITOR.replace('description');
CKEDITOR.instances['description'].on('change', function() {
    this.updateElement();
});
</script>
<script>
$(document).ready(function() {
    $('#category_id').select2({
        placeholder: 'Chọn phân loại',
        allowClear: true
    });
    $('#provider').select2({
        placeholder: 'Chọn nhà cung cấp',
        allowClear: true
    });
    $('#attributes').select2({
        placeholder: 'Chọn thuộc tính',
        allowClear: true
    });
});
</script>
<script>
$('#mode').change(function() {
    var mode = $(this).val();
    if (mode == 'option') {
        $('#option-html').show();
    } else {
        $('#option-html').hide();
    }
});
$('#mode').trigger('change');
</script>
@endsection