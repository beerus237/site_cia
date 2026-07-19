<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projet extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre', 'description', 'categorie', 'equipe', 'statut', 'lien_demo', 'lien_github', 'image_url', 'date_debut', 'date_fin'
    ];
    protected $dates = ['date_debut', 'date_fin'];

    public function campagnesCollecte(): HasMany
    {
        return $this->hasMany(CampagneCollecte::class, 'projet_id');
    }

    /**
     * La campagne de collecte active à mettre en avant pour ce projet
     * (bouton "Contribuer" sur la page Réalisations).
     */
    public function campagneActive()
    {
        return $this->campagnesCollecte()->where('est_active', true)->latest()->first();
    }
}
