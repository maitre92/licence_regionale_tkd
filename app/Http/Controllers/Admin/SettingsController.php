<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Salle;
use App\Models\User;
use App\Shared\Enums\UserRole;
use App\Shared\Enums\UserStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Contrôleur pour les paramètres de l'application
 */
class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->middleware('permission:create_permission,manage_permissions')->only(['storePermission']);
        $this->middleware('permission:delete_permission,manage_permissions')->only(['destroyPermission']);
        $this->middleware('permission:manage_permissions')->only(['updatePermission']);
    }

    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        $currentUser = auth()->user();
        $visibleRoles = collect(UserRole::visibleBy($currentUser))->pluck('value')->all();

        $users = User::with('permissions')
            ->when(!$currentUser?->isSuperAdmin(), function ($query) use ($visibleRoles) {
                $query->whereIn('role', $visibleRoles);
            })
            ->orderBy('name')
            ->get();

        $permissionSearch = request('permission_search');

        $allPermissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('order')
            ->get();

        $permissions = $allPermissions;

        // Grouper les permissions par module
        $permissionsByModule = $allPermissions->groupBy('module');
        $salles = Salle::orderBy('nom')->get();

        return view('admin.settings', [
            'users' => $users,
            'permissions' => $permissions,
            'permissionsByModule' => $permissionsByModule,
            'salles' => $salles,
            'permissionSearch' => $permissionSearch,
            'roles' => collect(UserRole::assignableBy($currentUser))->mapWithKeys(fn($role) => [$role->value => $role->label()])->toArray(),
            'statuses' => collect(UserStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])->toArray(),
            'page_title' => 'Paramètres',
            'active_menu' => 'settings',
        ]);
    }

    public function storeSalle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'capacite' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'nom.required' => 'Le nom de la salle est obligatoire.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['slug'] = Str::slug($validated['nom']);

        $existingSalle = Salle::withTrashed()
            ->where('nom', $validated['nom'])
            ->orWhere('slug', $validated['slug'])
            ->first();

        if ($existingSalle) {
            if ($existingSalle->trashed()) {
                $existingSalle->restore();
                $existingSalle->update($validated);

                return redirect()->route('admin.settings', ['tab' => 'salles-list'])
                    ->with('success', 'Salle restaurée avec succès');
            }

            return redirect()->route('admin.settings', ['tab' => 'salles-list'])
                ->withInput()
                ->with('warning', 'Cette salle existe déjà.');
        }

        Salle::create($validated);

        return redirect()->route('admin.settings', ['tab' => 'salles-list'])
            ->with('success', 'Salle créée avec succès');
    }

    public function updateSalle(Request $request, Salle $salle): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('salles', 'nom')->ignore($salle->id),
            ],
            'description' => ['nullable', 'string'],
            'capacite' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'nom.required' => 'Le nom de la salle est obligatoire.',
            'nom.unique' => 'Cette salle existe déjà.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = Str::slug($validated['nom']);

        if (Salle::where('slug', $validated['slug'])->whereKeyNot($salle->id)->exists()) {
            return redirect()->route('admin.settings', ['tab' => 'salles-list'])
                ->withInput()
                ->with('warning', 'Une salle avec un nom équivalent existe déjà.');
        }

        $salle->update($validated);

        return redirect()->route('admin.settings', ['tab' => 'salles-list'])
            ->with('success', 'Salle mise à jour avec succès');
    }

    public function destroySalle(Salle $salle): RedirectResponse
    {
        $salle->delete();

        return redirect()->route('admin.settings', ['tab' => 'salles-list'])
            ->with('success', 'Salle supprimée avec succès');
    }

    /**
     * Stocker une nouvelle permission
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'module' => 'required|string|max:255',
        ], [
            'name.required' => 'Le nom de la permission est obligatoire.',
            'name.max' => 'Le nom de la permission ne doit pas dépasser 255 caractères.',
            'module.required' => 'Le module est obligatoire.',
            'module.max' => 'Le module ne doit pas dépasser 255 caractères.',
        ]);

        $validated = $this->preparePermissionData($validated);

        $existingPermission = Permission::withTrashed()
            ->where('name', $validated['name'])
            ->orWhere('slug', $validated['slug'])
            ->first();

        if ($existingPermission) {
            if ($existingPermission->trashed()) {
                $existingPermission->restore();
                $existingPermission->update($validated);

                return redirect()->route('admin.settings', ['tab' => 'permissions-list'])
                    ->with('success', 'Permission restaurée avec succès');
            }

            return redirect()->route('admin.settings', ['tab' => 'permissions-list'])
                ->with('warning', "La permission '{$existingPermission->name}' existe déjà dans le module {$existingPermission->module}.");
        }

        try {
            Permission::create($validated);
        } catch (QueryException $exception) {
            return redirect()->route('admin.settings', ['tab' => 'permissions-list'])
                ->withInput($request->input())
                ->with('warning', 'Cette permission existe déjà ou utilise un identifiant interne déjà présent.');
        }

        return redirect()->route('admin.settings', ['tab' => 'permissions-list'])
            ->with('success', 'Permission ajoutée avec succès');
    }

    /**
     * Mettre à jour une permission
     */
    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'module' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('permissions', 'slug')->ignore($permission->id),
            ],
        ], [
            'name.required' => 'Le nom de la permission est obligatoire.',
            'name.unique' => 'Une permission avec ce nom existe déjà. Veuillez choisir un autre nom.',
            'name.max' => 'Le nom de la permission ne doit pas dépasser 255 caractères.',
            'module.required' => 'Le module est obligatoire.',
            'module.max' => 'Le module ne doit pas dépasser 255 caractères.',
            'slug.unique' => 'Cet identifiant interne existe déjà.',
            'slug.max' => 'L\'identifiant interne ne doit pas dépasser 255 caractères.',
        ]);

        $validated = $this->preparePermissionData($validated, $permission);

        $permission->update($validated);

        return redirect()->route('admin.settings', ['tab' => 'permissions-list'])
            ->with('success', 'Permission modifiée avec succès');
    }

    /**
     * Supprimer une permission
     */
    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('admin.settings', ['tab' => 'permissions-list'])
            ->with('success', 'Permission supprimée avec succès');
    }

    private function preparePermissionData(array $data, ?Permission $permission = null): array
    {
        $data['slug'] = $permission?->slug ?? Str::slug($data['name'], '_');
        $data['action'] = Str::before($data['slug'], '_') ?: ($permission?->action ?? 'manage');
        $data['is_active'] = true;

        if (!$permission) {
            $data['order'] = ((int) Permission::where('module', $data['module'])->max('order')) + 1;
        }

        return $data;
    }

    /**
     * Assigner des permissions à un utilisateur
     */
    public function assignPermissions(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $currentRole = UserRole::tryFrom(auth()->user()?->role ?? '');
        $targetRole = UserRole::tryFrom($user->role);

        if (!$currentRole || !$targetRole || ($currentRole !== UserRole::SUPERADMIN && !$currentRole->canManage($targetRole))) {
            abort(403, 'Accès refusé.');
        }

        $permissions = $validated['permissions'] ?? [];

        // Sync des permissions utilisateur
        $user->permissions()->sync($permissions);

        return redirect()->route('admin.settings', ['tab' => 'permissions-assign'])
            ->with('success', 'Permissions de l\'utilisateur mises à jour avec succès');
    }

    /**
     * Mettre à jour les paramètres généraux
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string',
            'items_per_page' => 'required|integer|min:5|max:100',
        ]);

        // Sauvegarder les paramètres (exemple avec cache ou DB)
        foreach ($validated as $key => $value) {
            cache(["setting.$key" => $value]);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Paramètres mis à jour avec succès');
    }
}
