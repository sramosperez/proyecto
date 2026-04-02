<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;

Route::get('/', fn() => redirect()->route('issues.index'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('issues')->group(function () {
        Route::get('/', [IssueController::class, 'index'])->name('issues.index');
        Route::patch('/{id}', [IssueController::class, 'update'])
            ->middleware('role:User Authorized')
            ->name('issues.update');
    });
});
