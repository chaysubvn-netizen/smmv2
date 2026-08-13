@extends('client.layouts.master')
@section('title', $post->title)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
