<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/mission', [HomeController::class, 'mission'])->name('mission');
Route::get('/leadership', [HomeController::class, 'leadership'])->name('leadership');
Route::get('/advocacy', [HomeController::class, 'advocacy'])->name('advocacy');
Route::get('/stories', [HomeController::class, 'stories'])->name('stories');
Route::get('/projects', [HomeController::class, 'projects'])->name('projects');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/community', [HomeController::class, 'community'])->name('community');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:3,1')->name('contact.store');

Route::middleware('auth')->group(function () {
    Route::view('/verify-email', 'app')->name('verification.notice');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/member/{path?}', 'app')->where('path', '.*')->middleware('verified')->name('member.dashboard');
    Route::view('/admin/{path?}', 'app')->where('path', '.*')->middleware(['verified', 'role:Super Admin'])->name('admin.dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
