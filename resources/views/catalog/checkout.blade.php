@extends('layouts.app')

@section('title', 'Finaliser la Commande')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-credit-card me-2"></i>Finaliser la Commande
        </h1>
        <p class="text-muted">Veuillez remplir vos informations pour compléter votre commande.</p>
    </div>
</div>

<div class="row">
    <!-- Formulaire client -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-person me-2"></i>Informations Client
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cart.process') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label fw-medium">
                                <i class="bi bi-person-fill me-1"></i>Nom
                            </label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label fw-medium">
                                <i class="bi bi-person me-1"></i>Prénom
                            </label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                   id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label fw-medium">
                                <i class="bi bi-telephone me-1"></i>Téléphone
                            </label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                   id="telephone" name="telephone" value="{{ old('telephone') }}" required>
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ville" class="form-label fw-medium">
                                <i class="bi bi-geo-alt me-1"></i>Ville
                            </label>
                            <input type="text" class="form-control @error('ville') is-invalid @enderror" 
                                   id="ville" name="ville" value="{{ old('ville') }}" required>
                            @error('ville')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="adresse" class="form-label fw-medium">
                            <i class="bi bi-house me-1"></i>Adresse complète
                        </label>
                        <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                  id="adresse" name="adresse" rows="3" required>{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Retour au panier
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Confirmer la commande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Résumé de la commande -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>Votre Commande
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush mb-3">
                    @foreach($cart as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="fw-medium">{{ $item['designation'] }}</span>
                                <small class="text-muted d-block">x{{ $item['quantite'] }}</small>
                            </div>
                            <span>{{ number_format($item['prix'] * $item['quantite'], 2) }} DH</span>
                        </li>
                    @endforeach
                </ul>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Sous-total HT</span>
                    <span>{{ number_format($total, 2) }} DH</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>TVA (20%)</span>
                    <span>{{ number_format($total * 0.20, 2) }} DH</span>
                </div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                    <span>Total TTC</span>
                    <span class="text-primary">{{ number_format($total * 1.20, 2) }} DH</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
