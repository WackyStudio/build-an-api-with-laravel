<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class EnsureCorrectAPIHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('accept') !== 'application/vnd.api+json'){
            return $this->addCorrectContentType(new Response('', 406));
        }

        if($request->isMethod('POST') || $request->isMethod('PATCH')){
            if($request->header('content-type') !== 'application/vnd.api+json'){
                return $this->addCorrectContentType(new Response('', 415));
            }
        }

        return $this->addCorrectContentType($next($request));
    }

    private function addCorrectContentType(BaseResponse $response)
    {
        $response->headers->set('content-type', 'application/vnd.api+json');
        return $response;
    }
}
