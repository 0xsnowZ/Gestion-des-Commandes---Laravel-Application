<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommandesExport;

class CommandeController extends Controller
{
    /**
     * Liste paginée des commandes avec filtres
     */
    public function index(Request $request)
    {
        $query = Commande::with('client');

        // Filtres
        if ($request->filled('client')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->client . '%')
                    ->orWhere('prenom', 'like', '%' . $request->client . '%');
            });
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('montant_min')) {
            $query->where('total_ttc', '>=', $request->montant_min);
        }

        if ($request->filled('montant_max')) {
            $query->where('total_ttc', '<=', $request->montant_max);
        }

        $commandes = $query->latest()->paginate(15);
        $statuts = [
            Commande::STATUT_BROUILLON => 'Brouillon',
            Commande::STATUT_EN_ATTENTE => 'En attente',
            Commande::STATUT_CONFIRMEE => 'Confirmée',
            Commande::STATUT_ENVOYEE => 'Envoyée',
            Commande::STATUT_LIVREE => 'Livrée',
            Commande::STATUT_RETOURNEE => 'Retournée',
            Commande::STATUT_ANNULEE => 'Annulée',
            Commande::STATUT_CLOTUREE => 'Clôturée'
        ];

        return view('commandes.index', compact('commandes', 'statuts'));
    }

    /**
     * Créer une nouvelle commande
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $validated['statut'] = Commande::STATUT_BROUILLON;

        $commande = Commande::create($validated);

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande créée avec succès.');
    }

    /**
     * Afficher le détail d'une commande
     */
    public function show(Commande $commande)
    {
        $commande->load(['client', 'produits']);
        $produits = Produit::actifs()->get();

        return view('commandes.show', compact('commande', 'produits'));
    }

    /**
     * Modifier les informations générales de la commande
     */
    public function update(Request $request, Commande $commande)
    {
        if (!$commande->peutEtreModifiee()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $commande->update($validated);

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Supprimer/Annuler une commande (soft delete)
     */
    public function destroy(Commande $commande)
    {
        if (!$commande->annuler()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être annulée.');
        }

        return redirect()->route('commandes.index')
            ->with('success', 'Commande annulée avec succès.');
    }

    /**
     * Ajouter un produit à la commande
     */
    public function addProduit(Request $request, Commande $commande)
    {
        if (!$commande->peutEtreModifiee()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0'
        ]);

        $commande->addProduit(
            $validated['produit_id'],
            $validated['quantite'],
            $validated['prix']
        );

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Produit ajouté à la commande.');
    }

    /**
     * Modifier la quantité ou le prix d'un produit
     */
    public function updateProduit(Request $request, Commande $commande, Produit $produit)
    {
        if (!$commande->peutEtreModifiee()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'quantite' => 'required|integer|min:1',
            'prix' => 'nullable|numeric|min:0'
        ]);

        $commande->updateProduit(
            $produit->id,
            $validated['quantite'],
            $validated['prix'] ?? null
        );

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Produit mis à jour.');
    }

    /**
     * Supprimer un produit de la commande
     */
    public function removeProduit(Commande $commande, Produit $produit)
    {
        if (!$commande->peutEtreModifiee()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $commande->removeProduit($produit->id);

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Produit retiré de la commande.');
    }

    /**
     * Synchroniser les produits de la commande
     */
    public function syncProduits(Request $request, Commande $commande)
    {
        if (!$commande->peutEtreModifiee()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'produits' => 'required|array',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix' => 'required|numeric|min:0'
        ]);

        $produits = [];
        foreach ($validated['produits'] as $produit) {
            $produits[$produit['id']] = [
                'quantite' => $produit['quantite'],
                'prix' => $produit['prix']
            ];
        }

        $commande->syncProduits($produits);

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Produits synchronisés.');
    }

    /**
     * Calculer le total de la commande
     */
    public function calculateTotal(Commande $commande)
    {
        $totals = $commande->calculateTotal();

        return response()->json([
            'ht' => $totals['ht'],
            'tva' => $totals['tva'],
            'ttc' => $totals['ttc']
        ]);
    }

    /**
     * Recalculer les totaux
     */
    public function recalculate(Commande $commande)
    {
        $commande->recalculate();

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Totaux recalculés.');
    }

    /**
     * Valider la commande
     */
    public function validateCommande(Commande $commande)
    {
        if (!$commande->valider()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être validée.');
        }

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande validée avec succès.');
    }

    /**
     * Annuler la commande
     */
    public function cancel(Commande $commande)
    {
        if (!$commande->annuler()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être annulée.');
        }

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande annulée avec succès.');
    }

    /**
     * Marquer comme envoyée
     */
    public function deliver(Commande $commande)
    {
        if (!$commande->envoyer()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être marquée comme envoyée.');
        }

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande marquée comme envoyée.');
    }

    /**
     * Marquer comme livrée
     */
    public function close(Commande $commande)
    {
        if (!$commande->livrer()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être marquée comme livrée.');
        }

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande marquée comme livrée.');
    }

    /**
     * Clôturer la commande
     */
    public function cloturer(Commande $commande)
    {
        if (!$commande->clore()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être clôturée.');
        }

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande clôturée avec succès.');
    }

    /**
     * Recherche avancée de commandes
     */
    public function search(Request $request)
    {
        $query = Commande::with('client');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%");
                    });
            });
        }

        $commandes = $query->latest()->paginate(15);

        return view('commandes.index', compact('commandes'));
    }

    /**
     * Générer un PDF (facture)
     */
    public function exportPdf(Commande $commande)
    {
        $commande->load(['client', 'produits']);

        $pdf = PDF::loadView('commandes.pdf', compact('commande'));

        return $pdf->download('commande-' . $commande->id . '.pdf');
    }

    /**
     * Exporter en Excel
     */
    public function exportExcel()
    {
        return Excel::download(new CommandesExport, 'commandes.xlsx');
    }

    /**
     * Version imprimable
     */
    public function print(Commande $commande)
    {
        $commande->load(['client', 'produits']);

        return view('commandes.print', compact('commande'));
    }

    /**
     * Envoyer une notification au client
     */
    public function notifyClient(Commande $commande)
    {
        // Ici, vous intégreriez un service d'envoi d'emails ou SMS
        // Exemple: Mail::to($commande->client->email)->send(new CommandeNotification($commande));

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Notification envoyée au client.');
    }

    /**
     * Afficher l'historique de la commande
     */
    public function history(Commande $commande)
    {
        // Ici, vous pourriez implémenter un système de logs
        // Pour l'instant, on affiche juste les dates importantes

        return view('commandes.history', compact('commande'));
    }

    /**
     * Archiver une commande
     */
    public function archive(Commande $commande)
    {
        if (!$commande->archiver()) {
            return redirect()->back()
                ->with('error', 'Cette commande ne peut pas être archivée.');
        }

        return redirect()->route('commandes.index')
            ->with('success', 'Commande archivée avec succès.');
    }

    /**
     * Restaurer une commande archivée
     */
    public function restore($id)
    {
        $commande = Commande::withTrashed()->findOrFail($id);
        $commande->restore();

        return redirect()->route('commandes.index')
            ->with('success', 'Commande restaurée avec succès.');
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $clients = Client::all();
        $produits = Produit::actifs()->get();

        return view('commandes.create', compact('clients', 'produits'));
    }
}
