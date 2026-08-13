@extends('admin.layouts.app')
@section('title', 'Danh sách bài viết')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách bài viết</h4>
                    <div class="">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modal-posts-create">
                            <i class="bx bx-plus"></i> Thêm bài viết
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-posts">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Ảnh bìa</th>
                                    <th>Trạng thái</th>
                                    <th>Người viết</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-posts-create" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" action="{{ route('admin.posts.create') }}" method="POST" data-ajax="true"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề bài viết</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài viết</label>
                        <textarea class="form-control" id="content" name="content" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Ảnh bìa</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Hiển thị</option>
                            <option value="inactive">Ẩn</option>
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
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('content');
                //update content CKEditor
                CKEDITOR.instances['content'].on('change', function() {
                    this.updateElement();
                });
            }

            let table = $('#table-posts').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Post']) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data,type,row){
                            return `
                                <a href="${row?.edit_url}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger-gradient btn-sm" onclick="deleteData([${row?.id}], 'Post')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            `;
                        }
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function(data, type, row) {
                            return `<img src="${data}" alt="Image" style="width: 100px; height: auto;">`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return data == 'active' ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-danger">Ẩn</span>';
                        }
                    },
                    {
                        data: 'user',
                        name: 'user',
                        render: function(data, type, row) {
                            return data ? data.username : '<span class="text-danger">Unknown</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    }
                ],
            });
        });
    </script>
@endsection
