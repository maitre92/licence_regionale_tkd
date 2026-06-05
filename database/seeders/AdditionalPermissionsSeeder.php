<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class AdditionalPermissionsSeeder extends Seeder
{
    public function run()
    {
        $additionalPermissions = [
            // Dashboard
            ['name' => 'Accéder au tableau de bord', 'slug' => 'access_dashboard', 'module' => 'dashboard', 'action' => 'view', 'order' => 1],
            
            // Gérer catégories formations
            ['name' => 'Gérer les catégories de formation', 'slug' => 'gerer_categories_formations', 'module' => 'Catégories de formation', 'action' => 'manage', 'order' => 39],
        ];
        
        foreach ($additionalPermissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
