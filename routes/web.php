<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard.main'))->name('dashboard');
    Route::get('/dashboard', \App\Livewire\Dashboard\AdminDashboard::class)->name('dashboard.main');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', \App\Livewire\Users\UsersList::class)->name('index');
        Route::get('/create', \App\Livewire\Users\UserForm::class)->name('create');
        Route::get('/{user}/edit', \App\Livewire\Users\UserForm::class)->name('edit');
    });

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', \App\Livewire\Products\ProductCatalog::class)->name('index');
        Route::get('/create', \App\Livewire\Products\ProductForm::class)
            ->middleware('role:super_admin,md,ceo,nsm')->name('create');
        Route::get('/{product}/edit', \App\Livewire\Products\ProductForm::class)
            ->middleware('role:super_admin,md,ceo,nsm')->name('edit');
    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', \App\Livewire\Sales\SalesList::class)->name('index');
        Route::get('/record', \App\Livewire\Sales\SaleForm::class)->name('create');
        Route::get('/{sale}', \App\Livewire\Sales\SaleDetail::class)->name('show');
    });

    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', \App\Livewire\Stock\StockDashboard::class)->name('index');
        Route::get('/allocate', \App\Livewire\Stock\StockAllocate::class)->name('allocate');
        Route::get('/receive', \App\Livewire\Stock\StockReceive::class)->name('receive');
    });

    Route::prefix('targets')->name('targets.')->group(function () {
        Route::get('/', \App\Livewire\Targets\TargetManager::class)->name('index');
    });

    Route::get('/shops', \App\Livewire\Sales\ShopManager::class)->name('shops.index');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales/csv', [\App\Http\Controllers\ExportController::class, 'exportSalesCsv'])->name('sales.csv');
        Route::get('/stock/csv', [\App\Http\Controllers\ExportController::class, 'exportStockCsv'])->name('stock.csv');
    });

    Route::get('/profile', \App\Livewire\Profile::class)->name('profile');
});
