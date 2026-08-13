<?php

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Http\Controllers\Auth\AppAuthController;
use App\Http\Controllers\Client\AppClientController;
use App\Http\Controllers\Client\OrderClientController;
use App\Http\Controllers\Client\ProductClientController;
use App\Http\Controllers\Client\TicketClientController;
use App\Http\Controllers\Client\UpdateDataController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/CronJobs/RunAll', \App\Http\Controllers\Cron\RunAllCronJobController::class)->name('cron.run-all');
use App\Http\Controllers\UpdateController;

Route::get('/', function () {
    $landing = s('landing_page') ?? 'default';

    if ($landing === 'none') {
        return Auth::check()
            ? redirect()->route('new')   // Nếu login rồi → dashboard
            : redirect()->route('auth.login'); // Nếu chưa → login
    }

    if ($landing === 'default') return view('landing');
    if ($landing === 'modern')  return view('landing1');
    if ($landing === 'classic') return view('landing2');
    if ($landing === 'top')     return view('landing3');
    if ($landing === 'pro')     return view('landing4');
    if ($landing === 'vip')     return view('landing5');

    // fallback
    return view('landing');
})->name('landing');


// Ví dụ route khác
Route::middleware(['auth'])->group(function () {
    Route::get('/new', function () {
        return view('new');
    })->name('new');
});

Route::get('/ref/{username}', [AppAuthController::class, 'ref'])->name('ref');

Route::prefix('/')->middleware(['config'])->group(function () {
    Route::get('/new', [AppClientController::class, 'new'])->name('new');
    Route::get('/services', [AppClientController::class, 'services'])->name('services');
    Route::get('/apidoc', [AppClientController::class, 'apidoc'])->name('apidoc');
    Route::get('/service/stats/{id}', [AppClientController::class, 'getServiceStats'])->name('service.stats');
});

