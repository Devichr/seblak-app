<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Proses pembayaran
Route::post('/orders/{order}/payment', [PaymentController::class, 'processPayment'])->name('order.payment');

// Halaman detail pembayaran
Route::get('/orders/payment', function (Request $request) {
    return Inertia::render('Payment', [
        'paymentMethod' => $request->query('paymentMethod'),
        'paymentDetails' => $request->query('paymentDetails', []),
        'timeLimit' => $request->query('timeLimit', 15),
    ]);
})->name('orders.payment.detail');
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index'); // Menampilkan daftar menu
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
Route::put('/orders/{id}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
Route::post('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store'); // Menambah menu baru
    Route::get('/menus/{id}/edit', [MenuController::class, 'edit'])->name('menus.edit'); // Form edit menu
    Route::put('/menus/{id}', [MenuController::class, 'update'])->name('menus.update'); // Update menu
    Route::delete('/menus/{id}', [MenuController::class, 'destroy'])->name('menus.destroy'); // Hapus menu
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
    Route::put('/payment-methods/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'destroy']);

});

require __DIR__.'/auth.php';
