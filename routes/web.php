<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', function () {
    return view('index'); // Vue par défaut ou remplacez par votre vue personnalisée
})->name('home');

// Page "À propos"
Route::get('/about', function () {
    return view('about'); // Créez une vue about.blade.php
})->name('about');

// Page "Contact"
Route::get('/contact', function () {
    return view('contact'); // Créez une vue contact.blade.php
})->name('contact');
