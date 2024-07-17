<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class SSOAuthentication extends Controller
{
    public function verifyProviderToken(Request $request)
    {

        $token = $request->access_token;

        if (!$token) {
            Log::error('Token is missing in the request.');
            return response()->json([
                'status' => false,
                'message' => 'Token is required.'
            ], 400);
        }

        try {


            // Define the headers and body for the HTTP request
            $headers = [
                'Content-Type' => 'application/json'
            ];
            $body = ([
                'access_token' => $token
            ]);
            // Make the HTTP POST request to the external endpoint
            $res = Http::withHeaders($headers)->post('http://127.0.0.1:8000/api/sso/verify-token', $body);

            $response = json_decode($res->getBody(), true);

            // Check if the external request was successful
            if ($response['status'] == true) {
                return response()->json([
                    'status' => true,
                    'message' => 'Token verified successfully.',
                    'data' => $response
                ], 200);
            } else {
                Log::error('Token verification failed: ' . json_encode($response));
                return response()->json([
                    'status' => false,
                    'message' => $response['message'] ?? 'Failed to verify token.',
                    'data' => $response
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('RequestException in verifying token: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error in making HTTP request to verification server.',
                'data' => ''
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error in verifying token: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error in verifying token.',
                'data' => ''
            ], 500);
        }
    }
}
