<?php

use App\Http\Controllers\Api\ApiV2Controller;
use App\Http\Controllers\Client\OrderClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AppAuthController;
use App\Models\Version;
use Illuminate\Support\Facades\Storage;



use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiClientController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\ApiAdminController;
use App\Http\Controllers\Api\InstallController;

Route::get('/install/status', [InstallController::class, 'status']);
Route::post('/install', [InstallController::class, 'install']);

Route::post('/auth/api/login', [ApiAuthController::class, 'login']);
Route::post('/auth/api/register', [ApiAuthController::class, 'register']);
Route::post('/auth/google/exchange', [ApiAuthController::class, 'exchangeGoogleCode']);
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/api/me', [ApiAuthController::class, 'me']);
    Route::post('/auth/api/logout', [ApiAuthController::class, 'logout']);
    
    Route::get('/client/orders', [ApiClientController::class, 'getOrders']);
    Route::get('/client/orders/{id}', [ApiClientController::class, 'getOrder']);
    Route::get('/client/statistics', [ApiClientController::class, 'getStatistics']);
    Route::get('/client/updates', [ApiClientController::class, 'getServiceUpdates']);
    Route::get('/client/posts', [ApiClientController::class, 'getPosts']);
    Route::get('/client/posts/{slug}', [ApiClientController::class, 'getPost']);
    Route::get('/client/products', [ApiClientController::class, 'getProducts']);
    Route::get('/client/products/orders', [ApiClientController::class, 'getProductOrders']);
    Route::get('/client/products/{slug}', [ApiClientController::class, 'getProduct']);
    Route::post('/client/products/{id}/purchase', [ApiClientController::class, 'purchaseProduct']);
    Route::post('/client/products/{id}/reviews', [ApiClientController::class, 'storeProductReview']);
    Route::get('/client/subscriptions', [ApiClientController::class, 'getSubscriptions']);
    Route::get('/client/drip-feeds', [ApiClientController::class, 'getDripFeeds']);
    Route::get('/client/recharge-options', [ApiClientController::class, 'getRechargeOptions']);
    Route::get('/client/recharges', [ApiClientController::class, 'getRecharges']);
    Route::post('/client/recharges/bank', [ApiClientController::class, 'createBankRecharge']);
    Route::post('/client/recharges/usdt', [ApiClientController::class, 'createUsdtRecharge']);
    Route::post('/client/recharges/binance', [ApiClientController::class, 'createBinanceRecharge']);
    Route::post('/client/recharges/trc20', [ApiClientController::class, 'createTrc20Recharge']);
    Route::get('/client/recharges/{id}', [ApiClientController::class, 'getRecharge']);
    Route::post('/client/orders', [ApiClientController::class, 'createOrder']);
    Route::post('/client/orders/mass', [ApiClientController::class, 'createMassOrders']);
    Route::post('/client/orders/action', [ApiClientController::class, 'actionOrder']);
    Route::get('/client/refills', [ApiClientController::class, 'getRefills']);
    Route::get('/client/cashflow', [ApiClientController::class, 'getCashflow']);
    Route::get('/client/affiliate', [ApiClientController::class, 'getAffiliate']);
    Route::get('/client/child-panels', [ApiClientController::class, 'getChildPanels']);
    Route::post('/client/child-panels/check-domain', [ApiClientController::class, 'checkDomain']);
    Route::post('/client/child-panels', [ApiClientController::class, 'createChildPanel']);
    Route::post('/client/child-panels/{id}/key', [ApiClientController::class, 'getChildPanelKey']);
    Route::post('/client/child-panels/{id}/renew', [ApiClientController::class, 'renewChildPanel']);
    
    Route::get('/client/tickets', [ApiClientController::class, 'getTickets']);
    Route::post('/client/tickets', [ApiClientController::class, 'createTicket']);
    Route::get('/client/tickets/{id}', [ApiClientController::class, 'getTicket']);
    Route::post('/client/tickets/{id}/reply', [ApiClientController::class, 'replyTicket']);
    
    Route::post('/client/change-password', [ApiClientController::class, 'changePassword']);
    Route::post('/client/change-currency', [ApiClientController::class, 'changeCurrency']);
    Route::get('/client/profile', [ApiClientController::class, 'getProfile']);
    Route::put('/client/profile', [ApiClientController::class, 'updateProfile']);
    Route::get('/client/telegram-link', [ApiClientController::class, 'getTelegramLink']);
    Route::post('/client/api-key/regenerate', [ApiClientController::class, 'regenerateApiKey']);
    Route::get('/client/two-factor/setup', [ApiClientController::class, 'getTwoFactorSetup']);
    Route::post('/client/two-factor/enable', [ApiClientController::class, 'enableTwoFactor']);
    Route::post('/client/two-factor/disable', [ApiClientController::class, 'disableTwoFactor']);
    Route::post('/client/two-factor/telegram/send', [ApiClientController::class, 'sendTelegramTwoFactorCode']);
    Route::post('/client/two-factor/telegram/enable', [ApiClientController::class, 'enableTelegramTwoFactor']);

    Route::get('/admin/dashboard', [ApiAdminController::class, 'dashboard']);
    Route::get('/admin/cron-link', [ApiAdminController::class, 'cronLink']);
    Route::get('/admin/orders', [ApiAdminController::class, 'orders']);
    Route::post('/admin/orders/refresh-statuses', [ApiAdminController::class, 'refreshOrderStatuses']);
    Route::post('/admin/orders/clean', [ApiAdminController::class, 'cleanOrders']);
    Route::delete('/admin/orders/all', [ApiAdminController::class, 'destroyAllOrders']);
    Route::put('/admin/orders/{id}', [ApiAdminController::class, 'updateOrder']);
    Route::get('/admin/deposits', [ApiAdminController::class, 'deposits']);
    Route::post('/admin/deposits/{id}/approve', [ApiAdminController::class, 'approveDeposit']);
    Route::post('/admin/deposits/{id}/cancel', [ApiAdminController::class, 'cancelDeposit']);
    Route::get('/admin/payment-api-keys', [ApiAdminController::class, 'paymentApiKeys']);
    Route::put('/admin/payment-api-keys', [ApiAdminController::class, 'updatePaymentApiKeys']);
    Route::post('/admin/ocb/login', [ApiAdminController::class, 'ocbLogin']);
    Route::post('/admin/ocb/otp', [ApiAdminController::class, 'ocbOtp']);
    Route::get('/admin/ocb/transactions', [ApiAdminController::class, 'ocbTransactions']);
    Route::get('/admin/affiliates', [ApiAdminController::class, 'affiliates']);
    Route::get('/admin/tickets', [ApiAdminController::class, 'tickets']);
    Route::get('/admin/tickets/{id}', [ApiAdminController::class, 'ticketDetail']);
    Route::put('/admin/tickets/{id}', [ApiAdminController::class, 'updateTicket']);
    Route::post('/admin/tickets/{id}/reply', [ApiAdminController::class, 'replyTicket']);
    Route::delete('/admin/tickets/{id}', [ApiAdminController::class, 'destroyTicket']);
    Route::get('/admin/posts', [ApiAdminController::class, 'posts']);
    Route::post('/admin/posts', [ApiAdminController::class, 'storePost']);
    Route::post('/admin/posts/{id}', [ApiAdminController::class, 'updatePost']);
    Route::delete('/admin/posts/{id}', [ApiAdminController::class, 'destroyPost']);
    Route::get('/admin/contact-widgets', [ApiAdminController::class, 'contactWidgets']);
    Route::post('/admin/contact-widgets', [ApiAdminController::class, 'storeContactWidget']);
    Route::post('/admin/contact-widgets/{id}', [ApiAdminController::class, 'updateContactWidget']);
    Route::put('/admin/contact-widgets/{id}/status', [ApiAdminController::class, 'updateContactWidgetStatus']);
    Route::delete('/admin/contact-widgets/{id}', [ApiAdminController::class, 'destroyContactWidget']);
    Route::get('/admin/transactions', [ApiAdminController::class, 'transactions']);
    Route::get('/admin/facebook-tokens', [ApiAdminController::class, 'facebookTokens']);
    Route::post('/admin/facebook-tokens', [ApiAdminController::class, 'storeFacebookTokens']);
    Route::put('/admin/facebook-tokens/{id}/status', [ApiAdminController::class, 'updateFacebookTokenStatus']);
    Route::post('/admin/facebook-tokens/{id}/refresh', [ApiAdminController::class, 'refreshFacebookToken']);
    Route::delete('/admin/facebook-tokens/die', [ApiAdminController::class, 'destroyDeadFacebookTokens']);
    Route::delete('/admin/facebook-tokens/all', [ApiAdminController::class, 'destroyAllFacebookTokens']);
    Route::delete('/admin/facebook-tokens/{id}', [ApiAdminController::class, 'destroyFacebookToken']);
    Route::get('/admin/telegram', [ApiAdminController::class, 'telegramConfig']);
    Route::put('/admin/telegram', [ApiAdminController::class, 'updateTelegramConfig']);
    Route::post('/admin/telegram/webhook', [ApiAdminController::class, 'registerTelegramWebhook']);
    Route::get('/admin/notifications', [ApiAdminController::class, 'notificationConfig']);
    Route::put('/admin/notifications', [ApiAdminController::class, 'updateNotificationConfig']);
    Route::get('/admin/products', [ApiAdminController::class, 'products']);
    Route::get('/admin/products/categories', [ApiAdminController::class, 'productCategoryOptions']);
    Route::post('/admin/products', [ApiAdminController::class, 'storeProduct']);
    Route::post('/admin/products/{id}', [ApiAdminController::class, 'updateProduct']);
    Route::delete('/admin/products/{id}', [ApiAdminController::class, 'destroyProduct']);
    Route::get('/admin/products/{id}/stocks', [ApiAdminController::class, 'productStocks']);
    Route::post('/admin/products/{id}/stocks', [ApiAdminController::class, 'storeProductStocks']);
    Route::delete('/admin/products/{id}/stocks', [ApiAdminController::class, 'destroyProductStocks']);
    Route::get('/admin/product-categories', [ApiAdminController::class, 'productCategories']);
    Route::post('/admin/product-categories', [ApiAdminController::class, 'storeProductCategory']);
    Route::post('/admin/product-categories/{id}', [ApiAdminController::class, 'updateProductCategory']);
    Route::delete('/admin/product-categories', [ApiAdminController::class, 'destroyProductCategories']);
    Route::delete('/admin/product-categories/{id}', [ApiAdminController::class, 'destroyProductCategory']);
    Route::get('/admin/product-orders', [ApiAdminController::class, 'productOrders']);
    Route::post('/admin/ai-chat', [ApiAdminController::class, 'aiChat']);
    Route::get('/admin/users', [ApiAdminController::class, 'users']);
    Route::get('/admin/users/{id}', [ApiAdminController::class, 'userDetail']);
    Route::put('/admin/users/{id}', [ApiAdminController::class, 'updateUser']);
    Route::post('/admin/users/{id}/balance', [ApiAdminController::class, 'updateUserBalance']);
    Route::post('/admin/providers/sync-balances', [ApiAdminController::class, 'syncProviderBalances']);
    Route::get('/admin/providers/{id}/favicon', [ApiAdminController::class, 'providerFavicon']);
    Route::post('/admin/providers/{id}/update-balance', [ApiAdminController::class, 'updateProviderBalance']);
    Route::post('/admin/providers/{id}/update-prices', [ApiAdminController::class, 'updateProviderPrices']);
    Route::get('/admin/providers', [ApiAdminController::class, 'providers']);
    Route::post('/admin/providers', [ApiAdminController::class, 'storeProvider']);
    Route::put('/admin/providers/{id}', [ApiAdminController::class, 'updateProvider']);
    Route::post('/admin/platforms', [ApiAdminController::class, 'storePlatform']);
    Route::post('/admin/platforms/reorder', [ApiAdminController::class, 'reorderPlatforms']);
    Route::post('/admin/platforms/{id}', [ApiAdminController::class, 'updatePlatform']);
    Route::get('/admin/categories', [ApiAdminController::class, 'categories']);
    Route::post('/admin/categories', [ApiAdminController::class, 'storeCategory']);
    Route::post('/admin/categories/reorder', [ApiAdminController::class, 'reorderCategories']);
    Route::delete('/admin/categories/all', [ApiAdminController::class, 'destroyAllCategories']);
    Route::post('/admin/categories/{id}', [ApiAdminController::class, 'updateCategory']);
    Route::get('/admin/category-import/source', [ApiAdminController::class, 'categoryImportSource']);
    Route::post('/admin/category-import', [ApiAdminController::class, 'importCategories']);
    Route::get('/admin/service-import/source', [ApiAdminController::class, 'serviceImportSource']);
    Route::post('/admin/service-import', [ApiAdminController::class, 'importServices']);
    Route::get('/admin/services', [ApiAdminController::class, 'services']);
    Route::delete('/admin/services/all', [ApiAdminController::class, 'destroyAllServices']);
    Route::post('/admin/services/bulk-update', [ApiAdminController::class, 'bulkUpdateServices']);
    Route::post('/admin/banks', [ApiAdminController::class, 'storeBank']);
    Route::post('/admin/banks/{id}', [ApiAdminController::class, 'updateBank']);
    Route::delete('/admin/banks/{id}', [ApiAdminController::class, 'destroyBank']);

    // Dynamic Resources
    Route::get('/admin/resources/{resource}', [ApiAdminController::class, 'resourceIndex']);
    Route::post('/admin/resources/{resource}', [ApiAdminController::class, 'resourceStore']);
    Route::put('/admin/resources/{resource}/{id}', [ApiAdminController::class, 'resourceUpdate']);
    Route::delete('/admin/resources/{resource}/{id}', [ApiAdminController::class, 'resourceDestroy']);
    
    // Settings Image Upload
    Route::post('/admin/settings/upload-image', [ApiAdminController::class, 'uploadSettingImage']);

    // Child Panels Admin
    Route::get('/admin/childpanels', [\App\Http\Controllers\Api\ChildPanelAdminController::class, 'index']);
    Route::post('/admin/childpanels/settings', [\App\Http\Controllers\Api\ChildPanelAdminController::class, 'updateSettings']);
    Route::post('/admin/childpanels/{id}/status', [\App\Http\Controllers\Api\ChildPanelAdminController::class, 'updateStatus']);
    Route::get('/admin/childpanels/{id}/statistics', [\App\Http\Controllers\Api\ChildPanelAdminController::class, 'statistics']);
    Route::get('/admin/childpanels/{id}/login', [\App\Http\Controllers\Api\ChildPanelAdminController::class, 'loginAsAdmin']);
    Route::delete('/admin/childpanels/{id}', [\App\Http\Controllers\Api\ChildPanelAdminController::class, 'destroy']);
});

