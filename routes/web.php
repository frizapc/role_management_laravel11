<?php

use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;


Route::controller(DonationController::class)
    ->group(function () {
        Route::get('/donation', 'create');
        Route::get('/', 'index'); 
});