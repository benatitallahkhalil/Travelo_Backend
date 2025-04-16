<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\ReservationController;

Route::group([
'middleware' => 'api',
'prefix' => 'users'
], function ($router) {
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/refreshToken', [AuthController::class, 'refresh']);
Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
Route::group(['middleware' => 'api',
'prefix' => 'users'], function () {
    Route::get('/', [AuthController::class, 'listUsers']);              // Récupérer tous les utilisateurs
    Route::post('/', [AuthController::class, 'addUser']);               // Ajouter un utilisateur
    Route::get('/{id}', [AuthController::class, 'getUserById']);        // Récupérer un utilisateur par ID
    Route::put('/{id}', [AuthController::class, 'updateUser']);         // Modifier un utilisateur
    Route::delete('/{id}', [AuthController::class, 'deleteUser']);      // Supprimer un utilisateur
    Route::put('/{id}/role', [AuthController::class, 'updateUserRole']);
   

});


Route::get('users/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
// Routes pour Hotel
Route::get('/hotels', [HotelsController::class, 'getAll']);
Route::post('/hotels', [HotelsController::class, 'store']); // Ajouter un hôtel
Route::get('/hotels/{id}', [HotelsController::class, 'show']); // Récupérer un hôtel par ID
Route::put('/hotels/{id}', [HotelsController::class, 'update']); // Mettre à jour un hôtel
Route::delete('/hotels/{id}', [HotelsController::class, 'destroy']); // Supprimer un hôtel

// Routes pour Offre
Route::get('/offres', [OffreController::class, 'index']); // Récupérer toutes les offres
Route::post('/offres', [OffreController::class, 'store']); // Ajouter une offre
Route::get('/offres/{id}', [OffreController::class, 'show']); // Récupérer une offre par ID
Route::put('/offres/{id}', [OffreController::class, 'update']); // Mettre à jour une offre
Route::delete('/offres/{id}', [OffreController::class, 'destroy']); // Supprimer une offre
Route::get('/offres/search', [OffreController::class, 'search']);
Route::get('/offres/hotel/{hotelId}', [OffreController::class, 'getOffersByHotel']);


Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::put('/reservations/client/{id}', [ReservationController::class, 'updateClientReservation']);
Route::put('/reservations/admin/{id}', [ReservationController::class, 'updateAdminReservation']);
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

Route::apiResource('avis', AvisController::class);