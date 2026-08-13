@extends('admin.layouts.app')
@section('title', 'Danh sách nền tảng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách nền tảng</h4>
                    <div class="card-tool">
                        <button type="button" class="btn btn-primary btn-primary-gradient btn-wave" data-bs-toggle="modal"
                            data-bs-target="#addPlatformModal">
                            <i class="bx bx-plus"></i>
                            <span>Thêm mới</span>
                        </button>
                        <button type="button" class="btn btn-danger btn-danger-gradient btn-wave" id="btn-delete">
                            <i class="bx bx-trash"></i>
                            <span>Xoá</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsve">
                        <table class="table w-100 table-bordered" id="table-platforms">
                            <thead>
                                <tr>
                                    <th width="10"></th>
                                    <th>ID</th>
                                    <th style="width: 1%;">
                                        <input type="checkbox" id="check-all" class="form-check-input">
                                    </th>
                                    <th>Ưu tiên</th>
                                    <th>Thao tác</th>
                                    <th>Tên nền tảng</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian tạo</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPlatformModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.platforms.create') }}" method="POST" data-ajax="true"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm mới nền tảng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="icon" class="form-label required">Icon</label>
                        <input type="file" class="form-control" id="icon" name="icon" placeholder="Nhập icon">
                    </div>
                    <div class="form-group mb-3">
                        <label for="sort_order" class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0"
                            placeholder="Nhập thứ tự">
                    </div>
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Tên nền tảng</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Nhập tên nền tảng">
                    </div>
                      <div class="form-group mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="form_status" class="form-control">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Ngừng hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let table = $('#table-platforms').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Platform']) }}",
                    type: "GET",
                },
                columns: [{
                        data: null,
                        orderable: false,
                        className: 'reorder',
                        render: function(data, type, row) {
                            return '<i class="bx bx-move-vertical fs-18" style="cursor: move;"></i>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="form-check-input" name="ids[]" value="${row.id}">`
                        }
                    },
                    {
                        data: 'sort_order',
                        name: 'sort_order',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm text-center" 
                                        value="${data}" 
                                        onchange="changePriority(${row.id}, this.value)" 
                                        style="width: 70px; margin: 0 auto;">`
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<a href="${row.edit_url}" class="btn btn-primary btn-sm btn-edit"><i class="bx bx-edit"></i></a>`
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<div class="d-flex align-items-center">
                                <img src="${row.icon}" alt="${data}" class="me-2" width="30" height="30">
                                <span>${data}</span>
                            </div>`
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data == 'active') {
                                return `<span class="badge bg-success">Hoạt động</span>`
                            } else {
                                return `<span class="badge bg-danger">Ngừng hoạt động</span>`
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss')
                        }
                    },
                ],
                order: [
                    [3, 'asc']
                ],
                rowReorder: {
                    selector: 'td.reorder',
                    dataSrc: 'sort_order'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            })

            table.on('row-reorder', function(e, diff, edit) {
                setTimeout(function() {
                    let ids = [];
                    $('#table-platforms tbody tr').each(function() {
                        let id = $(this).find('input[name="ids[]"]').val();
                        if (id) {
                            ids.push(id);
                        }
                    });

                    if (ids.length > 0) {
                        $.ajax({
                            url: "{{ route('admin.loaddata.sort') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                model: 'Platform',
                                data: ids
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công',
                                        text: response.message,
                                        timer: 800,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Lỗi!', response.message, 'error');
                                    table.ajax.reload(null, false);
                                }
                            },
                            error: function() {
                                Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ', 'error');
                                table.ajax.reload(null, false);
                            }
                        });
                    }
                }, 200);
            });

            $('#check-all').on('click', function() {
                if ($(this).is(':checked')) {
                    $('input[name="ids[]"]').prop('checked', true);
                } else {
                    $('input[name="ids[]"]').prop('checked', false);
                }
            })

            // disable delete button if no checkbox is checked
            $('input[name="ids[]"]').on('click', function() {
                if ($('input[name="ids[]"]:checked').length > 0) {
                    $('#btn-delete').removeClass('disabled');
                } else {
                    $('#btn-delete').addClass('disabled');
                }
            })

            $('#btn-delete').on('click', function() {
                let ids = [];
                $('input[name="ids[]"]:checked').each(function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    return deleteData(ids, 'Platform', table);
                } else {
                    Swal.fire(
                        'Lỗi!',
                        'Vui lòng chọn ít nhất một mục để xoá.',
                        'error'
                    )
                }
            })

        })

        function changePriority(id, value) {
            $.ajax({
                url: "{{ route('admin.loaddata.update-field') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    model: 'Platform',
                    field: 'sort_order',
                    value: value
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message, 'error');
                    }
                },
                error: function(error) {
                    Swal.fire('Lỗi!', 'Đã xảy ra lỗi, vui lòng thử lại sau.', 'error');
                }
            });
        }
    </script>
@endsection
