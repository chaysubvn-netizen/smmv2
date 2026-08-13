@extends('admin.layouts.app')
@section('title', 'Hỗ trợ khách hàng')
@section('content')
<style>
    :root {
        --app-bg: #151521;
        --app-surface: #1e1e2d;
        --app-border: #2b2b40;
        --text-primary: #ffffff;
        --text-secondary: #b5b5c3; /* Lighter grey */
        --color-primary: #3699ff;
        --color-success: #1bc5bd;
        --color-warning: #ffa800;
        --color-danger: #f64e60;
        --msg-admin-bg: #3699ff;
        --msg-user-bg: #2b2b40;
    }

    /* Reset/Base Overrides for this page */
    .card { background: transparent !important; box-shadow: none !important; margin-bottom: 0 !important; }
    .main-content { padding-bottom: 0 !important; } /* Try to maximize height */

    /* Main Layout */
    .chat-wrapper {
        background: var(--app-bg);
        border-radius: 12px;
        box-shadow: 0 0 20px 0 rgba(0,0,0,0.3);
        height: calc(100vh - 110px); /* Adjust based on header height */
        display: flex;
        overflow: hidden;
        border: 1px solid var(--app-border);
        font-family: 'Inter', Helvetica, sans-serif; /* Try to use modern font if available */
        color: var(--text-primary);
    }
    
    /* Aggressively force text color on all children to prevent inheritance issues */
    .chat-wrapper * { color: inherit; }
    .chat-wrapper .text-muted, .chat-wrapper .text-secondary { color: var(--text-secondary) !important; }
    .chat-wrapper .text-white, .chat-wrapper .text-light, .chat-wrapper .text-dark { color: #ffffff !important; }

    /* Fix text contrast */
    .chat-wrapper input, .chat-wrapper textarea { color: #ffffff !important; background: transparent; }
    .chat-wrapper input::placeholder, .chat-wrapper textarea::placeholder { color: #6c7293 !important; }
    
    /* Buttons */
    .btn-outline-secondary { color: #b5b5c3; border-color: #3f3f55; }
    .btn-outline-secondary:hover { background: #3f3f55; color: #fff; }

    /* ================= LEFT SIDEBAR ================= */
    .sidebar-left {
        width: 320px;
        background: var(--app-surface);
        border-right: 1px solid var(--app-border);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    
    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid var(--app-border);
    }
    .search-box {
        position: relative;
    }
    .search-box input {
        background: var(--app-bg);
        border: 1px solid var(--app-border);
        color: var(--text-primary);
        padding: 10px 15px 10px 40px;
        border-radius: 8px;
        width: 100%;
        font-size: 13px;
    }
    .search-box input:focus { outline: none; border-color: var(--color-primary); }
    .search-box i {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .ticket-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }
    .ticket-item {
        display: flex;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 5px;
        border: 1px solid transparent;
        text-decoration: none;
        color: inherit;
    }
    .ticket-item:hover {
        background: var(--app-bg);
    }
    .ticket-item.active {
        background: rgba(54, 153, 255, 0.1); /* Light blue tint */
        border-color: rgba(54, 153, 255, 0.2);
    }
    .ticket-item.active .ticket-name { color: #ffffff !important; }
    .ticket-item.active .ticket-preview { color: #e4e6ef !important; }
    
    .avatar-wrapper {
        position: relative;
        margin-right: 12px;
    }
    .avatar-circle {
        width: 45px; height: 45px;
        border-radius: 10px; /* Squircle */
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #fff;
        background: linear-gradient(135deg, #a8ff78 0%, #78ffd6 100%); /* Default Gradient */
        font-size: 16px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .ticket-item.active .avatar-circle {
        box-shadow: 0 0 0 2px var(--color-primary); /* Active ring */
    }

    .ticket-info { flex: 1; overflow: hidden; }
    .ticket-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
    .ticket-name { font-weight: 600; color: var(--text-primary); font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ticket-time { font-size: 11px; color: var(--text-secondary); }
    .ticket-bottom { display: flex; justify-content: space-between; align-items: center; }
    .ticket-preview { font-size: 12px; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
    
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .status-pending { background: var(--color-warning); box-shadow: 0 0 5px var(--color-warning); }
    .status-processing { background: var(--color-primary); box-shadow: 0 0 5px var(--color-primary); }
    .status-completed { background: var(--color-success); }
    .status-closed { background: var(--text-secondary); }

    /* ================= MAIN CHAT ================= */
    .main-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--app-bg); /* Darker background for chat area */
        position: relative;
    }
    
    .chat-header {
        height: 70px;
        background: var(--app-surface);
        border-bottom: 1px solid var(--app-border);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 25px;
        flex-shrink: 0;
    }
    .header-user h5 { margin: 0; font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .header-status { font-size: 12px; color: var(--text-secondary); display: flex; align-items: center; gap: 5px; margin-top: 2px; }

    .chat-content {
        flex: 1;
        overflow-y: auto;
        padding: 25px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .message-group {
        display: flex;
        margin-bottom: 5px;
    }
    .message-group.right { flex-direction: row-reverse; }
    
    .message-avatar {
        width: 35px; height: 35px;
        border-radius: 50%;
        background: #333;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 12px; font-weight: bold;
        margin-top: auto; /* Align bottom */
    }
    .message-group.left .message-avatar { margin-right: 10px; }
    .message-group.right .message-avatar { margin-left: 10px; display: none; /* Hide admin avatar usually */ }

    .message-bubble {
        max-width: 65%;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
    }
    /* User Bubble */
    .message-group.left .message-bubble {
        background: var(--msg-user-bg);
        color: var(--text-primary);
        border-bottom-left-radius: 2px;
    }
    /* Admin Bubble */
    .message-group.right .message-bubble {
        background: linear-gradient(135deg, #3699ff 0%, #007bff 100%);
        color: #fff;
        border-bottom-right-radius: 2px;
        box-shadow: 0 4px 15px rgba(54, 153, 255, 0.3);
    }
    
    .message-time {
        font-size: 10px;
        color: var(--text-secondary);
        margin-top: 5px;
        opacity: 0.7;
    }
    .message-group.right .message-time { text-align: right; }

    .chat-footer {
        padding: 20px;
        background: var(--app-surface);
        border-top: 1px solid var(--app-border);
    }
    .input-wrapper {
        background: var(--app-bg);
        border: 1px solid var(--app-border);
        border-radius: 25px; /* Pill shape */
        padding: 5px 5px 5px 20px;
        display: flex; align-items: center;
        transition: border-color 0.2s;
    }
    .input-wrapper:focus-within { border-color: var(--color-primary); }
    .input-wrapper textarea {
        background: transparent; border: none;
        color: var(--text-primary);
        resize: none; width: 100%;
        height: 24px; max-height: 100px;
        padding: 10px 0;
        font-size: 14px;
        line-height: 24px;
    }
    .input-wrapper textarea:focus { outline: none; }
    .btn-send {
        width: 40px; height: 40px;
        border-radius: 50%;
        border: none;
        background: var(--color-primary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s;
        box-shadow: 0 4px 10px rgba(54, 153, 255, 0.4);
    }
    .btn-send:hover { transform: scale(1.05); }

    /* ================= RIGHT SIDEBAR ================= */
    .sidebar-right {
        width: 280px;
        background: var(--app-surface);
        border-left: 1px solid var(--app-border);
        flex-shrink: 0;
        display: flex; flex-direction: column;
        overflow-y: auto;
    }
    .info-section { padding: 25px 20px; border-bottom: 1px solid var(--app-border); }
    .info-profile { text-center: true; }
    .profile-avatar-lg {
        width: 80px; height: 80px;
        margin: 0 auto 15px;
        border-radius: 20px;
        background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%);
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; color: #fff; font-weight: bold;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .info-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 10px; font-weight: 600; }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; color: var(--text-primary); }
    .info-row span:first-child { color: var(--text-secondary); }
    .info-row span:last-child { font-weight: 600; }

    /* Actions */
    .action-btn {
        display: block; width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid var(--app-border);
        background: var(--app-bg);
        color: var(--text-primary);
        text-align: center;
        margin-top: 10px;
        transition: 0.2s;
        font-size: 13px; font-weight: 600;
        text-decoration: none;
    }
    .action-btn:hover { background: var(--color-primary); border-color: var(--color-primary); color: #fff; }

    /* Empty State */
    .empty-state {
        height: 100%; display: flex; flex-direction: column;
        align-items: center; justify-content: center; color: var(--text-secondary);
    }
    .empty-icon { font-size: 64px; margin-bottom: 20px; opacity: 0.2; }

    /* ================= MOBILE NOTICE ================= */
    .mobile-notice { display: none; }

    @media (max-width: 991px) {
        .chat-wrapper { display: none !important; }
        .mobile-notice {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 150px);
            text-align: center;
            padding: 20px;
            background: var(--app-bg);
            border-radius: 12px;
            border: 1px solid var(--app-border);
        }
        .mobile-icon-box {
            width: 80px; height: 80px;
            background: rgba(54, 153, 255, 0.1);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 25px;
        }
        .mobile-icon-box i { font-size: 40px; color: var(--color-primary); }
        .mobile-title { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .mobile-desc { font-size: 14px; color: var(--text-secondary); margin-bottom: 30px; max-width: 300px; line-height: 1.6; }
        .btn-to-tickets {
            background: var(--color-primary);
            color: #fff;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(54, 153, 255, 0.4);
            transition: transform 0.2s;
        }
        .btn-to-tickets:hover { transform: translateY(-2px); color: #fff; }
    }
</style>

<div class="row">
    <div class="col-12 p-0"> <!-- Full width wrapper -->
        
        <!-- Mobile Notice -->
        <div class="mobile-notice">
            <div class="mobile-icon-box">
                <i class="bx bx-mobile-alt"></i>
            </div>
            <h4 class="mobile-title">Trang Messages hiện chưa hỗ trợ trên thiết bị di động.</h4>
            <p class="mobile-desc">Vui lòng truy cập trang Tickets để theo dõi và xử lý yêu cầu một cách tốt nhất.</p>
            <a href="{{ route('admin.tickets.index') }}" class="btn-to-tickets">
                <i class="bx bx-arrow-back mr-2"></i> Đi tới trang Tickets
            </a>
        </div>

        <div class="chat-wrapper">
            
            <!-- Left: Ticket List -->
            <div class="sidebar-left">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="m-0 text-white font-weight-bold">Messages</h4>
                        <span class="badge badge-primary">{{ $tickets->count() }}</span>
                    </div>
                    <div class="search-box">
                         <i class="bx bx-search"></i>
                         <input type="text" placeholder="Tìm kiếm tickets...">
                    </div>
                </div>
                <div class="ticket-list">
                    @foreach($tickets as $ticket)
                        @php
                            // Random gradient for aesthetics logic
                            $gradients = [
                                'linear-gradient(135deg, #fad961 0%, #f76b1c 100%)',
                                'linear-gradient(135deg, #00c6fb 0%, #005bea 100%)',
                                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                            ];
                            $gradIdx = $ticket->id % 4; // Use ID to pick gradient
                        @endphp
                        <a href="{{ route('admin.tickets.messages', ['id' => $ticket->id]) }}" 
                           class="ticket-item {{ ($currentTicket && $currentTicket->id == $ticket->id) ? 'active' : '' }}">
                            <div class="avatar-wrapper">
                                <div class="avatar-circle" style="background: {{ $gradients[$gradIdx] }}">
                                    {{ strtoupper(substr($ticket->user->username ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            <div class="ticket-info">
                                <div class="ticket-top">
                                    <span class="ticket-name">{{ $ticket->user->username ?? 'Unknown' }}</span>
                                    <span class="ticket-time">{{ $ticket->updated_at->format('H:i') }}</span>
                                </div>
                                <div class="ticket-bottom">
                                    <span class="ticket-preview">
                                        {{ $ticket->title }}
                                    </span>
                                    @if($ticket->status == 'pending') <span class="status-dot status-pending" title="Chờ xử lý"></span>
                                    @elseif($ticket->status == 'processing') <span class="status-dot status-processing" title="Đang xử lý"></span>
                                    @elseif($ticket->status == 'completed') <span class="status-dot status-completed" title="Hoàn thành"></span>
                                    @else <span class="status-dot status-closed" title="Đóng"></span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Center: Chat -->
            <div class="main-chat">
                @if($currentTicket)
                    <div class="chat-header">
                        <div class="header-user">
                            <h5>{{ $currentTicket->user->username ?? 'Unknown' }}</h5>
                            <div class="header-status">
                                @if($currentTicket->status == 'pending') <span class="text-warning">● Waiting for support</span>
                                @elseif($currentTicket->status == 'processing') <span class="text-primary">● Processing</span>
                                @elseif($currentTicket->status == 'completed') <span class="text-success">● Completed</span>
                                @else <span class="text-secondary">● Closed</span>
                                @endif
                                <span class="ml-2">| Ticket #{{ $currentTicket->id }}</span>
                            </div>
                        </div>
                        <div>
                             <button class="btn btn-icon btn-sm btn-ghost-secondary"><i class="bx bx-dots-horizontal-rounded fs-4 text-white"></i></button>
                        </div>
                    </div>

                    <div class="chat-content" id="chat-box">
                        @foreach($currentTicket->messages as $message)
                            <div class="message-group {{ $message->type == 'user' ? 'left' : 'right' }}">
                                <div class="message-avatar">
                                    @if($message->type == 'user')
                                        {{ strtoupper(substr($currentTicket->user->username ?? 'U', 0, 1)) }}
                                    @else
                                        AD
                                    @endif
                                </div>
                                <div>
                                    <div class="message-bubble">
                                        {!! nl2br(e($message->message)) !!}
                                    </div>
                                    <div class="message-time">
                                        {{ $message->created_at->format('H:i d/m') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="chat-footer">
                        <form id="reply-form" action="{{ route('admin.tickets.reply', $currentTicket->id) }}" method="POST">
                            @csrf
                            <div class="input-wrapper">
                                <textarea name="message" id="msg-input" placeholder="Nhập tin nhắn... (Enter để gửi)" required></textarea>
                                <button type="submit" id="btn-reply" class="btn-send"><i class="bx bxs-send"></i></button>
                            </div>
                            <div class="d-flex mt-3 gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success border-0 change-status" data-status="completed"><i class="bx bx-check"></i> Hoàn thành</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary border-0 change-status" data-status="closed"><i class="bx bx-lock"></i> Đóng Ticket</button>
                            </div>
                        </form>
                    </div>

                @else
                    <div class="empty-state">
                        <i class="bx bx-message-square-dots empty-icon"></i>
                        <h3>Chào mừng trở lại!</h3>
                        <p>Chọn một cuộc hội thoại để bắt đầu hỗ trợ.</p>
                    </div>
                @endif
            </div>

            <!-- Right: Info -->
            <div class="sidebar-right">
                @if($currentTicket && $currentTicket->user)
                    <div class="info-section">
                        @php
                            $gradIdx = $currentTicket->id % 4; // Match listing gradient
                             $gradients = [
                                'linear-gradient(135deg, #fad961 0%, #f76b1c 100%)',
                                'linear-gradient(135deg, #00c6fb 0%, #005bea 100%)',
                                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                            ];
                        @endphp
                        <div class="profile-avatar-lg" style="background: {{ $gradients[$gradIdx] }}">
                             {{ strtoupper(substr($currentTicket->user->username ?? 'U', 0, 1)) }}
                        </div>
                        <h5 class="text-center text-white mb-1">{{ $currentTicket->user->username }}</h5>
                        <p class="text-center text-secondary fs-12">{{ $currentTicket->user->email }}</p>
                        
                        <a href="{{ route('admin.users.edit', $currentTicket->user->id) }}" target="_blank" class="action-btn mt-3">
                            Xem trang cá nhân
                        </a>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-label">Ví & Giao dịch</div>
                        <div class="info-row">
                            <span>Số dư</span>
                            <span class="text-success">{{ number_format($currentTicket->user->balance) }} đ</span>
                        </div>
                        <div class="info-row">
                            <span>Tổng nạp</span>
                            <span class="text-primary">{{ number_format($currentTicket->user->total_deposit) }} đ</span>
                        </div>
                        <div class="info-row">
                            <span>Tổng đơn</span>
                            <span>{{ $currentTicket->user->orders_count ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="info-section">
                         <div class="info-label">Thông tin Ticket</div>
                         <div class="info-row">
                            <span>ID</span>
                            <span>#{{ $currentTicket->id }}</span>
                        </div>
                        <div class="info-row">
                            <span>Ngày tạo</span>
                            <span>{{ $currentTicket->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span>Chủ đề</span>
                            <span class="text-break text-end" style="width: 60%">{{ $currentTicket->title }}</span>
                        </div>
                         @if($currentTicket->order_id)
                         <div class="info-row">
                             <span>Order ID</span>
                             <span class="text-warning font-weight-bold">#{{ $currentTicket->order_id }}</span>
                         </div>
                         @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var chatBox = document.getElementById('chat-box');
        
        // Scroll to bottom initially
        if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;

        // Auto resize textarea
        $('#msg-input').on('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if(!this.value) this.style.height = '24px'; // reset
        });

        // Enter to send
        $('#msg-input').on('keydown', function(event) {
            if (event.keyCode == 13 && !event.shiftKey) {
                event.preventDefault();
                $('#reply-form').submit();
            }
        });

        // Status Actions
         $('.change-status').click(function() {
             let status = $(this).data('status');
             if(!confirm('Xác nhận đổi trạng thái?')) return;
             
             $.ajax({
                url: "{{ route('admin.tickets.update', $currentTicket->id ?? 0) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status,
                    title: "{{ $currentTicket->title ?? '' }}",
                    type: "{{ $currentTicket->type ?? '' }}"
                },
                success: function(res) {
                    if(res.success) location.reload();
                    else alert(res.message);
                }
             });
        });

        // ================= AUTO LOAD & REPLY LOGIC =================
        @if($currentTicket)
            var lastMessageId = {{ $currentTicket->messages->count() > 0 ? $currentTicket->messages->max('id') : 0 }};
            var fetchUrl = "{{ route('admin.tickets.fetch-messages', $currentTicket->id) }}";
            var userInitial = "{{ strtoupper(substr($currentTicket->user->username ?? 'U', 0, 1)) }}";
            var currentUserAvatar = `
                 <div class="message-avatar">
                    ${userInitial}
                 </div>
            `;

            // Handle Reply
            $('#reply-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let msgText = $('#msg-input').val();
                if(!msgText.trim()) return;

                // Create Temp ID
                let tempId = 'temp-' + Date.now();
                
                // Optimistic Append
                let fakeMsg = `
                    <div class="message-group right" id="${tempId}">
                        <div class="message-avatar">AD</div>
                        <div>
                            <div class="message-bubble" style="opacity: 0.7;">
                                ${msgText.replace(/\n/g, "<br>")}
                            </div>
                            <div class="message-time">Sending...</div>
                        </div>
                    </div>
                `;
                $('#chat-box').append(fakeMsg);
                if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
                
                $('#msg-input').val('').css('height', '24px');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        if(res.success) {
                            // On success, we get the real message data (assuming controller returns 'data')
                            // If controller returns data.id, we update lastMessageId
                            if(res.data && res.data.id) {
                                if(res.data.id > lastMessageId) lastMessageId = res.data.id;
                                
                                // Update the temp message to real message
                                let realMsg = $(`#${tempId}`);
                                realMsg.attr('id', 'msg-' + res.data.id); // Set real ID
                                realMsg.attr('data-id', res.data.id);
                                realMsg.find('.message-bubble').css('opacity', '1');
                                realMsg.find('.message-time').text('Just now');
                            } else {
                                // Fallback if no data returned: remove temp and let fetcher get it
                                $(`#${tempId}`).remove();
                                loadNewMessages(); 
                            }
                        } else {
                            alert(res.message);
                            $(`#${tempId}`).remove();
                        }
                    },
                    error: function() {
                        alert('Lỗi gửi tin nhắn');
                        $(`#${tempId}`).remove();
                    }
                });
            });

            function loadNewMessages() {
                $.ajax({
                    url: fetchUrl,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        last_id: lastMessageId
                    },
                    success: function(res) {
                        if(res.success && res.messages.length > 0) {
                            let shouldScroll = (chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50);

                            res.messages.forEach(function(msg) {
                                // Check duplicate
                                if($('#chat-box').find(`[data-id="${msg.id}"]`).length > 0) {
                                    return; // Skip existing
                                }

                                // Update last ID
                                if(msg.id > lastMessageId) lastMessageId = msg.id;

                                // Time helper
                                let timeStr = 'Just now';
                                try {
                                    let date = new Date(msg.created_at);
                                    timeStr = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0') + ' ' + date.getDate() + '/' + (date.getMonth()+1);
                                } catch(e){}

                                let html = '';
                                if(msg.type === 'user') {
                                    html = `
                                        <div class="message-group left" data-id="${msg.id}">
                                            ${currentUserAvatar}
                                            <div>
                                                <div class="message-bubble">
                                                    ${msg.message ? msg.message.replace(/\n/g, "<br>") : ''}
                                                </div>
                                                <div class="message-time">${timeStr}</div>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    html = `
                                        <div class="message-group right" data-id="${msg.id}">
                                            <div class="message-avatar">AD</div>
                                            <div>
                                                <div class="message-bubble">
                                                    ${msg.message ? msg.message.replace(/\n/g, "<br>") : ''}
                                                </div>
                                                <div class="message-time text-end">${timeStr}</div>
                                            </div>
                                        </div>
                                    `;
                                }
                                $('#chat-box').append(html);
                            });

                             // Scroll to bottom if we were at bottom OR it's a new message
                             // Force scroll for new messages often desired in support chat
                            if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    }
                });
            }

            // Poll every 1 second
            setInterval(loadNewMessages, 1000);
        @endif
    });
</script>
@endsection
@endsection
