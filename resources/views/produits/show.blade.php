@extends('layouts.app')

@section('title', $produit->designation)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('produits.index') }}">Produits</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $produit->designation }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body text-center p-4">
                @if($produit->image)
                    <img src="{{ $produit->image_url }}" 
                         alt="{{ $produit->designation }}" 
                         class="img-fluid rounded" style="max-height: 350px; object-fit: cover;">
                @else
                    <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                         style="height: 350px;">
                        <i class="bi bi-image text-secondary" style="font-size: 6rem;"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7 mt-4 mt-lg-0">
        <div class="card h-100">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ $produit->designation }}</h3>
                    <div>
                        @if($produit->actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-2">Prix</h5>
                        <p class="fs-2 fw-bold text-primary mb-0">
                            {{ number_format($produit->prix, 2) }} DH
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted mb-2">Stock disponible</h5>
                        <p class="fs-2 fw-bold mb-0">
                            <span class="badge bg-{{ $produit->stock > 10 ? 'success' : ($produit->stock > 0 ? 'warning text-dark' : 'danger') }} fs-5">
                                {{ $produit->stock }} unités
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="text-muted mb-2">Description</h5>
                    <p class="mb-0">
                        {{ $produit->description ?: 'Aucune description disponible.' }}
                    </p>
                </div>

                <div class="mb-4">
                    <h5 class="text-muted mb-2">Informations</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td class="ps-0 text-muted">ID</td>
                            <td class="text-end fw-medium">#{{ $produit->id }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">Créé le</td>
                            <td class="text-end fw-medium">{{ $produit->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">Dernière modification</td>
                            <td class="text-end fw-medium">{{ $produit->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Retour
                    </a>
                    <div>
                        <a href="{{ route('produits.edit', $produit) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil me-2"></i>Modifier
                        </a>
                        <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                <i class="bi bi-trash me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
