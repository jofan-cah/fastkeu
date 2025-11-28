@extends('layouts.main')

@section('title', 'Edit Document')
@section('subtitle', 'Update document information')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Document</h2>
            <p class="text-sm text-gray-600 font-mono">{{ $document->doc_number }}</p>
        </div>
        <a href="{{ route('indexDocuments') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class='bx bx-arrow-back'></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="editForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Document Number (Read Only) -->
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Document Number:</p>
                        <p class="font-mono text-xl font-bold text-blue-600">{{ $document->doc_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Document ID:</p>
                        <p class="font-mono text-sm text-gray-800">{{ $document->id }}</p>
                    </div>
                </div>
            </div>

            <!-- Document Type (Read Only) -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Document Type
                </label>
                <input type="text"
                       value="{{ $document->documentType->name }} ({{ $document->documentType->code }})"
                       readonly
                       class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                <p class="text-xs text-gray-500 mt-1">
                    <i class='bx bx-lock-alt'></i> Document type tidak dapat diubah
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
                           value="{{ $document->subscription_id }}"
                           placeholder="SUB-20231118-001"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Customer Name -->
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Name
                    </label>
                    <input type="text"
                           id="customer_name"
                           name="customer_name"
                           value="{{ $document->customer_name }}"
                           placeholder="Agus Supratman"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                       value="{{ $document->document_date->format('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <option value="generated" {{ $document->status == 'generated' ? 'selected' : '' }}>Generated</option>
                    <option value="printed" {{ $document->status == 'printed' ? 'selected' : '' }}>Printed</option>
                    <option value="signed" {{ $document->status == 'signed' ? 'selected' : '' }}>Signed</option>
                    <option value="cancelled" {{ $document->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Current File -->
            @if($document->file_path)
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class='bx bx-file-blank text-2xl text-green-600'></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-800">File sudah ada</p>
                            <p class="text-xs text-green-600">PDF tersedia untuk download</p>
                        </div>
                    </div>
                    <a href="{{ route('downloadDocuments', $document->id) }}"
                       class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg flex items-center gap-1 transition">
                        <i class='bx bx-download'></i>
                        <span>Download</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Replace/Upload File -->
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $document->file_path ? 'Replace PDF File' : 'Upload PDF File' }}
                </label>
                <div class="flex items-center gap-4">
                    <label class="flex-1 flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <div class="text-center">
                            <i class='bx bx-upload text-3xl text-gray-400'></i>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $document->file_path ? 'Click to replace PDF' : 'Click to upload PDF' }}
                            </p>
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
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $document->notes }}</textarea>
            </div>

            <!-- Audit Info -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-sm font-semibold text-gray-700 mb-2">
                    <i class='bx bx-info-circle'></i> Document Info:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-600">Created:</p>
                        <p class="text-gray-800">{{ $document->created_at->format('d M Y, H:i') }}</p>
                        @if($document->creator)
                        <p class="text-xs text-gray-500">by {{ $document->creator->full_name }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-600">Last Updated:</p>
                        <p class="text-gray-800">{{ $document->updated_at->format('d M Y, H:i') }}</p>
                        @if($document->updater)
                        <p class="text-xs text-gray-500">by {{ $document->updater->full_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm font-semibold text-yellow-800 mb-1">
                    <i class='bx bx-error-circle'></i> Perhatian:
                </p>
                <ul class="text-xs text-yellow-700 space-y-1 ml-4 list-disc">
                    <li>Nomor dokumen tidak dapat diubah</li>
                    <li>Tipe dokumen tidak dapat diubah</li>
                    <li>Upload file baru akan mengganti file yang lama</li>
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
                    <span>Update Document</span>
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

    // Display selected file name
    window.displayFileName = function(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // MB

            $('#fileName').html(`
                <i class='bx bx-file-blank'></i>
                <span class="font-medium">${fileName}</span>
                <span class="text-gray-500">(${fileSize} MB)</span>
                <span class="text-blue-600 ml-2">✓ File baru akan mengganti file lama</span>
            `).removeClass('hidden');
        }
    };

    // Form Submit
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("updateDocuments", $document->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
                    confirmButtonText: 'View Document',
                    showCancelButton: true,
                    cancelButtonText: 'Back to List',
                    confirmButtonColor: '#3B82F6',
                    cancelButtonColor: '#6B7280',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/documents/{{ $document->id }}';
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
