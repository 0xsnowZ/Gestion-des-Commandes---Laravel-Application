<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $fillable = [
        'designation',
        'description',
        'prix',
        'stock',
        'image',
        'actif'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'actif' => 'boolean'
    ];

    /**
     * Relation Many-to-Many avec les commandes
     */
    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class, 'commande_produit')
            ->withPivot('quantite', 'prix', 'total_ligne')
            ->withTimestamps();
    }

    /**
     * Accesseur pour l'URL de l'image
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder-product.png');
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les produits en stock
     */
    public function scopeEnStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
