<?php
// app/Http/Controllers/Controller.php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsActivity;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, DispatchesJobs, LogsActivity;

    /**
     * Execute a callback within a database transaction with automatic logging
     *
     * @param callable $callback
     * @param string $operation
     * @param array $context
     * @return mixed
     * @throws \Throwable
     */
    protected function executeWithTransaction(callable $callback, string $operation, array $context = [])
    {
        DB::beginTransaction();
        
        try {
            $result = $callback();
            
            DB::commit();
            
            $this->logActivity(
                $operation,
                $context['table'] ?? null,
                $context['record_id'] ?? null,
                $context['old_data'] ?? null,
                $context['new_data'] ?? ($result->toArray() ?? null),
                $context['description'] ?? "Successfully executed: {$operation}"
            );
            
            return $result;
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            $this->logError($e, $operation);
            
            throw $e;
        }
    }

    /**
     * Return JSON response for API requests
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return error JSON response
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = 'Error occurred', int $statusCode = 500, array $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}