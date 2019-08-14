<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class IvxHandler extends Handler
{
    const STATUS_VALIDATION_FAIL = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_METHOD_NOT_ALLOWED = 405;
    const STATUS_NOT_FOUND = 404;

    public function render($request, Exception $exception)
    {
        //force api response to json
        if ($exception instanceof ApiValidationFailedException) {
            return $exception->response();
        }

        if ($request->wantsJson() || $request->ajax()) {
            if ($exception instanceof ValidationException) {
                return (new ApiValidationFailedException($exception->validator))->response();
            }
        }

        if ($this->isApiCall($request)) {
            if ($exception instanceof ValidationException) {
                return (new ApiValidationFailedException($exception->validator))->response();
            }

            if ($exception instanceof HttpException) {
                if (self::STATUS_FORBIDDEN == $exception->getStatusCode()) {
                    return $this->responseForbidden($exception);
                } elseif (self::STATUS_UNAUTHORIZED == $exception->getStatusCode()) {
                    return $this->responseUnauthorized($exception);
                } elseif (self::STATUS_VALIDATION_FAIL == $exception->getStatusCode()) {
                    return $this->respondValidationFail($exception);
                }
            }
            if ($exception instanceof NotFoundHttpException) {
                return $this->responseNotFound($exception);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->responseMethodNotAllowed();
            }

            if ($exception instanceof ModelNotFoundException) {
                return $this->responseModelNotFound();
            }

            if ($exception instanceof AuthorizationException) {
                return $this->respondUnauthorized();
            }
        }

        return parent::render($request, $exception);
    }

    protected function isApiCall(Request $request)
    {
        return false !== strpos($request->getUri(), '/api/v');
    }

    /**
     * @param Exception $exception
     *
     * @return mixed
     */
    protected function responseForbidden(Exception $exception)
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Forbidden',
        ], $exception->getStatusCode());
    }

    /**
     * @param Exception $exception
     *
     * @return mixed
     */
    protected function responseUnauthorized(Exception $exception)
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Unauthorized',
        ], $exception->getStatusCode());
    }

    private function responseNotFound(Exception $exception)
    {
        return Response::json([
            'status' => 'error',
            'message' => !empty($exception->getMessage()) ? $exception->getMessage() : 'Page not found',
        ], $exception->getStatusCode());
    }

    private function responseMethodNotAllowed()
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Method not allowed',
        ], self::STATUS_METHOD_NOT_ALLOWED);
    }

    private function responseModelNotFound()
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Model Not Found',
        ], self::STATUS_NOT_FOUND);
    }

    private function respondUnauthorized()
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Unauthorized',
        ], self::STATUS_UNAUTHORIZED);
    }

    private function respondValidationFail($exception)
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Validation failed',
        ], self::STATUS_VALIDATION_FAIL);
    }
}
