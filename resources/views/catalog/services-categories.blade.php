@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
@endpush

@section('content')
    <div class="container my-5">
        <h2 class="fw-bold text-uppercase text-start mb-4">
            Услуги категории: {{ $category }}
        </h2>
        <div class="catalog-divider mb-4"></div>

        <div class="row g-4">
            @forelse($services as $service)
                <div class="col-6 col-md-3">
                    <div class="card h-100 shadow-sm border-0 dz-clickable-card"
                         data-url="{{ route('services.show', $service->id) }}">
                        <img src="{{ $service->image_url ?? '/storage/services/default.png' }}"
                             alt="{{ $service->name }}"
                             class="card-img-top"
                             style="object-fit:contain; height:140px; background:#f8f8f8; border-bottom:1px solid #eee;">

                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                {{-- Название и описание --}}
                                <h6 class="fw-bold mb-1">{{ $service->name }}</h6>
                                <p class="text-muted small mb-2">
                                    {{ Str::limit($service->description, 100) }}
                                </p>

                                {{-- Статус услуги --}}
                                @if($service->status === 'Услуга доступна')
                                    <p class="text-success small mb-1">
                                        <i class="bi bi-check-circle-fill"></i> Услуга доступна
                                    </p>
                                @else
                                    <p class="text-secondary small mb-1">
                                        <i class="bi bi-x-circle-fill"></i> Временно недоступна
                                    </p>
                                @endif

                                {{-- Цена --}}
                                <p class="fw-semibold text-dark fs-6 mb-0">
                                    {{ number_format($service->price, 2, ',', ' ') }} ₽
                                </p>

                                {{-- Время выполнения --}}
                                @php
                                    $hours = floor($service->duration_minutes / 60);
                                    $minutes = $service->duration_minutes % 60;
                                @endphp
                                <p class="text-muted small">
                                    ⏱
                                    @if($hours > 0)
                                        {{ $hours }} ч {{ $minutes > 0 ? $minutes . ' мин' : '' }}
                                    @else
                                        {{ $minutes }} мин
                                    @endif
                                </p>
                            </div>

                            {{-- Кнопка добавления --}}
                            @if($service->status === 'Услуга доступна')
                                @auth
                                    <form method="POST" action="{{ url('/cart/add/'.$service->id.'/service') }}">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-orange w-100 mt-2 fw-bold">
                                            <i class="bi bi-cart-plus"></i> Добавить к заказу
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                            class="btn btn-orange w-100 fw-bold btn-login-required d-flex align-items-center justify-content-center gap-2"
                                            data-login-url="{{ route('login') }}">
                                        <i class="bi bi-cart-plus"></i> Добавить к заказу
                                    </button>
                                @endauth
                            @else
                                <button class="btn btn-secondary w-100 mt-2 fw-bold" disabled>
                                    <i class="bi bi-ban"></i> Недоступна
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-tools" style="font-size:3rem;color:#ff6a00"></i>
                    <h5 class="fw-bold mt-3">Нет услуг в этой категории</h5>
                    <p class="text-muted">Попробуйте выбрать другую категорию</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- === JS логика === --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ======== КЛИК ПО КАРТОЧКЕ УСЛУГИ ========
            document.querySelectorAll('.dz-clickable-card').forEach(card => {
                card.addEventListener('click', e => {
                    if (e.target.closest('button') || e.target.closest('form')) return;
                    const url = card.dataset.url;
                    if (url) window.location.href = url;
                });
            });

            // ======== TOAST при клике без авторизации ========
            const toast = document.createElement('div');
            toast.id = 'dz-toast';
            toast.className = 'dz-toast';
            toast.textContent = 'Пожалуйста, войдите, чтобы оформить заказ';
            document.body.appendChild(toast);

            document.querySelectorAll('.btn-login-required').forEach(btn => {
                btn.addEventListener('click', () => {
                    toast.classList.add('show');
                    setTimeout(() => {
                        window.location.href = btn.dataset.loginUrl;
                    }, 1000);
                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 3000);
                });
            });
        });
    </script>

    {{-- === Дополнительные стили === --}}
    <style>
        /* Кликабельная карточка */
        .dz-clickable-card {
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .dz-clickable-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Toast уведомление */
        .dz-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--dz-dark);
            color: #fff;
            padding: 14px 22px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.4s ease;
            z-index: 2000;
            pointer-events: none;
            border-left: 5px solid var(--dz-orange);
        }
        .dz-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-secondary {
            background: #ccc;
            border: none;
            cursor: not-allowed;
        }
    </style>
@endsection
