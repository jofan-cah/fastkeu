@extends('layouts.main')

@section('title', 'Generate BA Peminjaman Perangkat')
@section('subtitle', 'Create Berita Acara Peminjaman Perangkat')

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
<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Generate BA Peminjaman Perangkat</h2>
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
            Form ini akan generate PDF Berita Acara Peminjaman Perangkat dengan nomor otomatis.
            Tambahkan daftar perangkat yang dipinjam dengan menekan tombol "Tambah Perangkat".
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="baPeminjamanForm">
            @csrf

            <!-- Section 1: Data Peminjam (Pihak Kedua) -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class='bx bx-user'></i> Data Peminjam (Pihak Kedua)
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
                    <!-- Nama Peminjam -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Peminjam <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="borrower_name"
                               name="borrower_name"
                               required
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Otomatis terisi dari subscription">
                    </div>

                    <!-- Nama Usaha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Usaha/Tempat <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="borrower_business"
                               name="borrower_business"
                               required
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Otomatis terisi dari subscription">
                    </div>

                    <!-- ID Pelanggan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ID Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="borrower_id"
                               name="borrower_id"
                               required
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Otomatis terisi dari subscription">
                    </div>

                    <!-- No HP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor HP <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="borrower_phone"
                               name="borrower_phone"
                               required
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Otomatis terisi dari subscription">
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea id="borrower_address"
                                  name="borrower_address"
                                  required
                                  readonly
                                  rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Otomatis terisi dari subscription"></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Daftar Perangkat -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class='bx bx-package'></i> Daftar Perangkat yang Dipinjam
                    </h3>
                    <button type="button"
                            onclick="addItem()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition text-sm">
                        <i class='bx bx-plus'></i>
                        <span>Tambah Perangkat</span>
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">No</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Nama Barang</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Jumlah</th>
                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-semibold w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsList">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 3: Ketentuan Peminjaman -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class='bx bx-list-check'></i> Ketentuan Peminjaman
                </h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ketentuan & Syarat Peminjaman <span class="text-red-500">*</span>
                    </label>
                    <textarea name="loan_terms"
                              required
                              rows="6"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Contoh:&#10;- Segala bentuk kerusakan pada perangkat selama masa peminjaman menjadi tanggung jawab Pihak Pertama&#10;- Apabila terjadi kehilangan perangkat, hal tersebut menjadi tanggung jawab Pihak Kedua&#10;- Peminjaman perangkat ini hanya digunakan sesuai dengan kebutuhan yang telah disepakati"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Pisahkan setiap ketentuan dengan enter (baris baru) dan gunakan tanda "-" di awal</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('indexDocuments') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>

                <button type="button"
                        onclick="previewDocument()"
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

    // Auto-fill borrower data when subscription is selected
    $('#subscription_id').on('select2:select', function(e) {
        const data = e.params.data.data;
        // Extract name from text format "Name (ID)"
        let borrowerName = data.text || '';
        if (borrowerName.includes('(')) {
            borrowerName = borrowerName.substring(0, borrowerName.lastIndexOf('(')).trim();
        }
        $('#borrower_name').val(borrowerName);
        $('#borrower_business').val(borrowerName);  // Use same name as business
        $('#borrower_id').val(data.customer_id || '');
        $('#borrower_phone').val(data.phone || '');
        $('#borrower_address').val('');  // Not available in dropdown

        // Fetch full details for address if needed
        if (data.id) {
            $.get('{{ url('/api/befast/subscriptions') }}/' + data.id, function(response) {
                if (response.success && response.data) {
                    $('#borrower_address').val(response.data.address || '');
                }
            });
        }
    });

    // Clear borrower data when subscription is cleared
    $('#subscription_id').on('select2:clear', function() {
        $('#borrower_name').val('');
        $('#borrower_business').val('');
        $('#borrower_id').val('');
        $('#borrower_phone').val('');
        $('#borrower_address').val('');
    });
});

let itemCounter = 0;

// Add Item Row
function addItem() {
    itemCounter++;

    const html = `
        <tr id="item-${itemCounter}">
            <td class="border border-gray-300 px-4 py-2 text-center">${itemCounter}</td>
            <td class="border border-gray-300 px-4 py-2">
                <input type="text"
                       name="items[${itemCounter}][name]"
                       required
                       class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: Mikrotik E50ug Routerboard Hex">
            </td>
            <td class="border border-gray-300 px-4 py-2">
                <input type="number"
                       name="items[${itemCounter}][quantity]"
                       required
                       min="1"
                       value="1"
                       class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="1">
            </td>
            <td class="border border-gray-300 px-4 py-2 text-center">
                <button type="button"
                        onclick="removeItem(${itemCounter})"
                        class="text-red-600 hover:text-red-800">
                    <i class='bx bx-trash text-xl'></i>
                </button>
            </td>
        </tr>
    `;

    $('#itemsList').append(html);
    updateItemNumbers();
}

// Remove Item Row
function removeItem(id) {
    Swal.fire({
        title: 'Hapus Perangkat?',
        text: 'Perangkat ini akan dihapus dari daftar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $(`#item-${id}`).remove();
            updateItemNumbers();
        }
    });
}

// Update Item Numbers
function updateItemNumbers() {
    $('#itemsList tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

// Add first item on page load
$(document).ready(function() {
    addItem();
});

// Preview Document
function previewDocument() {
    const formData = $('#baPeminjamanForm').serializeArray();

    // Convert to JSON
    const jsonData = {};
    const items = [];

    formData.forEach(item => {
        if (item.name.startsWith('items[')) {
            const match = item.name.match(/items\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const index = match[1];
                const field = match[2];

                if (!items[index]) {
                    items[index] = {};
                }
                items[index][field] = item.value;
            }
        } else {
            jsonData[item.name] = item.value;
        }
    });

    jsonData.items = items.filter(e => e); // Remove empty elements

    $.ajax({
        url: '{{ route("previewBaPeminjaman") }}',
        type: 'POST',
        data: jsonData,
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
                    $('#baPeminjamanForm').submit();
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

// Submit Form
$('#baPeminjamanForm').on('submit', function(e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
        url: '{{ route("generateBaPeminjaman") }}',
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
            let filename = 'BA-Peminjaman.pdf';

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
                window.location.href = '{{ route("indexDocuments") }}';
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
.swal-wide {
    width: 90% !important;
}
</style>
@endpush