Route::prefix('/')->middleware(['auth', 'config'])->group(function () {
    Route::get('/statistics', [AppClientController::class, 'statistics'])->name('statistics');
    Route::get('/mass', [AppClientController::class, 'mass'])->name('mass');
    Route::post('/request-product', [OrderClientController::class, 'requestProduct'])->name('request-product');
    Route::post('/check-discount', [OrderClientController::class, 'checkDiscount'])->name('check-discount');
    Route::post('/order/action', [OrderClientController::class, 'action'])->name('order.action');
    Route::post('/product/review', [ProductClientController::class, 'storeReview'])->name('review.store');
    Route::get('/orders', [AppClientController::class, 'orders'])->name('orders');
    Route::get('/subscriptions', [AppClientController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/drip-feed', [AppClientController::class, 'dripfeed'])->name('drip-feed');
    Route::get('/refill', [AppClientController::class, 'refill'])->name('refill');

    Route::get('/addfuns', [AppClientController::class, 'addfuns'])->name('addfuns');
    Route::post('/addfuns', [AppClientController::class, 'addfunsPost'])->name('addfuns.post');
    Route::post('/deposit/create', [AppClientController::class, 'depositCreate'])->name('deposit.create');
    Route::get('/deposit/pay/{id}', [AppClientController::class, 'depositPay'])->name('deposit.pay');
    Route::get('/recharge/status/{id}', [AppClientController::class, 'rechargeStatus'])->name('recharge.status');
    Route::get('/cashflow', [AppClientController::class, 'cashflow'])->name('cashflow');

    Route::get('/settings', [AppClientController::class, 'settings'])->name('settings');
    Route::post('/change-password', [UpdateDataController::class, 'changePassword'])->name('change-password');

    Route::get('/posts', [AppClientController::class, 'posts'])->name('posts');
    Route::get('/post/{slug}', [AppClientController::class, 'postDetail'])->name('post');

    Route::get('/affiliate', [AppClientController::class, 'affiliate'])->name('affiliate');
    Route::get('/childpanel', [AppClientController::class, 'childpanel'])->name('childpanel');

    Route::get('/products', [ProductClientController::class, 'index'])->name('products');
    Route::get('/products/{slug}', [ProductClientController::class, 'detail'])->name('product.detail');
    Route::get('/products_orders', [ProductClientController::class, 'orders'])->name('products_orders');

    Route::get('/tickets', [TicketClientController::class, 'index'])->name('tickets');
    Route::post('/create-ticket', [TicketClientController::class, 'create'])->name('create-ticket');
    Route::get('/tickets/{id}', [TicketClientController::class, 'detail'])->name('ticket.detail');
    Route::post('/tickets/{id}/reply', [TicketClientController::class, 'reply'])->name('ticket.reply');

    Route::post('/request-action', [UpdateDataController::class, 'requestAction'])->name('request-action');

    Route::get('/update', [AppClientController::class, 'update'])->name('update');

    Route::get('/auto-update', [UpdateController::class, 'autoUpdate']);
    Route::get('/auth/logout', function () {
        Auth::logout();
        return redirect()->route('auth.login')->with('success', 'Đăng xuất thành công');
    })->name('auth.logout');

    Route::post('/start-now/tool/get-content', [OrderClientController::class, 'getContent'])->name('tool.get-content');

    // Bot Routes
    // Bot Routes
    Route::prefix('bot')->group(function () {
        Route::get('/list', [ \App\Http\Controllers\Client\BotClientController::class, 'index'])->name('bot.list');
        Route::get('/new', [ \App\Http\Controllers\Client\BotClientController::class, 'create'])->name('bot.new');
        Route::post('/store', [ \App\Http\Controllers\Client\BotClientController::class, 'store'])->name('bot.store');
        Route::post('/check-token', [ \App\Http\Controllers\Client\BotClientController::class, 'checkToken'])->name('bot.check_token');
    });

    // Bot API Routes (for JS integration)
    Route::prefix('api/services/bot')->group(function () {
        Route::post('/create', [ \App\Http\Controllers\Client\BotClientController::class, 'createApi'])->name('bot.api.create');
        Route::post('/list', [ \App\Http\Controllers\Client\BotClientController::class, 'listBotsApi'])->name('bot.api.list');
        Route::post('/delete', [ \App\Http\Controllers\Client\BotClientController::class, 'deleteBotApi'])->name('bot.api.delete');
        Route::post('/checklive', [ \App\Http\Controllers\Client\BotClientController::class, 'checkLiveApi'])->name('bot.api.checklive');
        Route::post('/viewlog', [ \App\Http\Controllers\Client\BotClientController::class, 'viewLogApi'])->name('bot.api.viewlog');
        Route::post('/extend', [ \App\Http\Controllers\Client\BotClientController::class, 'extendApi'])->name('bot.api.extend');
        Route::post('/update', [ \App\Http\Controllers\Client\BotClientController::class, 'updateApi'])->name('bot.api.update');
        Route::post('/checkstate', [ \App\Http\Controllers\Client\BotClientController::class, 'checkStateApi'])->name('bot.api.checkstate');
    });

    // Client Cron Job Routes
    Route::prefix('cron')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\CronClientController::class, 'index'])->name('cron');
        // Alias for compatibility if needed (internal links might use this)
        Route::get('/index', [\App\Http\Controllers\Client\CronClientController::class, 'index'])->name('client.cron.index');
        Route::post('/store', [\App\Http\Controllers\Client\CronClientController::class, 'store'])->name('client.cron.store');
        Route::post('/update/{id}', [\App\Http\Controllers\Client\CronClientController::class, 'update'])->name('client.cron.update');
        Route::post('/renew/{id}', [\App\Http\Controllers\Client\CronClientController::class, 'renew'])->name('client.cron.renew');
        Route::get('/logs/{id}', [\App\Http\Controllers\Client\CronClientController::class, 'logs'])->name('client.cron.logs');
        Route::get('/status/{id}/{status}', [\App\Http\Controllers\Client\CronClientController::class, 'status'])->name('client.cron.status');
        Route::post('/bulk-status', [\App\Http\Controllers\Client\CronClientController::class, 'bulkStatus'])->name('client.cron.bulk_status');
        Route::post('/bulk-delete', [\App\Http\Controllers\Client\CronClientController::class, 'bulkDelete'])->name('client.cron.bulk_delete');
        Route::get('/delete/{id}', [\App\Http\Controllers\Client\CronClientController::class, 'destroy'])->name('client.cron.destroy');
    });

    // Cron Job Management (Admin)
    Route::prefix('admin/cron')->middleware('admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CronAdminController::class, 'index'])->name('admin.cron.index');
        Route::get('/delete/{id}', [\App\Http\Controllers\Admin\CronAdminController::class, 'delete'])->name('admin.cron.delete');
        
        Route::prefix('server')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CronAdminController::class, 'serverIndex'])->name('admin.cron.server.index');
            Route::post('/store', [\App\Http\Controllers\Admin\CronAdminController::class, 'serverStore'])->name('admin.cron.server.store');
            Route::post('/update/{id}', [\App\Http\Controllers\Admin\CronAdminController::class, 'serverUpdate'])->name('admin.cron.server.update');
            Route::get('/delete/{id}', [\App\Http\Controllers\Admin\CronAdminController::class, 'serverDelete'])->name('admin.cron.server.delete');
        });
    });
});

Route::get('/errors/scam', function () {
    return view('errors.scam', [
        'domain' => request()->getHost(),
        'parent_domain' => env('MAIN_SITE', request()->getHost())
    ]);
})->name('errors.scam');
