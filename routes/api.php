<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgencyInvitationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
Route::middleware('auth:sanctum')->group(function () {

    // Send invitation
    Route::post('/invitations/send', [AgencyInvitationController::class, 'send']);

    // Accept invitation
    Route::post('/invitations/accept/{token}', [AgencyInvitationController::class, 'accept']);

    // List invitations
    Route::get('/invitations', [AgencyInvitationController::class, 'index']);

    // Delete invitation
    Route::delete('/invitations/{id}', [AgencyInvitationController::class, 'destroy']);

});

