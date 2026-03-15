<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WordForgeController;
use App\Http\Controllers\FindTheEmojiController;
use App\Http\Controllers\SequenceRushController;
use App\Http\Controllers\FlagGuessController;
use App\Http\Controllers\BlockDropController;
use App\Http\Controllers\SudokuController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\MemoryGridController;
use App\Http\Controllers\ColorMatchController;
use App\Http\Controllers\ReactionTimeController;
use App\Http\Controllers\MazeRunnerController;
use App\Http\Controllers\ColorSortController;
use App\Http\Controllers\IqTestController;
use App\Http\Controllers\ChatController;

// Stripe webhook (no CSRF, no auth)
Route::post('/stripe/webhook', [\App\Http\Controllers\WebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/games', fn() => view('pages.games'))->name('pages.games');
Route::get('/hoe-het-werkt', fn() => view('pages.how-it-works'))->name('pages.how');
Route::get('/pricing', fn() => view('pages.pricing'))->name('pages.pricing');

// Game landing pages (SEO)
Route::get('/games/word-forge-info', fn() => view('pages.games.word-forge'))->name('pages.games.word-forge');
Route::get('/games/find-the-emoji-info', fn() => view('pages.games.find-the-emoji'))->name('pages.games.find-the-emoji');
Route::get('/games/sequence-rush-info', fn() => view('pages.games.sequence-rush'))->name('pages.games.sequence-rush');
Route::get('/games/flag-guess-info', fn() => view('pages.games.flag-guess'))->name('pages.games.flag-guess');
Route::get('/games/block-drop-info', fn() => view('pages.games.block-drop'))->name('pages.games.block-drop');
Route::get('/games/sudoku-info', fn() => view('pages.games.sudoku'))->name('pages.games.sudoku');
Route::get('/games/memory-grid-info', fn() => view('pages.games.memory-grid'))->name('pages.games.memory-grid');
Route::get('/games/color-match-info', fn() => view('pages.games.color-match'))->name('pages.games.color-match');
Route::get('/games/reaction-time-info', fn() => view('pages.games.reaction-time'))->name('pages.games.reaction-time');
Route::get('/games/maze-runner-info', fn() => view('pages.games.maze-runner'))->name('pages.games.maze-runner');
Route::get('/games/color-sort-info', fn() => view('pages.games.color-sort'))->name('pages.games.color-sort');

// Category landing pages (SEO)
Route::get('/categorie/hersenkrakers', fn() => view('pages.categorie.hersenkrakers'))->name('pages.categorie.hersenkrakers');
Route::get('/categorie/geheugentraining', fn() => view('pages.categorie.geheugentraining'))->name('pages.categorie.geheugentraining');
Route::get('/categorie/logica-strategie', fn() => view('pages.categorie.logica-strategie'))->name('pages.categorie.logica-strategie');
Route::get('/categorie/snelheid-reactie', fn() => view('pages.categorie.snelheid-reactie'))->name('pages.categorie.snelheid-reactie');

// Legal pages
Route::get('/algemene-voorwaarden', fn() => view('pages.algemene-voorwaarden'))->name('pages.terms');
Route::get('/privacybeleid', fn() => view('pages.privacybeleid'))->name('pages.privacy');
Route::get('/cookiebeleid', fn() => view('pages.cookiebeleid'))->name('pages.cookies');

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

    Route::get('/games/sudoku', [SudokuController::class, 'show'])->name('games.sudoku');
    Route::post('/games/sudoku/check', [SudokuController::class, 'check'])->name('games.sudoku.check');

    Route::get('/games/memory-grid', [MemoryGridController::class, 'show'])->name('games.memorygrid');
    Route::post('/games/memory-grid/solve', [MemoryGridController::class, 'solve'])->name('games.memorygrid.solve');

    Route::get('/games/color-match', [ColorMatchController::class, 'show'])->name('games.colormatch');
    Route::post('/games/color-match/solve', [ColorMatchController::class, 'solve'])->name('games.colormatch.solve');

    Route::get('/games/reaction-time', [ReactionTimeController::class, 'show'])->name('games.reactiontime');
    Route::post('/games/reaction-time/solve', [ReactionTimeController::class, 'solve'])->name('games.reactiontime.solve');

    Route::get('/games/maze-runner', [MazeRunnerController::class, 'show'])->name('games.mazerunner');
    Route::post('/games/maze-runner/solve', [MazeRunnerController::class, 'solve'])->name('games.mazerunner.solve');

    Route::get('/games/color-sort', [ColorSortController::class, 'show'])->name('games.colorsort');
    Route::post('/games/color-sort/solve', [ColorSortController::class, 'solve'])->name('games.colorsort.solve');

    Route::get('/games/iq-test', [IqTestController::class, 'show'])->name('games.iqtest');

    Route::post('/games/share-preview', [DashboardController::class, 'sharePreview'])->name('games.share-preview');
    Route::post('/games/share-score', [DashboardController::class, 'shareScore'])->name('games.share-score');
    Route::post('/games/abandon', [DashboardController::class, 'abandonGame'])->name('games.abandon');
    Route::post('/games/mark-started', [DashboardController::class, 'markGameStarted'])->name('games.mark-started');

    Route::get('/leaderboard', [DashboardController::class, 'leaderboard'])->name('leaderboard');

    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');

    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [DashboardController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [DashboardController::class, 'updatePassword'])->name('settings.password');

    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::post('/shop/buy', [ShopController::class, 'buy'])->name('shop.buy');
    Route::post('/shop/buy-bundle', [ShopController::class, 'buyBundle'])->name('shop.buyBundle');
    Route::post('/shop/equip', [ShopController::class, 'equip'])->name('shop.equip');
    Route::post('/shop/unequip', [ShopController::class, 'unequip'])->name('shop.unequip');

    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::get('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    Route::get('/users/{user}', [FriendController::class, 'profile'])->name('users.profile');
    Route::post('/friends/request', [FriendController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/{friendship}/accept', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/{friendship}/decline', [FriendController::class, 'declineRequest'])->name('friends.decline');
    Route::delete('/friends/{user}', [FriendController::class, 'removeFriend'])->name('friends.remove');

    Route::post('/profile/media', [DashboardController::class, 'updateProfileMedia'])->name('profile.media');

    // Chat routes
    Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('/chat/messages/{user}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/unread', [ChatController::class, 'unreadCount'])->name('chat.unread');

    // Subscription routes
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/portal', [SubscriptionController::class, 'portal'])->name('subscription.portal');
    Route::get('/subscription/status', [SubscriptionController::class, 'status'])->name('subscription.status');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.toggle-admin');
    Route::post('/users/{user}/downgrade', [AdminController::class, 'downgradeUser'])->name('admin.downgrade');
});