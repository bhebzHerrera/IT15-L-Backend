<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHttpsForApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $enforceHttps = app()->environment('production') || (bool) env('REQUIRE_HTTPS_FOR_API', false);

        if ($enforceHttps && ! $request->secure()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'HTTPS is required for API requests.',
                'errors' => null,
            ], 426);
        }

        return $next($request);
    }
}
