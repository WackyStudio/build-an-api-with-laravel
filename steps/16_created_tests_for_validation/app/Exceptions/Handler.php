<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

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
        return parent::render($request, $exception);
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $errors = ( new Collection($exception->validator->errors()) )
            ->map(function ($error, $key) {
                return [
                    'title'   => 'Validation Error',
                    'details' => $error[0],
                    'source'  => [
                        'pointer' => '/' . str_replace('.', '/', $key),
                    ]
                ];
            })
            ->values();

        return response()->json([
            'errors' => $errors
        ], $exception->status);
    }
}
