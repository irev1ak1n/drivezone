@extends('layouts.layout')

@push('styles')
    @vite([
        'resources/css/auth/register.css', // стили .css
        'resources/js/Pages/Auth/Scripts/register.js' // логика js
    ])
@endpush

@section('content')
    <div class="top-arrow" onclick="window.location.href='{{ route('login') }}'">
        <i class="bi bi-arrow-left"></i>
    </div>

    <div class="center-container">
        <div class="register-wrapper">
            <!-- === Перемещено вверх === -->
            <h3>DriveZone</h3>
            <p class="login-subtitle">Регистрация</p>

            <div class="progress-bar">
                <div id="progressFill" class="progress-bar-fill"></div>
            </div>

            <form id="registerForm" method="POST" action="{{ url('/register') }}">
                @csrf

                <!-- === Шаг 1: Роль === -->
                <div class="register-step active" data-step="1">
                    <div class="step-counter" id="stepCounter">Шаг 1 из 4</div>
                    <p class="text-center mb-3">Выберите тип аккаунта:</p>

                    <button type="button" class="role-btn" data-role="customer">Я покупатель</button>
                    <button type="button" class="role-btn" data-role="employee">Я работник</button>
                    <button type="button" class="role-btn" data-role="admin">Я администратор</button>

                    <div class="step-nav">
                        <button type="button" class="btn-orange" id="nextBtn1" disabled>Далее →</button>
                    </div>
                </div>

                <!-- === Шаг 2 === -->
                <div class="register-step" data-step="2">
                    <div class="step-counter" id="stepCounter2">Шаг 2 из 4</div>

                    <div class="form-group">
                        <label>Имя <span class="required">*</span></label>
                        <input type="text" name="first_name" placeholder="Введите имя" required>
                    </div>

                    <div class="form-group">
                        <label>Фамилия <span class="required">*</span></label>
                        <input type="text" name="last_name" placeholder="Введите фамилию" required>
                    </div>

                    <div class="form-group">
                        <label>Пол</label>
                        <select name="gender">
                            <option value="">Не выбран</option>
                            <option value="male">Мужской</option>
                            <option value="female">Женский</option>
                            <option value="prefer-not-to-answer">Предпочту не указывать</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Дата рождения</label>
                        <input type="date" name="birth_date">
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-orange" id="prevBtn2">← Назад</button>
                        <button type="button" class="btn-orange" id="nextBtn2">Далее →</button>
                    </div>
                </div>

                <!-- === Шаг 3 === -->
                <div class="register-step" data-step="3">
                    <div class="step-counter" id="stepCounter3">Шаг 3 из 4</div>

                    <div class="form-group">
                        <label>Email
                            <span class="required" title="Это обязательное поле">*</span>
                        </label>
                        <input type="email" name="email" placeholder="Введите email" required>
                    </div>

                    <div class="form-group">
                        <label>Телефон (опционально)</label>
                        <input type="tel" name="phone_number" placeholder="Введите номер телефона">
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-orange" id="prevBtn3">← Назад</button>
                        <button type="button" class="btn-orange" id="nextBtn3">Далее →</button>
                    </div>
                </div>

                <!-- === Шаг 4 === -->
                <div class="register-step" data-step="4">
                    <div class="step-counter" id="stepCounter4">Шаг 4 из 4</div>

                    <div class="form-group">
                        <label>Пароль <span class="required">*</span></label>
                        <input type="password" name="password" placeholder="Введите пароль" required>
                    </div>

                    <div class="form-group">
                        <label>Подтвердите пароль <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Повторите пароль" required>
                    </div>

                    <div id="adminCodeField" style="display:none;">
                        <label>Секретный код администратора <span class="required">*</span></label>
                        <input type="text" name="admin_code" placeholder="Введите код администратора">
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-orange" id="prevBtn4">← Назад</button>
                        <button type="submit" class="btn-orange" id="submitBtn">Создать</button>
                    </div>
                </div>

                <input type="hidden" name="role" id="roleInput">
            </form>
        </div>
    </div>

@endsection
