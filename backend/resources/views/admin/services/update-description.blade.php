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
                    <form action="{{ route('admin.services.update-services', ['action' => 'description']) }}" data-ajax="true"
                        method="POST">
                        @csrf
                        <input type="hidden" name="ids" value="{{ request()->get('ids') }}">
                        <div class="col-md-12 form-group mb-3">
                            <label for="description" class="form-label">Ghi chú</label>
                            <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
@endsection
@section('script')
    <script>
        CKEDITOR.replace('description');
        CKEDITOR.instances['description'].on('change', function() {
            this.updateElement();
        });
    </script>
@endsection
