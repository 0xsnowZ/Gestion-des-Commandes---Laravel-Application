<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Page d'accueil - Catalogue des produits
Route::get('/', [HomeController::class, 'index'])->name('catalog.index');

// Routes du catalogue et panier
Route::prefix('catalog')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('catalog.index');
    Route::post('/add/{produit}', [HomeController::class, 'add'])->name('cart.add');
    Route::get('/cart', [HomeController::class, 'showCart'])->name('cart.show');
    Route::delete('/cart/delete/{produitId}', [HomeController::class, 'delete'])->name('cart.delete');
    Route::delete('/cart/clear', [HomeController::class, 'clear'])->name('cart.clear');
    Route::put('/cart/update/{produitId}', [HomeController::class, 'updateQuantity'])->name('cart.update');
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout', [HomeController::class, 'processOrder'])->name('cart.process');
});

// Routes des produits (CRUD complet avec upload d'images)
Route::resource('produits', ProduitController::class);

// Routes des commandes (CRUD complet avec gestion des statuts)
Route::resource('commandes', CommandeController::class);

// Routes additionnelles pour la gestion des commandes
Route::prefix('commandes')->group(function () {
    // Actions sur les produits d'une commande
    Route::post('/{commande}/produits', [CommandeController::class, 'addProduit'])->name('commandes.addProduit');
    Route::put('/{commande}/produits/{produit}', [CommandeController::class, 'updateProduit'])->name('commandes.updateProduit');
    Route::delete('/{commande}/produits/{produit}', [CommandeController::class, 'removeProduit'])->name('commandes.removeProduit');
    Route::post('/{commande}/sync', [CommandeController::class, 'syncProduits'])->name('commandes.syncProduits');

    // Calculs et recalculs
    Route::get('/{commande}/total', [CommandeController::class, 'calculateTotal'])->name('commandes.calculateTotal');
    Route::post('/{commande}/recalculate', [CommandeController::class, 'recalculate'])->name('commandes.recalculate');

    // Gestion des statuts
    Route::post('/{commande}/validate', [CommandeController::class, 'validateCommande'])->name('commandes.validate');
    Route::post('/{commande}/cancel', [CommandeController::class, 'cancel'])->name('commandes.cancel');
    Route::post('/{commande}/deliver', [CommandeController::class, 'deliver'])->name('commandes.deliver');
    Route::post('/{commande}/close', [CommandeController::class, 'close'])->name('commandes.close');
    Route::post('/{commande}/cloturer', [CommandeController::class, 'cloturer'])->name('commandes.cloturer');

    // Recherche et export
    Route::get('/search', [CommandeController::class, 'search'])->name('commandes.search');
    Route::get('/{commande}/pdf', [CommandeController::class, 'exportPdf'])->name('commandes.exportPdf');
    Route::get('/export/excel', [CommandeController::class, 'exportExcel'])->name('commandes.exportExcel');
    Route::get('/{commande}/print', [CommandeController::class, 'print'])->name('commandes.print');

    // Notifications et historique
    Route::post('/{commande}/notify', [CommandeController::class, 'notifyClient'])->name('commandes.notifyClient');
    Route::get('/{commande}/history', [CommandeController::class, 'history'])->name('commandes.history');

    // Archivage
    Route::post('/{commande}/archive', [CommandeController::class, 'archive'])->name('commandes.archive');
    Route::post('/{id}/restore', [CommandeController::class, 'restore'])->name('commandes.restore');
});

// Routes des clients (pour création rapide depuis les commandes)
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
