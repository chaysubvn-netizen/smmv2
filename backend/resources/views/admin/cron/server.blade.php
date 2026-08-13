@extends('client.layouts.master')
@section('title', 'Quản lý Server Cron')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Danh sách Server Cron</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServerModal">Thêm Server</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên Server</th>
                                <th>Giá (VNĐ/tháng)</th>
                                <th>Khu vực</th>
                                <th>Giới hạn</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servers as $server)
                            <tr>
                                <td>{{ $server->id }}</td>
                                <td>{{ $server->name }}</td>
                                <td>{{ number_format($server->price) }} đ</td>
                                <td>{{ $server->region }}</td>
                                <td>{{ $server->limit_req }}</td>
                                <td>
                                    @if($server->status)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Bảo trì</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editServer(@json($server))" data-bs-toggle="modal" data-bs-target="#editServerModal">Sửa</button>
                                    <a href="{{ route('admin.cron.server.delete', $server->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Xóa server này?')">Xóa</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addServerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.cron.server.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Server Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tên Server</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Giá (VNĐ)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Khu vực</label>
                        <input type="text" name="region" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editServerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="editForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Server</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tên Server</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Giá (VNĐ)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Khu vực</label>
                        <input type="text" name="region" id="edit_region" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editServer(server) {
        document.getElementById('edit_name').value = server.name;
        document.getElementById('edit_price').value = server.price;
        document.getElementById('edit_region').value = server.region;
        document.getElementById('editForm').action = "/admin/cron/server/update/" + server.id;
    }
</script>
@endsection
