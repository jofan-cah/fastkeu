<?php

namespace App\Helpers;

use App\Models\Document;
use App\Models\DocumentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentNumberHelper
{
    /**
     * Generate single document
     *
     * @param mixed $model Model yang berelasi (Subscription, Customer, dll)
     * @param string $code Document type code (form, konf, ba)
     * @param array $additionalData Data tambahan (notes, status, dll)
     * @return Document
     */
    public static function generateDocument($model, string $code, array $additionalData = [])
    {
        return DB::transaction(function () use ($model, $code, $additionalData) {
            $now = Carbon::now();
            $currentMonth = self::getRomanMonth($now->month);
            $currentYear = $now->format('Y');

            // Ambil document type
            $docType = DocumentType::where('code', $code)
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            // Cek duplikasi
            $existing = Document::where('documentable_type', get_class($model))
                ->where('documentable_id', $model->getKey())
                ->where('doc_type_id', $docType->doc_type_id)
                ->first();

            if ($existing) {
                Log::info("Document already exists: {$existing->doc_number}");
                return $existing;
            }

            // Auto-sync counter dengan data existing (GLOBAL, semua periode)
            $syncedNumber = self::syncCounterWithExistingDocuments($docType, $currentMonth, $currentYear);

            // Set counter
            $docType->current_number = $syncedNumber;

            // Increment counter
            $docType->current_number += 1;
            $sequence = $docType->current_number;

            // Update bulan/tahun (tapi gak reset counter)
            $docType->current_month = $currentMonth;
            $docType->current_year = $currentYear;
            $docType->save();

            // Format nomor dokumen
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

            // Generate Document ID untuk routing
            $documentId = DocumentHelper::generateDocumentId($code);

            // Simpan dokumen
            $document = Document::create(array_merge([
                'id' => $documentId,
                'doc_number' => $docNumber,
                'doc_type_id' => $docType->doc_type_id,
                'documentable_type' => get_class($model),
                'documentable_id' => $model->getKey(),
                'document_date' => $now->toDateString(),
                'status' => 'generated',
                'created_by' => Auth::id() ?? 'system',
            ], $additionalData));

            Log::info("Document generated: {$document->doc_number} (ID: {$document->id})");

            return $document;
        });
    }

    /**
     * Generate multiple documents sekaligus
     *
     * @param mixed $model
     * @param array $codes ['form', 'konf', 'ba']
     * @param array $additionalData
     * @return array ['form' => Document, 'konf' => Document, 'ba' => Document]
     */
    public static function generateMultipleDocuments($model, array $codes, array $additionalData = [])
    {
        $documents = [];

        foreach ($codes as $code) {
            $documents[$code] = self::generateDocument($model, $code, $additionalData);
        }

        return $documents;
    }

    /**
     * Preview next number (tanpa save)
     *
     * @param string $code
     * @return string
     */
    public static function previewNextNumber(string $code): string
    {
        $now = Carbon::now();
        $currentMonth = self::getRomanMonth($now->month);
        $currentYear = $now->format('Y');

        $docType = DocumentType::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$docType) {
            Log::warning("Document type not found for code: {$code}");
            return '-';
        }

        // Sync dengan data existing
        $syncedNumber = self::syncCounterWithExistingDocuments($docType, $currentMonth, $currentYear);
        $nextNumber = $syncedNumber + 1;

        // Format
        if ($nextNumber < 1000) {
            return sprintf(
                '%s.%03d/%s/%s/%s',
                $docType->prefix,
                $nextNumber,
                $docType->format_code,
                $currentMonth,
                $currentYear
            );
        } else {
            return sprintf(
                '%s.%d/%s/%s/%s',
                $docType->prefix,
                $nextNumber,
                $docType->format_code,
                $currentMonth,
                $currentYear
            );
        }
    }

    /**
     * Sync counter dengan data existing
     * PENTING: Counter TERUS NAMBAH, gak reset per bulan/tahun!
     *
     * @param DocumentType $docType
     * @param string $month
     * @param string $year
     * @return int
     */
    private static function syncCounterWithExistingDocuments($docType, $month, $year): int
    {
        Log::info("=== Syncing Counter ===");
        Log::info("Doc Type: {$docType->code} (ID: {$docType->doc_type_id})");
        Log::info("Prefix: {$docType->prefix}, Format: {$docType->format_code}");

        // Pattern untuk extract nomor (IGNORE bulan & tahun)
        $pattern = "/^" . preg_quote($docType->prefix, '/') . "\.(\d+)\/" . preg_quote($docType->format_code, '/') . "\//";

        Log::info("Pattern: {$pattern}");

        // Ambil SEMUA documents dengan doc_type_id ini
        $documents = Document::where('doc_type_id', $docType->doc_type_id)->get();

        Log::info("Total documents found: {$documents->count()}");

        if ($documents->isEmpty()) {
            Log::info("No documents found, starting from 0");
            return 0;
        }

        // Extract semua nomor urut
        $numbers = [];

        foreach ($documents as $doc) {
            if (preg_match($pattern, $doc->doc_number, $matches)) {
                $num = (int) $matches[1];
                $numbers[] = $num;
                Log::info("✓ Matched {$doc->doc_number} -> Number: {$num}");
            } else {
                Log::warning("✗ Not matched: {$doc->doc_number}");
            }
        }

        if (empty($numbers)) {
            Log::info("No matching numbers found");
            return 0;
        }

        $max = max($numbers);
        Log::info("Max number found: {$max}");
        Log::info("=== End Syncing ===");

        return $max;
    }

    /**
     * Convert month to Roman numeral
     */
    private static function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romans[$month] ?? 'I';
    }
}
