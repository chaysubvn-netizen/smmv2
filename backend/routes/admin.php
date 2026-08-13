<?php

use App\Http\Controllers\Admin\Affiliate\AffiliateAdminController;
use App\Http\Controllers\Admin\AppAdminController;
use App\Http\Controllers\Admin\Bank\AccountBankController;
use App\Http\Controllers\Admin\Config\GeneralController;
use App\Http\Controllers\Admin\Data\DataAdminController;
use App\Http\Controllers\Admin\LoadDataController;
use App\Http\Controllers\Admin\Post\PostController;
use App\Http\Controllers\Admin\Product\CategoryProductController;
use App\Http\Controllers\Admin\Product\ProductAdminController;
use App\Http\Controllers\Admin\Provider\ProviderController;
use App\Http\Controllers\Admin\Services\CategoryController;
use App\Http\Controllers\Admin\Services\ImportControler;
use App\Http\Controllers\Admin\Services\PlatformController;
use App\Http\Controllers\Admin\Services\ServiceController;
use App\Http\Controllers\Admin\System\SystemUpdateController;
use App\Http\Controllers\Admin\Ticket\TicketAdminController;
use App\Http\Controllers\Admin\User\UserAdminController;
use App\Http\Controllers\Admin\Website\ChildpanelAdminController;
use App\Http\Controllers\Admin\Website\CurrencyController;
use App\Http\Controllers\Admin\FbTokenController;
use App\Http\Controllers\Cron\System\FbTokenCronController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/dashboard', [AppAdminController::class, 'index'])->name('admin.dashboard');

// System Update routes
Route::prefix('/system_setting')->group(function () {
    Route::get('/', [SystemUpdateController::class, 'index'])->name('admin.system.update');
    Route::post('/check_update', [SystemUpdateController::class, 'apiCheckUpdate'])->name('admin.system.update.check');
    Route::get('/check_update', [SystemUpdateController::class, 'apiCheckUpdate'])->name('admin.system.update.check.show');
    Route::post('/run_update', [SystemUpdateController::class, 'runUpdate'])->name('admin.system.update.upgrade');
});
Route::get('/provider-statistic', [AppAdminController::class, 'providerStatistic'])->name('admin.provider-statistic');
Route::get('/top-services', [AppAdminController::class, 'topServices'])->name('admin.top-services');
Route::get('/leaderboard', [AppAdminController::class, 'leaderboard'])->name('admin.leaderboard');
Route::post('/ai-chat', [AppAdminController::class, 'aiChat'])->name('admin.ai-chat');

// platform
Route::prefix('/platforms')->group(function () {
    Route::get('/', [PlatformController::class, 'index'])->name('admin.platforms.index');
    Route::post('/create', [PlatformController::class, 'create'])->name('admin.platforms.create');
    Route::get('/edit/{id}', [PlatformController::class, 'edit'])->name('admin.platforms.edit');
    Route::post('/update/{id}', [PlatformController::class, 'update'])->name('admin.platforms.update');
});
// phân loại
Route::prefix('/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::post('/update/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
});

// providers
Route::prefix('/providers')->group(function () {
    Route::get('/', [ProviderController::class, 'index'])->name('admin.providers.index');
    Route::get('/create', [ProviderController::class, 'create'])->name('admin.providers.create');
    Route::post('/store', [ProviderController::class, 'store'])->name('admin.providers.store');
    Route::get('/edit/{id}', [ProviderController::class, 'edit'])->name('admin.providers.edit');
    Route::post('/update/{id}', [ProviderController::class, 'update'])->name('admin.providers.update');
    Route::post('/update-balance/{id}', [ProviderController::class, 'updateBalance'])->name('admin.providers.update-balance');
    Route::post('/update-prices/{id}', [ProviderController::class, 'updatePrices'])->name('admin.providers.update-prices');
});

