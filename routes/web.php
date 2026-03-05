<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WordForgeController;
use App\Http\Controllers\FindTheEmojiController;
use App\Http\Controllers\SequenceRushController;
use App\Http\Controllers\FlagGuessController;
use App\Http\Controllers\BlockDropController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/daily-challenges', [DashboardController::class, 'dailyChallenges'])->name('dashboard.daily');
    Route::post('/daily-challenges/quests/claim', [DashboardController::class, 'claimDailyQuests'])->name('dashboard.daily.quests.claim');
    Route::post('/daily-challenges/quests/claim-single', [DashboardController::class, 'claimSingleQuest'])->name('dashboard.daily.quests.claim.single');

    Route::get('/games/word-forge', [WordForgeController::class, 'show'])->name('games.wordforge');
    Route::post('/games/word-forge/guess', [WordForgeController::class, 'guess'])->name('games.wordforge.guess');

    Route::get('/games/find-the-emoji', [FindTheEmojiController::class, 'show'])->name('games.findtheemoji');
    Route::post('/games/find-the-emoji/solve', [FindTheEmojiController::class, 'solve'])->name('games.findtheemoji.solve');

    Route::get('/games/sequence-rush', [SequenceRushController::class, 'show'])->name('games.sequence');
    Route::post('/games/sequence-rush/answer', [SequenceRushController::class, 'answer'])->name('games.sequence.answer');

    Route::get('/games/flag-guess', [FlagGuessController::class, 'show'])->name('games.flagguess');
    Route::post('/games/flag-guess/answer', [FlagGuessController::class, 'answer'])->name('games.flagguess.answer');

    Route::get('/games/block-drop', [BlockDropController::class, 'show'])->name('games.blockdrop');
    Route::post('/games/block-drop/finish', [BlockDropController::class, 'finish'])->name('games.blockdrop.finish');

    Route::post('/games/abandon', [DashboardController::class, 'abandonGame'])->name('games.abandon');
    Route::post('/games/mark-started', [DashboardController::class, 'markGameStarted'])->name('games.mark-started');

    Route::get('/leaderboard', [DashboardController::class, 'leaderboard'])->name('leaderboard');

    Route::post('/profile/media', [DashboardController::class, 'updateProfileMedia'])->name('profile.media');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});