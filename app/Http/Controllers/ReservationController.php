<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Offre;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Lister toutes les réservations
    public function index() {
        return response()->json(Reservation::with(['user', 'offre'])->get());
    }

    // Créer une nouvelle réservation
    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'date_debut' => 'required|date',
            'nbr_jour' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
            'offre_id' => 'required|exists:offres,id',
        ]);

        // Récupérer le prix de l'offre
        $offre = Offre::findOrFail($request->offre_id);
        $prixTotale = $offre->prix * $request->nbr_jour;

        $reservation = Reservation::create([
            'date' => $request->date,
            'date_debut' => $request->date_debut,
            'nbr_jour' => $request->nbr_jour,
            'prix_totale' => $prixTotale,
            'user_id' => $request->user_id,
            'offre_id' => $request->offre_id,
            'etatReservation' => 'en attente',
        ]);

        return response()->json([
            'message' => 'Réservation créée avec succès',
            'reservation' => $reservation
        ]);
    }

    // Confirmer ou annuler la réservation par le client
    public function updateClientReservation(Request $request, $id)
    {
        try {
            $request->validate([
                'etatReservation' => 'required|in:confirme,annule',
            ]);
            
    
            $reservation = Reservation::find($id);
    
            if (!$reservation) {
                return response()->json(['message' => 'Réservation non trouvée'], 404);
            }
    
            $reservation->etatReservation = $request->etatReservation;
            $reservation->save();
    
            return response()->json(['message' => 'Mise à jour réussie', 'reservation' => $reservation]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
        
    
        // Accepter ou refuser la réservation par l'admin
        public function updateAdminReservation(Request $request, $id) {
            $request->validate([
                'etatReservation' => 'required|in:accepte,refuse',
            ]);
        
            $reservation = Reservation::findOrFail($id);
            $reservation->update(['etatReservation' => $request->etatReservation]);
        
            return response()->json([
                'message' => 'Mise à jour réussie par l\'administrateur',
                'reservation' => $reservation,
            ]);
        }

    // Supprimer une réservation
    public function destroy($id) {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée avec succès'
        ]);
    }
}
