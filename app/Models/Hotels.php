<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotels extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'urlImage',
        'description',
        'nbEtoiles'
    ];

    // Relation avec les offres
    public function offres()
    {
        return $this->hasMany(Offre::class, 'hotelId');
    }
}