Route::get('/client/config', [ApiClientController::class, 'getConfig']);
Route::get('/client/categories', [ApiClientController::class, 'getCategories']);
Route::get('/client/platforms', [ApiClientController::class, 'getPlatforms']);
Route::get('/client/services', [ApiClientController::class, 'getServices']);
Route::get('/client/currencies', [ApiClientController::class, 'getCurrencies']);

Route::post('/auth/login', [AppAuthController::class, 'DoLogin']);
Route::post('/auth/register', [AppAuthController::class, 'DoRegister']);
use App\Http\Controllers\TestLicenseController;

use App\Http\Controllers\Cron\Recharge\RechargeUsdtCronJobController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match(['get', 'post'], '/mock-license-verify', [TestLicenseController::class, 'verify']);
Route::match(['get', 'post'], '/mock-check-update', [TestLicenseController::class, 'checkUpdate']);


Route::post('/request-order', [OrderClientController::class, 'requestOrder'])->name('request-order');
Route::get('/request-order', [OrderClientController::class, 'requestOrder'])->name('request-order');
Route::post('/request-action', [OrderClientController::class, 'requestAction'])->name('api.request-action');
Route::get('/request-action', [OrderClientController::class, 'requestAction'])->name('api.request-action');
Route::post('/request-product', [OrderClientController::class, 'requestProduct'])->name('request-product');
Route::post('/check-discount', [OrderClientController::class, 'checkDiscount'])->name('check-discount');

