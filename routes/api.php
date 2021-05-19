<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateway\TransactionController;
use App\Http\Controllers\Gateway\WalletController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\UserTypeController;

Route::group(
    ["prefix" => "/users"],
    function () {
        Route::post('/', [UserController::class, 'create']);
    }
);

Route::group(
    ["prefix" => "/wallet/{id}"],
    function () {
        Route::get('/', [WalletController::class, 'show']);
    }
);

Route::group(
    ["prefix" => "/user-types"],
    function () {
        Route::get('/', [UserTypeController::class, 'show']);
    }
);

Route::group(
    ["prefix" => "/transaction"],
    function () {
        Route::post('/', [TransactionController::class, 'create']);
        Route::post('/{id}/rollback', [TransactionController::class, 'rollback']);
    }
);
