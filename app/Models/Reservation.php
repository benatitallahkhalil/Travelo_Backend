<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_debut',      // ✅ Nouveau champ
        'nbr_jour',        // ✅ Nouveau champ
        'prix_totale',     // ✅ Nouveau champ
        'user_id',
        'offre_id',
        'etatReservation'
    ];


    // Relation avec l'utilisateur
    public function user()
    { 
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation avec l'offre
    public function offre()
    {
        return $this->belongsTo(Offre::class, 'offre_id');
    }

    public function avis() {
        return $this->hasOne(Avis::class);
    }
    
}