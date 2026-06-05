<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Depense;
use App\Models\Inscription;
use App\Models\Apprenant;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    public function index()
    {
        $total_revenue = Paiement::sum('montant');
        $total_expenses = Depense::sum('montant');
        $balance = $total_revenue - $total_expenses;
        
        $recent_paiements = Paiement::with(['inscription.apprenant', 'inscription.formation', 'inscription.groupeFormation'])
            ->latest()
            ->take(10)
            ->get();
            
        $recent_depenses = Depense::latest()->take(10)->get();
        
        // Data for charts
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M'));
        $revenue_by_month = collect(range(5, 0))->map(fn($i) => 
            Paiement::whereYear('date_paiement', now()->subMonths($i)->year)
                   ->whereMonth('date_paiement', now()->subMonths($i)->month)
                   ->sum('montant')
        );
        $expenses_by_month = collect(range(5, 0))->map(fn($i) => 
            Depense::whereYear('date_depense', now()->subMonths($i)->year)
                   ->whereMonth('date_depense', now()->subMonths($i)->month)
                   ->sum('montant')
        );

        return view('admin.finances.index', compact(
            'total_revenue', 'total_expenses', 'balance', 
            'recent_paiements', 'recent_depenses',
            'months', 'revenue_by_month', 'expenses_by_month'
        ));
    }

    public function payments(Request $request)
    {
        $query = Paiement::with(['inscription.apprenant', 'inscription.formation', 'inscription.groupeFormation']);

        if ($request->filled('groupe_id')) {
            $query->whereHas('inscription', function ($q) use ($request) {
                $q->where('groupe_formation_id', $request->groupe_id);
            });
        }

        if ($request->filled('formation_id')) {
            $query->whereHas('inscription', function ($q) use ($request) {
                $q->where('formation_id', $request->formation_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recu_numero', 'like', "%{$search}%")
                  ->orWhere('mode_paiement', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('inscription.apprenant', function ($q2) use ($search) {
                      $q2->where('prenom', 'like', "%{$search}%")
                         ->orWhere('nom', 'like', "%{$search}%")
                         ->orWhere('matricule', 'like', "%{$search}%");
                  });
            });
        }

        $paiements = $query->latest()->paginate(20)->withQueryString();

        $inscriptions = Inscription::with(['apprenant', 'formation', 'groupeFormation'])
            ->whereNotIn('statut', ['terminee', 'annulee'])
            ->get()
            ->filter(function ($ins) {
                return $ins->montant_total > $ins->montant_paye;
            })
            ->values();

        $groupes = GroupeFormation::with('formation')->orderBy('nom')->get();
        $formations = Formation::orderBy('nom')->get();
            
        return view('admin.finances.payments', compact('paiements', 'inscriptions', 'groupes', 'formations'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $inscription = Inscription::findOrFail($request->inscription_id);
        
        if ($inscription->statut === 'annulee') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['inscription_id' => "Impossible d'enregistrer un paiement pour une inscription annulée."]);
        }

        $reste = $inscription->montant_total - $inscription->montant_paye;
        
        if ($reste <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['montant' => "Cette inscription est déjà entièrement réglée."]);
        }

        if ($request->montant > $reste) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['montant' => "Le montant saisi (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le reste à payer (" . number_format($reste, 0, ',', ' ') . " FCFA)."]);
        }

        DB::transaction(function () use ($request, $inscription) {
            $paiement = Paiement::create([
                'inscription_id' => $request->inscription_id,
                'montant' => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference' => $request->reference,
                'recu_numero' => Paiement::generateRecuNumero(),
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Update inscription paid amount
            $inscription->increment('montant_paye', $request->montant);

            // Automatically transition status from 'en_attente' to 'validee' upon payment
            if ($inscription->statut === 'en_attente') {
                $inscription->update(['statut' => 'validee']);
            }
        });

        return redirect()->back()->with('success', 'Paiement enregistré avec succès.');
    }

    public function expenses()
    {
        $depenses = Depense::latest()->paginate(20);
        $categories = Depense::getCategories();
        return view('admin.finances.expenses', compact('depenses', 'categories'));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'categorie' => ['required', 'string', Rule::in(Depense::getCategories())],
            'montant' => 'required|numeric|min:1',
            'date_depense' => 'required|date|before_or_equal:today',
            'beneficiaire' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Contrôle de caisse : Dépenses espèces ne peuvent pas dépasser le solde de caisse actuel
        if ($request->categorie === 'espèces' || $request->categorie === 'Salaire' || $request->categorie === 'Rémunération Formateur') {
            $total_revenue = Paiement::sum('montant');
            $total_expenses = Depense::sum('montant');
            $balance = $total_revenue - $total_expenses;

            if ($request->montant > $balance) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['montant' => "Le montant de la dépense (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le solde de caisse général disponible (" . number_format($balance, 0, ',', ' ') . " FCFA)."]);
            }
        }

        Depense::create(array_merge($request->all(), [
            'created_by' => Auth::id()
        ]));

        return redirect()->back()->with('success', 'Dépense enregistrée avec succès.');
    }

    public function receipt(Paiement $paiement)
    {
        $paiement->load(['inscription.apprenant', 'inscription.formation', 'inscription.groupeFormation', 'creator']);
        return view('admin.finances.receipt', compact('paiement'));
    }

    public function updatePayment(Request $request, Paiement $paiement)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasPermission('edit_payment')) {
            abort(403);
        }

        $request->validate([
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $inscription = $paiement->inscription;

        if ($inscription->statut === 'annulee') {
            return redirect()->back()
                ->withErrors(['montant' => "Impossible de modifier un paiement pour une inscription annulée."]);
        }

        $other_payments_sum = $inscription->paiements()->where('id', '!=', $paiement->id)->sum('montant');
        $reste = $inscription->montant_total - $other_payments_sum;

        if ($request->montant > $reste) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['montant' => "Le montant saisi (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le reste à payer (" . number_format($reste, 0, ',', ' ') . " FCFA)."]);
        }

        DB::transaction(function () use ($request, $paiement, $inscription) {
            // Ajuster le montant payé de l'inscription
            $diff = $request->montant - $paiement->montant;
            $inscription->increment('montant_paye', $diff);

            // Mettre à jour le paiement
            $paiement->update([
                'montant' => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->back()->with('success', 'Paiement modifié avec succès.');
    }

    public function destroyPayment(Paiement $paiement)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasPermission('delete_payment')) {
            abort(403);
        }

        DB::transaction(function () use ($paiement) {
            $inscription = $paiement->inscription;
            
            // Déduire le montant du paiement de l'inscription
            $inscription->decrement('montant_paye', $paiement->montant);

            // Supprimer le paiement
            $paiement->delete();
        });

        return redirect()->back()->with('success', 'Paiement supprimé avec succès.');
    }

    public function trainerPayments()
    {
        // Charger les groupes qui ont des formateurs et charger avec inscriptions.paiements
        $groupesFormation = GroupeFormation::with(['formation', 'formateurs', 'inscriptions.paiements'])
            ->whereHas('formateurs')
            ->get();
            
        // Charger les paiements de commission formateurs
        $payments = Depense::with(['formation', 'groupeFormation', 'trainer'])
            ->where('categorie', 'Rémunération Formateur')
            ->latest()
            ->paginate(20);
            
        // Construire les données JSON préchargées pour le JS
        $formationsData = $groupesFormation->map(function($groupe) {
            $total_collecte = $groupe->inscriptions->flatMap->paiements->sum('montant');
            $total_contrats = $groupe->inscriptions->sum('montant_total');
            
            $formateurs = $groupe->formateurs->map(function($trainer) use ($groupe, $total_collecte, $total_contrats) {
                $commissionType = $trainer->pivot->commission_type ?? 'pourcentage';
                $percentage = $trainer->pivot->taux_commission ?? 0;
                $fixedAmount = (float) ($trainer->pivot->montant_commission ?? 0);
                
                if ($commissionType === 'montant') {
                    $commission_contrat = $fixedAmount;
                    $commission_acquise = $fixedAmount;
                } else {
                    // Commission sur les contrats inscrits
                    $commission_contrat = ($total_contrats * $percentage) / 100;

                    // Commission sur l'argent réellement collecté
                    $commission_acquise = ($total_collecte * $percentage) / 100;
                }
                
                // Déjà payé à ce formateur pour ce groupe
                $deja_paye = Depense::where('user_id', $trainer->id)
                    ->where('groupe_formation_id', $groupe->id)
                    ->sum('montant');
                    
                $reste_a_payer = $commission_acquise - $deja_paye;
                
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'email' => $trainer->email,
                    'phone' => $trainer->phone,
                    'commission_type' => $commissionType,
                    'percentage' => $percentage,
                    'fixed_amount' => $fixedAmount,
                    'commission_contrat' => $commission_contrat,
                    'commission_acquise' => $commission_acquise,
                    'deja_paye' => $deja_paye,
                    'reste_a_payer' => max(0, $reste_a_payer)
                ];
            });
            
            return [
                'id' => $groupe->id,
                'nom' => $groupe->nom,
                'code' => $groupe->code,
                'formation_nom' => $groupe->formation->nom ?? '',
                'total_collecte' => $total_collecte,
                'total_contrats' => $total_contrats,
                'formateurs' => $formateurs
            ];
        });

        return view('admin.finances.trainer_payments', compact('payments', 'formationsData', 'groupesFormation'));
    }

    public function storeTrainerPayment(Request $request)
    {
        $request->validate([
            'groupe_formation_id' => 'required|exists:groupes_formation,id',
            'user_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $groupeFormation = GroupeFormation::with(['formation', 'formateurs' => function($q) use ($request) {
            $q->where('users.id', $request->user_id);
        }, 'inscriptions.paiements'])->findOrFail($request->groupe_formation_id);
        
        $trainer = $groupeFormation->formateurs->first();
        if (!$trainer) {
            return redirect()->back()->withErrors(['user_id' => 'Le formateur sélectionné n\'est pas associé à ce groupe.']);
        }
        
        $total_collecte = $groupeFormation->inscriptions->flatMap->paiements->sum('montant');
        $commissionType = $trainer->pivot->commission_type ?? 'pourcentage';
        $percentage = $trainer->pivot->taux_commission ?? 0;
        $fixedAmount = (float) ($trainer->pivot->montant_commission ?? 0);
        $commission_acquise = $commissionType === 'montant'
            ? $fixedAmount
            : ($total_collecte * $percentage) / 100;
        
        $deja_paye = Depense::where('user_id', $request->user_id)
            ->where('groupe_formation_id', $request->groupe_formation_id)
            ->sum('montant');
            
        $reste = $commission_acquise - $deja_paye;
        
        if ($reste <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['montant' => "Ce formateur a déjà perçu la totalité de sa commission acquise."]);
        }
        
        if ($request->montant > $reste) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['montant' => "Le montant de commission (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le reste à payer (" . number_format($reste, 0, ',', ' ') . " FCFA)."]);
        }
        
        // Contrôle de caisse
        if ($request->mode_paiement === 'espèces') {
            $total_revenue = Paiement::sum('montant');
            $total_expenses = Depense::sum('montant');
            $balance = $total_revenue - $total_expenses;
            
            if ($request->montant > $balance) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['montant' => "Le montant du versement en espèces (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le solde de caisse disponible (" . number_format($balance, 0, ',', ' ') . " FCFA)."]);
            }
        }
        
        Depense::create([
            'titre' => "Commission Formateur : " . $trainer->name . " - " . $groupeFormation->nom,
            'categorie' => 'Rémunération Formateur',
            'montant' => $request->montant,
            'date_depense' => $request->date_paiement,
            'beneficiaire' => $trainer->name,
            'description' => ($commissionType === 'montant'
                ? "Commission fixe de " . number_format($fixedAmount, 0, ',', ' ') . " FCFA sur le groupe " . $groupeFormation->nom
                : "Commission de " . $percentage . "% sur le groupe " . $groupeFormation->nom)
                . ". Règlement via " . ucfirst($request->mode_paiement) . ($request->reference ? " (Réf: " . $request->reference . ")" : "") . ". Notes: " . $request->notes,
            'formation_id' => $groupeFormation->formation_id,
            'groupe_formation_id' => $groupeFormation->id,
            'user_id' => $request->user_id,
            'created_by' => Auth::id()
        ]);
        
        return redirect()->back()->with('success', 'Rémunération du formateur enregistrée avec succès.');
    }

    public function trainerReceipt(Depense $depense)
    {
        $depense->load(['formation', 'trainer', 'creator']);
        return view('admin.finances.trainer_receipt', compact('depense'));
    }

    public function updateExpense(Request $request, Depense $depense)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasPermission('edit_expense')) {
            abort(403);
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'categorie' => ['required', 'string', Rule::in(Depense::getCategories())],
            'montant' => 'required|numeric|min:1',
            'date_depense' => 'required|date|before_or_equal:today',
            'beneficiaire' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($request->categorie === 'espèces' || $request->categorie === 'Salaire' || $request->categorie === 'Rémunération Formateur') {
            $total_revenue = Paiement::sum('montant');
            $total_expenses = Depense::where('id', '!=', $depense->id)->sum('montant');
            $balance = $total_revenue - $total_expenses;

            if ($request->montant > $balance) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['montant' => "Le montant de la dépense (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le solde de caisse général disponible (" . number_format($balance, 0, ',', ' ') . " FCFA)."]);
            }
        }

        $depense->update($request->all());

        return redirect()->back()->with('success', 'Dépense modifiée avec succès.');
    }

    public function destroyExpense(Depense $depense)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasPermission('delete_expense')) {
            abort(403);
        }

        $depense->delete();

        return redirect()->back()->with('success', 'Dépense supprimée avec succès.');
    }

    public function updateTrainerPayment(Request $request, Depense $depense)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasPermission('edit_trainer_payment')) {
            abort(403);
        }

        $request->validate([
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (floatval($request->montant) !== floatval($depense->montant)) {
            return redirect()->back()
                ->withErrors(['montant' => "Le montant d'un règlement formateur est automatiquement généré et ne peut pas être modifié."]);
        }

        $trainer = User::findOrFail($depense->user_id);
        $formation = Formation::findOrFail($depense->formation_id);

        $pivot = $formation->formateurs()->where('user_id', $trainer->id)->first()?->pivot;
        $percentage = $pivot->pourcentage_commission ?? 0;

        $total_collecte = $formation->inscriptions->flatMap->paiements->sum('montant');
        $commission_acquise = ($total_collecte * $percentage) / 100;

        $deja_paye = Depense::where('user_id', $trainer->id)
            ->where('formation_id', $formation->id)
            ->where('id', '!=', $depense->id)
            ->sum('montant');

        $reste = $commission_acquise - $deja_paye;

        if ($request->montant > $reste) {
            return redirect()->back()
                ->withErrors(['montant' => "Le montant saisi (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le solde restant dû pour ce formateur (" . number_format($reste, 0, ',', ' ') . " FCFA)."]);
        }

        $total_revenue = Paiement::sum('montant');
        $total_expenses = Depense::where('id', '!=', $depense->id)->sum('montant');
        $balance = $total_revenue - $total_expenses;

        if ($request->montant > $balance) {
            return redirect()->back()
                ->withErrors(['montant' => "Le montant du versement (" . number_format($request->montant, 0, ',', ' ') . " FCFA) dépasse le solde de caisse général disponible (" . number_format($balance, 0, ',', ' ') . " FCFA)."]);
        }

        $depense->update([
            'titre' => "Commission Formateur : " . $trainer->name . " - " . $formation->nom,
            'montant' => $request->montant,
            'date_depense' => $request->date_paiement,
            'description' => "Commission de " . $percentage . "% sur la formation " . $formation->nom . ". Règlement via " . ucfirst($request->mode_paiement) . ($request->reference ? " (Réf: " . $request->reference . ")" : "") . ". Notes: " . $request->notes,
        ]);

        return redirect()->back()->with('success', 'Versement formateur modifié avec succès.');
    }

    public function destroyTrainerPayment(Depense $depense)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasPermission('delete_trainer_payment')) {
            abort(403);
        }

        $depense->delete();

        return redirect()->back()->with('success', 'Versement formateur supprimé avec succès.');
    }
}
