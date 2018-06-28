<?php

namespace Inpin\LaraLog;

use Closure;
use Illuminate\Support\Facades\Route;

class CaptureApiActionMiddleware
{
    /**
     * Create a log from the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        ApiActionLog::query()->create([
            'user_id'    => auth('api')->id(),
            'route_name' => Route::currentRouteName(),
            'method'     => $request->method(),
            'uri'        => $request->getUri(),
            'body'       => json_encode($request->all()),
        ]);

        return $next($request);
    }
}
