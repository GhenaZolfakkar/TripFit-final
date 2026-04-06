<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgencyInvitationController;
use App\Http\Controllers\Api\NotificationController;
use App\Models\Notification;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\TripCategoryController;

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

    Route::get('/notifications', function () {
        return auth()->user()->notifications()->latest()->get();
    });

    Route::post('/notifications/{id}/read', function ($id) {
        $notification = Notification::findOrFail($id);

        $notification->update([
            'is_read' => true
        ]);

        return response()->json(['message' => 'Notification marked as read']);
    }); 
 });
Route::middleware('auth:sanctum')->group(function () {

    // TRIPS
    Route::prefix('trips')->group(function () {
        Route::get('/list', [TripController::class, 'index']);
        Route::get('/{id}', [TripController::class, 'show']);
        Route::post('/store', [TripController::class, 'store']);
        Route::put('/{id}', [TripController::class, 'update']);
        Route::delete('/{id}', [TripController::class, 'destroy']);
    });

});
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('trip-categories')->group(function () {
        // 📄 Get all categories
        Route::get('/list', [TripCategoryController::class, 'index']);

        // 🔍 Get category by ID
        Route::get('/{id}', [TripCategoryController::class, 'show']);
    });

});


