@extends('layouts.main')

@section('title', 'Dashboard')
@section('subtitle', 'Overview sistem dokumen')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">
            <i class='bx bx-wave'></i> Selamat Datang, {{ auth()->user()->full_name }}!
        </h1>
        <p class="text-blue-100">
            <i class='bx bx-calendar'></i> {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Documents -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Documents</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_documents'] }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class='bx bx-file text-3xl text-blue-600'></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4">
                <span class="text-green-600 font-semibold">
                    <i class='bx bx-up-arrow-alt'></i> {{ $stats['today_documents'] }}
                </span> hari ini
            </p>
        </div>

        <!-- Generated -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Generated</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['generated'] }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <i class='bx bx-time text-3xl text-yellow-600'></i>
                </div>
            </div>
        </div>

        <!-- Signed -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Signed</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['signed'] }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class='bx bx-check-circle text-3xl text-green-600'></i>
                </div>
            </div>
        </div>

        <!-- Document Types -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Document Types</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['document_types'] }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class='bx bx-category text-3xl text-purple-600'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Documents -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class='bx bx-time-five'></i> Recent Documents
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Document Number</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Type</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDocuments as $doc)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <span class="font-mono text-sm text-blue-600">{{ $doc->doc_number }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $doc->documentType->name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $doc->document_date->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            @if($doc->status === 'generated')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    <i class='bx bx-file'></i> Generated
                                </span>
                            @elseif($doc->status === 'printed')
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                                    <i class='bx bx-printer'></i> Printed
                                </span>
                            @elseif($doc->status === 'signed')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                    <i class='bx bx-check-circle'></i> Signed
                                </span>
                            @elseif($doc->status === 'cancelled')
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                    <i class='bx bx-x-circle'></i> Cancelled
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $doc->creator->full_name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            <i class='bx bx-inbox text-4xl mb-2'></i>
                            <p>Belum ada dokumen</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
