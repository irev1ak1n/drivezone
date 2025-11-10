@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
@endpush

@section('content')
    <div id="cart-root" class="container my-5">
        <h2 class="fw-bold mb-4 text-uppercase">Моя корзина</h2>

        @if ($cartItems->isEmpty())
            <!-- Пустая корзина -->
            <div class="empty-cart">
                <i class="bi bi-cart-x"></i>
                <h4 class="fw-bold mt-3">Ваша корзина пуста</h4>
                <p>Поиск товаров доступен выше</p>
                <a href="{{ route('products.index') }}" class="btn btn-orange mt-3 px-4">Вернуться в каталог</a>
            </div>
        @else
            <div class="row g-4">
                <!-- Левая колонка — элементы корзины -->
                <div class="col-lg-8">
                    @foreach ($cartItems as $item)
                        @php
                            $subject = $item->subject;
                            $isProduct = $item->subject_type === \App\Models\Catalog\Product::class;
                            $isService = $item->subject_type === \App\Models\Catalog\Service::class;
                        @endphp

                        <div class="card mb-4 shadow-sm border-0 p-3"
                             data-qty="{{ $item->quantity }}"
                             data-price="{{ $subject->price ?? 0 }}">
                            <div class="d-flex align-items-start justify-content-between flex-wrap">
                                <!-- Изображение -->
                                <div class="d-flex align-items-center">
                                    <img src="{{ $subject->image_url ?? 'https://via.placeholder.com/100' }}"
                                         alt="{{ $subject->name ?? 'Элемент' }}"
                                         class="rounded me-3 border"
                                         style="width:100px; height:auto;">

                                    <div>
                                        <h5 class="fw-bold mb-1">{{ $subject->name ?? 'Без названия' }}</h5>

                                        @if ($isService)
                                            <p class="text-muted mb-1">Услуга</p>
                                        @elseif ($isProduct)
                                            <p class="text-muted mb-1">Товар</p>
                                        @endif


                                        <!-- Кнопки изменения количества (универсальные для всех) -->
                                        <div class="d-flex align-items-center mt-2">
                                            <button class="btn btn-outline-secondary btn-qty"
                                                    data-action="decrease"
                                                    data-id="{{ $item->id }}">−</button>
                                            <span class="mx-2 fw-semibold qty-value">{{ $item->quantity }}</span>
                                            <button class="btn btn-outline-secondary btn-qty"
                                                    data-action="increase"
                                                    data-id="{{ $item->id }}">+</button>
                                        </div>

                                        <p class="text-dark fw-semibold mb-0 mt-2">
                                            {{ number_format($subject->price ?? 0, 2, ',', ' ') }} ₽
                                        </p>

                                        @if (!empty($subject->duration_minutes))
                                            @php
                                                $hours = floor($subject->duration_minutes / 60);
                                                $minutes = $subject->duration_minutes % 60;
                                                $timeString = $hours > 0
                                                    ? "{$hours} ч " . ($minutes > 0 ? "{$minutes} мин" : "")
                                                    : "{$minutes} мин";
                                            @endphp
                                            <p class="text-muted small mt-1">
                                                ⏱ {{ $timeString }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Управление -->
                                <div class="text-end mt-3 mt-sm-0">
                                    <button type="button"
                                            class="btn btn-link text-danger text-decoration-none btn-remove"
                                            data-id="{{ $item->id }}">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Правая колонка — Итог -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h5>ИТОГ ЗАКАЗА</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Количество элементов:</span>
                            <span id="item-count">{{ $cartItems->sum('quantity') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 fw-bold fs-5">
                            <span>Общая сумма:</span>
                            <span id="total-amount" class="text-dark">
                                {{ number_format($cartItems->sum(fn($i) => ($i->subject->price ?? 0) * $i->quantity), 2, ',', ' ') }} ₽
                            </span>
                        </div>

                        <form method="POST" action="{{ url('/cart/checkout') }}">
                            @csrf
                            <button type="submit" class="btn btn-orange w-100 mb-2">Оформить заказ</button>
                        </form>

                        <a href="{{ route('products.index') }}" class="btn btn-outline-dark w-100">
                            Продолжить покупки
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- === МОДАЛКА "ЗАКАЗ ОФОРМЛЕН" === -->
    <!-- === МОДАЛКА "ЗАКАЗ ОФОРМЛЕН" === -->
    <div id="orderSuccessModal" class="dz-modal hidden">
        <div class="dz-modal-content text-center">
            <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
            <h4 class="fw-bold mt-3">Заказ успешно оформлен!</h4>
            <p class="text-muted mt-2">Мы свяжемся с вами для подтверждения и уточнения деталей.</p>
            <button class="btn btn-orange mt-3 px-4" id="closeOrderModal">Ок</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const cartRoot = document.getElementById('cart-root');
            const cartBadge = document.querySelector('#cart-count-badge');
            const modal = document.getElementById('orderSuccessModal');
            const closeBtn = document.getElementById('closeOrderModal');
            const orderForm = document.querySelector('form[action$="/cart/checkout"]');

            // === TOAST ===
            function showToast(message, isError = false) {
                const toast = document.createElement('div');
                toast.className = `dz-toast show ${isError ? 'error' : ''}`;
                toast.innerHTML = `<i class="bi ${isError ? 'bi-exclamation-triangle' : 'bi-check-circle'} me-2"></i>${message}`;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 400);
                }, 2500);
            }

            // === УДАЛЕНИЕ ===
            document.querySelectorAll('.btn-remove').forEach(button => {
                button.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    const card = this.closest('.card');
                    try {
                        const response = await fetch(`/cart/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error();

                        card.style.transition = 'opacity 0.4s ease';
                        card.style.opacity = 0;
                        setTimeout(() => {
                            card.remove();
                            updateCartSummary();
                            showToast('Товар удалён из корзины 🗑️');
                        }, 400);
                    } catch {
                        showToast('Ошибка при удалении товара ⚠️', true);
                    }
                });
            });

            // === КНОПКИ +/- ===
            document.querySelectorAll('.btn-qty').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.dataset.id;
                    const card = btn.closest('.card');
                    const qtyEl = card.querySelector('.qty-value');
                    let quantity = parseInt(qtyEl.textContent);

                    if (btn.dataset.action === 'increase') quantity++;
                    else if (btn.dataset.action === 'decrease' && quantity > 1) quantity--;

                    qtyEl.textContent = quantity;
                    card.dataset.qty = quantity;

                    try {
                        const response = await fetch(`/cart/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ quantity })
                        });
                        if (!response.ok) throw new Error();
                        updateCartSummary();
                        showToast('Количество обновлено ✅');
                    } catch {
                        showToast('Ошибка при изменении количества ⚠️', true);
                    }
                });
            });

            // === ИТОГИ ===
            function updateCartSummary() {
                const cards = cartRoot.querySelectorAll('.col-lg-8 .card');
                const countElement = cartRoot.querySelector('#item-count');
                const totalElement = cartRoot.querySelector('#total-amount');
                let total = 0, units = 0;

                cards.forEach(card => {
                    const price = parseFloat(card.dataset.price || '0');
                    const qty = parseInt(card.dataset.qty || '1', 10);
                    units += qty;
                    total += price * qty;
                });

                if (countElement) countElement.textContent = units;
                if (totalElement) totalElement.textContent = total.toLocaleString('ru-RU', { style: 'currency', currency: 'RUB' });
                if (cartBadge) cartBadge.textContent = units;

                if (cards.length === 0) {
                    cartRoot.classList.add('fade-out');
                    setTimeout(() => {
                        cartRoot.innerHTML = `
                    <div class="empty-cart text-center py-5">
                        <i class="bi bi-cart-x" style="font-size:3rem;color:#ff6a00"></i>
                        <h4 class="fw-bold mt-3">Ваша корзина пуста</h4>
                        <p>Поиск товаров доступен выше</p>
                        <a href="/catalog/products" class="btn btn-orange mt-3 px-4">Вернуться в каталог</a>
                    </div>`;
                        cartRoot.classList.remove('fade-out');
                        if (cartBadge) cartBadge.textContent = 0;
                    }, 300);
                }
            }

            // === ОФОРМЛЕНИЕ ЗАКАЗА ===
            if (orderForm) {
                orderForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    try {
                        const res = await fetch(orderForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            modal.classList.remove('hidden');
                            document.querySelector('.col-lg-8')?.remove();
                            document.querySelector('.order-summary')?.remove();
                            showToast('Заказ успешно оформлен ✅');
                        } else throw new Error();
                    } catch {
                        showToast('Ошибка при оформлении заказа ⚠️', true);
                    }
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    window.location.href = '/catalog/products';
                });
            }
        });
    </script>

    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-qty {
            width: 32px;
            height: 32px;
            padding: 0;
            font-weight: bold;
            font-size: 1.1rem;
            line-height: 1;
            border-radius: 6px;
        }
        .qty-value {
            min-width: 24px;
            text-align: center;
        }


    </style>

@endsection
