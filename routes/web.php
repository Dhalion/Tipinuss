<?php

declare(strict_types=1);

use App\Livewire\Page\Register;
use App\Livewire\Page\Account;
use App\Livewire\Page\Bets\Create;
use App\Livewire\Page\Bets\BetsListing;
use App\Livewire\Page\Bets\BetDetail;
use App\Livewire\Page\Login;
use App\Livewire\Page\MainPage;
use Illuminate\Support\Facades\Route;

Route::livewire('/', MainPage::class)->name('main');

Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();

        return redirect()->route('main');
    })->name('logout');
    Route::livewire('/account', Account::class)->name('account');

    Route::livewire('/bet/create', Create::class)->name('bets.create');
    Route::livewire('/bets', BetsListing::class)->name('bets.list');
    Route::livewire('/bets/{bet}', BetDetail::class)->name('bets.detail');
});
