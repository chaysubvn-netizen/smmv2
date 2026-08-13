<?php

use App\Http\Controllers\Cron\Order\StatusOrderCronJobController;
use App\Http\Controllers\Cron\Recharge\RechargeCronJobController;
use App\Http\Controllers\Cron\Recharge\RechargeUsdtCronJobController;
use App\Http\Controllers\Cron\Recharge\BinanceCronJobController;
use App\Http\Controllers\Cron\Recharge\Trc20CronJobController;
use App\Http\Controllers\Cron\Service\ServiceCronJobController;
use App\Http\Controllers\Cron\System\SystemScheduleCronJobController;
use Illuminate\Support\Facades\Route;


Route::prefix('order')->group(function(){
    Route::get('status', [StatusOrderCronJobController::class, 'index'])->name('cron.order.status');
    Route::get('refill', [StatusOrderCronJobController::class, 'refill'])->name('cron.order.refill');
    Route::get('dripfeed', [StatusOrderCronJobController::class, 'dripfeed'])->name('cron.order.dripfeed');
    Route::get('schedule', [StatusOrderCronJobController::class, 'schedule'])->name('cron.order.schedule');
});

Route::prefix('/services')->group(function(){
    Route::get('/', [ServiceCronJobController::class, 'index'])->name('cron.service');
    Route::get('/update-avg-time', [ServiceCronJobController::class, 'updateAvgTime'])->name('cron.service.update.avg.time');
    Route::get('/update-service', [ServiceCronJobController::class, 'updateService'])->name('cron.service.update');
    Route::get('/schedule', [ServiceCronJobController::class, 'schedule'])->name('cron.service.schedule');
    Route::get('/auto-add', [ServiceCronJobController::class, 'autoAdd'])->name('cron.service.auto_add');
});

Route::prefix('recharge')->group(function (){
    Route::get('usdt-callback', [RechargeUsdtCronJobController::class, 'index'])->name('cron.recharge.usdt');
    Route::get('bank', [RechargeCronJobController::class, 'bank'])->name('cron.recharge.bank');
    Route::get('binance', [BinanceCronJobController::class, 'index'])->name('cron.recharge.binance');
    Route::get('trc20', [Trc20CronJobController::class, 'index'])->name('cron.recharge.trc20');
});

Route::prefix('system')->group(function(){
    Route::get('/schedule', [SystemScheduleCronJobController::class, 'index'])->name('cron.system.schedule');
    Route::get('/auto-update', [SystemScheduleCronJobController::class, 'autoUpdate'])->name('cron.system.auto-update');
    Route::get('/fb-tokens', [\App\Http\Controllers\Cron\System\FbTokenCronController::class, 'index'])->name('cron.system.fb-tokens');
});
