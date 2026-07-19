<?php

namespace App\Http\Controllers;

use App\Models\CampagneCollecte;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    /**
     * Liste des campagnes de collecte actives
     */
    public function create()
    {
        $campagnes = CampagneCollecte::where('est_active', true)
            ->withCount('contributions')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('CollecteDonnees', [
            'campagnes' => $campagnes,
        ]);
    }

    /**
     * Détail d'une campagne : ne montre que les formulaires des types acceptés
     */
    public function show(CampagneCollecte $campagne)
    {
        abort_unless($campagne->est_active, 404);

        return Inertia::render('CollecteCampagne', [
            'campagne' => $campagne,
        ]);
    }

    /**
     * Stocke une nouvelle contribution, rattachée à une campagne
     */
    public function store(Request $request)
    {
        $campagne = CampagneCollecte::where('est_active', true)->findOrFail($request->input('campagne_id'));

        $rules = array_merge([
            'campagne_id' => 'required|exists:campagnes_collecte,id',
            'type' => 'required|in:image,texte,dataset',
            'titre' => 'required|string|max:255',
        ], $this->getValidationRules($request->input('type')));

        $validatedData = $request->validate($rules);

        // Le type soumis doit faire partie des types acceptés par cette campagne
        if (!$campagne->accepte($validatedData['type'])) {
            abort(422, "Ce type de contribution n'est pas accepté par cette campagne.");
        }

        $fileData = $this->handleFileUpload($request, $validatedData['type']);

        $contributionData = [
            'user_id' => Auth::check() ? Auth::id() : null,
            'campagne_id' => $campagne->id,
            'type' => $validatedData['type'],
            'titre' => $validatedData['titre'],
            'statut' => 'en_attente',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ];

        $typeSpecificData = match ($validatedData['type']) {
            'image' => [
                'nature' => $validatedData['nature'],
                'description' => $validatedData['description'],
            ],
            'texte' => [
                'thematique' => $validatedData['thematique'],
                'langue' => $validatedData['langue'],
                'contenu_texte' => $validatedData['contenu_texte'],
            ],
            'dataset' => [
                'domaine' => $validatedData['domaine'],
                'description' => $validatedData['description'],
            ],
            default => [],
        };

        Contribution::create(array_merge($contributionData, $typeSpecificData, $fileData));

        return redirect()->route('collecte-donnees.show', $campagne->slug)
            ->with('success', 'Merci pour votre contribution ! Elle sera examinée par notre équipe.');
    }

    /**
     * Règles de validation spécifiques par type
     */
    private function getValidationRules($type)
    {
        $baseRules = [
            'type' => 'required|in:image,texte,dataset',
            'titre' => 'required|string|max:255',
        ];

        $typeRules = match($type) {
            'image' => [
                'nature' => 'required|in:portrait,medical,agriculture,elevage,culture,urbain,nature',
                'description' => 'required|string|min:10|max:1000',
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:10240',
            ],
            'texte' => [
                'thematique' => 'required|in:contes,traditions,histoire,savoir,quotidien,autre',
                'langue' => 'required|in:français,anglais,ewondo,dual,bassa,autre',
                'contenu_texte' => 'required|string|min:1|max:1000000000',
            ],
            'dataset' => [
                'domaine' => 'required|in:sante,agriculture,elevage,environnement,education,economie,transport,autre',
                'description' => 'required|string|min:1|max:2000',
                'fichier_dataset' => 'required|file|mimes:csv,txt,xlsx,xls,json|max:51200',
            ],
            default => []
        };

        return array_merge($baseRules, $typeRules);
    }

    /**
     * Gestion de l'upload des fichiers
     */
    private function handleFileUpload(Request $request, $type)
    {
        $fileData = [];

        try {
            switch ($type) {
                case 'image':
                    if ($request->hasFile('images')) {
                        $imagePaths = [];
                        foreach ($request->file('images') as $image) {
                            $path = $image->store('contributions/images', 'public');
                            $imagePaths[] = $path;
                        }
                        // Le modèle Contribution caste "chemin_fichier" en 'array',
                        // qui se charge lui-même de l'encodage JSON : on assigne donc
                        // le tableau PHP brut, sans l'encoder manuellement ici.
                        $fileData['chemin_fichier'] = $imagePaths;
                        $fileData['nom_fichier'] = count($imagePaths) . ' image(s)';
                    }
                    break;

                case 'dataset':
                    if ($request->hasFile('fichier_dataset')) {
                        $file = $request->file('fichier_dataset');
                        $path = $file->store('contributions/datasets', 'public');
                        $fileData['chemin_fichier'] = $path;
                        $fileData['nom_fichier'] = $file->getClientOriginalName();
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Erreur upload fichier: ' . $e->getMessage());
            throw new \Exception('Erreur lors de l\'upload des fichiers: ' . $e->getMessage());
        }

        return $fileData;
    }

    /**
     * Affiche les statistiques des contributions
     */
    public function statistiques()
    {
        $stats = [
            'total' => Contribution::count(),
            'par_type' => Contribution::groupBy('type')
                ->selectRaw('type, count(*) as total')
                ->get(),
            'par_statut' => Contribution::groupBy('statut')
                ->selectRaw('statut, count(*) as total')
                ->get(),
        ];

        return Inertia::render('ResultatsAnalyse', [
            'statistiques' => $stats
        ]);
    }
}
