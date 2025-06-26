<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormatValidationErrorResponse
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}