<?php

namespace App\Services;

use App\Models\Attestation;
use App\Models\Depense;
use App\Models\Evaluation;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\Presence;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MouvementService
{
    public function build(array $filters): array
    {
        $start = Carbon::parse($filters['start_date'])->startOfDay();
        $end = Carbon::parse($filters['end_date'])->endOfDay();

        $base = [
            'start' => $start,
            'end' => $end,
            'formation_id' => $filters['formation_id'] ?? null,
            'groupe_formation_id' => $filters['groupe_formation_id'] ?? null,
            'has_active_filter' => $filters['has_active_filter'] ?? false,
        ];

        if (!$base['has_active_filter']) {
            return $this->emptyData($base);
        }

        $paymentsQuery = $this->paymentsQuery($base);
        $expensesQuery = $this->expensesQuery($base);
        $inscriptionsQuery = $this->inscriptionsQuery($base);
        $groupsQuery = $this->groupsQuery($base);
        $completedGroupsQuery = $this->completedGroupsQuery($base);
        $presencesQuery = $this->presencesQuery($base);
        $evaluationsQuery = $this->evaluationsQuery($base);
        $notesQuery = $this->notesQuery($base);
        $attestationsQuery = $this->attestationsQuery($base);

        $totalRevenue = (float) (clone $paymentsQuery)->sum('montant');
        $totalExpenses = (float) (clone $expensesQuery)->sum('montant');
        $contractAmount = (float) (clone $inscriptionsQuery)->whereNotIn('statut', ['annulee'])->sum('montant_total');
        $paidOnContracts = (float) (clone $inscriptionsQuery)->whereNotIn('statut', ['annulee'])->sum('montant_paye');
        $attendanceTotal = (clone $presencesQuery)->count();
        $attendancePresent = (clone $presencesQuery)->where('statut', 'present')->count();
        $notesTotal = (clone $notesQuery)->count();
        $notesSuccess = (clone $notesQuery)->where('valeur', '>=', 10)->count();

        return [
            'summary' => [
                'formations_realisees' => (clone $completedGroupsQuery)->count(),
                'groupes_actifs_periode' => (clone $groupsQuery)->count(),
                'apprenants_inscrits' => (clone $inscriptionsQuery)->whereNotIn('statut', ['annulee'])->distinct('apprenant_id')->count('apprenant_id'),
                'apprenants_formes' => $this->trainedLearnersCount($base),
                'recettes' => $totalRevenue,
                'depenses' => $totalExpenses,
                'solde' => $totalRevenue - $totalExpenses,
                'montant_contrats' => $contractAmount,
                'reste_a_payer' => max(0, $contractAmount - $paidOnContracts),
                'taux_recouvrement' => $contractAmount > 0 ? round(($paidOnContracts / $contractAmount) * 100, 1) : 0,
                'taux_presence' => $attendanceTotal > 0 ? round(($attendancePresent / $attendanceTotal) * 100, 1) : 0,
                'moyenne_notes' => round((float) ((clone $notesQuery)->avg('valeur') ?? 0), 2),
                'taux_reussite' => $notesTotal > 0 ? round(($notesSuccess / $notesTotal) * 100, 1) : 0,
                'evaluations' => (clone $evaluationsQuery)->where('type', 'evaluation')->count(),
                'examens' => (clone $evaluationsQuery)->where('type', 'examen')->count(),
                'attestations' => (clone $attestationsQuery)->count(),
            ],
            'recap_rows' => $this->recapRows($base),
            'charts' => [
                'financial_timeline' => $this->financialTimeline($base),
                'expenses_by_category' => $this->expensesByCategory($base),
                'pedagogical_mix' => [
                    'Présents' => $attendancePresent,
                    'Absents' => (clone $presencesQuery)->where('statut', 'absent')->count(),
                    'Retards' => (clone $presencesQuery)->where('statut', 'retard')->count(),
                    'Justifiés' => (clone $presencesQuery)->where('statut', 'justifie')->count(),
                ],
            ],
            'financial' => [
                'recent_payments' => (clone $paymentsQuery)
                    ->with(['inscription.apprenant', 'inscription.formation', 'inscription.groupeFormation'])
                    ->orderByDesc('date_paiement')
                    ->take(8)
                    ->get(),
                'recent_expenses' => (clone $expensesQuery)
                    ->with(['formation', 'groupeFormation', 'trainer'])
                    ->orderByDesc('date_depense')
                    ->take(8)
                    ->get(),
                'expenses_by_category' => $this->expensesByCategory($base),
            ],
            'learners' => [
                'recent_inscriptions' => (clone $inscriptionsQuery)
                    ->with(['apprenant', 'formation', 'groupeFormation'])
                    ->orderByDesc('date_inscription')
                    ->take(10)
                    ->get(),
                'by_status' => (clone $inscriptionsQuery)
                    ->select('statut', DB::raw('COUNT(*) as total'))
                    ->groupBy('statut')
                    ->pluck('total', 'statut'),
            ],
            'pedagogical' => [
                'recent_evaluations' => (clone $evaluationsQuery)
                    ->with(['formation', 'groupeFormation'])
                    ->orderByDesc('date_evaluation')
                    ->take(8)
                    ->get(),
                'recent_attestations' => (clone $attestationsQuery)
                    ->with(['apprenant', 'formation', 'groupeFormation'])
                    ->orderByDesc('date_emission')
                    ->take(8)
                    ->get(),
                'presence_by_status' => $this->presenceByStatus($base),
            ],
        ];
    }

    private function emptyData(array $base): array
    {
        return [
            'summary' => [
                'formations_realisees' => 0,
                'groupes_actifs_periode' => 0,
                'apprenants_inscrits' => 0,
                'apprenants_formes' => 0,
                'recettes' => 0,
                'depenses' => 0,
                'solde' => 0,
                'montant_contrats' => 0,
                'reste_a_payer' => 0,
                'taux_recouvrement' => 0,
                'taux_presence' => 0,
                'moyenne_notes' => 0,
                'taux_reussite' => 0,
                'evaluations' => 0,
                'examens' => 0,
                'attestations' => 0,
            ],
            'recap_rows' => collect(),
            'charts' => [
                'financial_timeline' => ['labels' => [], 'revenues' => [], 'expenses' => []],
                'expenses_by_category' => collect(),
                'pedagogical_mix' => ['Présents' => 0, 'Absents' => 0, 'Retards' => 0, 'Justifiés' => 0],
            ],
            'financial' => [
                'recent_payments' => collect(),
                'recent_expenses' => collect(),
                'expenses_by_category' => collect(),
            ],
            'learners' => [
                'recent_inscriptions' => collect(),
                'by_status' => collect(),
            ],
            'pedagogical' => [
                'recent_evaluations' => collect(),
                'recent_attestations' => collect(),
                'presence_by_status' => collect(),
            ],
        ];
    }

    public function filterOptions(): array
    {
        return [
            'formations' => Formation::orderBy('nom')->get(['id', 'nom', 'code']),
            'groupes' => GroupeFormation::with('formation')->orderBy('nom')->get(['id', 'formation_id', 'nom', 'code']),
        ];
    }

    private function paymentsQuery(array $base): Builder
    {
        return Paiement::query()
            ->whereBetween('date_paiement', [$base['start']->toDateString(), $base['end']->toDateString()])
            ->whereHas('inscription', fn(Builder $query) => $this->applyOperationalFilters($query, $base));
    }

    private function expensesQuery(array $base): Builder
    {
        return Depense::query()
            ->whereBetween('date_depense', [$base['start']->toDateString(), $base['end']->toDateString()])
            ->when($base['formation_id'], fn(Builder $query, $id) => $query->where('formation_id', $id))
            ->when($base['groupe_formation_id'], fn(Builder $query, $id) => $query->where('groupe_formation_id', $id));
    }

    private function inscriptionsQuery(array $base): Builder
    {
        $query = Inscription::query()
            ->whereBetween('date_inscription', [$base['start']->toDateString(), $base['end']->toDateString()]);

        $this->applyOperationalFilters($query, $base);

        return $query;
    }

    private function groupsQuery(array $base): Builder
    {
        $query = GroupeFormation::query()
            ->where(function (Builder $query) use ($base) {
                $query->whereBetween('date_debut', [$base['start']->toDateString(), $base['end']->toDateString()])
                    ->orWhereBetween('date_fin', [$base['start']->toDateString(), $base['end']->toDateString()])
                    ->orWhere(function (Builder $overlap) use ($base) {
                        $overlap->whereDate('date_debut', '<=', $base['start']->toDateString())
                            ->whereDate('date_fin', '>=', $base['end']->toDateString());
                    });
            });

        $this->applyGroupFilters($query, $base);

        return $query;
    }

    private function completedGroupsQuery(array $base): Builder
    {
        $query = GroupeFormation::query()
            ->where('statut', 'terminee')
            ->whereBetween('date_fin', [$base['start']->toDateString(), $base['end']->toDateString()]);

        $this->applyGroupFilters($query, $base);

        return $query;
    }

    private function presencesQuery(array $base): Builder
    {
        $query = Presence::query()
            ->whereBetween('date', [$base['start']->toDateString(), $base['end']->toDateString()]);

        $this->applyOperationalFilters($query, $base);

        return $query;
    }

    private function evaluationsQuery(array $base): Builder
    {
        $query = Evaluation::query()
            ->whereBetween('date_evaluation', [$base['start'], $base['end']]);

        $this->applyOperationalFilters($query, $base);

        return $query;
    }

    private function notesQuery(array $base): Builder
    {
        return Note::query()
            ->whereHas('evaluation', function (Builder $query) use ($base) {
                $query->whereBetween('date_evaluation', [$base['start'], $base['end']]);
                $this->applyOperationalFilters($query, $base);
            })
            ->when($base['groupe_formation_id'], fn(Builder $query, $id) => $query->where('groupe_formation_id', $id));
    }

    private function attestationsQuery(array $base): Builder
    {
        $query = Attestation::query()
            ->whereBetween('date_emission', [$base['start']->toDateString(), $base['end']->toDateString()]);

        $this->applyOperationalFilters($query, $base);

        return $query;
    }

    private function applyOperationalFilters(Builder $query, array $base): void
    {
        $query
            ->when($base['formation_id'], fn(Builder $q, $id) => $q->where('formation_id', $id))
            ->when($base['groupe_formation_id'], fn(Builder $q, $id) => $q->where('groupe_formation_id', $id));
    }

    private function applyGroupFilters(Builder $query, array $base): void
    {
        $query
            ->when($base['formation_id'], fn(Builder $q, $id) => $q->where('formation_id', $id))
            ->when($base['groupe_formation_id'], fn(Builder $q, $id) => $q->where('id', $id));
    }

    private function trainedLearnersCount(array $base): int
    {
        $groupIds = $this->completedGroupsQuery($base)->pluck('id');

        if ($groupIds->isEmpty()) {
            return 0;
        }

        return Inscription::whereIn('groupe_formation_id', $groupIds)
            ->whereNotIn('statut', ['annulee'])
            ->distinct('apprenant_id')
            ->count('apprenant_id');
    }

    private function recapRows(array $base): Collection
    {
        return $this->groupsQuery($base)
            ->with(['formation', 'formateurPrincipal'])
            ->orderBy('date_debut')
            ->orderBy('nom')
            ->get()
            ->map(function (GroupeFormation $groupe) use ($base) {
                $scopedBase = array_merge($base, [
                    'formation_id' => $groupe->formation_id,
                    'groupe_formation_id' => $groupe->id,
                ]);

                $inscriptions = Inscription::query()
                    ->where('groupe_formation_id', $groupe->id)
                    ->whereNotIn('statut', ['annulee']);

                $periodInscriptions = (clone $inscriptions)
                    ->whereBetween('date_inscription', [$base['start']->toDateString(), $base['end']->toDateString()]);

                $contractAmount = (float) (clone $inscriptions)->sum('montant_total');
                $paidAmount = (float) (clone $inscriptions)->sum('montant_paye');
                $revenue = (float) $this->paymentsQuery($scopedBase)->sum('montant');
                $expenses = (float) $this->expensesQuery($scopedBase)->sum('montant');
                $trainerRemuneration = (float) $this->expensesQuery($scopedBase)
                    ->where('categorie', 'Rémunération Formateur')
                    ->sum('montant');
                $otherExpenses = max(0, $expenses - $trainerRemuneration);
                $presenceTotal = $this->presencesQuery($scopedBase)->count();
                $presencePresent = $this->presencesQuery($scopedBase)->where('statut', 'present')->count();
                $notes = $this->notesQuery($scopedBase);
                $notesTotal = (clone $notes)->count();
                $notesSuccess = (clone $notes)->where('valeur', '>=', 10)->count();

                return [
                    'formation' => $groupe->formation?->nom ?? 'Formation supprimée',
                    'groupe' => $groupe->nom,
                    'code' => $groupe->code,
                    'formateur' => $groupe->formateurPrincipal?->name,
                    'statut' => $groupe->statut_label,
                    'date_debut' => $groupe->date_debut,
                    'date_fin' => $groupe->date_fin,
                    'inscrits_total' => (clone $inscriptions)->distinct('apprenant_id')->count('apprenant_id'),
                    'inscrits_periode' => (clone $periodInscriptions)->distinct('apprenant_id')->count('apprenant_id'),
                    'formes' => $groupe->statut === 'terminee' && $groupe->date_fin?->betweenIncluded($base['start'], $base['end'])
                        ? (clone $inscriptions)->distinct('apprenant_id')->count('apprenant_id')
                        : 0,
                    'recettes' => $revenue,
                    'depenses' => $expenses,
                    'remuneration_formateurs' => $trainerRemuneration,
                    'autres_depenses' => $otherExpenses,
                    'solde' => $revenue - $expenses,
                    'montant_contrats' => $contractAmount,
                    'reste_a_payer' => max(0, $contractAmount - $paidAmount),
                    'taux_recouvrement' => $contractAmount > 0 ? round(($paidAmount / $contractAmount) * 100, 1) : 0,
                    'taux_presence' => $presenceTotal > 0 ? round(($presencePresent / $presenceTotal) * 100, 1) : 0,
                    'moyenne_notes' => round((float) ((clone $notes)->avg('valeur') ?? 0), 2),
                    'taux_reussite' => $notesTotal > 0 ? round(($notesSuccess / $notesTotal) * 100, 1) : 0,
                    'evaluations' => $this->evaluationsQuery($scopedBase)->where('type', 'evaluation')->count(),
                    'examens' => $this->evaluationsQuery($scopedBase)->where('type', 'examen')->count(),
                    'attestations' => $this->attestationsQuery($scopedBase)->count(),
                ];
            });
    }

    private function financialTimeline(array $base): array
    {
        $days = $base['start']->diffInDays($base['end']) + 1;
        $monthly = $days > 62;
        $labels = [];
        $revenues = [];
        $expenses = [];

        if ($monthly) {
            $cursor = $base['start']->copy()->startOfMonth();
            while ($cursor <= $base['end']) {
                $monthStart = $cursor->copy()->startOfMonth();
                $monthEnd = $cursor->copy()->endOfMonth();
                if ($monthStart->lessThan($base['start'])) {
                    $monthStart = $base['start']->copy();
                }
                if ($monthEnd->greaterThan($base['end'])) {
                    $monthEnd = $base['end']->copy();
                }

                $labels[] = $cursor->format('M Y');
                $revenues[] = (float) $this->paymentsQuery(array_merge($base, ['start' => $monthStart, 'end' => $monthEnd]))->sum('montant');
                $expenses[] = (float) $this->expensesQuery(array_merge($base, ['start' => $monthStart, 'end' => $monthEnd]))->sum('montant');
                $cursor->addMonth();
            }
        } else {
            foreach (CarbonPeriod::create($base['start']->toDateString(), $base['end']->toDateString()) as $date) {
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();
                $labels[] = $date->format('d/m');
                $revenues[] = (float) $this->paymentsQuery(array_merge($base, ['start' => $dayStart, 'end' => $dayEnd]))->sum('montant');
                $expenses[] = (float) $this->expensesQuery(array_merge($base, ['start' => $dayStart, 'end' => $dayEnd]))->sum('montant');
            }
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    private function expensesByCategory(array $base): Collection
    {
        return $this->expensesQuery($base)
            ->select('categorie', DB::raw('SUM(montant) as total'))
            ->groupBy('categorie')
            ->orderByDesc('total')
            ->pluck('total', 'categorie');
    }

    private function presenceByStatus(array $base): Collection
    {
        return $this->presencesQuery($base)
            ->select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');
    }
}
