<?php

namespace App\Http\Controllers;

use App\Services\BefastApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BefastDataController extends Controller
{
    protected $befastApi;

    public function __construct(BefastApiService $befastApi)
    {
        $this->befastApi = $befastApi;
    }

    /**
     * Test API Connection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        $result = $this->befastApi->testConnection();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Connection to BEFAST API successful',
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to BEFAST API',
            'error' => $result['error'] ?? 'Unknown error'
        ], 500);
    }

    /**
     * Get Subscriptions for dropdown (AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubscriptionsDropdown(Request $request)
    {
        try {
            $params = [
                'search' => $request->get('search', ''),
                'limit' => $request->get('limit', 100),
            ];

            $result = $this->befastApi->getSubscriptionsDropdown($params);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['data'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch subscriptions'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error fetching subscriptions dropdown: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Subscription detail by ID (AJAX)
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubscription($id)
    {
        try {
            $result = $this->befastApi->getSubscription($id);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Subscription not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching subscription: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Pakets for dropdown (AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaketsDropdown(Request $request)
    {
        try {
            $params = [
                'status' => $request->get('status', 'active'),
            ];

            $result = $this->befastApi->getPaketsDropdown($params);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['data'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch pakets'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error fetching pakets dropdown: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Paket detail by ID (AJAX)
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaket($id)
    {
        try {
            $result = $this->befastApi->getPaket($id);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Paket not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching paket: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Karyawan for dropdown (AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKaryawanDropdown(Request $request)
    {
        try {
            $params = [
                'search' => $request->get('search', ''),
                'employment_status' => $request->get('employment_status', 'active'),
                'limit' => $request->get('limit', 100),
            ];

            $result = $this->befastApi->getKaryawanDropdown($params);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['data'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch karyawan'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error fetching karyawan dropdown: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Karyawan detail by ID (AJAX)
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKaryawan($id)
    {
        try {
            $result = $this->befastApi->getKaryawanById($id);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Karyawan not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching karyawan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clear BEFAST API cache
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            $this->befastApi->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'BEFAST API cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing cache: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
}
