<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes publiques (non protégées)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Routes protégées avec Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Gestion des parkings
    Route::apiResource('/parkings', ParkingController::class);

    // Gestion des réservations
    Route::apiResource('/reservations', ReservationController::class);
    Route::put('/reservation/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservation/{id}', [ReservationController::class, 'destroy']);
    Route::get('/reservations/{user_id}', [ReservationController::class, 'getUserReservations']);



    // Authentification & profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Récupérer l'utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });
});
