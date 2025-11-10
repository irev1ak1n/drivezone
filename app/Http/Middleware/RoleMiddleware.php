<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Использование: ->middleware('role:admin') или 'role:admin,employee'
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // 1) если не авторизован
        if (!$user) {
            abort(401, 'Пользователь не авторизован');
        }

        // 2) если роль не подходит
        if (!empty($roles) && !in_array($user->role, $roles, true)) {
            abort(403, 'Доступ запрещён: недостаточно прав');
        }

        return $next($request);
    } // handle

} // RoleMiddleware
