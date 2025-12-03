# 📖 PANDUAN LENGKAP: Integrasi API BEFAST ↔ FASTKEU

> **Tujuan:** Membuat dropdown di FASTKEU yang terisi otomatis dari data BEFAST melalui API
>
> **Use Case:** Form di FASTKEU (Laravel 12) bisa mengambil data Subscriptions, Pakets, dan Karyawan dari BEFAST (Laravel 9) secara real-time

---

## 📋 Daftar Isi

1. [Overview Sistem](#overview-sistem)
2. [Implementasi di BEFAST (API Provider)](#implementasi-di-befast)
3. [Implementasi di FASTKEU (API Consumer)](#implementasi-di-fastkeu)
4. [Cara Penggunaan](#cara-penggunaan)
5. [Testing](#testing)
6. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview Sistem

### Arsitektur

```
┌─────────────────────────────────────────────────────────────────┐
│                         BEFAST (Laravel 9)                       │
│                         Port: 8000                               │
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐     │
│  │ Subscriptions│    │   Pakets     │    │  Karyawan    │     │
│  │   Model      │    │   Model      │    │   Model      │     │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘     │
│         │                   │                   │              │
│  ┌──────▼────────────────────▼────────────────────▼───────┐   │
│  │         API Controllers                                 │   │
│  │  - SubscriptionApiController                           │   │
│  │  - PaketApiController                                  │   │
│  │  - KaryawanApiController                               │   │
│  └────────────────────────┬────────────────────────────────┘   │
│                           │                                     │
│                    ┌──────▼──────┐                             │
│                    │ routes/api.php│                            │
│                    └──────┬──────┘                             │
└───────────────────────────┼─────────────────────────────────────┘
                            │
                            │ HTTP API Calls
                            │ (Bearer Token Auth)
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│                       FASTKEU (Laravel 12)                       │
│                         Port: 8001                               │
│                                                                  │
│  ┌────────────────────────────────────────────────────┐         │
│  │          BefastApiService (Service Layer)          │         │
│  │  - getSubscriptionsDropdown()                      │         │
│  │  - getPaketsDropdown()                             │         │
│  │  - getKaryawanDropdown()                           │         │
│  └────────────────────┬───────────────────────────────┘         │
│                       │                                          │
│  ┌────────────────────▼───────────────────────────────┐         │
│  │      BefastDataController (AJAX Endpoints)         │         │
│  │  GET /api/befast/subscriptions/dropdown            │         │
│  │  GET /api/befast/pakets/dropdown                   │         │
│  │  GET /api/befast/karyawan/dropdown                 │         │
│  └────────────────────┬───────────────────────────────┘         │
│                       │                                          │
│  ┌────────────────────▼───────────────────────────────┐         │
│  │         Blade Templates (Forms)                    │         │
│  │  - Select2 Dropdown                                │         │
│  │  - Auto-fill dari API                              │         │
│  └────────────────────────────────────────────────────┘         │
└──────────────────────────────────────────────────────────────────┘
```

### Flow Data

1. **User** mengakses form di FASTKEU
2. **Frontend** (JavaScript) request dropdown data ke internal endpoint FASTKEU
3. **BefastDataController** menerima request
4. **BefastApiService** melakukan HTTP call ke BEFAST API
5. **BEFAST API** mengembalikan data dari database
6. **FASTKEU** menampilkan data di dropdown (Select2)

---

## 🔧 Implementasi di BEFAST

### 📁 File yang Perlu Dibuat

```bash
befast/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── SubscriptionApiController.php  ← Buat ini
│   │   │       ├── PaketApiController.php          ← Buat ini
│   │   │       └── KaryawanApiController.php       ← Buat ini
│   │   └── Middleware/
│   │       └── ApiTokenAuth.php                    ← Buat ini
└── routes/
    └── api.php                                     ← Update ini
```

### ⚙️ Setup

1. **Buat Controllers**
   - Copy kode dari file `BEFAST_API_IMPLEMENTATION.md`
   - Paste ke folder yang sesuai

2. **Setup Routes**
   - Buka `befast/routes/api.php`
   - Tambahkan routes untuk API (lihat dokumentasi)

3. **Buat Middleware Authentication**
   - Buat file `ApiTokenAuth.php`
   - Register di `app/Http/Kernel.php`

4. **Setup Environment**
   ```env
   # befast/.env
   API_TOKEN=befast_api_token_12345_secure_random_string
   ```

5. **Enable CORS** (jika perlu)
   ```env
   # befast/config/cors.php
   'allowed_origins' => [
       'http://localhost:8001',  # FASTKEU URL
   ],
   ```

### 🧪 Test API di BEFAST

```bash
# Test endpoint public
curl http://localhost:8000/api/test

# Test dengan token
curl -H "Authorization: Bearer befast_api_token_12345_secure_random_string" \
     http://localhost:8000/api/subscriptions/dropdown
```

**✅ Jika berhasil**, Anda akan melihat response JSON berisi data subscriptions.

---

## 🔧 Implementasi di FASTKEU

### 📁 File yang Sudah Dibuat (Otomatis)

```bash
fastkeu/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── BefastDataController.php      ✅ Sudah dibuat
│   └── Services/
│       └── BefastApiService.php               ✅ Sudah dibuat
├── config/
│   └── services.php                           ✅ Sudah diupdate
├── routes/
│   └── web.php                                ✅ Sudah diupdate
└── .env                                       ✅ Sudah diupdate
```

### ⚙️ Konfigurasi

File `.env` sudah diupdate dengan:

```env
# BEFAST API Configuration
BEFAST_API_URL=http://localhost:8000
BEFAST_API_TOKEN=befast_api_token_12345_secure_random_string
BEFAST_API_TIMEOUT=30
```

**⚠️ PENTING:** Pastikan token sama dengan yang di BEFAST!

### 🧪 Test Koneksi

```bash
# Jalankan FASTKEU
php artisan serve --port=8001

# Test koneksi API (di browser atau cURL)
curl http://localhost:8001/api/befast/test
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Connection to BEFAST API successful"
}
```

---

## 🎨 Cara Penggunaan di Template

### Install Select2 (jika belum)

Tambahkan di layout atau template:

```html
<!-- Di head -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Sebelum closing body -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

### Contoh: Dropdown Subscriptions

```blade
<!-- HTML -->
<select id="subscription_id" name="subscription_id" class="form-control" required>
    <option value="">-- Pilih Subscription --</option>
</select>

<!-- JavaScript -->
<script>
$('#subscription_id').select2({
    placeholder: 'Cari subscription...',
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
            return { results: [] };
        },
        cache: true
    }
});

// Event ketika dipilih
$('#subscription_id').on('select2:select', function (e) {
    const data = e.params.data;
    console.log('Selected:', data);
    // data.id = subscription ID
    // data.text = subscription name
    // data.customer_id, data.email, data.phone
});
</script>
```

### Contoh: Dropdown Pakets (Tanpa Search)

```blade
<select id="paket_id" name="paket_id" class="form-control" required>
    <option value="">-- Pilih Paket --</option>
</select>

<script>
// Load semua paket active
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
    }
});
</script>
```

### Contoh: Dropdown Karyawan dengan Auto-fill

```blade
<!-- Dropdown -->
<select id="karyawan_id" name="karyawan_id"></select>

