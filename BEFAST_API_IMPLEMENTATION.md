# IMPLEMENTASI API DI BEFAST (Laravel 9)

## Tujuan
Buat API endpoint READ-ONLY untuk FASTKEU ambil data:
- **Subscriptions** (data customer/pelanggan)
- **Pakets** (data paket layanan)

**PENTING:** Ini API READ-ONLY untuk dropdown di form FASTKEU. Tidak ada CREATE/UPDATE/DELETE.

**CATATAN:** Data Karyawan ada di project PREFAST (lihat file `PREFAST_API_IMPLEMENTATION.md`)

---

## CATATAN PENTING
**Kode di bawah adalah TEMPLATE!**

Sesuaikan dengan struktur database/model Befast kalian:
- ✅ **Nama kolom** mungkin beda (misal: `subs_name` vs `subscription_name`)
- ✅ **Nama tabel** mungkin beda (misal: `subscriptions` vs `subs`)
- ✅ **Primary key** bisa beda (misal: `id` vs `subs_id`)
- ✅ **Relasi** bisa beda (misal: ada/tidak ada relasi ke tabel lain)

**Cek dulu struktur database kalian sebelum copy-paste!**

---

## File yang Perlu Dibuat

```
befast/
├── app/Http/Controllers/Api/
│   ├── SubscriptionApiController.php   ← Buat ini
│   └── PaketApiController.php          ← Buat ini
├── app/Http/Middleware/
│   └── ApiTokenAuth.php                ← Buat ini
├── routes/api.php                      ← Update ini
├── app/Http/Kernel.php                 ← Update ini
└── .env                                ← Update ini
```

---

## 📋 Quick Summary (Untuk Claude)

Jika kamu adalah Claude yang akan implement ini:

1. **Buat 2 controller API** di `app/Http/Controllers/Api/`:
   - SubscriptionApiController (data pelanggan)
   - PaketApiController (data paket layanan)

2. **Buat 1 middleware** di `app/Http/Middleware/ApiTokenAuth.php` (untuk auth token)

3. **Update routes** di `routes/api.php` (tambah endpoint API)

4. **Register middleware** di `app/Http/Kernel.php`

5. **Setup token** di `.env`

6. **PENTING:** Cek dulu struktur tabel/kolom database Befast, sesuaikan dengan kode template!

**Note:** Data Karyawan bukan tanggung jawab Befast, itu ada di project PREFAST terpisah.

---

## 📝 STEP 1: Buat API Controllers di BEFAST

### 1.1 SubscriptionApiController.php

**Lokasi:** `befast/app/Http/Controllers/Api/SubscriptionApiController.php`

