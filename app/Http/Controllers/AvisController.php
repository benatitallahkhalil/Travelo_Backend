<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    /**
     * Afficher tous les avis.
     */
    public function index()
    {
        $avis = Avis::with('reservation')->get();
        return response()->json($avis);
    }

    /**
     * Créer un nouvel avis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'commentaire' => 'required|string',
            'note' => 'required|integer|min:1|max:5',
            'reservation_id' => 'required|exists:reservations,id'
        ]);

        $avis = Avis::create([
            'commentaire' => $request->commentaire,
            'note' => $request->note,
            'reservation_id' => $request->reservation_id
        ]);

        return response()->json(['message' => 'Avis ajouté avec succès', 'avis' => $avis], 201);
    }

    /**
     * Afficher un avis spécifique.
     */
    public function show($id)
    {
        $avis = Avis::with('reservation')->find($id);
        if (!$avis) {
            return response()->json(['message' => 'Avis non trouvé'], 404);
        }
        return response()->json($avis);
    }

    /**
     * Mettre à jour un avis existant.
     */
    public function update(Request $request, $id)
    {
        $avis = Avis::find($id);
        if (!$avis) {
            return response()->json(['message' => 'Avis non trouvé'], 404);
        }

        $request->validate([
            'commentaire' => 'sometimes|string',
            'note' => 'sometimes|integer|min:1|max:5'
        ]);

        $avis->update($request->only('commentaire', 'note'));

        return response()->json(['message' => 'Avis mis à jour avec succès', 'avis' => $avis]);
    }

    /**
     * Supprimer un avis.
     */
    public function destroy($id)
    {
        $avis = Avis::find($id);
        if (!$avis) {
            return response()->json(['message' => 'Avis non trouvé'], 404);
        }

        $avis->delete();
        return response()->json(['message' => 'Avis supprimé avec succès']);
    }
}