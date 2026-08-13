@extends('client.layouts.ablepro')
@section('title', 'Able Pro Test')
@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Able Pro Test</li>
                </ul>
            </div>
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Test Page - New Theme</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Hello Card</h5>
            </div>
            <div class="card-body">
                <p>Hello! This is the Able Pro theme integration test.</p>
                <button class="btn btn-primary">Primary Button</button>
                <button class="btn btn-secondary">Secondary Button</button>
                <button class="btn btn-danger">Danger Button</button>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
    
    <div class="col-xl-4 col-md-6">
        <div class="card bg-secondary-dark dashnum-card text-white overflow-hidden">
        <span class="round small"></span>
        <span class="round big"></span>
        <div class="card-body">
            <div class="row">
            <div class="col">
                <div class="avtar avtar-lg">
                <i class="text-white ti ti-credit-card"></i>
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                <a type="button" class="btn btn-primary">Check</a>
                </div>
            </div>
            </div>
            <span class="text-white d-block f-34 f-w-500 my-2">1050 <i class="ti ti-arrow-up-right-circle opacity-50"></i></span>
            <p class="mb-0 opacity-50">Total Orders</p>
        </div>
        </div>
    </div>
</div>
@endsection
