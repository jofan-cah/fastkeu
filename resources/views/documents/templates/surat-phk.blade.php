<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan PHK</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            width: 210mm;
            min-height: 297mm;
            position: relative;
        }

        /* Background Image Full A4 */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: -1;
        }

        .background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Content overlay */
        .content-wrapper {
            position: relative;
            padding: 30mm 20mm 20mm 20mm;
            z-index: 1;
        }

        .header-info {
            margin-bottom: 20px;
        }

        .header-info .number {
            font-size: 10pt;
            margin-bottom: 5px;
        }

        .header-info .subject {
            font-size: 10pt;
        }

        .title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 20px 0;
        }

        .director-info {
            margin: 15px 0;
            font-size: 10pt;
        }

        .director-info table {
            border: none;
            width: 100%;
        }

        .director-info td {
            padding: 2px 0;
            border: none;
            vertical-align: top;
        }

        .director-info .label {
            width: 150px;
        }

        .director-info .separator {
            width: 20px;
            text-align: center;
        }

        .statement-section {
            margin: 15px 0;
            font-size: 10pt;
        }

        .statement-section .statement-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .statement-section ol {
            margin-left: 20px;
            padding-left: 5px;
        }

        .statement-section li {
            margin: 8px 0;
            text-align: justify;
        }

        .employee-table {
            margin: 20px 0;
        }

        .employee-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .employee-table th,
        .employee-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .employee-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }

        .signature-section {
            margin-top: 40px;
            text-align: right;
            padding-right: 30px;
        }

        .signature-section .date {
            margin-bottom: 5px;
        }

        .signature-section .position {
            font-weight: bold;
        }

        .signature-section .sign-space {
            height: 60px;
            margin: 10px 0;
        }

        .signature-section .sign-space img {
            max-height: 50px;
        }

        .signature-section .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 150px;
            padding-bottom: 2px;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- Background Image Full A4 -->
    <div class="background">
        <img src="{{ public_path('pdf123.jpg') }}" alt="Background">
    </div>

    <!-- Content Overlay - Page 1 -->
    <div class="content-wrapper">

        <!-- Header Info -->
        <div class="header-info">
            <div class="number">Nomor: {{ $docNumber }}</div>
            <div class="subject">Hal: Surat Pernyataan Tanggung Jawab Mutlak<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pelaporan PHK dari Badan Usaha</div>
        </div>

        <!-- Title -->
        <div class="title">
            SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK PIMPINAN PERUSAHAAN
        </div>

        <!-- Director Info -->
        <div class="director-info">
            <table>
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="separator">:</td>
                    <td>{{ $director_name }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="separator">:</td>
                    <td>Direktur</td>
                </tr>
                <tr>
                    <td class="label">Nama Perusahaan</td>
                    <td class="separator">:</td>
                    <td>PT. Jaringan Fiberone Indonesia</td>
                </tr>
                <tr>
                    <td class="label">No. HP/Alamat email</td>
                    <td class="separator">:</td>
                    <td>{{ $director_phone }} / {{ $director_email }}</td>
                </tr>
            </table>
        </div>

        <!-- Statement Section -->
        <div class="statement-section">
            <div class="statement-title">DENGAN INI MENYATAKAN:</div>

            <ol>
                <li>Bahwa telah dilakukan Pemutusan Hubungan Kerja (PHK) terhadap sejumlah karyawan dan PHK atas sejumlah karyawan tersebut diusulkan untuk dinonaktifkan dari kepesertaan JKN (daftar nama terlampir).</li>

                <li>Bahwa seluruh data/informasi/dokumen yang dilampirkan dalam surat ini adalah benar dan kebenaran terhadap dokumen yang disampaikan oleh Pemberi Kerja merupakan tanggung jawab dari Pemberi Kerja.</li>

                <li>Bahwa telah dilakukan sosialisasi kepada pekerja yang diusulkan untuk dinonaktifkan terkait hak dan kewajiban yang berkaitan dengan Jaminan Kesehatan Nasional (JKN).</li>

                <li>Bahwa tidak terdapat penolakan pekerja atas pemutusan hubungan kerja yang telah dilakukan sesuai dengan peraturan perundangan-undangan yang berlaku.</li>

                <li>Apabila pemberi kerja melakukan penonaktifan kepada pekerja yang masih dalam proses perselisihan PHK atau melakukan penonaktifan pekerja yang masih berstatus sebagai pekerja dari pemberi kerja maka Pemberi Kerja wajib mendaftarkan kembali pekerjanya atau dapat didaftarkan kembali oleh BPJS Kesehatan sebagai tanggungan pemberi kerja, serta Pemberi Kerja wajib memungut, membayar, dan menyetorkan Iuran sesuai dengan peraturan perundang-undangan yang berlaku.</li>

                <li>Dalam hal Pemberi Kerja memberikan dokumen yang tidak benar, Pemberi Kerja diberikan sanksi sesuai dengan ketentuan Perundang-Undangan.</li>
            </ol>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="date">Klaten, {{ $generated_date->format('d F Y') }}</div>
            <div class="position">Direktur</div>
            <div class="position">PT. Jaringan Fiberone Indonesia</div>
            <div class="sign-space">
                <img src="{{ public_path('images/ttd_arief.png') }}" alt="TTD">
            </div>
            <div class="name">{{ $director_name }}</div>
        </div>

    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Content Overlay - Page 2 (Lampiran) -->
    <div class="content-wrapper">

        <!-- Lampiran Header -->
        <div style="text-align: right; margin-bottom: 20px;">
            <div><strong>Lampiran Surat</strong></div>
            <div>Nomor: {{ $docNumber }}</div>
            <div>Tanggal: {{ $generated_date->format('d F Y') }}</div>
        </div>

        <!-- Table Section -->
        <div class="employee-table">
            <h4 style="margin-bottom: 10px;">b. PHK Tanpa Jaminan 6 Bulan (dokumen tidak/belum lengkap)</h4>

            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 80px;">Noka Peserta</th>
                        <th>Nama Pekerja</th>
                        <th style="width: 100px;">Nomor HP</th>
                        <th>Alasan/Jenis PHK</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach($employees as $index => $employee)
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $employee['noka'] }}</td>
                        <td>{{ $employee['name'] }}</td>
                        <td>{{ $employee['phone'] }}</td>
                        <td>{{ $employee['reason'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Signature at bottom -->
        <div class="signature-section" style="margin-top: 60px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                        <div style="margin-bottom: 5px;">Jabatan</div>
                        <div style="margin-bottom: 5px;">Tanda Tangan</div>
                        <div style="height: 60px;"></div>
                        <div style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px; padding-bottom: 2px;">Nama Perwakilan Serikat Pekerja/</div>
                        <div>Perwakilan pekerja yang di PHK</div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                        <div style="margin-bottom: 5px;">Direktur</div>
                        <div style="margin-bottom: 5px;">PT Jaringan FiberOne Indonesia</div>
                        <div style="height: 60px;">
                            <img src="{{ public_path('images/ttd_arief.png') }}" alt="TTD" style="max-height: 50px;">
                        </div>
                        <div style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px; padding-bottom: 2px; font-weight: bold;">{{ $director_name }}</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>
