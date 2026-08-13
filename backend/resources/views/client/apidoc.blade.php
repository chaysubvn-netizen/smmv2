@extends('client.layouts.master')
@section('title', __('API Documentation'))

@section('content')
@include('client.layouts.breadcrumb', ['title' => __('API Documentation')])

<style>
    /* Dark Premium API Doc Theme */
    :root {
        --api-dark-bg: #0d0d12;
        --api-sidebar-bg: #15151b;
        --api-card-bg: #1a1a22;
        --api-primary: #0080ff;
        --api-border: #2a2a35;
        --api-text-main: #e1e1e6;
        --api-text-muted: #8d8d99;
        
        --api-code-bg: #09090b;
        --api-code-header: #121216;
    }

    /* Override body for dark feel if needed, but better to wrap in container */
    .doc-wrapper {
        background-color: var(--api-dark-bg);
        border-radius: 20px;
        padding: 2rem;
        color: var(--api-text-main);
        font-family: 'Inter', sans-serif;
    }

    .doc-container {
        display: flex;
        gap: 2rem;
    }

    .doc-sidebar {
        width: 300px;
        flex-shrink: 0;
        background: var(--api-sidebar-bg);
        border-radius: 20px;
        padding: 20px;
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .doc-content {
        flex: 1;
        min-width: 0;
    }

    @media (max-width: 1200px) {
        .doc-container { flex-direction: column; }
        .doc-sidebar { width: 100%; position: static; }
    }

    .sidebar-title {
        color: #fff;
        font-weight: 800;
        font-size: 1.2rem;
        margin-bottom: 2rem;
        padding-left: 10px;
    }

    .nav-doc .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        border-radius: 12px;
        color: var(--api-text-muted);
        font-weight: 600;
        transition: all 0.3s ease;
        margin-bottom: 8px;
        border: none;
    }

    .nav-doc .nav-link i {
        margin-right: 12px;
        font-size: 1.25rem;
        opacity: 0.7;
    }

    .nav-doc .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .nav-doc .nav-link.active {
        background: var(--api-primary);
        color: #fff;
        box-shadow: 0 10px 20px rgba(0, 128, 255, 0.2);
    }

    .nav-doc .nav-link.active i { opacity: 1; }

    /* Content Cards */
    .doc-card {
        background: var(--api-card-bg);
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 2rem;
        border: 1px solid var(--api-border);
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 20px;
    }

    .section-desc {
        color: var(--api-text-muted);
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    /* API Key Box */
    .api-key-container {
        background: rgba(0, 128, 255, 0.03);
        border: 2px dashed rgba(0, 128, 255, 0.3);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 40px;
    }

    .api-key-info h4 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .api-key-info p {
        color: var(--api-text-muted);
        font-size: 0.9rem;
        margin: 0;
    }

    .api-key-value-box {
        background: #09090b;
        border-radius: 12px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid var(--api-border);
    }

    .api-key-value-box code {
        color: var(--api-primary);
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .btn-copy-key {
        background: var(--api-primary);
        color: #fff;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-copy-key:hover { transform: scale(1.1); background: #0070e0; }

    /* Endpoint Box */
    .endpoint-box {
        background: #09090b;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid var(--api-border);
        margin-bottom: 30px;
    }

    .method-tag {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
        background: rgba(114, 57, 234, 0.1);
        color: #7239ea;
        text-transform: uppercase;
    }

    .endpoint-url {
        color: var(--api-text-muted);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.95rem;
    }

    /* Params Table */
    .table-params { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .table-params th {
        text-align: left;
        padding: 15px;
        color: var(--api-text-muted);
        font-size: 0.8rem;
        text-transform: uppercase;
        border-bottom: 1px solid var(--api-border);
    }
    .table-params td {
        padding: 20px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }

    .p-name { color: #f1416c; font-weight: 700; font-family: 'JetBrains Mono', monospace; }
    .p-type { color: var(--api-text-muted); font-size: 0.75rem; margin-left: 8px; }

    /* Code Block */
    .code-container {
        background: var(--api-code-bg);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--api-border);
        margin-top: 30px;
    }

    .code-header {
        background: var(--api-code-header);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--api-border);
    }

    .code-tabs { display: flex; gap: 10px; }
    .c-tab {
        background: transparent;
        border: none;
        color: var(--api-text-muted);
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        padding: 6px 12px;
        border-radius: 6px;
    }
    .c-tab.active { background: rgba(255,255,255,0.05); color: #fff; }

    .code-body {
        padding: 24px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        line-height: 1.6;
        color: #abb2bf;
        overflow-x: auto;
    }

    .keyword { color: #c678dd; }
    .string { color: #98c379; }
    .var { color: #61afef; }
</style>

<div class="doc-wrapper mt-5">
    <div class="doc-container">
        <!-- Sidebar -->
        <div class="doc-sidebar shadow-lg">
            <div class="sidebar-title">Menu</div>
            <div class="nav nav-doc flex-column">
                <a href="#intro" class="nav-link active" data-target="intro">
                    <i class="ki-duotone ki-element-11"></i> {{ __('Giới thiệu') }}
                </a>
                <a href="#services" class="nav-link" data-target="services">
                    <i class="ki-duotone ki-category"></i> {{ __('Danh sách dịch vụ') }}
                </a>
                <a href="#add_order" class="nav-link" data-target="add_order">
                    <i class="ki-duotone ki-plus-circle"></i> {{ __('Tạo đơn hàng') }}
                </a>
                <a href="#status" class="nav-link" data-target="status">
                    <i class="ki-duotone ki-search-list"></i> {{ __('Trạng thái đơn hàng') }}
                </a>
                <a href="#balance" class="nav-link" data-target="balance">
                    <i class="ki-duotone ki-wallet"></i> {{ __('Số dư tài khoản') }}
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="doc-content">
            <!-- Intro Section -->
            <section id="intro" class="doc-card">
                <h1 class="section-title">Kết nối API</h1>
                <p class="section-desc">
                    Chào mừng bạn đến với hệ thống API của chúng tôi. Bạn có thể dễ dàng tích hợp dịch vụ của mình với hệ thống thông qua các giao thức HTTP POST chuẩn.
                </p>

                <div class="api-key-container">
                    <div class="api-key-info">
                        <h4>API Key của bạn</h4>
                        <p>Giữ bí mật mã này để bảo mật tài khoản của bạn.</p>
                    </div>
                    <div class="api-key-value-box">
                        <code>{{ Auth::user()->api_key ?? '********************************' }}</code>
                        <button class="btn-copy-key" onclick="copyToClipboard('{{ Auth::user()->api_key }}')">
                            <i class="ki-duotone ki-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="endpoint-box">
                    <span class="method-tag">POST</span>
                    <span class="endpoint-url">{{ url('/api/v2') }}</span>
                </div>
            </section>

            <!-- Services Section -->
            <section id="services" class="doc-card">
                <h2 class="fw-bolder text-white mb-6">Danh sách dịch vụ</h2>
                <div class="table-responsive">
                    <table class="table-params">
                        <thead>
                            <tr>
                                <th>Tham số</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="p-name">key</span><span class="p-type">String</span></td>
                                <td>Mã API Key cá nhân của bạn</td>
                            </tr>
                            <tr>
                                <td><span class="p-name">action</span><span class="p-type">String</span></td>
                                <td>Hành động: <code class="text-primary">services</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="code-container">
                    <div class="code-header">
                        <div class="code-tabs">
                            <button class="c-tab active">PHP</button>
                            <button class="c-tab">cURL</button>
                        </div>
                    </div>
                    <div class="code-body">
                        <span class="var">$api_url</span> = <span class="string">"{{ url('/api/v2') }}"</span>;<br>
                        <span class="var">$post_data</span> = [<br>
                        &nbsp;&nbsp;<span class="string">'key'</span> => <span class="string">'YOUR_API_KEY'</span>,<br>
                        &nbsp;&nbsp;<span class="string">'action'</span> => <span class="string">'services'</span><br>
                        ];<br>
                        <span class="var">$ch</span> = <span class="keyword">curl_init</span>(<span class="var">$api_url</span>);<br>
                        <span class="keyword">curl_setopt</span>(<span class="var">$ch</span>, CURLOPT_POSTFIELDS, <span class="var">$post_data</span>);<br>
                        ...
                    </div>
                </div>
            </section>

            <!-- Add Order Section -->
            <section id="add_order" class="doc-card">
                <h2 class="fw-bolder text-white mb-6">Tạo đơn hàng</h2>
                <div class="table-responsive">
                    <table class="table-params">
                        <thead>
                            <tr>
                                <th>Tham số</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="p-name">key</span><span class="p-type">String</span></td>
                                <td>Mã API Key cá nhân của bạn</td>
                            </tr>
                            <tr>
                                <td><span class="p-name">action</span><span class="p-type">String</span></td>
                                <td>Hành động: <code class="text-primary">add</code></td>
                            </tr>
                            <tr>
                                <td><span class="p-name">service</span><span class="p-type">Int</span></td>
                                <td>ID của gói dịch vụ</td>
                            </tr>
                            <tr>
                                <td><span class="p-name">link</span><span class="p-type">String</span></td>
                                <td>Đường dẫn hoặc ID đối tượng</td>
                            </tr>
                            <tr>
                                <td><span class="p-name">quantity</span><span class="p-type">Int</span></td>
                                <td>Số lượng cần mua</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Balance Section -->
            <section id="balance" class="doc-card">
                <h2 class="fw-bolder text-white mb-6">Số dư tài khoản</h2>
                <div class="table-responsive">
                    <table class="table-params">
                        <thead>
                            <tr>
                                <th>Tham số</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="p-name">key</span><span class="p-type">String</span></td>
                                <td>Mã API Key cá nhân của bạn</td>
                            </tr>
                            <tr>
                                <td><span class="p-name">action</span><span class="p-type">String</span></td>
                                <td>Hành động: <code class="text-primary">balance</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-8">
                    <h5 class="fw-bold text-white mb-4">Kết quả trả về (JSON)</h5>
                    <div class="code-container">
                        <div class="code-body">
                            {<br>
                            &nbsp;&nbsp;<span class="string">"balance"</span>: <span class="string">"1,000,000"</span>,<br>
                            &nbsp;&nbsp;<span class="string">"currency"</span>: <span class="string">"USD"</span><br>
                            }
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-doc .nav-link');
        const sections = document.querySelectorAll('section.doc-card');

        // Scroll Spy
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });

        // Smooth Scroll
        navLinks.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                text: '{{ __("Đã sao chép mã API Key!") }}',
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        });
    }
</script>
@endsection
