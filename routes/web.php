<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\DocumentNumberHelper;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;

Route::get('/', function () {
    return view('welcome');
});

// Public Route - Validasi Dokumen (Tanpa Login)
Route::get('/validate/{docNumber}', [DocumentController::class, 'validateDocument'])->name('validateDocument');



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Documents (nanti)
    Route::prefix('documents')->middleware('check.permission:Documents,read')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('indexDocuments');
        Route::get('/create', [DocumentController::class, 'create'])
            ->middleware('check.permission:Documents,create')
            ->name('createDocuments');
        // ✅ Export Excel
        Route::get('/export', [DocumentController::class, 'export'])->name('exportDocuments');

        Route::post('/store', [DocumentController::class, 'store'])
            ->middleware('check.permission:Documents,create')
            ->name('storeDocuments');

        // 1. BA Kesepakatan
        Route::get('/create-ba-kesepakatan', [DocumentController::class, 'createBaKesepakatan'])
            ->middleware('check.permission:Documents,create')
            ->name('createBaKesepakatan');
        Route::post('/preview-ba-kesepakatan', [DocumentController::class, 'previewBaKesepakatan'])
            ->middleware('check.permission:Documents,create')
            ->name('previewBaKesepakatan');
        Route::post('/generate-ba-kesepakatan', [DocumentController::class, 'generateBaKesepakatan'])
            ->middleware('check.permission:Documents,create')
            ->name('generateBaKesepakatan');

        // 2. Surat Pengalaman Kerja (SKPK)
        Route::get('/create-skpk', [DocumentController::class, 'createSkpk'])
            ->middleware('check.permission:Documents,create')
            ->name('createSkpk');
        Route::post('/preview-skpk', [DocumentController::class, 'previewSkpk'])
            ->middleware('check.permission:Documents,create')
            ->name('previewSkpk');
        Route::post('/generate-skpk', [DocumentController::class, 'generateSkpk'])
            ->middleware('check.permission:Documents,create')
            ->name('generateSkpk');

        // 3. Surat PHK
        Route::get('/create-surat-phk', [DocumentController::class, 'createSuratPhk'])
            ->middleware('check.permission:Documents,create')
            ->name('createSuratPhk');
        Route::post('/preview-surat-phk', [DocumentController::class, 'previewSuratPhk'])
            ->middleware('check.permission:Documents,create')
            ->name('previewSuratPhk');
        Route::post('/generate-surat-phk', [DocumentController::class, 'generateSuratPhk'])
            ->middleware('check.permission:Documents,create')
            ->name('generateSuratPhk');

        // 4. BA Peminjaman Perangkat
        Route::get('/create-ba-peminjaman', [DocumentController::class, 'createBaPeminjaman'])
            ->middleware('check.permission:Documents,create')
            ->name('createBaPeminjaman');
        Route::post('/preview-ba-peminjaman', [DocumentController::class, 'previewBaPeminjaman'])
            ->middleware('check.permission:Documents,create')
            ->name('previewBaPeminjaman');
        Route::post('/generate-ba-peminjaman', [DocumentController::class, 'generateBaPeminjaman'])
            ->middleware('check.permission:Documents,create')
            ->name('generateBaPeminjaman');
        // ✅ Import Excel
        Route::get('/import', [DocumentController::class, 'importForm'])
            ->middleware('check.permission:Documents,create')
            ->name('importDocuments');
        Route::post('/import', [DocumentController::class, 'import'])
            ->middleware('check.permission:Documents,create')
            ->name('processImportDocuments');


        Route::get('/{id}', [DocumentController::class, 'show'])->name('showDocuments');
        Route::get('/{id}/edit', [DocumentController::class, 'edit'])
            ->middleware('check.permission:Documents,update')
            ->name('editDocuments');
        Route::put('/{id}', [DocumentController::class, 'update'])
            ->middleware('check.permission:Documents,update')
            ->name('updateDocuments');
        Route::delete('/{id}', [DocumentController::class, 'destroy'])
            ->middleware('check.permission:Documents,delete')
            ->name('destroyDocuments');
        Route::get('/{id}/download', [DocumentController::class, 'download'])->name('downloadDocuments');
        Route::post('/{id}/upload', [DocumentController::class, 'uploadFile'])
            ->middleware('check.permission:Documents,update')
            ->name('uploadFileDocuments');
    });



    // Document Types Routes
    Route::prefix('document-types')->middleware('check.permission:DocumentTypes,read')->group(function () {
        Route::get('/', [DocumentTypeController::class, 'index'])->name('indexDocumentTypes');
        Route::get('/create', [DocumentTypeController::class, 'create'])
            ->middleware('check.permission:DocumentTypes,create')
            ->name('createDocumentTypes');
        Route::post('/store', [DocumentTypeController::class, 'store'])
            ->middleware('check.permission:DocumentTypes,create')
            ->name('storeDocumentTypes');
        Route::get('/{doc_type_id}', [DocumentTypeController::class, 'show'])->name('showDocumentTypes');
        Route::get('/{doc_type_id}/edit', [DocumentTypeController::class, 'edit'])
            ->middleware('check.permission:DocumentTypes,update')
            ->name('editDocumentTypes');
        Route::put('/{doc_type_id}', [DocumentTypeController::class, 'update'])
            ->middleware('check.permission:DocumentTypes,update')
            ->name('updateDocumentTypes');
        Route::post('/{doc_type_id}/toggle-status', [DocumentTypeController::class, 'toggleStatus'])
            ->middleware('check.permission:DocumentTypes,update')
            ->name('toggleStatusDocumentTypes');
        Route::delete('/{doc_type_id}', [DocumentTypeController::class, 'destroy'])
            ->middleware('check.permission:DocumentTypes,delete')
            ->name('destroyDocumentTypes');
        Route::get('/{doc_type_id}/preview', [DocumentTypeController::class, 'previewNumber'])->name('previewDocumentTypes');
        Route::post('/{doc_type_id}/reset-counter', [DocumentTypeController::class, 'resetCounter'])
            ->middleware('check.permission:DocumentTypes,update')
            ->name('resetCounterDocumentTypes');
    });
});

Route::get('/test', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'app_timezone' => config('app.timezone'),
        'db_connection' => config('database.default'),
        'db_database' => config('database.connections.mysql.database'),
    ]);
});

Route::get('/test-helper', function () {

    // Preview next numbers
    $form = DocumentNumberHelper::previewNextNumber('form');
    $konf = DocumentNumberHelper::previewNextNumber('konf');
    $ba = DocumentNumberHelper::previewNextNumber('ba');

    return response()->json([
        'preview' => [
            'form' => $form,
            'konf' => $konf,
            'ba' => $ba,
        ]
    ]);
});
