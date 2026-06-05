<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\Salle;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class GroupeFormationController extends Controller
{
    public function index(Request $request)
    {
        $query = GroupeFormation::with(['formation.categorie', 'formateurPrincipal', 'apprenants'])
            ->withCount('apprenants');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('formation', function ($formationQuery) use ($search) {
                        $formationQuery->where('nom', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->formation_id);
        }

        $groupesFormation = $query->latest()->paginate(15);
        $formations = Formation::orderBy('nom')->get();
        $page_title = 'Groupes de formation';

        return view('admin.groupes-formations.index', compact('groupesFormation', 'formations', 'page_title'));
    }

    public function emploiDuTempsPdf(GroupeFormation $groupesFormation)
    {
        $groupesFormation->load(['formation.categorie', 'formateurPrincipal', 'formateurs']);
        $schedule = $this->parseSchedule($groupesFormation->emploi_du_temps);

        return view('admin.groupes-formations.emploi-du-temps-pdf', [
            'groupe' => $groupesFormation,
            'schedule' => $schedule,
            'plainSchedule' => empty($schedule) ? $groupesFormation->emploi_du_temps : null,
        ]);
    }

    public function create(Request $request)
    {
        $formation = $request->formation_id ? Formation::findOrFail($request->formation_id) : null;
        $formations = Formation::orderBy('nom')->get();
        $formateurs = $this->availableFormateurs();
        $salles = $this->availableSalles($formation?->salle);
        $page_title = 'Nouveau groupe de formation';

        return view('admin.groupes-formations.create', compact('formation', 'formations', 'formateurs', 'salles', 'page_title'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateGroupe($request);
        $formateurIds = Arr::pull($validated, 'formateurs', []);
        $roles = Arr::pull($validated, 'formateur_roles', []);
        $commissions = Arr::pull($validated, 'formateur_commissions', []);
        $commissionTypes = Arr::pull($validated, 'formateur_commission_types', []);
        $commissionAmounts = Arr::pull($validated, 'formateur_commission_amounts', []);

        $validated['created_by'] = Auth::id();

        $groupe = GroupeFormation::create($validated);
        $this->syncFormateurs($groupe, $formateurIds, $roles, $commissions, $commissionTypes, $commissionAmounts);

        return redirect()->route('admin.formations.show', $groupe->formation_id)
            ->with('success', 'Groupe de formation créé avec succès.');
    }

    public function edit(GroupeFormation $groupesFormation)
    {
        $groupesFormation->load('formation', 'formateurs');
        $formateurs = $this->availableFormateurs();
        $formations = Formation::orderBy('nom')->get();
        $salles = $this->availableSalles($groupesFormation->salle);
        $page_title = 'Modifier le groupe';
        $selectedFormateurs = $groupesFormation->formateurs->pluck('id')->toArray();
        $selectedRoles = $groupesFormation->formateurs->pluck('pivot.role', 'id')->toArray();
        $selectedCommissions = $groupesFormation->formateurs->pluck('pivot.taux_commission', 'id')->toArray();
        $selectedCommissionTypes = $groupesFormation->formateurs->pluck('pivot.commission_type', 'id')->toArray();
        $selectedCommissionAmounts = $groupesFormation->formateurs->pluck('pivot.montant_commission', 'id')->toArray();

        return view('admin.groupes-formations.edit', compact(
            'groupesFormation',
            'formations',
            'formateurs',
            'salles',
            'page_title',
            'selectedFormateurs',
            'selectedRoles',
            'selectedCommissions',
            'selectedCommissionTypes',
            'selectedCommissionAmounts'
        ));
    }

    public function update(Request $request, GroupeFormation $groupesFormation)
    {
        $validated = $this->validateGroupe($request, $groupesFormation);
        $formateurIds = Arr::pull($validated, 'formateurs', []);
        $roles = Arr::pull($validated, 'formateur_roles', []);
        $commissions = Arr::pull($validated, 'formateur_commissions', []);
        $commissionTypes = Arr::pull($validated, 'formateur_commission_types', []);
        $commissionAmounts = Arr::pull($validated, 'formateur_commission_amounts', []);

        $groupesFormation->update($validated);
        $this->syncFormateurs($groupesFormation, $formateurIds, $roles, $commissions, $commissionTypes, $commissionAmounts);

        return redirect()->route('admin.formations.show', $groupesFormation->formation_id)
            ->with('success', 'Groupe de formation mis à jour avec succès.');
    }

    public function updateStatus(Request $request, GroupeFormation $groupesFormation)
    {
        $validated = $request->validate([
            'statut' => ['required', 'in:planifiee,en_cours,terminee,suspendue'],
        ]);

        $groupesFormation->update(['statut' => $validated['statut']]);

        return back()->with('success', 'Le statut du groupe a été mis à jour.');
    }

    public function destroy(GroupeFormation $groupesFormation)
    {
        $formationId = $groupesFormation->formation_id;
        $groupesFormation->delete();

        return redirect()->route('admin.formations.show', $formationId)
            ->with('success', 'Groupe de formation archivé avec succès.');
    }

    private function validateGroupe(Request $request, ?GroupeFormation $groupe = null): array
    {
        return $request->validate([
            'formation_id' => ['required', 'exists:formations,id'],
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'unique:groupes_formation,code,' . ($groupe?->id ?? 'NULL')],
            'formateur_principal_id' => ['required', 'exists:users,id'],
            'statut' => ['required', 'in:planifiee,en_cours,terminee,suspendue'],
            'capacite_max' => ['nullable', 'integer', 'min:0'],
            'salle' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'emploi_du_temps' => ['nullable', 'string'],
            'formateurs' => ['nullable', 'array'],
            'formateurs.*' => ['exists:users,id'],
            'formateur_roles' => ['nullable', 'array'],
            'formateur_roles.*' => ['nullable', 'in:principal,assistant,intervenant'],
            'formateur_commission_types' => ['nullable', 'array'],
            'formateur_commission_types.*' => ['nullable', 'in:pourcentage,montant'],
            'formateur_commissions' => ['nullable', 'array'],
            'formateur_commissions.*' => ['nullable', 'integer', 'in:20,30,40,50,60,70,80'],
            'formateur_commission_amounts' => ['nullable', 'array'],
            'formateur_commission_amounts.*' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
        ]);
    }

    private function syncFormateurs(
        GroupeFormation $groupe,
        array $formateurIds,
        array $roles,
        array $commissions,
        array $commissionTypes,
        array $commissionAmounts
    ): void
    {
        $ids = collect($formateurIds)
            ->push($groupe->formateur_principal_id)
            ->filter()
            ->unique();

        $groupe->formateurs()->sync(
            $ids->mapWithKeys(function ($id) use ($groupe, $roles, $commissions, $commissionTypes, $commissionAmounts) {
                $isPrincipal = (int) $id === (int) $groupe->formateur_principal_id;
                $commissionType = $isPrincipal ? 'pourcentage' : ($commissionTypes[$id] ?? 'pourcentage');

                return [
                    (int) $id => [
                        'role' => $isPrincipal ? 'principal' : ($roles[$id] ?? 'intervenant'),
                        'commission_type' => $commissionType,
                        'taux_commission' => $commissionType === 'pourcentage' && isset($commissions[$id]) && $commissions[$id] !== '' ? (int) $commissions[$id] : null,
                        'montant_commission' => $commissionType === 'montant' && isset($commissionAmounts[$id]) && $commissionAmounts[$id] !== '' ? (float) $commissionAmounts[$id] : null,
                        'assigned_at' => now(),
                    ],
                ];
            })->toArray()
        );
    }

    private function availableFormateurs()
    {
        return User::where('role', UserRole::FORMATEUR->value)
            ->orderBy('name')
            ->get();
    }

    private function availableSalles(?string $currentSalle = null)
    {
        return Salle::where('is_active', true)
            ->when($currentSalle, function ($query) use ($currentSalle) {
                $query->orWhere('nom', $currentSalle);
            })
            ->orderBy('nom')
            ->get();
    }

    private function parseSchedule(?string $emploiDuTemps): array
    {
        if (!$emploiDuTemps) {
            return [];
        }

        $decoded = json_decode($emploiDuTemps, true);

        if (!is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->filter(fn($item) => is_array($item))
            ->map(fn($item) => [
                'day' => $item['day'] ?? $item['jour'] ?? '',
                'start' => $item['start'] ?? $item['debut'] ?? '',
                'end' => $item['end'] ?? $item['fin'] ?? '',
                'activity' => $item['activity'] ?? $item['activite'] ?? $item['module'] ?? '',
            ])
            ->values()
            ->all();
    }
}
