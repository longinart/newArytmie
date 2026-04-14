<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Livewire\Admin\ConcertManager;
use App\Livewire\Admin\GalleryManager;
use App\Livewire\Admin\NewsManager;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/aktuality/{slug}', [PublicSiteController::class, 'showNews'])->name('news.show');
Route::get('/koncerty/{slug}', [PublicSiteController::class, 'showConcert'])->name('concerts.show');
Route::get('/galerie', [PublicSiteController::class, 'gallery'])->name('gallery.index');
Route::get('/galerie/{slug}', [PublicSiteController::class, 'showAlbum'])->name('gallery.show');

Route::post('/kontakt', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/news', NewsManager::class)->name('news.index');
    Route::get('/concerts', ConcertManager::class)->name('concerts.index');
    Route::get('/gallery', GalleryManager::class)->name('gallery.index');
});

require __DIR__.'/auth.php';
