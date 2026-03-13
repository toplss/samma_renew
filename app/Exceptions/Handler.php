<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // ============================
        // 개발환경은 기본 에러 유지
        // ============================
        if (!app()->isProduction()) {
            return parent::render($request, $exception);
        }

        $url = url()->previous() ?? '/';

        return redirect($url)
            ->with('error', $exception->getMessage());

        // ============================
        // Validation Exception
        // ============================
        if ($exception instanceof ValidationException) {

            return redirect()->back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        // ============================
        // abort(), 404, 403 예외는 view 유지
        // ============================
        if ($exception instanceof HttpExceptionInterface) {

            return parent::render($request, $exception);
        }

        // ============================
        // 일반 WEB Exception → error-alert
        // ============================
        return redirect($url)
            ->with('error', $exception->getMessage());
    }
}
