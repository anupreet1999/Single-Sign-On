<?php

namespace App\Exceptions;


use Throwable;
use Illuminate\Http\Response;
use App\Helpers\ApiHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (BadRequestHttpException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_BAD_REQUEST, $e->getMessage() ?: Response::$statusTexts[Response::HTTP_BAD_REQUEST]);
        });
    
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_NOT_FOUND, $e->getMessage());
        });
    
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_TOO_MANY_REQUESTS, $e->getMessage());
        });
    
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_METHOD_NOT_ALLOWED, 'Method Not Allowed.');
        });
    
        $this->renderable(function (ValidationException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        });
    
        $this->renderable(function (AuthenticationException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_UNAUTHORIZED, $e->getMessage());
        });
    
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return ApiHelper::sendResponse(true, Response::HTTP_FORBIDDEN, $e->getMessage());
        });
    
        $this->renderable(function (Throwable $e, $request) {
            $message = '';
            $trace = [];
    
            if (config('app.debug')) {
                $message = $e->getMessage();
                $trace = ['trace' => $e->getTrace()];
            } else if ($e->getCode() === Response::HTTP_INTERNAL_SERVER_ERROR) {
                $message = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
            } else {
                $message = $e->getMessage();
            }
    
            return ApiHelper::sendResponse(true, is_numeric($e->getCode()) && (100 <= $e->getCode()) && ($e->getCode() < 600) ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR, $message, [], $trace);
        });
    }
}
