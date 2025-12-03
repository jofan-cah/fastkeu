@extends('layouts.main')

@section('title', 'Generate Surat Pernyataan PHK')
@section('subtitle', 'Create Surat Pernyataan Tanggung Jawab Mutlak PHK')

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
        <h2 class="text-xl font-bold text-gray-800">Generate Surat Pernyataan PHK</h2>
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
            Form ini akan generate PDF Surat Pernyataan Tanggung Jawab Mutlak Pelaporan PHK dengan nomor otomatis.
            Tambahkan data karyawan yang di-PHK dengan menekan tombol "Tambah Karyawan".
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="suratPhkForm">
            @csrf

            <!-- Section 1: Data Direktur -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class='bx bx-user-circle'></i> Data Direktur Perusahaan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Nama Direktur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap Direktur <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="director_name"
                               required
                               value="Arief Nur Huda"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: Arief Nur Huda">
                    </div>

                    <!-- No HP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor HP Direktur <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="director_phone"
                               required
                               value="081564640708"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: 081564640708">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Direktur <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="director_email"
                               required
                               value="jaringanfiberoneindonesia@gmail.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: email@company.com">
                    </div>
                </div>
            </div>

            <!-- Section 2: Data Karyawan yang di-PHK -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class='bx bx-user-x'></i> Data Karyawan yang di-PHK
                    </h3>
                    <button type="button"
                            onclick="addEmployee()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition text-sm">
                        <i class='bx bx-plus'></i>
                        <span>Tambah Karyawan</span>
                    </button>
                </div>

                <div id="employeesList" class="space-y-4">
                    <!-- Employee rows will be added here -->
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
let employeeCounter = 0;

// Add Employee Row
function addEmployee() {
    employeeCounter++;

    const html = `
        <div class="employee-row bg-gray-50 p-4 rounded-lg border border-gray-200" id="employee-${employeeCounter}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-gray-700">Karyawan #${employeeCounter}</h4>
                <button type="button"
                        onclick="removeEmployee(${employeeCounter})"
                        class="text-red-600 hover:text-red-800">
                    <i class='bx bx-trash text-xl'></i>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Karyawan <span class="text-red-500">*</span>
                    </label>
                    <select id="karyawan-select-${employeeCounter}"
                            class="karyawan-select w-full"
                            data-counter="${employeeCounter}">
                        <option value="">-- Pilih Karyawan --</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Karyawan <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="employee-name-${employeeCounter}"
                           name="employees[${employeeCounter}][name]"
                           required
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Otomatis terisi dari karyawan">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Noka Peserta <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="employee-noka-${employeeCounter}"
                           name="employees[${employeeCounter}][noka]"
                           required
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Otomatis terisi dari karyawan">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="employee-phone-${employeeCounter}"
                           name="employees[${employeeCounter}][phone]"
                           required
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Otomatis terisi dari karyawan">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan/Jenis PHK <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="employee-reason-${employeeCounter}"
                           name="employees[${employeeCounter}][reason]"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Pemutusan Hubungan Kerja">
                </div>
            </div>
        </div>
    `;

    $('#employeesList').append(html);

    // Initialize Select2 for the new employee row
    initializeKaryawanSelect(employeeCounter);
}

// Initialize Select2 for Karyawan dropdown
function initializeKaryawanSelect(counter) {
    $(`#karyawan-select-${counter}`).select2({
        placeholder: '-- Pilih Karyawan --',
        allowClear: true,
        ajax: {
            url: '{{ route('befast.karyawan.dropdown') }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    employment_status: 'inactive',  // For PHK, we want inactive employees
                    limit: 50
                };
            },
            processResults: function(response) {
                if (response.success) {
                    return {
                        results: response.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text,  // Already formatted as "full_name (nip)"
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

    // Auto-fill employee data when selected
    $(`#karyawan-select-${counter}`).on('select2:select', function(e) {
        const data = e.params.data.data;
        const counter = $(this).data('counter');

        // Extract name from text format "Name (NIP)"
        let employeeName = data.text || '';
        if (employeeName.includes('(')) {
            employeeName = employeeName.substring(0, employeeName.lastIndexOf('(')).trim();
        }

        $(`#employee-name-${counter}`).val(employeeName);
        $(`#employee-noka-${counter}`).val(data.nip || '');  // NIP sebagai noka
        $(`#employee-phone-${counter}`).val(data.phone || '');
    });

    // Clear employee data when cleared
    $(`#karyawan-select-${counter}`).on('select2:clear', function() {
        const counter = $(this).data('counter');

        $(`#employee-name-${counter}`).val('');
        $(`#employee-noka-${counter}`).val('');
        $(`#employee-phone-${counter}`).val('');
    });
}

// Remove Employee Row
function removeEmployee(id) {
    Swal.fire({
        title: 'Hapus Karyawan?',
        text: 'Data karyawan ini akan dihapus dari form',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $(`#employee-${id}`).remove();
        }
    });
}

// Add first employee on page load
$(document).ready(function() {
    addEmployee();
});

// Preview Document
function previewDocument() {
    const formData = $('#suratPhkForm').serializeArray();

    // Convert to JSON
    const jsonData = {};
    const employees = [];

    formData.forEach(item => {
        if (item.name.startsWith('employees[')) {
            const match = item.name.match(/employees\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const index = match[1];
                const field = match[2];

                if (!employees[index]) {
                    employees[index] = {};
                }
                employees[index][field] = item.value;
            }
        } else {
            jsonData[item.name] = item.value;
        }
    });

    jsonData.employees = employees.filter(e => e); // Remove empty elements

    $.ajax({
        url: '{{ route("previewSuratPhk") }}',
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
                    $('#suratPhkForm').submit();
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
$('#suratPhkForm').on('submit', function(e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
        url: '{{ route("generateSuratPhk") }}',
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
            let filename = 'Surat-PHK.pdf';

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
