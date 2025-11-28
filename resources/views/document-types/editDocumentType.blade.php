@extends('layouts.main')

@section('title', 'Edit Document Type')
@section('subtitle', 'Update document type information')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Document Type</h2>
            <p class="text-sm text-gray-600">{{ $documentType->name }}</p>
        </div>
        <a href="{{ route('indexDocumentTypes') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class='bx bx-arrow-back'></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="editForm">
            @csrf
            @method('PUT')

            <!-- Code -->
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Code <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="code"
                       name="code"
                       required
                       value="{{ $documentType->code }}"
                       placeholder="form, konf, ba, skpk, dll"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Unique identifier (lowercase, no spaces)</p>
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       required
                       value="{{ $documentType->name }}"
                       placeholder="Formulir Berlangganan"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Prefix & Format Code -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="prefix" class="block text-sm font-medium text-gray-700 mb-2">
                        Prefix <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="prefix"
                           name="prefix"
                           required
                           value="{{ $documentType->prefix }}"
                           placeholder="22"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Nomor prefix (10, 13, 22, dst)</p>
                </div>

                <div>
                    <label for="format_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Format Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="format_code"
                           name="format_code"
                           required
                           value="{{ $documentType->format_code }}"
                           placeholder="F1-FB"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format code (F1-FB, F1-SC, dst)</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          placeholder="Optional description..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $documentType->description }}</textarea>
            </div>

            <!-- Current Counter Info -->
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm font-semibold text-yellow-800 mb-2">
                    <i class='bx bx-info-circle'></i> Current Counter Info:
                </p>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Current Number:</p>
                        <p class="font-bold text-gray-800">{{ $documentType->current_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Last Month:</p>
                        <p class="font-bold text-gray-800">{{ $documentType->current_month ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Last Year:</p>
                        <p class="font-bold text-gray-800">{{ $documentType->current_year ?? '-' }}</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-700 mt-2">
                    <i class='bx bx-error-circle'></i>
                    Counter tidak akan berubah saat update. Gunakan fitur "Reset Counter" jika diperlukan.
                </p>
            </div>

            <!-- Preview Box -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-800 mb-2">
                    <i class='bx bx-info-circle'></i> Preview Format:
                </p>
                <p id="previewFormat" class="font-mono text-lg text-blue-600">
                    {{ $documentType->prefix }}.{{ str_pad($documentType->current_number + 1, 3, '0', STR_PAD_LEFT) }}/{{ $documentType->format_code }}/XI/2025
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('indexDocumentTypes') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class='bx bx-save'></i>
                    <span>Update</span>
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

    // Update preview on input change
    function updatePreview() {
        const prefix = $('#prefix').val() || '[Prefix]';
        const formatCode = $('#format_code').val() || '[Format]';
        const currentNumber = {{ $documentType->current_number }};
        const nextNumber = String(currentNumber + 1).padStart(3, '0');
        const preview = `${prefix}.${nextNumber}/${formatCode}/XI/2025`;
        $('#previewFormat').text(preview);
    }

    $('#prefix, #format_code').on('input', updatePreview);

    // Form Submit
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("updateDocumentTypes", $documentType->doc_type_id) }}',
            type: 'PUT',
            data: formData,
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '{{ route("indexDocumentTypes") }}';
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
