<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'date',
        'statut',
        'total_ht',
        'tva',
        'total_ttc',
        'notes',
        'date_validation',
        'date_expedition',
        'date_livraison',
        'date_annulation',
        'date_cloture'
    ];

    protected $casts = [
        'date' => 'datetime',
        'total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'date_validation' => 'datetime',
        'date_expedition' => 'datetime',
        'date_livraison' => 'datetime',
        'date_annulation' => 'datetime',
        'date_cloture' => 'datetime'
    ];

    // Constantes pour les statuts
    const STATUT_BROUILLON = 'brouillon';
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_CONFIRMEE = 'confirmee';
    const STATUT_ENVOYEE = 'envoyee';
    const STATUT_LIVREE = 'livree';
    const STATUT_RETOURNEE = 'retournee';
    const STATUT_ANNULEE = 'annulee';
    const STATUT_CLOTUREE = 'closee';

    /**
     * Relation BelongsTo avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation Many-to-Many avec les produits (table pivot)
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class, 'commande_produit')
            ->withPivot('quantite', 'prix', 'total_ligne')
            ->withTimestamps();
    }

    /**
     * Ajouter un produit à la commande
     */
    public function addProduit(int $produitId, int $quantite, float $prix): void
    {
        $this->produits()->attach($produitId, [
            'quantite' => $quantite,
            'prix' => $prix
        ]);
        $this->recalculate();
    }

    /**
     * Modifier un produit dans la commande
     */
    public function updateProduit(int $produitId, int $quantite, ?float $prix = null): void
    {
        $data = ['quantite' => $quantite];
        if ($prix !== null) {
            $data['prix'] = $prix;
        }
        $this->produits()->updateExistingPivot($produitId, $data);
        $this->recalculate();
    }

    /**
     * Supprimer un produit de la commande
     */
    public function removeProduit(int $produitId): void
    {
        $this->produits()->detach($produitId);
        $this->recalculate();
    }

    /**
     * Synchroniser les produits de la commande
     */
    public function syncProduits(array $produits): void
    {
        // Format: [produit_id => ['quantite' => x, 'prix' => y], ...]
        $this->produits()->sync($produits);
        $this->recalculate();
    }

    /**
     * Calculer le total de la commande
     */
    public function calculateTotal(): array
    {
        $totalHT = $this->produits->sum(function ($produit) {
            return $produit->pivot->quantite * $produit->pivot->prix;
        });

        $tvaRate = 0.20; // TVA 20%
        $tva = $totalHT * $tvaRate;
        $totalTTC = $totalHT + $tva;

        return [
            'ht' => round($totalHT, 2),
            'tva' => round($tva, 2),
            'ttc' => round($totalTTC, 2)
        ];
    }

    /**
     * Recalculer les totaux
     */
    public function recalculate(): void
    {
        $totals = $this->calculateTotal();
        $this->update([
            'total_ht' => $totals['ht'],
            'tva' => $totals['tva'],
            'total_ttc' => $totals['ttc']
        ]);
    }

    /**
     * Valider la commande
     */
    public function valider(): bool
    {
        if (!in_array($this->statut, [self::STATUT_BROUILLON, self::STATUT_EN_ATTENTE])) {
            return false;
        }

        $this->update([
            'statut' => self::STATUT_CONFIRMEE,
            'date_validation' => now()
        ]);
        return true;
    }

    /**
     * Marquer comme envoyée
     */
    public function envoyer(): bool
    {
        if ($this->statut !== self::STATUT_CONFIRMEE) {
            return false;
        }

        $this->update([
            'statut' => self::STATUT_ENVOYEE,
            'date_expedition' => now()
        ]);
        return true;
    }

    /**
     * Marquer comme livrée
     */
    public function livrer(): bool
    {
        if ($this->statut !== self::STATUT_ENVOYEE) {
            return false;
        }

        $this->update([
            'statut' => self::STATUT_LIVREE,
            'date_livraison' => now()
        ]);
        return true;
    }

    /**
     * Annuler la commande
     */
    public function annuler(): bool
    {
        if (in_array($this->statut, [self::STATUT_LIVREE, self::STATUT_CLOTUREE])) {
            return false;
        }

        $this->update([
            'statut' => self::STATUT_ANNULEE,
            'date_annulation' => now()
        ]);
        return true;
    }

    /**
     * Clôturer la commande
     */
    public function clore(): bool
    {
        if ($this->statut !== self::STATUT_LIVREE) {
            return false;
        }

        $this->update([
            'statut' => self::STATUT_CLOTUREE,
            'date_cloture' => now()
        ]);
        return true;
    }

    /**
     * Archiver la commande (soft delete)
     */
    public function archiver(): bool
    {
        if ($this->statut !== self::STATUT_CLOTUREE) {
            return false;
        }

        $this->delete();
        return true;
    }

    /**
     * Vérifier si la commande peut être modifiée
     */
    public function peutEtreModifiee(): bool
    {
        return in_array($this->statut, [self::STATUT_BROUILLON, self::STATUT_EN_ATTENTE]);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeParStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les commandes actives (non archivées)
     */
    public function scopeActives($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope pour la recherche
     */
    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('client', function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('telephone', 'like', "%{$search}%");
        })->orWhere('id', 'like', "%{$search}%");
    }

    /**
     * Labels pour les statuts
     */
    public static function getStatutLabel(string $statut): string
    {
        $labels = [
            self::STATUT_BROUILLON => 'Brouillon',
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_CONFIRMEE => 'Confirmée',
            self::STATUT_ENVOYEE => 'Envoyée',
            self::STATUT_LIVREE => 'Livrée',
            self::STATUT_RETOURNEE => 'Retournée',
            self::STATUT_ANNULEE => 'Annulée',
            self::STATUT_CLOTUREE => 'Clôturée'
        ];

        return $labels[$statut] ?? $statut;
    }

    /**
     * Couleurs pour les statuts (pour les badges)
     */
    public static function getStatutColor(string $statut): string
    {
        $colors = [
            self::STATUT_BROUILLON => 'secondary',
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_CONFIRMEE => 'info',
            self::STATUT_ENVOYEE => 'primary',
            self::STATUT_LIVREE => 'success',
            self::STATUT_RETOURNEE => 'dark',
            self::STATUT_ANNULEE => 'danger',
            self::STATUT_CLOTUREE => 'light'
        ];

        return $colors[$statut] ?? 'secondary';
    }
}
