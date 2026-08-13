@extends('client.layouts.master')
@section('title', 'Dòng tiền')
@section('content')
    @include('client.layouts.breadcrumb', ['title' => 'Dòng tiền'])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body px-3">
                    <div class="table-responsive">
                        <table class="table w-100">
                            <thead>
                                <tr class="fw-semibold text-gray-700 fs-6">
                                    <th>{{ __("Thời gian") }}</th>
                                    <th>{{ __("Nội dung") }}</th>
                                    <th>{{ __("Chi tiết") }}</th>
                                    <th>{{ __("Số tiền") }}</th>
                                    <th>{{ __("Số dư") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $value)
                                    <tr class="py-1 fs-6">
                                        <td>
                                            {{ $value->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td>
                                            {{ $value->description }}
                                        </td>
                                        <td>
                                            {{ $value->transaction_code }}
                                        </td>
                                        <td>
                                            @if ($value->type == 'sub')
                                                <span class="text-danger">-
                                                    {{ convert_currency($value->amount, Auth::user()->currency, false) }}</span>
                                            @else
                                                <span class="text-success">+
                                                    {{ convert_currency($value->amount, Auth::user()->currency, false) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ convert_currency($value->balance_after, Auth::user()->currency, false) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $transactions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
