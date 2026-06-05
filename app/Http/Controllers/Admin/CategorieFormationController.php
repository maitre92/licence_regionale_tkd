<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorieFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategorieFormationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:voir_categories_formations,ajouter_categorie_formation,modifier_categorie_formation,supprimer_categorie_formation,gerer_categories_formations,voir_formations')->only('index');
        $this->middleware('permission:ajouter_categorie_formation,gerer_categories_formations')->only('store');
        $this->middleware('permission:modifier_categorie_formation,gerer_categories_formations')->only('update');
        $this->middleware('permission:supprimer_categorie_formation,gerer_categories_formations')->only('destroy');
    }

    public function index()
    {
        $categories = CategorieFormation::withCount('formations')->orderBy('nom')->get();
        return view('admin.categories_formations.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|unique:categorie_formations,nom',
            'description' => 'nullable|string',
        ]);

        CategorieFormation::create([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, CategorieFormation $categorieFormation)
    {
        $request->validate([
            'nom' => 'required|unique:categorie_formations,nom,' . $categorieFormation->id,
            'description' => 'nullable|string',
        ]);

        $categorieFormation->update([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(CategorieFormation $categorieFormation)
    {
        if ($categorieFormation->formations()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette catégorie car elle contient des formations.');
        }

        $categorieFormation->delete();
        return redirect()->back()->with('success', 'Catégorie supprimée avec succès.');
    }
}
