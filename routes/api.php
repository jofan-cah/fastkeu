<?php

use App\Http\Controllers\API\DocumentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('documents')->group(function () {
    // Preview nomor (GET)
    Route::get('preview', [DocumentApiController::class, 'previewNumbers']);

    // Generate documents (POST) - AUTO CREATE!
    Route::post('generate', [DocumentApiController::class, 'generateDocuments']);
     // ✅ Get latest all (simple)
    Route::get('latest-all', [DocumentApiController::class, 'getLatestAll']);

    // ✅ Complete documents dengan customer data
    Route::post('store', [DocumentApiController::class, 'store']);

    // Get document info (GET)
    Route::get('{doc_id}', [DocumentApiController::class, 'show']);

    // Upload file (POST)
    Route::post('{doc_id}/upload', [DocumentApiController::class, 'uploadFile']);
});
