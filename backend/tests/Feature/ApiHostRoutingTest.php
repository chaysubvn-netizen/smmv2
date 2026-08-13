<?php

test('api host redirects web routes to the frontend', function () {
    $response = $this
        ->withServerVariables([
            'HTTP_HOST' => 'api.top1smm.vn',
            'HTTPS' => 'on',
        ])
        ->get('/admin/dashboard?period=today');

    $response
        ->assertStatus(308)
        ->assertRedirect('https://top1smm.vn/admin/dashboard?period=today');
});

test('api host keeps the run all cron route on Laravel', function () {
    $response = $this
        ->withServerVariables([
            'HTTP_HOST' => 'api.top1smm.vn',
            'HTTPS' => 'on',
        ])
        ->get('/CronJobs/RunAll?secret=invalid');

    $response->assertForbidden();
});

test('admin cron link uses the Laravel api host', function () {
    config()->set('cron.secret', 'test-secret');

    $request = \Illuminate\Http\Request::create(
        'https://api.top1smm.vn/api/admin/cron-link',
        'GET'
    );
    $request->setUserResolver(fn () => (object) ['role' => 'admin']);

    $response = (new \App\Http\Controllers\Api\ApiAdminController())->cronLink($request);

    expect($response->getData(true)['data']['url'])
        ->toBe('https://api.top1smm.vn/CronJobs/RunAll?secret=test-secret');
});
