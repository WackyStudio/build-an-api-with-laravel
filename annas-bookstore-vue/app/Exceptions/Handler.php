<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if($exception instanceof QueryException || $exception instanceof ModelNotFoundException){
            $exception = new NotFoundHttpException('Resource not found');
        }


        return parent::render($request, $exception);
    }

    protected function prepareJsonResponse($request, Exception $e)
    {
        return response()->json([
            'errors' => [
                [
                    'title' => Str::title(Str::snake(class_basename($e), ' ')),
                    'details' => $e->getMessage(),
                ]
            ]
        ], $this->isHttpException($e) ? $e->getStatusCode() : 500);
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

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($request->expectsJson()){
            return response()->json([
                'errors' => [
                    [
                        'title' => 'Unauthenticated',
                        'details' => 'You are not authenticated',
                    ]
                ]
            ], 403);
        }
        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
