<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Récupérer toutes les offres
    public function index()
    {
        return response()->json(Offre::all(), 200);
    }

    // Créer une nouvelle offre
    public function store(Request $request)
    {
        $request->validate([
            'prix' => 'required|numeric',
            'description' => 'required|string',
            'urlImage' => 'required|string',
            'hotelId' => 'required|exists:hotels,id',
            'type_chambre' => 'nullable|string',
            'nombre_personne' => 'nullable|integer',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $offre = Offre::create($request->all());

        return response()->json($offre, 201);
    }

    // Récupérer une offre par son ID
    public function show($id)
    {
        $offre = Offre::find($id);
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }
        return response()->json($offre, 200);
    }

    // Mettre à jour une offre
    public function update(Request $request, $id)
    {
        $offre = Offre::find($id);
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        $request->validate([
            'prix' => 'numeric',
            'description' => 'string',
            'urlImage' => 'string',
            'hotelId' => 'exists:hotels,id',
            'type_chambre' => 'nullable|string',
            'nombre_personne' => 'nullable|integer',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $offre->update($request->all());

        return response()->json($offre, 200);
    }

    // Supprimer une offre
    public function destroy($id)
    {
        $offre = Offre::find($id);
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        $offre->delete();

        return response()->json(['message' => 'Offre supprimée'], 200);
    }
    public function getOffersByHotel($hotelId)
{
    $offres = Offre::where('hotelId', $hotelId)->get();

    if ($offres->isEmpty()) {
        return response()->json(['message' => 'Aucune offre trouvée pour cet hôtel'], 404);
    }

    return response()->json($offres, 200);
}

    // Recherche d'offres avec filtres
    public function search(Request $request)
    {
        $query = Offre::query();

        if ($request->has('prix')) {
            $query->where('prix', $request->prix);
        }

        if ($request->has('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->has('hotelId')) {
            $query->where('hotelId', $request->hotelId);
        }

        // Filtres supplémentaires (optionnel)
        if ($request->has('type_chambre')) {
            $query->where('type_chambre', $request->type_chambre);
        }

        if ($request->has('nombre_personne')) {
            $query->where('nombre_personne', $request->nombre_personne);
        }

        if ($request->has('date_debut')) {
            $query->whereDate('date_debut', '>=', $request->date_debut);
        }

        if ($request->has('date_fin')) {
            $query->whereDate('date_fin', '<=', $request->date_fin);
        }

        $offres = $query->get();

        if ($offres->isEmpty()) {
            return response()->json(['message' => 'Aucune offre trouvée'], 404);
        }

        return response()->json($offres, 200);
    }
}
