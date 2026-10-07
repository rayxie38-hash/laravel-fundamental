<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (Admin & User)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Items List & View (Admin & User)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // Loans List & User Loan Request (Admin & User)
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');

    // Return Logs List (Admin & User)
    Route::get('/returns', [ReturnLogController::class, 'index'])->name('returns.index');

    // Profile Management (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Item Management (Store, Update, Delete)
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

        // Loan Verification & Management
        Route::patch('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::patch('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');

        // Return Verification & Management
        Route::post('/returns', [ReturnLogController::class, 'store'])->name('returns.store');
        Route::delete('/returns/{returnLog}', [ReturnLogController::class, 'destroy'])->name('returns.destroy');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // User Account Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
