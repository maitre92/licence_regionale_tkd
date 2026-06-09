<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenceHolder;

/**
 * Contrôleur pour le tableau de bord administrateur
 */
class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord
     */
    public function index()
    {
        $stats = [
            'total_cards' => LicenceHolder::count(),
            'generated_cards' => LicenceHolder::whereNotNull('licence_number')->count(),
            'men' => LicenceHolder::where('gender', 'M')->count(),
            'women' => LicenceHolder::where('gender', 'F')->count(),
        ];

        $recentCards = LicenceHolder::latest()->take(8)->get();

        return view('admin.dashboard.index', array_merge($stats, [
            'page_title' => 'Tableau de bord',
            'recentCards' => $recentCards,
        ]));
    }

    /**
     * Obtenir les statistiques (via AJAX si besoin)
     */
    public function getStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_cards' => LicenceHolder::count(),
                'generated_cards' => LicenceHolder::whereNotNull('licence_number')->count(),
                'men' => LicenceHolder::where('gender', 'M')->count(),
                'women' => LicenceHolder::where('gender', 'F')->count(),
            ]
        ]);
    }
}
