<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            // 1. Surat Pengalaman Kerja
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'skpk',
                'name' => 'Surat Pengalaman Kerja',
                'prefix' => '10',
                'format_code' => 'SKPK-F1',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Surat Pengalaman Kerja untuk karyawan',
            ],

            // 2. Surat Permohonan
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'sp',
                'name' => 'Surat Permohonan',
                'prefix' => '13',
                'format_code' => 'F1-SP',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Surat Permohonan untuk berbagai keperluan',
            ],

            // 3. Berita Acara Instalasi (Official)
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'bai',
                'name' => 'Berita Acara Instalasi',
                'prefix' => '13',
                'format_code' => 'F1-BAI',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Berita Acara Instalasi resmi',
            ],

            // 4. Surat Adendum MOU
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'mou',
                'name' => 'Surat Adendum MOU',
                'prefix' => '15',
                'format_code' => 'F1-MoU',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Surat Adendum Memorandum of Understanding',
            ],

            // 5. Surat Pernyataan PHK
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'phk',
                'name' => 'Surat Pernyataan PHK',
                'prefix' => '20',
                'format_code' => 'F1-SP',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Surat Pernyataan Pemutusan Hubungan Kerja',
            ],

            // 6. Surat Rekomendasi
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'rekom',
                'name' => 'Surat Rekomendasi',
                'prefix' => '21',
                'format_code' => 'F1-FO',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Surat Rekomendasi untuk berbagai keperluan',
            ],

            // 7. Formulir Berlangganan - BEFAST (API)
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'form',
                'name' => 'Formulir Berlangganan',
                'prefix' => '22',
                'format_code' => 'F1-FB',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Formulir Berlangganan untuk customer baru (Generate dari BEFAST)',
            ],

            // 8. Sales Confirmation - BEFAST (API)
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'konf',
                'name' => 'Sales Confirmation',
                'prefix' => '23',
                'format_code' => 'F1-SC',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Sales Confirmation untuk konfirmasi pesanan (Generate dari BEFAST)',
            ],

            // 9. Berita Acara (BA Instalasi) - BEFAST (API)
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'ba',
                'name' => 'Berita Acara',
                'prefix' => '24',
                'format_code' => 'F1-BA',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Berita Acara Instalasi customer (Generate dari BEFAST)',
            ],

            // 10. Surat Kuasa
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'kuasa',
                'name' => 'Surat Kuasa',
                'prefix' => '30',
                'format_code' => 'F1-SK',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Surat Kuasa untuk berbagai keperluan',
            ],

            // 11. Berita Acara Kesepakatan Perubahan Layanan
            [
                'doc_type_id' => $this->generateDocTypeId(),
                'code' => 'baper',
                'name' => 'Berita Acara Kesepakatan Perubahan Layanan',
                'prefix' => '31',
                'format_code' => 'FO-BA',
                'current_number' => 0,
                'current_month' => null,
                'current_year' => null,
                'is_active' => true,
                'description' => 'Berita Acara untuk perubahan layanan customer',
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::create($type);
        }

        $this->command->info('✅ Document Types seeded successfully!');
        $this->command->info('📄 Total: ' . count($documentTypes) . ' document types created');
        $this->command->info('');
        $this->command->info('🔥 Document Types untuk BEFAST API:');
        $this->command->info('   - form  → 22.xxx/F1-FB/...');
        $this->command->info('   - konf  → 23.xxx/F1-SC/...');
        $this->command->info('   - ba    → 24.xxx/F1-BA/...');
    }

    /**
     * Generate Document Type ID
     * Format: DT-YYYYMMDD-RANDOM6
     */
    private function generateDocTypeId(): string
    {
        return 'DT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
