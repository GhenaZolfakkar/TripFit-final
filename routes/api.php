<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Models\Notification;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\TripCategoryController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\SearchHistoryController;
use App\Http\Controllers\Api\AgencyRequestController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\InquiryController;

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
        Route::get('/list', [TripCategoryController::class, 'index']);

        Route::get('/{id}', [TripCategoryController::class, 'show']);
    });

});

Route::post('/chatbot', [ChatbotController::class, 'ask']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('search-history')->group(function () {

        Route::get('/list', [SearchHistoryController::class, 'index']);
        Route::post('/store', [SearchHistoryController::class, 'store']);
        Route::delete('/{id}', [SearchHistoryController::class, 'destroy']);
        Route::delete('/', [SearchHistoryController::class, 'clear']);

    });

});
Route::post('/agency-request', [AgencyRequestController::class, 'store']);

Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/faqs/{id}', [FaqController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/faqs', [FaqController::class, 'store']);
    Route::put('/faqs/{id}', [FaqController::class, 'update']);
    Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);
    
    });

 Route::post('/inquiries', [InquiryController::class, 'store']);
Route::get('/inquiries', [InquiryController::class, 'index']);
Route::get('/inquiries/{id}', [InquiryController::class, 'show']);
Route::patch('/inquiries/{id}', [InquiryController::class, 'update']);