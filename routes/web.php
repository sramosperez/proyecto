<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('issues.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('issues')->group(function () {
        Route::get('/', [IssueController::class, 'index'])->name('issues.index');
        Route::get('/{id}', [IssueController::class, 'show'])
            ->middleware('role:Responsable,Dirección')
            ->name('issues.show');
        Route::patch('/{id}', [IssueController::class, 'update'])
            ->middleware('role:Responsable,Dirección')
            ->name('issues.update');
    });
});
