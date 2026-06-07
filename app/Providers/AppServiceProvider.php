<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Database\Seeders\PermissionSeeder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer les gates dynamiquement pour les permissions
        Gate::define('manage-settings', function (User $user) {
            return $user->isSuperAdmin() || $user->hasPermission('manage_settings');
        });

        $this->ensureDefaultPermissions();

        // Définir dynamiquement les gates pour chaque permission active en base
        if ($this->hasTableSafely('permissions')) {
            $permissionSlugs = Permission::where('is_active', true)->pluck('slug')->toArray();
            foreach ($permissionSlugs as $permission) {
                Gate::define($permission, function (User $user) use ($permission) {
                    return $user->isSuperAdmin() || $user->hasPermission($permission);
                });
            }
        }
    }

    private function ensureDefaultPermissions(): void
    {
        if (!$this->hasTableSafely('permissions') || !$this->hasTableSafely('users') || !$this->hasTableSafely('user_permissions')) {
            return;
        }

        if (Permission::count() === 0) {
            (new PermissionSeeder())->run();
        }

        $permissionSlugs = Permission::where('is_active', true)->pluck('slug')->toArray();
        if (empty($permissionSlugs)) {
            return;
        }

        $superAdmins = User::where('role', UserRole::SUPERADMIN->value)->get();

        // Si aucun superadmin n'existe, créer deux comptes par défaut à partir des variables d'environnement
        if ($superAdmins->count() === 0) {
            $defaults = [
                [
                    'name' => env('DEFAULT_SUPERADMIN_NAME_1', 'Barry Moustapha'),
                    'email' => env('DEFAULT_SUPERADMIN_EMAIL_1', 'barrymoustapha485@gmail.com'),
                    'password' => env('DEFAULT_SUPERADMIN_PASSWORD_1', 'superadmin123'),
                ],
                [
                    'name' => env('DEFAULT_SUPERADMIN_NAME_2', 'Oumar Ouolo'),
                    'email' => env('DEFAULT_SUPERADMIN_EMAIL_2', 'oumarouolo2023@gmail.com'),
                    'password' => env('DEFAULT_SUPERADMIN_PASSWORD_2', 'superadmin123'),
                ],
            ];

            foreach ($defaults as $d) {
                try {
                    $user = User::firstOrCreate(
                        ['email' => $d['email']],
                        [
                            'name' => $d['name'],
                            'password' => Hash::make($d['password']),
                            'role' => UserRole::SUPERADMIN->value,
                            'is_active' => true,
                            'status' => null,
                        ]
                    );

                    // S'assurer que le rôle est bien superadmin (au cas où il existait déjà avec un autre rôle)
                    if ($user->role !== UserRole::SUPERADMIN->value) {
                        $user->role = UserRole::SUPERADMIN->value;
                        $user->save();
                    }

                    // Attribuer les permissions initiales
                    $user->grantPermissions($permissionSlugs, 'Création automatique du superadmin');
                } catch (\Exception $ex) {
                    Log::warning('Impossible de créer le superadmin par défaut: ' . $ex->getMessage());
                }
            }
        } else {
            foreach ($superAdmins as $superAdmin) {
                $superAdmin->grantPermissions($permissionSlugs, 'Initialisation automatique des permissions pour superadmin');
            }
        }

        User::where('role', UserRole::ADMIN->value)
            ->get()
            ->each(function (User $admin) use ($permissionSlugs) {
                $admin->grantPermissions($permissionSlugs, 'Initialisation automatique des permissions pour admin');
            });
    }

    private function hasTableSafely(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $exception) {
            Log::warning("Impossible de vérifier la table {$table}: " . $exception->getMessage());
            return false;
        }
    }
}
