<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    const CART_COOKIE_NAME = 'shopping_cart';
    const CART_COOKIE_DURATION = 60 * 24 * 7; // 7 jours

    /**
     * Afficher le catalogue des produits
     */
    public function index()
    {
        $produits = Produit::actifs()->enStock()->latest()->paginate(12);
        $cartCount = $this->getCartCount();
        
        return view('catalog.index', compact('produits', 'cartCount'));
    }

    /**
     * Ajouter un produit au panier (cookie)
     */
    public function add(Request $request, Produit $produit)
    {
        $request->validate([
            'quantite' => 'required|integer|min:1|max:' . $produit->stock
        ]);

        $cart = $this->getCart();
        $quantite = $request->input('quantite', 1);

        if (isset($cart[$produit->id])) {
            $cart[$produit->id]['quantite'] += $quantite;
        } else {
            $cart[$produit->id] = [
                'produit_id' => $produit->id,
                'designation' => $produit->designation,
                'prix' => $produit->prix,
                'quantite' => $quantite,
                'image' => $produit->image
            ];
        }

        $this->saveCart($cart);

        return redirect()->back()
            ->with('success', 'Produit ajouté au panier.');
    }

    /**
     * Afficher le contenu du panier
     */
    public function showCart()
    {
        $cart = $this->getCart();
        $total = $this->calculateCartTotal($cart);
        
        return view('catalog.cart', compact('cart', 'total'));
    }

    /**
     * Supprimer un produit du panier
     */
    public function delete(Request $request, $produitId)
    {
        $cart = $this->getCart();
        
        if (isset($cart[$produitId])) {
            unset($cart[$produitId]);
            $this->saveCart($cart);
        }

        return redirect()->route('cart.show')
            ->with('success', 'Produit retiré du panier.');
    }

    /**
     * Vider le panier
     */
    public function clear()
    {
        $this->saveCart([]);

        return redirect()->route('cart.show')
            ->with('success', 'Panier vidé.');
    }

    /**
     * Mettre à jour la quantité d'un produit dans le panier
     */
    public function updateQuantity(Request $request, $produitId)
    {
        $request->validate([
            'quantite' => 'required|integer|min:1'
        ]);

        $cart = $this->getCart();
        
        if (isset($cart[$produitId])) {
            $cart[$produitId]['quantite'] = $request->input('quantite');
            $this->saveCart($cart);
        }

        return redirect()->route('cart.show')
            ->with('success', 'Quantité mise à jour.');
    }

    /**
     * Afficher le formulaire de commande
     */
    public function checkout()
    {
        $cart = $this->getCart();
        
        if (empty($cart)) {
            return redirect()->route('catalog.index')
                ->with('error', 'Votre panier est vide.');
        }

        $total = $this->calculateCartTotal($cart);
        
        return view('catalog.checkout', compact('cart', 'total'));
    }

    /**
     * Traiter la commande
     */
    public function processOrder(Request $request)
    {
        $cart = $this->getCart();
        
        if (empty($cart)) {
            return redirect()->route('catalog.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Validation des informations client
        $validatedClient = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'ville' => 'required|string|max:255',
            'adresse' => 'required|string'
        ]);

        // Créer le client
        $client = Client::create($validatedClient);

        // Calculer les totaux
        $totalHT = 0;
        foreach ($cart as $item) {
            $totalHT += $item['prix'] * $item['quantite'];
        }
        $tva = $totalHT * 0.20;
        $totalTTC = $totalHT + $tva;

        // Créer la commande
        $commande = Commande::create([
            'client_id' => $client->id,
            'date' => now(),
            'statut' => Commande::STATUT_EN_ATTENTE,
            'total_ht' => $totalHT,
            'tva' => $tva,
            'total_ttc' => $totalTTC
        ]);

        // Ajouter les produits à la commande
        foreach ($cart as $item) {
            $commande->addProduit(
                $item['produit_id'],
                $item['quantite'],
                $item['prix']
            );

            // Mettre à jour le stock
            $produit = Produit::find($item['produit_id']);
            if ($produit) {
                $produit->stock -= $item['quantite'];
                $produit->save();
            }
        }

        // Vider le panier
        $this->saveCart([]);

        return redirect()->route('catalog.index')
            ->with('success', 'Commande #' . $commande->id . ' créée avec succès !');
    }

    /**
     * Récupérer le contenu du panier depuis le cookie
     */
    private function getCart(): array
    {
        $cartJson = Cookie::get(self::CART_COOKIE_NAME);
        
        if ($cartJson) {
            $cart = json_decode($cartJson, true);
            return is_array($cart) ? $cart : [];
        }
        
        return [];
    }

    /**
     * Sauvegarder le panier dans le cookie
     */
    private function saveCart(array $cart): void
    {
        Cookie::queue(
            self::CART_COOKIE_NAME,
            json_encode($cart),
            self::CART_COOKIE_DURATION
        );
    }

    /**
     * Calculer le total du panier
     */
    private function calculateCartTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['prix'] * $item['quantite'];
        }
        return $total;
    }

    /**
     * Obtenir le nombre d'articles dans le panier
     */
    private function getCartCount(): int
    {
        $cart = $this->getCart();
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantite'];
        }
        return $count;
    }
}