// services
Route::prefix('/services')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/create', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/edit/{id}', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::post('/update/{id}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::get('/edit-rate/{id}', [ServiceController::class, 'editRate'])->name('admin.services.edit-rate');
    Route::post('/update-rate/{id}', [ServiceController::class, 'updateRate'])->name('admin.services.update-rate');

    Route::get('/update-rates', [ServiceController::class, 'updateRates'])->name('admin.services.update-rates');
    Route::get('/update-attributes', [ServiceController::class, 'updateAttributes'])->name('admin.services.update-attributes');
    Route::get('/update-description', [ServiceController::class, 'updateDescription'])->name('admin.services.update-description');
    Route::post('/update-services', [ServiceController::class, 'updateServices'])->name('admin.services.update-services');
});

// discounts
Route::resource('discounts', \App\Http\Controllers\Admin\DiscountController::class)->names([
    'index' => 'admin.discounts.index',
    'store' => 'admin.discounts.store',
    'update' => 'admin.discounts.update',
    'destroy' => 'admin.discounts.destroy',
]);
// Nhập dịch vụ từ phía nhà cung cấp')
Route::prefix('/import')->group(function () {
    Route::get('/services', [ImportControler::class, 'services'])->name('admin.import.services.index');
    Route::post('/services/add', [ImportControler::class, 'addServices'])->name('admin.import.services.add');
    Route::get('/categories', [ImportControler::class, 'categories'])->name('admin.import.categories.index');
    Route::post('/categories/add', [ImportControler::class, 'add'])->name('admin.import.categories.add');
});

// products
Route::prefix('/products')->group(function(){
    Route::get('/', [ProductAdminController::class, 'index'])->name('admin.products.index');
    Route::get('/create', [ProductAdminController::class, 'create'])->name('admin.products.create');
    Route::post('/create', [ProductAdminController::class, 'store'])->name('admin.products.store');
    Route::get('/edit/{id}', [ProductAdminController::class, 'edit'])->name('admin.products.edit');
    Route::post('/update/{id}', [ProductAdminController::class, 'update'])->name('admin.products.update');
    Route::post('/stock/create', [ProductAdminController::class, 'stockCreate'])->name('admin.products.stock.create');
    Route::get('/stock/{id}', [ProductAdminController::class, 'stock'])->name('admin.products.stock');

    Route::get('/categories', [CategoryProductController::class, 'index'])->name('admin.products.categories.index');
    Route::post('/categories/create', [CategoryProductController::class, 'create'])->name('admin.products.categories.create');
    Route::get('/categories/edit/{id}', [CategoryProductController::class, 'edit'])->name('admin.products.categories.edit');
    Route::post('/categories/update/{id}', [CategoryProductController::class, 'update'])->name('admin.products.categories.update');

    ROute::get('/orders', [ProductAdminController::class, 'orders'])->name('admin.products.orders.index');
    Route::get('/orders/{id}', [ProductAdminController::class, 'orderDetail'])->name('admin.products.orders.detail');
});

// website
Route::prefix('/config')->group(function () {
    Route::get('/general', [GeneralController::class, 'index'])->name('admin.config.general.index');
    Route::get('/api-keys', [GeneralController::class, 'apiKeys'])->name('admin.config.api-keys.index');
    Route::get('/telegram-link', [GeneralController::class, 'telegramLink'])->name('admin.config.telegram-link.index');
    Route::post('/telegram-link', [GeneralController::class, 'updateTelegramLink'])->name('admin.config.telegram-link.update');
    Route::post('/telegram-link/webhook', [GeneralController::class, 'registerTelegramWebhook'])->name('admin.config.telegram-link.webhook');
    Route::post('/update', [GeneralController::class, 'update'])->name('admin.config.general.update');

    Route::get('/notifications', [GeneralController::class, 'notifications'])->name('admin.config.notifications');

    // System
    Route::prefix('system')->group(function () {
        Route::get('/ip-block', [\App\Http\Controllers\Admin\System\IpBlockController::class, 'index'])->name('admin.system.ip-block.index');
        Route::post('/ip-block', [\App\Http\Controllers\Admin\System\IpBlockController::class, 'store'])->name('admin.system.ip-block.store');
        Route::delete('/ip-block/{id}', [\App\Http\Controllers\Admin\System\IpBlockController::class, 'destroy'])->name('admin.system.ip-block.destroy');
    });
});

