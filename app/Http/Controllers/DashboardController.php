<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        // Stats
        $stats = [
            'total_documents' => Document::count(),
            'today_documents' => Document::whereDate('created_at', today())->count(),
            'generated' => Document::where('status', 'generated')->count(),
            'printed' => Document::where('status', 'printed')->count(),
            'signed' => Document::where('status', 'signed')->count(),
            'cancelled' => Document::where('status', 'cancelled')->count(),
            'document_types' => DocumentType::where('is_active', true)->count(),
        ];

        // Recent documents
        $recentDocuments = Document::with(['documentType', 'creator'])
            ->latest()
            ->take(10)
            ->get();

        // Documents per type
        $documentsPerType = Document::select('doc_type_id', DB::raw('count(*) as total'))
            ->groupBy('doc_type_id')
            ->with('documentType')
            ->get();

        return view('dashboard', compact('stats', 'recentDocuments', 'documentsPerType'));
    }
}
