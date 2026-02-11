<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    /**
     * Afficher la liste des produits
     */
    public function index()
    {
        $produits = Produit::latest()->paginate(10);
        return view('produits.index', compact('produits'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('produits.create');
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'actif' => 'boolean'
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products/images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['actif'] = $request->has('actif');

        Produit::create($validatedData);

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Afficher un produit spécifique
     */
    public function show(Produit $produit)
    {
        return view('produits.show', compact('produit'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Produit $produit)
    {
        return view('produits.edit', compact('produit'));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, Produit $produit)
    {
        $validatedData = $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'actif' => 'boolean'
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->image && Storage::disk('public')->exists($produit->image)) {
                Storage::disk('public')->delete($produit->image);
            }

            $imagePath = $request->file('image')->store('products/images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['actif'] = $request->has('actif');

        $produit->update($validatedData);

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Produit $produit)
    {
        // Supprimer l'image si elle existe
        if ($produit->image && Storage::disk('public')->exists($produit->image)) {
            Storage::disk('public')->delete($produit->image);
        }

        $produit->delete();

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
