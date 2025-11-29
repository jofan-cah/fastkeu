<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengalaman Kerja</title>
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
            font-size: 11pt;
            line-height: 1.6;
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
            padding: 35mm 20mm 20mm 20mm;
            z-index: 1;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .doc-number {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 20px;
        }

        .content {
            text-align: justify;
            margin: 15px 0;
            font-size: 11pt;
        }

        .content p {
            margin: 10px 0;
        }

        .employee-info {
            margin: 15px 0 15px 30px;
            font-size: 11pt;
        }

        .employee-info table {
            border: none;
            width: 100%;
        }

        .employee-info td {
            padding: 3px 0;
            border: none;
            vertical-align: top;
        }

        .employee-info .label {
            width: 150px;
        }

        .employee-info .separator {
            width: 20px;
            text-align: center;
        }

        .job-description {
            margin: 15px 0 15px 30px;
            font-size: 11pt;
        }

        .job-description ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        .job-description li {
            margin: 3px 0;
        }

        .closing {
            margin: 20px 0;
            font-size: 11pt;
            text-align: justify;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
            padding-right: 50px;
        }

        .signature .date {
            margin-bottom: 10px;
        }

        .signature .position {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature .company {
            font-weight: bold;
        }

        .signature .sign-space {
            height: 60px;
            margin: 10px 0;
        }

        .signature .sign-space img {
            max-height: 50px;
        }

        .signature .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 3px;
        }

        .signature .title-position {
            margin-top: 3px;
        }

        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }

        .qr-section img {
            width: 100px;
            height: 100px;
            display: block;
            margin: 0 auto 8px auto;
        }

        .qr-section .qr-label {
            font-size: 8pt;
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
            SURAT PENGALAMAN KERJA
        </div>

        <!-- Document Number -->
        <div class="doc-number">
            No: {{ $docNumber }}
        </div>

        <!-- Opening -->
        <div class="content">
            <p>Yang bertanda tangan di bawah ini:</p>
        </div>

        <!-- Company Info -->
        <div class="employee-info">
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
                    <td>PT. Jaringan FiberOne Indonesia</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td>Griya Permata Hijau, Jl. Mpu Sedah No.01 Blok A, Gatak, Sumberrejo, Klaten Selatan, Klaten, Jawa Tengah 57422</td>
                </tr>
            </table>
        </div>

        <!-- Statement -->
        <div class="content">
            <p>Dengan ini menerangkan bahwa:</p>
        </div>

        <!-- Employee Info -->
        <div class="employee-info">
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td><strong>{{ $employee_name }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="separator">:</td>
                    <td>{{ $position }}</td>
                </tr>
                <tr>
                    <td class="label">Departemen</td>
                    <td class="separator">:</td>
                    <td>{{ $department }}</td>
                </tr>
                <tr>
                    <td class="label">Periode Kerja</td>
                    <td class="separator">:</td>
                    <td>{{ $start_date->format('d F Y') }} s/d {{ $end_date->format('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <!-- Job Description -->
        <div class="content">
            <p>Selama bekerja di perusahaan kami, yang bersangkutan telah melaksanakan tugas dan tanggung jawab sebagai berikut:</p>
        </div>

        <div class="job-description">
            <ul>
                @foreach(explode("\n", $job_description) as $task)
                    @if(trim($task))
                    <li>{{ trim($task) }}</li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Closing -->
        <div class="closing">
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Signature -->
        <div class="signature">
            <div class="date">
                Klaten, {{ $generated_date->format('d F Y') }}
            </div>
            <div class="position">PT. Jaringan FiberOne Indonesia</div>
            <div class="company">Administrasi</div>
            <div class="sign-space">
               <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            </div>
            <div class="name">Ayu Mutiara A.</div>
        </div>

    </div>

</body>
</html>
