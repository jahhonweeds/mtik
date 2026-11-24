<?php
use App\Livewire\PppoeManager;
use App\Livewire\BillingManager;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Mikrotik;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('/pppoe-manager', PppoeManager::class)->name('pppoe.manager');
    Route::get('/billing-manager', BillingManager::class)->name('billing.manager');
   
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/mikrotik', Mikrotik::class)->name('settings.mikrotik');
});

require __DIR__.'/auth.php';
