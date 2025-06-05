<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OperatorController;
use App\Http\Middleware\AdminAccess;

Route::get('/', function () {
    return view('welcome'); // pubblica
});

// ROTTE OPERATORE (autenticato)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/operator-dashboard', [OperatorController::class, 'index'])->name('operator.index');
    Route::get('/select-pre-assembled', [OperatorController::class, 'selectPreAssembled'])->name('select.preassembled');
    Route::get('/lotto-create/{id}', [OperatorController::class, 'lottoCreate'])->name('lotto.create');
    Route::post('/lotto-submit', [OperatorController::class, 'submitLotto'])->name('lotto.submit');
    Route::get('/select-lotto', [OperatorController::class, 'selectLotto'])->name('lotto.select');
    Route::get('/edit-lotto/{id}', [OperatorController::class, 'editLotto'])->name('lotto.edit');
    Route::post('/lotto-update', [OperatorController::class, 'updateLotto'])->name('lotto.update');
});

// ROTTE PROFILO (autenticato)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ROTTE ADMIN (autenticato + controllo ruolo)
Route::middleware(['auth', AdminAccess::class])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/edit-users', [AdminController::class, 'editUsers'])->name('admin.editUsers');
});


require __DIR__.'/auth.php';
