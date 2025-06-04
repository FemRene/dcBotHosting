<?php

use App\Http\Controllers\Auth\DiscordController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\DashboardConroller;
use App\Http\Controllers\TwoFactorController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Bot management routes
    Route::get('/bots', [BotController::class, 'index'])->name('bots.index');
    Route::get('/bots/create', [BotController::class, 'create'])->name('bots.create');
    Route::post('/bots', [BotController::class, 'store'])->name('bots.store');
    Route::get('/bots/{bot}', [BotController::class, 'show'])->name('bots.show');
    Route::get('/bots/{bot}/settings', [BotController::class, 'settings'])->name('bots.settings');
    Route::post('/bots/{bot}/settings', [BotController::class, 'updateSettings'])->name('bots.updateSettings');
    Route::get('/bots/{bot}/modules', [BotController::class, 'modules'])->name('bots.modules');
    Route::post('/bots/{bot}/modules', [BotController::class, 'updateModules'])->name('bots.updateModules');
    Route::post('/bots/{bot}/start', [BotController::class, 'start'])->name('bots.start');
    Route::post('/bots/{bot}/stop', [BotController::class, 'stop'])->name('bots.stop');
    Route::post('/bots/{bot}/restart', [BotController::class, 'restart'])->name('bots.restart');
    Route::get('/bots/{bot}/logs', [BotController::class, 'logs'])->name('bots.logs');
    Route::delete('/bots/{bot}', [BotController::class, 'destroy'])->name('bots.destroy');

    // Admin routes
    Route::get('/admin/bots', [BotController::class, 'adminIndex'])
        ->middleware('admin')
        ->name('admin.bots');
});

Route::get('/auth/discord', [DiscordController::class, 'redirectToDiscord'])->name('discord.login');
Route::get('/auth/discord/callback', [DiscordController::class, 'handleDiscordCallback'])->name('discord.callback');

Route::get('/2fa/setup', [TwoFactorController::class, 'showSetupForm'])->name('2fa.setup');
Route::post('/2fa/enable', [TwoFactorController::class, 'enableTwoFactor'])->name('2fa.enable');

Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verifyTwoFactor'])->name('2fa.verify.post');

Route::get('dashboard', [DashboardConroller::class, 'showDashboard'])->name('dashboard');

require __DIR__.'/auth.php';
