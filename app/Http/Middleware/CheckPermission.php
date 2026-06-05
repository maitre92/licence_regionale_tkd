<?php


namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Création automatique de la permission si elle n'existe pas
        foreach ($permissions as $slug) {
            $exists = \App\Models\Permission::where('slug', $slug)->exists();
            if (!$exists) {
                // Générer un nom et module en français à partir du slug
                $nom = ucwords(str_replace(['_', '-'], [' ', ' '], $slug));
                $module = 'Autre';
                if (str_contains($slug, 'user')) $module = 'Utilisateurs';
                if (str_contains($slug, 'permission')) $module = 'Permissions';
                if (str_contains($slug, 'apprenant') || str_contains($slug, 'learner')) $module = 'Apprenants';
                if (str_contains($slug, 'formation') || str_contains($slug, 'course')) $module = 'Formations';
                if (str_contains($slug, 'categorie') || str_contains($slug, 'category')) $module = 'Catégories de formation';
                if (str_contains($slug, 'attestation')) $module = 'Attestations';
                if (str_contains($slug, 'document')) $module = 'Documents';
                if (str_contains($slug, 'rapport')) $module = 'Rapports';
                if (str_contains($slug, 'note') || str_contains($slug, 'grade')) $module = 'Notes';
                if (str_contains($slug, 'finance')) $module = 'Finances';
                if (str_contains($slug, 'paiement') || str_contains($slug, 'payment')) $module = 'Paiements';
                if (str_contains($slug, 'depense') || str_contains($slug, 'expense')) $module = 'Dépenses';
                if (str_contains($slug, 'recette') || str_contains($slug, 'revenue')) $module = 'Recettes';
                if (str_contains($slug, 'emploi') || str_contains($slug, 'schedule')) $module = 'Emplois du Temps';
                if (str_contains($slug, 'parametre') || str_contains($slug, 'setting')) $module = 'Paramètres';
                \App\Models\Permission::create([
                    'name' => $nom,
                    'slug' => $slug,
                    'module' => $module,
                    'is_active' => true,
                ]);
            }
        }

        // Super admin peut accéder à tout
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Vérifier les permissions
        if (!$user->hasAnyPermission($permissions)) {
            abort(403, 'Vous n\'avez pas la permission d\'accéder à cette page.');
        }

        return $next($request);
    }
}
