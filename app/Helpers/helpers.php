<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

if (!function_exists('logActivity')) {
    /**
     * Log user activity
     *
     * @param string $action (create, update, delete, generate, import)
     * @param string $description
     * @return void
     */
    function logActivity(string $action, string $description)
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
