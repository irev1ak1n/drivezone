@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
@endpush

@section('content')
    <div class="container my-5">
        <div class="row g-5">
            <div class="col-md-5 text-center">
                <img src="{{ $service->image_url ?? '/storage/services/default.png' }}"
                     alt="{{ $service->name }}"
                     class="img-fluid rounded shadow-sm"
                     style="max-height: 400px; object-fit: contain;">
            </div>

            <div class="col-md-7">
                <h2 class="fw-bold mb-2">{{ $service->name }}</h2>
                <p class="text-muted small mb-1">
                    Статус:
                    @if ($service->status === 'Услуга доступна')
                        <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Доступна</span>
                    @else
                        <span class="text-danger fw-semibold"><i class="bi bi-x-circle-fill"></i> Недоступна</span>
                    @endif
                </p>

                <h4 class="text-orange fw-bold my-3">
                    {{ number_format($service->price, 2, ',', ' ') }} ₽
                </h4>

                @php
                    $hours = intdiv($service->duration_minutes, 60);
                    $minutes = $service->duration_minutes % 60;
                @endphp

                <p class="text-muted mb-3">
                    ⏱ Длительность: {{ $hours > 0 ? $hours . ' ч ' : '' }}{{ $minutes }} мин
                </p>

                <p class="mb-4 text-secondary" style="line-height:1.7;">
                    {{ $service->description }}
                </p>

                @if ($service->status === 'Услуга доступна')
                    @auth
                        <form method="POST" action="{{ url('/cart/add/'.$service->id.'/service') }}">
                            @csrf
                            <button type="submit" class="btn btn-orange fw-bold px-4">
                                <i class="bi bi-cart-plus"></i> Добавить к заказу
                            </button>
                        </form>
                    @else
                        <button type="button"
                                class="btn btn-orange fw-bold px-4 btn-login-required"
                                data-login-url="{{ route('login') }}">
                            <i class="bi bi-cart-plus"></i> Добавить к заказу
                        </button>
                    @endauth
                @else
                    <button class="btn btn-secondary px-4 fw-bold" disabled>
                        <i class="bi bi-ban"></i> Временно недоступна
                    </button>
                @endif
            </div>
        </div>
    </div>
@endsection
