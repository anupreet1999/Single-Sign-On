<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\PersonalAccessTokenResult;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class SSOAuthentication extends Controller
{
    public function sendToken(Request $request)
    {

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => []
            ], 404);
        }
        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create token',
                'data' => []
            ], 500);
        }
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $body = ([
            'access_token' => $token
        ]);
        $res = Http::withHeaders($headers)->post('http://127.0.0.1:8080/api/sso/authentication', $body);
        $response = json_decode($res->getBody(), true);


        if ($response == null) {
            Log::error('Failed to decode JSON response: ' . json_last_error_msg());
            return response()->json([
                'status' => false,
                'message' => 'Invalid JSON response from SSO server',
                'data' => []
            ], 500);
        }



        if ($response['status'] == false) {
            return response()->json([
                'status' => false,
                'message' => $response['message'],
                'data' => []
            ], 400);
        }

        if ($response['status'] == true) {
            return response()->json([
                'status' => true,
                'message' => $response['message'],
                'data' => [
                    'access_token' => $token,
                    'user' => $user
                ]
            ], 200);
        }
    }

    public function verifyToken(Request $request)
    {
        $token = $request->access_token;
        
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Token is required.',
                'data' => []
            ], 400);
        }
        try {
            // Decode and validate the token
            $payload = Auth::guard('api')->setToken($token)->payload();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token has expired.',
                'data' => []

            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token.',
                'data' => []

            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token error: ' . $e->getMessage(),
                'data' => []

            ], 400);
        }

        // Retrieve the authenticated user
        $authUser = Auth::guard('api')->user();

        return response()->json([
            'status' => true,
            'message' => 'User details fetched successfully.',
            'status_code' => 200,
            'data' => [
                'user_id' => $authUser->id,
                'name' => $authUser->name,
                'email' => $authUser->email,
                'token' => $token,
                'token_expiry' => $payload['exp']
            ]
        ]);
    }
}
