<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  data-bs-theme="dark" data-menu-color="dark" >

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ s('favicon') }}">

    <!-- Vendor css -->
    <link href="/admin/assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="/admin/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="/admin/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Old Icons Restored (for backward compatibility with menu) -->
    <link href="/admin-pack/assets/css/icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">

    <!-- Theme Config Js -->
    <script src="/admin/assets/js/config.js"></script>

    <!-- Essential Custom CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Plugins CSS -->


    @yield('styles')
    
    <script>
        const bvq_config = {
            locale: '{{ app()->getLocale() }}',
            timezone: '{{ config('app.timezone') }}',
            currency: @json($currency = json_currency()),
            csrf_token: '{{ csrf_token() }}',
            base_url: '{{ url('/') }}',
            api_url: '{{ url('/api') }}',
            current_url: '{{ url()->current() }}',
        }
        const currencies = @json(\App\Models\Currency::all()->keyBy('code'));
    </script>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <!-- Menu -->
        <!-- Sidenav Menu Start -->
        <div class="sidenav-menu">

            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ s('logo') }}" alt="logo" style="max-height: 40px;"></span>
                    <span class="logo-sm"><img src="{{ s('favicon') }}" alt="small logo" style="max-height: 40px;"></span>
                </span>

                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ s('logo') }}" alt="dark logo" style="max-height: 40px;"></span>
                    <span class="logo-sm"><img src="{{ s('favicon') }}" alt="small logo" style="max-height: 40px;"></span>
                </span>
            </a>

            <!-- Sidebar Hover Menu Toggle Button -->
            <button class="button-sm-hover">
                <i class="ri-circle-line align-middle"></i>
            </button>

            <!-- Full Sidebar Menu Close Button -->
            <button class="button-close-fullsidebar">
                <i class="ri-close-line align-middle"></i>
            </button>

            <div data-simplebar>

                <!-- User (Optional in new theme sidebar, keeping it simple as per request or original functionality) -->
                <!-- We can recreate the user dropdown here if needed, but the new theme topbar handles it well. -->

                <!--- Sidenav Menu -->
                <ul class="side-nav">
                    @include('admin.layouts.menu')
                </ul>

                <div class="clearfix"></div>
            </div>
        </div>
        <!-- Sidenav Menu End -->

        <!-- Topbar Start -->
        <header class="app-topbar">
            <div class="page-container topbar-menu">
                <div class="d-flex align-items-center gap-2">

                    <!-- Brand Logo -->
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <span class="logo-light">
                            <span class="logo-lg"><img src="{{ s('logo') }}" alt="logo"></span>
                            <span class="logo-sm"><img src="{{ s('favicon') }}" alt="small logo"></span>
                        </span>

                        <span class="logo-dark">
                            <span class="logo-lg"><img src="{{ s('logo') }}" alt="dark logo"></span>
                            <span class="logo-sm"><img src="{{ s('favicon') }}" alt="small logo"></span>
                        </span>
                    </a>

                    <!-- Sidebar Menu Toggle Button -->
                    <button class="sidenav-toggle-button px-2">
                        <i class="ri-menu-5-line fs-24"></i>
                    </button>

                    <!-- Horizontal Menu Toggle Button -->
                    <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                        <i class="ri-menu-5-line fs-24"></i>
                    </button>

                    <!-- Topbar Page Title -->
                    <div class="topbar-item d-none d-md-flex">
                        <div>
                            <h4 class="page-title fs-18 fw-bold mb-0">@yield('title')</h4>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <!-- Light/Dark Mode Button -->
                    <div class="topbar-item d-none d-sm-flex">
                        <button class="topbar-link" id="light-dark-mode" type="button">
                            <i class="ri-moon-line fs-22"></i>
                        </button>
                    </div>
                    <!-- User Dropdown -->
                    <div class="topbar-item nav-user">
                        <div class="dropdown">
                            <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                                data-bs-offset="0,25" type="button" aria-haspopup="false" aria-expanded="false">
                                <img src="/admin-pack/assets/images/faces/9.jpg" width="32" class="rounded-circle me-lg-2 d-flex"
                                    alt="user-image">
                                <span class="d-lg-flex flex-column gap-1 d-none">
                                    <h5 class="my-0">{{ Auth::user()->name ?? 'Admin' }}</h5>
                                </span>
                                <i class="ri-arrow-down-s-line d-none d-lg-block align-middle ms-2"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>

                                <!-- item-->
                                <a href="{{ route('settings') }}" class="dropdown-item">
                                    <i class="ri-settings-2-line me-1 fs-16 align-middle"></i>
                                    <span class="align-middle">Settings</span>
                                </a>

                                <!-- item-->

                                <div class="dropdown-divider"></div>

                                <!-- item-->
                                <a href="{{ route('auth.logout') }}" class="dropdown-item active fw-semibold text-danger">
                                    <i class="ri-logout-box-line me-1 fs-16 align-middle"></i>
                                    <span class="align-middle">Sign Out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Topbar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="page-content">
            <div class="page-container">
                 @yield('content')
            </div> <!-- end page-container -->

          

        </div> <!-- end page-content -->
        <!-- ============================================================== -->
        <!-- End Page Content here -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
   


    <!-- Required Js -->
  <script src="/admin/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="/admin/assets/js/app.js"></script>

    <!-- Apex Chart js -->


    <!-- Projects Analytics Dashboard App js -->
    <script src="/admin/assets/js/pages/dashboard.js"></script>

    
    <style>
        /* Dashboard Widgets (Restoring Old Theme Look) */
        .hrm-main-card {
            border: 0;
            margin-bottom: 24px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            position: relative;
            overflow: hidden;
        }
        .hrm-main-card .card-body {
            padding: 1.5rem;
        }
        .hrm-main-card.primary { border-top: 3px solid #0d6efd; }
        .hrm-main-card.secondary { border-top: 3px solid #0dcaf0; }
        .hrm-main-card.warning { border-top: 3px solid #ffc107; }
        .hrm-main-card.danger { border-top: 3px solid #dc3545; }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            font-size: 1.25rem;
            border-radius: 50%;
            color: #fff;
        }
        .bg-primary-transparent { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; padding: 2px 8px; border-radius: 4px; }
        .bg-info-transparent { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; padding: 2px 8px; border-radius: 4px; }
        .bg-warning-transparent { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; padding: 2px 8px; border-radius: 4px; }
        .bg-danger-transparent { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 2px 8px; border-radius: 4px; }

        .blink { animation: blinker 1.5s linear infinite; }
        @keyframes blinker { 50% { opacity: 0; } }
        
        .badge-live { background-color: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; }
        
        /* Fix Layout Spacing */
        .page-header { margin-bottom: 1.5rem; }
    </style>

    <!-- Other Libs -->
    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        if ($.fn.dataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
                "lengthMenu": [[10, 25, 50, 100,1000, -1], [10, 25, 50, 100,1000, "Tất cả"]],
                "language": {
                    "sProcessing": "Đang Tải Dữ Liệu...",
                    "sLengthMenu": "Xem _MENU_ mục",
                    "sZeroRecords": "<div class='text-center p-3'><svg width='184' height='152' viewBox='0 0 184 152' xmlns='http://www.w3.org/2000/svg'><g fill='none' fill-rule='evenodd'><g transform='translate(24 31.67)'><ellipse fill-opacity='.8' fill='#F5F5F7' cx='67.797' cy='106.89' rx='67.797' ry='12.668'></ellipse><path d='M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z' fill='#AEB8C2'></path><path d='M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z' fill='#AEB8C2' transform='translate(13.56)'></path><path d='M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z' fill='#F5F5F7'></path><path d='M42.678 9.953h50.237a2 2 0 0 1 2-2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z' fill='#DCE0E6'></path></g><path d='M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z' fill='#DCE0E6'></path><g transform='translate(149.65 15.383)' fill='#FFF'><ellipse cx='20.654' cy='3.167' rx='2.849' ry='2.815'></ellipse><path d='M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z'></path></g></g></svg><div class='mt-2'>Không tìm thấy dòng nào phù hợp</div></div>",
                    "sEmptyTable": "<div class='text-center p-3'><svg width='184' height='152' viewBox='0 0 184 152' xmlns='http://www.w3.org/2000/svg'><g fill='none' fill-rule='evenodd'><g transform='translate(24 31.67)'><ellipse fill-opacity='.8' fill='#F5F5F7' cx='67.797' cy='106.89' rx='67.797' ry='12.668'></ellipse><path d='M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z' fill='#AEB8C2'></path><path d='M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z' fill='#AEB8C2' transform='translate(13.56)'></path><path d='M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z' fill='#F5F5F7'></path><path d='M42.678 9.953h50.237a2 2 0 0 1 2-2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z' fill='#DCE0E6'></path></g><path d='M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z' fill='#DCE0E6'></path><g transform='translate(149.65 15.383)' fill='#FFF'><ellipse cx='20.654' cy='3.167' rx='2.849' ry='2.815'></ellipse><path d='M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z'></path></g></g></svg><div class='mt-2'>Không tìm thấy dữ liệu trong bảng</div></div>",
                    "sInfo": "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty": "Đang xem 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(lọc từ tổng số _MAX_ mục)",
                    "sSearch": "Tìm kiếm:",
                    "oPaginate": {
                        "sFirst": "Đầu", "sPrevious": "Trước", "sNext": "Tiếp", "sLast": "Cuối"
                    }
                }
            });
        }
    </script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const bvq_config = {
            locale: '{{ app()->getLocale() }}',
            timezone: '{{ config('app.timezone') }}',
            currency: @json($currency = json_currency()),
            csrf_token: '{{ csrf_token() }}',
            base_url: '{{ url('/') }}',
            api_url: '{{ url('/api') }}',
            current_url: '{{ url()->current() }}',
        }

        const currencies = {
            "VND": @json($currency = json_currency("VND")),
            "USD": @json($currency = json_currency("USD")),
        }
    </script>
    <div id="fui-toast"></div>
    <script src="{{ asset('bvq/assets/js/bvq.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'Tôi Đã Hiểu',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'Tôi Đã Hiểu',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        </script>
    @endif

    @yield('script')
    
   
    <!-- Cron Modal -->
    @include('admin.layouts.link-cron')

</body>
</html>