Route::post('/v2', [ApiV2Controller::class, 'handle'])->name('api.v2');
Route::get('/v2', [ApiV2Controller::class, 'handle'])->name('api.v2');
Route::match(['get', 'post'], '/v2/{key}', [ApiV2Controller::class, 'handle'])
    ->where('key', '[A-Za-z0-9]+')
    ->name('api.v2.key');

Route::get('/check-update', function (Request $request) {
    $currentVersion = $request->query('version'); // ?version=1.0.4
    $latest = Version::where('is_latest', true)->latest()->first();

    if (!$latest) {
        return response()->json([
            'ok' => false,
            'message' => 'No update info found'
        ]);
    }

    if ($currentVersion && version_compare($latest->version, $currentVersion, '<=')) {
        return response()->json([
            'ok' => true,
            'update_available' => false,
            'message' => 'You are using the latest version (' . $currentVersion . ')'
        ]);
    }

    return response()->json([
        'ok' => true,
        'update_available' => true,
        'latest_version' => $latest->version,
        'changelog' => $latest->changelog,
        'file_url' => $latest->file_url,
    ]);
});

// Cron Job Runner API
Route::get('/cron/jobs', [\App\Http\Controllers\Api\CronApiController::class, 'getJobs']);
Route::post('/cron/jobs/update/{id}', [\App\Http\Controllers\Api\CronApiController::class, 'updateJob']);