**⚠️ SESUAIKAN:**
- Nama kolom sesuai tabel `subscriptions` di database kalian
- Primary key mungkin bukan `subs_id`, tapi `id` atau lainnya
- Field seperti `subs_name`, `customer_id`, `email`, `handphone` → cek di database kalian

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionApiController extends Controller
{
    /**
     * Get all subscriptions (dengan pagination)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $search = $request->get('search', '');

            $query = Subscription::query()
                ->select([
                    'subs_id',
                    'customer_id',
                    'subs_name',
                    'email',
                    'handphone',
                    'pakets_id',
                    'tanggal_aktivasi',
                    'activity_type'
                ]);

            // Search functionality
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('subs_id', 'like', "%{$search}%")
                      ->orWhere('subs_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('handphone', 'like', "%{$search}%");
                });
            }

            $subscriptions = $query->orderBy('created_at', 'desc')
                                   ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Subscriptions retrieved successfully',
                'data' => $subscriptions->items(),
                'meta' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Subscription Index Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription by ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $subscription = Subscription::find($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription retrieved successfully',
                'data' => $subscription
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Subscription Show Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscriptions untuk dropdown (simplified)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dropdown(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 100);

            $query = Subscription::query()
                ->select(['subs_id', 'subs_name', 'customer_id', 'email', 'handphone']);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('subs_id', 'like', "%{$search}%")
                      ->orWhere('subs_name', 'like', "%{$search}%");
                });
            }

            $subscriptions = $query->orderBy('subs_name', 'asc')
                                   ->limit($limit)
                                   ->get()
                                   ->map(function($item) {
                                       return [
                                           'id' => $item->subs_id,
                                           'text' => $item->subs_name . ' (' . $item->subs_id . ')',
                                           'customer_id' => $item->customer_id,
                                           'email' => $item->email,
                                           'phone' => $item->handphone,
                                       ];
                                   });

            return response()->json([
                'success' => true,
                'data' => $subscriptions
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Subscription Dropdown Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### 1.2 PaketApiController.php

**Lokasi:** `befast/app/Http/Controllers/Api/PaketApiController.php`

**⚠️ SESUAIKAN:**
- Nama kolom sesuai tabel `pakets` di database kalian
- Primary key mungkin bukan `pakets_id`, tapi `id` atau lainnya
- Field seperti `nama_paket`, `speed`, `price`, `status`, `description` → cek di database kalian

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaketApiController extends Controller
{
    /**
     * Get all pakets
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $status = $request->get('status', null);

            $query = Paket::query();

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            $pakets = $query->orderBy('nama_paket', 'asc')
                           ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Pakets retrieved successfully',
                'data' => $pakets->items(),
                'meta' => [
                    'current_page' => $pakets->currentPage(),
                    'last_page' => $pakets->lastPage(),
                    'per_page' => $pakets->perPage(),
                    'total' => $pakets->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Paket Index Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pakets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paket by ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $paket = Paket::find($id);

            if (!$paket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paket retrieved successfully',
                'data' => $paket
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Paket Show Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve paket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pakets untuk dropdown
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dropdown(Request $request)
    {
        try {
            $status = $request->get('status', 'active');

            $pakets = Paket::where('status', $status)
                          ->orderBy('nama_paket', 'asc')
                          ->get()
                          ->map(function($item) {
                              return [
                                  'id' => $item->pakets_id,
                                  'text' => $item->nama_paket . ' - ' . $item->speed . ' (' . number_format($item->price, 0, ',', '.') . ')',
                                  'nama_paket' => $item->nama_paket,
                                  'speed' => $item->speed,
                                  'price' => $item->price,
                                  'description' => $item->description,
                              ];
                          });

            return response()->json([
                'success' => true,
                'data' => $pakets
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Paket Dropdown Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pakets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 📝 STEP 2: Setup Routes di BEFAST

Lokasi: `befast/routes/api.php`

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\PaketApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public route untuk test API
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'BEFAST API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Protected routes dengan API Token
Route::middleware('api.token')->group(function () {

    // ============================================
    // SUBSCRIPTIONS API
    // ============================================
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionApiController::class, 'index']); // List all
        Route::get('/dropdown', [SubscriptionApiController::class, 'dropdown']); // Untuk dropdown
        Route::get('/{id}', [SubscriptionApiController::class, 'show']); // Detail by ID
    });

    // ============================================
    // PAKETS API
    // ============================================
    Route::prefix('pakets')->group(function () {
        Route::get('/', [PaketApiController::class, 'index']); // List all
        Route::get('/dropdown', [PaketApiController::class, 'dropdown']); // Untuk dropdown
        Route::get('/{id}', [PaketApiController::class, 'show']); // Detail by ID
    });
});
```

---

## 📝 STEP 3: Buat API Token Middleware

Lokasi: `befast/app/Http/Middleware/ApiTokenAuth.php`

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

Lokasi: `befast/app/Http/Kernel.php`

Tambahkan di `protected $routeMiddleware`:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'api.token' => \App\Http\Middleware\ApiTokenAuth::class,
];
```

---

## 📝 STEP 5: Setup Environment di BEFAST

Lokasi: `befast/.env`

```env
# API Configuration
API_TOKEN=befast_api_token_12345_secure_random_string

# CORS Configuration (jika perlu)
APP_URL=http://localhost:8000
```

---

## 📝 STEP 6: Enable CORS di BEFAST (Optional)

Lokasi: `befast/config/cors.php`

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

## 🧪 TESTING API di BEFAST

**⚠️ Ganti token sesuai dengan yang di `.env` kalian!**

```bash
# 1. Test endpoint public (tidak perlu token)
curl http://localhost:8000/api/test

# Jika berhasil, response:
# {"success":true,"message":"BEFAST API is running","version":"1.0.0"}

# 2. Test Subscriptions (perlu token)
curl -H "Authorization: Bearer befast_api_token_12345_secure_random_string" \
     http://localhost:8000/api/subscriptions/dropdown

# 3. Test Pakets (perlu token)
curl -H "Authorization: Bearer befast_api_token_12345_secure_random_string" \
     http://localhost:8000/api/pakets/dropdown
```

**Jika response 401 Unauthorized:** Token salah atau belum setup di `.env`

**Jika response 500 Error:** Cek nama tabel/kolom di controller, mungkin tidak sesuai dengan database

---

## ✅ CHECKLIST IMPLEMENTASI di BEFAST

- [ ] Buat folder `app/Http/Controllers/Api/`
- [ ] Copy 2 controller files (SubscriptionApiController, PaketApiController)
- [ ] Buat `app/Http/Middleware/ApiTokenAuth.php`
- [ ] Update `routes/api.php`
- [ ] Register middleware di `app/Http/Kernel.php`
- [ ] Setup API_TOKEN di `.env`
- [ ] (Optional) Setup CORS di `config/cors.php`
- [ ] Test semua endpoints dengan Postman/cURL

**Note:** Untuk Karyawan, lihat file `PREFAST_API_IMPLEMENTATION.md` (project terpisah)

---

## 🔗 API Endpoints yang Tersedia

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/test` | Test API connection (no auth) |
| GET | `/api/subscriptions` | Get all subscriptions (paginated) |
| GET | `/api/subscriptions/dropdown` | Get subscriptions for dropdown |
| GET | `/api/subscriptions/{id}` | Get subscription detail |
| GET | `/api/pakets` | Get all pakets (paginated) |
| GET | `/api/pakets/dropdown` | Get pakets for dropdown |
| GET | `/api/pakets/{id}` | Get paket detail |

---

## 📝 SETELAH SELESAI

Setelah semua endpoint API berjalan dan testing berhasil:

1. **Kirim ke tim FASTKEU:**
   - URL API Befast (misal: `http://localhost:8000` atau `https://befast.yourdomain.com`)
   - API Token yang sudah di-generate
   - List endpoint yang tersedia (copy tabel di atas)

2. **Contoh format yang dikirim:**
   ```
   BEFAST API Ready!

   URL: http://localhost:8000
   Token: befast_api_token_12345_secure_random_string

   Endpoints:
   - GET /api/subscriptions/dropdown (Customer/Pelanggan)
   - GET /api/pakets/dropdown (Paket Layanan)

   Note: Data Karyawan dari PREFAST (project terpisah)
   ```

3. Tim FASTKEU akan update `.env` mereka dengan info ini

---

## 🆘 Troubleshooting

### Error: "Class SubscriptionApiController not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Error: "Table subscriptions doesn't exist"
- Cek nama tabel di database kalian
- Update nama tabel di controller (di bagian `Model::query()`)

### Error: "Column subs_id not found"
- Cek nama kolom di database kalian
- Update nama kolom di controller (di bagian `->select([...])`)

### API lambat / timeout
- Tambahkan index di database untuk kolom yang sering di-search
- Pertimbangkan caching jika data jarang berubah

---

**✅ SELESAI!** Tim FASTKEU bisa mulai konsumsi API ini untuk dropdown mereka.
