<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer des produits de démonstration
        $produits = [
            [
                'designation' => 'Ordinateur Portable HP',
                'description' => 'Ordinateur portable HP 15.6" avec processeur Intel Core i5, 8GB RAM, 512GB SSD',
                'prix' => 5999.00,
                'stock' => 15,
                'actif' => true
            ],
            [
                'designation' => 'Souris Gaming Logitech',
                'description' => 'Souris gaming sans fil avec capteur HERO 25K, 25 600 DPI',
                'prix' => 499.00,
                'stock' => 50,
                'actif' => true
            ],
            [
                'designation' => 'Clavier Mécanique RGB',
                'description' => 'Clavier mécanique gaming avec switches Cherry MX Red et rétroéclairage RGB',
                'prix' => 799.00,
                'stock' => 30,
                'actif' => true
            ],
            [
                'designation' => 'Écran 27" Full HD',
                'description' => 'Écran LED 27 pouces Full HD 1920x1080, 75Hz, IPS',
                'prix' => 1499.00,
                'stock' => 20,
                'actif' => true
            ],
            [
                'designation' => 'Casque Audio Bluetooth',
                'description' => 'Casque sans fil avec réduction de bruit active, autonomie 30h',
                'prix' => 899.00,
                'stock' => 25,
                'actif' => true
            ],
            [
                'designation' => 'Disque Dur Externe 2To',
                'description' => 'Disque dur externe portable USB 3.0, 2 To',
                'prix' => 649.00,
                'stock' => 40,
                'actif' => true
            ],
            [
                'designation' => 'Webcam HD 1080p',
                'description' => 'Webcam Full HD 1080p avec microphone intégré',
                'prix' => 349.00,
                'stock' => 35,
                'actif' => true
            ],
            [
                'designation' => 'Support PC Portable',
                'description' => 'Support réglable en aluminium pour ordinateur portable',
                'prix' => 199.00,
                'stock' => 60,
                'actif' => true
            ]
        ];

        foreach ($produits as $produit) {
            Produit::create($produit);
        }

        // Créer quelques clients
        $clients = Client::factory(5)->create();

        // Créer quelques commandes de démonstration
        foreach ($clients as $client) {
            $commande = Commande::create([
                'client_id' => $client->id,
                'date' => now()->subDays(rand(1, 30)),
                'statut' => array_rand([
                    Commande::STATUT_BROUILLON => 1,
                    Commande::STATUT_EN_ATTENTE => 1,
                    Commande::STATUT_CONFIRMEE => 1,
                    Commande::STATUT_LIVREE => 1
                ]),
                'total_ht' => 0,
                'tva' => 0,
                'total_ttc' => 0
            ]);

            // Ajouter 2-4 produits aléatoires à chaque commande
            $produitsCommande = Produit::inRandomOrder()->take(rand(2, 4))->get();
            foreach ($produitsCommande as $produit) {
                $quantite = rand(1, 3);
                $commande->addProduit($produit->id, $quantite, $produit->prix);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Produits créés: ' . Produit::count());
        $this->command->info('Clients créés: ' . Client::count());
        $this->command->info('Commandes créées: ' . Commande::count());
    }
}
