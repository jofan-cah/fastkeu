<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of document types
     */
    public function index(Request $request)
    {
        $query = DocumentType::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('prefix', 'like', "%{$search}%")
                  ->orWhere('format_code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active' ? 1 : 0);
        }

        $documentTypes = $query->orderBy('prefix', 'asc')->get();

        return view('document-types.indexDocumentType', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new document type
     */
    public function create()
    {
        return view('document-types.createDocumentType');
    }

    /**
     * Store a newly created document type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:document_types,code',
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:10',
            'format_code' => 'required|string|max:50',
            'description' => 'nullable|string',
        ], [
            'code.required' => 'Kode wajib diisi',
            'code.unique' => 'Kode sudah digunakan',
            'name.required' => 'Nama wajib diisi',
            'prefix.required' => 'Prefix wajib diisi',
            'format_code.required' => 'Format code wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate doc_type_id
            $docTypeId = 'DT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $documentType = DocumentType::create([
                'doc_type_id' => $docTypeId,
                'code' => $request->code,
                'name' => $request->name,
                'prefix' => $request->prefix,
                'format_code' => $request->format_code,
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => $request->description,
            ]);

            logActivity('create', Auth::user()->full_name . ' created document type: ' . $documentType->name);

            return response()->json([
                'success' => true,
                'message' => 'Document type berhasil dibuat',
                'data' => $documentType
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating document type: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat document type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified document type
     */
    public function show($doc_type_id)
    {
        $documentType = DocumentType::findOrFail($doc_type_id);

        // Get documents count
        $totalDocuments = Document::where('doc_type_id', $doc_type_id)->count();

        // Get latest document
        $latestDocument = Document::where('doc_type_id', $doc_type_id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Get documents per month (last 6 months)
        $documentsPerMonth = Document::where('doc_type_id', $doc_type_id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        return view('document-types.showDocumentType', compact(
            'documentType',
            'totalDocuments',
            'latestDocument',
            'documentsPerMonth'
        ));
    }

    /**
     * Show the form for editing the document type
     */
    public function edit($doc_type_id)
    {
        $documentType = DocumentType::findOrFail($doc_type_id);
        return view('document-types.editDocumentType', compact('documentType'));
    }

    /**
     * Update the specified document type
     */
    public function update(Request $request, $doc_type_id)
    {
        $documentType = DocumentType::findOrFail($doc_type_id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:document_types,code,' . $doc_type_id . ',doc_type_id',
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:10',
            'format_code' => 'required|string|max:50',
            'description' => 'nullable|string',
        ], [
            'code.required' => 'Kode wajib diisi',
            'code.unique' => 'Kode sudah digunakan',
            'name.required' => 'Nama wajib diisi',
            'prefix.required' => 'Prefix wajib diisi',
            'format_code.required' => 'Format code wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $documentType->update([
                'code' => $request->code,
                'name' => $request->name,
                'prefix' => $request->prefix,
                'format_code' => $request->format_code,
                'description' => $request->description,
            ]);

            logActivity('update', Auth::user()->full_name . ' updated document type: ' . $documentType->name);

            return response()->json([
                'success' => true,
                'message' => 'Document type berhasil diupdate',
                'data' => $documentType
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating document type: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate document type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($doc_type_id)
    {
        try {
            $documentType = DocumentType::findOrFail($doc_type_id);

            $documentType->is_active = !$documentType->is_active;
            $documentType->save();

            $status = $documentType->is_active ? 'activated' : 'deactivated';
            logActivity('update', Auth::user()->full_name . " {$status} document type: " . $documentType->name);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah',
                'is_active' => $documentType->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
        }
    }

    /**
     * Remove the specified document type (soft delete)
     */
    public function destroy($doc_type_id)
    {
        try {
            $documentType = DocumentType::findOrFail($doc_type_id);

            // Check if has related documents
            $documentsCount = Document::where('doc_type_id', $doc_type_id)->count();

            if ($documentsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus document type yang memiliki {$documentsCount} dokumen terkait. Nonaktifkan saja."
                ], 422);
            }

            $docTypeName = $documentType->name;
            $documentType->delete();

            logActivity('delete', Auth::user()->full_name . ' deleted document type: ' . $docTypeName);

            return response()->json([
                'success' => true,
                'message' => 'Document type berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting document type: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus document type'
            ], 500);
        }
    }

    /**
     * Get next number preview for a document type
     */
    public function previewNumber($doc_type_id)
    {
        try {
            $documentType = DocumentType::findOrFail($doc_type_id);

            $now = Carbon::now();
            $currentMonth = $this->getRomanMonth($now->month);
            $currentYear = $now->format('Y');

            $nextNumber = $documentType->current_number + 1;

            // Format preview
            if ($nextNumber < 1000) {
                $preview = sprintf(
                    '%s.%03d/%s/%s/%s',
                    $documentType->prefix,
                    $nextNumber,
                    $documentType->format_code,
                    $currentMonth,
                    $currentYear
                );
            } else {
                $preview = sprintf(
                    '%s.%d/%s/%s/%s',
                    $documentType->prefix,
                    $nextNumber,
                    $documentType->format_code,
                    $currentMonth,
                    $currentYear
                );
            }

            return response()->json([
                'success' => true,
                'preview' => $preview,
                'current_number' => $documentType->current_number,
                'next_number' => $nextNumber
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate preview'
            ], 500);
        }
    }

    /**
     * Reset counter (dengan konfirmasi)
     */
    public function resetCounter(Request $request, $doc_type_id)
    {
        try {
            $documentType = DocumentType::findOrFail($doc_type_id);

            $documentType->current_number = 0;
            $documentType->current_month = null;
            $documentType->current_year = null;
            $documentType->save();

            logActivity('update', Auth::user()->full_name . ' reset counter for document type: ' . $documentType->name);

            return response()->json([
                'success' => true,
                'message' => 'Counter berhasil direset ke 0'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset counter'
            ], 500);
        }
    }

    /**
     * Convert month to Roman numeral
     */
    private function getRomanMonth($month)
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romans[$month] ?? 'I';
    }
}
