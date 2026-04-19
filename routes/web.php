<?php

use App\Http\Controllers\Admin\EditorUploadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryPhotoController;
use App\Http\Controllers\MemberMaterialFileController;
use App\Http\Controllers\MembersAreaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Livewire\Admin\ConcertManager;
use App\Livewire\Admin\GalleryManager;
use App\Livewire\Admin\MemberResourcesManager;
use App\Livewire\Admin\NewsManager;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/o-nas', [PublicSiteController::class, 'about'])->name('about');
Route::get('/aktuality/{slug}', [PublicSiteController::class, 'showNews'])->name('news.show');
Route::get('/koncerty/{slug}', [PublicSiteController::class, 'showConcert'])->name('concerts.show');
Route::get('/galerie', [PublicSiteController::class, 'gallery'])->name('gallery.index');
Route::get('/galerie/fotka/{photo}/nahled', [GalleryPhotoController::class, 'thumbnail'])
    ->name('gallery.photo.thumb');
Route::get('/galerie/fotka/{photo}/velke', [GalleryPhotoController::class, 'large'])
    ->name('gallery.photo.large');
Route::get('/galerie/{slug}', [PublicSiteController::class, 'showAlbum'])->name('gallery.show');

Route::get('/pro-cleny', [MembersAreaController::class, 'show'])->name('members.index');
Route::post('/pro-cleny', [MembersAreaController::class, 'unlock'])
    ->middleware('throttle:10,1')
    ->name('members.unlock');
Route::post('/pro-cleny/zavrit', [MembersAreaController::class, 'lock'])->name('members.lock');

Route::middleware('members.unlocked')->prefix('pro-cleny')->name('members.')->group(function () {
    Route::get('/harmonogram', [MembersAreaController::class, 'harmonogram'])->name('harmonogram');
    Route::get('/naslechy', [MembersAreaController::class, 'naslechy'])->name('naslechy');
    Route::get('/noty', [MembersAreaController::class, 'noty'])->name('noty');
    Route::get('/soubor/{memberResourceFile}', [MemberMaterialFileController::class, 'download'])->name('file.download');
});

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
    Route::get('/member-materials', MemberResourcesManager::class)->name('member-materials.index');
    Route::post('/editor-upload', EditorUploadController::class)->name('editor.upload');
});

require __DIR__.'/auth.php';
