<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;

class TokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Токен отсутствует'], 401);
        }

        $plain = substr($header, 7);
        $hashed = hash('sha256', $plain);
        $user = User::where('remember_token', $hashed)->first();

        if (! $user) {
            return response()->json(['message' => 'Токен недействителен'], 401);
        }

        Auth::setUser($user);
        return $next($request);
    }
}
