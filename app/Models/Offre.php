<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;

    protected $fillable = [
        'prix',
        'description',
        'urlImage',
        'hotelId',
        'type_chambre',
        'nombre_personne',
        'date_debut',
        'date_fin',
    ];

    // Relation avec l'hôtel
    public function hotel()
    {
        return $this->belongsTo(Hotels::class, 'hotelId');
    }
}
