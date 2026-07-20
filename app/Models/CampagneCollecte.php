<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CampagneCollecte extends Model
{
    use HasFactory;

    protected $table = 'campagnes_collecte';

    protected $fillable = [
        'titre', 'slug', 'description', 'types_acceptes',
        'image_url', 'projet_id', 'est_active',
    ];

    protected $casts = [
        'types_acceptes' => 'array',
        'est_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (CampagneCollecte $campagne) {
            if (empty($campagne->slug)) {
                $campagne->slug = static::genererSlugUnique($campagne->titre);
            }
        });
    }

    public static function genererSlugUnique(string $titre): string
    {
        $base = Str::slug($titre);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'campagne_id');
    }

    public function accepte(string $type): bool
    {
        return in_array($type, $this->types_acceptes ?? [], true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