// License Server API
use App\Http\Controllers\Api\LicenseServerController;

Route::post('/license/check', [LicenseServerController::class, 'check']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/license/buy', [LicenseServerController::class, 'buy']);
    Route::get('/license/mine', [LicenseServerController::class, 'getLicenses']);
    Route::get('/license/products', [LicenseServerController::class, 'getProducts']);
    Route::get('/license/user', [LicenseServerController::class, 'getUser']);

});

use App\Http\Controllers\Admin\Licensing\LicenseAdminController;
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/licenses', [LicenseAdminController::class, 'index']);
    Route::post('/licenses/status/{id}', [LicenseAdminController::class, 'updateStatus']);
    
    // Currencies API
    Route::get('/currencies', [\App\Http\Controllers\Api\ApiCurrencyController::class, 'index']);
    Route::post('/currencies', [\App\Http\Controllers\Api\ApiCurrencyController::class, 'store']);
    Route::get('/currencies/{id}', [\App\Http\Controllers\Api\ApiCurrencyController::class, 'show']);
    Route::post('/currencies/{id}', [\App\Http\Controllers\Api\ApiCurrencyController::class, 'update']);
    Route::delete('/currencies/{id}', [\App\Http\Controllers\Api\ApiCurrencyController::class, 'destroy']);
});

// Tools API (for external tools)
use App\Http\Controllers\Api\ToolsAdminController;
Route::prefix('tools')->group(function () {
    Route::match(['get', 'post'], '/services/manual', [ToolsAdminController::class, 'listManual']);
    Route::post('/services/update', [ToolsAdminController::class, 'updateService']);
    Route::match(['get', 'post'], '/orders/manual', [ToolsAdminController::class, 'listOrders']);
    Route::post('/orders/update', [ToolsAdminController::class, 'updateOrder']);
    Route::get('/facebook/tokens', [\App\Http\Controllers\Api\FbTokenApiController::class, 'getLiveTokens']);
});
