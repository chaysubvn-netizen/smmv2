@extends('admin.layouts.app')
@section('title', 'Import Services')
@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
    }
    
    .card.border-0.shadow-sm.rounded-4 {
        transition: all 0.3s ease;
        border: 1px solid var(--glass-border) !important;
    }
    
    .card.border-0.shadow-sm.rounded-4:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        padding-left: 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 10px;
    }
</style>
@endsection
@section('content')
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold" style="letter-spacing: -0.5px;">Import Services</h4>
            <p class="text-muted mb-0 fs-13">Bắt đầu nhập dịch vụ mới từ các nguồn cung cấp API</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark mb-0">Thiết lập nguồn nhập</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.import.services.index') }}" method="GET">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label text-dark fw-bold fs-13 mb-2">Phân loại đích</label>
                                <select name="category_id" id="category_id" class="form-select select2-custom">
                                    <option value="">-- Chọn phân loại sẽ lưu vào --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="provider_id" class="form-label text-dark fw-bold fs-13 mb-2">Nguồn cung cấp (API Provider)</label>
                                <select name="provider_id" id="provider_id" class="form-select select2-custom">
                                    <option value="">-- Chọn API PROVIDER nguồn --</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="bx bx-search-alt me-1"></i> Kiểm tra và lấy danh sách dịch vụ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#category_id').select2({
                placeholder: '-- Chọn phân loại --',
                allowClear: true,
                width: '100%'
            });
            $('#provider_id').select2({
                placeholder: '-- Chọn API PROVIDER --',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
