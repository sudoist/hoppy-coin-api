<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyHoppyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = config('app.hoppy_api_key');

        $apiKeyIsValid = (
            !empty($apiKey)
            && $request->header('x-api-key') == $apiKey
        );

        if (!$apiKeyIsValid) {
            return response()->json([
                'status' => 'Error',
                'code' => '403',
                'message' => 'Access denied',
            ]);
        }

        return $next($request);
    }
}
