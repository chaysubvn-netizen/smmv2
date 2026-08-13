@extends('admin.layouts.app')
@section('title', 'Quản lý tiền tệ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách tiền tệ</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
                        Thêm tiền tệ mới
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 table-bordered" id="table-currencies">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Tên</th>
                                    <th>Mã tiền tệ</th>
                                    <th>Tỷ giá tiền tệ</th>
                                    <th>Tách đơn vị ngàn</th>
                                    <th>Phân số thập phân</th>
                                    <th>Số tập phân của tiền tệ</th>
                                    <th>Ngày thêm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($currencies as $item)
                                    <tr>
                                        <td>
                                            {{ $item->id }}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="modalEdit({{ $item->id }})"
                                                class="btn btn-sm btn-primary">
                                                <i class="bx bx-edit"></i>
                                                Sửa
                                            </a>
                                            <form action="{{ route('admin.currencies.delete', $item->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bx bx-trash"></i>
                                                    Xóa
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            {{ $item->name }}
                                        </td>
                                        <td>
                                            {{ $item->code }}
                                        </td>
                                        <td>
                                            {{ number_format($item->exchange_rate, $item->decimal_places) }}{{ $item->symbol }}
                                        </td>
                                        <td>
                                            {{ $item->thousand_separator }}
                                        </td>
                                        <td>
                                            {{ $item->decimal_separator }}
                                        </td>
                                        <td>
                                            {{ $item->decimal_places }}
                                        </td>
                                        <td>
                                            {{ $item->created_at }}
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

    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" action="{{ route('admin.currencies.create') }}" method="POST"data-ajax="true" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm tiền tệ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 form-group mb-3">
                            <label for="name" class="form-label">Tên quốc gia</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Tên quốc gia">
                        </div>
                        <div class="col-md-6 col-lg-4 form-group mb-3">
                            <label for="code" class="form-label">Mã tiền tệ</label>
                            <input type="text" class="form-control" id="code" name="code"
                                placeholder="Mã tiền tệ">
                        </div>
                        <div class="col-md-6 col-lg-4 form-group mb-3">
                            <label for="symbol" class="form-label">Ký hiệu tiền tệ</label>
                            <input type="text" class="form-control" id="symbol" name="symbol"
                                placeholder="Ký hiệu tiền tệ">
                        </div>
                        <div class="col-md-6 col-lg-6 form-group mb-3">
                            <label for="position" class="form-label">Vị trí hiển thị</label>
                            <select class="form-select" id="position" name="position">
                                <option value="right">Phía bên phải</option>
                                <option value="left">Phía bên trái</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-6 form-group mb-3">
                            <label for="thousand_separator" class="form-label">Dấu phân cách hàng nghìn</label>
                            <select class="form-select" id="thousand_separator" name="thousand_separator">
                                <option value="comma">, (Comma)</option>
                                <option value="dot">. (Dot)</option>
                                <option value="space"> (Space)</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-6 form-group mb-3">
                            <label for="decimal_separator" class="form-label">Dấu phân cách thập phân</label>
                            <select class="form-select" id="decimal_separator" name="decimal_separator">
                                <option value="dot">. (Dot)</option>
                                <option value="comma">, (Comma)</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-6 form-group mb-3">
                            <label for="decimal_places" class="form-label">Số thập phân</label>
                            <select name="decimal_places" id="decimal_places" class="form-control">
                                <option value="0">0</option>
                                <option value="1">0.0</option>
                                <option value="2">0.00</option>
                                <option value="3">0.000</option>
                                <option value="4">0.0000</option>
                            </select>
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label for="exchange_rate" class="form-label">Tỷ giá quy đổi (Bao nhiêu VND cho 1 đơn vị tiền tệ này)</label>
                            <input type="text" class="form-control" id="exchange_rate" name="exchange_rate"
                                placeholder="Tỷ giá quy đổi" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content" id="modalEditContent">
                 <!-- Content loaded via AJAX -->
             </div>
         </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#table-currencies').DataTable();
        })

        function modalEdit(id) {
            $.ajax({
                url: "{{ route('admin.currencies.edit', ':id') }}".replace(':id', id),
                type: "GET",
                success: function(data) {
                    $('#modalEditContent').html(data);
                    $('#modalEdit').modal('show');
                }
            });
        }
    </script>
@endsection
