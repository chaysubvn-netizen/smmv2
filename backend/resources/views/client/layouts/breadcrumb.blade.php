<!--begin::Page title-->
<h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __($title ?? 'Trang chủ') }}</h1>
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('new') }}" class="text-muted text-hover-primary">{{ __('Trang chủ') }}</a>
    </li>
    @if(isset($title) && $title != 'Trang chủ')
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-300 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">{{ __($title) }}</li>
    @endif
</ul>
<!--end::Page title-->
