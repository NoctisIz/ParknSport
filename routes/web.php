<?php

use App\Models\MapController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
require __DIR__.'/auth.php';


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/', [MapController::class, 'map'])->name('map.index');
Route::get('/markers', [MapController::class, 'getMarkers'])->name('map.markers');
Route::get('/', function () {
    return view('map'); // Vue par défaut ou remplacez par votre vue personnalisée
})->name('home');

// Page "À propos"
Route::get('/about', function () {
    return view('about'); // Créez une vue about.blade.php
})->name('about');

// Page "Contact"
Route::get('/contact', function () {
    return view('contact'); // Créez une vue contact.blade.php
})->name('contact');
