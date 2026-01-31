<?php
// app/Http/Controllers/BakongController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BakongController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'md5' => 'required|string',
        ]);

        $md5 = $request->query('md5');
        $token = env('BAKONG_TOKEN');

        if (!$token) {
            return response()->json(['paid' => false, 'error' => 'Missing BAKONG_TOKEN'], 500);
        }

        try {
            $http = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ]);

            // local only
            if (app()->environment('local')) {
                $http = $http->withOptions(['verify' => false]);
            }

            $response = $http->post(
                'https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5',
                ['md5' => $md5]
            );

            if (!$response->successful()) {
                Log::error('BAKONG check failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return response()->json([
                    'paid'  => false,
                    'error' => 'Bakong API error',
                ], 500);
            }

            $result = $response->json();
            $responseCode = $result['responseCode'] ?? null;
            $errorCode    = $result['errorCode'] ?? null;

            // ✅ paid
            if ($responseCode === 0 && !empty($result['data'])) {
                return response()->json([
                    'paid' => true,
                    'data' => $result['data'],
                    'md5'  => $md5,
                ]);
            }

            // ❌ failed
            if ($responseCode === 1 && $errorCode === 3) {
                return response()->json([
                    'paid'   => false,
                    'failed' => true,
                    'data'   => $result,
                ]);
            }

            // ⏳ pending
            return response()->json([
                'paid'    => false,
                'pending' => true,
                'md5'     => $md5,
            ]);
        } catch (\Throwable $e) {
            Log::error('BAKONG check exception', ['message' => $e->getMessage()]);
            return response()->json(['paid' => false, 'error' => 'Verification failed'], 500);
        }
    }
}
