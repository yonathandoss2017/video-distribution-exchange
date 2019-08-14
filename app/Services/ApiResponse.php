<?php

namespace App\Services;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiResponse
{
    public function render($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return \response()->json([
                'errors' => [
                    'message' => 'Page not found',
                    'status_code' => 404,
                ],
            ], 404);
        }
    }
}
