<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;

class ResponseInRequest
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
        $resp = new ResponseHelper();
        $request->request->add(['response_helper' => $resp]);
        return $next($request);
    }
}
