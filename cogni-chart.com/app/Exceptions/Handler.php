<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Log;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (
            $exception instanceof ValidationException ||
            $exception instanceof HttpResponseException ||
            $exception instanceof MaintenanceModeException
        ) {
            return parent::render($request, $exception);
        }
        Log::error($exception);
        if ($this->isHttpException($exception)) {
            return $this->httpExceptionResponse($exception);
        }
        return $this->serverErrorResponse($exception);
    }

    private function httpExceptionResponse(HttpException $exception)
    {
        $status = intVal($exception->getStatusCode());
        if ($status >= 500) {
            return $this->serverErrorResponse($exception);
        }
        $message = __("The request wasn't accepted.\nPlease check your request url, request parameters, request headers.");
        $eMessage = $exception->getMessage();
        if (!empty($eMessage)) {
            $message .= "\n\n{$eMessage}";
        }
        return response($message, $exception->getStatusCode());
    }

    private function serverErrorResponse(Exception $exception)
    {
        $message = __("Some kind of error has occurred on server.\nPlease wait for a while and retry again.");
        $eMessage = $exception->getMessage();
        if (!empty($eMessage)) {
            $message .= "\n\n{$eMessage}";
        }
        return response($message, 500);
    }

}