<!-- Fields yang akan di auto-fill -->
<input type="text" id="karyawan_nip" readonly>
<input type="text" id="karyawan_position" readonly>

<script>
$('#karyawan_id').select2({
    placeholder: 'Cari karyawan...',
    ajax: {
        url: '{{ route("befast.karyawan.dropdown") }}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term,
                employment_status: 'active'
            };
        },
        processResults: function (response) {
            if (response.success) {
                return { results: response.data };
            }
            return { results: [] };
        }
    }
});

// Auto-fill ketika dipilih
$('#karyawan_id').on('select2:select', function (e) {
    const data = e.params.data;
    $('#karyawan_nip').val(data.nip);
    $('#karyawan_position').val(data.position);
});
</script>
```

---

## 🧪 Testing

### 1. Test API Endpoints (BEFAST)

```bash
# Di terminal
curl -H "Authorization: Bearer befast_api_token_12345_secure_random_string" \
     http://localhost:8000/api/subscriptions/dropdown

curl -H "Authorization: Bearer befast_api_token_12345_secure_random_string" \
     http://localhost:8000/api/pakets/dropdown

curl -H "Authorization: Bearer befast_api_token_12345_secure_random_string" \
     http://localhost:8000/api/karyawan/dropdown
```

### 2. Test Internal Endpoints (FASTKEU)

```bash
# Di browser (setelah login)
http://localhost:8001/api/befast/test
http://localhost:8001/api/befast/subscriptions/dropdown
http://localhost:8001/api/befast/pakets/dropdown
http://localhost:8001/api/befast/karyawan/dropdown
```

### 3. Test di Browser Console

```javascript
// Buka form di FASTKEU, buka Console (F12)
fetch('/api/befast/subscriptions/dropdown')
    .then(r => r.json())
    .then(data => console.log(data));
