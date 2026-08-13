@extends('client.layouts.master')
@section('title', __('Tạo đơn hàng'))

@section('css')
<style>
    :root {
        --primary-color: #4680ff;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --card-bg: #ffffff;
    }

    [data-bs-theme="dark"] {
        --bg-light: #10101a;
        --border-color: #2b2b40;
        --text-main: #ffffff;
        --text-muted: #92929f;
        --card-bg: #151521;
    }

    /* Main Container */
    .order-page-container {
        display: flex;
        gap: 24px;
        padding-bottom: 50px;
    }

    .order-main-content {
        flex: 1;
        min-width: 0;
    }

    .order-sidebar {
        width: 380px;
        flex-shrink: 0;
    }

    /* Cards */
    .custom-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .custom-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .custom-card-body {
        padding: 24px;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-main);
        margin-bottom: 10px;
        display: block;
    }

    .form-label span {
        color: #ef4444;
        margin-left: 4px;
    }

    .custom-input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .custom-input-group i {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        z-index: 10;
        pointer-events: none;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Fix select2 overlap icons */
    .custom-input-group .select2-container {
        width: 100% !important;
    }
    .custom-input-group .select2-container--bootstrap5 .select2-selection--single {
        padding-left: 50px !important;
        display: flex;
        align-items: center;
    }
    .custom-input-group .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
    }

    .icon-left {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 16px;
        color: var(--text-muted);
    }

    .custom-control {
        width: 100%;
        padding: 12px 16px;
        padding-left: 44px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-light);
        color: var(--text-main);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .custom-control:focus {
        border-color: var(--primary-color);
        background-color: var(--card-bg);
        box-shadow: 0 0 0 4px rgba(70, 128, 255, 0.1);
        outline: none;
    }

    .btn-paste {
        position: absolute;
        right: 8px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #3563cc 100%);
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(70, 128, 255, 0.2);
    }

    .btn-paste:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
    }

    /* Badges Row */
    .stats-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
        margin-bottom: 25px;
    }

    .stat-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }

    .badge-status { background: rgba(22, 163, 74, 0.1); color: #16a34a; border-color: rgba(22, 163, 74, 0.2); }
    .badge-time { background: rgba(37, 99, 235, 0.1); color: #2563eb; border-color: rgba(37, 99, 235, 0.2); }
    .badge-refund { background: rgba(219, 39, 119, 0.1); color: #db2777; border-color: rgba(219, 39, 119, 0.2); }
    .badge-warranty { background: rgba(234, 88, 12, 0.1); color: #ea580c; border-color: rgba(234, 88, 12, 0.2); }
    .badge-details { 
        background: rgba(6, 182, 212, 0.1); 
        color: #06b6d4; 
        border-color: rgba(6, 182, 212, 0.2); 
        transition: all 0.2s;
        cursor: pointer;
    }
    .badge-details:hover {
        background: rgba(6, 182, 212, 0.2);
        transform: translateY(-2px);
    }

    /* Mode Selection */
    .mode-selector {
        display: flex;
        gap: 16px;
        margin-bottom: 25px;
    }

    .mode-option {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        padding: 14px;
        border-radius: 14px;
        border: 2px solid var(--border-color);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 700;
    }

    .mode-option input[type="radio"] {
        display: none;
    }

    .mode-option:hover {
        border-color: rgba(70, 128, 255, 0.5);
        background: rgba(70, 128, 255, 0.05);
        color: var(--primary-color);
    }

    .mode-option.active {
        border-color: var(--primary-color);
        background: rgba(70, 128, 255, 0.08);
        color: var(--primary-color);
        box-shadow: 0 5px 15px rgba(70, 128, 255, 0.1);
    }

    /* Price Box */
    .price-box {
        background: rgba(70, 128, 255, 0.05);
        border: 2px dashed rgba(70, 128, 255, 0.2);
        border-radius: 16px;
        padding: 24px;
        margin-top: 30px;
    }

    .btn-order-submit {
        width: 100% !important;
        background: linear-gradient(135deg, #4680ff 0%, #3563cc 100%);
        color: #fff !important;
        border: none !important;
        padding: 20px !important;
        border-radius: 18px !important;
        font-size: 1.15rem !important;
        font-weight: 800 !important;
        margin-top: 30px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        box-shadow: 0 10px 30px rgba(70, 128, 255, 0.3) !important;
        cursor: pointer !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
    }

    .btn-order-submit:hover {
        transform: translateY(-4px) scale(1.02) !important;
        box-shadow: 0 20px 40px rgba(70, 128, 255, 0.4) !important;
        filter: brightness(1.15) !important;
    }

    .btn-order-submit:active {
        transform: translateY(-1px) scale(1) !important;
    }

    .price-label { color: var(--text-muted); font-weight: 600; }
    .price-value { color: var(--text-muted); font-weight: 700; }

    .balance-info {
        margin-top: 10px;
        font-size: 0.8rem;
        color: #16a34a;
        font-weight: 600;
    }

    /* Service Description Box */
    .service-description-box {
        margin-top: 20px;
        margin-bottom: 25px;
        padding: 24px;
        background: rgba(70, 128, 255, 0.04);
        border: 1px dashed rgba(70, 128, 255, 0.3);
        border-radius: 16px;
        min-height: 80px;
        position: relative;
    }

    [data-bs-theme="dark"] .service-description-box {
        background: rgba(70, 128, 255, 0.02);
        border: 1px dashed rgba(70, 128, 255, 0.2);
    }

    .service-description-content {
        transition: max-height 0.3s ease;
    }

    .service-description-content.collapsed {
        max-height: 120px;
        overflow: hidden;
        position: relative;
    }

    .service-description-content.collapsed::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.9));
        pointer-events: none;
    }

    [data-bs-theme="dark"] .service-description-content.collapsed::after {
        background: linear-gradient(to bottom, rgba(30, 30, 45, 0), #1e1e2d);
    }

    /* Sidebar User Card */
    .user-profile-card {
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .user-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        border: 2px solid var(--border-color);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .user-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 0 24px 24px;
    }

    .stat-item {
        background: var(--bg-light);
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .stat-label { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px; display: block; }
    .stat-val { font-size: 0.95rem; font-weight: 800; color: var(--text-main); }

    .sidebar-btns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 0 24px 24px;
    }

    .btn-sidebar {
        padding: 12px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-deposit { background: var(--primary-color); color: #fff; }
    .btn-account { background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border-color); }

    /* Utility Cards */
    .utility-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .util-card {
        background: var(--bg-light);
        padding: 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        border: 1px solid var(--border-color);
        transition: all 0.2s;
    }

    .util-card:hover { border-color: var(--primary-color); transform: translateY(-2px); }

    .util-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .bg-blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    .bg-green { background: rgba(22, 163, 74, 0.1); color: #16a34a; }
    .bg-orange { background: rgba(234, 88, 12, 0.1); color: #ea580c; }
    .bg-cyan { background: rgba(8, 145, 178, 0.1); color: #0891b2; }

    /* Order Statistics Cards */
    .user-order-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 0 24px 24px;
    }

    .order-stat-card {
        background: var(--bg-light);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
    }

    .order-stat-card:hover {
        transform: translateY(-5px);
        background: var(--card-bg);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* Hover border colors matching icons */
    .order-stat-card:hover:has(.icon-blue) { border-color: #4680ff; }
    .order-stat-card:hover:has(.icon-orange) { border-color: #ffaa00; }
    .order-stat-card:hover:has(.icon-teal) { border-color: #00b894; }
    .order-stat-card:hover:has(.icon-red) { border-color: #ff4757; }
    .order-stat-card:hover:has(.icon-pink) { border-color: #ed498d; }
    .order-stat-card:hover:has(.icon-gray) { border-color: #636e72; }

    .order-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .order-stat-card:hover .order-stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .order-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        margin-bottom: 6px;
    }

    .order-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 700;
        white-space: nowrap;
    }

    /* Icon Colors */
    .icon-blue { background: rgba(70, 128, 255, 0.1); color: #4680ff; }
    .icon-orange { background: rgba(255, 170, 0, 0.1); color: #ffaa00; }
    .icon-teal { background: rgba(20, 184, 166, 0.1); color: #14b8a6; }
    .icon-red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .icon-pink { background: rgba(236, 72, 153, 0.1); color: #ec4899; }
    .icon-gray { background: rgba(100, 116, 139, 0.1); color: #64748b; }

    /* Stylized Profile Section */
    .profile-section-centered {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px 24px;
        text-align: center;
    }

    .avatar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin-bottom: 16px;
    }

    .avatar-main {
        width: 100%;
        height: 100%;
        border-radius: 24px;
        object-fit: cover;
        box-shadow: 0 10px 20px rgba(70, 128, 255, 0.2);
        border: 4px solid #fff;
    }
    
    [data-bs-theme="dark"] .avatar-main {
        border-color: #1e1e2d;
    }

    .status-dot {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 18px;
        height: 18px;
        background: #22c55e;
        border: 3px solid #fff;
        border-radius: 50%;
    }

    [data-bs-theme="dark"] .status-dot {
        border-color: #1e1e2d;
    }

    .profile-name {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .level-badge-premium {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        background: rgba(70, 128, 255, 0.1);
        color: #4680ff;
        border-radius: 100px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-edit-profile {
        width: calc(100% - 48px);
        margin: 0 24px 24px;
        padding: 14px;
        background: linear-gradient(135deg, #4680ff 0%, #00bcd4 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 8px 15px rgba(70, 128, 255, 0.2);
    }

    .btn-edit-profile:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(70, 128, 255, 0.3);
        color: #fff;
    }

    @media (max-width: 1200px) {
        .order-page-container { flex-direction: column; }
        .order-sidebar { width: 100%; }
    }

    /* Toggle Switch */
    .switch-group {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 15px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
    }

    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--border-color);
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px; width: 16px;
        left: 3px; bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider { background-color: var(--primary-color); }
    input:checked + .slider:before { transform: translateX(22px); }

    .switch-label { font-weight: 600; color: var(--text-main); font-size: 0.9rem; }

    /* Modal Styles - Premium Subx Style */
    .order-confirm-modal {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .order-confirm-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        animation: fadeIn 0.3s ease;
    }

    .order-confirm-wrapper {
        position: relative;
        z-index: 10001;
        width: 100%;
        max-width: 850px;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .order-confirm-modal.active .order-confirm-wrapper {
        transform: translateY(0);
        opacity: 1;
    }

    .order-confirm-container {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    [data-bs-theme="dark"] .order-confirm-container {
        background: #151521;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .order-confirm-header {
        padding: 24px 30px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    [data-bs-theme="dark"] .order-confirm-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .order-confirm-header-icon {
        width: 44px;
        height: 44px;
        background: rgba(70, 128, 255, 0.1);
        color: #4680ff;
        border-radius: 12px;
        font-size: 1.5rem;
    }

    .order-confirm-title { font-weight: 800; font-size: 1.25rem; color: #1e293b; }
    [data-bs-theme="dark"] .order-confirm-title { color: #ffffff; }

    .order-confirm-close {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.8rem;
        cursor: pointer;
        transition: color 0.2s;
        display: flex;
        align-items: center;
    }
    .order-confirm-close:hover { color: #ef4444; }

    .order-confirm-body { padding: 30px; }

    .order-confirm-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 30px;
    }

    @media (max-width: 768px) {
        .order-confirm-layout { grid-template-columns: 1fr; }
    }

    .order-confirm-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .order-info-list {
        background: rgba(0, 0, 0, 0.02);
        border-radius: 16px;
        overflow: hidden;
    }

    [data-bs-theme="dark"] .order-info-list {
        background: rgba(255, 255, 255, 0.02);
    }

    .order-info-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        align-items: center;
    }

    [data-bs-theme="dark"] .order-info-row {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .order-info-row:last-child { border-bottom: none; }

    .order-info-label {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .order-info-icon {
        width: 28px;
        height: 28px;
        background: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4680ff;
        font-size: 1.1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .order-info-icon {
        background: #2b2b40;
        color: #72a1ff;
    }

    .order-info-value {
        color: #1e293b;
        font-weight: 700;
        font-size: 0.95rem;
        text-align: right;
    }

    [data-bs-theme="dark"] .order-info-value { color: #ffffff; }

    .order-price-box {
        background: #f8fafc;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        padding: 24px;
    }

    [data-bs-theme="dark"] .order-price-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .order-price-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-weight: 600;
        color: #64748b;
    }

    .order-price-total {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px dashed rgba(0, 0, 0, 0.05);
        color: #ef4444 !important;
        font-size: 1.25rem;
        font-weight: 800;
    }

    [data-bs-theme="dark"] .order-price-total {
        border-top-color: rgba(255, 255, 255, 0.05);
    }

    .order-confirm-footer {
        padding: 24px 30px;
        background: rgba(0, 0, 0, 0.02);
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    [data-bs-theme="dark"] .order-confirm-footer {
        background: rgba(255, 255, 255, 0.02);
    }

    .order-confirm-btn {
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .order-confirm-btn-cancel {
        background: #f1f5f9;
        color: #64748b;
    }

    .order-confirm-btn-cancel:hover { background: #e2e8f0; }

    .order-confirm-btn-submit {
        background: #4680ff;
        color: #fff;
        box-shadow: 0 10px 15px -3px rgba(70, 128, 255, 0.3);
    }

    .order-confirm-btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 20px -3px rgba(70, 128, 255, 0.4); }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Select2 Dark Mode Fixes */
    [data-bs-theme="dark"] .select2-dropdown { background-color: #151521 !important; border: 1px solid #2b2b40 !important; }
    [data-bs-theme="dark"] .select2-results__option { color: #92929f !important; }
    [data-bs-theme="dark"] .select2-results__option--highlighted { background-color: rgba(70, 128, 255, 0.1) !important; color: #fff !important; }
    [data-bs-theme="dark"] .select2-search__field { background-color: #10101a !important; border: 1px solid #2b2b40 !important; color: #fff !important; }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection { background-color: #10101a !important; border: 1px solid #2b2b40 !important; color: #fff !important; }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { color: #fff !important; }

    /* Premium Badge Overrides for Select2 */
    .badge-light-primary {
        background-color: rgba(70, 128, 255, 0.1) !important;
        color: #4680ff !important;
        border: none !important;
    }
    .badge-light-success {
        background-color: rgba(22, 163, 74, 0.1) !important;
        color: #16a34a !important;
        border: none !important;
    }
    [data-bs-theme="dark"] .badge-light-primary {
        background-color: rgba(70, 128, 255, 0.15) !important;
        color: #72a1ff !important;
    }
    [data-bs-theme="dark"] .badge-light-success {
        background-color: rgba(22, 163, 74, 0.15) !important;
        color: #4ade80 !important;
    }

    /* Select2 Dropdown Search Box Styling - Premium Match */
    .select2-container--bootstrap-5 .select2-dropdown .select2-search {
        padding: 15px !important;
        background: transparent !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field {
        border-radius: 12px !important;
        padding: 12px 16px !important;
        background-color: var(--bg-light) !important;
        border: 1px solid var(--border-color) !important;
        font-size: 0.9rem !important;
    }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field {
        background-color: #10101a !important;
        border-color: #2b2b40 !important;
        color: #fff !important;
    }
    .select2-results__message {
        padding: 25px !important;
        text-align: center;
        color: var(--text-muted) !important;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Modal Styles */
    .order-confirm-modal {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .order-confirm-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        animation: fadeIn 0.3s ease;
    }
    .order-confirm-wrapper {
        position: relative;
        z-index: 10000;
        width: 100%;
        max-width: 800px;
        padding: 20px;
    }
    .order-confirm-container {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }

    /* Announcement Box Style */
    .announcement-box {
        background: #f0f7ff;
        border: 1px solid rgba(70, 128, 255, 0.1);
        border-left: 4px solid #4680ff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    [data-bs-theme="dark"] .announcement-box {
        background: rgba(70, 128, 255, 0.05);
        border-color: rgba(70, 128, 255, 0.1);
        border-left-color: #4680ff;
    }

    .announcement-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .announcement-icon {
        width: 32px;
        height: 32px;
        background: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4680ff;
        font-size: 1.2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .announcement-icon {
        background: #2b2b40;
    }

    .announcement-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }

    [data-bs-theme="dark"] .announcement-title { color: #fff; }

    .announcement-content {
        color: #475569;
        font-size: 0.88rem;
        line-height: 1.6;
    }

    [data-bs-theme="dark"] .announcement-content { color: #94a3b8; }

    /* Step Numbers Style - Premium Neon Look */
    .step-number {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #6366f1 0%, #4680ff 100%);
        color: #fff;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 900;
        margin-right: 12px;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        flex-shrink: 0;
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        position: relative;
        font-family: 'Inter', sans-serif;
    }

    [data-bs-theme="dark"] .step-number {
        box-shadow: 0 0 20px rgba(70, 128, 255, 0.25);
        background: linear-gradient(135deg, #818cf8 0%, #4680ff 100%);
    }

    .form-label {
        display: flex;
        align-items: center;
        font-weight: 700 !important;
        color: var(--text-main) !important;
        margin-bottom: 15px !important;
        font-size: 0.95rem !important;
        letter-spacing: -0.01em;
    }
</style>

@endsection

@section('content')
<!-- Announcement Box (Full Width) -->
@if (s('notice_service'))
<div class="announcement-box">
    <div class="announcement-header">
        <div class="announcement-icon">
            <iconify-icon icon="solar:bell-bold-duotone"></iconify-icon>
        </div>
        <div class="announcement-title">{{ __('Thông báo hệ thống') }}</div>
    </div>
    <div class="announcement-content">
        {!! nl2br(e(s(''))) !!}
    </div>
</div>
@endif

<div class="order-page-container">
    <!-- Left Column: Order Form -->
    <div class="order-main-content">

        <div class="custom-card">
            <div class="card-header border-0 pt-5 d-flex align-items-center justify-content-between">
                <h3 class="card-title align-items-center mb-0">
                    <iconify-icon icon="solar:cart-large-bold" class="text-primary me-2 fs-2"></iconify-icon>
                    <span class="fw-bold fs-3 text-white">{{ __('Đặt hàng') }}</span>
                </h3>
                <div class="card-toolbar">
                    <button class="btn btn-outline-warning btn-sm fw-bold rounded-pill px-4 d-flex align-items-center" onclick="openNoteOrderModal()">
                        <iconify-icon icon="solar:info-circle-bold" class="fs-6 me-2"></iconify-icon>
                        {{ __('Lưu ý khi đặt đơn') }}
                    </button>
                </div>
            </div>
            <div class="custom-card-body position-relative">
                <div id="service-loader" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 100; border-radius: inherit; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border text-primary" style="width: 2.5rem; height: 2.5rem;" role="status"></div>
                        <span class="text-white fw-bold mt-3">{{ __('Đang tải dữ liệu...') }}</span>
                    </div>
                </div>
                <form id="order-form" bvq-request="true" action="{{ route('request-order') }}" method="POST" reload="true">
                    @csrf
                    <!-- Quick Search -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Tìm nhanh dịch vụ') }}</label>
                        <div class="custom-input-group">
                            <i class="ri-search-line"></i>
                            <select id="sl-quick-search" class="custom-control" data-control="select2" data-placeholder="{{ __('-- Tìm và chọn dịch vụ nhanh --') }}">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><span class="step-number">1</span> {{ __('Nền tảng') }} <span>*</span></label>
                                <div class="custom-input-group">
                                    <i class="ri-apps-2-line"></i>
                                    <select id="sl-platforms" class="custom-control" data-control="select2" data-placeholder="{{ __('Chọn nền tảng') }}">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><span class="step-number">1</span> {{ __('Phân loại') }} <span>*</span></label>
                                <div class="custom-input-group">
                                    <i class="ri-folder-open-line"></i>
                                    <select id="sl-categories" class="custom-control" data-control="select2" data-placeholder="{{ __('Chọn phân loại') }}">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="step-number">2</span> {{ __('Dịch vụ') }} <span>*</span></label>
                        <div class="custom-input-group">
                            <i class="ri-customer-service-2-line"></i>
                            <select id="sl-services" name="service" class="custom-control" data-control="select2" data-placeholder="{{ __('Chọn dịch vụ') }}">
                            </select>
                        </div>
                        <!-- Status Badges -->
                        <div class="stats-badges">
                            <span class="stat-badge badge-status" id="status-service">
                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                {{ __('Trạng thái:') }} {{ __('Hoạt động') }}
                            </span>
                            <span class="stat-badge badge-time" id="time-service">
                                <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                                {{ __('Ước tính:') }} 00:00:00
                            </span>
                            <span class="stat-badge badge-refund" id="refund-service">
                                <iconify-icon icon="solar:round-transfer-horizontal-bold"></iconify-icon>
                                {{ __('Hoàn tiền:') }} {{ __('Không') }}
                            </span>
                            <span class="stat-badge badge-warranty" id="warranty-service">
                                <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                                {{ __('Bảo hành:') }} {{ __('Không') }}
                            </span>
                        </div>

                        <!-- Service Description Box -->
                        <div class="service-description-box" id="service-description-container">
                            <div class="service-description-content" id="service-description-wrapper">
                                <div class="text-white fw-medium fs-6" id="description-service" style="line-height: 1.6; opacity: 0.8;">
                                    <div class="d-flex align-items-center justify-content-center py-4">
                                        <span class="text-muted">{{ __('-- Chọn dịch vụ để xem lưu ý --') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="service-description-toggle text-center mt-4" id="service-description-toggle" style="display: none;">
                                <button type="button" class="btn btn-sm btn-light-primary fw-bold" onclick="toggleServiceDescription()" style="border-radius: 8px;">
                                    <iconify-icon icon="solar:add-circle-line-duotone" class="me-1"></iconify-icon> <span id="service-description-toggle-text">{{ __('Xem thêm nội dung') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="div-link">
                        <label class="form-label"><span class="step-number">3</span> {{ __('Liên kết') }} / UID {{ __('Cần tăng') }} <span>*</span></label>
                        <div class="custom-input-group">
                            <i class="ri-link"></i>
                            <input type="text" id="link" name="link" class="custom-control" placeholder="{{ __('Nhập liên kết hoặc UID cần tăng tương tác...') }}">
                            <button type="button" class="btn-paste" onclick="openPasteConfirmModal()">
                                <iconify-icon icon="solar:copy-bold"></iconify-icon>
                                {{ __('Dán') }}
                            </button>
                        </div>
                    </div>

                    <div class="form-group" id="div-link-many" style="display: none;">
                        <label class="form-label">{{ __('Danh sách Liên Kết / UID') }} <span>*</span></label>
                        <textarea id="link_many" name="link_many" class="form-control bg-light border-0 rounded-4 p-4" rows="5" placeholder="{{ __('Mỗi dòng một liên kết...') }}" onkeyup="countLines()" onchange="countLines()"></textarea>
                        <div class="mt-2 fs-7 text-muted d-flex align-items-center">
                            <iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon>
                            {{ __('Nhập mỗi liên kết trên một dòng.') }} <span class="ms-1 fw-bold text-primary">{{ __('Tổng cộng:') }} <span id="line-count">0</span> {{ __('liên kết') }}</span>
                        </div>
                    </div>

                    <div class="mode-selector">
                        <label class="mode-option">
                            <input type="radio" name="mode" value="one" checked onchange="toggleMode()">
                            <span>{{ __('Một') }}</span>
                        </label>
                        <label class="mode-option">
                            <input type="radio" name="mode" value="many" onchange="toggleMode()">
                            <span>{{ __('Nhiều') }}</span>
                        </label>
                    </div>

                    <div class="form-group" id="div-comments" style="display: none;">
                        <label class="form-label">{{ __('Nội dung bình luận') }} <span>*</span></label>
                        <textarea id="comments" name="comments" class="form-control bg-light border-0 rounded-4 p-4" rows="8" placeholder="{{ __('Nhập mỗi dòng là một bình luận...') }}" onkeyup="handleCommentInput()" onchange="handleCommentInput()"></textarea>
                        <div class="mt-2 fs-7 text-muted d-flex align-items-center">
                            <iconify-icon icon="solar:info-circle-bold" class="me-1 text-primary"></iconify-icon>
                            {{ __('Mỗi dòng là một bình luận.') }} <span class="ms-1 fw-bold text-primary">{{ __('Tổng cộng:') }} <span id="comment-count">0</span> {{ __('bình luận') }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="step-number">4</span> {{ __('Số lượng') }} <span>*</span></label>
                        <input type="number" id="quantity" name="quantity" class="form-control bg-light border-0 rounded-4 p-4 fw-bold" value="1000">
                        <div class="form-text text-muted mt-2" id="quantity_limit">{{ __('Tối thiểu:') }} 10 - {{ __('Tối đa:') }} 10.000.000</div>
                    </div>

                    <!-- Dynamic Attributes -->
                    <div id="idKeyInput"></div>

                    <!-- Price Box -->
                    <div class="price-box">
                        <div class="d-flex justify-content-between mb-3 opacity-75">
                            <span class="fs-7">{{ __('Giá trị đơn hàng:') }}</span>
                            <span class="fs-7 fw-bold" id="order-value">0.00 đ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold text-white">{{ __('Tổng tiền cần thanh toán:') }}</span>
                            <span class="fs-4 fw-bolder text-primary" id="payment">0.00 đ</span>
                        </div>
                        <div class="d-flex align-items-center fs-7 text-muted">
                            <iconify-icon icon="solar:wallet-bold" class="me-2 text-success"></iconify-icon>
                            {{ __('Số dư hiện tại:') }} <span class="ms-1 fw-bold text-success">{{ Auth::check() ? convert_currency(Auth::user()->balance, null, false) : '0' }}</span>
                        </div>
                    </div>

                    <div class="switch-group">
                        <label class="switch">
                            <input type="checkbox" name="is_schedule" id="is_schedule" onchange="toggleSchedule()">
                            <span class="slider"></span>
                        </label>
                        <span class="switch-label">{{ __('Đặt lịch chạy. Múi giờ: Asia/Ho_Chi_Minh') }}</span>
                    </div>

                    <div id="schedule-container" style="display: none; margin-bottom: 20px; padding: 18px; background: rgba(70, 128, 255, 0.03); border-radius: 12px; border: 1px dashed var(--border-color);">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ __('Thời gian bắt đầu') }}</label>
                            <div class="custom-input-group">
                                <iconify-icon icon="solar:calendar-bold" class="icon-left text-primary"></iconify-icon>
                                <input type="datetime-local" name="schedule_time" class="custom-control">
                            </div>
                        </div>
                    </div>

                    <div class="switch-group">
                        <label class="switch">
                            <input type="checkbox" name="is_loop" id="is_loop" onchange="toggleDripFeed()">
                            <span class="slider"></span>
                        </label>
                        <span class="switch-label">{{ __('Đặt hàng lặp lại') }}</span>
                    </div>

                    <div id="drip-feed-container" style="display: none; margin-bottom: 20px; padding: 18px; background: rgba(70, 128, 255, 0.03); border-radius: 12px; border: 1px dashed var(--border-color);">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="form-label">{{ __('Số lần chạy (Runs)') }}</label>
                                    <div class="custom-input-group">
                                        <iconify-icon icon="solar:play-bold" class="icon-left text-success"></iconify-icon>
                                        <input type="number" name="runs" id="runs" class="custom-control" value="2" min="2" oninput="handleDataInput()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="form-label">{{ __('Giãn cách (phút)') }}</label>
                                    <div class="custom-input-group">
                                        <iconify-icon icon="solar:clock-circle-bold" class="icon-left text-warning"></iconify-icon>
                                        <input type="number" name="interval" class="custom-control" value="60" min="1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border border-dashed border-primary" style="background: var(--card-bg) !important;">
                                    <span class="fs-7 fw-bold text-muted">{{ __('Tổng số lượng:') }}</span>
                                    <span class="fs-6 fw-bold text-primary" id="total-drip-quantity">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-order-submit">
                        <iconify-icon icon="solar:cart-check-bold" class="fs-4 me-2"></iconify-icon>
                        {{ __('TẠO ĐƠN HÀNG NGAY') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar -->
    <div class="order-sidebar">
        <div class="custom-card">
            <div class="custom-card-header">
                <div class="custom-card-title">
                    <iconify-icon icon="solar:user-circle-bold" class="text-primary"></iconify-icon>
                    {{ __('Thông Tin Tài Khoản') }}
                </div>
            </div>
            @if(Auth::check())
            <div class="profile-section-centered">
                <div class="avatar-wrapper">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?: Auth::user()->username) }}&background=4680ff&color=fff&bold=true&size=128" class="avatar-main" alt="avatar">
                    <div class="status-dot"></div>
                </div>
                <div class="profile-name">{{ Auth::user()->name ?: Auth::user()->username }}</div>
                <div class="level-badge-premium">
                    <iconify-icon icon="solar:crown-minimalistic-bold"></iconify-icon>
                    {{ Auth::user()->level }}
                </div>
            </div>

            <div class="user-order-stats">
                <a href="{{ route('orders') }}" class="order-stat-card">
                    <div class="order-stat-icon icon-blue">
                        <iconify-icon icon="solar:cart-large-bold"></iconify-icon>
                    </div>
                    <div class="order-stat-value">{{ $stats['total'] ?? 0 }}</div>
                    <div class="order-stat-label">{{ __('Đơn đã mua') }}</div>
                </a>
                <a href="{{ route('orders', ['status' => 'In progress']) }}" class="order-stat-card">
                    <div class="order-stat-icon icon-orange">
                        <iconify-icon icon="solar:hourglass-bold"></iconify-icon>
                    </div>
                    <div class="order-stat-value">{{ ($stats['running'] ?? 0) + ($stats['processing'] ?? 0) }}</div>
                    <div class="order-stat-label">{{ __('Đơn đang chạy') }}</div>
                </a>
                <a href="{{ route('orders', ['status' => 'Completed']) }}" class="order-stat-card">
                    <div class="order-stat-icon icon-teal">
                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                    </div>
                    <div class="order-stat-value">{{ $stats['completed'] ?? 0 }}</div>
                    <div class="order-stat-label">{{ __('Đơn hoàn tất') }}</div>
                </a>
                <a href="{{ route('orders', ['status' => 'Canceled']) }}" class="order-stat-card">
                    <div class="order-stat-icon icon-red">
                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                    </div>
                    <div class="order-stat-value">{{ $stats['canceled'] ?? 0 }}</div>
                    <div class="order-stat-label">{{ __('Đơn hủy') }}</div>
                </a>
                <a href="{{ route('orders', ['status' => 'Partial']) }}" class="order-stat-card">
                    <div class="order-stat-icon icon-pink">
                        <iconify-icon icon="solar:pause-circle-bold"></iconify-icon>
                    </div>
                    <div class="order-stat-value">{{ $stats['refunded'] ?? 0 }}</div>
                    <div class="order-stat-label">{{ __('Đơn tạm dừng') }}</div>
                </a>
                <a href="{{ route('orders', ['status' => 'Refunded']) }}" class="order-stat-card">
                    <div class="order-stat-icon icon-gray">
                        <iconify-icon icon="solar:history-bold"></iconify-icon>
                    </div>
                    <div class="order-stat-value">{{ Auth::user()->orders()->where('status', 'Refunded')->count() }}</div>
                    <div class="order-stat-label">{{ __('Đơn hoàn tiền') }}</div>
                </a>
            </div>

            <a href="{{ route('settings') }}" class="btn-edit-profile">
                <iconify-icon icon="solar:pen-new-square-bold"></iconify-icon>
                {{ __('Chỉnh sửa hồ sơ') }}
            </a>
            @else
            <div class="p-6 text-center">
                <h5 class="mb-4 text-gray-800 fw-bold">{{ __('Vui lòng đăng nhập') }}</h5>
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('auth.login') }}" class="btn btn-primary rounded-3">{{ __('Đăng nhập ngay') }}</a>
                    <a href="{{ route('auth.register') }}" class="btn btn-light-primary rounded-3">{{ __('Đăng ký tài khoản') }}</a>
                </div>
            </div>
            @endif
        </div>

        <div class="custom-card">
            <div class="custom-card-header">
                <div class="custom-card-title">{{ __('Tiện ích nhanh') }}</div>
            </div>
            <div class="custom-card-body">
                <div class="utility-grid">
                    <a href="{{ route('orders') }}" class="util-card">
                        <div class="util-icon bg-blue"><iconify-icon icon="solar:clipboard-list-bold"></iconify-icon></div>
                        <div>
                            <div class="fw-bold fs-7 text-gray-800">{{ __('Đơn hàng') }}</div>
                            <div class="text-muted fs-8">{{ __('Xem lịch sử đơn') }}</div>
                        </div>
                    </a>
                    <a href="{{ route('addfuns') }}" class="util-card">
                        <div class="util-icon bg-green"><iconify-icon icon="solar:card-send-bold"></iconify-icon></div>
                        <div>
                            <div class="fw-bold fs-7 text-gray-800">{{ __('Nạp tiền') }}</div>
                            <div class="text-muted fs-8">{{ __('Lịch sử & tạo phiếu') }}</div>
                        </div>
                    </a>
                    <a href="{{ route('services') }}" class="util-card">
                        <div class="util-icon bg-orange"><iconify-icon icon="solar:box-bold"></iconify-icon></div>
                        <div>
                            <div class="fw-bold fs-7 text-gray-800">{{ __('Sản phẩm') }}</div>
                            <div class="text-muted fs-8">{{ __('Danh mục dịch vụ') }}</div>
                        </div>
                    </a>
                    <a href="{{ route('statistics') }}" class="util-card">
                        <div class="util-icon bg-cyan"><iconify-icon icon="solar:chart-bold"></iconify-icon></div>
                        <div>
                            <div class="fw-bold fs-7 text-gray-800">{{ __('Thống kê') }}</div>
                            <div class="text-muted fs-8">{{ __('Hiệu suất tài khoản') }}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="custom-card">
             <div class="custom-card-header">
                <div class="custom-card-title">{{ __('Thống kê nhanh') }}</div>
            </div>
            <div class="custom-card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-6 border-0">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-35px me-4">
                                <span class="symbol-label bg-light-primary text-primary"><iconify-icon icon="solar:money-bag-bold"></iconify-icon></span>
                            </div>
                            <span class="text-gray-700 fw-bold fs-7">{{ __('Tổng nạp') }}</span>
                        </div>
                        <span class="text-gray-900 fw-bolder fs-7">{{ convert_currency(Auth::user()->total_deposit ?? 0, null, false) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-6 border-0">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-35px me-4">
                                <span class="symbol-label bg-light-success text-success"><iconify-icon icon="solar:calendar-bold"></iconify-icon></span>
                            </div>
                            <span class="text-gray-700 fw-bold fs-7">{{ __('Tháng này') }}</span>
                        </div>
                        <span class="text-gray-900 fw-bolder fs-7">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Xác nhận đặt hàng (Custom Subx Style) -->
<div class="order-confirm-modal" id="orderConfirmModal" style="display: none;">
    <div class="order-confirm-overlay" onclick="closeOrderConfirmModal()"></div>
    <div class="order-confirm-wrapper">
        <div class="order-confirm-container">
            <div class="order-confirm-header">
                <div class="d-flex align-items-center gap-3">
                    <span class="order-confirm-header-icon d-inline-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:cart-check-linear"></iconify-icon>
                    </span>
                    <div class="d-flex flex-column">
                        <h4 class="order-confirm-title mb-0">{{ __('Xác nhận đặt hàng') }}</h4>
                        <small class="text-muted">{{ __('Kiểm tra lại thông tin trước khi xác nhận thanh toán') }}</small>
                    </div>
                </div>
                <button type="button" class="order-confirm-close" onclick="closeOrderConfirmModal()">
                    <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                </button>
            </div>

            <div class="order-confirm-body">
                <div class="order-confirm-layout">
                    <div class="order-confirm-main">
                        <h6 class="order-confirm-section-title">
                            <iconify-icon icon="solar:clipboard-list-linear" class="me-2 text-primary"></iconify-icon>{{ __('Thông tin đơn hàng') }}
                        </h6>
                        <div class="order-info-list">
                            <div class="order-info-row">
                                <div class="order-info-label">
                                    <span class="order-info-icon"><iconify-icon icon="solar:cart-large-linear"></iconify-icon></span>
                                    <span>{{ __('Dịch vụ') }}</span>
                                </div>
                                <div class="order-info-value" id="confirm-service-name">-</div>
                            </div>
                            <div class="order-info-row">
                                <div class="order-info-label">
                                    <span class="order-info-icon"><iconify-icon icon="solar:link-linear"></iconify-icon></span>
                                    <span>{{ __('Liên kết / UID') }}</span>
                                </div>
                                <div class="order-info-value text-break" id="confirm-service-link">-</div>
                            </div>
                            <div class="order-info-row">
                                <div class="order-info-label">
                                    <span class="order-info-icon"><iconify-icon icon="solar:hashtag-linear"></iconify-icon></span>
                                    <span>{{ __('Số lượng') }}</span>
                                </div>
                                <div class="order-info-value" id="confirm-service-quantity">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="order-confirm-side">
                        <h6 class="order-confirm-section-title mb-4">
                            <iconify-icon icon="solar:card-linear" class="me-2 text-primary"></iconify-icon>{{ __('Thông tin thanh toán') }}
                        </h6>
                        <div class="order-price-box">
                            <div class="order-price-item">
                                <span>{{ __('Giá trị đơn hàng') }}</span>
                                <span id="confirm-service-value">0₫</span>
                            </div>
                            <div class="order-price-item order-price-total">
                                <span>{{ __('Tổng tiền thanh toán') }}</span>
                                <span id="confirm-service-total">0₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-confirm-footer">
                <button type="button" class="order-confirm-btn order-confirm-btn-cancel" onclick="closeOrderConfirmModal()">{{ __('Hủy') }}</button>
                <button type="button" class="order-confirm-btn order-confirm-btn-submit" id="btn-confirm-order-final">{{ __('Xác nhận đặt hàng') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Paste Confirm Modal -->
<div class="order-confirm-modal" id="pasteConfirmModal" style="display: none;">
    <div class="order-confirm-overlay" onclick="closePasteConfirmModal()"></div>
    <div class="order-confirm-wrapper" style="max-width: 450px;">
        <div class="order-confirm-container text-center p-8">
            <div class="mb-5 d-flex justify-content-center">
                <div class="d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(70, 128, 255, 0.1); border-radius: 50%;">
                    <iconify-icon icon="solar:question-square-bold-duotone" style="font-size: 45px; color: #4680ff;"></iconify-icon>
                </div>
            </div>
            <h4 class="fw-bold text-white mb-3 fs-3">{{ __('Xác nhận dán liên kết') }}</h4>
            <p class="text-muted mb-8 px-4">{{ __('Bạn có muốn dán liên kết từ clipboard vào ô nhập liên kết không?') }}</p>
            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-secondary px-8 py-3 fw-bold" onclick="closePasteConfirmModal()" style="border-radius: 12px;">{{ __('Hủy') }}</button>
                <button type="button" class="btn btn-primary px-8 py-3 fw-bold" onclick="confirmPaste()" style="border-radius: 12px; background: #4680ff; border: none; box-shadow: 0 4px 15px rgba(70, 128, 255, 0.3);">
                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                    {{ __('Có, dán ngay') }}
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Service Stats Modal -->
<div class="order-confirm-modal" id="serviceStatsModal" style="display: none;">
    <div class="order-confirm-overlay" onclick="closeServiceStatsModal()"></div>
    <div class="order-confirm-wrapper" style="max-width: 700px;">
        <div class="order-confirm-container">
            <div class="order-confirm-header">
                <div class="d-flex align-items-center">
                    <div class="order-confirm-header-icon me-3 d-flex align-items-center justify-content-center" style="background: rgba(var(--bg-light-rgb), 0.1);">
                        <img src="" id="stats-service-icon" alt="" width="24" height="24" style="object-fit: contain; display: none;">
                        <iconify-icon id="stats-service-default-icon" icon="solar:chart-square-bold-duotone" style="font-size: 24px; color: #00bcd4;"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="order-confirm-title mb-0" id="stats-service-name">...</h4>
                        <p class="text-muted mb-0 fs-7">{{ __('Dữ liệu thống kê từ 10 đơn hàng gần nhất') }}</p>
                    </div>
                </div>
            </div>
            <div class="order-confirm-body py-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 text-center border" style="background: rgba(var(--bg-light-rgb), 0.5); border-color: var(--border-color) !important;">
                            <div class="text-muted fs-7 mb-2">{{ __('Trạng thái') }}</div>
                            <div class="fw-bold text-success fs-6" id="stats-status">Ổn định</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 text-center border" style="background: rgba(var(--bg-light-rgb), 0.5); border-color: var(--border-color) !important;">
                            <div class="text-muted fs-7 mb-2">{{ __('Nút hủy đơn') }}</div>
                            <div class="fw-bold fs-6" id="stats-cancel">Có</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 text-center border" style="background: rgba(var(--bg-light-rgb), 0.5); border-color: var(--border-color) !important;">
                            <div class="text-muted fs-7 mb-2">{{ __('Bảo hành') }}</div>
                            <div class="fw-bold fs-6" id="stats-refill">Không</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 text-center border" style="background: rgba(70, 128, 255, 0.05); border-color: rgba(70, 128, 255, 0.2) !important;">
                            <div class="text-muted fs-7 mb-2">{{ __('Đơn mới nhất') }}</div>
                            <div class="fw-bold fs-6 text-primary" id="stats-newest">--:--:-- / 0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 text-center border" style="background: rgba(70, 128, 255, 0.05); border-color: rgba(70, 128, 255, 0.2) !important;">
                            <div class="text-muted fs-7 mb-2">{{ __('Đơn số lượng cao nhất') }}</div>
                            <div class="fw-bold fs-6 text-primary" id="stats-highest">--:--:-- / 0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 text-center border" style="background: rgba(70, 128, 255, 0.05); border-color: rgba(70, 128, 255, 0.2) !important;">
                            <div class="text-muted fs-7 mb-2">{{ __('Đơn số lượng thấp nhất') }}</div>
                            <div class="fw-bold fs-6 text-primary" id="stats-lowest">--:--:-- / 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-confirm-footer d-flex justify-content-end">
                <button type="button" class="btn btn-secondary px-5 py-3 rounded-pill fw-bold" onclick="closeServiceStatsModal()">{{ __('Đóng') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ONLOAD_FETURES = @json($platforms);
    const attributesCode = @json($attributes = attribute_views(null, false, true));
    const trans = {

        "no_warranty": "{{ __('Không bảo hành') }}",
        "warranty": "{{ __('Bảo hành') }}",
        "estimate": "{{ __('Ước tính') }}",
        "refund": "{{ __('Hoàn tiền') }}",
        "view_details": "{{ __('Xem chi tiết') }}",
        "stats_title": "{{ __('Dữ liệu thống kê từ 10 đơn hàng gần nhất') }}",
        "status": "{{ __('Trạng thái') }}",
        "cancel_btn": "{{ __('Nút hủy đơn') }}",
        "newest_order": "{{ __('Đơn mới nhất') }}",
        "highest_order": "{{ __('Đơn số lượng cao nhất') }}",
        "lowest_order": "{{ __('Đơn số lượng thấp nhất') }}",
        "yes": "{{ __('Có') }}",
        "no": "{{ __('Không') }}",
        "active": "{{ __('Hoạt động') }}"
    };
    
    function openOrderConfirmModal() {
        $('#orderConfirmModal').css('display', 'flex');
        setTimeout(() => {
            $('#orderConfirmModal').addClass('active');
        }, 10);
        $('body').css('overflow', 'hidden');
    }

    function closeOrderConfirmModal() {
        $('#orderConfirmModal').removeClass('active');
        setTimeout(() => {
            $('#orderConfirmModal').css('display', 'none');
            $('body').css('overflow', '');
        }, 300);
    }

    function toggleMode() {
        const mode = $('input[name="mode"]:checked').val();
        
        // Handle visual classes
        $('.mode-option').removeClass('active');
        $(`input[name="mode"]:checked`).closest('.mode-option').addClass('active');

        if (mode === 'many') {
            $('#div-link').hide();
            $('#div-link-many').show();
        } else {
            $('#div-link').show();
            $('#div-link-many').hide();
        }
    }

    function toggleSchedule() {
        if ($('#is_schedule').is(':checked')) {
            $('#schedule-container').slideDown(300);
        } else {
            $('#schedule-container').slideUp(300);
        }
    }

    function toggleDripFeed() {
        if ($('#is_loop').is(':checked')) {
            $('#drip-feed-container').slideDown(300);
        } else {
            $('#drip-feed-container').slideUp(300);
        }
        if (typeof handleDataInput === 'function') {
            handleDataInput();
        }
    }

    function handleCommentInput() {
        const text = $('#comments').val();
        const lines = text.split('\n').filter(line => line.trim() !== '');
        $('#comment-count').text(lines.length);
        
        // Tự động điền số lượng nếu đang ở chế độ nhập bình luận
        if ($('#div-comments').is(':visible')) {
            $('#quantity').val(lines.length);
            if (typeof handleDataInput === 'function') handleDataInput();
        }
    }

    // Initialize state
    $(document).ready(function() {
        toggleMode();
        
        // Auto select service from URL if provided
        const urlParams = new URLSearchParams(window.location.search);
        const serviceIdParam = urlParams.get('service');
        if (serviceIdParam) {
            let targetPlatform = null;
            let targetCategory = null;
            let targetServiceData = null;
            let targetCategoryIcon = null;
            
            for (let platform of ONLOAD_FETURES) {
                for (let category of platform.categories) {
                    for (let service of category.services) {
                        if (service.id == serviceIdParam) {
                            targetPlatform = platform.id;
                            targetCategory = category.id;
                            targetServiceData = service;
                            targetCategoryIcon = category.icon;
                            break;
                        }
                    }
                    if (targetServiceData) break;
                }
                if (targetServiceData) break;
            }
            
            if (targetServiceData) {
                setTimeout(() => {
                    $("#service-loader").css("display", "flex").hide().fadeIn(200);
                    $("#sl-platforms").val(targetPlatform).trigger("change");
                    
                    if (typeof initCategorySelect2 === 'function') {
                        initCategorySelect2(targetPlatform);
                    }
                    $("#sl-categories").val(targetCategory).trigger("change");
                    
                    if (typeof initServiceSelect2 === 'function') {
                        initServiceSelect2(targetPlatform, targetCategory, serviceIdParam);
                    }
                    $("#sl-services").val(serviceIdParam).trigger("change");
                    
                    if (typeof updateInput === 'function') {
                        let formattedData = {
                            id: targetServiceData.id,
                            text: targetServiceData.name,
                            icon: targetServiceData.icon || targetCategoryIcon,
                            rate: targetServiceData.rate,
                            platform: targetPlatform,
                            category: targetCategory,
                            service: targetServiceData.name,
                            min: targetServiceData.min,
                            max: targetServiceData.max,
                            refill: targetServiceData.refill,
                            cancel: targetServiceData.cancel,
                            dripfeed: targetServiceData.dripfeed,
                            warranty: targetServiceData.warranty,
                            average_time: targetServiceData.average_time,
                            attributes: targetServiceData.attributes,
                            type: targetServiceData.type
                        };
                        updateInput(formattedData);
                    }
                    
                    // Update quick search to match
                    if ($("#sl-quick-search").length) {
                        $("#sl-quick-search").val(serviceIdParam).trigger("change.select2");
                    }
                    $("#service-loader").fadeOut(200);
                }, 300);
            }
        }
    });
    function openNoteOrderModal() {
        $('#noteOrderModal').css('display', 'flex');
        setTimeout(() => {
            $('#noteOrderModal').addClass('active');
        }, 10);
        $('body').css('overflow', 'hidden');
    }

    function closeNoteOrderModal() {
        $('#noteOrderModal').removeClass('active');
        setTimeout(() => {
            $('#noteOrderModal').css('display', 'none');
            $('body').css('overflow', '');
        }, 300);
    }

    function countLines() {
        const text = $('#link_many').val();
        const lines = text.split('\n').filter(line => line.trim() !== '');
        $('#line-count').text(lines.length);
    }

    function openPasteConfirmModal() {
        $('#pasteConfirmModal').css('display', 'flex');
        setTimeout(() => {
            $('#pasteConfirmModal').addClass('active');
        }, 10);
    }

    function closePasteConfirmModal() {
        $('#pasteConfirmModal').removeClass('active');
        setTimeout(() => {
            $('#pasteConfirmModal').css('display', 'none');
        }, 300);
    }

    function extractUID(text) {
        try {
            const url = new URL(text);
            // Handle profile.php?id=...
            if (url.searchParams.has('id')) {
                return url.searchParams.get('id');
            }
            // Handle /groups/123, /posts/123, etc.
            const paths = url.pathname.split('/').filter(p => p !== "");
            for (let i = paths.length - 1; i >= 0; i--) {
                if (/^\d+$/.test(paths[i])) {
                    return paths[i];
                }
            }
            return text;
        } catch (e) {
            // Not a URL, return original text (might be a UID already)
            return text.trim();
        }
    }

    async function confirmPaste() {
        try {
            const text = await navigator.clipboard.readText();
            const cleanText = extractUID(text);
            $('#link').val(cleanText);
            notify("{{ __('Đã dán và xử lý liên kết thành công!') }}", "success");
            closePasteConfirmModal();
            if (typeof handleDataInput === 'function') handleDataInput();
        } catch (err) {
            notify("{{ __('Không thể truy cập clipboard. Vui lòng dán thủ công.') }}", "error");
            closePasteConfirmModal();
        }
    }
    function toggleServiceDescription() {
        const wrapper = $('#service-description-wrapper');
        const textSpan = $('#service-description-toggle-text');
        const icon = $('#service-description-toggle iconify-icon');
        
        if (wrapper.hasClass('collapsed')) {
            wrapper.removeClass('collapsed');
            wrapper.addClass('expanded-manually');
            textSpan.text('{{ __("Thu gọn nội dung") }}');
            icon.attr('icon', 'solar:minus-circle-line-duotone');
        } else {
            wrapper.addClass('collapsed');
            wrapper.removeClass('expanded-manually');
            textSpan.text('{{ __("Xem thêm nội dung") }}');
            icon.attr('icon', 'solar:add-circle-line-duotone');
        }
    }

    $(document).ready(function() {
        const descBox = document.getElementById('description-service');
        if (descBox) {
            const observer = new MutationObserver(function() {
                setTimeout(() => {
                    // Check if content height is greater than our collapse threshold
                    if (descBox.scrollHeight > 140) {
                        $('#service-description-toggle').show();
                        // Auto-collapse if not manually expanded
                        if (!$('#service-description-wrapper').hasClass('expanded-manually')) {
                            $('#service-description-wrapper').addClass('collapsed');
                            $('#service-description-toggle-text').text('{{ __("Xem thêm nội dung") }}');
                            $('#service-description-toggle iconify-icon').attr('icon', 'solar:add-circle-line-duotone');
                        }
                    } else {
                        $('#service-description-toggle').hide();
                        $('#service-description-wrapper').removeClass('collapsed');
                    }
                }, 100);
            });
            observer.observe(descBox, { childList: true, subtree: true, characterData: true });
        }
    });

</script>

<!-- Note Order Modal -->
<div class="order-confirm-modal" id="noteOrderModal" style="display: none;">
    <div class="order-confirm-overlay" onclick="closeNoteOrderModal()"></div>
    <div class="order-confirm-wrapper" style="max-width: 800px;">
        <div class="order-confirm-container">
            <div class="order-confirm-header">
                <div class="d-flex align-items-center">
                    <div class="order-confirm-header-icon me-3 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:shield-warning-bold-duotone" style="font-size: 24px; color: #ff9800;"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="order-confirm-title mb-0">{{ __('Lưu ý trước khi đặt hàng') }}</h4>
                        <p class="text-muted mb-0 fs-7">{{ __('Vui lòng đọc kỹ các quy định sau đây') }}</p>
                    </div>
                </div>
                <button type="button" class="order-confirm-close" onclick="closeNoteOrderModal()">
                    <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                </button>
            </div>
            <div class="order-confirm-body">
                <div class="row g-6">
                    <!-- Section 1 -->
                    <div class="col-12 mb-4">
                        <div class="d-flex align-items-start">
                            <iconify-icon icon="solar:danger-triangle-bold" class="text-danger me-3 fs-3 mt-1"></iconify-icon>
                            <div>
                                <h5 class="fw-bold text-danger mb-2">{{ __('Quy định quan trọng — Vui lòng đọc kỹ trước khi sử dụng') }}</h5>
                                <p class="text-white-50 fs-7 mb-0">{{ __('Tuyệt đối nghiêm cấm các hành vi buff hoặc tạo đơn hàng có nội dung vi phạm pháp luật liên quan đến chính trị, đồi trụy, bạo lực, kích động, phân biệt đối xử hoặc bất kỳ nội dung nào bị cấm theo pháp luật Việt Nam và chính sách hệ thống.') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Section 2 -->
                    <div class="col-12 mb-4">
                        <div class="d-flex align-items-start">
                            <iconify-icon icon="solar:history-bold" class="text-warning me-3 fs-3 mt-1"></iconify-icon>
                            <div>
                                <h5 class="fw-bold text-warning mb-2">{{ __('Về việc đặt hàng ở nhiều nơi cùng một lúc') }}</h5>
                                <p class="text-white-50 fs-7 mb-0">{{ __('Nếu đơn hàng đang được hệ thống xử lý mà bạn tiếp tục đặt cùng một link ở các hệ thống khác, hoặc đặt nhiều đơn chồng lên nhau sẽ dễ dẫn đến tình trạng: Số lượng tăng không đều, thiếu hụt số lượng so với ước tính. Trong những trường hợp này, hệ thống không chịu trách nhiệm giải quyết, hoàn tiền hoặc bù số lượng.') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Section 3 -->
                    <div class="col-12 mb-4">
                        <div class="d-flex align-items-start">
                            <iconify-icon icon="solar:bill-cross-bold" class="text-info me-3 fs-3 mt-1"></iconify-icon>
                            <div>
                                <h5 class="fw-bold text-info mb-2">{{ __('Thông tin đơn hàng bị sai') }}</h5>
                                <p class="text-white-50 fs-7 mb-0">{{ __('Đơn hàng sai link, sai ID, sai thông số cấu hình hoặc tự ý chỉnh sửa khi hệ thống đang chạy sẽ không được hoàn tiền vì lỗi xuất phát từ phía người dùng. Vui lòng kiểm tra kỹ trước khi xác nhận đơn hàng.') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Section 4 -->
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <iconify-icon icon="solar:chat-round-call-bold" class="text-success me-3 fs-3 mt-1"></iconify-icon>
                            <div>
                                <h5 class="fw-bold text-success mb-2">{{ __('Hỗ trợ khi có lỗi phát sinh') }}</h5>
                                <p class="text-white-50 fs-7 mb-0">{{ __('Nếu có lỗi phát sinh từ hệ thống, chúng tôi sẽ kiểm tra và hỗ trợ xử lý. Vui lòng cung cấp đầy đủ: Mã đơn hàng, Link đơn hàng, bằng chứng lỗi (ảnh chụp màn hình/video nếu có).') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 d-flex justify-content-end gap-3 border-top border-white-5">
                <button type="button" class="btn btn-secondary px-6 fw-bold" onclick="closeNoteOrderModal()" style="border-radius: 12px;">{{ __('Đóng') }}</button>
                <button type="button" class="btn btn-primary px-8 fw-bold" onclick="closeNoteOrderModal()" style="border-radius: 12px; background: linear-gradient(135deg, #4680ff 0%, #3563cc 100%); border: none; box-shadow: 0 4px 15px rgba(70, 128, 255, 0.3);">{{ __('Đã hiểu, tiếp tục đặt hàng') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection