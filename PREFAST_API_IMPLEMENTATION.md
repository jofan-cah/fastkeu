# 🚀 IMPLEMENTASI API DI PREFAST (Laravel 9)

## 🎯 Tujuan
Buat API endpoint READ-ONLY untuk FASTKEU ambil data:
- **Karyawan** (data pegawai/karyawan)

**PENTING:** Ini API READ-ONLY untuk dropdown di form FASTKEU. Tidak ada CREATE/UPDATE/DELETE.

**CATATAN:** Data Subscription & Paket ada di project BEFAST (lihat file `BEFAST_API_IMPLEMENTATION.md`)

---

## ⚠️ CATATAN PENTING
**Kode di bawah adalah TEMPLATE!**

Sesuaikan dengan struktur database/model Prefast kalian:
- ✅ **Nama kolom** mungkin beda (misal: `full_name` vs `nama_lengkap`)
- ✅ **Nama tabel** mungkin beda (misal: `karyawan` vs `employees`)
- ✅ **Primary key** bisa beda (misal: `id` vs `karyawan_id`)
- ✅ **Status field** bisa beda (misal: `employment_status` vs `status_kerja`)

**Cek dulu struktur database kalian sebelum copy-paste!**

---

## 📁 File yang Perlu Dibuat

```
prefast/
├── app/Http/Controllers/Api/
│   └── KaryawanApiController.php      ← Buat ini
├── app/Http/Middleware/
│   └── ApiTokenAuth.php               ← Buat ini
├── routes/api.php                     ← Update ini
├── app/Http/Kernel.php                ← Update ini
└── .env                               ← Update ini
```

---

## 📋 Quick Summary (Untuk Claude)

Jika kamu adalah Claude yang akan implement ini:

1. **Buat 1 controller API** di `app/Http/Controllers/Api/`:
   - KaryawanApiController (data pegawai/karyawan)

2. **Buat 1 middleware** di `app/Http/Middleware/ApiTokenAuth.php` (untuk auth token)

3. **Update routes** di `routes/api.php` (tambah endpoint API)

4. **Register middleware** di `app/Http/Kernel.php`

5. **Setup token** di `.env`

6. **PENTING:** Cek dulu struktur tabel/kolom database Prefast, sesuaikan dengan kode template!

**Note:** Data Subscription & Paket bukan tanggung jawab Prefast, itu ada di project BEFAST terpisah.

---

## 📝 STEP 1: Buat API Controller di PREFAST

### 1.1 KaryawanApiController.php

**Lokasi:** `prefast/app/Http/Controllers/Api/KaryawanApiController.php`

