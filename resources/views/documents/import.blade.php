@extends('layouts.main')

@section('title', 'Import Documents')
@section('subtitle', 'Upload Excel file to bulk import documents')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Import Documents from Excel</h2>
        <a href="{{ route('indexDocuments') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class='bx bx-arrow-back'></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class='bx bx-info-circle'></i> Format Excel
        </h3>
        <p class="text-sm text-blue-700 mb-3">Excel harus memiliki kolom berikut (header di row pertama):</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-700">
            <div>✓ <strong>nomor_surat_sales_confirmation</strong></div>
            <div>✓ <strong>nomor_surat_berita_acara</strong></div>
            <div>✓ <strong>nomor_surat_formulir_berlangganan</strong></div>
            <div>✓ <strong>nama</strong> (required)</div>
            <div>✓ <strong>tanggal</strong> (required)</div>
            <div>✓ <strong>keterangan</strong> (optional)</div>
        </div>
        <div class="mt-4">
            <a href="{{ asset('templates/import_documents_template.xlsx') }}"
               class="text-blue-600 hover:text-blue-800 underline text-sm">
                <i class='bx bx-download'></i> Download Template Excel
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="importForm" enctype="multipart/form-data">
            @csrf

            <!-- File Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Select Excel File <span class="text-red-500">*</span>
                </label>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition cursor-pointer"
                     onclick="document.getElementById('fileInput').click()">
                    <i class='bx bx-cloud-upload text-5xl text-gray-400 mb-3'></i>
                    <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500">Excel (XLSX, XLS, CSV) - Max 5MB</p>
                    <input type="file"
                           id="fileInput"
                           name="file"
                           accept=".xlsx,.xls,.csv"
                           required
                           class="hidden"
                           onchange="displayFileName(this)">
                </div>

                <div id="fileInfo" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700 flex items-center gap-2">
                        <i class='bx bx-check-circle'></i>
                        <span id="fileName"></span>
                    </p>
                </div>
            </div>

            <!-- Preview (optional) -->
            <div id="previewSection" class="hidden mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Preview Data:</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">No</th>
                                <th class="border px-2 py-1">Form</th>
                                <th class="border px-2 py-1">Konf</th>
                                <th class="border px-2 py-1">BA</th>
                                <th class="border px-2 py-1">Nama</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('indexDocuments') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class='bx bx-upload'></i>
                    <span>Import Data</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function displayFileName(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB

        $('#fileName').text(`${fileName} (${fileSize} MB)`);
        $('#fileInfo').removeClass('hidden');
    }
}

$('#importForm').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
        url: '{{ route("processImportDocuments") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            Swal.fire({
                title: 'Importing...',
                html: 'Processing Excel file, please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Import Berhasil!',
                html: `
                    <div class="text-left">
                        <p class="mb-3">${response.message}</p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Import Statistics:</p>
                            <div class="space-y-1 text-sm">
                                <p><strong>New:</strong> ${response.stats.imported} documents</p>
                                <p><strong>Updated:</strong> ${response.stats.updated} documents</p>
                                <p><strong>Total:</strong> ${response.stats.total} documents</p>
                                ${response.stats.errors > 0 ? `<p class="text-red-600"><strong>Errors:</strong> ${response.stats.errors}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'View Documents',
            }).then(() => {
                window.location.href = '{{ route("indexDocuments") }}';
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
});
</script>
@endpush
