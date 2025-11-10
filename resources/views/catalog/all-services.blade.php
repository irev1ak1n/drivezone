@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
@endpush

@section('content')
    <div class="container-fluid my-5">
        <div class="row g-4">

            {{-- ==== ЛЕВАЯ КОЛОНКА — ФИЛЬТРЫ ==== --}}
            <aside class="col-lg-3 col-md-4">
                <div class="card shadow-sm border-0 p-3 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-3 text-uppercase"><i class="bi bi-funnel me-2"></i>Фильтры</h5>

                    {{-- Категории --}}
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Категория</h6>
                        <select id="filterCategory" class="form-select">
                            <option value="">Все категории</option>
                            <option value="engine">Двигатель</option>
                            <option value="brakes">Тормозная система</option>
                            <option value="suspension">Подвеска</option>
                            <option value="diagnostics">Диагностика</option>
                            <option value="other">Прочее</option>
                        </select>
                    </div>

                    {{-- Статус --}}
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Статус услуги</h6>
                        <select id="filterStatus" class="form-select">
                            <option value="">Все</option>
                            <option value="Услуга доступна">Доступные</option>
                            <option value="Не доступна">Недоступные</option>
                        </select>
                    </div>

                    {{-- Диапазон цен --}}
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Цена (₽)</h6>
                        <div class="d-flex gap-2">
                            <input type="number" id="priceMin" class="form-control" placeholder="От">
                            <input type="number" id="priceMax" class="form-control" placeholder="До">
                        </div>
                    </div>

                    {{-- Кнопки --}}
                    <button id="applyFilters" class="btn btn-orange w-100 fw-bold mt-2">
                        Применить фильтры
                    </button>
                    <button id="resetFilters" class="btn btn-outline-dark w-100 fw-semibold mt-2">
                        Сбросить
                    </button>
                </div>
            </aside>

            {{-- ==== ПРАВАЯ КОЛОНКА — СПИСОК УСЛУГ ==== --}}
            <div class="col-lg-9 col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-uppercase m-0">Все услуги автосервиса</h2>
                    <select id="sortSelect" class="form-select w-auto">
                        <option value="default">Сортировать...</option>
                        <option value="price_asc">По цене ↑</option>
                        <option value="price_desc">По цене ↓</option>
                        <option value="name_asc">По названию A–Z</option>
                        <option value="name_desc">По названию Z–A</option>
                    </select>
                </div>

                {{-- === Контейнер для услуг === --}}
                <div id="servicesContainer" class="row g-4">
                    @foreach ($services as $service)
                        <div class="col-md-4 dz-product-card">
                            <div class="card h-100 shadow-sm border-0 dz-clickable-card {{ $service->status === 'Не доступна' ? 'opacity-75' : '' }}"
                                 data-url="{{ route('services.show', $service->id) }}">

                                <img src="{{ $service->image_url ?? '/storage/services/default.png' }}"
                                     class="card-img-top rounded-top"
                                     alt="{{ $service->name }}"
                                     style="object-fit:contain; height:160px; background:#f8f8f8; border-bottom:1px solid #eee;">

                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $service->name }}</h6>

                                        @if ($service->status === 'Услуга доступна')
                                            <p class="text-success small fw-semibold mb-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Услуга доступна
                                            </p>
                                        @else
                                            <p class="text-danger small fw-semibold mb-2">
                                                <i class="bi bi-x-circle-fill me-1"></i> Временно недоступна
                                            </p>
                                        @endif

                                        <p class="text-muted small mb-2" style="min-height: 80px;">
                                            {!! nl2br(e(Str::limit($service->description, 150))) !!}
                                        </p>

                                        <p class="fw-semibold text-dark fs-6 mb-0">
                                            {{ number_format($service->price, 2, ',', ' ') }} ₽
                                        </p>

                                        @php
                                            $hours = intdiv($service->duration_minutes, 60);
                                            $minutes = $service->duration_minutes % 60;
                                        @endphp
                                        <p class="text-muted small">
                                            ⏱
                                            @if ($hours > 0)
                                                {{ $hours }} ч{{ $minutes > 0 ? ' ' . $minutes . ' мин' : '' }}
                                            @else
                                                {{ $minutes }} мин
                                            @endif
                                        </p>
                                    </div>

                                    @if ($service->status === 'Услуга доступна')
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
                                        <button type="button" class="btn btn-secondary w-100 mt-2 fw-bold" disabled>
                                            <i class="bi bi-ban"></i> Недоступна
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($services->isEmpty())
                        <p class="text-muted text-center mt-5 w-100">
                            По вашему запросу ничего не найдено.
                        </p>
                    @endif
                </div>

                {{-- === Контейнер для пагинации === --}}
                <div class="mt-4" id="servicesPagination">
                    {{ $services->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- === JS логика === --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let servicesContainer = document.getElementById('servicesContainer');
            let paginationWrap    = document.getElementById('servicesPagination');
            const btnApply  = document.getElementById('applyFilters');
            const btnReset  = document.getElementById('resetFilters');
            const selectSort= document.getElementById('sortSelect');
            const state = { loading: false, aborter: null };

            function lockUI(active) {
                const pg = document.querySelector('#servicesPagination .pagination');
                if (pg) {
                    pg.style.pointerEvents = active ? 'none' : 'auto';
                    pg.style.opacity       = active ? '0.4'  : '1';
                }
            }

            function renderHTML(html) {
                const temp = document.createElement('div');
                temp.innerHTML = html.trim();

                const newGrid = temp.querySelector('#servicesContainer');
                const newPag  = temp.querySelector('#servicesPagination');

                if (newGrid) {
                    servicesContainer.replaceWith(newGrid);
                    servicesContainer = document.getElementById('servicesContainer');
                }
                if (newPag) {
                    paginationWrap.replaceWith(newPag);
                    paginationWrap = document.getElementById('servicesPagination');
                }

                attachCardClicks();
                attachPagination();
            }

            async function applyFilters(page = 1) {
                if (state.loading) return;
                state.loading = true;

                const category = document.getElementById('filterCategory').value;
                const status   = document.getElementById('filterStatus').value;
                const min_price= document.getElementById('priceMin').value;
                const max_price= document.getElementById('priceMax').value;
                const sort     = selectSort.value;

                const params = new URLSearchParams({ page, category, status, min_price, max_price, sort });
                history.replaceState({}, '', '?' + params.toString());

                lockUI(true);
                servicesContainer.style.opacity = '0.5';
                servicesContainer.style.pointerEvents = 'none';

                state.aborter?.abort();
                state.aborter = new AbortController();

                try {
                    const res = await fetch(`{{ route('services.filter') }}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal : state.aborter.signal
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    renderHTML(data.html);
                    attachCardClicks();
                    attachPagination();
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Ошибка фильтрации:', err);
                        servicesContainer.innerHTML = `
                     <p class="text-muted text-center my-5">Ошибка загрузки услуг. Попробуйте позже.</p>`;
                    }
                }
                finally {
                    lockUI(false);
                    servicesContainer.style.opacity = '1';
                    servicesContainer.style.pointerEvents = 'auto';
                    state.loading = false;
                }
            }

            function attachPagination() {
                document.querySelectorAll('#servicesPagination .pagination a').forEach(link => {
                    const clone = link.cloneNode(true);
                    link.replaceWith(clone);
                });
                document.querySelectorAll('#servicesPagination .pagination a').forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        const page = new URL(link.href).searchParams.get('page') || 1;
                        applyFilters(page);
                    });
                });
            }

            function attachCardClicks() {
                document.querySelectorAll('.dz-clickable-card').forEach(card => {
                    if (card.dataset.bound) return;
                    card.dataset.bound = '1';
                    card.addEventListener('click', e => {
                        if (e.target.closest('button') || e.target.closest('form')) return;
                        location.href = card.dataset.url;
                    });
                });
            }

            function resetFilters() {
                document.getElementById('filterCategory').value = '';
                document.getElementById('filterStatus').value = '';
                document.getElementById('priceMin').value = '';
                document.getElementById('priceMax').value = '';
                selectSort.value = 'default';
                applyFilters(1);
            }

            // Привязки
            btnApply .addEventListener('click', () => applyFilters(1));
            selectSort.addEventListener('change', () => applyFilters(1));
            btnReset .addEventListener('click', resetFilters);
            attachCardClicks();
            attachPagination();

            // Восстановление фильтров из URL
            const urlParams = new URLSearchParams(location.search);
            if (urlParams.has('category')) document.getElementById('filterCategory').value = urlParams.get('category');
            if (urlParams.has('status'))   document.getElementById('filterStatus').value   = urlParams.get('status');
            if (urlParams.has('min_price'))document.getElementById('priceMin').value      = urlParams.get('min_price');
            if (urlParams.has('max_price'))document.getElementById('priceMax').value      = urlParams.get('max_price');
            if (urlParams.has('sort'))     selectSort.value                               = urlParams.get('sort');

            if (urlParams.toString()) {
                const page = urlParams.get('page') || 1;
                applyFilters(page);
            }
        });
    </script>

    {{-- === Дополнительные стили === --}}
    <style>
        aside .card h6 { color: #222; }
        aside .form-check-label { font-size: 0.9rem; color: #333; }
        aside input[type="number"] { font-size: 0.9rem; }

        .dz-clickable-card {
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .dz-clickable-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .card.opacity-75 { filter: grayscale(0.3); transition: 0.2s ease; }

        .btn-secondary {
            background: #ccc;
            border: none;
            cursor: not-allowed;
        }
        .pagination { transition: opacity 0.3s ease, filter 0.3s ease; }
        .pagination[style*="opacity: 0.4"] { filter: grayscale(0.6); }
        </style>

@endsection
