<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WebPushSubscriptionController;
use Illuminate\Notifications\DatabaseNotification;

Route::get('/', function () {
     return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
     Route::post('/push-subscriptions', [WebPushSubscriptionController::class, 'store'])->name('push-subscriptions.store');
    Route::delete('/push-subscriptions', [WebPushSubscriptionController::class, 'destroy'])->name('push-subscriptions.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','permission:users.manage'])->group(function () {
    Route::resource('users', UserController::class)->except(['show','destroy']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
Route::middleware('auth')->post('/notifications/{notification}/read', function (DatabaseNotification $notification) {
    abort_unless($notification->notifiable_id === auth()->id(), 403);

    $notification->markAsRead();

    return redirect($notification->data['url'] ?? route('dashboard'));
})->name('notifications.read');

require __DIR__.'/auth.php';
require __DIR__.'/quality.php';
require __DIR__.'/admin.php';