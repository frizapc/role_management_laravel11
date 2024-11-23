<?php

use App\Http\Controllers\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(DonationController::class)
    ->group(function () {
        Route::post('/donation', 'store'); 
        Route::post('/donation/notification', 'notification'); 
});