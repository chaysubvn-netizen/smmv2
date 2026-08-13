@php
    $siteTitle = s('title') ?? 'SMM Panel';
    $description = s('description') ?? 'SMM Panel';
    $keywords = s('keywords') ?? 'SMM Panel';
    $author = s('author') ?? 'Bùi Văn Quyết';
    $favicon = s('favicon') ? asset(s('favicon')) : asset('favicon.ico');
    $ogImage = s('og_image') ? asset(s('og_image')) : asset('default-og.jpg');
@endphp

@if(isset($title) && $title)
    <title>{{ $siteTitle }}</title>
@endif

<link rel="icon" href="{{ $favicon }}" type="image/x-icon">

<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $author }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $siteTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteTitle }}">
<meta property="og:locale" content="vi_VN">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $siteTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">
