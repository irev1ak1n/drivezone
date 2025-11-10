<?php

namespace App\Http\Requests;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Класс для валидации данных профиля при обновлении.
// - Проверяет правильность email, телефона, ролей и других полей.
// - Обеспечивает безопасность и предотвращает дублирование данных (например, одинаковых email).
class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'photo_url'    => ['nullable', 'string', 'max:500'],
            'gender'       => ['required', Rule::in(['male', 'female'])],
            'email'        => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique(User::class)->ignore($this->user()->id)],
            'password'     => ['nullable', 'string', 'min:8'], // меняется только если введён
            'role'         => ['required', Rule::in(['admin', 'customer', 'employee', 'supervisor'])],
        ];

    } // rules

} // ProfileUpdateRequest
