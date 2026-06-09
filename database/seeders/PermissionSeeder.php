<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Voir les utilisateurs', 'module' => 'Utilisateurs', 'slug' => 'view_users', 'action' => 'view', 'order' => 1],
            ['name' => 'Ajouter un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'create_user', 'action' => 'create', 'order' => 2],
            ['name' => 'Modifier un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'edit_user', 'action' => 'edit', 'order' => 3],
            ['name' => 'Supprimer un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'delete_user', 'action' => 'delete', 'order' => 4],

            ['name' => 'Voir les permissions', 'module' => 'Permissions', 'slug' => 'view_permissions', 'action' => 'view', 'order' => 10],
            ['name' => 'Gérer les permissions', 'module' => 'Permissions', 'slug' => 'manage_permissions', 'action' => 'manage', 'order' => 11],
            ['name' => 'Ajouter une permission', 'module' => 'Permissions', 'slug' => 'create_permission', 'action' => 'create', 'order' => 12],
            ['name' => 'Supprimer une permission', 'module' => 'Permissions', 'slug' => 'delete_permission', 'action' => 'delete', 'order' => 13],

            ['name' => 'Voir les cartes', 'module' => 'Cartes', 'slug' => 'view_licence_holders', 'action' => 'view', 'order' => 20],
            ['name' => 'Ajouter une carte', 'module' => 'Cartes', 'slug' => 'create_licence_holder', 'action' => 'create', 'order' => 21],
            ['name' => 'Modifier une carte', 'module' => 'Cartes', 'slug' => 'edit_licence_holder', 'action' => 'edit', 'order' => 22],
            ['name' => 'Supprimer une carte', 'module' => 'Cartes', 'slug' => 'delete_licence_holder', 'action' => 'delete', 'order' => 23],

            ['name' => 'Gérer les paramètres', 'module' => 'Paramètres', 'slug' => 'manage_settings', 'action' => 'manage', 'order' => 30],
        ];

        foreach ($permissions as $permission) {
            $model = Permission::withTrashed()->firstOrNew(['slug' => $permission['slug']]);

            if ($model->exists && $model->trashed()) {
                $model->restore();
            }

            $model->fill(array_merge($permission, ['is_active' => true]));
            $model->save();
        }
    }
}
