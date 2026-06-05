<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\CategorieFormation;
use App\Models\GroupeFormation;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormationController extends Controller
{
    public function index(Request $request)
    {
        $query = Formation::with(['categorie', 'formateurs', 'groupes.formateurPrincipal'])->withCount('groupes');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('categorie_id') && $request->categorie_id != '') {
            $query->where('categorie_formation_id', $request->categorie_id);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $formations = $query->latest()->paginate(10);
        $categories = CategorieFormation::where('is_active', true)->orderBy('nom')->get();

        return view('admin.formations.index', compact('formations', 'categories'));
    }

    public function create()
    {
        $categories = CategorieFormation::where('is_active', true)->orderBy('nom')->get();

        return view('admin.formations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:formations,code',
            'categorie_formation_id' => 'required|exists:categorie_formations,id',
            'type' => 'required|in:Présentiel,En ligne,Hybride',
            'duree_heures' => 'nullable|integer|min:0',
            'cout' => 'nullable|numeric|min:0',
            'frais_inscription' => 'nullable|numeric|min:0',
            'capacite_max' => 'nullable|integer|min:0',
            'niveau' => 'nullable|string|max:100',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'formateurs' => 'nullable|array',
            'formateurs.*' => 'exists:users,id',
            'formateur_commissions' => 'nullable|array',
            'formateur_commissions.*' => 'nullable|integer|in:20,30,40,50,60,70',
        ]);

        $formateurIds = Arr::pull($validated, 'formateurs', []);
        $pourcentages = Arr::pull($validated, 'formateur_commissions', []);
        $this->validateFormateurPercentages($formateurIds, $pourcentages);

        $validated['code'] = $validated['code'] ?? $this->generateFormationCode($validated['nom']);
        $validated['created_by'] = Auth::id();
        $validated['statut'] = 'planifiee';

        $formation = Formation::create($validated);

        $formation->formateurs()->sync($this->buildFormateurSyncData($formateurIds, $pourcentages));
        $this->syncDefaultGroupeFromFormation($formation, $formateurIds, $pourcentages);

        return redirect()->route('admin.formations.index')->with('success', 'Formation créée avec succès.');
    }

    public function show(Formation $formation)
    {
        $formation->load(['categorie', 'formateurs', 'creator', 'groupes.formateurPrincipal', 'groupes.formateurs']);
        return view('admin.formations.show', compact('formation'));
    }

    public function edit(Formation $formation)
    {
        $categories = CategorieFormation::where('is_active', true)->orderBy('nom')->get();
        $formateurs = User::where('role', '!=', UserRole::SUPERADMIN->value)
            ->orderBy('name')
            ->get();
        $selectedFormateurs = $formation->formateurs->pluck('id')->toArray();
        $selectedPourcentages = $formation->formateurs
            ->pluck('pivot.pourcentage_commission', 'id')
            ->filter()
            ->toArray();
        return view('admin.formations.edit', compact('formation', 'categories', 'formateurs', 'selectedFormateurs', 'selectedPourcentages'));
    }

    public function update(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:formations,code,' . $formation->id,
            'categorie_formation_id' => 'required|exists:categorie_formations,id',
            'type' => 'required|in:Présentiel,En ligne,Hybride',
            'duree_heures' => 'nullable|integer|min:0',
            'cout' => 'nullable|numeric|min:0',
            'frais_inscription' => 'nullable|numeric|min:0',
            'capacite_max' => 'nullable|integer|min:0',
            'niveau' => 'nullable|string|max:100',
            'salle' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'formateurs' => 'nullable|array',
            'formateurs.*' => 'exists:users,id',
            'formateur_commissions' => 'nullable|array',
            'formateur_commissions.*' => 'nullable|integer|in:20,30,40,50,60,70',
        ]);

        $formateurIds = Arr::pull($validated, 'formateurs', []);
        $pourcentages = Arr::pull($validated, 'formateur_commissions', []);
        $this->validateFormateurPercentages($formateurIds, $pourcentages);

        $formation->update($validated);

        $formation->formateurs()->sync($this->buildFormateurSyncData($formateurIds, $pourcentages));
        $this->syncDefaultGroupeFromFormation($formation, $formateurIds, $pourcentages);

        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();
        return redirect()->route('admin.formations.index')->with('success', 'Formation supprimée (archivée) avec succès.');
    }

    public function byFormateur(User $formateur)
    {
        $formations = $formateur->formations()->with('categorie')->latest()->paginate(10);
        return view('admin.formations.index', compact('formations', 'formateur'));
    }

    private function validateFormateurPercentages(array $formateurIds, array $pourcentages): void
    {
        $ids = array_map('intval', $formateurIds);
        $allowed = [20, 30, 40, 50, 60, 70];

        $formateurs = User::whereIn('id', $ids)
            ->where('role', UserRole::FORMATEUR->value)
            ->get(['id', 'name']);

        $errors = [];

        foreach ($formateurs as $formateur) {
            $percentage = $pourcentages[$formateur->id] ?? null;

            if (!$percentage || !in_array((int) $percentage, $allowed, true)) {
                $errors["formateur_commissions.{$formateur->id}"] = "Veuillez sélectionner le pourcentage de commission pour {$formateur->name}.";
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function buildFormateurSyncData(array $formateurIds, array $pourcentages): array
    {
        return collect($formateurIds)
            ->filter()
            ->unique()
            ->mapWithKeys(function ($id) use ($pourcentages) {
                return [
                    (int) $id => [
                        'role' => 'formateur',
                        'pourcentage_commission' => isset($pourcentages[$id]) ? (int) $pourcentages[$id] : null,
                        'assigned_at' => now(),
                    ],
                ];
            })
            ->toArray();
    }

    private function syncDefaultGroupeFromFormation(Formation $formation, array $formateurIds, array $pourcentages): void
    {
        $principalId = collect($formateurIds)->filter()->first();

        $groupe = GroupeFormation::firstOrCreate(
            ['formation_id' => $formation->id, 'code' => $formation->code . '-G1'],
            [
                'nom' => $formation->nom . ' G1',
                'created_by' => $formation->created_by,
            ]
        );

        $groupe->update([
            'nom' => $groupe->nom ?: $formation->nom . ' G1',
            'formateur_principal_id' => $principalId ?: $groupe->formateur_principal_id,
            'capacite_max' => $formation->capacite_max,
            'date_debut' => $formation->date_debut,
            'date_fin' => $formation->date_fin,
        ]);

        $groupe->formateurs()->sync(
            collect($formateurIds)
                ->filter()
                ->unique()
                ->mapWithKeys(function ($id) use ($principalId, $pourcentages) {
                    return [
                        (int) $id => [
                            'role' => ((int) $id === (int) $principalId) ? 'principal' : 'intervenant',
                            'taux_commission' => isset($pourcentages[$id]) ? (int) $pourcentages[$id] : null,
                            'assigned_at' => now(),
                        ],
                    ];
                })
                ->toArray()
        );
    }

    private function generateFormationCode(string $name): string
    {
        $base = Str::of($name)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '-')
            ->trim('-')
            ->limit(18, '')
            ->value();

        $base = $base ?: 'FORMATION';
        $counter = 1;

        do {
            $code = sprintf('%s-%03d', $base, $counter);
            $counter++;
        } while (Formation::withTrashed()->where('code', $code)->exists());

        return $code;
    }
}
