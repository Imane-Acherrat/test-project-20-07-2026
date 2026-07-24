<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestEndpointController extends Controller
{
    public function receive(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (empty($payload)) {
            return response()->json(['error' => 'Invalid or empty JSON payload'], 400);
        }

        DB::table('test_webhook_logs')->insert([
            'payload' => json_encode($payload),
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Payload stored']);
    }

    public function logs(): JsonResponse
    {
        $logs = DB::table('test_webhook_logs')->latest()->paginate(15);
        return response()->json($logs);
    }
}
