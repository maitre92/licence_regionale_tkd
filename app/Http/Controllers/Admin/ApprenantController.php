<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprenantRequest;
use App\Models\Apprenant;
use App\Services\ApprenantService;
use App\Shared\Enums\ApprenantStatut;
use App\Shared\Enums\NiveauEtude;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApprenantController extends Controller
{
    protected ApprenantService $apprenantService;

    public function __construct(ApprenantService $apprenantService)
    {
        $this->apprenantService = $apprenantService;
        $this->middleware('auth');
        $this->middleware('permission:view_learners')->only(['index']);
        $this->middleware('permission:create_learner')->only(['create', 'store']);
        $this->middleware('permission:view_learner_details')->only(['show']);
        $this->middleware('permission:edit_learner')->only(['edit', 'update']);
        $this->middleware('permission:delete_learner')->only(['destroy']);
    }

    /**
     * Afficher la liste des apprenants
     */
    public function index(): View
    {
        $search = request('search');
        $statut = request('statut');
        $niveauEtude = request('niveau_etude');

        $apprenants = $this->apprenantService->getAllPaginated(
            search: $search,
            statut: $statut,
            niveauEtude: $niveauEtude,
            perPage: 15
        );

        return view('admin.apprenants.index', [
            'apprenants' => $apprenants,
            'search' => $search,
            'currentStatut' => $statut,
            'currentNiveauEtude' => $niveauEtude,
            'statuts' => collect(ApprenantStatut::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray(),
            'niveaux' => collect(NiveauEtude::cases())->mapWithKeys(fn($n) => [$n->value => $n->label()])->toArray(),
            'page_title' => 'Apprenants',
            'active_menu' => 'apprenants',
        ]);
    }

    /**
     * Afficher le formulaire de création d'un apprenant
     */
    public function create(): View
    {
        return view('admin.apprenants.create', [
            'statuts' => collect(ApprenantStatut::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray(),
            'niveaux' => collect(NiveauEtude::cases())->mapWithKeys(fn($n) => [$n->value => $n->label()])->toArray(),
            'formations' => \App\Models\Formation::where('statut', 'planifiee')->orWhere('statut', 'en_cours')->get(),
            'selectedFormationId' => request('formation_id'),
            'page_title' => 'Ajouter un apprenant',
            'active_menu' => 'apprenants',
        ]);
    }

    /**
     * Enregistrer un nouvel apprenant
     */
    public function store(ApprenantRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $apprenant = $this->apprenantService->create($request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "L'apprenant {$apprenant->nom_complet} a été créé avec succès. Matricule : {$apprenant->matricule}",
                    'apprenant' => $apprenant,
                ], 201);
            }

            return redirect()->route('admin.apprenants.create')
                ->with('success', "L'apprenant {$apprenant->nom_complet} a été créé avec succès. Matricule : {$apprenant->matricule}");
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('ApprenantController@store failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la création de l\'apprenant: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la création de l\'apprenant: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'un apprenant
     */
    public function show(Apprenant $apprenant): View
    {
        $apprenant->load(['inscriptions.formation', 'inscriptions.paiements.creator']);
        
        return view('admin.apprenants.show', [
            'apprenant' => $apprenant,
            'page_title' => $apprenant->nom_complet,
            'active_menu' => 'apprenants',
        ]);
    }

    /**
     * Afficher le formulaire de modification d'un apprenant
     */
    public function edit(Apprenant $apprenant): View
    {
        return view('admin.apprenants.edit', [
            'apprenant' => $apprenant,
            'statuts' => collect(ApprenantStatut::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray(),
            'niveaux' => collect(NiveauEtude::cases())->mapWithKeys(fn($n) => [$n->value => $n->label()])->toArray(),
            'formations' => \App\Models\Formation::where('statut', 'planifiee')->orWhere('statut', 'en_cours')->get(),
            'page_title' => 'Modifier : ' . $apprenant->nom_complet,
            'active_menu' => 'apprenants',
        ]);
    }

    /**
     * Mettre à jour un apprenant
     */
    public function update(ApprenantRequest $request, Apprenant $apprenant): RedirectResponse|JsonResponse
    {
        try {
            $this->apprenantService->update($apprenant, $request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "L'apprenant {$apprenant->nom_complet} a été modifié avec succès.",
                    'apprenant' => $apprenant->fresh(),
                ], 200);
            }

            return redirect()->route('admin.apprenants.show', $apprenant)
                ->with('success', "L'apprenant {$apprenant->nom_complet} a été modifié avec succès.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('ApprenantController@update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Supprimer un apprenant (soft delete)
     */
    public function destroy(Apprenant $apprenant): RedirectResponse|JsonResponse
    {
        try {
            $name = $apprenant->nom_complet;
            $this->apprenantService->delete($apprenant);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => "L'apprenant {$name} a été supprimé avec succès."], 200);
            }

            return redirect()->route('admin.apprenants.index')
                ->with('success', "L'apprenant {$name} a été supprimé avec succès.");
        } catch (\Exception $e) {
            \Log::error('ApprenantController@destroy failed', ['error' => $e->getMessage()]);
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
