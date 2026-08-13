@extends('client.layouts.master')
@section('title', 'Bài viết')
@section('content')
    <div class="row">
        @foreach ($posts as $post)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-0 radius-12 overflow-hidden">
                    <div class="card-body p-3">
                        <a href="{{ route('post', $post->slug) }}" class="w-100 max-h-194-px radius-8 overflow-hidden">
                            <img src="{{ asset($post->image) }}" alt="" class="w-100 h-100 object-fit-cover bg-center">
                        </a>
                        <div class="mt-20">
                            <h6 class="mb-3">
                                <a href="{{ route('post', $post->slug) }}"
                                    class="text-line-2 text-hover-primary-600 text-lg transition-2">
                                    {{ $post->title }}
                                </a>
                            </h6>
                            <a href="{{ route('post', $post->slug) }}"
                                class="d-flex align-items-center gap-8 fw-semibold text-neutral-900 text-hover-primary-600 transition-2">
                                {{ __("Xem thêm") }}
                                <i class="ri-arrow-right-double-line text-xl d-flex line-height-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
