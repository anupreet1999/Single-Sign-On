<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() && session('api_token')) {
            $http = new Client();
            try {
                $response = $http->get('http://127.0.0.1:8000/api/verify-token', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . session('api_token'),
                    ],
                ]);

                $result = json_decode((string) $response->getBody(), true);
                $user = User::where('email', $result['user']['email'])->first();
                Auth::login($user);
            } catch (\Exception $e) {
                session()->forget('api_token');
            }
        }

        return $next($request);
    }


}
