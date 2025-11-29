<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen - FASTKEU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-block bg-white p-4 rounded-full shadow-lg mb-4">
                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Validasi Dokumen</h1>
            <p class="text-gray-600">PT Jaringan FiberOne Indonesia</p>
        </div>

        <!-- Validation Result Card -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">

            @if($valid)
                <!-- Valid Document -->
                <div class="bg-green-500 text-white p-4 text-center">
                    <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-2xl font-bold">Dokumen Valid</h2>
                    <p class="text-green-100 mt-1">Dokumen ini sah dikeluarkan oleh PT Jaringan FiberOne Indonesia</p>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Nomor Dokumen -->
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">Nomor Dokumen</label>
                            <p class="text-lg font-bold text-gray-800">{{ $document->doc_number }}</p>
                        </div>

                        <!-- Tipe Dokumen -->
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">Tipe Dokumen</label>
                            <p class="text-gray-800 font-semibold">{{ $document->documentType->name }}</p>
                        </div>

                        <!-- Customer Name -->
                        @if($document->customer_name)
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">Nama Pelanggan/Pihak Terkait</label>
                            <p class="text-gray-800">{{ $document->customer_name }}</p>
                        </div>
                        @endif

                        <!-- Subscription ID -->
                        @if($document->subscription_id)
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">ID Pelanggan</label>
                            <p class="text-gray-800">{{ $document->subscription_id }}</p>
                        </div>
                        @endif

                        <!-- Tanggal Dokumen -->
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">Tanggal Dokumen</label>
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($document->document_date)->translatedFormat('d F Y') }}</p>
                        </div>

                        <!-- Status -->
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">Status</label>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                @if($document->status == 'generated') bg-blue-100 text-blue-800
                                @elseif($document->status == 'printed') bg-yellow-100 text-yellow-800
                                @elseif($document->status == 'signed') bg-green-100 text-green-800
                                @elseif($document->status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($document->status) }}
                            </span>
                        </div>

                        <!-- Dibuat Oleh -->
                        <div class="border-b pb-3">
                            <label class="text-sm text-gray-500 block mb-1">Dibuat Oleh</label>
                            <p class="text-gray-800">{{ $document->creator->full_name ?? 'N/A' }}</p>
                        </div>

                        <!-- Tanggal Dibuat -->
                        <div>
                            <label class="text-sm text-gray-500 block mb-1">Tanggal Dibuat</label>
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($document->created_at)->translatedFormat('d F Y H:i') }} WIB</p>
                        </div>

                        <!-- Notes -->
                        @if($document->notes)
                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <label class="text-sm text-gray-500 block mb-2">Catatan</label>
                            <p class="text-gray-700 text-sm">{{ $document->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Pernyataan -->
                    <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <p class="text-sm text-blue-800">
                            <strong>Pernyataan:</strong> Dokumen ini menyatakan bahwa data diambil secara sah dan tanpa tekanan dari pihak manapun.
                        </p>
                    </div>
                </div>

            @else
                <!-- Invalid Document -->
                <div class="bg-red-500 text-white p-4 text-center">
                    <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-2xl font-bold">Dokumen Tidak Valid</h2>
                    <p class="text-red-100 mt-1">{{ $message }}</p>
                </div>

                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800 mb-2">
                            <strong>Nomor Dokumen yang Anda cari:</strong>
                        </p>
                        <p class="text-lg font-mono text-red-900">{{ $docNumber }}</p>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-gray-600 text-sm">
                            Jika Anda yakin nomor dokumen benar, silakan hubungi:
                        </p>
                        <p class="text-blue-600 font-semibold mt-2">PT Jaringan FiberOne Indonesia</p>
                        <p class="text-gray-600 text-sm mt-1">Email: admin@fiberone.id</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-600 text-sm">
                &copy; {{ date('Y') }} PT Jaringan FiberOne Indonesia
            </p>
            <p class="text-gray-500 text-xs mt-1">
                Sistem Validasi Dokumen FASTKEU
            </p>
        </div>
    </div>

</body>
</html>
