<?php

namespace App\Http\Middleware;

use App\Helpers\InstanceHelper;
use Closure;

class Instance
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
        // get and load the instance in the call
        InstanceHelper::getInstance();
        return $next($request);
    }
}
