@extends('layouts.app')

@section('title', 'Nouvelle Commande')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-plus-circle me-2"></i>Nouvelle Commande
        </h1>
        <p class="text-muted">Créez une nouvelle commande pour un client.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('commandes.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="client_id" class="form-label fw-medium">
                            <i class="bi bi-person me-1"></i>Client
                        </label>
                        <div class="input-group">
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom_complet }} - {{ $client->telephone }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
                                <i class="bi bi-plus-lg"></i> Nouveau
                            </button>
                        </div>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label fw-medium">
                            <i class="bi bi-calendar me-1"></i>Date de la commande
                        </label>
                        <input type="datetime-local" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-medium">
                            <i class="bi bi-sticky me-1"></i>Notes (optionnel)
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-2"></i>Créer la commande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Information
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">
                    La commande sera créée en statut <strong>"Brouillon"</strong>. 
                    Vous pourrez ajouter des produits et modifier la commande avant de la valider.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Client -->
<div class="modal fade" id="newClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('clients.store') }}" method="POST" id="newClientForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="telephone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" name="ville" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <textarea name="adresse" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le client</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
