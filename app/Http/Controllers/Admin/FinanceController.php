<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Depense;
use App\Models\Inscription;
use App\Models\Apprenant;
use App\Models\Formation;
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
        
        $recent_paiements = Paiement::with(['inscription.apprenant', 'inscription.formation'])
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

    public function payments()
    {
        $paiements = Paiement::with(['inscription.apprenant', 'inscription.formation'])->latest()->paginate(20);
        $inscriptions = Inscription::with(['apprenant', 'formation'])
            ->whereNotIn('statut', ['terminee', 'annulee'])
            ->get()
            ->filter(function ($ins) {
                return $ins->montant_total > $ins->montant_paye;
            })
            ->values();
            
        return view('admin.finances.payments', compact('paiements', 'inscriptions'));
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
        $paiement->load(['inscription.apprenant', 'inscription.formation', 'creator']);
        return view('admin.finances.receipt', compact('paiement'));
    }

    public function trainerPayments()
    {
        // Charger les formations qui ont des formateurs et charger avec inscriptions.paiements
        $formations = Formation::with(['formateurs', 'inscriptions.paiements'])
            ->whereHas('formateurs')
            ->get();
            
        // Charger les paiements de commission formateurs
        $payments = Depense::with(['formation', 'trainer'])
            ->where('categorie', 'Rémunération Formateur')
            ->latest()
            ->paginate(20);
            
        // Construire les données JSON préchargées pour le JS
        $formationsData = $formations->map(function($f) {
            $total_collecte = $f->inscriptions->flatMap->paiements->sum('montant');
            $total_contrats = $f->inscriptions->sum('montant_total');
            
            $formateurs = $f->formateurs->map(function($trainer) use ($f, $total_collecte, $total_contrats) {
                $percentage = $trainer->pivot->pourcentage_commission ?? 0;
                
                // Commission sur les contrats inscrits
                $commission_contrat = ($total_contrats * $percentage) / 100;
                
                // Commission sur l'argent réellement collecté
                $commission_acquise = ($total_collecte * $percentage) / 100;
                
                // Déjà payé à ce formateur pour cette formation
                $deja_paye = Depense::where('user_id', $trainer->id)
                    ->where('formation_id', $f->id)
                    ->sum('montant');
                    
                $reste_a_payer = $commission_acquise - $deja_paye;
                
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'email' => $trainer->email,
                    'phone' => $trainer->phone,
                    'percentage' => $percentage,
                    'commission_contrat' => $commission_contrat,
                    'commission_acquise' => $commission_acquise,
                    'deja_paye' => $deja_paye,
                    'reste_a_payer' => max(0, $reste_a_payer)
                ];
            });
            
            return [
                'id' => $f->id,
                'nom' => $f->nom,
                'code' => $f->code,
                'total_collecte' => $total_collecte,
                'total_contrats' => $total_contrats,
                'formateurs' => $formateurs
            ];
        });

        return view('admin.finances.trainer_payments', compact('payments', 'formationsData', 'formations'));
    }

    public function storeTrainerPayment(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'user_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $formation = Formation::with(['formateurs' => function($q) use ($request) {
            $q->where('users.id', $request->user_id);
        }, 'inscriptions.paiements'])->findOrFail($request->formation_id);
        
        $trainer = $formation->formateurs->first();
        if (!$trainer) {
            return redirect()->back()->withErrors(['user_id' => 'Le formateur sélectionné n\'est pas associé à cette formation.']);
        }
        
        $total_collecte = $formation->inscriptions->flatMap->paiements->sum('montant');
        $percentage = $trainer->pivot->pourcentage_commission ?? 0;
        $commission_acquise = ($total_collecte * $percentage) / 100;
        
        $deja_paye = Depense::where('user_id', $request->user_id)
            ->where('formation_id', $request->formation_id)
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
            'titre' => "Commission Formateur : " . $trainer->name . " - " . $formation->nom,
            'categorie' => 'Rémunération Formateur',
            'montant' => $request->montant,
            'date_depense' => $request->date_paiement,
            'beneficiaire' => $trainer->name,
            'description' => "Commission de " . $percentage . "% sur la formation " . $formation->nom . ". Règlement via " . ucfirst($request->mode_paiement) . ($request->reference ? " (Réf: " . $request->reference . ")" : "") . ". Notes: " . $request->notes,
            'formation_id' => $request->formation_id,
            'user_id' => $request->user_id,
            'created_by' => Auth::id()
        ]);
        
        return redirect()->back()->with('success', 'Rémunération du formateur enregistrée avec succès.');
    }
}
