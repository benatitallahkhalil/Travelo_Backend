<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use Illuminate\Http\Request;


class HotelsController extends Controller
{
    // Récupérer tous les hôtels
// Dans HotelController.php
public function getAll() // Renommez la méthode index() en getAll()
{
    return response()->json(Hotels::all(), 200);
}

    // Créer un nouvel hôtel
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'urlImage' => 'required|string',
            'description' => 'required|string',
            'nbEtoiles' => 'required|integer|min:1|max:5',
        ]);

        $hotel = Hotels::create($request->all());

        return response()->json($hotel, 201);
    }

    // Récupérer un hôtel par son ID
    public function show($id)
    {
        $hotel = Hotels::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hôtel non trouvé'], 404);
        }
        return response()->json($hotel, 200);
    }

    // Mettre à jour un hôtel
    public function update(Request $request, $id)
    {
        $hotel = Hotels::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hôtel non trouvé'], 404);
        }

        $request->validate([
            'nom' => 'string|max:255',
            'adresse' => 'string|max:255',
            'urlImage' => 'string',
            'description' => 'string',
            'nbEtoiles' => 'integer|min:1|max:5',
        ]);

        $hotel->update($request->all());

        return response()->json($hotel, 200);
    }

    // Supprimer un hôtel
    public function destroy($id)
    {
        $hotel = Hotels::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hôtel non trouvé'], 404);
        }

        $hotel->delete();

        return response()->json(['message' => 'Hôtel supprimé'], 200);
    }
}
