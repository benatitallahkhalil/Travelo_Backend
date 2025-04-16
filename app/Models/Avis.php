<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{

    //
    use HasFactory;

    protected $fillable = ['commentaire', 'note', 'reservation_id'];

    // Relation avec la réservation
    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }
}
