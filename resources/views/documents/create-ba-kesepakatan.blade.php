@extends('layouts.main')

@section('title', 'Generate BA Kesepakatan Perubahan Layanan')
@section('subtitle', 'Create Berita Acara Kesepakatan Perubahan Layanan')

@section('content')
    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Generate BA Kesepakatan Perubahan Layanan</h2>
            <a href="{{ route('indexDocuments') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class='bx bx-arrow-back'></i>
                <span>Back</span>
            </a>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">
                <i class='bx bx-info-circle'></i> Informasi
            </h3>
            <p class="text-sm text-blue-700">
                Form ini akan generate PDF Berita Acara Kesepakatan Perubahan Layanan dengan nomor otomatis.
                Setelah di-generate, PDF akan otomatis terdownload dan data tersimpan di database.
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form id="baKespakatanForm">
                @csrf

                <!-- Section 1: Data Pelanggan -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class='bx bx-user'></i> Data Pelanggan (Pihak Kedua)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Pelanggan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Ady Korniawan">
                        </div>

                        <!-- ID Pelanggan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ID Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: F1KLN569">
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_phone" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: +62 822-2572-9825">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bandwidth Awal -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class='bx bx-wifi'></i> Bandwidth Awal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Jenis Layanan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bandwidth_awal_jenis" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: HEKTO">
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kapasitas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bandwidth_awal_kapasitas" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 20 Mbps">
                        </div>

                        <!-- Biaya -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Biaya (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="bandwidth_awal_biaya" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 180000">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Bandwidth Sekarang -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class='bx bx-wifi-2'></i> Bandwidth Sekarang (Upgrade)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Jenis Layanan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bandwidth_sekarang_jenis" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: SMALL DEKA">
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kapasitas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bandwidth_sekarang_kapasitas" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 20 Mbps">
                        </div>

                        <!-- Biaya -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Biaya (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="bandwidth_sekarang_biaya" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 375000">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Info Tambahan -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class='bx bx-calendar'></i> Informasi Tambahan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Starting Billing -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Starting Billing <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="starting_billing" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('indexDocuments') }}"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="button" onclick="previewDocument()"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                        <i class='bx bx-show'></i>
                        <span>Preview Document</span>
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class='bx bx-download'></i>
                        <span>Generate & Download PDF</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('#baKespakatanForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route('generateBaKesepakatan') }}',
                type: 'POST',
                data: formData,
                xhrFields: {
                    responseType: 'blob' // Important untuk download file
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Generating PDF...',
                        html: 'Please wait while we generate your document',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(blob, status, xhr) {
                    // Get filename dari response header
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    let filename = 'BA-Kesepakatan.pdf';

                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    // Download file
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    a.remove();

                    // Close loading & show success
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'PDF berhasil di-generate dan tersimpan',
                        confirmButtonText: 'OK',
                    }).then(() => {
                        window.location.href = '{{ route('indexDocuments') }}';
                    });
                },
                error: function(xhr) {
                    Swal.close();

                    // Parse blob error response
                    const reader = new FileReader();
                    reader.onload = function() {
                        try {
                            const response = JSON.parse(reader.result);

                            if (xhr.status === 422) {
                                const errors = response.errors;
                                let errorMessage = '<ul class="text-left">';
                                Object.keys(errors).forEach(key => {
                                    errors[key].forEach(error => {
                                        errorMessage += `<li>• ${error}</li>`;
                                    });
                                });
                                errorMessage += '</ul>';

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal',
                                    html: errorMessage
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Terjadi kesalahan'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal generate PDF'
                            });
                        }
                    };
                    reader.readAsText(xhr.responseText);
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ✅ FUNCTION: Preview Document
        function previewDocument() {
            const formData = $('#baKespakatanForm').serialize();

            $.ajax({
                url: '{{ route('previewBaKesepakatan') }}',
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Loading Preview...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(html) {
                    Swal.close();

                    // ✅ Show preview dalam modal
                    Swal.fire({
                        title: 'Preview Document',
                        html: `
                    <div style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd;">
                        <iframe srcdoc="${html.replace(/"/g, '&quot;')}"
                                style="width: 100%; height: 800px; border: none;">
                        </iframe>
                    </div>
                `,
                        width: '90%',
                        showConfirmButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-download"></i> Download PDF',
                        cancelButtonText: 'Close',
                        confirmButtonColor: '#3B82F6',
                        customClass: {
                            popup: 'swal-wide'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kalau user klik "Download PDF", submit form
                            $('#baKespakatanForm').submit();
                        }
                    });
                },
                error: function(xhr) {
                    Swal.close();

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = '<ul class="text-left">';
                        Object.keys(errors).forEach(key => {
                            errors[key].forEach(error => {
                                errorMessage += `<li>• ${error}</li>`;
                            });
                        });
                        errorMessage += '</ul>';

                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: errorMessage
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                }
            });
        }

        // FUNCTION: Submit Form (Download PDF)
        $('#baKespakatanForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route('generateBaKesepakatan') }}',
                type: 'POST',
                data: formData,
                xhrFields: {
                    responseType: 'blob'
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Generating PDF...',
                        html: 'Please wait while we generate your document',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(blob, status, xhr) {
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    let filename = 'BA-Kesepakatan.pdf';

                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    a.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'PDF berhasil di-generate dan tersimpan',
                        confirmButtonText: 'OK',
                    }).then(() => {
                        window.location.href = '{{ route('indexDocuments') }}';
                    });
                },
                error: function(xhr) {
                    Swal.close();

                    const reader = new FileReader();
                    reader.onload = function() {
                        try {
                            const response = JSON.parse(reader.result);

                            if (xhr.status === 422) {
                                const errors = response.errors;
                                let errorMessage = '<ul class="text-left">';
                                Object.keys(errors).forEach(key => {
                                    errors[key].forEach(error => {
                                        errorMessage += `<li>• ${error}</li>`;
                                    });
                                });
                                errorMessage += '</ul>';

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal',
                                    html: errorMessage
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Terjadi kesalahan'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal generate PDF'
                            });
                        }
                    };
                    reader.readAsText(xhr.responseText);
                }
            });
        });
    </script>

    <style>
        /* ✅ Custom style untuk modal preview */
        .swal-wide {
            width: 90% !important;
        }
    </style>
@endpush
