<?php

namespace App\Exceptions;

use Exception;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Auth\AuthenticationException;

class Handler extends IvxHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,

        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Queue\MaxAttemptsExceededException::class, // Not report when job max retries reached
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \Symfony\Component\Console\Exception\CommandNotFoundException::class, //Not report when call wrong artisan command
        \Symfony\Component\Console\Exception\RuntimeException::class, // not report when call artisan command with wrong arguments
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ApiValidationFailedException::class, // transform exception to json, no need to record in log
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     */
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception)) {
            Bugsnag::notifyException($exception);
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
