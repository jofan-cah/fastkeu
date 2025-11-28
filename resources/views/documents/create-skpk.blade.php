@extends('layouts.main')

@section('title', 'Generate Surat Pengalaman Kerja')
@section('subtitle', 'Create Surat Pengalaman Kerja (SKPK)')

@section('content')
<div class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Generate Surat Pengalaman Kerja</h2>
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
            Form ini akan generate PDF Surat Pengalaman Kerja dengan nomor otomatis.
            Setelah di-generate, PDF akan otomatis terdownload dan data tersimpan di database.
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="skpkForm">
            @csrf

            <!-- Section 1: Data Karyawan -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class='bx bx-user'></i> Data Karyawan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Karyawan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap Karyawan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="employee_name"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: Budi Santoso">
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="position"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: Network Engineer">
                    </div>

                    <!-- Departemen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Departemen <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="department"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: IT & Network">
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="start_date"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="end_date"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Section 2: Deskripsi Pekerjaan -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class='bx bx-briefcase'></i> Deskripsi Pekerjaan
                </h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Uraian Tugas & Tanggung Jawab <span class="text-red-500">*</span>
                    </label>
                    <textarea name="job_description"
                              required
                              rows="6"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Contoh:&#10;1. Melakukan instalasi dan maintenance jaringan fiber optic&#10;2. Monitoring kualitas jaringan 24/7&#10;3. Troubleshooting masalah koneksi pelanggan"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Pisahkan setiap tugas dengan enter (baris baru)</p>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Preview Document
function previewDocument() {
    const formData = $('#skpkForm').serialize();

    $.ajax({
        url: '{{ route("previewSkpk") }}',
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
                    $('#skpkForm').submit();
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
$('#skpkForm').on('submit', function(e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
        url: '{{ route("generateSkpk") }}',
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
            let filename = 'SKPK.pdf';

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
