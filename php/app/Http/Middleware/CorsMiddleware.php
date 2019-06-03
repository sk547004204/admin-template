<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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
        if($request->isMethod('OPTIONS')) {
			$response = response('', 200);
		} else {
			$response = $next($request);
        }
        
        if (!in_array($request->path(), ['api/login/wechat']))
        {
            $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
            $response->header('Access-Control-Allow-Origin', '*');
        }

        return $response;
    }
}
