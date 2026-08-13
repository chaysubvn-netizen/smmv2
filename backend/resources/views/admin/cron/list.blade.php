@extends('client.layouts.master')
@section('title', 'Danh sách User Cron')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Danh sách Cron Job của Thành viên</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>URL</th>
                                <th>Method</th>
                                <th>Server</th>
                                <th>Thời gian</th>
                                <th>Hết hạn</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($crons as $cron)
                            <tr>
                                <td>{{ $cron->id }}</td>
                                <td>{{ $cron->user->username ?? 'Unknown' }}</td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <a href="{{ $cron->url }}" target="_blank">{{ $cron->url }}</a>
                                </td>
                                <td>{{ $cron->method }}</td>
                                <td>{{ $cron->server->name ?? 'Deleted' }}</td>
                                <td>{{ $cron->loop_time }}s</td>
                                <td>{{ $cron->expired_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($cron->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">{{ $cron->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.cron.delete', $cron->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Xóa cron này?')">Xóa</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $crons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
