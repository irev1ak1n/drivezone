<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\Catalog\ServiceController;
use App\Http\Controllers\Orders\CartController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\VehicleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

// ===== Публичный каталог (виден всем) =====
Route::prefix('catalog')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
});

// ===== Публичная корзина (только просмотр) =====
Route::get('/cart', [CartController::class, 'index'])->name('cart.page');

// ===== Личный кабинет (только авторизованные) =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');

    // ===== Действия с корзиной (только для авторизованных) =====
    Route::post('/cart/add/{id}/{type}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

// ===== API-версия корзины (для Postman, через токен) =====
Route::prefix('api/cart')
    ->middleware(['token', 'role:customer'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add/{id}/{type}', [CartController::class, 'add']);
        Route::post('/checkout', [CartController::class, 'checkout']);
    });

// ===== Админка =====
//Route::middleware(['auth', 'role:admin'])
//    ->prefix('admin')
//    ->name('admin.')
//    ->group(function () {
//        Route::resource('users', UserController::class);
//        Route::resource('products', ProductController::class);
//        Route::resource('services', ServiceController::class);
//    });

// ===== После логина — редирект по роли =====
Route::get('/dashboard', function () {
    $role = auth()->user()?->role;

    return match ($role) {
        'admin' => redirect()->route('admin.users.index'),
        'employee', 'supervisor' => redirect()->route('services.index'),
        default => redirect()->route('products.index'),
    };
})->middleware('auth')->name('dashboard');

// ===== Авторизация =====
Route::post('/login', [AuthController::class, 'login'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/me', [AuthController::class, 'me'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

// ===== Страница входа (Blade) =====
Route::get('/login', function (Request $request) {
    // Если пользователь пришёл не со страницы логина или логаута,
    // запомним адрес, чтобы вернуть его туда после успешного входа.
    $prev = url()->previous();

    if (!str_contains($prev, '/login') && !str_contains($prev, '/logout')) {
        session(['url.intended' => $prev]);
    }

    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('profile.avatar');

Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])
    ->middleware('auth')
    ->name('profile.avatar.delete');

// Полный список товаров (только товары, без доп. секций)
Route::get('/catalog/products/all', [ProductController::class, 'showAll'])
    ->name('products.all');

Route::get('/catalog', [ServiceController::class, 'index'])->name('catalog.index');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('/catalog/services/category/{category}', [ServiceController::class, 'category'])
    ->name('services.category');
Route::get('/catalog/services/all', [ServiceController::class, 'showAll'])
    ->name('services.all');


// === Товары ===
Route::get('/catalog/products/{id}', [\App\Http\Controllers\Catalog\ProductController::class, 'show'])
    ->name('products.show');

Route::get('/catalog/services/filter', [ServiceController::class, 'filter'])->name('services.filter');

// === Услуги ===
Route::get('/catalog/services/{id}', [\App\Http\Controllers\Catalog\ServiceController::class, 'showDetail'])
    ->name('services.show');

Route::get('/catalog/search', [ProductController::class, 'search'])->name('catalog.search');
Route::get('/catalog/filter', [ProductController::class, 'filter'])->name('products.filter');

Route::middleware('auth')->group(function () {
    Route::post('/vehicles/add', [VehicleController::class, 'store'])->name('vehicles.store');
});



Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/update/{id}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});

