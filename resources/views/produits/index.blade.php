@extends('layouts.app')

@section('title', 'Gestion des Produits')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-box-seam me-2"></i>Gestion des Produits
        </h1>
        <p class="text-muted">Gérez votre catalogue de produits.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('produits.create') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-lg me-2"></i>Nouveau Produit
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Désignation</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produits as $produit)
                        <tr>
                            <td>
                                @if($produit->image)
                                    <img src="{{ $produit->image_url }}" 
                                         alt="{{ $produit->designation }}" 
                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="rounded bg-secondary d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-image text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-medium">{{ $produit->designation }}</span>
                                @if($produit->description)
                                    <small class="text-muted d-block">{{ Str::limit($produit->description, 50) }}</small>
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($produit->prix, 2) }} DH</td>
                            <td>
                                <span class="badge bg-{{ $produit->stock > 10 ? 'success' : ($produit->stock > 0 ? 'warning text-dark' : 'danger') }}">
                                    {{ $produit->stock }}
                                </span>
                            </td>
                            <td>
                                @if($produit->actif)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('produits.show', $produit) }}" class="btn btn-sm btn-outline-info me-1">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('produits.edit', $produit) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">Aucun produit trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($produits->hasPages())
        <div class="card-footer bg-white">
            {{ $produits->links() }}
        </div>
    @endif
</div>
@endsection
