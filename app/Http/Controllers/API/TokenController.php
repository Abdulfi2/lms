<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PersonalAccessToken;

class TokenController extends Controller
{
    /**
     * List all tokens for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at,
                'expires_at' => $token->expires_at,
                'created_at' => $token->created_at,
                'is_current' => $token->id === request()->user()->currentAccessToken()?->id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tokens
        ]);
    }

    /**
     * Create a new API token
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'nullable|array',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        $expiresAt = $request->expires_in_days ? now()->addDays($request->expires_in_days) : null;
        $abilities = $request->abilities ?? ['*'];

        $token = $request->user()->createToken($request->name, $abilities, $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil dibuat',
            'token' => $token->plainTextToken,
            'token_id' => $token->accessToken->id,
            'expires_at' => $expiresAt,
        ], 201);
    }

    /**
     * Revoke a specific token
     */
    public function destroy(Request $request, $tokenId): JsonResponse
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil dihapus'
        ]);
    }

    /**
     * Revoke all tokens for the user
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $count = $request->user()->tokens()->count();
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} token berhasil dihapus"
        ]);
    }
}