# 🚀 IMPLEMENTASI INTEGRASI API di FASTKEU (Laravel 12)

## 🎯 Overview

FASTKEU mengambil data dari **2 sumber API terpisah**:

| Sumber | Data | Port | File Dokumentasi |
|--------|------|------|------------------|
| **BEFAST** | Subscription & Paket | 8000 | `BEFAST_API_IMPLEMENTATION.md` |
| **PREFAST** | Karyawan | 8002 | `PREFAST_API_IMPLEMENTATION.md` |

**Tujuan:** Isi dropdown di form FASTKEU → Generate dokumen PDF

---

## ✅ Yang Sudah Dibuat

### 1. Service Layer
- `app/Services/BefastApiService.php` - Service untuk konsumsi API dari Befast & Prefast

### 2. Controller
- `app/Http/Controllers/BefastDataController.php` - Controller untuk handle dropdown requests

### 3. Configuration
- `config/services.php` - Konfigurasi API (Befast & Prefast)
- `.env` - Environment variables untuk API endpoint dan token

### 4. Routes
- Routes untuk dropdown API sudah ditambahkan di `routes/web.php`

---

## 📝 Setup Environment (.env)

Tambahkan konfigurasi untuk kedua API:

```env
# BEFAST API Configuration (Subscription & Paket)
BEFAST_API_URL=http://localhost:8000
BEFAST_API_TOKEN=befast_api_token_12345_secure_random_string
BEFAST_API_TIMEOUT=30

# PREFAST API Configuration (Karyawan)
PREFAST_API_URL=http://localhost:8002
PREFAST_API_TOKEN=prefast_api_token_67890_secure_random_string
PREFAST_API_TIMEOUT=30
```

**⚠️ PENTING:**
- Token harus sama dengan yang di `.env` Befast & Prefast
- URL sesuaikan dengan server kalian
- Untuk production, ganti dengan URL production

---

## 📝 Cara Update Template untuk Menggunakan Dropdown dari API

### Contoh 1: Dropdown Subscriptions dengan Select2

```blade
<!-- Form dengan Subscription Dropdown dari BEFAST API -->
<div class="mb-4">
    <label for="subscription_id" class="block text-sm font-medium text-gray-700 mb-2">
        Subscription <span class="text-red-500">*</span>
    </label>
    <select id="subscription_id"
            name="subscription_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        <option value="">-- Pilih Subscription --</option>
    </select>
    <p class="text-xs text-gray-500 mt-1">Data dari BEFAST API</p>
</div>

<!-- Hidden fields untuk data tambahan -->
<input type="hidden" id="customer_name" name="customer_name">
<input type="hidden" id="customer_email" name="customer_email">
<input type="hidden" id="customer_phone" name="customer_phone">

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 dengan AJAX
    $('#subscription_id').select2({
        placeholder: 'Cari subscription...',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("befast.subscriptions.dropdown") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    limit: 50
                };
            },
            processResults: function (response) {
                if (response.success) {
                    return {
                        results: response.data
                    };
                }
                return { results: [] };
            },
            cache: true
        }
    });

    // Event ketika subscription dipilih
    $('#subscription_id').on('select2:select', function (e) {
        const data = e.params.data;

        // Auto-fill fields lain
        if (data.customer_id) {
            $('#customer_name').val(data.text);
            $('#customer_email').val(data.email || '');
            $('#customer_phone').val(data.phone || '');
        }

        console.log('Selected subscription:', data);
    });
});
</script>
@endpush
```

### Contoh 2: Dropdown Pakets

