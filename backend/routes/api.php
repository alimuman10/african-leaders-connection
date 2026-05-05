<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PlatformContentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ResourceLibraryController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1');
});
Route::get('email/verify/{id}/{hash}', function (Request $request, string $id, string $hash) {
    $user = User::findOrFail($id);

    abort_unless(hash_equals($hash, sha1($user->getEmailForVerification())), 403, 'Invalid verification link.');

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    $user->update(['status' => 'active']);

    return response()->json(['message' => 'Email verified successfully.']);
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1');
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::post('contact', [ContactMessageController::class, 'store'])->middleware('throttle:3,1');
Route::get('stories', [PlatformContentController::class, 'publicStories']);
Route::get('projects', [PlatformContentController::class, 'publicProjects']);
Route::get('services', [PlatformContentController::class, 'publicServices']);
Route::get('advocacy', [PlatformContentController::class, 'publicAdvocacy']);
Route::get('leadership', [PlatformContentController::class, 'publicLeadership']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('user', [AuthController::class, 'me']);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('password', [ProfileController::class, 'updatePassword']);
    Route::post('account/deactivate', [ProfileController::class, 'deactivate']);
    Route::get('resources', [ResourceLibraryController::class, 'index']);
    Route::post('stories/submit', [PlatformContentController::class, 'submitStory'])->middleware('throttle:5,1');
});

Route::middleware(['auth:sanctum', 'verified', 'role:Super Admin|Admin|Content Manager|Community Manager'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::apiResource('users', UserController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('users/{user}/suspend', [UserController::class, 'suspend']);
    Route::post('users/{user}/reactivate', [UserController::class, 'reactivate']);
    Route::post('users/{user}/roles/assign', [UserController::class, 'assignRole']);
    Route::post('users/{user}/roles/remove', [UserController::class, 'removeRole']);
    Route::apiResource('admin/stories', PlatformContentController::class)->parameters(['stories' => 'story']);
    Route::apiResource('admin/projects', PlatformContentController::class)->parameters(['projects' => 'project']);
    Route::apiResource('admin/services', PlatformContentController::class)->parameters(['services' => 'service']);
    Route::apiResource('admin/advocacy', PlatformContentController::class)->parameters(['advocacy' => 'advocacySection']);
    Route::apiResource('admin/leadership', PlatformContentController::class)->parameters(['leadership' => 'leadershipContent']);
    Route::apiResource('contact/messages', ContactMessageController::class)
        ->parameters(['messages' => 'contactMessage'])
        ->except(['store']);
    Route::post('contact/messages/{contactMessage}/reply', [ContactMessageController::class, 'reply']);
    Route::post('contact/messages/{contactMessage}/archive', [ContactMessageController::class, 'archive']);
    Route::apiResource('settings', SettingController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('media', [MediaController::class, 'index']);
    Route::post('media', [MediaController::class, 'store'])->middleware('throttle:20,1');
    Route::delete('media/{mediaFile}', [MediaController::class, 'destroy']);
});
