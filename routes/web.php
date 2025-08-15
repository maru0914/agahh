<?php

use App\Http\Controllers\DiaryCommentController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyLogController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/', WelcomeController::class)->name('home');

    Route::resource('diaries', DiaryController::class)->except(['create', 'store', 'edit']);
    Route::get('/diaries/{date}/create', [DiaryController::class, 'create'])->name('diaries.create');
    Route::post('/diaries/{date}', [DiaryController::class, 'store'])->name('diaries.store');

    Route::post('/diaries/{diary}/comments', [DiaryCommentController::class, 'store'])->name('diaries.comments.store');

    // Study Logs
    Route::resource('study-logs', StudyLogController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
