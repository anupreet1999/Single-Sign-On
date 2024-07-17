<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiHelper
{
    public const DEFAULT_ERROR = false;
    public const DEFAULT_CODE = 200;
    public const DEFAULT_MESSAGE = 'SUCCESS';

    /**
     * Generic function to return api response
     *
     * @param boolean $error
     * @param integer $code
     * @param string $message
     * @param mixed $data
     * @param array ...$extras
     * @return JsonResponse
     */
    public static function sendResponse($error = false, $code = 200, $message = 'SUCCESS', $data = [], ...$extras): JsonResponse
    {
        return response()->json(
            array_merge(
                [
                    'error' => $error,
                    'code' => $code,
                    'message' => $message,
                    'data' => $data,
                ],
                ...$extras
            ),
            $code
        );
    }
    
}
