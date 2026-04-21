<?php

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
| Admin login page (Inertia shell — the form itself calls POST /api/login
| and stores the returned Sanctum token in localStorage).
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => Inertia::render('auth/LoginPage'))->name('login');

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

    Route::get('/products/create', fn () => Inertia::render('admin/ProductFormPage'))
        ->name('products.create');

    Route::get('/products/{id}/edit', fn (int $id) => Inertia::render('admin/ProductFormPage', [
        'id' => $id,
    ]))
        ->whereNumber('id')
        ->name('products.edit');
});
