<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\RoleMiddleware;

class UserController extends Controller
{
    /**
     * Список всех пользователей (для админа).
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    } // index

    /**
     * Форма для создания нового пользователя (только админ).
     */
    public function create()
    {
        return view('users.create');
    } // create

    /**
     * Сохранение нового пользователя в БД.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'photo_url'    => 'nullable|string|max:255',
            'gender'       => 'nullable|in:male,female',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password'     => 'required|min:6',
            'role'         => 'required|in:admin,customer,employee,supervisor',
        ]);

        User::create([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'photo_url'    => $request->photo_url,
            'gender'       => $request->gender,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Пользователь создан');
    } // store

    /**
     * Просмотр конкретного пользователя.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    } // show

    /**
     * Форма для редактирования пользователя.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    } // edit

    /**
     * Обновление данных пользователя.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'photo_url'    => 'nullable|string|max:255',
            'gender'       => 'nullable|in:male,female',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'role'         => 'required|in:admin,customer,employee,supervisor',
        ]);

        $user->update([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'photo_url'    => $request->photo_url,
            'gender'       => $request->gender,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'role'         => $request->role,
        ]);

        // пароль меняем только если был передан
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Данные пользователя обновлены');
    } // update

    /**
     * Удаление пользователя.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Пользователь удалён');
    } // destroy

    /**
     * Профиль текущего авторизованного пользователя.
     */
    public function profile()
    {
        return view('users.profile', ['user' => auth()->user()]);
    } // profile

} // UserController
