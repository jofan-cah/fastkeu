@extends('layouts.main')

@section('title', 'Create Document Type')
@section('subtitle', 'Add new document type')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Create New Document Type</h2>
        <a href="{{ route('indexDocumentTypes') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class='bx bx-arrow-back'></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="createForm">
            @csrf

            <!-- Code -->
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Code <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="code"
                       name="code"
                       required
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
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Preview Box -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-800 mb-2">
                    <i class='bx bx-info-circle'></i> Preview Format:
                </p>
                <p id="previewFormat" class="font-mono text-lg text-blue-600">
                    [Prefix].001/[Format]/XI/2025
                </p>
                <p class="text-xs text-gray-600 mt-2">
                    Counter akan dimulai dari <strong>0</strong> dan akan otomatis increment setiap generate.
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
                    <span>Create</span>
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
        const preview = `${prefix}.001/${formatCode}/XI/2025`;
        $('#previewFormat').text(preview);
    }

    $('#prefix, #format_code').on('input', updatePreview);

    // Form Submit
    $('#createForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("storeDocumentTypes") }}',
            type: 'POST',
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
