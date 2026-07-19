<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Models\CampagneCollecte;
use App\Models\Projet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampagneCollecteController extends Controller
{
    use HandlesImageUpload;

    public function index()
    {
        $campagnes = CampagneCollecte::with('projet')
            ->withCount('contributions')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Campagnes/Index', [
            'campagnes' => $campagnes,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Campagnes/Create', [
            'projets' => Projet::orderBy('titre')->get(['id', 'titre']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['image_url'] = $this->resolveImageUrl($request, 'campagnes');

        CampagneCollecte::create($validated);

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne de collecte créée avec succès');
    }

    public function edit(CampagneCollecte $campagne)
    {
        return Inertia::render('Admin/Campagnes/Edit', [
            'campagne' => $campagne,
            'projets' => Projet::orderBy('titre')->get(['id', 'titre']),
        ]);
    }

    public function update(Request $request, CampagneCollecte $campagne)
    {
        $validated = $this->validated($request, $campagne);
        $validated['image_url'] = $this->resolveImageUrl($request, 'campagnes', $campagne->image_url);

        $campagne->update($validated);

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne mise à jour avec succès');
    }

    public function destroy(CampagneCollecte $campagne)
    {
        $this->deleteLocalImageIfOwned($campagne->image_url);
        $campagne->delete();

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne supprimée avec succès');
    }

    private function validated(Request $request, ?CampagneCollecte $campagne = null): array
    {
        return $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'types_acceptes' => 'required|array|min:1',
            'types_acceptes.*' => 'in:image,texte,dataset',
            'image' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
            'projet_id' => 'nullable|exists:projets,id',
            'est_active' => 'boolean',
        ]);
    }
}
