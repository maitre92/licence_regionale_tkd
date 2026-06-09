<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\PermissionService;
use App\Services\UserService;
use App\Shared\Enums\UserRole;
use App\Shared\Enums\UserStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    protected UserService $userService;
    protected PermissionService $permissionService;

    public function __construct(UserService $userService, PermissionService $permissionService)
    {
        $this->userService = $userService;
        $this->permissionService = $permissionService;
        $this->middleware('auth');
        $this->middleware('permission:view_users')->only(['index', 'show']);
        $this->middleware('permission:create_user')->only(['create', 'store']);
        $this->middleware('permission:edit_user')->only(['edit', 'update', 'activate', 'deactivate']);
        $this->middleware('permission:delete_user')->only(['destroy']);
    }

    /**
     * Empêcher les non-superadmins d'accéder aux comptes superadmin
     */
    private function authorizeTarget(User $target): void
    {
        $current = auth()->user();
        if (!$current) {
            abort(403, 'Accès refusé.');
        }

        $currentRole = UserRole::tryFrom($current->role);
        $targetRole = UserRole::tryFrom($target->role);

        if (!$currentRole || !$targetRole) {
            abort(403, 'Accès refusé.');
        }

        if ($currentRole !== UserRole::SUPERADMIN && !$currentRole->canManage($targetRole)) {
            abort(403, 'Accès refusé.');
        }
    }

    private function authorizeActivationTarget(User $target): void
    {
        $this->authorizeTarget($target);

        if (auth()->id() === $target->id) {
            abort(403, 'Vous ne pouvez pas activer ou désactiver votre propre compte.');
        }

        if (in_array($target->role, [UserRole::SUPERADMIN->value, UserRole::ADMIN->value], true)) {
            abort(403, 'Les comptes Super Administrateur et Administrateur ne peuvent pas être activés ou désactivés depuis cette action.');
        }
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index(): View
    {
        $search = request('search');
        $currentUser = auth()->user();
        $visibleRoles = collect(UserRole::visibleBy($currentUser))->pluck('value')->all();
        
        // Construire la requête de base en fonction du rôle
        if ($currentUser->isSuperAdmin()) {
            // Superadmin: voir tout le monde
            $query = User::query();
        } else {
            // Chaque rôle ne voit que les rôles placés sous lui dans la hiérarchie
            $query = User::whereIn('role', $visibleRoles);
        }

        // Appliquer la recherche si elle existe
        if ($search) {
            $query = $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
            $users = $query->paginate(15);
        } else {
            $users = $query->paginate(15);
        }

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'roles' => collect(UserRole::assignableBy($currentUser))->mapWithKeys(fn($role) => [$role->value => $role->label()])->toArray(),
            'statuses' => collect(UserStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])->toArray(),
            'page_title' => 'Utilisateurs',
            'active_menu' => 'users',
        ]);
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => collect(UserRole::assignableBy(auth()->user()))->mapWithKeys(fn($role) => [$role->value => $role->label()])->toArray(),
            'statuses' => collect(UserStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])->toArray(),
            'page_title' => 'Créer un utilisateur',
            'active_menu' => 'users',
        ]);
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(UserRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $user = $this->userService->create($request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'L\'utilisateur a été créé avec succès.',
                    'user' => $user,
                ], 201);
            }

            return $this->redirectAfterMutation($request)
                ->with('success', 'L\'utilisateur a été créé avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('UserController@store failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user): View
    {
        $this->authorizeTarget($user);
        return view('admin.users.show', [
            'user' => $user,
            'page_title' => 'Détails de ' . $user->name,
            'active_menu' => 'users',
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur
     */
    public function edit(User $user): View
    {
        $this->authorizeTarget($user);
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => collect(UserRole::assignableBy(auth()->user()))->mapWithKeys(fn($role) => [$role->value => $role->label()])->toArray(),
            'statuses' => collect(UserStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])->toArray(),
            'page_title' => 'Modifier ' . $user->name,
            'active_menu' => 'users',
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(UserRequest $request, User $user): RedirectResponse|JsonResponse
    {
        $this->authorizeTarget($user);
        try {
            $updated = $this->userService->update($user, $request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'L\'utilisateur a été modifié avec succès.',
                    'user' => $user->fresh(),
                ], 200);
            }

            return $this->redirectAfterMutation($request)
                ->with('success', 'L\'utilisateur a été modifié avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('UserController@update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user): RedirectResponse|JsonResponse
    {
        $this->authorizeTarget($user);
        try {
            $this->userService->delete($user);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'L\'utilisateur a été supprimé avec succès.'], 200);
            }

            return $this->redirectAfterMutation(request())
                ->with('success', 'L\'utilisateur a été supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('UserController@destroy failed', ['error' => $e->getMessage()]);
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Restaurer un utilisateur supprimé
     */
    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->find($id);
        
        if (!$user) {
            return back()->with('error', 'Utilisateur non trouvé.');
        }

        $this->authorizeTarget($user);

        $this->userService->restore($user);

        return back()->with('success', 'L\'utilisateur a été restauré avec succès.');
    }

    /**
     * Activer un utilisateur
     */
    public function activate(User $user): RedirectResponse
    {
        $this->authorizeActivationTarget($user);
        $this->userService->activate($user);

        return back()->with('success', 'L\'utilisateur a été activé avec succès.');
    }

    /**
     * Désactiver un utilisateur
     */
    public function deactivate(User $user): RedirectResponse
    {
        $this->authorizeActivationTarget($user);
        $this->userService->deactivate($user);

        return back()->with('success', 'L\'utilisateur a été désactivé avec succès.');
    }

    /**
     * Afficher le formulaire de gestion des permissions
     */
    public function editPermissions(User $user): View
    {
        $this->authorizeTarget($user);
        $grid = $this->permissionService->getAdminGrid($user->id);

        return view('admin.users.permissions', [
            'user' => $user,
            'modules' => $grid['modules'],
            'userPermissions' => $grid['userPermissions'],
            'page_title' => 'Permissions de ' . $user->name,
            'active_menu' => 'users',
        ]);
    }

    private function redirectAfterMutation($request): RedirectResponse
    {
        if ($request->boolean('back_to_settings')) {
            return redirect()->route('admin.settings', ['tab' => 'users-list']);
        }

        return redirect()->route('admin.users.index');
    }

    /**
     * Mettre à jour les permissions d'un utilisateur
     */
    public function updatePermissions(User $user): RedirectResponse
    {
        $this->authorizeTarget($user);
        $permissionIds = request('permissions', []);
        
        $this->userService->syncPermissions($user, $permissionIds);

        return back()->with('success', 'Les permissions de l\'utilisateur ont été mises à jour.');
    }
}
