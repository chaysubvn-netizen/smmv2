@extends('admin.layouts.app')
@section('title', "Chỉnh sửa đơn hàng #$order->id")
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin đơn hàng</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.data.orders.update', $order->id) }}" method="POST" data-ajax="true">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="service" class="form-label">Dịch vụ</label>
                                <input type="text" class="form-control" id="service" name="service"
                                    value="{{ $order->service->name ?? 'Không tìm thấy' }}" readonly>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="text" class="form-control" id="link" name="link"
                                    value="{{ $order->link }}" readonly>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="text" class="form-control" id="quantity" name="quantity"
                                    value="{{ $order->quantity }}" readonly>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="comments" class="form-label">Comments</label>
                                <textarea class="form-control" id="comments" name="comments"
                                    rows="3" readonly>{{ data_get($order->input_data, 'comments', '') }}</textarea>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="text" class="form-control" id="rate" name="rate"
                                    value="{{ number_format((float) $order->rate, 3, '.', ',') }}đ" readonly>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="start_count" class="form-label">Start count</label>
                                <input type="text" class="form-control" id="start_count" name="start_count"
                                    value="{{ $order->start_count }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="remains" class="form-label">Remains</label>
                                <input type="text" class="form-control" id="remains" name="remains"
                                    value="{{ $order->remains }}">
                            </div>
                          <div class="col-md-6 form-group mb-3">

                                <label for="status" class="form-label">Trạng thái</label>



                                <select name="status" id="order_status" class="form-control" style="display:block; width:100%;">

                                    <option value="Pending" {{ old('status', $order->status) == 'Pending' ? 'selected' : '' }}>

                                        Chờ xử lý

                                    </option>


                                    <option value="Processing" {{ old('status', $order->status) == 'Processing' ? 'selected' : '' }}>

                                        Đang chạy

                                    </option>


                                    <option value="Completed" {{ old('status', $order->status) == 'Completed' ? 'selected' : '' }}>

                                        Hoàn thành

                                    </option>


                                    <option value="Partial" {{ old('status', $order->status) == 'Partial' ? 'selected' : '' }}>

                                        Hoàn tiền một phần

                                    </option>


                                    <option value="Canceled" {{ old('status', $order->status) == 'Canceled' ? 'selected' : '' }}>

                                        Huỷ

                                    </option>

                                    @if (!in_array(old('status', $order->status), ['Pending', 'Processing', 'Completed', 'Partial', 'Canceled']))

                                        <option value="{{ old('status', $order->status) }}" selected>

                                            {{ old('status', $order->status) }}

                                        </option>

                                    @endif

                                </select>

                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="total" class="form-label">Thanh toán</label>
                                <input type="text" class="form-control" id="total" name="total"
                                    value="{{ number_format($order->total) }}đ" readonly>
                            </div>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Lưu
                            </button>
                            <a href="{{ route('admin.data.orders') }}" class="btn btn-secondary-gradient">
                                <i class="bx bx-arrow-back"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
