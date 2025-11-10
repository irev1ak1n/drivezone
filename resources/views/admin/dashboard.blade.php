@extends('layouts.layout')

@section('content')
    <div class="container my-5">
        <h2 class="fw-bold mb-4 text-uppercase">Админ-панель</h2>
        <p class="text-muted">Добро пожаловать, {{ auth()->user()->name }}!</p>

        <div class="card shadow-sm p-4">
            <h5>Быстрые действия:</h5>
            <a href="{{ route('admin.users') }}" class="btn btn-orange mt-3">
                <i class="bi bi-people-fill"></i> Управление пользователями
            </a>
        </div>
    </div>
@endsection
