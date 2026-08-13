@extends('client.layouts.master')
@section('title', 'Chi tiết yêu cầu hỗ trợ')
@section('content')
    @include('client.layouts.breadcrumb', ['title' => 'Chi tiết yêu cầu hỗ trợ #' . $ticket->id])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="info">
                        <h6 class="text-md mb-0">{{ $ticket->title }}</h6>
                    </div>
                </div>
                <div class="card-body" id="kt_chat_messenger_body">
                    <!--begin::Messages-->
                    <div class="scroll-y me-n5 pe-5 h-300px h-lg-auto chat-message-list"  data-kt-scroll="true"
                        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_app_toolbar, #kt_toolbar, #kt_footer, #kt_app_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer"
                        data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_messenger_body"
                        data-kt-scroll-offset="5px" style="">
                        @foreach ($ticket->messages as $msg)
                            @if ($msg->type == 'admin')
                                <div class="d-flex justify-content-start mb-10 ">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column align-items-start">
                                        <!--begin::User-->
                                        <div class="d-flex align-items-center mb-2">
                                            <!--begin::Avatar-->
                                            <div class="symbol  symbol-35px symbol-circle "><img alt="Pic"
                                                    src="/assets/media/avatars/300-25.jpg"></div><!--end::Avatar-->
                                            <!--begin::Details-->
                                            <div class="ms-3">
                                                <a href="#"
                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">ADMIN</a>
                                                <span
                                                    class="text-muted fs-7 mb-1">{{ $msg->created_at->format('H:i A') }}</span>
                                            </div>
                                            <!--end::Details-->

                                        </div>
                                        <!--end::User-->

                                        <!--begin::Text-->
                                        <div class="p-5 rounded bg-light-info text-gray-900 fw-semibold mw-lg-400px text-start"
                                            data-kt-element="message-text">
                                            {{ $msg->message }} </div>
                                        <!--end::Text-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                            @else
                                <div class="d-flex justify-content-end mb-10 ">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column align-items-end">
                                        <!--begin::User-->
                                        <div class="d-flex align-items-center mb-2">
                                            <!--begin::Details-->
                                            <div class="me-3">
                                                <span
                                                    class="text-muted fs-7 mb-1">{{ $msg->created_at->format('H:i A') }}</span>
                                                <a href="#"
                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">Bạn</a>
                                            </div>
                                            <!--end::Details-->

                                            <!--begin::Avatar-->
                                            <div class="symbol  symbol-35px symbol-circle "><img alt="Pic"
                                                    src="/assets/media/avatars/300-1.jpg"></div><!--end::Avatar-->
                                        </div>
                                        <!--end::User-->

                                        <!--begin::Text-->
                                        <div class="p-5 rounded bg-light-primary text-gray-900 fw-semibold mw-lg-400px text-end"
                                            data-kt-element="message-text">
                                            {{ $msg->message }} </div>
                                        <!--end::Text-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!--end::Messages-->
                </div>
                <div class="card-footer pt-4" id="kt_chat_messenger_footer">
                    <!--begin::Input-->
                    <form action="{{ route('ticket.reply', $ticket->id) }}" method="POST" bvq-request>

                        <textarea class="form-control form-control-solid mb-3" rows="1" data-kt-element="input" name="message"
                            placeholder="Viết tin nhắn"></textarea>
                        <!--end::Input-->

                        <!--begin:Toolbar-->
                        <div class="d-flex flex-stack">
                            <!--begin::Actions-->
                            {{-- <div class="d-flex align-items-center me-2">
                            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" aria-label="Coming soon" data-bs-original-title="Coming soon" data-kt-initialized="1">
                                <i class="ki-duotone ki-paper-clip fs-3"></i>                </button>
                            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" aria-label="Coming soon" data-bs-original-title="Coming soon" data-kt-initialized="1">
                                <i class="ki-duotone ki-exit-up fs-3"><span class="path1"></span><span class="path2"></span></i>                </button>
                        </div> --}}
                            <!--end::Actions-->

                            <!--begin::Send-->
                            <button class="btn btn-primary" type="submit">Gửi tin nhắn</button>
                            <!--end::Send-->
                        </div>
                        <!--end::Toolbar-->
                    </form>
                </div>
                {{-- <div class="card-body">
                    @foreach ($ticket->messages as $msg)
                        @if ($msg->type == 'admin')
                            <div class="chat-single-message left">
                                <div class="chat-message-content">
                                    <p class="mb-3">{{ $msg->message }}</p>
                                    <p class="chat-time mb-0">
                                        <span>{{ $msg->created_at->format('H:i A') }}</span>
                                        <span class="">- Admin</span>
                                    </p>
                                </div>
                            </div><!-- chat-single-message end -->
                        @else
                            <div class="chat-single-message right">
                                <div class="chat-message-content">
                                    <p class="mb-3">{{ $msg->message }}</p>
                                    <p class="chat-time mb-0">
                                        <span>{{ $msg->created_at->format('H:i A') }}</span>
                                        <span class="">- Bạn</span>
                                    </p>
                                </div>
                            </div><!-- chat-single-message end -->
                        @endif
                    @endforeach
                </div> --}}
                {{-- <form class="chat-message-box" action="{{ route('ticket.reply', $ticket->id) }}" method="POST"
                    bvq-request reload="true" novalidate="true">
                    <input type="text" name="message" placeholder="Viết tin nhắn ">
                    <div class="chat-message-box-action">
                        <button type="submit"
                            class="btn btn-sm btn-primary-600 radius-8 d-inline-flex align-items-center gap-1">
                            <span class="d-none d-sm-inline-block">{{ __('Gửi') }}</span>
                            <iconify-icon icon="f7:paperplane"></iconify-icon>
                        </button>
                    </div>
                </form> --}}
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.chat-message-list').animate({
                scrollTop: $('.chat-message-list')[0].scrollHeight
            }, 1000);
        });
    </script>
@endsection
