<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/ai-models', function () {
    $response = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key=" . env('GEMINI_API_KEY'));
    return $response->json();
});
