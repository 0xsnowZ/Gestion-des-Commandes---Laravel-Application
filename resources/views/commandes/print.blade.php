<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande #{{ $commande->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-section h2 {
            font-size: 16px;
            color: #2563eb;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-grid {
            display: flex;
            justify-content: space-between;
        }
        
        .info-box {
            width: 48%;
        }
        
        .info-box p {
            margin-bottom: 5px;
        }
        
        .info-box .label {
            color: #666;
        }
        
        .info-box .value {
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }
        
        .total-section table {
            margin-bottom: 0;
        }
        
        .total-section td {
            border: none;
            padding: 8px 12px;
        }
        
        .total-section .total-row {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .status-brouillon { background: #6c757d; color: white; }
        .status-en_attente { background: #ffc107; color: black; }
        .status-confirmee { background: #17a2b8; color: white; }
        .status-envoyee { background: #0d6efd; color: white; }
        .status-livree { background: #198754; color: white; }
        .status-annulee { background: #dc3545; color: white; }
        .status-closee { background: #adb5bd; color: black; }
        
        @media print {
            body {
                padding: 20px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BON DE COMMANDE</h1>
        <p>N° {{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</p>
        <span class="status-badge status-{{ $commande->statut }}">
            {{ App\Models\Commande::getStatutLabel($commande->statut) }}
        </span>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-box">
                <h2>Informations Client</h2>
                <p><span class="label">Nom:</span> <span class="value">{{ $commande->client->nom_complet }}</span></p>
                <p><span class="label">Téléphone:</span> <span class="value">{{ $commande->client->telephone }}</span></p>
                <p><span class="label">Ville:</span> <span class="value">{{ $commande->client->ville }}</span></p>
                <p><span class="label">Adresse:</span> <span class="value">{{ $commande->client->adresse }}</span></p>
            </div>
            <div class="info-box">
                <h2>Détails Commande</h2>
                <p><span class="label">Date:</span> <span class="value">{{ $commande->date->format('d/m/Y H:i') }}</span></p>
                <p><span class="label">Date d'impression:</span> <span class="value">{{ now()->format('d/m/Y H:i') }}</span></p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Produits commandés</h2>
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Désignation</th>
                    <th class="text-center">Prix unit.</th>
                    <th class="text-center">Qté</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commande->produits as $index => $produit)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $produit->designation }}</td>
                        <td class="text-center">{{ number_format($produit->pivot->prix, 2) }} DH</td>
                        <td class="text-center">{{ $produit->pivot->quantite }}</td>
                        <td class="text-right">{{ number_format($produit->pivot->total_ligne, 2) }} DH</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total-section">
        <table>
            <tr>
                <td>Total HT</td>
                <td class="text-right">{{ number_format($commande->total_ht, 2) }} DH</td>
            </tr>
            <tr>
                <td>TVA (20%)</td>
                <td class="text-right">{{ number_format($commande->tva, 2) }} DH</td>
            </tr>
            <tr class="total-row">
                <td>Total TTC</td>
                <td class="text-right">{{ number_format($commande->total_ttc, 2) }} DH</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p><strong>OFPPT - ISTA INZEGANE</strong></p>
        <p>Filière DD WFS | Module : Développer en Backend</p>
        <p>Document généré automatiquement - Merci de votre confiance</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; cursor: pointer;">
            <i class="bi bi-printer"></i> Imprimer
        </button>
    </div>
</body>
</html>
