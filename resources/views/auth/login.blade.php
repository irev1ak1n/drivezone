@extends('layouts.layout')

@push('styles')
    {{-- Подключаем внешний CSS для входа --}}
    @vite([
         'resources/css/auth/login.css', // стили .css
         'resources/js/Pages/Auth/Scripts/login.js' // логика js
     ])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />
@endpush

@section('content')
    <div class="top-arrow" onclick="window.history.back()">
        <i class="bi bi-arrow-left"></i>
    </div>

    <div class="center-container">
        <div class="login-wrapper">
            <h3>DRIVEZONE</h3>
            <div class="login-subtitle">Вход или регистрация</div>

            <div class="login-tabs">
                <button type="button" class="login-tab active" data-type="email"><i class="bi bi-envelope"></i> Mail</button>
                <button type="button" class="login-tab" data-type="phone"><i class="bi bi-phone"></i> Phone</button>
            </div>

            <form method="POST" action="{{ url('/login') }}" style="width: 100%;">
                @csrf

                <!-- Скрытое поле login, которое реально уходит в Laravel -->
                <input type="hidden" name="login" id="login-hidden">

                <div class="form-group email-field">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" class="form-control" placeholder="Email">
                </div>

                <div class="form-group phone-field" style="display: none;">
                    <i class="bi bi-phone input-icon"></i>
                    <input type="tel" id="phone" class="form-control" placeholder="Номер телефона">
                </div>

                <div class="form-group">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Пароль" required>
                    <i class="bi bi-eye toggle-password" onclick="togglePassword()"></i>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <button type="submit" class="btn-orange">Войти</button>
                <button type="button"
                        class="btn-orange secondary"
                        onclick="window.location.href='{{ route('register') }}'">
                    Создать аккаунт
                </button>
            </form>
        </div>
    </div>

    {{-- === Toast при ошибке входа (вынесен из .login-wrapper) === --}}
    @if (session('login_error'))
        <div id="dzToast" class="dz-toast show error">
            {{ session('login_error') }}
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

@endsection
