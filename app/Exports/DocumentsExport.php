<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class DocumentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $docTypeId;

    public function __construct($startDate = null, $endDate = null, $docTypeId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->docTypeId = $docTypeId;
    }

    /**
     * Ambil data documents yang di-group by subscription_id
     */
    public function collection()
    {
        // Query documents dengan filter
        $query = Document::with(['documentType'])
            ->select('subscription_id', 'customer_name', 'document_date', 'notes')
            ->whereNotNull('subscription_id');

        // Filter by date range
        if ($this->startDate) {
            $query->whereDate('document_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('document_date', '<=', $this->endDate);
        }

        // Group by subscription_id
        $query->groupBy('subscription_id', 'customer_name', 'document_date', 'notes')
            ->orderBy('document_date', 'desc');

        $subscriptions = $query->get();

        // Untuk setiap subscription, ambil 3 nomor dokumen (form, konf, ba)
        $result = collect();

        foreach ($subscriptions as $subscription) {
            // Ambil 3 documents untuk subscription ini
            $documents = Document::with('documentType')
                ->where('subscription_id', $subscription->subscription_id)
                ->whereIn('doc_type_id', function($query) {
                    $query->select('doc_type_id')
                        ->from('document_types')
                        ->whereIn('code', ['form', 'konf', 'ba']);
                })
                ->get();

            // Extract nomor per type
            $noForm = null;
            $noKonf = null;
            $noBa = null;

            foreach ($documents as $doc) {
                $code = $doc->documentType->code ?? null;

                if ($code === 'form') {
                    $noForm = $doc->doc_number;
                } elseif ($code === 'konf') {
                    $noKonf = $doc->doc_number;
                } elseif ($code === 'ba') {
                    $noBa = $doc->doc_number;
                }
            }

            // Add to result
            $result->push((object)[
                'subscription_id' => $subscription->subscription_id,
                'no_konf' => $noKonf,
                'no_ba' => $noBa,
                'no_form' => $noForm,
                'customer_name' => $subscription->customer_name,
                'document_date' => $subscription->document_date,
                'notes' => $subscription->notes ?? 'OK',
            ]);
        }

        return $result;
    }

    /**
     * Header columns
     */
    public function headings(): array
    {
        return [
            'NO',
            'NOMOR SURAT SALES CONFIRMATION',
            'NOMOR SURAT BERITA ACARA',
            'NOMOR SURAT FORMULIR BERLANGGANAN',
            'NAMA',
            'TANGGAL',
            'KETERANGAN',
        ];
    }

    /**
     * Mapping data ke columns
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->no_konf ?? '-',
            $row->no_ba ?? '-',
            $row->no_form ?? '-',
            $row->customer_name ?? '-',
            $row->document_date ? \Carbon\Carbon::parse($row->document_date)->format('d M Y') : '-',
            $row->notes ?? 'OK',
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // All cells
            'A:G' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 35,  // Sales Confirmation
            'C' => 35,  // Berita Acara
            'D' => 35,  // Formulir
            'E' => 25,  // Nama
            'F' => 15,  // Tanggal
            'G' => 15,  // Keterangan
        ];
    }
}
