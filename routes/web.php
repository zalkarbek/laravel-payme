<?php

use App\Constants\Payments\PaycomPayItem;
use App\Http\Controllers\Payment\PaycomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api/v1/payments/paycom/hook'], function() {

    Route::post('license', PaycomController::class)
        ->name('payment.paycom.hook.license')
        ->middleware('paycom.auth:'. PaycomPayItem::LICENSE);

    Route::post('event', PaycomController::class)
        ->name('payment.paycom.hook.event')
        ->middleware('paycom.auth:'. PaycomPayItem::EVENT);
});
