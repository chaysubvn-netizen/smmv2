@extends('admin.layouts.app')
@section('title', 'Quản lý vé hỗ trợ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Chi tiết vé hỗ trợ</h4>
                </div>
                <div class="card-body" id="chatBox">
                    @foreach ($ticket->messages as $msg)
                        @if ($msg->type == 'user')
                            <!-- Tin nhắn người khác (bên trái) -->
                            <div class="message auto">
                                <div class="message-content fw-bold text-gray-600 fs-6">
                                    {{ $msg->message }}
                                </div>
                                <div class="message-time text-primary fw-bold ">
                                    {{ $msg->created_at->format('H:i A') }}
                                </div>
                            </div>
                        @endif
                        <!-- Tin nhắn của bạn (bên phải) -->
                        @if ($msg->type == 'admin')
                            <div class="message right">
                                <div class="message-content fw-bold text-gray-700 fs-6">
                                    {{ $msg->message }}
                                </div>
                                <div class="message-time text-white ">
                                    {{ $msg->created_at->format('H:i A') }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" data-ajax="true" class="input-group" novalidate>
                        @csrf
                        <input type="text" class="form-control" name="message" placeholder="Nhập tin nhắn...">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-send"></i>
                            Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('styles')
    <style>
        /* CSS cho bảng chat */
        .chat-box {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .message {
            margin-bottom: 15px;
            max-width: 70%;
            padding: 8px 12px;
            border-radius: 12px;
            position: relative;
            word-wrap: break-word;
        }

        .message.left {
            background-color: #e9ecef;
            margin-right: auto;
            border-top-left-radius: 0;
        }

        .message.right {
            background-color: #007bff;
            color: white;
            margin-left: auto;
            border-top-right-radius: 0;
        }

        .message-content {
            font-size: 1em;
            margin-bottom: 5px;
        }

        .message-time {
            text-align: right;
            font-size: 0.8em;
        }

        /* Custom scrollbar */
        .chat-box::-webkit-scrollbar {
            width: 8px;
        }

        .chat-box::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-box::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .chat-box::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Tự động cuộn xuống cuối khi có tin nhắn mới
            $('#chatBox').animate({
                scrollTop: $('#chatBox')[0].scrollHeight
            }, 1000);
        });
    </script>
@endsection
