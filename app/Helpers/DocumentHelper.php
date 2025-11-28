<?php

namespace App\Helpers;

use App\Models\Document;

class DocumentHelper
{
    /**
     * Generate Document ID untuk routing
     * Format: DOC + [Code Map] + [Random 6 digit]
     *
     * Example: DOCF100001, DOCK200015, DOCB300100
     */
    public static function generateDocumentId(string $typeCode): string
    {
        // Mapping code ke prefix
        $codeMap = [
            'form' => 'F1',     // Formulir
            'konf' => 'K2',     // Konfirmasi
            'ba' => 'B3',       // Berita Acara
            'skpk' => 'S4',     // Surat Pengalaman Kerja
            'sp' => 'P5',       // Surat Permohonan
            'bai' => 'I6',      // BA Instalasi
            'mou' => 'M7',      // MoU
            'phk' => 'H8',      // PHK
            'rekom' => 'R9',    // Rekomendasi
            'kuasa' => 'U0',    // Kuasa
            'baper' => 'A1',    // BA Perubahan
        ];

        $prefix = $codeMap[$typeCode] ?? 'XX';

        // Generate unique ID
        do {
            $counter = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $id = 'DOC' . $prefix . $counter; // DOCF100001

            $exists = Document::where('id', $id)->exists();
        } while ($exists);

        return $id;
    }
}
