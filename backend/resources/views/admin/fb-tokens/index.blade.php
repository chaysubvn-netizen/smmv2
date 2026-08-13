@extends('admin.layouts.app')

@section('title', 'Quản lý Token FB')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Thêm Tài Nguyên</h4>
                <p class="text-muted font-14">
                    Mỗi dòng một Cookie hoặc Token (hoặc Cookie|AccessToken)
                </p>

                <form action="{{ route('admin.fb-tokens.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="list" class="form-control" rows="8" placeholder="Mỗi dòng một Cookie hoặc Token (hoặc Cookie|AccessToken)"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại dữ liệu</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type1" value="cookie_token" checked>
                                    <label class="form-check-label" for="type1">Cookie|AccessToken</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type2" value="cookie">
                                    <label class="form-check-label" for="type2">Cookie</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type3" value="token">
                                    <label class="form-check-label" for="type3">AccessToken</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ghi chú (Tùy chọn)</label>
                            <input type="text" name="note" class="form-control" placeholder="VD: Nick clone, Nick 2FA...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="ri-add-line"></i> Thêm Tài Nguyên
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="header-title">Danh Sách Tài Nguyên</h4>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.fb-tokens.delete-all') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tất cả?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa Tất Cả</button>
                        </form>
                        <form action="{{ route('admin.fb-tokens.delete-die') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tất cả Die?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning">Xóa Hết Die</button>
                        </form>
                    </div>
                </div>

                <div class="mb-3 d-flex justify-content-between flex-wrap gap-2">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.fb-tokens.index', ['status' => 'all']) }}" class="btn btn-sm {{ request('status', 'all') == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            All <span class="badge bg-light text-dark ms-1">{{ $stats['all'] }}</span>
                        </a>
                        <a href="{{ route('admin.fb-tokens.index', ['status' => 'live']) }}" class="btn btn-sm {{ request('status') == 'live' ? 'btn-success' : 'btn-outline-success' }}">
                            Live <span class="badge bg-light text-dark ms-1">{{ $stats['live'] }}</span>
                        </a>
                        <a href="{{ route('admin.fb-tokens.index', ['status' => 'processing']) }}" class="btn btn-sm {{ request('status') == 'processing' ? 'btn-info' : 'btn-outline-info' }}">
                            Processing <span class="badge bg-light text-dark ms-1">{{ $stats['processing'] }}</span>
                        </a>
                        <a href="{{ route('admin.fb-tokens.index', ['status' => 'block']) }}" class="btn btn-sm {{ request('status') == 'block' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Block <span class="badge bg-light text-dark ms-1">{{ $stats['block'] }}</span>
                        </a>
                        <a href="{{ route('admin.fb-tokens.index', ['status' => 'die']) }}" class="btn btn-sm {{ request('status') == 'die' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Die <span class="badge bg-light text-dark ms-1">{{ $stats['die'] }}</span>
                        </a>
                    </div>

                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm UID, tên, ghi chú..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-secondary">Lọc</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                    </div>
                                </th>
                                <th>TÀI NGUYÊN</th>
                                <th>GHI CHÚ</th>
                                <th>TRẠNG THÁI</th>
                                <th>UID FB</th>
                                <th>TÊN FB</th>
                                <th>THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tokens as $token)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $token->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="max-width: 300px;">
                                        <span class="badge bg-soft-primary text-primary me-2">{{ strtoupper($token->type) }}</span>
                                        <code class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $token->data }}">
                                            {{ $token->data }}
                                        </code>
                                    </div>
                                    <small class="text-muted d-block mt-1">ID: #{{ $token->id }}</small>
                                </td>
                                <td>{{ $token->note ?? '---' }}</td>
                                <td>
                                    @if($token->status == 'live')
                                        <span class="badge bg-success">LIVE</span>
                                    @elseif($token->status == 'die')
                                        <span class="badge bg-danger">DIE</span>
                                    @elseif($token->status == 'block')
                                        <span class="badge bg-warning">BLOCK</span>
                                    @else
                                        <span class="badge bg-info">{{ strtoupper($token->status) }}</span>
                                    @endif
                                </td>
                                <td><code>{{ $token->uid ?? '---' }}</code></td>
                                <td>{{ $token->name ?? '---' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-soft-info" onclick="refreshToken({{ $token->id }})" title="Làm mới">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger" onclick="deleteToken({{ $token->id }})" title="Xóa">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $tokens->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function deleteToken(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa tài nguyên này?')) return;
        
        $.ajax({
            url: `{{ url('/admin/facebook-tokens/delete') }}/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    location.reload();
                }
            }
        });
    }

    function refreshToken(id) {
        // Show loading state or toast
        const btn = $(event.currentTarget);
        btn.find('i').addClass('ri-loader-4-line').removeClass('ri-refresh-line');
        
        $.ajax({
            url: `{{ url('/admin/facebook-tokens/refresh') }}/${id}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: res.is_live ? 'success' : 'error',
                        title: res.is_live ? 'Live' : 'Die',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            complete: function() {
                btn.find('i').removeClass('ri-loader-4-line').addClass('ri-refresh-line');
            }
        });
    }

    $(document).ready(function() {
        $('#checkAll').change(function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
        });
    });
</script>
@endsection
