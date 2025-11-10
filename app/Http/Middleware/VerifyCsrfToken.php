<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Исключаем маршруты из CSRF-проверки
     */
    protected $except = [
        'login',
        'logout',
        'me',
        'profile/avatar*', // <- вместо /profile/avatar
    ];

}
