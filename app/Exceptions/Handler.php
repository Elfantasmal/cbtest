<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,

        \App\Exceptions\ApiException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if ($request->header('X-ISAPI') == 1 && get_class($exception) == 'App\\Exceptions\\ApiException'   ) {
            return $this->result($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * 返回结果
     * @param $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function result($request, \Exception $exception)
    {

        $data = [
            'status' => false,
            'error_msg' => $exception->getMessage(),
            'error_code' =>  $exception->getErrorId(),
            'data' => [],
            'list' => [],
        ];

        config('app.debug')== 'true' ? $data['debug'] = [
            'type' => get_class($exception),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ] : true;

        return response()->json($data, $exception->getCode());
    }

}