**⚠️ SESUAIKAN:**
- Nama kolom sesuai tabel `karyawan` di database kalian
- Primary key mungkin bukan `karyawan_id`, tapi `id` atau lainnya
- Field seperti `full_name`, `nip`, `position`, `phone`, `employment_status` → cek di database kalian

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KaryawanApiController extends Controller
{
    /**
     * Get all karyawan (dengan pagination)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $search = $request->get('search', '');
            $status = $request->get('employment_status', null);

            $query = Karyawan::query()
                ->select([
                    'karyawan_id',
                    'user_id',
                    'department_id',
                    'nip',
                    'full_name',
                    'position',
                    'phone',
                    'employment_status',
                    'staff_status'
                ]);

            // Search functionality
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            }

            // Filter by employment status
            if ($status) {
                $query->where('employment_status', $status);
            }

            $karyawan = $query->orderBy('full_name', 'asc')
                             ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan retrieved successfully',
                'data' => $karyawan->items(),
                'meta' => [
                    'current_page' => $karyawan->currentPage(),
                    'last_page' => $karyawan->lastPage(),
                    'per_page' => $karyawan->perPage(),
                    'total' => $karyawan->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Karyawan Index Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get karyawan by ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $karyawan = Karyawan::find($id);

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Karyawan retrieved successfully',
                'data' => $karyawan
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Karyawan Show Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get karyawan untuk dropdown (simplified)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dropdown(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $status = $request->get('employment_status', 'active');
            $limit = $request->get('limit', 100);

            $query = Karyawan::where('employment_status', $status)
                ->select(['karyawan_id', 'full_name', 'nip', 'position', 'phone']);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            }

            $karyawan = $query->orderBy('full_name', 'asc')
                             ->limit($limit)
                             ->get()
                             ->map(function($item) {
                                 return [
                                     'id' => $item->karyawan_id,
                                     'text' => $item->full_name . ' (' . $item->nip . ')',
                                     'nip' => $item->nip,
                                     'position' => $item->position,
                                     'phone' => $item->phone,
                                 ];
                             });

            return response()->json([
                'success' => true,
                'data' => $karyawan
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Karyawan Dropdown Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 📝 STEP 2: Setup Routes di PREFAST

Lokasi: `prefast/routes/api.php`

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KaryawanApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public route untuk test API
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'PREFAST API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Protected routes dengan API Token
Route::middleware('api.token')->group(function () {

    // ============================================
    // KARYAWAN API
    // ============================================
    Route::prefix('karyawan')->group(function () {
        Route::get('/', [KaryawanApiController::class, 'index']); // List all
        Route::get('/dropdown', [KaryawanApiController::class, 'dropdown']); // Untuk dropdown
        Route::get('/{id}', [KaryawanApiController::class, 'show']); // Detail by ID
    });
});
```

---

## 📝 STEP 3: Buat API Token Middleware

Lokasi: `prefast/app/Http/Middleware/ApiTokenAuth.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $validToken = env('API_TOKEN', 'your-secret-api-token-here');

        if (!$token || $token !== $validToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing API token.'
            ], 401);
        }

        return $next($request);
    }
}
```

---

## 📝 STEP 4: Register Middleware

Lokasi: `prefast/app/Http/Kernel.php`

Tambahkan di `protected $routeMiddleware`:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'api.token' => \App\Http\Middleware\ApiTokenAuth::class,
];
```

---

## 📝 STEP 5: Setup Environment di PREFAST

Lokasi: `prefast/.env`

```env
# API Configuration
API_TOKEN=prefast_api_token_67890_secure_random_string

# CORS Configuration (jika perlu)
APP_URL=http://localhost:8002
```

**⚠️ PENTING:** Token harus beda dengan BEFAST! Jangan pakai token yang sama.

---

## 📝 STEP 6: Enable CORS di PREFAST (Optional)

Lokasi: `prefast/config/cors.php`

```php
<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:8001', // FASTKEU URL
        'http://127.0.0.1:8001',
        // Tambahkan production URL jika ada
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

---

## 🧪 TESTING API di PREFAST

**⚠️ Ganti token sesuai dengan yang di `.env` kalian!**

```bash
# 1. Test endpoint public (tidak perlu token)
curl http://localhost:8002/api/test

# Jika berhasil, response:
# {"success":true,"message":"PREFAST API is running","version":"1.0.0"}

# 2. Test Karyawan (perlu token)
curl -H "Authorization: Bearer prefast_api_token_67890_secure_random_string" \
     http://localhost:8002/api/karyawan/dropdown
```

**Jika response 401 Unauthorized:** Token salah atau belum setup di `.env`

**Jika response 500 Error:** Cek nama tabel/kolom di controller, mungkin tidak sesuai dengan database

---

## ✅ CHECKLIST IMPLEMENTASI di PREFAST

- [ ] Buat folder `app/Http/Controllers/Api/`
- [ ] Copy controller file (KaryawanApiController)
- [ ] Buat `app/Http/Middleware/ApiTokenAuth.php`
- [ ] Update `routes/api.php`
- [ ] Register middleware di `app/Http/Kernel.php`
- [ ] Setup API_TOKEN di `.env`
- [ ] (Optional) Setup CORS di `config/cors.php`
- [ ] Test endpoint dengan Postman/cURL

**Note:** Untuk Subscription & Paket, lihat file `BEFAST_API_IMPLEMENTATION.md` (project terpisah)

---

## 🔗 API Endpoints yang Tersedia

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/test` | Test API connection (no auth) |
| GET | `/api/karyawan` | Get all karyawan (paginated) |
| GET | `/api/karyawan/dropdown` | Get karyawan for dropdown |
| GET | `/api/karyawan/{id}` | Get karyawan detail |

---

## 📝 SETELAH SELESAI

Setelah semua endpoint API berjalan dan testing berhasil:

1. **Kirim ke tim FASTKEU:**
   - URL API Prefast (misal: `http://localhost:8002` atau `https://prefast.yourdomain.com`)
   - API Token yang sudah di-generate
   - List endpoint yang tersedia (copy tabel di atas)

2. **Contoh format yang dikirim:**
   ```
   PREFAST API Ready!

   URL: http://localhost:8002
   Token: prefast_api_token_67890_secure_random_string

   Endpoints:
   - GET /api/karyawan/dropdown (Data Karyawan/Pegawai)

   Note: Data Subscription & Paket dari BEFAST (project terpisah)
   ```

3. Tim FASTKEU akan update `.env` mereka dengan info ini

---

## 🆘 Troubleshooting

### Error: "Class KaryawanApiController not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Error: "Table karyawan doesn't exist"
- Cek nama tabel di database kalian
- Update nama tabel di controller (di bagian `Model::query()`)

### Error: "Column karyawan_id not found"
- Cek nama kolom di database kalian
- Update nama kolom di controller (di bagian `->select([...])`)

### API lambat / timeout
- Tambahkan index di database untuk kolom yang sering di-search
- Pertimbangkan caching jika data jarang berubah

---

## 📌 Perbedaan dengan BEFAST

| Aspek | BEFAST | PREFAST |
|-------|--------|---------|
| Data | Subscription & Paket | Karyawan |
| Port | 8000 | 8002 |
| Token | `BEFAST_API_TOKEN` | `PREFAST_API_TOKEN` |
| Endpoints | `/api/subscriptions`, `/api/pakets` | `/api/karyawan` |

**PENTING:** Token harus berbeda untuk keamanan!

---

**✅ SELESAI!** Tim FASTKEU bisa mulai konsumsi API ini untuk dropdown Karyawan mereka.