//users
Route::prefix('/users')->group(function () {
    Route::get('/', [UserAdminController::class, 'index'])->name('admin.users.index');
    ROute::get('/edit/{id}', [UserAdminController::class, 'edit'])->name('admin.users.edit');
    Route::post('/update/{id}', [UserAdminController::class, 'update'])->name('admin.users.update');
    Route::post('/update-balance/{id}', [UserAdminController::class, 'updateBalance'])->name('admin.users.update-balance');
});

// banks
Route::prefix('/banks')->group(function () {
    Route::get('/accounts', [AccountBankController::class, 'index'])->name('admin.banks.accounts.index');
    Route::post('/accounts/create', [AccountBankController::class, 'create'])->name('admin.banks.accounts.create');
    Route::get('/accounts/edit/{id}', [AccountBankController::class, 'edit'])->name('admin.banks.accounts.edit');
    Route::post('/accounts/update/{id}', [AccountBankController::class, 'update'])->name('admin.banks.accounts.update');
    Route::delete('/accounts/delete/{id}', [AccountBankController::class, 'delete'])->name('admin.banks.accounts.delete');

    // tiền thưởng
    Route::get('/bonus', [AccountBankController::class, 'bonus'])->name('admin.banks.bonus.index');
    Route::post('/bonus/create', [AccountBankController::class, 'bonusCreate'])->name('admin.banks.bonus.create');
    Route::get('/bonus/edit/{id}', [AccountBankController::class, 'bonusEdit'])->name('admin.banks.bonus.edit');
});

//currency
Route::prefix('/currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('admin.currencies.index');
    Route::post('/create', [CurrencyController::class, 'create'])->name('admin.currencies.create');
    Route::get('/edit/{id}', [CurrencyController::class, 'edit'])->name('admin.currencies.edit');
    Route::post('/update/{id}', [CurrencyController::class, 'update'])->name('admin.currencies.update');
    Route::delete('/delete/{id}', [CurrencyController::class, 'delete'])->name('admin.currencies.delete');
});

// bài viết
Route::prefix('/posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('admin.posts.index');
    Route::post('/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::get('/edit/{id}', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::post('/update/{id}', [PostController::class, 'update'])->name('admin.posts.update');
});

//
Route::prefix('/affiliates')->group(function () {
    Route::get('/', [AffiliateAdminController::class, 'affiliates'])->name('admin.affiliate.index');
});

// site con
Route::prefix('/childpanels')->group(function () {
    Route::get('/', [ChildpanelAdminController::class, 'index'])->name('admin.childpanels.index');
    Route::post('/active', [ChildpanelAdminController::class, 'active'])->name('admin.childpanels.active');
    Route::post('/toggle-scam', [ChildpanelAdminController::class, 'toggleScam'])->name('admin.childpanels.scam');
    Route::post('/settings', [ChildpanelAdminController::class, 'updateSettings'])->name('admin.childpanels.settings');
    Route::post('/{id}/update-status', [ChildpanelAdminController::class, 'updateStatus'])->name('admin.childpanels.update-status');
    Route::get('/{id}/login', [ChildpanelAdminController::class, 'loginAsAdmin'])->name('admin.childpanels.login');
    Route::get('/{id}/statistics', [ChildpanelAdminController::class, 'statistics'])->name('admin.childpanels.statistics');
    Route::delete('/{id}', [ChildpanelAdminController::class, 'destroy'])->name('admin.childpanels.destroy');
});

