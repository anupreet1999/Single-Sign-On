<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function ssoLogin(Request $request)
    {

        try {
            $http = new \GuzzleHttp\Client();
            $response = $http->post('http://127.0.0.1:8000/api/login', [
                'form_params' => [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);

            return response()->json($result);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error('Provider client error', [
                'status' => $e->getResponse()->getStatusCode(),
                'body' => json_decode((string) $e->getResponse()->getBody(), true)
            ]);
            return response()->json(['error' => 'Provider client error'], 401);
        } catch (\Exception $e) {
            \Log::error('SSO login error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'SSO login failed', 'message' => $e->getMessage()], 500);
        }
    }


    public function getUser(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        $http = new Client;

        try {
            $response = $http->get('http://127.0.0.1:8000/api/sso-validate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $user = json_decode((string) $response->getBody(), true);


            // Log the user in
            return response()->json(['message' => 'User authenticated successfully', $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed', 'message' => $e->getMessage()], 401);
        }
    }
}
