<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'ville' => 'required|string|max:255',
            'adresse' => 'required|string'
        ]);

        $client = Client::create($validated);

        return redirect()->back()
            ->with('success', 'Client créé avec succès.');
    }
}
