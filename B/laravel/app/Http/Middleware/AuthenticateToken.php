<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'message' => 'Authentication is required',
            ], 401);
        }

        $plainText = substr($header, 7);
        $hashed = hash('sha256', $plainText);

        $token = PersonalAccessToken::with('user')
            ->where('token', $hashed)
            ->first();

        if (! $token || $token->isExpired()) {
            return response()->json([
                'message' => 'Authentication is required',
            ], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        $request->setUserResolver(fn () => $token->user);
        $request->attributes->set('current_token', $token);

        return $next($request);
    }
}
