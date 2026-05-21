<?php

declare(strict_types=1);

use App\Livewire\Page\Account;
use App\Livewire\Page\Admin\BetaKeyManagement;
use App\Livewire\Page\Admin\OrganisationManagement;
use App\Livewire\Page\Admin\UserManagement;
use App\Livewire\Page\Auth\PendingApproval;
use App\Livewire\Page\Bets\BetDetail;
use App\Livewire\Page\Bets\BetsListing;
use App\Livewire\Page\Bets\Create;
use App\Livewire\Page\Login;
use App\Livewire\Page\MainPage;
use App\Livewire\Page\Register;
use App\Models\Bet;
use Illuminate\Support\Facades\Route;

Route::livewire('/', MainPage::class)->name('main');

Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

Route::livewire('/pending', PendingApproval::class)->name('pending.approval');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('main');
    })->name('logout');
    Route::livewire('/account', Account::class)->name('account');

    Route::livewire('/bet/create', Create::class)->name('bets.create');
    Route::livewire('/bets', BetsListing::class)->name('bets.list');
    Route::get('/bets/{uuid}', function (string $uuid) {
        $bet = Bet::findOrFail($uuid);

        return redirect()->route('bets.detail', ['bet' => $bet->slugUrl()], 301);
    })->whereUuid('uuid');
    Route::livewire('/bets/{bet}', BetDetail::class)->name('bets.detail');

    Route::middleware('can:admin')->group(function () {
        Route::livewire('/admin/organisations', OrganisationManagement::class)->name('admin.organisations');
        Route::livewire('/admin/users', UserManagement::class)->name('admin.users');
        Route::livewire('/admin/beta-keys', BetaKeyManagement::class)->name('admin.beta-keys');
    });
});
