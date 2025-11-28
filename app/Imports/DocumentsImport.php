<?php

namespace App\Imports;

use App\Models\Document;
use App\Models\DocumentType;
use App\Helpers\DocumentHelper;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow; // ✅ Add this
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DocumentsImport implements ToCollection, WithStartRow, SkipsEmptyRows // ✅ Add WithStartRow
{
    protected $imported = 0;
    protected $updated = 0;
    protected $skipped = 0;
    protected $errors = [];

    /**
     * ✅ Start from row 2 (skip row 1 yang isinya "45662")
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Process collection dari Excel
     */
    public function collection(Collection $rows)
    {
        // Get header from first row
        $header = $rows->first();

        // Process data rows (skip header row)
        foreach ($rows->slice(1) as $index => $row) {
            try {
                $rowNumber = $index + 3; // +3 karena skip row 1 + header row 2 + index 0

                // Map data by column position (karena header ada di row 2)
                $no = $row[0] ?? null;
                $noKonf = $row[1] ?? null;  // NOMOR SURAT SALES CORMINATION
                $noBa = $row[2] ?? null;    // NOMOR SURAT BERITA ACARA
                $noForm = $row[3] ?? null;  // NOMOR SURAT FORMULIR BERLANGGANAN
                $customerName = $row[4] ?? null; // NAMA
                $tanggal = $row[5] ?? null;      // TANGGAL
                $notes = $row[6] ?? 'OK';        // KETERANGAN

                // Clean data
                $noKonf = $this->cleanValue($noKonf);
                $noBa = $this->cleanValue($noBa);
                $noForm = $this->cleanValue($noForm);
                $customerName = $this->cleanValue($customerName);
                $notes = $this->cleanValue($notes) ?? 'OK';

                // Validasi data wajib
                if (empty($customerName)) {
                    $this->errors[] = "Row {$rowNumber}: Nama customer tidak boleh kosong";
                    $this->skipped++;
                    continue;
                }

                if (empty($tanggal)) {
                    $this->errors[] = "Row {$rowNumber}: Tanggal tidak boleh kosong";
                    $this->skipped++;
                    continue;
                }

                // Skip kalau semua nomor kosong
                if (empty($noKonf) && empty($noBa) && empty($noForm)) {
                    $this->errors[] = "Row {$rowNumber}: Minimal harus ada 1 nomor dokumen";
                    $this->skipped++;
                    continue;
                }

                // Parse tanggal
                $documentDate = $this->parseDate($tanggal);

                // Generate subscription_id
                $subscriptionId = 'SUB-IMP-' . date('Ymd') . '-' . str_pad($this->imported + $this->updated + 1, 4, '0', STR_PAD_LEFT);

                // ✅ IMPORT DOCUMENTS
                $documentsToImport = [
                    ['code' => 'konf', 'doc_number' => $noKonf],
                    ['code' => 'ba', 'doc_number' => $noBa],
                    ['code' => 'form', 'doc_number' => $noForm],
                ];

                $successCount = 0;

                foreach ($documentsToImport as $docData) {
                    if (empty($docData['doc_number'])) continue;

                    $code = $docData['code'];
                    $docNumber = trim($docData['doc_number']);

                    // Get document type
                    $docType = DocumentType::where('code', $code)
                        ->where('is_active', true)
                        ->first();

                    if (!$docType) {
                        $this->errors[] = "Row {$rowNumber}: Document type '{$code}' tidak ditemukan";
                        continue;
                    }

                    // ✅ CEK DUPLICATE
                    $existingDoc = Document::where('doc_number', $docNumber)->first();

                    if ($existingDoc) {
                        // Update existing
                        $existingDoc->update([
                            'subscription_id' => $subscriptionId,
                            'customer_name' => $customerName,
                            'document_date' => $documentDate,
                            'notes' => $notes,
                            'status' => 'signed',
                            'updated_by' => 'excel-import',
                        ]);

                        $successCount++;
                        Log::info("Document updated: {$docNumber}");
                    } else {
                        // Create new
                        $documentId = DocumentHelper::generateDocumentId($code);

                        Document::create([
                            'id' => $documentId,
                            'doc_number' => $docNumber,
                            'doc_type_id' => $docType->doc_type_id,
                            'subscription_id' => $subscriptionId,
                            'customer_name' => $customerName,
                            'document_date' => $documentDate,
                            'status' => 'signed',
                            'notes' => $notes,
                            'created_by' => 'excel-import',
                        ]);

                        // Update counter
                        $this->updateCounterFromDocNumber($docType, $docNumber);

                        $successCount++;
                        Log::info("Document imported: {$docNumber}");
                    }
                }

                // Count per subscription (bukan per document)
                if ($successCount > 0) {
                    // Cek apakah ini update atau import baru
                    $existingAny = Document::where('subscription_id', $subscriptionId)->count() > $successCount;

                    if ($existingAny) {
                        $this->updated++;
                    } else {
                        $this->imported++;
                    }
                }

            } catch (\Exception $e) {
                $rowNumber = $index + 3;
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->skipped++;
                Log::error("Import error row {$rowNumber}: " . $e->getMessage());
            }
        }
    }

    /**
     * Clean value (trim & handle empty)
     */
    private function cleanValue($value)
    {
        if (is_null($value)) return null;

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseDate($date)
    {
        try {
            // Kalau udah Carbon
            if ($date instanceof \Carbon\Carbon) {
                return $date->format('Y-m-d');
            }

            // Kalau DateTime
            if ($date instanceof \DateTime) {
                return $date->format('Y-m-d');
            }

            // Kalau numeric (Excel date serial)
            if (is_numeric($date)) {
                // Excel date serial: 1900-01-01 = 1
                $unixTimestamp = ($date - 25569) * 86400;
                return date('Y-m-d', $unixTimestamp);
            }

            // Parse string date (support berbagai format)
            // Format: "18 Januari 2023"
            $months = [
                'januari' => '01', 'februari' => '02', 'maret' => '03',
                'april' => '04', 'mei' => '05', 'juni' => '06',
                'juli' => '07', 'agustus' => '08', 'september' => '09',
                'oktober' => '10', 'november' => '11', 'desember' => '12',
                'january' => '01', 'february' => '02', 'march' => '03',
                'may' => '05', 'june' => '06', 'july' => '07',
                'august' => '08', 'october' => '10', 'december' => '12',
            ];

            $dateStr = strtolower($date);

            foreach ($months as $monthName => $monthNum) {
                if (strpos($dateStr, $monthName) !== false) {
                    // Extract day and year
                    preg_match('/(\d+)\s+\w+\s+(\d{4})/', $dateStr, $matches);
                    if (count($matches) === 3) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        return "{$year}-{$monthNum}-{$day}";
                    }
                }
            }

            // Fallback: try parse with Carbon
            return Carbon::parse($date)->format('Y-m-d');

        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$date}, using current date");
            return now()->format('Y-m-d');
        }
    }

    /**
     * Update counter dari doc_number
     */
    private function updateCounterFromDocNumber($docType, $docNumber)
    {
        $pattern = "/^" . preg_quote($docType->prefix, '/') . "\.(\d+)\//";

        if (preg_match($pattern, $docNumber, $matches)) {
            $number = (int) $matches[1];

            if ($number > $docType->current_number) {
                $docType->current_number = $number;

                // Extract month & year
                if (preg_match('/\/([IVX]+)\/(\d{4})$/', $docNumber, $dateMatches)) {
                    $docType->current_month = $dateMatches[1];
                    $docType->current_year = $dateMatches[2];
                }

                $docType->save();
            }
        }
    }

    /**
     * Get import stats
     */
    public function getStats()
    {
        return [
            'imported' => $this->imported,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'total' => $this->imported + $this->updated,
            'errors' => $this->errors,
        ];
    }
}
