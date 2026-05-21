<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ApprenantController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\CategorieFormationController;
use App\Http\Controllers\Admin\PedagogieController;
use App\Http\Controllers\Admin\AttestationController;
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

        // Gestion des apprenants
        Route::resource('apprenants', ApprenantController::class);

        // Gestion des formations
        Route::get('formations/formateur/{formateur}', [FormationController::class, 'byFormateur'])->name('formations.formateur');
        Route::patch('formations/{formation}/status', [FormationController::class, 'updateStatus'])->name('formations.status');
        Route::resource('formations', FormationController::class);
        Route::resource('categories-formations', CategorieFormationController::class)
            ->parameters(['categories-formations' => 'categorieFormation'])
            ->only(['index', 'store', 'update', 'destroy']);

        // Paramètres
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        
        // Permissions
        Route::post('/permissions', [SettingsController::class, 'storePermission'])->name('permissions.store');
        Route::put('/permissions/{permission}', [SettingsController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [SettingsController::class, 'destroyPermission'])->name('permissions.destroy');
        Route::post('/permissions/assign', [SettingsController::class, 'assignPermissions'])->name('permissions.assign');

        // Gestion Pédagogique
        Route::prefix('pedagogie')->name('pedagogie.')->group(function () {
            Route::get('/presences', [PedagogieController::class, 'presences'])->name('presences');
            Route::post('/presences', [PedagogieController::class, 'storePresences'])->name('presences.store');
            
            Route::get('/evaluations', [PedagogieController::class, 'evaluations'])->name('evaluations');
            Route::post('/evaluations', [PedagogieController::class, 'storeEvaluation'])->name('evaluations.store');
            
            Route::get('/examens', [PedagogieController::class, 'examens'])->name('examens');
            // Les examens utilisent la même logique que les évaluations mais filtrés par type 'examen'
            
            Route::get('/notes', [PedagogieController::class, 'notes'])->name('notes');
            Route::get('/notes/evaluation/{evaluation}', [PedagogieController::class, 'editNotes'])->name('notes.edit');
            Route::post('/notes/evaluation/{evaluation}', [PedagogieController::class, 'storeNotes'])->name('notes.store');
            
            Route::get('/resultats', [PedagogieController::class, 'resultats'])->name('resultats');
        });

        // Gestion Financière
        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('index');
            Route::get('/paiements', [App\Http\Controllers\Admin\FinanceController::class, 'payments'])->name('payments');
            Route::post('/paiements', [App\Http\Controllers\Admin\FinanceController::class, 'storePayment'])->name('payments.store');
            Route::get('/depenses', [App\Http\Controllers\Admin\FinanceController::class, 'expenses'])->name('expenses');
            Route::post('/depenses', [App\Http\Controllers\Admin\FinanceController::class, 'storeExpense'])->name('expenses.store');
            Route::get('/paiements/{paiement}/recu', [App\Http\Controllers\Admin\FinanceController::class, 'receipt'])->name('payments.receipt');
            
            // Paiements des Formateurs (Commissions)
            Route::get('/formateurs', [App\Http\Controllers\Admin\FinanceController::class, 'trainerPayments'])->name('trainer_payments');
            Route::post('/formateurs', [App\Http\Controllers\Admin\FinanceController::class, 'storeTrainerPayment'])->name('trainer_payments.store');
        });

        // Gestion des Attestations
        Route::resource('attestations', AttestationController::class);
    });

    /**
     * Routes Dashboard utilisateur (redirige vers admin par défaut si admin)
     */
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});
