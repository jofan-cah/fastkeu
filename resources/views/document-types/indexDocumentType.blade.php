@extends('layouts.main')

@section('title', 'Document Types')
@section('subtitle', 'Manage document types & numbering')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Search -->
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input type="text"
                       id="searchInput"
                       placeholder="Search by name, code, prefix..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <!-- Filter Status -->
            <select id="filterStatus"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <!-- Create Button -->
            @if(auth()->user()->hasPermission('DocumentTypes', 'create'))
            <a href="{{ route('createDocumentTypes') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition whitespace-nowrap">
                <i class='bx bx-plus'></i>
                <span class="hidden sm:inline">Create New</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Types</p>
            <p class="text-2xl font-bold text-gray-800">{{ $documentTypes->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $documentTypes->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Inactive</p>
            <p class="text-2xl font-bold text-red-600">{{ $documentTypes->where('is_active', false)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">For BEFAST</p>
            <p class="text-2xl font-bold text-blue-600">{{ $documentTypes->whereIn('code', ['form', 'konf', 'ba'])->count() }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Code</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Format</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Counter</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($documentTypes as $type)
                    <tr class="border-b hover:bg-gray-50 document-type-row"
                        data-search="{{ strtolower($type->name . ' ' . $type->code . ' ' . $type->prefix . ' ' . $type->format_code) }}"
                        data-status="{{ $type->is_active ? 'active' : 'inactive' }}">
                        <!-- Code -->
                        <td class="py-3 px-4">
                            <span class="font-mono text-sm font-semibold text-blue-600">{{ $type->code }}</span>
                        </td>

                        <!-- Name -->
                        <td class="py-3 px-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $type->name }}</p>
                                <p class="text-xs text-gray-500">{{ $type->description ?? '-' }}</p>
                            </div>
                        </td>

                        <!-- Format -->
                        <td class="py-3 px-4">
                            <div class="text-sm">
                                <p class="font-mono text-gray-700">{{ $type->prefix }}.XXX/{{ $type->format_code }}/MM/YYYY</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Example: {{ $type->prefix }}.001/{{ $type->format_code }}/XI/2025
                                </p>
                            </div>
                        </td>

                        <!-- Counter -->
                        <td class="py-3 px-4">
                            <div class="text-sm">
                                <p class="font-semibold text-gray-800">{{ $type->current_number }}</p>
                                @if($type->current_month && $type->current_year)
                                <p class="text-xs text-gray-500">{{ $type->current_month }}/{{ $type->current_year }}</p>
                                @else
                                <p class="text-xs text-gray-400">Not used yet</p>
                                @endif
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-4">
                            @if($type->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                <i class='bx bx-check-circle'></i> Active
                            </span>
                            @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                                <i class='bx bx-x-circle'></i> Inactive
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <!-- Show -->
                                <a href="{{ route('showDocumentTypes', $type->doc_type_id) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1"
                                   title="Detail">
                                    <i class='bx bx-show text-lg'></i>
                                </a>

                                <!-- Edit -->
                                @if(auth()->user()->hasPermission('DocumentTypes', 'update'))
                                <a href="{{ route('editDocumentTypes', $type->doc_type_id) }}"
                                   class="text-yellow-600 hover:text-yellow-800 p-1"
                                   title="Edit">
                                    <i class='bx bx-edit text-lg'></i>
                                </a>
                                @endif

                                <!-- Toggle Status -->
                                @if(auth()->user()->hasPermission('DocumentTypes', 'update'))
                                <button onclick="toggleStatus('{{ $type->doc_type_id }}')"
                                        class="text-purple-600 hover:text-purple-800 p-1"
                                        title="Toggle Status">
                                    <i class='bx bx-revision text-lg'></i>
                                </button>
                                @endif

                                <!-- Delete -->
                                @if(auth()->user()->hasPermission('DocumentTypes', 'delete'))
                                <button onclick="deleteDocumentType('{{ $type->doc_type_id }}')"
                                        class="text-red-600 hover:text-red-800 p-1"
                                        title="Delete">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            <i class='bx bx-inbox text-4xl mb-2'></i>
                            <p>No document types found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="hidden py-8 text-center text-gray-500">
            <i class='bx bx-search text-4xl mb-2'></i>
            <p>No results found</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // Search Function
    $('#searchInput').on('keyup', function() {
        filterTable();
    });

    // Filter Status
    $('#filterStatus').on('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const statusFilter = $('#filterStatus').val();
        let visibleCount = 0;

        $('.document-type-row').each(function() {
            const row = $(this);
            const searchData = row.data('search');
            const status = row.data('status');

            const matchesSearch = searchTerm === '' || searchData.includes(searchTerm);
            const matchesStatus = statusFilter === '' || status === statusFilter;

            if (matchesSearch && matchesStatus) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            $('#noResults').removeClass('hidden');
            $('#emptyRow').hide();
        } else {
            $('#noResults').addClass('hidden');
        }
    }

    // Clear search on ESC
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#searchInput').val('');
            $('#filterStatus').val('');
            filterTable();
        }
    });

});

// Toggle Status
function toggleStatus(docTypeId) {
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
                url: `/document-types/${docTypeId}/toggle-status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => window.location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Gagal mengubah status', 'error');
                }
            });
        }
    });
}

// Delete Document Type
function deleteDocumentType(docTypeId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus document type ini? Data tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/document-types/${docTypeId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => window.location.reload());
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
