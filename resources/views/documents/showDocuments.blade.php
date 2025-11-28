@extends('layouts.main')

@section('title', 'Document Detail')
@section('subtitle', $document->doc_number)

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $document->documentType->name }}</h2>
            <p class="text-gray-600 font-mono text-lg">{{ $document->doc_number }}</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Back Button -->
            <a href="{{ route('indexDocuments') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class='bx bx-arrow-back'></i>
                <span>Back</span>
            </a>

            <!-- Download Button -->
            @if($document->file_path)
            <a href="{{ route('downloadDocuments', $document->id) }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class='bx bx-download'></i>
                <span>Download PDF</span>
            </a>
            @endif

            <!-- Edit Button -->
            @if(auth()->user()->hasPermission('Documents', 'update'))
            <a href="{{ route('editDocuments', $document->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class='bx bx-edit'></i>
                <span>Edit</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Main Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Document Info -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class='bx bx-info-circle text-blue-600'></i>
                Document Information
            </h3>

            <div class="space-y-4">
                <!-- Document Number -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Document Number:</span>
                    <span class="font-mono font-bold text-blue-600">{{ $document->doc_number }}</span>
                </div>

                <!-- Document ID -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Document ID:</span>
                    <span class="font-mono text-sm text-gray-800">{{ $document->id }}</span>
                </div>

                <!-- Document Type -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Document Type:</span>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">{{ $document->documentType->name }}</p>
                        <p class="text-xs text-gray-500">{{ $document->documentType->code }}</p>
                    </div>
                </div>

                <!-- Customer -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-semibold text-gray-800">{{ $document->customer_name ?? '-' }}</span>
                </div>

                <!-- Subscription ID -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Subscription ID:</span>
                    <span class="font-mono text-sm text-gray-800">{{ $document->subscription_id ?? '-' }}</span>
                </div>

                <!-- Document Date -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Document Date:</span>
                    <span class="text-gray-800">{{ $document->document_date->format('d M Y') }}</span>
                </div>

                <!-- Status -->
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600">Status:</span>
                    <div>
                        @if($document->status === 'generated')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                                <i class='bx bx-file'></i> Generated
                            </span>
                        @elseif($document->status === 'printed')
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium">
                                <i class='bx bx-printer'></i> Printed
                            </span>
                        @elseif($document->status === 'signed')
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                                <i class='bx bx-check-circle'></i> Signed
                            </span>
                        @elseif($document->status === 'cancelled')
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full font-medium">
                                <i class='bx bx-x-circle'></i> Cancelled
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($document->notes)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 font-medium mb-2">Notes:</p>
                <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $document->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Side Info -->
        <div class="space-y-6">

            <!-- File Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class='bx bx-file-blank text-blue-600'></i>
                    File Status
                </h3>

                @if($document->file_path)
                <div class="text-center py-4">
                    <div class="inline-block bg-green-100 p-4 rounded-full mb-3">
                        <i class='bx bx-check-circle text-4xl text-green-600'></i>
                    </div>
                    <p class="font-semibold text-green-800 mb-1">File Available</p>
                    <p class="text-sm text-gray-600 mb-4">PDF file ready to download</p>
                    <a href="{{ route('downloadDocuments', $document->id) }}"
                       class="w-full inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class='bx bx-download'></i> Download PDF
                    </a>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="inline-block bg-gray-100 p-4 rounded-full mb-3">
                        <i class='bx bx-error-circle text-4xl text-gray-400'></i>
                    </div>
                    <p class="font-semibold text-gray-600 mb-1">No File</p>
                    <p class="text-sm text-gray-500 mb-4">PDF file not uploaded yet</p>
                    @if(auth()->user()->hasPermission('Documents', 'update'))
                    <button onclick="uploadFile()"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class='bx bx-upload'></i> Upload File
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- Audit Trail -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class='bx bx-history text-blue-600'></i>
                    Audit Trail
                </h3>

                <div class="space-y-3 text-sm">
                    <!-- Created -->
                    <div class="pb-3 border-b border-gray-100">
                        <p class="text-gray-600 mb-1">Created:</p>
                        <p class="font-semibold text-gray-800">{{ $document->created_at->format('d M Y, H:i') }}</p>
                        @if($document->creator)
                        <p class="text-xs text-gray-500">by {{ $document->creator->full_name }}</p>
                        @endif
                    </div>

                    <!-- Updated -->
                    <div>
                        <p class="text-gray-600 mb-1">Last Updated:</p>
                        <p class="font-semibold text-gray-800">{{ $document->updated_at->format('d M Y, H:i') }}</p>
                        @if($document->updater)
                        <p class="text-xs text-gray-500">by {{ $document->updater->full_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if(auth()->user()->hasPermission('Documents', 'update') || auth()->user()->hasPermission('Documents', 'delete'))
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class='bx bx-cog text-blue-600'></i>
                    Quick Actions
                </h3>

                <div class="space-y-2">
                    <!-- Edit -->
                    @if(auth()->user()->hasPermission('Documents', 'update'))
                    <a href="{{ route('editDocuments', $document->id) }}"
                       class="w-full flex items-center justify-between px-4 py-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition">
                        <span class="flex items-center gap-2 text-blue-700 font-medium">
                            <i class='bx bx-edit text-xl'></i>
                            Edit Document
                        </span>
                        <i class='bx bx-chevron-right text-blue-600'></i>
                    </a>
                    @endif

                    <!-- Delete -->
                    @if(auth()->user()->hasPermission('Documents', 'delete'))
                    <button onclick="deleteDocument()"
                            class="w-full flex items-center justify-between px-4 py-3 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition">
                        <span class="flex items-center gap-2 text-red-700 font-medium">
                            <i class='bx bx-trash text-xl'></i>
                            Delete Document
                        </span>
                        <i class='bx bx-chevron-right text-red-600'></i>
                    </button>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>

<!-- Upload File Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Upload PDF File</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i class='bx bx-x text-2xl'></i>
            </button>
        </div>

        <form id="uploadForm" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select PDF File <span class="text-red-500">*</span>
                </label>
                <input type="file"
                       id="pdfFile"
                       name="file"
                       accept=".pdf"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Max file size: 5MB</p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeUploadModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Upload File Modal
function uploadFile() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm').reset();
}

// Upload Form Submit
$('#uploadForm').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
        url: '/documents/{{ $document->id }}/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            Swal.fire({
                title: 'Uploading...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            closeUploadModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
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
                    text: xhr.responseJSON?.message || 'Gagal upload file'
                });
            }
        }
    });
});

// Delete Document
function deleteDocument() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus document ini? Data tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/documents/{{ $document->id }}',
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => window.location.href = '{{ route("indexDocuments") }}');
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Gagal menghapus data';
                    Swal.fire('Error!', message, 'error');
                }
            });
        }
    });
}

// Close modal with ESC key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUploadModal();
    }
});
</script>
@endpush
