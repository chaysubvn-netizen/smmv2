@extends('admin.layouts.app')
@section('title', 'Thêm dịch vụ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Thêm dịch vụ mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.services.store') }}" method="POST" data-ajax="true">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="category_id" class="form-label">Phân loại</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="name" class="form-label">Tên dịch vụ</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Nhập tên dịch vụ">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="mode" class="form-label">Chế độ</label>
                                <select name="mode" id="mode" class="form-control">
                                    <option value="option">Option</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="col-md-12" id="option-html">
                                <div class="form-group mb-3">
                                    <label for="provider" class="form-label">API PROVIDER</label>
                                    <select name="provider" id="provider" class="form-control">
                                        <option value="">Chọn nhà cung cấp</option>
                                        @foreach ($providers as $provider)
                                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="provider_id" class="form-label">ID Dịch vụ</label>
                                    <input type="text" name="provider_id" id="provider_id" class="form-control"
                                        placeholder="Nhập ID dịch vụ">
                                </div>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="Default">Default</option>
                                    <option value="Package">Package</option>
                                    <option value="SEO">SEO</option>
                                    <option value="Custom Comments">Custom Comments</option>
                                    <option value="Mentions with Hashtags">Mentions with Hashtags</option>
                                    <option value="Mentions Custom List">Mentions Custom List</option>
                                    <option value="Mentions Hashtag">Mentions Hashtag</option>
                                    <option value="Mentions User Followers">Mentions User Followers</option>
                                    <option value="Mentions Media Likers">Mentions Media Likers</option>
                                    <option value="Custom Comments Package">Custom Comments Package</option>
                                    <option value="Comment Likes">Comment Likes</option>
                                    <option value="Poll">Poll</option>
                                    <option value="Comment Replies">Comment Replies</option>
                                    <option value="Invites from Groups">Invites from Groups</option>
                                    <option value="Subscriptions">Subscriptions</option>
                                    <option value="Web Traffic">Web Traffic</option>
                                    <option value="Special (1DG)">Special (1DG)</option>
                                    <option value="Special1 (1DG)">Special1 (1DG)</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group mb-3">
                                <label for="min" class="form-label">Mua tối thiểu</label>
                                <input type="number" name="min" id="min" class="form-control"
                                    placeholder="Nhập số lượng tối thiểu">
                            </div>
                            <div class="col-md-6 col-lg-4 form-group mb-3">
                                <label for="max" class="form-label">Mua tối đa</label>
                                <input type="number" name="max" id="max" class="form-control"
                                    placeholder="Nhập số lượng tối đa">
                            </div>
                            <div class="col-md-6 col-lg-4 form-group mb-3">
                                <label for="rate" class="form-label">Giá trên 1000/Lượt</label>
                                <input type="text" name="rate" id="rate" class="form-control"
                                    placeholder="Nhập giá trên 1000 lượt">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="form_status" class="form-control">
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Ngừng hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3 form-group mb-3">
                                <label for="refill" class="form-label">Refill</label>
                                <select name="refill" id="refill" class="form-control">
                                    <option value="0">Không</option>
                                    <option value="1">Có</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3 form-group mb-3">
                                <label for="cancel" class="form-label">Cancel</label>
                                <select name="cancel" id="cancel" class="form-control">
                                    <option value="0">Không</option>
                                    <option value="1">Có</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3 form-group mb-3">
                                <label for="warranty" class="form-label">Bảo hành</label>
                                <input type="text" name="warranty" id="warranty" class="form-control"
                                    placeholder="Nhập số ngày bảo hành" value="0">
                                <small class="text-danger">Nhập 0 thì sẽ không bảo hành</small>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="attributes" class="form-label">Thêm thuộc tính</label>
                                <select name="attributes[]" id="attributes" class="form-control" multiple>
                                    @foreach ($attributes = attribute_views(null, false, true) as $key => $attribute)
                                        <option value="{{ $key }}">{{ $attribute[0] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="description" class="form-label">Ghi chú</label>
                                <textarea name="description" id="description" class="form-control" placeholder="Nhập ghi chú"></textarea>
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
