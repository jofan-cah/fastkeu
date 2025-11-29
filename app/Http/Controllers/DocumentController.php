<?php

namespace App\Http\Controllers;

use App\Exports\DocumentsExport;
use App\Models\Document;
use App\Models\DocumentType;
use App\Helpers\DocumentNumberHelper;
use App\Helpers\DocumentHelper;
use App\Imports\DocumentsImport;
use Barryvdh\DomPDF\Facade\Pdf;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents
     */
    public function index(Request $request)
    {
        $query = Document::with(['documentType', 'creator']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('subscription_id', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by document type
        if ($request->has('doc_type') && $request->doc_type != '') {
            $query->where('doc_type_id', $request->doc_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('document_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('document_date', '<=', $request->date_to);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get document types for filter
        $documentTypes = DocumentType::where('is_active', true)->get();

        return view('documents.indexDocuments', compact('documents', 'documentTypes'));
    }

    /**
     * Show the form for creating a new document
     */
    public function create()
    {
        $documentTypes = DocumentType::where('is_active', true)->get();
        return view('documents.createDocuments', compact('documentTypes'));
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doc_type_id' => 'required|exists:document_types,doc_type_id',
            'subscription_id' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'document_date' => 'required|date',
            'status' => 'required|in:generated,printed,signed,cancelled',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
        ], [
            'doc_type_id.required' => 'Document type wajib dipilih',
            'doc_type_id.exists' => 'Document type tidak valid',
            'document_date.required' => 'Tanggal dokumen wajib diisi',
            'status.required' => 'Status wajib dipilih',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $documentType = DocumentType::findOrFail($request->doc_type_id);

            // Generate document ID
            $documentId = DocumentHelper::generateDocumentId($documentType->code);

            // Generate document number
            $now = Carbon::parse($request->document_date);
            $currentMonth = $this->getRomanMonth($now->month);
            $currentYear = $now->format('Y');

            // Sync counter dengan data existing
            $syncedNumber = $this->syncCounterWithExistingDocuments($documentType);
            $documentType->current_number = $syncedNumber + 1;
            $sequence = $documentType->current_number;

            // Update bulan/tahun
            $documentType->current_month = $currentMonth;
            $documentType->current_year = $currentYear;
            $documentType->save();

            // Format nomor dokumen
            if ($sequence < 1000) {
                $docNumber = sprintf(
                    '%s.%03d/%s/%s/%s',
                    $documentType->prefix,
                    $sequence,
                    $documentType->format_code,
                    $currentMonth,
                    $currentYear
                );
            } else {
                $docNumber = sprintf(
                    '%s.%d/%s/%s/%s',
                    $documentType->prefix,
                    $sequence,
                    $documentType->format_code,
                    $currentMonth,
                    $currentYear
                );
            }

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = str_replace(['/', '.'], '-', $documentId) . '.pdf';
                $filePath = $file->storeAs('documents', $fileName, 'public');
            }

            // Create document
            $document = Document::create([
                'id' => $documentId,
                'doc_number' => $docNumber,
                'doc_type_id' => $request->doc_type_id,
                'subscription_id' => $request->subscription_id,
                'customer_name' => $request->customer_name,
                'document_date' => $request->document_date,
                'status' => $request->status,
                'file_path' => $filePath,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            logActivity('create', Auth::user()->full_name . ' created document: ' . $document->doc_number);

            return response()->json([
                'success' => true,
                'message' => 'Document berhasil dibuat',
                'data' => $document
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat document: ' . $e->getMessage()
            ], 500);
        }
    }


    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $docTypeId = $request->doc_type_id;

        $fileName = 'Documents_Export_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new DocumentsExport($startDate, $endDate, $docTypeId),
            $fileName
        );
    }

    /**
     * Display the specified document
     */
    public function show($id)
    {
        $document = Document::with(['documentType', 'creator', 'updater'])->findOrFail($id);
        return view('documents.showDocuments', compact('document'));
    }

    /**
     * Show the form for editing the document
     */
    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $documentTypes = DocumentType::where('is_active', true)->get();
        return view('documents.editDocuments', compact('document', 'documentTypes'));
    }

    /**
     * Update the specified document
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'subscription_id' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'document_date' => 'required|date',
            'status' => 'required|in:generated,printed,signed,cancelled',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'document_date.required' => 'Tanggal dokumen wajib diisi',
            'status.required' => 'Status wajib dipilih',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $file = $request->file('file');
                $fileName = str_replace(['/', '.'], '-', $document->id) . '.pdf';
                $filePath = $file->storeAs('documents', $fileName, 'public');
                $document->file_path = $filePath;
            }

            $document->update([
                'subscription_id' => $request->subscription_id,
                'customer_name' => $request->customer_name,
                'document_date' => $request->document_date,
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            logActivity('update', Auth::user()->full_name . ' updated document: ' . $document->doc_number);

            return response()->json([
                'success' => true,
                'message' => 'Document berhasil diupdate',
                'data' => $document
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified document (soft delete)
     */
    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);

            $docNumber = $document->doc_number;
            $document->delete();

            logActivity('delete', Auth::user()->full_name . ' deleted document: ' . $docNumber);

            return response()->json([
                'success' => true,
                'message' => 'Document berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus document'
            ], 500);
        }
    }

    /**
     * Download document file
     */
    public function download($id)
    {
        try {
            $document = Document::findOrFail($id);

            if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $fileName = str_replace(['/', '.'], '-', $document->doc_number) . '.pdf';

            return Storage::disk('public')->download($document->file_path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal download file'
            ], 500);
        }
    }

    /**
     * Upload file to existing document
     */
    public function uploadFile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf|max:5120',
        ], [
            'file.required' => 'File wajib dipilih',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $document = Document::findOrFail($id);

            // Delete old file
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Upload new file
            $file = $request->file('file');
            $fileName = str_replace(['/', '.'], '-', $document->id) . '.pdf';
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $document->update([
                'file_path' => $filePath,
                'status' => 'printed',
                'updated_by' => Auth::id(),
            ]);

            logActivity('update', Auth::user()->full_name . ' uploaded file for document: ' . $document->doc_number);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'file_url' => Storage::disk('public')->url($filePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file'
            ], 500);
        }
    }

    /**
     * Sync counter dengan data existing
     */
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

    /**
     * Convert month to Roman numeral
     */
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


    public function importForm()
    {
        return view('documents.import');
    }

    /**
     * Process import Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv', // 5MB max
        ], [
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls, csv)',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');

            // Import
            $import = new DocumentsImport();
            Excel::import($import, $file);

            // Get stats
            $stats = $import->getStats();

            return response()->json([
                'success' => true,
                'message' => 'Import berhasil!',
                'stats' => [
                    'imported' => $stats['imported'],
                    'updated' => $stats['updated'],
                    'total' => $stats['imported'] + $stats['updated'],
                    'errors' => count($stats['errors']),
                ],
                'errors' => $stats['errors'],
            ]);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal import: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show form create BA Kesepakatan
     */
    public function createBaKesepakatan()
    {
        return view('documents.create-ba-kesepakatan');
    }

    /**
     * Generate & Download BA Kesepakatan PDF
     */
    public function generateBaKesepakatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'customer_id' => 'required|string',
            'customer_phone' => 'required|string',
            'bandwidth_awal_jenis' => 'required|string',
            'bandwidth_awal_kapasitas' => 'required|string',
            'bandwidth_awal_biaya' => 'required|numeric',
            'bandwidth_sekarang_jenis' => 'required|string',
            'bandwidth_sekarang_kapasitas' => 'required|string',
            'bandwidth_sekarang_biaya' => 'required|numeric',
            'starting_billing' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate document number
            $docType = DocumentType::where('code', 'baper')->first();

            if (!$docType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document type BA Kesepakatan tidak ditemukan'
                ], 404);
            }

            // Generate nomor
            $syncedNumber = $this->syncCounterWithExistingDocuments($docType);
            $docType->current_number = $syncedNumber + 1;
            $sequence = $docType->current_number;

            $now = Carbon::now();
            $currentMonth = $this->getRomanMonth($now->month);
            $currentYear = $now->format('Y');

            $docType->current_month = $currentMonth;
            $docType->current_year = $currentYear;
            $docType->save();

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

            // Generate QR Code URL untuk validasi
            $validationUrl = url('/validate/' . urlencode($docNumber));
            $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($validationUrl));

            // Prepare data untuk PDF
            $data = [
                'docNumber' => $docNumber,
                'hariTanggal' => Carbon::parse($request->starting_billing)->translatedFormat('l, d F Y'),
                'qrCode' => $qrCode,
                'pihakPertama' => [
                    'nama' => 'Ayu Mutiara A.',
                    'jabatan' => 'Administrasi',
                    'telepon' => '0815-6464-2022',
                    'ttd_path' => 'images/ttd_ayum.png',
                ],
                'pihakKedua' => [
                    'nama' => $request->customer_name,
                    'id_pelanggan' => $request->customer_id,
                    'telepon' => $request->customer_phone,
                ],
                'bandwidthAwal' => [
                    'jenis_layanan' => $request->bandwidth_awal_jenis,
                    'kapasitas' => $request->bandwidth_awal_kapasitas,
                    'biaya' => $request->bandwidth_awal_biaya,
                    'ppn' => $request->bandwidth_awal_biaya * 0.11,
                    'total' => $request->bandwidth_awal_biaya * 1.11,
                ],
                'bandwidthSekarang' => [
                    'jenis_layanan' => $request->bandwidth_sekarang_jenis,
                    'kapasitas' => $request->bandwidth_sekarang_kapasitas,
                    'biaya' => $request->bandwidth_sekarang_biaya,
                    'ppn' => $request->bandwidth_sekarang_biaya * 0.11,
                    'total' => $request->bandwidth_sekarang_biaya * 1.11,
                ],
                'syaratKondisi' => [
                    '24/7 Support & Network Monitoring',
                    'Starting billing ' . Carbon::parse($request->starting_billing)->translatedFormat('d F Y'),
                    'Downgrade Minimal setelah 3 bulan',
                ],
            ];

            // Generate PDF
            $pdf = Pdf::loadView('documents.templates.ba-kesepakatan', $data);
            $pdf->setPaper('a4', 'portrait');

            // Save document to database
            $documentId = DocumentHelper::generateDocumentId('ba-kesepakatan');

            $document = Document::create([
                'id' => $documentId,
                'doc_number' => $docNumber,
                'doc_type_id' => $docType->doc_type_id,
                'subscription_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'document_date' => $request->starting_billing,
                'status' => 'generated',
                'notes' => 'BA Kesepakatan Perubahan Layanan',
                'created_by' => Auth::id(),
            ]);

            // Return PDF download
            $fileName = str_replace(['/', '.'], '-', $docNumber) . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating BA Kesepakatan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewBaKesepakatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'customer_id' => 'required|string',
            'customer_phone' => 'required|string',
            'bandwidth_awal_jenis' => 'required|string',
            'bandwidth_awal_kapasitas' => 'required|string',
            'bandwidth_awal_biaya' => 'required|numeric',
            'bandwidth_sekarang_jenis' => 'required|string',
            'bandwidth_sekarang_kapasitas' => 'required|string',
            'bandwidth_sekarang_biaya' => 'required|numeric',
            'starting_billing' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prepare data (sama seperti generate, tapi gak save)
            $data = [
                'docNumber' => '31.XXX/FO-BA/XI/2025', // Preview nomor
                'hariTanggal' => Carbon::parse($request->starting_billing)->translatedFormat('l, d F Y'),
                'pihakPertama' => [
                    'nama' => 'Ayu Mutiara A.',
                    'jabatan' => 'Administrasi',
                    'telepon' => '0815-6464-2022',
                    'ttd_path' => 'images/ttd_ayum.png',
                ],
                'pihakKedua' => [
                    'nama' => $request->customer_name,
                    'id_pelanggan' => $request->customer_id,
                    'telepon' => $request->customer_phone,
                ],
                'bandwidthAwal' => [
                    'jenis_layanan' => $request->bandwidth_awal_jenis,
                    'kapasitas' => $request->bandwidth_awal_kapasitas,
                    'biaya' => $request->bandwidth_awal_biaya,
                    'ppn' => $request->bandwidth_awal_biaya * 0.11,
                    'total' => $request->bandwidth_awal_biaya * 1.11,
                ],
                'bandwidthSekarang' => [
                    'jenis_layanan' => $request->bandwidth_sekarang_jenis,
                    'kapasitas' => $request->bandwidth_sekarang_kapasitas,
                    'biaya' => $request->bandwidth_sekarang_biaya,
                    'ppn' => $request->bandwidth_sekarang_biaya * 0.11,
                    'total' => $request->bandwidth_sekarang_biaya * 1.11,
                ],
                'syaratKondisi' => [
                    '24/7 Support & Network Monitoring',
                    'Starting billing ' . Carbon::parse($request->starting_billing)->translatedFormat('d F Y'),
                    'Downgrade Minimal setelah 3 bulan',
                ],
            ];

            // Return HTML (bukan PDF)
            $html = view('documents.templates.ba-kesepakatan', $data)->render();

            return response($html, 200)
                ->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            Log::error('Error preview BA Kesepakatan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal preview: ' . $e->getMessage()
            ], 500);
        }
    }
    public function createSkpk()
    {
        return view('documents.create-skpk');
    }

    /**
     * Preview SKPK
     */
    public function previewSkpk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'job_description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'docNumber' => '10.XXX/SKPK-F1/XI/2025', // Preview
                'employee_name' => $request->employee_name,
                'position' => $request->position,
                'department' => $request->department,
                'start_date' => Carbon::parse($request->start_date),
                'end_date' => Carbon::parse($request->end_date),
                'job_description' => $request->job_description,
                'generated_date' => Carbon::now(),
            ];

            $html = view('documents.templates.skpk', $data)->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            Log::error('Error preview SKPK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate SKPK PDF
     */
    public function generateSkpk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'job_description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate document number
            $docType = DocumentType::where('code', 'skpk')->first();

            if (!$docType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document type SKPK tidak ditemukan'
                ], 404);
            }

            // Generate nomor
            $docNumber = $this->generateDocumentNumber($docType);

            // Generate QR Code URL untuk validasi
            $validationUrl = url('/validate/' . urlencode($docNumber));
            $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($validationUrl));

            // Prepare data
            $data = [
                'docNumber' => $docNumber,
                'qrCode' => $qrCode,
                'employee_name' => $request->employee_name,
                'position' => $request->position,
                'department' => $request->department,
                'start_date' => Carbon::parse($request->start_date),
                'end_date' => Carbon::parse($request->end_date),
                'job_description' => $request->job_description,
                'generated_date' => Carbon::now(),
            ];

            // Generate PDF
            $pdf = PDF::loadView('documents.templates.skpk', $data);
            $pdf->setPaper('a4', 'portrait');

            // Save to database
            $documentId = DocumentHelper::generateDocumentId('skpk');
            Document::create([
                'id' => $documentId,
                'doc_number' => $docNumber,
                'doc_type_id' => $docType->doc_type_id,
                'customer_name' => $request->employee_name,
                'document_date' => now(),
                'status' => 'generated',
                'notes' => 'Surat Pengalaman Kerja - ' . $request->position,
                'created_by' => Auth::id(),
            ]);

            $fileName = str_replace(['/', '.'], '-', $docNumber) . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating SKPK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // 2. SURAT PHK
    // ============================================

    /**
     * Show form create Surat PHK
     */
    public function createSuratPhk()
    {
        return view('documents.create-surat-phk');
    }

    /**
     * Preview Surat PHK
     */
    public function previewSuratPhk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'director_name' => 'required|string',
            'director_phone' => 'required|string',
            'director_email' => 'required|string|email',
            'employees' => 'required|array|min:1',
            'employees.*.name' => 'required|string',
            'employees.*.noka' => 'required|string',
            'employees.*.phone' => 'required|string',
            'employees.*.reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'docNumber' => '20.XXX/F1-SP/XI/2025', // Preview
                'director_name' => $request->director_name,
                'director_phone' => $request->director_phone,
                'director_email' => $request->director_email,
                'employees' => $request->employees,
                'generated_date' => Carbon::now(),
            ];

            $html = view('documents.templates.surat-phk', $data)->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            Log::error('Error preview Surat PHK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Surat PHK PDF
     */
    public function generateSuratPhk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'director_name' => 'required|string',
            'director_phone' => 'required|string',
            'director_email' => 'required|string|email',
            'employees' => 'required|array|min:1',
            'employees.*.name' => 'required|string',
            'employees.*.noka' => 'required|string',
            'employees.*.phone' => 'required|string',
            'employees.*.reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate document number
            $docType = DocumentType::where('code', 'phk')->first();

            if (!$docType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document type PHK tidak ditemukan'
                ], 404);
            }

            $docNumber = $this->generateDocumentNumber($docType);

            // Generate QR Code URL untuk validasi
            $validationUrl = url('/validate/' . urlencode($docNumber));
            $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($validationUrl));

            // Prepare data
            $data = [
                'docNumber' => $docNumber,
                'qrCode' => $qrCode,
                'director_name' => $request->director_name,
                'director_phone' => $request->director_phone,
                'director_email' => $request->director_email,
                'employees' => $request->employees,
                'generated_date' => Carbon::now(),
            ];

            // Generate PDF
            $pdf = PDF::loadView('documents.templates.surat-phk', $data);
            $pdf->setPaper('a4', 'portrait');

            // Save to database
            $documentId = DocumentHelper::generateDocumentId('phk');
            Document::create([
                'id' => $documentId,
                'doc_number' => $docNumber,
                'doc_type_id' => $docType->doc_type_id,
                'customer_name' => count($request->employees) . ' Karyawan',
                'document_date' => now(),
                'status' => 'generated',
                'notes' => 'Surat Pernyataan PHK - ' . count($request->employees) . ' karyawan',
                'created_by' => Auth::id(),
            ]);

            $fileName = str_replace(['/', '.'], '-', $docNumber) . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating Surat PHK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // 3. BA PEMINJAMAN PERANGKAT
    // ============================================

    /**
     * Show form create BA Peminjaman
     */
    public function createBaPeminjaman()
    {
        return view('documents.create-ba-peminjaman');
    }

    /**
     * Preview BA Peminjaman
     */
    public function previewBaPeminjaman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_name' => 'required|string',
            'borrower_business' => 'required|string',
            'borrower_id' => 'required|string',
            'borrower_phone' => 'required|string',
            'borrower_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer',
            'loan_terms' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'docNumber' => '14.XXX/F1-BAPP/XI/2025', // Preview
                'borrower_name' => $request->borrower_name,
                'borrower_business' => $request->borrower_business,
                'borrower_id' => $request->borrower_id,
                'borrower_phone' => $request->borrower_phone,
                'borrower_address' => $request->borrower_address,
                'items' => $request->items,
                'loan_terms' => $request->loan_terms,
                'generated_date' => Carbon::now(),
            ];

            $html = view('documents.templates.ba-peminjaman', $data)->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            Log::error('Error preview BA Peminjaman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate BA Peminjaman PDF
     */
    public function generateBaPeminjaman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_name' => 'required|string',
            'borrower_business' => 'required|string',
            'borrower_id' => 'required|string',
            'borrower_phone' => 'required|string',
            'borrower_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer',
            'loan_terms' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate document number
            $docType = DocumentType::where('code', 'pemi')->first();

            if (!$docType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document type Peminjaman tidak ditemukan'
                ], 404);
            }

            $docNumber = $this->generateDocumentNumber($docType);

            // Generate QR Code URL untuk validasi
            $validationUrl = url('/validate/' . urlencode($docNumber));
            $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($validationUrl));

            // Prepare data
            $data = [
                'docNumber' => $docNumber,
                'qrCode' => $qrCode,
                'borrower_name' => $request->borrower_name,
                'borrower_business' => $request->borrower_business,
                'borrower_id' => $request->borrower_id,
                'borrower_phone' => $request->borrower_phone,
                'borrower_address' => $request->borrower_address,
                'items' => $request->items,
                'loan_terms' => $request->loan_terms,
                'generated_date' => Carbon::now(),
            ];

            // Generate PDF
            $pdf = PDF::loadView('documents.templates.ba-peminjaman', $data);
            $pdf->setPaper('a4', 'portrait');

            // Save to database
            $documentId = DocumentHelper::generateDocumentId('pemi');
            Document::create([
                'id' => $documentId,
                'doc_number' => $docNumber,
                'doc_type_id' => $docType->doc_type_id,
                'customer_name' => $request->borrower_name,
                'document_date' => now(),
                'status' => 'generated',
                'notes' => 'BA Peminjaman Perangkat - ' . count($request->items) . ' items',
                'created_by' => Auth::id(),
            ]);

            $fileName = str_replace(['/', '.'], '-', $docNumber) . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating BA Peminjaman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateDocumentNumber($docType)
    {
        $syncedNumber = $this->syncCounterWithExistingDocuments($docType);
        $docType->current_number = $syncedNumber + 1;
        $sequence = $docType->current_number;

        $now = Carbon::now();
        $currentMonth = $this->getRomanMonth($now->month);
        $currentYear = $now->format('Y');

        $docType->current_month = $currentMonth;
        $docType->current_year = $currentYear;
        $docType->save();

        if ($sequence < 1000) {
            return sprintf(
                '%s.%03d/%s/%s/%s',
                $docType->prefix,
                $sequence,
                $docType->format_code,
                $currentMonth,
                $currentYear
            );
        } else {
            return sprintf(
                '%s.%d/%s/%s/%s',
                $docType->prefix,
                $sequence,
                $docType->format_code,
                $currentMonth,
                $currentYear
            );
        }
    }

    /**
     * Public: Validasi Dokumen via QR Code
     */
    public function validateDocument($docNumber)
    {
        try {
            // Decode jika nomor dokumen di-encode
            $docNumber = urldecode($docNumber);

            // Cari dokumen berdasarkan nomor
            $document = Document::where('doc_number', $docNumber)
                ->with(['documentType', 'creator'])
                ->first();

            if (!$document) {
                return view('documents.validate', [
                    'valid' => false,
                    'message' => 'Dokumen tidak ditemukan',
                    'docNumber' => $docNumber
                ]);
            }

            // Dokumen valid
            return view('documents.validate', [
                'valid' => true,
                'document' => $document,
                'docNumber' => $docNumber
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating document: ' . $e->getMessage());

            return view('documents.validate', [
                'valid' => false,
                'message' => 'Terjadi kesalahan saat validasi',
                'docNumber' => $docNumber ?? 'N/A'
            ]);
        }
    }
}
