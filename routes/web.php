<?php

use App\Livewire\Articles\ShowArticles;
use App\Livewire\Articles\CreateArticles;
use App\Livewire\Articles\EditArticles;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Officialletter\Form;
use App\Livewire\Officialletter\Index;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/articles',ShowArticles::class)->name('articles.index');
    Route::get('/articles/create',CreateArticles::class)->name('articles.create');
    Route::get('/articles/{id}/edit',EditArticles::class)->name('articles.edit');

Route::middleware(['auth'])->group(function () {
    Route::get('/outgoing-letters', \App\Livewire\OutgoingLetter\Index::class)->name('outgoing-letters');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/sending-letters', \App\Livewire\SendingLetter\Index::class)->name('sending-letters');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/command-manager', \App\Livewire\CommandManager\Index::class)->name('command-manager');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/announcement-manager', \App\Livewire\AnnouncementManager\Index::class)->name('announcement-manager');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/memo-manager', \App\Livewire\MemoManager\Index::class)->name('memo-manager');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', \App\Livewire\Admin\ManageUsers::class)->name('admin.users');
});

Route::get('/officialletters', Index::class)->name('officialletter.index');
Route::get('/officialletters/create', Form::class)->name('officialletter.create');
Route::get('/officialletters/{id}/edit',Form::class)->name('officialletter.edit');
Route::get('/officialletters/{id}/show', Index::class)->name('officialletter.show');




    Route::redirect('settings', 'settings/profile');


    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
