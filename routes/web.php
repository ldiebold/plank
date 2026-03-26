<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlankController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('challenges/create', [ChallengeController::class, 'create'])->name('challenges.create');
    Route::post('challenges', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::get('challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::get('join/{inviteCode}', [ChallengeController::class, 'join'])->name('challenges.join');

    Route::get('planks', [PlankController::class, 'index'])->name('planks.index');
    Route::post('planks', [PlankController::class, 'store'])->name('planks.store');
    Route::delete('planks', [PlankController::class, 'destroy'])->name('planks.destroy');
});

require __DIR__.'/settings.php';
