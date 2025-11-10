<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Users\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        session()->flash('welcome', 'Добро пожаловать, ' . auth()->user()->first_name . '!');
        return view('admin.dashboard');
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|max:255',
            'role' => 'required|string|in:customer,employee,manager,admin',
        ]);

        $user->update($data);

        return back()->with('success', 'Данные пользователя успешно обновлены!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return back()->with('success', 'Нельзя удалить свой собственный аккаунт!');
        }

        $user->delete();
        return back()->with('success', 'Пользователь удалён.');
    }
}
