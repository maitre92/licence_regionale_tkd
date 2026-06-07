<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupeFormation;
use App\Services\MouvementService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MouvementController extends Controller
{
    public function index(Request $request, MouvementService $mouvementService)
    {
        $filters = $this->resolveFilters($request);
        $data = $mouvementService->build($filters);
        $options = $mouvementService->filterOptions();

        $selectedGroupe = null;
        if ($filters['groupe_formation_id']) {
            $selectedGroupe = GroupeFormation::with('formation')->find($filters['groupe_formation_id']);
        }

        return view('admin.mouvements.index', [
            'page_title' => 'Mouvements / Pilotage',
            'filters' => $filters,
            'summary' => $data['summary'],
            'recapRows' => $data['recap_rows'],
            'charts' => $data['charts'],
            'financial' => $data['financial'],
            'learners' => $data['learners'],
            'pedagogical' => $data['pedagogical'],
            'formations' => $options['formations'],
            'groupes' => $options['groupes'],
            'selectedGroupe' => $selectedGroupe,
            'hasActiveFilter' => $filters['has_active_filter'],
        ]);
    }

    private function resolveFilters(Request $request): array
    {
        $period = $request->get('period', 'month');
        if (!in_array($period, ['today', 'week', 'month', 'year', 'custom'], true)) {
            $period = 'month';
        }

        $today = now();
        $start = $today->copy()->startOfMonth();
        $end = $today->copy()->endOfMonth();

        if ($period === 'today') {
            $start = $today->copy()->startOfDay();
            $end = $today->copy()->endOfDay();
        } elseif ($period === 'week') {
            $start = $today->copy()->startOfWeek();
            $end = $today->copy()->endOfWeek();
        } elseif ($period === 'year') {
            $start = $today->copy()->startOfYear();
            $end = $today->copy()->endOfYear();
        } elseif ($period === 'custom') {
            $start = Carbon::parse($request->get('start_date', $today->copy()->startOfMonth()->toDateString()))->startOfDay();
            $end = Carbon::parse($request->get('end_date', $today->toDateString()))->endOfDay();
        }

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [
            'period' => $period,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'formation_id' => $request->filled('formation_id') ? (int) $request->formation_id : null,
            'groupe_formation_id' => $request->filled('groupe_formation_id') ? (int) $request->groupe_formation_id : null,
            'has_active_filter' => $request->filled('formation_id') || $request->filled('groupe_formation_id'),
        ];
    }
}
