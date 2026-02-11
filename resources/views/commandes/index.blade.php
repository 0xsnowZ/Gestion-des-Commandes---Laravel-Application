@extends('layouts.app')

@section('title', 'Gestion des Commandes')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-receipt me-2"></i>Gestion des Commandes
        </h1>
        <p class="text-muted">Gérez toutes vos commandes et leur statut.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('commandes.create') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-lg me-2"></i>Nouvelle Commande
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('commandes.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Client</label>
                <input type="text" name="client" class="form-control" 
                       value="{{ request('client') }}" placeholder="Nom ou prénom">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date début</label>
                <input type="date" name="date_debut" class="form-control" 
                       value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date fin</label>
                <input type="date" name="date_fin" class="form-control" 
                       value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous</option>
                    @foreach($statuts as $key => $label)
                        <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Montant</label>
                <div class="input-group">
                    <input type="number" name="montant_min" class="form-control" 
                           value="{{ request('montant_min') }}" placeholder="Min">
                    <input type="number" name="montant_max" class="form-control" 
                           value="{{ request('montant_max') }}" placeholder="Max">
                </div>
            </div>
            <div class="col-12 text-end">
                <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-x-lg me-1"></i>Réinitialiser
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Liste des commandes -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                        <tr>
                            <td class="fw-bold">#{{ $commande->id }}</td>
                            <td>
                                <span class="fw-medium">{{ $commande->client->nom_complet }}</span>
                                <small class="text-muted d-block">{{ $commande->client->telephone }}</small>
                            </td>
                            <td>{{ $commande->date->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge statut-{{ $commande->statut }}">
                                    {{ App\Models\Commande::getStatutLabel($commande->statut) }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format($commande->total_ttc, 2) }} DH
                            </td>
                            <td class="text-center">
                                <a href="{{ route('commandes.show', $commande) }}" 
                                   class="btn btn-sm btn-outline-info me-1" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('commandes.print', $commande) }}" 
                                   class="btn btn-sm btn-outline-secondary me-1" title="Imprimer" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="{{ route('commandes.exportPdf', $commande) }}" 
                                   class="btn btn-sm btn-outline-danger" title="PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">Aucune commande trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($commandes->hasPages())
        <div class="card-footer bg-white">
            {{ $commandes->links() }}
        </div>
    @endif
</div>
@endsection
