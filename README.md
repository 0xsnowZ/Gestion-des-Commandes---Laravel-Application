# Gestion des Commandes - Laravel Application

Application Laravel 

## Fonctionnalités

Cette application combine les trois TP :

### 1. TP Many-to-Many (Relations)
- Modèles `Commande` et `Produit` avec relation Many-to-Many via table pivot
- Table pivot `commande_produit` avec champs `quantite` et `prix`
- Méthodes Eloquent : `attach()`, `detach()`, `sync()`, `updateExistingPivot()`

### 2. TP Catalogue Produits (Panier)
- Affichage du catalogue des produits
- Panier utilisant les **cookies** (stockage côté client)
- Ajout, modification quantité, suppression de produits du panier
- Formulaire client et création de commande

### 3. TP Image Upload
- Upload d'images pour les produits
- Validation : `image|mimes:jpeg,png,jpg,gif,svg|max:2048`
- Stockage dans `storage/app/public/products/images`
- Affichage via lien symbolique

### Gestion des Commandes Complète
- **Statuts** : Brouillon → En attente → Confirmée → Envoyée → Livrée → Clôturée
- **Actions** : Valider, Annuler, Marquer envoyée, Marquer livrée, Clôturer
- **Export** : PDF et Excel
- **Impression** : Version imprimable

## Structure de la Base de Données

```
clients
    - id, nom, prenom, telephone, ville, adresse, email, timestamps

produits
    - id, designation, description, prix, stock, image, actif, timestamps

commandes
    - id, client_id, date, statut, total_ht, tva, total_ttc, notes
    - date_validation, date_expedition, date_livraison, date_annulation, date_cloture
    - softDeletes, timestamps

commande_produit (pivot)
    - id, commande_id, produit_id, quantite, prix, total_ligne (calculé), timestamps
```

## Installation

### 1. Cloner et installer les dépendances

```bash
cd laravel-gestion-commandes
composer install
```

### 2. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Modifiez le fichier `.env` avec vos informations de base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_commandes
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 3. Créer la base de données

```bash
mysql -u root -p -e "CREATE DATABASE gestion_commandes;"
```

### 4. Exécuter les migrations

```bash
php artisan migrate
```

### 5. Créer le lien symbolique pour le stockage

```bash
php artisan storage:link
```

### 6. (Optionnel) Remplir la base avec des données de test

```bash
php artisan db:seed
```

### 7. Lancer le serveur de développement

```bash
php artisan serve
```

Accédez à l'application : http://localhost:8000

## Routes Principales

### Catalogue & Panier
| Méthode | URI | Description |
|---------|-----|-------------|
| GET | `/` | Catalogue des produits |
| POST | `/catalog/add/{produit}` | Ajouter au panier |
| GET | `/catalog/cart` | Voir le panier |
| PUT | `/catalog/cart/update/{produitId}` | Modifier quantité |
| DELETE | `/catalog/cart/delete/{produitId}` | Supprimer du panier |
| DELETE | `/catalog/cart/clear` | Vider le panier |
| GET | `/catalog/checkout` | Formulaire commande |
| POST | `/catalog/checkout` | Traiter la commande |

### Produits (CRUD)
| Méthode | URI | Description |
|---------|-----|-------------|
| GET | `/produits` | Liste des produits |
| GET | `/produits/create` | Formulaire création |
| POST | `/produits` | Créer un produit |
| GET | `/produits/{produit}` | Détails produit |
| GET | `/produits/{produit}/edit` | Formulaire modification |
| PUT | `/produits/{produit}` | Mettre à jour |
| DELETE | `/produits/{produit}` | Supprimer |

### Commandes (CRUD + Actions)
| Méthode | URI | Description |
|---------|-----|-------------|
| GET | `/commandes` | Liste des commandes |
| POST | `/commandes` | Créer une commande |
| GET | `/commandes/{commande}` | Détails commande |
| PUT | `/commandes/{commande}` | Mettre à jour |
| DELETE | `/commandes/{commande}` | Supprimer |
| POST | `/commandes/{commande}/validate` | Valider |
| POST | `/commandes/{commande}/cancel` | Annuler |
| POST | `/commandes/{commande}/deliver` | Marquer envoyée |
| POST | `/commandes/{commande}/close` | Marquer livrée |
| POST | `/commandes/{commande}/cloturer` | Clôturer |
| GET | `/commandes/{commande}/pdf` | Export PDF |
| GET | `/commandes/{commande}/print` | Version imprimable |

## Diagramme de Classes

```
┌─────────────┐       ┌─────────────────┐       ┌─────────────┐
│   Client    │       │    Commande     │       │   Produit   │
├─────────────┤       ├─────────────────┤       ├─────────────┤
│ - id        │1     *│ - id            │*     *│ - id        │
│ - nom       │───────│ - client_id     │───────│ - designation│
│ - prenom    │       │ - date          │       │ - prix      │
│ - telephone │       │ - statut        │       │ - stock     │
│ - ville     │       │ - total_ht      │       │ - image     │
│ - adresse   │       │ - tva           │       └─────────────┘
└─────────────┘       │ - total_ttc     │
                      │ - notes         │
                      └─────────────────┘
                               │
                               │ (pivot)
                               ▼
                      ┌─────────────────┐
                      │ commande_produit│
                      ├─────────────────┤
                      │ - commande_id   │
                      │ - produit_id    │
                      │ - quantite      │
                      │ - prix          │
                      └─────────────────┘
```

## Technologies Utilisées

- **Framework** : Laravel 10.x
- **Langage** : PHP 8.1+
- **Base de données** : MySQL / MariaDB
- **Frontend** : Bootstrap 5, Bootstrap Icons
- **PDF** : barryvdh/laravel-dompdf
- **Excel** : maatwebsite/excel

## Auteur

Développé dans le cadre de la formation **Développement Digital - Web Full Stack**  
**OFPPT - ISTA INZEGANE**  
Année de formation : 2024/2025 - 2025/2026

## Licence

Ce projet est développé à des fins éducatives.
