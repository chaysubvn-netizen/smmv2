@extends('client.layouts.master')
@section('title', __('Hỗ trợ'))
@section('content')
    @include('client.layouts.breadcrumb', ['title' => 'Hỗ trợ'])
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __("Liên hệ") }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('bvq/assets/image/communication.png') }}" alt="communication"
                                    class="rounded-circle" width="40px">
                                <div>
                                    <p class="text-muted text-xs fw-seminold" class="mb-0 p-0 m-0">Facebook</p>
                                    <a href="{{ s('facebook_link') }}"
                                        class="mb-0 text-gray-800 text-hover-primary text-sm fw-bold p-0 m-0"
                                        target="_blank">{{ s('facebook_link') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('bvq/assets/image/telegram.png') }}" alt="communication"
                                    class="rounded-circle" width="40px">
                                <div>
                                    <p class="text-muted text-xs fw-seminold" class="mb-0 p-0 m-0">Telegram</p>
                                    <a href="{{ s('telegram_link') }}"
                                        class="mb-0 text-gray-800 text-hover-primary text-sm fw-bold p-0 m-0"
                                        target="_blank">{{ s('telegram_link') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('bvq/assets/image/zalo.png') }}" alt="communication"
                                    class="rounded-circle" width="40px">
                                <div>
                                    <p class="text-muted text-xs fw-seminold" class="mb-0 p-0 m-0">Zalo</p>
                                    <a href="{{ s('zalo_link') }}"
                                        class="mb-0 text-gray-800 text-hover-primary text-sm fw-bold p-0 m-0"
                                        target="_blank">{{ s('zalo_link') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-3">
                    <div class="border p-3 rounded-2 border-danger">
                        <ul style="list-style: circle; padding-left: 20px;">
                            <li class="text-danger text-sm fw-bold">Cần rep nhanh inbox Telegram.</li>
                            <li class="text-danger text-sm fw-bold">Thời gian hỗ trợ: 7h30 - 22h30.</li>
                            <li class="text-danger text-sm fw-bold">Gặp lỗi gì vui lòng vào thẳng vấn đề để được hỗ trợ tốt
                                nhất nhé!.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body px-3">
                    <div class="mb-3 d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-primary btn-sm d-flex gap-1 align-items-center"
                            data-bs-toggle="modal" data-bs-target="#createTicketModal">
                            <iconify-icon icon="material-symbols:add-circle-outline"></iconify-icon>
                            {{ __("Tạo vé mới") }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __("Mã đơn hàng") }}</th>
                                    <th>{{ __("Tiêu đề") }}</th>
                                    <th>{{ __("Trạng thái") }}</th>
                                    <th>{{ __("Cập nhật") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($tickets->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">{{ __("Chưa có ticket nào") }}</td>
                                    </tr>
                                @endif
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <a href="{{ route('ticket.detail', $ticket->id) }}"
                                                class="text-primary text-hover-primary text-sm fw-bold p-0 m-0">
                                                #{{ $ticket->id }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $ticket->order_id ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $ticket->title }}
                                        </td>
                                        <td>
                                            @if ($ticket->status == 'completed')
                                                <span class="badge bg-success">{{ __("Đã xử lý") }}</span>
                                            @elseif($ticket->status == 'processing')
                                                <span class="badge bg-primary">{{ __("Đang xử lý") }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __("Chờ xử lý") }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $tickets->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('create-ticket') }}" method="POST" bvq-request class="modal-body">
                    <div class="text-center">
                        <span class="fs-3 fw-bold text-primary text-center mb-3">{{ __("Thêm vé mới") }}</span>
                    </div>
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">{{ __("Tiêu đề") }}</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Nhập tiêu đề">
                        </div>
                        <div class="form-group mb-3">
                            <label for="type" class="form-label">{{ __("Loại hỗ trợ") }}</label>
                            <select name="type" id="type" class="form-control">
                                <option value="orther">{{ __("Khác") }}</option>
                                <option value="refill">{{ __("Bảo hành") }}</option>
                                <option value="cancel">{{ __("Huỷ đơn") }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3" id="order_id_div">
                            <label for="order_id" class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="order_id" name="order_id"
                                placeholder="Nhập mã đơn hàng">
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">{{ __("Nội dung") }}</label>
                            <textarea name="description" id="description" class="form-control" rows="5" placeholder="Nhập nội dung"></textarea>
                        </div>
                    </div>
                    <div class="d-flex mt-2 justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __("Đóng") }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ __("Thêm mới") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $("#order_id_div").hide();
            $("#type").change(function() {
                if ($(this).val() == "refill" || $(this).val() == "cancel") {
                    $("#order_id_div").show();
                } else {
                    $("#order_id_div").hide();
                }
            });
        })
    </script>
@endsection
