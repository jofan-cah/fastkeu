<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Kesepakatan Perubahan Layanan</title>
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
            font-size: 9pt;
            line-height: 1.3;
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

        /* Content overlay - OPTIMIZED untuk 1 halaman */
        .content-wrapper {
            position: relative;
            padding: 30mm 15mm 15mm 15mm;
            z-index: 1;
        }

        .doc-number {
            text-align: center;
            margin-bottom: 8px;
            font-size: 9pt;
            font-weight: bold;
        }

        .opening {
            text-align: justify;
            margin: 6px 0;
            font-size: 8.5pt;
            line-height: 1.2;
        }

        .party-info {
            margin: 6px 0;
            font-size: 8.5pt;
            line-height: 1.2;
        }

        .party-info .party-title {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .party-info .party-content {
            margin-left: 15px;
        }

        .party-info .label {
            display: inline-block;
            width: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 8pt;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
        }

        table th {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            margin: 6px 0 3px 0;
            font-size: 8.5pt;
        }

        .syarat-kondisi {
            margin: 5px 0;
            font-size: 8.5pt;
        }

        .syarat-kondisi ul {
            margin: 2px 0 0 15px;
            padding: 0;
        }

        .syarat-kondisi li {
            margin: 1px 0;
            line-height: 1.2;
        }

        .closing {
            text-align: justify;
            margin: 6px 0;
            font-size: 8.5pt;
            line-height: 1.2;
        }

        /* Signature - COMPACT */
        .signature {
            margin-top: 15px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 5px;
        }

        .signature-box .title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 2px;
        }

        .signature-box .company {
            font-weight: bold;
            margin: 2px 0;
            font-size: 8pt;
        }

        .signature-box .sign-space {
            height: 40px;
            margin: 5px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-box .sign-space img {
            max-height: 35px;
            max-width: 120px;
        }

        .signature-box .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 120px;
            padding-bottom: 2px;
            font-size: 9pt;
        }

        .signature-box .position {
            font-size: 8pt;
            margin-top: 2px;
        }

        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }

        .qr-section img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto 5px auto;
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

        <!-- Document Number -->
        <div class="doc-number">
            BERITA ACARA KESEPAKATANPERUBAHANLAYANAN

        </div>

        <!-- Opening -->
        <div class="opening">
            Pada hari ini <strong>{{ $hariTanggal }}</strong> telah dilakukan kesepakatan biaya berlangganan atas Jaringan Telekomunikasi oleh dan antara:
        </div>
        <div class="opening">
          No. Berita Acara : {{ $docNumber }}
        </div>

        <!-- Pihak Pertama -->
        <div class="party-info">
            <div class="party-title">PIHAK PERTAMA</div>
            <div class="party-content">
                <span class="label"></span>: PT. JARINGAN FIBERONE INDONESIA<br>
                <span class="label">Diwakili Oleh</span>: {{ $pihakPertama['nama'] }}<br>
                <span class="label">Jabatan</span>: {{ $pihakPertama['jabatan'] }}<br>
                <span class="label">Nomor Telepon</span>: {{ $pihakPertama['telepon'] }}
            </div>
        </div>

        <!-- Pihak Kedua -->
        <div class="party-info">
            <div class="party-title">PIHAK KEDUA</div>
            <div class="party-content">
                <span class="label">Diwakili Oleh</span>: {{ $pihakKedua['nama'] }}<br>
                <span class="label">ID Pelanggan</span>: {{ $pihakKedua['id_pelanggan'] }}<br>
                <span class="label">Nomor Telepon</span>: {{ $pihakKedua['telepon'] }}
            </div>
        </div>

        <!-- Content -->
        <div class="opening">
            Bahwa <strong>PIHAK KEDUA</strong> sepakat untuk menggunakan Jaringan Telekomunikasi dari <strong>PIHAK PERTAMA</strong>, sesuai dengan ketentuan dibawah ini:
        </div>

        <!-- Bandwidth Awal -->
        <div class="section-title">Bandwidth Awal:</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Jenis Layanan</th>
                    <th style="width: 70px;">Kapasitas</th>
                    <th style="width: 110px;">Biaya Berlangganan<br>(Rp) / Montly</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>{{ $bandwidthAwal['jenis_layanan'] }}</td>
                    <td>{{ $bandwidthAwal['kapasitas'] }}</td>
                    <td style="text-align: right;">Rp. {{ number_format($bandwidthAwal['biaya'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>PPn</strong></td>
                    <td style="text-align: right;">Rp. {{ number_format($bandwidthAwal['ppn'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                    <td style="text-align: right;"><strong>Rp. {{ number_format($bandwidthAwal['total'], 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Bandwidth Sekarang -->
        <div class="section-title">Bandwidth Sekarang:</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Jenis Layanan</th>
                    <th style="width: 70px;">Kapasitas</th>
                    <th style="width: 110px;">Biaya Berlangganan<br>(Rp) / Montly</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>{{ $bandwidthSekarang['jenis_layanan'] }}</td>
                    <td>{{ $bandwidthSekarang['kapasitas'] }}</td>
                    <td style="text-align: right;">Rp. {{ number_format($bandwidthSekarang['biaya'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>PPn</strong></td>
                    <td style="text-align: right;">Rp. {{ number_format($bandwidthSekarang['ppn'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                    <td style="text-align: right;"><strong>Rp. {{ number_format($bandwidthSekarang['total'], 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Syarat dan Kondisi -->
        <div class="syarat-kondisi">
            <strong>Syarat dan Kondisi:</strong>
            <ul style="list-style: none; padding: 0; margin-left: 10px;">
                @foreach($syaratKondisi as $syarat)
                <li>− {{ $syarat }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Closing -->
        <div class="closing">
            Demikian Berita Acara Kesepakatan Perubahan Layanan ini dibuat dalam rangkap 2 (dua), asli dan mempunyai kekuatan hukum yang sama setelah ditandai dan merupakan bagian tidak terpisahkan dari Perjanjian antara <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong>.
        </div>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-box">
                <div class="title">PIHAK PERTAMA</div>
                <div class="company">PT. JARINGAN FIBERONE INDONESIA</div>
                <div class="sign-space">
                      <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                </div>
                <div class="name">{{ $pihakPertama['nama'] }}</div>
                <div class="position">{{ $pihakPertama['jabatan'] }}</div>
            </div>

            <div class="signature-box">
                <div class="title">PIHAK KEDUA</div>
                <div class="company">PELANGGAN</div>
                <div class="sign-space"></div>
                <div class="name">{{ $pihakKedua['nama'] }}</div>
                <div class="position">{{ $pihakKedua['id_pelanggan'] }}</div>
            </div>
        </div>
        
    </div>

</body>
</html>
