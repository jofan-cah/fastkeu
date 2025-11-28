<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Helpers\DocumentNumberHelper;
use App\Helpers\DocumentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DocumentApiController extends Controller
{
    /**
     * Generate multiple documents untuk BEFAST
     *
     * POST /api/documents/generate
     * {
     *   "subscription_id": "SUB-20251124-001",
     *   "customer_name": "Agus Supratman",
     *   "codes": ["form", "konf", "ba"],
     *   "notes": "Auto-generated from BEFAST"
     * }
     */
    public function generateDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'codes' => 'required|array|min:1',
            'codes.*' => 'required|string|exists:document_types,code',
            'notes' => 'nullable|string',
        ], [
            'codes.required' => 'Document codes wajib diisi',
            'codes.*.exists' => 'Document code tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $documents = [];
            $now = Carbon::now();

            foreach ($request->codes as $code) {
                $docType = DocumentType::where('code', $code)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$docType) {
                    continue;
                }

                // Sync counter
                $syncedNumber = $this->syncCounterWithExistingDocuments($docType);
                $docType->current_number = $syncedNumber + 1;
                $sequence = $docType->current_number;

                // Get month & year
                $currentMonth = $this->getRomanMonth($now->month);
                $currentYear = $now->format('Y');

                // Update doc type
                $docType->current_month = $currentMonth;
                $docType->current_year = $currentYear;
                $docType->save();

                // Format document number
                if ($sequence < 1000) {
                    $docNumber = sprintf(
                        '%s.%03d/%s/%s/%s',
                        $docType->prefix,
                        $sequence,
                        $docType->format_code,
                        $currentMonth,
                        $currentYear
                    );
                } else {
                    $docNumber = sprintf(
                        '%s.%d/%s/%s/%s',
                        $docType->prefix,
                        $sequence,
                        $docType->format_code,
                        $currentMonth,
                        $currentYear
                    );
                }

                // Generate document ID
                $documentId = DocumentHelper::generateDocumentId($code);

                // Create document
                $document = Document::create([
                    'id' => $documentId,
                    'doc_number' => $docNumber,
                    'doc_type_id' => $docType->doc_type_id,
                    'subscription_id' => $request->subscription_id,
                    'customer_name' => $request->customer_name,
                    'document_date' => $now->toDateString(),
                    'status' => 'generated',
                    'notes' => $request->notes,
                    'created_by' => 'befast-api', // Marker dari API
                ]);

                $documents[$code] = [
                    'doc_id' => $document->id,
                    'doc_number' => $document->doc_number,
                    'doc_type_name' => $docType->name,
                    'status' => $document->status,
                    'created_at' => $document->created_at->toIso8601String(),
                ];

                Log::info("Document generated via API: {$document->doc_number}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Documents berhasil di-generate',
                'documents' => $documents
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error generating documents via API: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate documents: ' . $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'subscription_id' => 'required|string|max:255',
        'customer_name' => 'required|string|max:255',
        'document_date' => 'required|date',
        'notes' => 'nullable|string',
        'documents' => 'required|array|min:1',
        'documents.*.code' => 'required|string|exists:document_types,code',
        'documents.*.doc_number' => 'required|string',
    ], [
        'subscription_id.required' => 'Subscription ID wajib diisi',
        'customer_name.required' => 'Customer name wajib diisi',
        'document_date.required' => 'Tanggal dokumen wajib diisi',
        'documents.required' => 'Documents wajib diisi',
        'documents.*.code.required' => 'Document code wajib diisi',
        'documents.*.code.exists' => 'Document code tidak valid',
        'documents.*.doc_number.required' => 'Document number wajib diisi',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $savedDocuments = [];
        $errors = [];

        // Loop setiap document
        foreach ($request->documents as $docData) {
            try {
                $code = $docData['code'];
                $docNumber = $docData['doc_number'];

                // Get document type
                $documentType = DocumentType::where('code', $code)
                    ->where('is_active', true)
                    ->first();

                if (!$documentType) {
                    $errors[] = "Document type '{$code}' tidak ditemukan atau tidak aktif";
                    continue;
                }

                // ✅ CEK: Kalau udah ada document dengan nomor yang sama, UPDATE
                $existingDoc = Document::where('doc_number', $docNumber)->first();

                if ($existingDoc) {
                    // Update existing
                    $existingDoc->update([
                        'subscription_id' => $request->subscription_id,
                        'customer_name' => $request->customer_name,
                        'document_date' => $request->document_date,
                        'notes' => $request->notes,
                        'status' => 'printed',
                        'updated_by' => 'befast-api',
                    ]);

                    $savedDocuments[$code] = [
                        'doc_id' => $existingDoc->id,
                        'doc_number' => $existingDoc->doc_number,
                        'doc_type_name' => $documentType->name,
                        'status' => $existingDoc->status,
                        'action' => 'updated',
                    ];

                    \Log::info("Document updated: {$existingDoc->doc_number}");
                } else {
                    // ✅ BUAT BARU: Langsung pakai nomor yang dikasih

                    // Generate document ID
                    $documentId = DocumentHelper::generateDocumentId($code);

                    // Create document
                    $document = Document::create([
                        'id' => $documentId,
                        'doc_number' => $docNumber,
                        'doc_type_id' => $documentType->doc_type_id,
                        'subscription_id' => $request->subscription_id,
                        'customer_name' => $request->customer_name,
                        'document_date' => $request->document_date,
                        'status' => 'generated',
                        'notes' => $request->notes,
                        'created_by' => 'befast-api',
                    ]);

                    // Update counter di document_type (biar sync)
                    $this->updateCounterFromDocNumber($documentType, $docNumber);

                    $savedDocuments[$code] = [
                        'doc_id' => $document->id,
                        'doc_number' => $document->doc_number,
                        'doc_type_name' => $documentType->name,
                        'status' => $document->status,
                        'action' => 'created',
                    ];

                    Log::info("Document created: {$document->doc_number}");
                }

            } catch (\Exception $e) {
                $errors[] = "Error saving document '{$docData['code']}': " . $e->getMessage();
                Log::error("Error saving document {$docData['code']}: " . $e->getMessage());
            }
        }

        // Cek apakah ada yang berhasil disimpan
        if (empty($savedDocuments)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan semua documents',
                'errors' => $errors
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => count($savedDocuments) . ' document(s) berhasil disimpan',
            'data' => $savedDocuments,
            'errors' => $errors // Kalau ada error partial
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error storing documents: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan documents: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Update counter dari doc_number yang dikasih
 * Biar counter di document_type tetap sync
 */
private function updateCounterFromDocNumber($docType, $docNumber)
{
    // Extract number dari doc_number
    // Format: 22.016/F1-FB/XI/2025 → ambil 016
    $pattern = "/^" . preg_quote($docType->prefix, '/') . "\.(\d+)\//";

    if (preg_match($pattern, $docNumber, $matches)) {
        $number = (int) $matches[1];

        // Update counter kalau number lebih besar dari current
        if ($number > $docType->current_number) {
            $docType->current_number = $number;

            // Extract month & year dari doc_number
            // Format: 22.016/F1-FB/XI/2025 → XI/2025
            if (preg_match('/\/([IVX]+)\/(\d{4})$/', $docNumber, $dateMatches)) {
                $docType->current_month = $dateMatches[1];
                $docType->current_year = $dateMatches[2];
            }

            $docType->save();

            \Log::info("Counter updated for doc_type {$docType->code}: {$number}");
        }
    }
}

    /**
     * Complete/Update documents dengan data customer
     * Dipanggil setelah subscription saved di BEFAST
     *
     * POST /api/documents/complete
     * {
     *   "subscription_id": "SUB-20251124-001",
     *   "customer_name": "Agus Supratman",
     *   "no_form": "22.016/F1-FB/XI/2025",
     *   "no_konf": "23.003/F1-SC/XI/2025",
     *   "no_ba": "24.007/F1-BA/XI/2025",
     *   "tanggal_aktivasi": "2025-11-24"
     * }
     */
    public function completeDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'no_form' => 'required|string',
            'no_konf' => 'required|string',
            'no_ba' => 'required|string',
            'tanggal_aktivasi' => 'nullable|date',
        ], [
            'subscription_id.required' => 'Subscription ID wajib diisi',
            'customer_name.required' => 'Customer name wajib diisi',
            'no_form.required' => 'No Form wajib diisi',
            'no_konf.required' => 'No Konfirmasi wajib diisi',
            'no_ba.required' => 'No BA wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = [];
            $notFound = [];

            // Map nomor dokumen ke doc number
            $documents = [
                'form' => $request->no_form,
                'konf' => $request->no_konf,
                'ba' => $request->no_ba,
            ];

            foreach ($documents as $code => $docNumber) {
                // Cari document berdasarkan doc_number
                $document = Document::where('doc_number', $docNumber)->first();

                if ($document) {
                    // ✅ Update document dengan data lengkap
                    $document->update([
                        'subscription_id' => $request->subscription_id,
                        'customer_name' => $request->customer_name,
                        'document_date' => $request->tanggal_aktivasi ?? now(),
                        'status' => 'printed', // Status berubah jadi printed
                        'notes' => 'Completed from BEFAST subscription',
                        'updated_by' => 'befast-api',
                    ]);

                    $updated[$code] = [
                        'doc_id' => $document->id,
                        'doc_number' => $document->doc_number,
                        'status' => $document->status,
                    ];

                    Log::info("Document completed: {$document->doc_number}", [
                        'subscription_id' => $request->subscription_id,
                        'customer_name' => $request->customer_name,
                    ]);
                } else {
                    $notFound[] = $docNumber;
                    Log::warning("Document not found: {$docNumber}");
                }
            }

            if (!empty($notFound)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some documents not found',
                    'not_found' => $notFound
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Documents berhasil di-complete',
                'updated' => $updated
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing documents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal complete documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview next document numbers (tanpa save)
     *
     * GET /api/documents/preview?codes[]=form&codes[]=konf&codes[]=ba
     */
    public function previewNumbers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codes' => 'required|array|min:1',
            'codes.*' => 'required|string|exists:document_types,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $previews = [];
            $now = Carbon::now();
            $currentMonth = $this->getRomanMonth($now->month);
            $currentYear = $now->format('Y');

            foreach ($request->codes as $code) {
                $docType = DocumentType::where('code', $code)
                    ->where('is_active', true)
                    ->first();

                if (!$docType) {
                    continue;
                }

                $syncedNumber = $this->syncCounterWithExistingDocuments($docType);
                $nextNumber = $syncedNumber + 1;

                if ($nextNumber < 1000) {
                    $preview = sprintf(
                        '%s.%03d/%s/%s/%s',
                        $docType->prefix,
                        $nextNumber,
                        $docType->format_code,
                        $currentMonth,
                        $currentYear
                    );
                } else {
                    $preview = sprintf(
                        '%s.%d/%s/%s/%s',
                        $docType->prefix,
                        $nextNumber,
                        $docType->format_code,
                        $currentMonth,
                        $currentYear
                    );
                }

                $previews[$code] = $preview;
            }

            return response()->json([
                'success' => true,
                'previews' => $previews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal preview numbers'
            ], 500);
        }
    }




    /**
     * Get latest numbers for all types (simple)
     *
     * GET /api/documents/latest-all
     */
    /**
     * Get next available numbers for all types (latest + 1)
     *
     * GET /api/documents/latest-all
     *
     * Dipakai di:
     * 1. BEFAST - untuk tampilkan nomor di PDF form
     * 2. FASTKEU - untuk preview sebelum create document
     */
    public function getLatestAll()
    {
        try {
            $codes = ['form', 'konf', 'ba'];
            $result = [];
            $now = Carbon::now();
            $currentMonth = $this->getRomanMonth($now->month);
            $currentYear = $now->format('Y');

            foreach ($codes as $code) {
                $docType = DocumentType::where('code', $code)
                    ->where('is_active', true)
                    ->first();

                if (!$docType) {
                    $result[$code] = null;
                    continue;
                }

                // Sync counter dengan data existing
                $syncedNumber = $this->syncCounterWithExistingDocuments($docType);

                // ✅ NEXT NUMBER = latest + 1
                $nextNumber = $syncedNumber + 1;

                // Format nomor dokumen
                if ($nextNumber < 1000) {
                    $docNumber = sprintf(
                        '%s.%03d/%s/%s/%s',
                        $docType->prefix,
                        $nextNumber,
                        $docType->format_code,
                        $currentMonth,
                        $currentYear
                    );
                } else {
                    $docNumber = sprintf(
                        '%s.%d/%s/%s/%s',
                        $docType->prefix,
                        $nextNumber,
                        $docType->format_code,
                        $currentMonth,
                        $currentYear
                    );
                }

                $result[$code] = $docNumber;
            }

            return response()->json([
                'success' => true,
                'latest' => [
                    'form' => $result['form'],
                    'konf' => $result['konf'],
                    'ba' => $result['ba'],
                ],
                'note' => 'These are NEXT available numbers (latest + 1)'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting latest all: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving latest numbers'
            ], 500);
        }
    }

    /**
     * Upload PDF file untuk document yang sudah di-generate
     *
     * POST /api/documents/{doc_id}/upload
     */
    public function uploadFile(Request $request, $doc_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $document = Document::findOrFail($doc_id);

            // Upload file
            $file = $request->file('file');
            $fileName = str_replace(['/', '.'], '-', $document->id) . '.pdf';
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $document->update([
                'file_path' => $filePath,
                'status' => 'printed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'file_url' => url('storage/' . $filePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file'
            ], 500);
        }
    }

    /**
     * Get document info
     *
     * GET /api/documents/{doc_id}
     */
    public function show($doc_id)
    {
        try {
            $document = Document::with('documentType')->findOrFail($doc_id);

            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'doc_number' => $document->doc_number,
                    'doc_type' => $document->documentType->name,
                    'subscription_id' => $document->subscription_id,
                    'customer_name' => $document->customer_name,
                    'document_date' => $document->document_date->format('Y-m-d'),
                    'status' => $document->status,
                    'file_url' => $document->file_path ? url('storage/' . $document->file_path) : null,
                    'created_at' => $document->created_at->toIso8601String(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }
    }

    // Helper methods
    private function syncCounterWithExistingDocuments($docType)
    {
        $pattern = "/^" . preg_quote($docType->prefix, '/') . "\.(\d+)\/" . preg_quote($docType->format_code, '/') . "\//";
        $documents = Document::where('doc_type_id', $docType->doc_type_id)->get();

        if ($documents->isEmpty()) {
            return 0;
        }

        $numbers = [];
        foreach ($documents as $doc) {
            if (preg_match($pattern, $doc->doc_number, $matches)) {
                $numbers[] = (int) $matches[1];
            }
        }

        return empty($numbers) ? 0 : max($numbers);
    }

    private function getRomanMonth($month)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romans[$month] ?? 'I';
    }
}