// tickets
Route::prefix('/tickets')->group(function () {
    Route::get('/', [TicketAdminController::class, 'index'])->name('admin.tickets.index');
    Route::get('/messages', [TicketAdminController::class, 'messages'])->name('admin.tickets.messages');
    Route::get('/edit/{id}', [TicketAdminController::class, 'edit'])->name('admin.tickets.edit');
    Route::get('/detail/{id}', [TicketAdminController::class, 'detail'])->name('admin.tickets.detail');
    Route::post('/update/{id}', [TicketAdminController::class, 'update'])->name('admin.tickets.update');
    Route::post('/reply/{id}', [TicketAdminController::class, 'reply'])->name('admin.tickets.reply');
    Route::post('/fetch-messages/{id}', [TicketAdminController::class, 'fetchMessages'])->name('admin.tickets.fetch-messages');
});

Route::prefix('/data')->group(function () {
    Route::get('/orders', [DataAdminController::class, 'orders'])->name('admin.data.orders');
    Route::get('/orders/export', [DataAdminController::class, 'exportOrders'])->name('admin.data.orders.export');
    Route::get('/orders/{id}', [DataAdminController::class, 'orderDetail'])->name('admin.data.orders.detail');
    Route::post('/orders/update/{id}', [DataAdminController::class, 'orderUpdate'])->name('admin.data.orders.update');
    Route::post('/orders/clean', [DataAdminController::class, 'cleanOrders'])->name('admin.data.orders.clean');

    Route::get('deposits', [DataAdminController::class, 'deposits'])->name('admin.data.deposits');
    Route::post('deposits/approve', [DataAdminController::class, 'rechargeApprove'])->name('admin.data.deposits.approve');
    Route::post('deposits/cancel', [DataAdminController::class, 'rechargeCancel'])->name('admin.data.deposits.cancel');
    Route::get('transactions', [DataAdminController::class, 'transactions'])->name('admin.data.transactions');
});

Route::any('/loaddata', [LoadDataController::class, 'index'])->name('admin.loaddata.index');
Route::post('/update-field', [LoadDataController::class, 'updateField'])->name('admin.loaddata.update-field');
Route::post('/sort', [LoadDataController::class, 'sort'])->name('admin.loaddata.sort');
Route::post('/delete', [LoadDataController::class, 'delete'])->name('admin.loaddata.delete');
Route::post('/remove-cache', [LoadDataController::class, 'removeCache'])->name('admin.loaddata.remove-cache');

// Facebook Tokens
Route::prefix('/facebook-tokens')->group(function () {
    Route::get('/', [FbTokenController::class, 'index'])->name('admin.fb-tokens.index');
    Route::post('/store', [FbTokenController::class, 'store'])->name('admin.fb-tokens.store');
    Route::post('/update-status/{id}', [FbTokenController::class, 'updateStatus'])->name('admin.fb-tokens.update-status');
    Route::post('/refresh/{id}', [FbTokenController::class, 'refresh'])->name('admin.fb-tokens.refresh');
    Route::delete('/delete/{id}', [FbTokenController::class, 'destroy'])->name('admin.fb-tokens.delete');
    Route::post('/delete-all', [FbTokenController::class, 'deleteAll'])->name('admin.fb-tokens.delete-all');
    Route::post('/delete-die', [FbTokenController::class, 'deleteDie'])->name('admin.fb-tokens.delete-die');
});

Route::post('/upload-update', function (Request $request) {
    $request->validate([
        'version' => 'required|string',
        'changelog' => 'nullable|string',
        'file' => 'required|file|mimes:zip'
    ]);

    // $path = $request->file('file')->storeAs('updates', $request->file('file')->getClientOriginalName());

    // // Đặt tất cả các version khác là is_latest=false
    // Version::query()->update(['is_latest' => false]);

    // $version = Version::create([
    //     'version' => $request->version,
    //     'changelog' => $request->changelog,
    //     'file_url' => url('storage/' . $path),
    //     'is_latest' => true,
    // ]);

    // return response()->json([
    //     'ok' => true,
    //     'message' => 'Upload success',
    //     'data' => $version
    // ]);
});
