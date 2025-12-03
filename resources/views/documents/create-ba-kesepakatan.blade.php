@extends('layouts.main')

@section('title', 'Generate BA Kesepakatan Perubahan Layanan')
@section('subtitle', 'Create Berita Acara Kesepakatan Perubahan Layanan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            padding-left: 16px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

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

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <!-- Pilih Subscription -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Subscription <span class="text-red-500">*</span>
                            </label>
                            <select id="subscription_id" name="subscription_id" class="w-full" required>
                                <option value="">-- Pilih Subscription --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Pelanggan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_name" name="customer_name" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari subscription">
                        </div>

                        <!-- ID Pelanggan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ID Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_id" name="customer_id" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari subscription">
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_phone" name="customer_phone" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari subscription">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bandwidth Awal -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class='bx bx-wifi'></i> Bandwidth Awal
                    </h3>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <!-- Pilih Paket Awal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Paket Awal <span class="text-red-500">*</span>
                            </label>
                            <select id="paket_awal_id" name="paket_awal_id" class="w-full" required>
                                <option value="">-- Pilih Paket Awal --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Jenis Layanan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="bandwidth_awal_jenis" name="bandwidth_awal_jenis" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari paket">
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kapasitas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="bandwidth_awal_kapasitas" name="bandwidth_awal_kapasitas" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari paket">
                        </div>

                        <!-- Biaya -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Biaya (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bandwidth_awal_biaya" name="bandwidth_awal_biaya" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari paket">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Bandwidth Sekarang -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                        <i class='bx bx-wifi-2'></i> Bandwidth Sekarang (Upgrade)
                    </h3>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <!-- Pilih Paket Sekarang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Paket Sekarang (Upgrade) <span class="text-red-500">*</span>
                            </label>
                            <select id="paket_sekarang_id" name="paket_sekarang_id" class="w-full" required>
                                <option value="">-- Pilih Paket Sekarang --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Jenis Layanan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="bandwidth_sekarang_jenis" name="bandwidth_sekarang_jenis" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari paket">
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kapasitas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="bandwidth_sekarang_kapasitas" name="bandwidth_sekarang_kapasitas" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari paket">
                        </div>

                        <!-- Biaya -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Biaya (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bandwidth_sekarang_biaya" name="bandwidth_sekarang_biaya" required readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Otomatis terisi dari paket">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for Subscriptions
            $('#subscription_id').select2({
                placeholder: '-- Pilih Subscription --',
                allowClear: true,
                ajax: {
                    url: '{{ route('befast.subscriptions.dropdown') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            limit: 50
                        };
                    },
                    processResults: function(response) {
                        if (response.success) {
                            return {
                                results: response.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text,  // Already formatted from API: "Name (ID)"
                                        data: item
                                    };
                                })
                            };
                        }
                        return { results: [] };
                    },
                    cache: true
                }
            });

            // Auto-fill customer data when subscription is selected
            $('#subscription_id').on('select2:select', function(e) {
                const data = e.params.data.data;
                // Extract name from text format "Name (ID)"
                let customerName = data.text || '';
                if (customerName.includes('(')) {
                    customerName = customerName.substring(0, customerName.lastIndexOf('(')).trim();
                }
                $('#customer_name').val(customerName);
                $('#customer_id').val(data.customer_id || '');
                $('#customer_phone').val(data.phone || '');
            });

            // Clear customer data when subscription is cleared
            $('#subscription_id').on('select2:clear', function() {
                $('#customer_name').val('');
                $('#customer_id').val('');
                $('#customer_phone').val('');
            });

            // Initialize Select2 for Paket Awal
            $('#paket_awal_id').select2({
                placeholder: '-- Pilih Paket Awal --',
                allowClear: true,
                ajax: {
                    url: '{{ route('befast.pakets.dropdown') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            status: 'active',
                            search: params.term
                        };
                    },
                    processResults: function(response) {
                        if (response.success) {
                            return {
                                results: response.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.nama_paket + ' - ' + item.speed,
                                        data: item
                                    };
                                })
                            };
                        }
                        return { results: [] };
                    },
                    cache: true
                }
            });

            // Auto-fill paket awal data when selected
            $('#paket_awal_id').on('select2:select', function(e) {
                const data = e.params.data.data;
                $('#bandwidth_awal_jenis').val(data.nama_paket || '');
                $('#bandwidth_awal_kapasitas').val(data.speed || '');
                $('#bandwidth_awal_biaya').val(data.price || '');
            });

            // Clear paket awal data when cleared
            $('#paket_awal_id').on('select2:clear', function() {
                $('#bandwidth_awal_jenis').val('');
                $('#bandwidth_awal_kapasitas').val('');
                $('#bandwidth_awal_biaya').val('');
            });

            // Initialize Select2 for Paket Sekarang
            $('#paket_sekarang_id').select2({
                placeholder: '-- Pilih Paket Sekarang --',
                allowClear: true,
                ajax: {
                    url: '{{ route('befast.pakets.dropdown') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            status: 'active',
                            search: params.term
                        };
                    },
                    processResults: function(response) {
                        if (response.success) {
                            return {
                                results: response.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.nama_paket + ' - ' + item.speed,
                                        data: item
                                    };
                                })
                            };
                        }
                        return { results: [] };
                    },
                    cache: true
                }
            });

            // Auto-fill paket sekarang data when selected
            $('#paket_sekarang_id').on('select2:select', function(e) {
                const data = e.params.data.data;
                $('#bandwidth_sekarang_jenis').val(data.nama_paket || '');
                $('#bandwidth_sekarang_kapasitas').val(data.speed || '');
                $('#bandwidth_sekarang_biaya').val(data.price || '');
            });

            // Clear paket sekarang data when cleared
            $('#paket_sekarang_id').on('select2:clear', function() {
                $('#bandwidth_sekarang_jenis').val('');
                $('#bandwidth_sekarang_kapasitas').val('');
                $('#bandwidth_sekarang_biaya').val('');
            });
        });

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
