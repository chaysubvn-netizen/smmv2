@extends('client.layouts.master')
@section('title', __('Đặt hàng SLL'))
@section('content')
    @include('client.layouts.breadcrumb', ['title' => 'Đặt hàng SLL'])
    <div class="row gy-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('request-order') }}" class="mb-3" id="mass-order">
                        <div class="form-group mb-3">
                            <label for="data-order"
                                class="form-label">{{ __('Nhập mỗi đơn hàng là một dòng theo định dạng bên dưới') }}</label>
                            <textarea class="form-control" id="data-order" rows="10" name="data-order" placeholder=""></textarea>
                        </div>
                        {{-- <div class="form-switch switch-primary d-flex align-items-center gap-3 mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="switch1">
                            <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="switch1">Đặt lịch chạy. Múi giờ: +07:00</label>
                        </div>
                        <div class="form-switch switch-primary d-flex align-items-center gap-3 mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="switch2">
                            <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="switch2">
                                <small>Đặt hàng lặp lại. Tự động đặt lại đơn hàng sau khi đơn hàng này <strong>HOÀN THÀNH</strong>. Đảm bảo số dư của bạn đủ cho các đơn hàng sau.</small>
                            </label>
                        </div> --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary col-12">{{ __('Tạo đơn hàng') }}</button>
                        </div>
                    </form>
                    <div id="alert-notice"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#data-order').attr('placeholder', 'service_id | link | quantity\n');

        $(document).ready(function() {
            $('#mass-order').on('submit', function(e) {
                e.preventDefault();
                let data = $('#data-order').val();
                let alertNotice = $('#alert-notice');
                alertNotice.html('');

                if (data == '') {
                    alertNotice.append(
                        '<div class="alert bg-danger text-white fw-bold text-sm">Vui lòng nhập dữ liệu</div>'
                    );
                    return;
                }

                let formData = new FormData();

                let lines = data.split('\n');
                let count = 0;
                lines.forEach(function(line) {
                    if (line.trim() != '') {
                        count++;
                    }
                });
                lines.forEach(function(line) {
                    try {
                        let parts = line.split('|');
                        let serviceId = parts[0].trim() || '';
                        let link = parts[1].trim() || '';
                        let quantity = parts[2].trim() || '';
                        let comments = parts[3] ? parts[3].trim() : '';

                        $.ajax({
                            url: '{{ route('request-order') }}',
                            type: 'POST',
                            data: {
                                service: serviceId,
                                link: link,
                                quantity: quantity,
                                "comments": comments,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success == true) {
                                    alertNotice.append(
                                        '<div class="alert bg-success text-white fw-bold text-sm">ORDER ID: ' +
                                        response.data.order_id + '</div>'
                                    );
                                } else {
                                    alertNotice.append(
                                        '<div class="alert bg-danger text-white fw-bold text-sm">' +
                                        response.message + '</div>'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                let errorMessage = xhr.responseJSON ? xhr.responseJSON
                                    .message : 'Đã xảy ra lỗi';
                                alertNotice.append(
                                    '<div class="alert bg-danger text-white fw-bold text-sm">' +
                                    errorMessage + '</div>'
                                );
                            }
                        });
                    } catch (e) {
                        alertNotice.append(
                            '<div class="alert bg-danger text-white fw-bold text-sm">Đã xảy ra lỗi khi tạo đơn hàng</div>'
                        );
                    }

                });

            });
        });
    </script>
@endsection
