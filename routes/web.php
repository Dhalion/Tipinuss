<?php declare(strict_types=1);

use App\Livewire\Page\MainPage;
use Illuminate\Support\Facades\Route;

Route::livewire('/', MainPage::class)->name('home');