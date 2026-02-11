@extends('layouts.app')

@section('title', 'Catalogue des Produits')

@section('styles')
<style>
    /* Header Section */
    .catalog-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
    }
    
    /* Product Card Styles */
    .product-card-modern {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .product-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    
    /* Image Container */
    .product-img-container {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: #f8f9fa;
    }
    .product-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .product-card-modern:hover .product-img-container img {
        transform: scale(1.08);
    }
    .product-img-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    }
    
    /* Sale Badge */
    .sale-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
    }
    
    /* Wishlist Button */
    .wishlist-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 2;
    }
    .wishlist-btn:hover {
        color: #ef4444;
        transform: scale(1.1);
    }
    .wishlist-btn.active {
        color: #ef4444;
    }
    
    /* Stock Badge */
    .stock-badge-overlay {
        position: absolute;
        bottom: 12px;
        left: 12px;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }
    .stock-high { background: rgba(16, 185, 129, 0.9); color: white; }
    .stock-medium { background: rgba(245, 158, 11, 0.9); color: white; }
    .stock-low { background: rgba(239, 68, 68, 0.9); color: white; }
    
    /* Card Body */
    .product-card-body {
        padding: 1.25rem;
    }
    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8em;
    }
    .product-description {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
        min-height: 2.55em;
    }
    
    /* Rating Stars */
    .rating-stars {
        display: flex;
        align-items: center;
        gap: 0.15rem;
        margin-bottom: 0.75rem;
    }
    .rating-stars i {
        font-size: 0.85rem;
        color: #fbbf24;
    }
    .rating-stars i.empty {
        color: #e5e7eb;
    }
    .rating-count {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-left: 0.5rem;
    }
    
    /* Price Section */
    .price-section {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .current-price {
        font-size: 1.35rem;
        font-weight: 700;
        color: #2563eb;
    }
    .original-price {
        font-size: 0.9rem;
        color: #9ca3af;
        text-decoration: line-through;
    }
    
    /* Add to Cart Section */
    .add-cart-row {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem;
        font-weight: 500;
        transition: border-color 0.2s;
    }
    .qty-input:focus {
        outline: none;
        border-color: #3b82f6;
    }
    .btn-add-cart {
        flex: 1;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        border-radius: 0.5rem;
        color: white;
        padding: 0.55rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all 0.3s ease;
    }
    .btn-add-cart:hover:not(:disabled) {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        color: white;
    }
    .btn-add-cart:disabled {
        background: #d1d5db;
        cursor: not-allowed;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.06);
    }
    .empty-icon {
        width: 100px;
        height: 100px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    /* View Cart Button */
    .btn-view-cart {
        background: rgba(255,255,255,0.15);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        border-radius: 0.75rem;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-view-cart:hover {
        background: white;
        color: #2563eb;
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Header -->
    <div class="catalog-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold mb-2">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Catalogue des Produits
                </h1>
                <p class="mb-0 opacity-75">Découvrez notre sélection de produits et ajoutez-les à votre panier.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('cart.show') }}" class="btn btn-view-cart">
                    <i class="bi bi-cart3 me-2"></i>Voir le Panier
                </a>
            </div>
        </div>
    </div>

    @if($produits->count() > 0)
        <div class="row g-4">
            @foreach($produits as $produit)
                <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                    <div class="card product-card-modern h-100">
                        <!-- Image Container -->
                        <div class="product-img-container">
                            @if($produit->image)
                                <img src="{{ $produit->image_url }}" alt="{{ $produit->designation }}">
                            @else
                                <div class="product-img-placeholder">
                                    <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Sale Badge (show randomly for demo, or based on condition) -->
                            @if($produit->id % 3 == 0)
                                <span class="sale-badge">-20%</span>
                            @endif
                            
                            <!-- Wishlist Heart Icon -->
                            <button type="button" class="wishlist-btn" onclick="this.classList.toggle('active')">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                            
                            <!-- Stock Badge -->
                            <span class="stock-badge-overlay {{ $produit->stock > 10 ? 'stock-high' : ($produit->stock > 0 ? 'stock-medium' : 'stock-low') }}">
                                <i class="bi bi-box-seam me-1"></i>{{ $produit->stock }} en stock
                            </span>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="product-card-body">
                            <h5 class="product-title">{{ $produit->designation }}</h5>
                            <p class="product-description">{{ Str::limit($produit->description, 60) }}</p>
                            
                            <!-- Star Rating -->
                            <div class="rating-stars">
                                @php $rating = ($produit->id % 5) + 1; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $rating ? '' : 'empty' }}"></i>
                                @endfor
                                <span class="rating-count">({{ rand(10, 150) }})</span>
                            </div>
                            
                            <!-- Price -->
                            <div class="price-section">
                                <span class="current-price">{{ number_format($produit->prix, 2) }} DH</span>
                                @if($produit->id % 3 == 0)
                                    <span class="original-price">{{ number_format($produit->prix * 1.25, 2) }} DH</span>
                                @endif
                            </div>
                            
                            <!-- Add to Cart -->
                            <form action="{{ route('cart.add', $produit) }}" method="POST">
                                @csrf
                                <div class="add-cart-row">
                                    <input type="number" name="quantite" value="1" min="1" 
                                           max="{{ $produit->stock }}" class="qty-input"
                                           {{ $produit->stock <= 0 ? 'disabled' : '' }}>
                                    <button type="submit" class="btn btn-add-cart" 
                                            {{ $produit->stock <= 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus"></i>
                                        <span>Ajouter</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-5 d-flex justify-content-center">
            {{ $produits->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-inbox" style="font-size: 2.5rem; color: #9ca3af;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Aucun produit disponible</h3>
            <p class="text-muted mb-0">Veuillez revenir plus tard ou contacter l'administrateur.</p>
        </div>
    @endif
</div>
@endsection
