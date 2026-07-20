<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Inertia\Inertia;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = Projet::with(['campagnesCollecte' => function ($query) {
            $query->where('est_active', true)->latest();
        }])->orderBy('created_at', 'desc')->get();

        $projets->each(function ($projet) {
            $projet->campagne_active = $projet->campagnesCollecte->first();
            unset($projet->campagnesCollecte);
        });

        $categories = $projets
            ->pluck('categorie')
            ->filter()
            ->unique()
            ->values();

        return Inertia::render('Realisations', [
            'projets' => $projets,
            'categories' => $categories,
        ]);
    }
}
