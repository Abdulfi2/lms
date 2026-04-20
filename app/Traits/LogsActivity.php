<?php
// app/Traits/LogsActivity.php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log activity ke database
     *
     * @param string $action
     * @param string $tableName
     * @param int|null $recordId
     * @param array|null $oldData
     * @param array|null $newData
     * @param string|null $description
     * @return void
     */
    protected function logActivity(
        string $action,
        string $tableName = null,
        int $recordId = null,
        array $oldData = null,
        array $newData = null,
        string $description = null
    ): void {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_data' => $oldData,
                'new_data' => $newData,
                'description' => $description ?? $action,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Jangan biarkan logging gagal mengganggu proses utama
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Log error ketika terjadi exception
     *
     * @param \Exception $exception
     * @param string $context
     * @return void
     */
    protected function logError(\Exception $exception, string $context = ''): void
    {
        \Log::error($context . ' - ' . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => Auth::id(),
            'ip' => Request::ip(),
        ]);

        $this->logActivity(
            'error',
            null,
            null,
            null,
            ['error' => $exception->getMessage(), 'context' => $context],
            'Exception occurred: ' . $exception->getMessage()
        );
    }
}