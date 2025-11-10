<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{



    /**
     * Авторизация пользователя (по email или телефону)
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = $validated['login'];

        // Определяем поле для входа
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $field = 'phone_number';

            // Нормализуем телефон
            $loginInput = preg_replace('/[^\d+]/', '', $loginInput);
            if (preg_match('/^8\d{10}$/', $loginInput)) {
                $loginInput = '+7' . substr($loginInput, 1);
            }
            if ($loginInput && $loginInput[0] !== '+') {
                $loginInput = '+' . $loginInput;
            }
        }

        $credentials = [
            $field     => $loginInput,
            'password' => $validated['password'],
        ];

//        if (!Auth::attempt($credentials)) {
//            return $request->expectsJson()
//                ? response()->json(['message' => 'Неверный логин или пароль'], 401)
//                : back()->withErrors(['login' => 'Неверный логин или пароль']);
//        }

        if (!Auth::attempt($credentials)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Неверный логин или пароль'], 401)
                : back()->with('login_error', 'Неверный логин или пароль');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Если JSON-запрос (API/Postman)
        if ($request->expectsJson()) {
            $token = $user->generateApiToken();
            return response()->json([
                'message' => 'Успешный вход',
                'token'   => $token,
                'user'    => $user,
            ]);
        }

        // После успешного входа, веб
//        return redirect()->route('products.index')
//            ->with('login_success', true); // flash-сессия

        // После успешного входа, веб
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('login_success', true);
        }

        return redirect()->route('products.index')->with('login_success', true);

    } // login

    /**
     * Получить данные текущего пользователя
     */
    public function me(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);

        // Проверяем remember_token
        $user = User::where('remember_token', hash('sha256', $token))->first();

        if (!$user) {
            return response()->json(['message' => 'Токен недействителен'], 401);
        }

        return response()->json(['user' => $user]);
    } // me

    /**
     * Выход из системы
     */
    public function logout(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);

        $user = User::where('remember_token', hash('sha256', $token))->first();
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Для Postman / API
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Вы вышли из системы']);
        }

        // Для браузера
        return back()->with('logout_message', 'Вы успешно вышли из аккаунта');
    }

    // МЕТОДЫ, СВЯЗАННЫЕ С РЕГИСТРАЦИЕЙ НОВОГО ПОЛЬЗОВАТЕЛЯ:
    // (ОПРЕДЕЛЕНИЕ ФУНКЦИОНАЛА КНОПКИ ДОБАВИТЬ НА ГЛАВНОЙ СТРАНИЦЕ)

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Проверка кода администратора
        if ($request->role === 'admin' && $request->admin_code !== env('ADMIN_SECRET_CODE')) {
            return back()->withErrors(['admin_code' => 'Неверный код администратора']);
        }

        $data = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required_without:phone_number|nullable|email|unique:users,email',
            'phone_number'      => 'required_without:email|nullable|string|max:20|unique:users,phone_number',
            'password'          => 'required|string|min:6|confirmed',
            'gender'            => 'nullable|in:male,female',
            'birth_date'        => 'nullable|date|before:today',
        ]);

        $user = User::create([
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'email'        => $data['email'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'password'     => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role'         => $request->role ?? 'customer',
            'avatar_style' => 'stethem', // default
            'gender'       => $data['gender'] ?? 'male',
            'birth_date'   => $data['birth_date'] ?? null,
            'photo_url'    => null, // можно добавить дефолт, если хочешь
        ]);

        auth()->login($user);

//        return redirect()->route('dashboard')->with('login_success', true);

        // Перенаправление в зависимости от роли
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('login_success', true);
        }

        return redirect()->route('products.index')->with('login_success', true);
    }


}
