<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\WooCommerceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');

    Route::middleware(['auth'])->group(function () {
        // Create new material
        Route::post('/products/{product}/materials', [MaterialController::class, 'store'])
            ->name('materials.store')
            ->middleware(['auth']);

        // Update existing material
        Route::patch('/materials/{material}', [MaterialController::class, 'update'])
            ->name('materials.update');
    });

    Route::patch('/products/{product}/sale-price', [\App\Http\Controllers\ProductController::class, 'updateSalePrice'])
        ->name('products.update-sale-price');

    Route::get('/api/woo-categories', [WooCommerceController::class, 'getCategories']);
    Route::patch('/products/{product}/woo-category', [ProductController::class, 'updateWooCategory'])
        ->name('products.update-woo-category')
        ->middleware(['auth']);

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    Route::resource('expenses', ExpenseController::class);
    Route::post('expense-categories', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
});


