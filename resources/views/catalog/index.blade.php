@extends('layouts.app')

@section('title', 'Catalogue des Produits')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-grid-3x3-gap me-2"></i>Catalogue des Produits
        </h1>
        <p class="text-muted">Découvrez notre sélection de produits et ajoutez-les à votre panier.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('cart.show') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-cart3 me-2"></i>Voir le Panier
        </a>
    </div>
</div>

@if($produits->count() > 0)
    <div class="row g-4">
        @foreach($produits as $produit)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card product-card h-100">
                    @if($produit->image)
                        <img src="{{ $produit->image_url }}" 
                             alt="{{ $produit->designation }}" 
                             class="product-image">
                    @else
                        <div class="product-image-placeholder">
                            <i class="bi bi-image text-secondary" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold">{{ $produit->designation }}</h5>
                        <p class="card-text text-muted flex-grow-1">
                            {{ Str::limit($produit->description, 80) }}
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="price-tag">{{ number_format($produit->prix, 2) }} DH</span>
                            <span class="badge bg-{{ $produit->stock > 10 ? 'success' : ($produit->stock > 0 ? 'warning' : 'danger') }}">
                                Stock: {{ $produit->stock }}
                            </span>
                        </div>
                        
                        <form action="{{ route('cart.add', $produit) }}" method="POST" class="mt-auto">
                            @csrf
                            <div class="input-group">
                                <input type="number" name="quantite" value="1" min="1" 
                                       max="{{ $produit->stock }}" 
                                       class="form-control" style="max-width: 80px;">
                                <button type="submit" class="btn btn-primary" 
                                        {{ $produit->stock <= 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus me-1"></i>Ajouter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="mt-4">
        {{ $produits->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
        <h3 class="mt-3 text-muted">Aucun produit disponible</h3>
        <p class="text-muted">Veuillez revenir plus tard ou contacter l'administrateur.</p>
    </div>
@endif
@endsection
