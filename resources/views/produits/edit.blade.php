@extends('layouts.app')

@section('title', 'Modifier le Produit')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-pencil-square me-2"></i>Modifier le Produit
        </h1>
        <p class="text-muted">Modifiez les informations du produit.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('produits.update', $produit) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="designation" class="form-label fw-medium">
                            <i class="bi bi-tag me-1"></i>Désignation
                        </label>
                        <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                               id="designation" name="designation" 
                               value="{{ old('designation', $produit->designation) }}" required>
                        @error('designation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium">
                            <i class="bi bi-text-paragraph me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $produit->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prix" class="form-label fw-medium">
                                <i class="bi bi-currency-dollar me-1"></i>Prix (DH)
                            </label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('prix') is-invalid @enderror" 
                                   id="prix" name="prix" 
                                   value="{{ old('prix', $produit->prix) }}" required>
                            @error('prix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-medium">
                                <i class="bi bi-boxes me-1"></i>Stock
                            </label>
                            <input type="number" min="0" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" name="stock" 
                                   value="{{ old('stock', $produit->stock) }}" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-medium">
                            <i class="bi bi-image me-1"></i>Image du produit
                        </label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*"
                               onchange="previewImage(this)">
                        <small class="text-muted">Laissez vide pour conserver l'image actuelle.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="actif" name="actif" value="1" 
                                   {{ old('actif', $produit->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">
                                Produit actif (visible dans le catalogue)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-2"></i>Mettre à jour
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
                    <i class="bi bi-image me-2"></i>Image actuelle
                </h5>
            </div>
            <div class="card-body text-center">
                <div id="imagePreview">
                    @if($produit->image)
                        <img src="{{ $produit->image_url }}" 
                             alt="{{ $produit->designation }}" 
                             class="img-fluid rounded" style="max-height: 250px; object-fit: cover;">
                    @else
                        <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                             style="height: 250px;">
                            <i class="bi bi-image text-secondary" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 250px; object-fit: cover;">';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
@endsection
