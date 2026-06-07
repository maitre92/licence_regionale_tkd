<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Utilisateurs
            ['name' => 'Voir les utilisateurs', 'module' => 'Utilisateurs', 'slug' => 'view_users'],
            ['name' => 'Ajouter un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'create_user'],
            ['name' => 'Modifier un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'edit_user'],
            ['name' => 'Supprimer un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'delete_user'],

            // Permissions
            ['name' => 'Voir les permissions', 'module' => 'Permissions', 'slug' => 'view_permissions'],
            ['name' => 'Gérer les permissions', 'module' => 'Permissions', 'slug' => 'manage_permissions'],
            ['name' => 'Ajouter une permission', 'module' => 'Permissions', 'slug' => 'create_permission'],
            ['name' => 'Supprimer une permission', 'module' => 'Permissions', 'slug' => 'delete_permission'],

            // Apprenants
            ['name' => 'Voir les apprenants', 'module' => 'Apprenants', 'slug' => 'view_learners'],
            ['name' => 'Ajouter un apprenant', 'module' => 'Apprenants', 'slug' => 'create_learner'],
            ['name' => 'Modifier un apprenant', 'module' => 'Apprenants', 'slug' => 'edit_learner'],
            ['name' => 'Supprimer un apprenant', 'module' => 'Apprenants', 'slug' => 'delete_learner'],
            ['name' => 'Voir les détails apprenant', 'module' => 'Apprenants', 'slug' => 'view_learner_details'],

            // Formations
            ['name' => 'Voir les formations', 'module' => 'Formations', 'slug' => 'voir_formations', 'action' => 'view', 'order' => 30],
            ['name' => 'Ajouter une formation', 'module' => 'Formations', 'slug' => 'ajouter_formation', 'action' => 'create', 'order' => 31],
            ['name' => 'Modifier une formation', 'module' => 'Formations', 'slug' => 'modifier_formation', 'action' => 'edit', 'order' => 32],
            ['name' => 'Supprimer une formation', 'module' => 'Formations', 'slug' => 'supprimer_formation', 'action' => 'delete', 'order' => 33],
            ['name' => 'Voir les détails formation', 'module' => 'Formations', 'slug' => 'voir_details_formation', 'action' => 'view_details', 'order' => 34],

            // Catégories de formation
            ['name' => 'Voir les catégories de formation', 'module' => 'Catégories de formation', 'slug' => 'voir_categories_formations', 'action' => 'view', 'order' => 35],
            ['name' => 'Ajouter une catégorie de formation', 'module' => 'Catégories de formation', 'slug' => 'ajouter_categorie_formation', 'action' => 'create', 'order' => 36],
            ['name' => 'Modifier une catégorie de formation', 'module' => 'Catégories de formation', 'slug' => 'modifier_categorie_formation', 'action' => 'edit', 'order' => 37],
            ['name' => 'Supprimer une catégorie de formation', 'module' => 'Catégories de formation', 'slug' => 'supprimer_categorie_formation', 'action' => 'delete', 'order' => 38],
            ['name' => 'Gérer les catégories de formation', 'module' => 'Catégories de formation', 'slug' => 'gerer_categories_formations', 'action' => 'manage', 'order' => 39],

            // Pédagogique
            ['name' => 'Voir le module pédagogique', 'module' => 'Pédagogique', 'slug' => 'view_pedagogical'],
            ['name' => 'Voir les présences', 'module' => 'Présences', 'slug' => 'view_attendance'],
            ['name' => 'Voir les évaluations', 'module' => 'Évaluations', 'slug' => 'view_evaluations'],
            ['name' => 'Voir les examens', 'module' => 'Examens', 'slug' => 'view_exams'],
            ['name' => 'Voir les notes', 'module' => 'Notes', 'slug' => 'view_grades'],

            // Emplois du temps
            ['name' => 'Voir les emplois du temps', 'module' => 'Emplois du Temps', 'slug' => 'view_schedules'],

            // Finances
            ['name' => 'Voir les finances', 'module' => 'Finances', 'slug' => 'view_finances'],
            ['name' => 'Voir les paiements', 'module' => 'Paiements', 'slug' => 'view_payments'],
            ['name' => 'Voir les dépenses', 'module' => 'Dépenses', 'slug' => 'view_expenses'],
            ['name' => 'Voir les recettes', 'module' => 'Recettes', 'slug' => 'view_revenue'],
            ['name' => 'Modifier une dépense', 'module' => 'Dépenses', 'slug' => 'edit_expense'],
            ['name' => 'Supprimer une dépense', 'module' => 'Dépenses', 'slug' => 'delete_expense'],
            ['name' => 'Modifier un versement formateur', 'module' => 'Finances', 'slug' => 'edit_trainer_payment'],
            ['name' => 'Supprimer un versement formateur', 'module' => 'Finances', 'slug' => 'delete_trainer_payment'],

            // Attestations
            ['name' => 'Voir les attestations', 'module' => 'Attestations', 'slug' => 'view_certificates'],

            // Documents
            ['name' => 'Voir les documents', 'module' => 'Documents', 'slug' => 'view_documents'],

            // Rapports
            ['name' => 'Voir les rapports', 'module' => 'Rapports', 'slug' => 'view_reports'],

            // Mouvements / Pilotage
            ['name' => 'Voir les mouvements et le pilotage', 'module' => 'Mouvements', 'slug' => 'view_movements'],

            // Traçabilité
            ['name' => 'Voir la traçabilité', 'module' => 'Traçabilité', 'slug' => 'view_audit'],

            // Paramètres
            ['name' => 'manage_settings', 'module' => 'Paramètres', 'slug' => 'manage_settings'],
        ];

        foreach ($permissions as $permission) {
            $existing = Permission::withTrashed()->where('slug', $permission['slug'])->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }

                $nameAlreadyUsed = Permission::where('name', $permission['name'])
                    ->where('id', '!=', $existing->id)
                    ->exists();

                if ($nameAlreadyUsed) {
                    unset($permission['name']);
                }

                $existing->update($permission);
            } else {
                if (Permission::where('name', $permission['name'])->exists()) {
                    $permission['name'] = $permission['name'] . ' (' . $permission['slug'] . ')';
                }

                Permission::create($permission);
            }
        }
    }
}
