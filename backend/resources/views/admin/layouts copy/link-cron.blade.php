<div class="modal fade" id="linkCronLoad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="" method="POST" data-ajax="true" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Danh sách link Cron</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th>*</th>
                                <th>Tên</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (env('MAIN_SITE') == request()->getHost())
                                <tr>
                                    <td>*</td>
                                    <td>
                                        <h6 class="text-danger">Cập nhật hệ thống (Bắt buộc chạy)</h6>
                                    </td>
                                    <td>
                                        <a class="text-primary" href="{{ route('cron.system.schedule') }}"
                                            target="_blank">{{ route('cron.system.schedule') }}</a>
                                    </td>
                                </tr>
                                                                 <tr>
                                        <td>*</td>
                                        <td>(Cron Status Order All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.order.status') }}"
                                                target="_blank">{{ route('cron.order.status') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>(Cron Service All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.service') }}"
                                                target="_blank">{{ route('cron.service') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>(Cron Refill All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.order.refill') }}"
                                                target="_blank">{{ route('cron.order.refill') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>(Cron Dripfeed All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.order.dripfeed') }}"
                                                target="_blank">{{ route('cron.order.dripfeed') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>(Cron Schedule Order All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.order.schedule') }}"
                                                target="_blank">{{ route('cron.order.schedule') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>(Cron Schedule Service All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.service.schedule') }}"
                                                target="_blank">{{ route('cron.service.schedule') }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>(Cron Avg Time Service All Provider)</td>
                                        <td>
                                            <a class="text-primary"
                                                href="{{ route('cron.service.update.avg.time') }}"
                                                target="_blank">{{ route('cron.service.update.avg.time') }}</a>
                                        </td>
                                    </tr>
                                <tr>
                                    <td>*</td>
                                    <td>Cập nhật Dịch vụ (Child Site)</td>
                                    <td>
                                        <a class="text-primary" href="{{ route('cron.service.update') }}"
                                            target="_blank">{{ route('cron.service.update') }}</a>
                                    </td>
                                </tr>
                            @else
                            @endif
                            <tr>
                                <td>*</td>
                                <td>Nạp Bank (Mbbank)</td>
                                <td>
                                    <a class="text-primary"
                                        href="{{ route('cron.recharge.bank', ['bank' => 'mbbank']) }}"
                                        target="_blank">{{ route('cron.recharge.bank', ['bank' => 'mbbank']) }}</a>
                            </tr>
                            <tr>
                                <td>*</td>
                                <td>Nạp Bank (Acb)</td>
                                <td>
                                    <a class="text-primary" href="{{ route('cron.recharge.bank', ['bank' => 'Acb']) }}"
                                        target="_blank">{{ route('cron.recharge.bank', ['bank' => 'Acb']) }}</a>
                            </tr>
                            <tr>
                                <td>*</td>
                                <td>Nạp Bank (Vcb)</td>
                                <td>
                                    <a class="text-primary" href="{{ route('cron.recharge.bank', ['bank' => 'Vcb']) }}"
                                        target="_blank">{{ route('cron.recharge.bank', ['bank' => 'Vcb']) }}</a>
                            </tr>
                            <tr>
                                <td>*</td>
                                <td>Nạp Bank (Viettinbank)</td>
                                <td>
                                    <a class="text-primary"
                                        href="{{ route('cron.recharge.bank', ['bank' => 'Viettinbank']) }}"
                                        target="_blank">{{ route('cron.recharge.bank', ['bank' => 'Viettinbank']) }}</a>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </form>
    </div>
</div>
