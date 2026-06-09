<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\LicenceHolderController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/**
 * Routes publiques
 */
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/**
 * Routes d'authentification (sans protection)
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.store');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/**
 * Routes protégées par authentification
 */
Route::middleware('auth')->group(function () {
    
    /**
     * Déconnexion
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']);

    /**
     * Changement de mot de passe
     */
    Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

    /**
     * Routes Admin (protégées par authentification et permission admin)
     */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Tableau de bord
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('dashboard.stats');

        // Gestion des utilisateurs
        Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::resource('users', UserController::class);

        // Gestion des cartes de licence
        Route::get('cartes/planches/imprimer', [LicenceHolderController::class, 'printSheet'])->name('cards.sheet.print');
        Route::get('cartes/planches/telecharger', [LicenceHolderController::class, 'downloadSheet'])->name('cards.sheet.download');
        Route::get('cartes/mise-a-jour-grades', [LicenceHolderController::class, 'gradeUpdates'])->name('cards.grade-updates');
        Route::post('cartes/mise-a-jour-grades', [LicenceHolderController::class, 'applyGradeUpdates'])->name('cards.grade-updates.apply');
        Route::get('cartes/{licenceHolder}/generer', [LicenceHolderController::class, 'card'])->name('cards.card');
        Route::get('cartes/{licenceHolder}/telecharger', [LicenceHolderController::class, 'download'])->name('cards.download');
        Route::get('cartes/{licenceHolder}/imprimer', [LicenceHolderController::class, 'print'])->name('cards.print');
        Route::resource('cartes', LicenceHolderController::class)
            ->names('cards')
            ->parameters(['cartes' => 'licenceHolder']);

        // Paramètres
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        
        // Permissions
        Route::post('/permissions', [SettingsController::class, 'storePermission'])->name('permissions.store');
        Route::put('/permissions/{permission}', [SettingsController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [SettingsController::class, 'destroyPermission'])->name('permissions.destroy');
        Route::post('/permissions/assign', [SettingsController::class, 'assignPermissions'])->name('permissions.assign');
    });

    /**
     * Routes Dashboard utilisateur (redirige vers admin par défaut si admin)
     */
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});
