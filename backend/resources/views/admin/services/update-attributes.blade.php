@extends('admin.layouts.app')
@section('title', 'Cập nhât thuộc tính dịch vụ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Cập nhật thuộc tính</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.services.update-services', ['action' => 'attributes']) }}" data-ajax="true" method="POST">
                        @csrf
                        <input type="hidden" name="ids" value="{{ request()->get('ids') }}">
                        <div class="col-md-12 form-group mb-3">
                            <label for="attributes" class="form-label">Thuộc tính</label>
                            <select name="attributes[]" id="attributes" class="form-control" multiple>
                                @foreach ($attributes = attribute_views(null, false, true) as $key => $attribute)
                                    <option value="{{ $key }}">{{ $attribute[0] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
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
        $(document).ready(function() {
            $('#attributes').select2({
                placeholder: 'Chọn thuộc tính',
                allowClear: true,
                tags: true,
            });
        });
    </script>
@endsection
