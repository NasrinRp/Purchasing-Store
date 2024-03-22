<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return auth()->user()->activeCodes()->create([
        'code' => 123,
        'expired_at' => now()->addMinutes(10)
    ]);
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('auth/google')->name('auth.google.')->group(function () {
    Route::get('/', [GoogleAuthController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [GoogleAuthController::class, 'callback'])->name('callback');
});

Route::middleware('auth')->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('secret-route', function () {
        return 'You Confirmed Your Password because this content is sensitive.';
    })->middleware(['password.confirm']);

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/two-factor-auth', [ProfileController::class, 'twoFactorAuth'])->name('two-factor-auth');
        Route::post('/two-factor-auth', [ProfileController::class, 'mangeTwoFactoryAuth'])->name('manage-two-factor-auth');
        Route::get('/two-factor-auth/phone-verify', [ProfileController::class, 'getPhoneVerify'])->name('get-phone-verify');
        Route::post('/two-factor-auth/phone-verify', [ProfileController::class, 'postPhoneVerify'])->name('post-phone-verify');
    });
});
