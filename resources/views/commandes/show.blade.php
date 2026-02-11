@extends('layouts.app')

@section('title', 'Commande #' . $commande->id)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('commandes.index') }}">Commandes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Commande #{{ $commande->id }}</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-receipt me-2"></i>Commande #{{ $commande->id }}
        </h1>
    </div>
    <div class="col-md-4 text-md-end">
        <span class="badge statut-{{ $commande->statut }} fs-6 px-3 py-2">
            {{ App\Models\Commande::getStatutLabel($commande->statut) }}
        </span>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @if($commande->peutEtreModifiee())
                        <form action="{{ route('commandes.validate', $commande) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>Valider
                            </button>
                        </form>
                    @endif
                    
                    @if($commande->statut == App\Models\Commande::STATUT_CONFIRMEE)
                        <form action="{{ route('commandes.deliver', $commande) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-truck me-1"></i>Marquer envoyée
                            </button>
                        </form>
                    @endif
                    
                    @if($commande->statut == App\Models\Commande::STATUT_ENVOYEE)
                        <form action="{{ route('commandes.close', $commande) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Marquer livrée
                            </button>
                        </form>
                    @endif
                    
                    @if($commande->statut == App\Models\Commande::STATUT_LIVREE)
                        <form action="{{ route('commandes.cloturer', $commande) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-lock me-1"></i>Clôturer
                            </button>
                        </form>
                    @endif
                    
                    @if(!in_array($commande->statut, [App\Models\Commande::STATUT_LIVREE, App\Models\Commande::STATUT_CLOTUREE, App\Models\Commande::STATUT_ANNULEE]))
                        <form action="{{ route('commandes.cancel', $commande) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                <i class="bi bi-x-lg me-1"></i>Annuler
                            </button>
                        </form>
                    @endif
                    
                    <div class="ms-auto">
                        <a href="{{ route('commandes.print', $commande) }}" class="btn btn-outline-secondary" target="_blank">
                            <i class="bi bi-printer me-1"></i>Imprimer
                        </a>
                        <a href="{{ route('commandes.exportPdf', $commande) }}" class="btn btn-outline-danger">
                            <i class="bi bi-file-pdf me-1"></i>PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations client -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-person me-2"></i>Informations Client
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="ps-0 text-muted">Nom complet</td>
                        <td class="text-end fw-medium">{{ $commande->client->nom_complet }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0 text-muted">Téléphone</td>
                        <td class="text-end fw-medium">{{ $commande->client->telephone }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0 text-muted">Ville</td>
                        <td class="text-end fw-medium">{{ $commande->client->ville }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0 text-muted">Adresse</td>
                        <td class="text-end fw-medium">{{ $commande->client->adresse }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-calculator me-2"></i>Résumé
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="ps-0 text-muted">Total HT</td>
                        <td class="text-end fw-medium">{{ number_format($commande->total_ht, 2) }} DH</td>
                    </tr>
                    <tr>
                        <td class="ps-0 text-muted">TVA (20%)</td>
                        <td class="text-end fw-medium">{{ number_format($commande->tva, 2) }} DH</td>
                    </tr>
                    <tr class="border-top">
                        <td class="ps-0 fw-bold">Total TTC</td>
                        <td class="text-end fw-bold fs-5 text-primary">
                            {{ number_format($commande->total_ttc, 2) }} DH
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Produits de la commande -->
    <div class="col-lg-8 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>Produits
                </h5>
                @if($commande->peutEtreModifiee())
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProduitModal">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter un produit
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Prix unit.</th>
                                <th class="text-center">Qté</th>
                                <th class="text-end">Total</th>
                                @if($commande->peutEtreModifiee())
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commande->produits as $produit)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $produit->designation }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($produit->pivot->prix, 2) }} DH</td>
                                    <td class="text-center">
                                        @if($commande->peutEtreModifiee())
                                            <form action="{{ route('commandes.updateProduit', [$commande, $produit]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm" style="max-width: 100px; margin: 0 auto;">
                                                    <input type="number" name="quantite" 
                                                           value="{{ $produit->pivot->quantite }}" 
                                                           min="1" class="form-control text-center">
                                                    <button type="submit" class="btn btn-outline-primary">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            {{ $produit->pivot->quantite }}
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($produit->pivot->total_ligne, 2) }} DH
                                    </td>
                                    @if($commande->peutEtreModifiee())
                                        <td class="text-center">
                                            <form action="{{ route('commandes.removeProduit', [$commande, $produit]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Supprimer ce produit ?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $commande->peutEtreModifiee() ? 5 : 4 }}" class="text-center py-4">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 text-muted">Aucun produit dans cette commande</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historique -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Historique
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="bi bi-plus-circle me-2 text-success"></i>Création</span>
                        <span class="text-muted">{{ $commande->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                    @if($commande->date_validation)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-check-lg me-2 text-success"></i>Validation</span>
                            <span class="text-muted">{{ $commande->date_validation->format('d/m/Y H:i') }}</span>
                        </li>
                    @endif
                    @if($commande->date_expedition)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-truck me-2 text-primary"></i>Expédition</span>
                            <span class="text-muted">{{ $commande->date_expedition->format('d/m/Y H:i') }}</span>
                        </li>
                    @endif
                    @if($commande->date_livraison)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-box-seam me-2 text-success"></i>Livraison</span>
                            <span class="text-muted">{{ $commande->date_livraison->format('d/m/Y H:i') }}</span>
                        </li>
                    @endif
                    @if($commande->date_annulation)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-x-lg me-2 text-danger"></i>Annulation</span>
                            <span class="text-muted">{{ $commande->date_annulation->format('d/m/Y H:i') }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Produit -->
@if($commande->peutEtreModifiee())
<div class="modal fade" id="addProduitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('commandes.addProduit', $commande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produit</label>
                        <select name="produit_id" class="form-select" required>
                            <option value="">Sélectionner un produit</option>
                            @foreach($produits as $produit)
                                <option value="{{ $produit->id }}" data-prix="{{ $produit->prix }}">
                                    {{ $produit->designation }} - {{ number_format($produit->prix, 2) }} DH
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantité</label>
                        <input type="number" name="quantite" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix unitaire (DH)</label>
                        <input type="number" step="0.01" name="prix" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="produit_id"]').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const prix = selected.getAttribute('data-prix');
        document.querySelector('input[name="prix"]').value = prix;
    });
</script>
@endif
@endsection
