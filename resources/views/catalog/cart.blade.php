@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-cart3 me-2"></i>Mon Panier
        </h1>
        <p class="text-muted">Gérez les articles de votre panier avant de passer commande.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Continuer les achats
        </a>
    </div>
</div>

@if(count($cart) > 0)
    <div class="row">
        <!-- Liste des produits -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bag me-2"></i>Articles ({{ count($cart) }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Prix unitaire</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}" 
                                                         alt="{{ $item['designation'] }}" 
                                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-secondary d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                @endif
                                                <span class="ms-3 fw-medium">{{ $item['designation'] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ number_format($item['prix'], 2) }} DH
                                        </td>
                                        <td class="text-center align-middle">
                                            <form action="{{ route('cart.update', $item['produit_id']) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm" style="max-width: 120px; margin: 0 auto;">
                                                    <input type="number" name="quantite" value="{{ $item['quantite'] }}" 
                                                           min="1" class="form-control text-center">
                                                    <button type="submit" class="btn btn-outline-primary">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end align-middle fw-bold">
                                            {{ number_format($item['prix'] * $item['quantite'], 2) }} DH
                                        </td>
                                        <td class="text-center align-middle">
                                            <form action="{{ route('cart.delete', $item['produit_id']) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-2"></i>Vider le panier
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Résumé de la commande -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="total-box mb-4">
                <h5 class="mb-3">
                    <i class="bi bi-calculator me-2"></i>Résumé
                </h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Sous-total HT</span>
                    <span>{{ number_format($total, 2) }} DH</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>TVA (20%)</span>
                    <span>{{ number_format($total * 0.20, 2) }} DH</span>
                </div>
                <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total TTC</span>
                    <span>{{ number_format($total * 1.20, 2) }} DH</span>
                </div>
            </div>

            <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-lg w-100">
                <i class="bi bi-credit-card me-2"></i>Passer la commande
            </a>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
        <h3 class="mt-3 text-muted">Votre panier est vide</h3>
        <p class="text-muted">Parcourez notre catalogue pour ajouter des produits.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg mt-3">
            <i class="bi bi-grid me-2"></i>Voir le catalogue
        </a>
    </div>
@endif
@endsection
