<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgentApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Agent API Routes
Route::prefix('agent')->middleware('api.auth')->group(function () {
    Route::post('/search', [AgentApiController::class, 'searchFlights']);
    Route::post('/book', [AgentApiController::class, 'createBooking']);
    Route::post('/booking/{reference}/passengers', [AgentApiController::class, 'addPassengers']);
    Route::post('/booking/{reference}/issue', [AgentApiController::class, 'issueTicket']);
    Route::get('/booking/{reference}', [AgentApiController::class, 'getBooking']);
    Route::get('/balance', [AgentApiController::class, 'getBalance']);
    Route::get('/transactions', [AgentApiController::class, 'getTransactions']);
});
