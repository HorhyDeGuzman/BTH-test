<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => Inertia::render('HomePage'))->name('home');

Route::get('/product/{id}', fn (int $id) => Inertia::render('ProductDetailPage', [
    'id' => $id,
]))
    ->whereNumber('id')
    ->name('products.show');

/*
|--------------------------------------------------------------------------
| Admin pages (Inertia shell only — auth is enforced client-side via the
| Sanctum token in localStorage; the REST API itself rejects anonymous
| writes with 401).
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/products', fn () => Inertia::render('admin/ProductListPage'))
        ->name('products.index');
});

/*
|--------------------------------------------------------------------------
| Authenticated (session) routes kept from Breeze scaffold for now.
| The product-admin flow runs on the REST API (Sanctum tokens), not on
| session auth — those routes are added in a later commit.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
