<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\FrontController;
use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\CheckOperatorCode;

Route::get('/', function () {
    return view('welcome'); // pubblica
});

// ROTTE OPERATORE (autenticato)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/operator-dashboard', [OperatorController::class, 'index'])->name('operator.index');
    Route::get('/select-pre-assembled', [FrontController::class, 'selectPreAssembled'])->name('select.preassembled');
    Route::get('/lotto-create/{lottoId}', [OperatorController::class, 'lottoCreate'])->name('lotto.create');
    Route::get('/select-lotto', [OperatorController::class, 'selectLotto'])->name('lotto.show');
    Route::get('/edit-lotto/{lottoId}', [OperatorController::class, 'editLotto'])->name('lotto.edit');
    // post routes
    Route::post('/lotto-submit', [OperatorController::class, 'submitLotto'])->name('lotto.submit');
    Route::post('/lotto-update', [OperatorController::class, 'updateLotto'])->name('lotto.update');
});

// ROTTE PROFILO (autenticato)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/today-lottos', [FrontController::class, 'showTodayLottos'])->name('lotto.today');
    Route::get('/download/{filename}', [FrontController::class, 'downloadLotto'])->name('download.lotto');
    Route::get('/show-all-lottos', [FrontController::class, 'showAllLottos'])->name('front.show-all-lottos');
});

// ROTTE ADMIN (autenticato + controllo ruolo)
Route::middleware(['auth', AdminAccess::class])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/edit-users', [AdminController::class, 'editUsers'])->name('admin.editUsers');
    Route::get('/edit-articles', [AdminController::class, 'showArticles'])->name('admin.editArticles');
    Route::get('/audit-logs/{id}', [AdminController::class, 'showAuditLogs'])->name('admin.auditLog');

    // post routes
    Route::post('/create-user', [AdminController::class, 'createUser'])->name('admin.createUser');
    Route::post('/user/{id}/delete', [AdminController::class, 'deleteUser'])->name('user.delete');
    Route::post('/user/{id}/make-admin', [AdminController::class, 'makeAdmin'])->name('user.makeAdmin');
    Route::post('/user/{id}/make-operator', [AdminController::class, 'makeOperator'])->name('user.makeOperator');

    Route::post('/create-article', [AdminController::class, 'createArticle'])->name('article.create');
    Route::post('/article/{id}/delete', [AdminController::class, 'deleteArticle'])->name('article.delete');
    Route::post('/article/{id}/make-moca', [AdminController::class, 'updateArticle'])->name('article.update');

});

Route::middleware(['auth', CheckOperatorCode::class])->group(function () {
    Route::get('/bingo-perasso', [ProfileController::class, 'bingo'])->name('bingo');
    Route::post('/avvia', [ProfileController::class, 'avvia'])->name('bingo.avvia');
});

require __DIR__.'/auth.php';
