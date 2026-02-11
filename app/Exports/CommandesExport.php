<?php

namespace App\Exports;

use App\Models\Commande;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CommandesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Commande::with('client')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N° Commande',
            'Client',
            'Téléphone',
            'Date',
            'Statut',
            'Total HT',
            'TVA',
            'Total TTC',
            'Date de création'
        ];
    }

    /**
     * @param Commande $commande
     * @return array
     */
    public function map($commande): array
    {
        return [
            $commande->id,
            $commande->client->nom_complet,
            $commande->client->telephone,
            $commande->date->format('d/m/Y H:i'),
            Commande::getStatutLabel($commande->statut),
            number_format($commande->total_ht, 2),
            number_format($commande->tva, 2),
            number_format($commande->total_ttc, 2),
            $commande->created_at->format('d/m/Y H:i')
        ];
    }
}
