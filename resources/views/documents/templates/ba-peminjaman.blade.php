<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Peminjaman Perangkat</title>
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
            height: 297mm;
            position: relative;
            overflow: hidden;
        }

        /* Background Image Full A4 */
        .background {
            position: absolute;
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
            padding: 25mm 18mm 18mm 18mm;
            z-index: 1;
        }

        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .doc-number {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .opening {
            text-align: justify;
            margin: 10px 0;
            font-size: 10pt;
        }

        .party-section {
            margin: 10px 0;
            font-size: 10pt;
        }

        .party-section .party-title {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .party-section table {
            border: none;
            margin-left: 10px;
        }

        .party-section td {
            padding: 2px 0;
            border: none;
            vertical-align: top;
        }

        .party-section .label {
            width: 100px;
        }

        .party-section .separator {
            width: 15px;
            text-align: center;
        }

        .statement {
            margin: 10px 0;
            font-size: 10pt;
            text-align: justify;
        }

        .items-table {
            margin: 10px 0;
        }

        .items-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .items-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }

        .terms-section {
            margin: 10px 0;
            font-size: 10pt;
        }

        .terms-section .terms-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .terms-section ul {
            margin-left: 10px;
            padding-left: 5px;
            list-style: none;
        }

        .terms-section li {
            margin: 3px 0;
            text-align: justify;
        }

        .closing {
            margin: 10px 0;
            font-size: 10pt;
            text-align: justify;
        }

        .signature {
            margin-top: 20px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-box .title-sig {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 3px;
        }

        .signature-box .company {
            font-weight: bold;
            margin: 3px 0;
            font-size: 9pt;
        }

        .signature-box .sign-space {
            height: 50px;
            margin: 8px 0;
        }

        .signature-box .sign-space img {
            max-height: 45px;
        }

        .signature-box .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 130px;
            padding-bottom: 2px;
            font-size: 10pt;
        }

        .signature-box .position {
            font-size: 9pt;
            margin-top: 2px;
        }

        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px dashed #ccc;
        }

        .qr-section img {
            width: 85px;
            height: 85px;
            display: block;
            margin: 0 auto 6px auto;
        }

        .qr-section .qr-label {
            font-size: 7pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Background Image Full A4 -->
    <div class="background">
        <img src="{{ public_path('pdf123.jpg') }}" alt="Background">
    </div>

    <!-- Content Overlay -->
    <div class="content-wrapper">

        <!-- Title -->
        <div class="title">
            BERITA ACARA PEMINJAMAN PERANGKAT
        </div>

        <!-- Document Number -->
        <div class="doc-number">
            NO. {{ $docNumber }}
        </div>

        <!-- Opening -->
        <div class="opening">
            Pada hari ini <strong>{{ $generated_date->translatedFormat('l') }}</strong> tanggal <strong>{{ $generated_date->translatedFormat('d F Y') }}</strong> Yang bertanda tangan di bawah ini:
        </div>

        <!-- Pihak Pertama -->
        <div class="party-section">
            <div class="party-title">Pihak Pertama</div>
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td>Ayu Mutiara A.</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="separator">:</td>
                    <td>Administrasi</td>
                </tr>
                <tr>
                    <td class="label">Perusahaan</td>
                    <td class="separator">:</td>
                    <td>PT Jaringan FiberOne Indonesia</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td>Griya Permata Hijau, Jl. Mpu Sedah No.01 Blok A, Gatak, Sumberejo, Klaten Selatan, Klaten, Jawa Tengah</td>
                </tr>
            </table>
        </div>

        <!-- Pihak Kedua -->
        <div class="party-section">
            <div class="party-title">Pihak Kedua</div>
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td>{{ $borrower_name }}</td>
                </tr>
                <tr>
                    <td class="label">Usaha</td>
                    <td class="separator">:</td>
                    <td>{{ $borrower_business }}</td>
                </tr>
                <tr>
                    <td class="label">ID Pelanggan</td>
                    <td class="separator">:</td>
                    <td>{{ $borrower_id }}</td>
                </tr>
                <tr>
                    <td class="label">No Hp</td>
                    <td class="separator">:</td>
                    <td>{{ $borrower_phone }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td>{{ $borrower_address }}</td>
                </tr>
            </table>
        </div>

        <!-- Statement -->
        <div class="statement">
            Dengan ini menyatakan bahwa <strong>Pihak Pertama</strong> menyerahkan peminjaman perangkat kepada <strong>Pihak Kedua</strong> berupa:
        </div>

        <!-- Items Table -->
        <div class="items-table">
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No.</th>
                        <th>Nama Barang</th>
                        <th style="width: 70px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no =1;
                    @endphp
                    @foreach($items as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}.</td>
                        <td>{{ $item['name'] }}</td>
                        <td style="text-align: center;">{{ $item['quantity'] }} Unit</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Terms -->
        <div class="terms-section">
            <div class="terms-title">Ketentuan Peminjaman:</div>
            <ul>
                @foreach(explode("\n", $loan_terms) as $term)
                    @if(trim($term))
                    <li>{{ trim($term) }}</li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Closing -->
        <div class="closing">
            Demikian bukti peminjaman ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <!-- Signature -->
        <div style="text-align: right; margin: 10px 0; font-size: 10pt;">
            Klaten, {{ $generated_date->format('d F Y') }}
        </div>

        <div class="signature">
            <div class="signature-box">
                <div class="title-sig">PIHAK PERTAMA</div>
                <div class="company">PT. JARINGAN FIBERONE INDONESIA</div>
                <div class="sign-space">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                </div>
                <div class="name">Ayu Mutiara A.</div>
                <div class="position">Administrasi</div>
            </div>

            <div class="signature-box">
                <div class="title-sig">PIHAK KEDUA</div>
                <div class="company">PELANGGAN</div>
                <div class="sign-space">
                     <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                </div>
                <div class="name">{{ $borrower_name }}</div>
                <div class="position">{{ $borrower_id }}</div>
            </div>
        </div>

    </div>

</body>
</html>
