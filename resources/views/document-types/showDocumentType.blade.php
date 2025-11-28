@extends('layouts.main')

@section('title', 'Document Type Detail')
@section('subtitle', $documentType->name)

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $documentType->name }}</h2>
            <p class="text-gray-600">Code: <span class="font-mono font-semibold text-blue-600">{{ $documentType->code }}</span></p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Back Button -->
            <a href="{{ route('indexDocumentTypes') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class='bx bx-arrow-back'></i>
                <span>Back</span>
            </a>

            <!-- Edit Button -->
            @if(auth()->user()->hasPermission('DocumentTypes', 'update'))
            <a href="{{ route('editDocumentTypes', $documentType->doc_type_id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class='bx bx-edit'></i>
                <span>Edit</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Documents -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Documents</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalDocuments }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class='bx bx-file text-3xl text-blue-600'></i>
                </div>
            </div>
        </div>

        <!-- Current Counter -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Current Counter</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $documentType->current_number }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class='bx bx-hash text-3xl text-purple-600'></i>
                </div>
            </div>
        </div>

        <!-- Last Period -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Last Period</p>
                    <p class="text-xl font-bold text-gray-800 mt-2">
                        @if($documentType->current_month && $documentType->current_year)
                            {{ $documentType->current_month }}/{{ $documentType->current_year }}
                        @else
                            <span class="text-gray-400">Not used</span>
                        @endif
                    </p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class='bx bx-calendar text-3xl text-green-600'></i>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Status</p>
                    <p class="text-xl font-bold mt-2">
                        @if($documentType->is_active)
                            <span class="text-green-600">Active</span>
                        @else
                            <span class="text-red-600">Inactive</span>
                        @endif
                    </p>
                </div>
                <div class="bg-{{ $documentType->is_active ? 'green' : 'red' }}-100 p-4 rounded-lg">
                    <i class='bx bx-{{ $documentType->is_active ? 'check' : 'x' }}-circle text-3xl text-{{ $documentType->is_active ? 'green' : 'red' }}-600'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Basic Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class='bx bx-info-circle text-blue-600'></i>
                Basic Information
            </h3>

            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Code:</span>
                    <span class="font-mono font-semibold text-blue-600">{{ $documentType->code }}</span>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-semibold text-gray-800">{{ $documentType->name }}</span>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Prefix:</span>
                    <span class="font-mono font-semibold text-gray-800">{{ $documentType->prefix }}</span>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Format Code:</span>
                    <span class="font-mono font-semibold text-gray-800">{{ $documentType->format_code }}</span>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Created:</span>
                    <span class="text-sm text-gray-600">{{ $documentType->created_at->format('d M Y, H:i') }}</span>
                </div>

                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Updated:</span>
                    <span class="text-sm text-gray-600">{{ $documentType->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            @if($documentType->description)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 font-medium mb-1">Description:</p>
                <p class="text-gray-700">{{ $documentType->description }}</p>
            </div>
            @endif
        </div>

        <!-- Format Preview & Actions -->
        <div class="space-y-6">

            <!-- Format Preview -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class='bx bx-show text-blue-600'></i>
                    Format Preview
                </h3>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-600 mb-2">Current Format:</p>
                    <p class="font-mono text-xl text-blue-600 font-bold">
                        {{ $documentType->prefix }}.XXX/{{ $documentType->format_code }}/MM/YYYY
                    </p>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2">Next Document Number:</p>
                    <p class="font-mono text-lg text-green-600 font-bold" id="nextNumberPreview">
                        Loading...
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class='bx bx-cog text-blue-600'></i>
                    Quick Actions
                </h3>

                <div class="space-y-3">
                    <!-- Toggle Status -->
                    @if(auth()->user()->hasPermission('DocumentTypes', 'update'))
                    <button onclick="toggleStatus()"
                            class="w-full flex items-center justify-between px-4 py-3 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition">
                        <span class="flex items-center gap-2 text-purple-700 font-medium">
                            <i class='bx bx-revision text-xl'></i>
                            Toggle Status
                        </span>
                        <i class='bx bx-chevron-right text-purple-600'></i>
                    </button>
                    @endif

                    <!-- Reset Counter -->
                    @if(auth()->user()->hasPermission('DocumentTypes', 'update'))
                    <button onclick="resetCounter()"
                            class="w-full flex items-center justify-between px-4 py-3 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg transition">
                        <span class="flex items-center gap-2 text-yellow-700 font-medium">
                            <i class='bx bx-reset text-xl'></i>
                            Reset Counter
                        </span>
                        <i class='bx bx-chevron-right text-yellow-600'></i>
                    </button>
                    @endif

                    <!-- Delete -->
                    @if(auth()->user()->hasPermission('DocumentTypes', 'delete'))
                    <button onclick="deleteDocumentType()"
                            class="w-full flex items-center justify-between px-4 py-3 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition">
                        <span class="flex items-center gap-2 text-red-700 font-medium">
                            <i class='bx bx-trash text-xl'></i>
                            Delete Document Type
                        </span>
                        <i class='bx bx-chevron-right text-red-600'></i>
                    </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Latest Document -->
    @if($latestDocument)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class='bx bx-file text-blue-600'></i>
            Latest Document
        </h3>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Document Number:</p>
                    <p class="font-mono font-semibold text-blue-600">{{ $latestDocument->doc_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Document ID:</p>
                    <p class="font-mono text-sm text-gray-800">{{ $latestDocument->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date:</p>
                    <p class="text-gray-800">{{ $latestDocument->document_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status:</p>
                    <span class="px-2 py-1 bg-{{ $latestDocument->status === 'signed' ? 'green' : 'blue' }}-100 text-{{ $latestDocument->status === 'signed' ? 'green' : 'blue' }}-800 text-xs rounded-full">
                        {{ ucfirst($latestDocument->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Documents Per Month Chart -->
    @if($documentsPerMonth->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class='bx bx-bar-chart text-blue-600'></i>
            Documents Per Month (Last 6 Months)
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-4 text-sm font-semibold text-gray-700">Month</th>
                        <th class="text-left py-2 px-4 text-sm font-semibold text-gray-700">Total</th>
                        <th class="text-left py-2 px-4 text-sm font-semibold text-gray-700">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documentsPerMonth as $data)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ \Carbon\Carbon::parse($data->month)->format('M Y') }}</td>
                        <td class="py-2 px-4 font-semibold">{{ $data->total }}</td>
                        <td class="py-2 px-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($data->total / $documentsPerMonth->max('total')) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // Load next number preview
    loadNextNumberPreview();

    function loadNextNumberPreview() {
        $.ajax({
            url: '/document-types/{{ $documentType->doc_type_id }}/preview',
            type: 'GET',
            success: function(response) {
                $('#nextNumberPreview').text(response.preview);
            },
            error: function() {
                $('#nextNumberPreview').text('Error loading preview');
            }
        });
    }

});

// Toggle Status
function toggleStatus() {
    Swal.fire({
        title: 'Toggle Status',
        text: 'Yakin ingin mengubah status document type ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/document-types/{{ $documentType->doc_type_id }}/toggle-status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => window.location.reload());
                },
                error: function() {
                    Swal.fire('Error!', 'Gagal mengubah status', 'error');
                }
            });
        }
    });
}

// Reset Counter
function resetCounter() {
    Swal.fire({
        title: 'Reset Counter',
        html: `
            <p class="text-red-600 font-semibold mb-2">PERINGATAN!</p>
            <p class="text-gray-700 mb-2">Counter akan direset ke <strong>0</strong>.</p>
            <p class="text-gray-700">Yakin ingin melanjutkan?</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/document-types/{{ $documentType->doc_type_id }}/reset-counter',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => window.location.reload());
                },
                error: function() {
                    Swal.fire('Error!', 'Gagal reset counter', 'error');
                }
            });
        }
    });
}

// Delete Document Type
function deleteDocumentType() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `
            <p class="text-red-600 font-semibold mb-2">PERINGATAN!</p>
            <p class="text-gray-700 mb-2">Document type akan dihapus permanen.</p>
            <p class="text-gray-700">Data tidak dapat dikembalikan!</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/document-types/{{ $documentType->doc_type_id }}',
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => window.location.href = '{{ route("indexDocumentTypes") }}');
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Gagal menghapus data';
                    Swal.fire('Error!', message, 'error');
                }
            });
        }
    });
}
</script>
@endpush
