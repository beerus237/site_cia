<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvenementController extends Controller
{
    /**
     * Affiche la liste des événements à venir
     */
    public function index()
    {
        $evenements = Evenement::where('est_actif', true)
                            ->where('date_debut', '>', now())
                            ->orderBy('date_debut', 'asc')
                            ->get();

        return Inertia::render('Evenements', [
            'evenements' => $evenements
        ]);
    }
}