```blade
<!-- Paket Dropdown -->
<div class="mb-4">
    <label for="paket_id" class="block text-sm font-medium text-gray-700 mb-2">
        Paket <span class="text-red-500">*</span>
    </label>
    <select id="paket_id"
            name="paket_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            required>
        <option value="">-- Pilih Paket --</option>
    </select>
</div>

<!-- Display paket info -->
<div id="paket-info" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
    <p class="text-sm font-semibold text-blue-800">Info Paket:</p>
    <ul class="text-sm text-gray-700 mt-2">
        <li>Speed: <span id="paket-speed"></span></li>
        <li>Price: Rp <span id="paket-price"></span></li>
        <li>Description: <span id="paket-description"></span></li>
    </ul>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load pakets dropdown (non-search, langsung load semua)
    $.ajax({
        url: '{{ route("befast.pakets.dropdown") }}',
        type: 'GET',
        data: { status: 'active' },
        success: function(response) {
            if (response.success) {
                const select = $('#paket_id');
                response.data.forEach(function(item) {
                    select.append(new Option(item.text, item.id));
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data paket dari BEFAST API'
            });
        }
    });

    // Event ketika paket dipilih
    $('#paket_id').on('change', function() {
        const paketId = $(this).val();

        if (paketId) {
            // Get detail paket
            $.ajax({
                url: `/api/befast/pakets/${paketId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const paket = response.data;

                        // Display paket info
                        $('#paket-speed').text(paket.speed);
                        $('#paket-price').text(paket.price.toLocaleString('id-ID'));
                        $('#paket-description').text(paket.description || '-');
                        $('#paket-info').removeClass('hidden');
                    }
                }
            });
        } else {
            $('#paket-info').addClass('hidden');
        }
    });
});
</script>
@endpush
```

### Contoh 3: Dropdown Karyawan dengan Search (dari PREFAST)

```blade
<!-- Karyawan Dropdown (Data dari PREFAST API) -->
<div class="mb-4">
    <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-2">
        Karyawan <span class="text-red-500">*</span>
    </label>
    <select id="karyawan_id"
            name="karyawan_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            required>
        <option value="">-- Pilih Karyawan --</option>
    </select>
    <p class="text-xs text-gray-500 mt-1">Data dari PREFAST API</p>
</div>

<!-- Display karyawan info -->
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
        <input type="text" id="karyawan_nip" readonly
               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Posisi</label>
        <input type="text" id="karyawan_position" readonly
               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 dengan AJAX
    $('#karyawan_id').select2({
        placeholder: 'Cari karyawan...',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("prefast.karyawan.dropdown") }}', // Note: dari PREFAST, bukan BEFAST
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    employment_status: 'active',
                    limit: 50
                };
            },
            processResults: function (response) {
                if (response.success) {
                    return {
                        results: response.data
                    };
                }
                return { results: [] };
            },
            cache: true
        }
    });

    // Event ketika karyawan dipilih
    $('#karyawan_id').on('select2:select', function (e) {
        const data = e.params.data;

        // Auto-fill karyawan info
        $('#karyawan_nip').val(data.nip || '');
        $('#karyawan_position').val(data.position || '');

        console.log('Selected karyawan:', data);
    });
});
</script>
@endpush
```

---

## 📝 Contoh Update File create-ba-kesepakatan.blade.php

Berikut adalah contoh lengkap update file untuk menggunakan dropdown dari BEFAST API:

```blade
@extends('layouts.main')

