<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuthenticateToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (str_starts_with($header, 'Bearer ')) {
            $hashed = hash('sha256', substr($header, 7));

            $token = PersonalAccessToken::with('user')
                ->where('token', $hashed)
                ->first();

            if ($token && ! $token->isExpired()) {
                $token->forceFill(['last_used_at' => now()])->save();
                $request->setUserResolver(fn () => $token->user);
            }
        }

        return $next($request);
    }
}
