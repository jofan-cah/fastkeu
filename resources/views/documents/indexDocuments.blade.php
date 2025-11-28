@extends('layouts.main')

@section('title', 'Documents')
@section('subtitle', 'Manage all documents')

@section('content')
    <div class="space-y-4 md:space-y-6">

        <!-- Header Actions -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Search -->
            <div class="flex-1 max-w-md">
                <form method="GET" action="{{ route('indexDocuments') }}" id="searchForm">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by doc number, customer, subscription..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                        @if (request('search'))
                            <button type="button" onclick="clearSearch()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class='bx bx-x text-xl'></i>
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <!-- Filter Button -->
                <button onclick="toggleFilters()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2 transition">
                    <i class='bx bx-filter'></i>
                    <span class="hidden sm:inline">Filters</span>
                    @if (request('doc_type') || request('status') || request('date_from') || request('date_to'))
                        <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                            {{ collect([request('doc_type'), request('status'), request('date_from'), request('date_to')])->filter()->count() }}
                        </span>
                    @endif
                </button>

                <button onclick="exportExcel()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition whitespace-nowrap">
                    <i class='bx bx-download'></i>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>
                @if (auth()->user()->hasPermission('Documents', 'create'))
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition whitespace-nowrap">
                            <i class='bx bx-file-blank'></i>
                            <span class="hidden sm:inline">Generate Document</span>
                            <i class='bx bx-chevron-down transition-transform' :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <div class="py-2">
                                <!-- BA Kesepakatan -->
                                <a href="{{ route('createBaKesepakatan') }}"
                                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <i class='bx bx-file text-orange-600 text-xl'></i>
                                    <div>
                                        <div class="font-medium text-gray-800">BA Kesepakatan</div>
                                        <div class="text-xs text-gray-500">Berita Acara Perubahan Layanan</div>
                                    </div>
                                </a>

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- Surat Pengalaman Kerja -->
                                <a href="{{ route('createSkpk') }}"
                                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <i class='bx bx-briefcase text-blue-600 text-xl'></i>
                                    <div>
                                        <div class="font-medium text-gray-800">Surat Pengalaman Kerja</div>
                                        <div class="text-xs text-gray-500">SKPK untuk karyawan</div>
                                    </div>
                                </a>

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- Surat PHK -->
                                <a href="{{ route('createSuratPhk') }}"
                                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <i class='bx bx-user-x text-red-600 text-xl'></i>
                                    <div>
                                        <div class="font-medium text-gray-800">Surat Pernyataan PHK</div>
                                        <div class="text-xs text-gray-500">Pemutusan Hubungan Kerja</div>
                                    </div>
                                </a>

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- BA Peminjaman -->
                                <a href="{{ route('createBaPeminjaman') }}"
                                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <i class='bx bx-package text-purple-600 text-xl'></i>
                                    <div>
                                        <div class="font-medium text-gray-800">BA Peminjaman Perangkat</div>
                                        <div class="text-xs text-gray-500">Berita Acara Peminjaman</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Create Button -->
                @if (auth()->user()->hasPermission('Documents', 'create'))
                    <a href="{{ route('createDocuments') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition whitespace-nowrap">
                        <i class='bx bx-plus'></i>
                        <span class="hidden sm:inline">Create New</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Filter Panel -->
        <div id="filterPanel" class="hidden bg-white rounded-lg shadow-md p-4">
            <form method="GET" action="{{ route('indexDocuments') }}" id="filterForm">
                <!-- Keep search value -->
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Document Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                        <select name="doc_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->doc_type_id }}"
                                    {{ request('doc_type') == $type->doc_type_id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>Generated
                            </option>
                            <option value="printed" {{ request('status') == 'printed' ? 'selected' : '' }}>Printed</option>
                            <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>Signed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <a href="{{ route('indexDocuments') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Clear Filters
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Total Documents</p>
                <p class="text-2xl font-bold text-gray-800">{{ $documents->total() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Generated</p>
                <p class="text-2xl font-bold text-blue-600">
                    {{ \App\Models\Document::where('status', 'generated')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Signed</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ \App\Models\Document::where('status', 'signed')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Today</p>
                <p class="text-2xl font-bold text-purple-600">
                    {{ \App\Models\Document::whereDate('created_at', today())->count() }}
                </p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Document Number</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Type</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Customer</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">File</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr class="border-b hover:bg-gray-50">
                                <!-- Document Number -->
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-mono text-sm font-semibold text-blue-600">{{ $doc->doc_number }}
                                        </p>
                                        <p class="text-xs text-gray-500">ID: {{ $doc->id }}</p>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $doc->documentType->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $doc->documentType->code }}</p>
                                    </div>
                                </td>

                                <!-- Customer -->
                                <td class="py-3 px-4">
                                    <div>
                                        @if ($doc->customer_name)
                                            <p class="text-sm text-gray-800">{{ $doc->customer_name }}</p>
                                        @else
                                            <p class="text-sm text-gray-400">-</p>
                                        @endif
                                        @if ($doc->subscription_id)
                                            <p class="text-xs text-gray-500">{{ $doc->subscription_id }}</p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Date -->
                                <td class="py-3 px-4">
                                    <p class="text-sm text-gray-800">{{ $doc->document_date->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $doc->created_at->diffForHumans() }}</p>
                                </td>

                                <!-- Status -->
                                <td class="py-3 px-4">
                                    @if ($doc->status === 'generated')
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full whitespace-nowrap">
                                            <i class='bx bx-file'></i> Generated
                                        </span>
                                    @elseif($doc->status === 'printed')
                                        <span
                                            class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full whitespace-nowrap">
                                            <i class='bx bx-printer'></i> Printed
                                        </span>
                                    @elseif($doc->status === 'signed')
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full whitespace-nowrap">
                                            <i class='bx bx-check-circle'></i> Signed
                                        </span>
                                    @elseif($doc->status === 'cancelled')
                                        <span
                                            class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full whitespace-nowrap">
                                            <i class='bx bx-x-circle'></i> Cancelled
                                        </span>
                                    @endif
                                </td>

                                <!-- File -->
                                <td class="py-3 px-4">
                                    @if ($doc->file_path)
                                        <a href="{{ route('downloadDocuments', $doc->id) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                            <i class='bx bx-download'></i>
                                            <span>PDF</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">No file</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <!-- Show -->
                                        <a href="{{ route('showDocuments', $doc->id) }}"
                                            class="text-blue-600 hover:text-blue-800 p-1" title="Detail">
                                            <i class='bx bx-show text-lg'></i>
                                        </a>

                                        <!-- Edit -->
                                        @if (auth()->user()->hasPermission('Documents', 'update'))
                                            <a href="{{ route('editDocuments', $doc->id) }}"
                                                class="text-yellow-600 hover:text-yellow-800 p-1" title="Edit">
                                                <i class='bx bx-edit text-lg'></i>
                                            </a>
                                        @endif

                                        <!-- Upload File -->
                                        @if (auth()->user()->hasPermission('Documents', 'update'))
                                            <button onclick="uploadFile('{{ $doc->id }}')"
                                                class="text-purple-600 hover:text-purple-800 p-1" title="Upload File">
                                                <i class='bx bx-upload text-lg'></i>
                                            </button>
                                        @endif

                                        <!-- Delete -->
                                        @if (auth()->user()->hasPermission('Documents', 'delete'))
                                            <button onclick="deleteDocument('{{ $doc->id }}')"
                                                class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    <i class='bx bx-inbox text-4xl mb-2'></i>
                                    <p>No documents found</p>
                                    @if (request()->has('search') || request()->has('doc_type') || request()->has('status'))
                                        <button onclick="window.location.href='{{ route('indexDocuments') }}'"
                                            class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                            Clear filters
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($documents->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

        <!-- Showing results info -->
        @if ($documents->total() > 0)
            <div class="text-sm text-gray-600 text-center">
                Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of {{ $documents->total() }}
                documents
            </div>
        @endif

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
                <input type="hidden" id="uploadDocId" name="doc_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select PDF File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" id="pdfFile" name="file" accept=".pdf" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Max file size: 5MB</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeUploadModal()"
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
        // Toggle Filters
        function toggleFilters() {
            const panel = document.getElementById('filterPanel');
            panel.classList.toggle('hidden');
        }

        // Clear Search
        function clearSearch() {
            window.location.href = '{{ route('indexDocuments') }}';
        }

        // Upload File Modal
        let currentDocId = null;

        function uploadFile(docId) {
            currentDocId = docId;
            document.getElementById('uploadDocId').value = docId;
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadForm').reset();
            currentDocId = null;
        }

        // Upload Form Submit
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const docId = currentDocId;

            $.ajax({
                url: `/documents/${docId}/upload`,
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


        function exportExcel() {
            // Get current filters
            const searchParams = new URLSearchParams();

            // Date filters
            const dateFrom = document.querySelector('input[name="date_from"]')?.value;
            const dateTo = document.querySelector('input[name="date_to"]')?.value;

            if (dateFrom) searchParams.append('start_date', dateFrom);
            if (dateTo) searchParams.append('end_date', dateTo);

            // Build URL
            const url = '{{ route('exportDocuments') }}' + '?' + searchParams.toString();

            // Show loading
            Swal.fire({
                title: 'Exporting...',
                text: 'Generating Excel file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Download
            window.location.href = url;

            // Close loading after delay
            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Excel file downloaded',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 2000);
        }

        // Delete Document
        function deleteDocument(docId) {
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
                        url: `/documents/${docId}`,
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

        // Close modal with ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUploadModal();
            }
        });
    </script>
@endpush
