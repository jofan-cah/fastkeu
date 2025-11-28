@extends('layouts.main')

@section('title', 'Create Document')
@section('subtitle', 'Generate new document with auto-numbering')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Create New Document</h2>
        <a href="{{ route('indexDocuments') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class='bx bx-arrow-back'></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="createForm" enctype="multipart/form-data">
            @csrf

            <!-- Document Type -->
            <div class="mb-4">
                <label for="doc_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Document Type <span class="text-red-500">*</span>
                </label>
                <select id="doc_type_id"
                        name="doc_type_id"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Document Type --</option>
                    @foreach($documentTypes as $type)
                    <option value="{{ $type->doc_type_id }}"
                            data-prefix="{{ $type->prefix }}"
                            data-format="{{ $type->format_code }}"
                            data-counter="{{ $type->current_number }}">
                        {{ $type->name }} ({{ $type->code }})
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih tipe dokumen yang akan dibuat</p>
            </div>

            <!-- Preview Box (Hidden initially) -->
            <div id="previewBox" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-800 mb-2">
                    <i class='bx bx-info-circle'></i> Document Number Preview:
                </p>
                <p id="previewNumber" class="font-mono text-xl text-blue-600 font-bold">
                    -
                </p>
                <p class="text-xs text-gray-600 mt-2">
                    Nomor akan otomatis di-generate saat save
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Subscription ID -->
                <div>
                    <label for="subscription_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Subscription ID
                    </label>
                    <input type="text"
                           id="subscription_id"
                           name="subscription_id"
                           placeholder="SUB-20231118-001"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional - ID langganan dari BEFAST</p>
                </div>

                <!-- Customer Name -->
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Name
                    </label>
                    <input type="text"
                           id="customer_name"
                           name="customer_name"
                           placeholder="Agus Supratman"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional - Nama pelanggan</p>
                </div>

            </div>

            <!-- Document Date -->
            <div class="mb-4 mt-4">
                <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Document Date <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="document_date"
                       name="document_date"
                       required
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Tanggal dokumen dibuat</p>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="generated">Generated</option>
                    <option value="printed">Printed</option>
                    <option value="signed">Signed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Status awal dokumen</p>
            </div>

            <!-- Upload File (Optional) -->
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload PDF File
                </label>
                <div class="flex items-center gap-4">
                    <label class="flex-1 flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <div class="text-center">
                            <i class='bx bx-upload text-3xl text-gray-400'></i>
                            <p class="text-sm text-gray-600 mt-1">Click to upload PDF</p>
                            <p class="text-xs text-gray-500">Max 5MB</p>
                        </div>
                        <input type="file"
                               id="file"
                               name="file"
                               accept=".pdf"
                               class="hidden"
                               onchange="displayFileName(this)">
                    </label>
                </div>
                <p id="fileName" class="text-sm text-gray-600 mt-2 hidden"></p>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          placeholder="Optional notes..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Info Box -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm font-semibold text-yellow-800 mb-1">
                    <i class='bx bx-info-circle'></i> Penting:
                </p>
                <ul class="text-xs text-yellow-700 space-y-1 ml-4 list-disc">
                    <li>Nomor dokumen akan otomatis di-generate berdasarkan counter</li>
                    <li>Counter akan otomatis sinkronisasi dengan data existing</li>
                    <li>Bulan dan tahun akan mengikuti tanggal dokumen yang dipilih</li>
                    <li>File PDF bersifat opsional, bisa diupload kemudian</li>
                </ul>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('indexDocuments') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class='bx bx-save'></i>
                    <span>Create Document</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // Update preview when document type changes
    $('#doc_type_id').on('change', function() {
        const selected = $(this).find('option:selected');

        if (selected.val()) {
            const prefix = selected.data('prefix');
            const format = selected.data('format');
            const counter = selected.data('counter');
            const nextNumber = counter + 1;

            // Get current date for month/year
            const date = new Date($('#document_date').val());
            const monthNames = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();

            // Format preview
            const formattedNumber = nextNumber < 1000 ?
                String(nextNumber).padStart(3, '0') :
                String(nextNumber);

            const preview = `${prefix}.${formattedNumber}/${format}/${month}/${year}`;

            $('#previewNumber').text(preview);
            $('#previewBox').removeClass('hidden');
        } else {
            $('#previewBox').addClass('hidden');
        }
    });

    // Update preview when date changes
    $('#document_date').on('change', function() {
        $('#doc_type_id').trigger('change');
    });

    // Display selected file name
    window.displayFileName = function(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // MB

            $('#fileName').html(`
                <i class='bx bx-file-blank'></i>
                <span class="font-medium">${fileName}</span>
                <span class="text-gray-500">(${fileSize} MB)</span>
            `).removeClass('hidden');
        }
    };

    // Form Submit
    $('#createForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("storeDocuments") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan...',
                    html: `
                        <div class="flex flex-col items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-3"></div>
                            <p class="text-gray-600">Generating document number...</p>
                        </div>
                    `,
                    allowOutsideClick: false,
                    showConfirmButton: false
                });
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: `
                        <div class="text-center">
                            <p class="text-gray-700 mb-3">${response.message}</p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-gray-600 mb-1">Document Number:</p>
                                <p class="font-mono text-lg font-bold text-blue-600">${response.data.doc_number}</p>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'View Document',
                    showCancelButton: true,
                    cancelButtonText: 'Back to List',
                    confirmButtonColor: '#3B82F6',
                    cancelButtonColor: '#6B7280',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/documents/${response.data.id}`;
                    } else {
                        window.location.href = '{{ route("indexDocuments") }}';
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
    });

});
</script>
@endpush
