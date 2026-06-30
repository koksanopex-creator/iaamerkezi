<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Customer Sync from koksan-takvim
Route::post('/customers/sync', [App\Http\Controllers\Api\CustomerSyncController::class, 'sync']);
Route::post('/customers/bulk-sync', [App\Http\Controllers\Api\CustomerSyncController::class, 'bulkSync']);

// User Sync from Merkezi API
Route::post('/users/sync-from-merkezi', [App\Http\Controllers\Api\UserSyncController::class, 'syncFromMerkezi']);
