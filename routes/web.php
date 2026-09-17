<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/search', [ProgramController::class, 'search'])->name('programs.search');
    Route::get('/programs/compare', [ProgramController::class, 'compare'])->name('programs.compare');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::patch('/steps/{step}', [ApplicationController::class, 'updateStep'])->name('steps.update');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});
Route::get('/faq', function () {
    return view('frontend.faq');
})->name('faq.index');

// TODO: these routes are protected by `auth` only - any logged-in user can access
// the admin panel. Role-based restriction is out of scope for this thesis iteration
// (see Section 5.2.6 / Chapter 9 limitations) and is recommended future work.
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('universities', UniversityController::class)->except(['show']);
    Route::resource('programs', AdminProgramController::class)->except(['show']);

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');

    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
});

require __DIR__ . '/auth.php';