```

---

## 🔧 Troubleshooting

### ❌ Error: "Connection failed"

**Penyebab:** BEFAST API tidak bisa diakses

**Solusi:**
1. Pastikan BEFAST running di port 8000
2. Test: `curl http://localhost:8000/api/test`
3. Cek firewall/antivirus

### ❌ Error: "401 Unauthorized"

**Penyebab:** Token tidak valid

**Solusi:**
1. Pastikan token sama di kedua `.env`
2. Restart kedua aplikasi setelah update `.env`
3. Clear config cache: `php artisan config:clear`

### ❌ Error: "CORS policy"

**Penyebab:** CORS tidak disetup di BEFAST

**Solusi:**
```bash
# Di BEFAST
composer require fruitcake/laravel-cors

# Edit config/cors.php
'allowed_origins' => ['http://localhost:8001'],
```

### ❌ Dropdown kosong / tidak muncul data

**Debugging:**
1. Buka Browser Console (F12)
2. Lihat tab Network
3. Cek request ke `/api/befast/...`
4. Lihat response: success true/false?
5. Cek Laravel log: `storage/logs/laravel.log`

### ❌ Error: "Class BefastApiService not found"

**Solusi:**
```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

---

## 📚 API Endpoints Reference

### BEFAST API (Port 8000)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/test` | ❌ No | Test API |
| GET | `/api/subscriptions` | ✅ Yes | List all subscriptions |
| GET | `/api/subscriptions/dropdown` | ✅ Yes | Subscriptions untuk dropdown |
| GET | `/api/subscriptions/{id}` | ✅ Yes | Detail subscription |
| GET | `/api/pakets` | ✅ Yes | List all pakets |
| GET | `/api/pakets/dropdown` | ✅ Yes | Pakets untuk dropdown |
| GET | `/api/pakets/{id}` | ✅ Yes | Detail paket |
| GET | `/api/karyawan` | ✅ Yes | List all karyawan |
| GET | `/api/karyawan/dropdown` | ✅ Yes | Karyawan untuk dropdown |
| GET | `/api/karyawan/{id}` | ✅ Yes | Detail karyawan |

### FASTKEU Internal API (Port 8001)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/befast/test` | Session | Test connection |
| GET | `/api/befast/subscriptions/dropdown` | Session | Get subscriptions |
| GET | `/api/befast/subscriptions/{id}` | Session | Get subscription detail |
| GET | `/api/befast/pakets/dropdown` | Session | Get pakets |
| GET | `/api/befast/pakets/{id}` | Session | Get paket detail |
| GET | `/api/befast/karyawan/dropdown` | Session | Get karyawan |
| GET | `/api/befast/karyawan/{id}` | Session | Get karyawan detail |
| POST | `/api/befast/cache/clear` | Session | Clear API cache |

---

## 📝 Summary

### Checklist Implementasi

**Di BEFAST:**
- [ ] Buat 3 API Controllers (Subscription, Paket, Karyawan)
- [ ] Buat ApiTokenAuth Middleware
- [ ] Update routes/api.php
- [ ] Setup API_TOKEN di .env
- [ ] Enable CORS (optional)
- [ ] Test semua endpoints

**Di FASTKEU:**
- [x] BefastApiService sudah dibuat
- [x] BefastDataController sudah dibuat
- [x] Routes sudah diupdate
- [x] Config services.php sudah diupdate
- [x] .env sudah diupdate
- [ ] Update template blade untuk pakai dropdown
- [ ] Install Select2 (jika belum)
- [ ] Test dropdown di form

---

## 💡 Tips

1. **Gunakan Cache:** API calls sudah otomatis di-cache 5-10 menit untuk performa
2. **Clear Cache:** Jika data tidak update, clear cache: `php artisan cache:clear`
3. **Select2 Lazy Loading:** Untuk data besar, gunakan search dengan delay 250ms
4. **Error Handling:** Selalu handle error di frontend dengan SweetAlert atau notification
5. **Token Security:** Jangan commit .env ke git!

---

## 📞 Support

Jika ada masalah:
1. Cek Laravel logs: `storage/logs/laravel.log`
2. Cek Browser Console (F12 → Console tab)
3. Cek Network tab (F12 → Network tab)
4. Test endpoint manual dengan cURL/Postman

---

**🎉 Selesai! Sekarang Anda bisa menggunakan dropdown dari BEFAST API di FASTKEU.**

Dokumentasi lengkap ada di:
- `BEFAST_API_IMPLEMENTATION.md` - Implementasi di BEFAST
- `FASTKEU_INTEGRATION.md` - Implementasi di FASTKEU + Contoh Template