@section('title', 'BA Kesepakatan Perubahan Layanan')
@section('subtitle', 'Buat BA Kesepakatan dengan data dari BEFAST')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">BA Kesepakatan Perubahan Layanan</h2>
        <a href="{{ route('indexDocuments') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class='bx bx-arrow-back'></i>
            <span>Back</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="baKesepakatan Form">
            @csrf

            <!-- ============================================ -->
            <!-- SECTION: Data Customer dari BEFAST API -->
            <!-- ============================================ -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class='bx bx-user'></i> Data Customer (dari BEFAST)
                </h3>

                <!-- Subscription Dropdown dengan Select2 -->
                <div class="mb-4">
                    <label for="subscription_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Subscription <span class="text-red-500">*</span>
                    </label>
                    <select id="subscription_id"
                            name="subscription_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            required>
                        <option value="">-- Cari Subscription --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class='bx bx-info-circle'></i> Data langsung dari BEFAST API
                    </p>
                </div>

                <!-- Customer Info (Auto-filled) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer ID</label>
                        <input type="text" id="customer_id" name="customer_id" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="customer_phone" name="customer_phone" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION: Paket Awal -->
            <!-- ============================================ -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    Paket Awal
                </h3>

                <!-- Paket Awal Dropdown -->
                <div class="mb-4">
                    <label for="paket_awal_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Paket Awal <span class="text-red-500">*</span>
                    </label>
                    <select id="paket_awal_id" name="paket_awal_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="">-- Pilih Paket --</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan</label>
                        <input type="text" id="bandwidth_awal_jenis" name="bandwidth_awal_jenis" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas</label>
                        <input type="text" id="bandwidth_awal_kapasitas" name="bandwidth_awal_kapasitas" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                        <input type="number" id="bandwidth_awal_biaya" name="bandwidth_awal_biaya" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION: Paket Sekarang -->
            <!-- ============================================ -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    Paket Sekarang (Perubahan)
                </h3>

                <!-- Paket Sekarang Dropdown -->
                <div class="mb-4">
                    <label for="paket_sekarang_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Paket Baru <span class="text-red-500">*</span>
                    </label>
                    <select id="paket_sekarang_id" name="paket_sekarang_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="">-- Pilih Paket --</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan</label>
                        <input type="text" id="bandwidth_sekarang_jenis" name="bandwidth_sekarang_jenis" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas</label>
                        <input type="text" id="bandwidth_sekarang_kapasitas" name="bandwidth_sekarang_kapasitas" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                        <input type="number" id="bandwidth_sekarang_biaya" name="bandwidth_sekarang_biaya" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>

            <!-- Starting Billing -->
            <div class="mb-6">
                <label for="starting_billing" class="block text-sm font-medium text-gray-700 mb-2">
                    Starting Billing <span class="text-red-500">*</span>
                </label>
                <input type="date" id="starting_billing" name="starting_billing" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <button type="button" id="previewBtn"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class='bx bx-show'></i> Preview
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class='bx bx-download'></i> Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // =============================================
    // Initialize Subscription Dropdown dengan Select2
    // =============================================
    $('#subscription_id').select2({
        placeholder: 'Cari subscription (ketik nama atau ID)...',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("befast.subscriptions.dropdown") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    limit: 50
                };
            },
            processResults: function (response) {
                if (response.success) {
                    return { results: response.data };
                }
                Swal.fire('Error', 'Gagal memuat data subscription', 'error');
                return { results: [] };
            },
            cache: true
        }
    });

    // Event ketika subscription dipilih
    $('#subscription_id').on('select2:select', function (e) {
        const data = e.params.data;
        $('#customer_id').val(data.customer_id || '');
        $('#customer_name').val(data.text.split('(')[0].trim());
        $('#customer_phone').val(data.phone || '');
    });

    // =============================================
    // Load Pakets Dropdown (Paket Awal & Sekarang)
    // =============================================
    function loadPakets() {
        $.ajax({
            url: '{{ route("befast.pakets.dropdown") }}',
            type: 'GET',
            data: { status: 'active' },
            success: function(response) {
                if (response.success) {
                    const options = response.data.map(item =>
                        `<option value="${item.id}" data-nama="${item.nama_paket}" data-speed="${item.speed}" data-price="${item.price}">${item.text}</option>`
                    ).join('');

                    $('#paket_awal_id').append(options);
                    $('#paket_sekarang_id').append(options);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data paket', 'error');
            }
        });
    }

    loadPakets();

    // Event Paket Awal dipilih
    $('#paket_awal_id').on('change', function() {
        const selected = $(this).find(':selected');
        $('#bandwidth_awal_jenis').val('Internet Dedicated');
        $('#bandwidth_awal_kapasitas').val(selected.data('speed') || '');
        $('#bandwidth_awal_biaya').val(selected.data('price') || '');
    });

    // Event Paket Sekarang dipilih
    $('#paket_sekarang_id').on('change', function() {
        const selected = $(this).find(':selected');
        $('#bandwidth_sekarang_jenis').val('Internet Dedicated');
        $('#bandwidth_sekarang_kapasitas').val(selected.data('speed') || '');
        $('#bandwidth_sekarang_biaya').val(selected.data('price') || '');
    });

    // =============================================
    // Preview PDF
    // =============================================
    $('#previewBtn').on('click', function() {
        // Validation check...
        // Submit to preview endpoint
    });

    // =============================================
    // Generate PDF
    // =============================================
    $('#baKesepakatanForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("generateBaKesepakatan") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Generating PDF...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function(blob, status, xhr) {
                Swal.close();

                // Download PDF
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'BA-Kesepakatan-' + Date.now() + '.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                Swal.fire('Success!', 'PDF berhasil di-generate', 'success');
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire('Error', 'Gagal generate PDF', 'error');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
```

---

## 🧪 Testing & Debugging

### 1. Test API Connections

```bash
# Test BEFAST API (Subscription & Paket)
curl http://localhost:8001/api/befast/test

# Expected response:
{
  "success": true,
  "message": "Connection to BEFAST API successful",
  "data": {
    "success": true,
    "message": "BEFAST API is running",
    "version": "1.0.0"
  }
}

# Test PREFAST API (Karyawan)
curl http://localhost:8001/api/prefast/test

# Expected response:
{
  "success": true,
  "message": "Connection to PREFAST API successful",
  "data": {
    "success": true,
    "message": "PREFAST API is running",
    "version": "1.0.0"
  }
}
```

### 2. Test Dropdown Endpoints

```javascript
// Test di Browser Console (setelah login ke FASTKEU)

// Dari BEFAST
fetch('/api/befast/subscriptions/dropdown')
    .then(r => r.json())
    .then(data => console.log('Subscriptions (BEFAST):', data));

fetch('/api/befast/pakets/dropdown')
    .then(r => r.json())
    .then(data => console.log('Pakets (BEFAST):', data));

// Dari PREFAST
fetch('/api/prefast/karyawan/dropdown')
    .then(r => r.json())
    .then(data => console.log('Karyawan (PREFAST):', data));
```

### 3. Clear Cache

```bash
# Clear cache dari route
curl -X POST http://localhost:8001/api/befast/cache/clear

# Atau dari Laravel artisan
php artisan cache:clear
```

---

## 🔧 Troubleshooting

### Error: Connection timeout
```env
# Increase timeout di .env
BEFAST_API_TIMEOUT=60
PREFAST_API_TIMEOUT=60
```

### Error: 401 Unauthorized

**Untuk BEFAST:**
```env
# Pastikan token sama
# BEFAST .env:
API_TOKEN=befast_api_token_12345_secure_random_string

# FASTKEU .env:
BEFAST_API_TOKEN=befast_api_token_12345_secure_random_string
```

**Untuk PREFAST:**
```env
# Pastikan token sama
# PREFAST .env:
API_TOKEN=prefast_api_token_67890_secure_random_string

# FASTKEU .env:
PREFAST_API_TOKEN=prefast_api_token_67890_secure_random_string
```

### Error: CORS
- Pastikan CORS sudah di-setup di BEFAST (lihat `BEFAST_API_IMPLEMENTATION.md`)
- Pastikan CORS sudah di-setup di PREFAST (lihat `PREFAST_API_IMPLEMENTATION.md`)

---

## 📊 Summary: 2 Sumber API

| Aspek | BEFAST | PREFAST |
|-------|--------|---------|
| **Data** | Subscription, Paket | Karyawan |
| **Port** | 8000 | 8002 |
| **URL (.env)** | `BEFAST_API_URL` | `PREFAST_API_URL` |
| **Token (.env)** | `BEFAST_API_TOKEN` | `PREFAST_API_TOKEN` |
| **Routes FASTKEU** | `/api/befast/*` | `/api/prefast/*` |
| **Dokumentasi** | `BEFAST_API_IMPLEMENTATION.md` | `PREFAST_API_IMPLEMENTATION.md` |

**PENTING:**
- ✅ Token harus berbeda untuk keamanan
- ✅ Kedua API harus running (port 8000 & 8002)
- ✅ FASTKEU harus setup kedua config di `.env`

---

## 📚 Resources

- Select2 Documentation: https://select2.org/
- Laravel HTTP Client: https://laravel.com/docs/12.x/http-client
- SweetAlert2: https://sweetalert2.github.io/

---

## 📝 Dokumentasi Terkait

- `BEFAST_API_IMPLEMENTATION.md` - Setup API di Befast (Subscription & Paket)
- `PREFAST_API_IMPLEMENTATION.md` - Setup API di Prefast (Karyawan)
- `README_API_INTEGRATION.md` - Overview lengkap integrasi

---

**✅ Selesai!** FASTKEU sekarang bisa menggunakan dropdown dari 2 sumber API (BEFAST & PREFAST).
