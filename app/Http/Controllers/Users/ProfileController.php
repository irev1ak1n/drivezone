<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Storage;


// Контроллер для управления собственным профилем пользователя.
// - Позволяет просматривать данные, обновлять информацию
// (имя, почта, телефон, пароль и т.д.) и удалять аккаунт.
// - Также Используется самим пользователем после входа, в отличие от UserController,
// который нужен администратору для управления всеми пользователями.
class ProfileController extends Controller
{
    /**
     * Показать профиль текущего пользователя.
     */
    public function edit(Request $request)
    {
        return response()->json($request->user()); // JSON вместо Inertia
    } // edit

    /**
     * Обновить информацию профиля.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

        // Если есть новый пароль → хэшируем
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // если пусто — не трогаем
        }

        $user->fill($data);
        $user->save();

        return response()->json([
            'message' => 'Профиль обновлён',
            'user' => $user
        ]);
    } // update

    /**
     * Удалить учётную запись.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Аккаунт удалён']);
    } // destroy

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);


        $path = $request->file('avatar')->store('avatars', 'public');

        $user = auth()->user();
        $user->photo_url = '/storage/' . $path;
        $user->avatar_style = 'custom';
        $user->save();

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path), // <= всегда абсолютный URL
            'message' => 'Аватар обновлён'
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = auth()->user();

        // если был кастомный аватар — удалим файл
        if ($user->photo_url) {
            $publicPrefix = '/storage/';
            $pos = strpos($user->photo_url, $publicPrefix);
            if ($pos !== false) {
                $relative = substr($user->photo_url, $pos + strlen($publicPrefix)); // avatars/xxx.jpg
                if (str_starts_with($relative, 'avatars/')) {
                    Storage::disk('public')->delete($relative);
                }
            }
        }

        // ⚡ вот здесь важно — сбросить поля
        $user->photo_url = null;
        $user->avatar_style = 'default';
        $user->save();   // <= без этого изменения не попадут в БД

        return response()->json([
            'success' => true,
            'url' => asset('storage/stethem.jpg'),
        ]);
    }


} // ProfileController
