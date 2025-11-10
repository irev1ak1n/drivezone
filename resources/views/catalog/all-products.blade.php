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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="catEngine" value="engine">
                            <label class="form-check-label" for="catEngine">Двигатель</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="catBrakes" value="brakes">
                            <label class="form-check-label" for="catBrakes">Тормозная система</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="catSuspension" value="suspension">
                            <label class="form-check-label" for="catSuspension">Подвеска</label>
                        </div>
                    </div>

                    {{-- Бренды --}}
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Бренд</h6>
                        <select id="filterBrand" class="form-select">
                            <option value="">Все бренды</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Ford">Ford</option>
                            <option value="BMW">BMW</option>
                            <option value="Volkswagen">Volkswagen</option>
                            <option value="Nissan">Nissan</option>
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

                    {{-- Кнопка фильтрации --}}
                    <button id="applyFilters" class="btn btn-orange w-100 fw-bold mt-2">
                        Применить фильтры
                    </button>

                    {{-- Сброс --}}
                    <button id="resetFilters" class="btn btn-outline-dark w-100 fw-semibold mt-2">
                        Сбросить
                    </button>
                </div>
            </aside>

            {{-- ==== ПРАВАЯ КОЛОНКА — СПИСОК ТОВАРОВ ==== --}}
            <div class="col-lg-9 col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-uppercase m-0">Все товары</h2>
                    <select id="sortSelect" class="form-select w-auto">
                        <option value="default">Сортировать...</option>
                        <option value="price_asc">По цене ↑</option>
                        <option value="price_desc">По цене ↓</option>
                        <option value="name_asc">По названию A–Z</option>
                        <option value="name_desc">По названию Z–A</option>
                    </select>
                </div>

                {{-- === Контейнер для товаров === --}}
                <div id="productsContainer" class="row g-4">
                    @foreach ($products as $product)
                        <div class="col-md-4 dz-product-card">
                            <div class="card h-100 shadow-sm border-0 dz-clickable-card"
                                 data-url="{{ route('products.show', $product->id) }}">
                                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/300x200' }}"
                                     class="card-img-top rounded-top"
                                     alt="{{ $product->name }}">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                                    <p class="text-muted mb-1">{{ $product->brand->name ?? 'Без бренда' }}</p>
                                    <p class="small text-secondary flex-grow-1">
                                        {{ $product->product_description }}
                                    </p>
                                    <p class="fw-bold text-dark fs-5 mb-3">
                                        {{ number_format($product->price, 2, ',', ' ') }} ₽
                                    </p>

                                    @auth
                                        <form method="POST" action="{{ url('/cart/add/'.$product->id.'/product') }}">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-orange w-100 fw-bold">
                                                Добавить в корзину
                                            </button>
                                        </form>
                                    @else
                                        <button type="button"
                                                class="btn btn-orange w-100 fw-bold btn-login-required"
                                                data-login-url="{{ route('login') }}">
                                            Добавить в корзину
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($products->isEmpty())
                        <p class="text-muted text-center mt-5 w-100">
                            По вашему запросу ничего не найдено.
                        </p>
                    @endif
                </div>

                {{-- === Контейнер для пагинации (добавлен id) === --}}
                <div class="mt-4" id="catalogPagination">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let productsContainer = document.getElementById('productsContainer'); // ← let, не const
            const btnApply  = document.getElementById('applyFilters');
            const btnReset  = document.getElementById('resetFilters');
            const selectSort= document.getElementById('sortSelect');
            let  paginationWrap = document.getElementById('catalogPagination');   // обёртка пагинации (может быть null)

            const state = { loading: false, aborter: null };

            function lockUI(active) {
                const pg = document.querySelector('#catalogPagination .pagination');
                if (pg) {
                    pg.style.pointerEvents = active ? 'none' : 'auto';
                    pg.style.opacity       = active ? '0.4'  : '1';
                }
            }

            function renderHTML(html) {
                const temp = document.createElement('div');
                temp.innerHTML = html.trim();

                // новый грид
                const incomingGrid = temp.querySelector('#productsContainer') || temp.querySelector('.row');
                if (incomingGrid) {
                    if (incomingGrid.id === 'productsContainer') {
                        // заменяем весь узел и ПЕРЕ-находим ссылку
                        productsContainer.replaceWith(incomingGrid);
                        productsContainer = document.getElementById('productsContainer');
                    } else {
                        // у ответа нет id — просто обновим содержимое текущего контейнера
                        productsContainer.innerHTML = incomingGrid.innerHTML;
                    }
                }

                // новая пагинация
                const incomingPagWrap = temp.querySelector('#catalogPagination');
                if (incomingPagWrap) {
                    if (paginationWrap) {
                        paginationWrap.replaceWith(incomingPagWrap);
                    } else {
                        // если раньше обёртки не было — попробуем найти место после грида
                        productsContainer.insertAdjacentElement('afterend', incomingPagWrap);
                    }
                    paginationWrap = document.getElementById('catalogPagination');
                }
            }

            async function applyFilters(page = 1) {
                if (state.loading) return;
                state.loading = true;

                const categories = [...document.querySelectorAll('input[type=checkbox]:checked')].map(cb => cb.value);
                const brand      = document.getElementById('filterBrand').value;
                const min_price  = document.getElementById('priceMin').value;
                const max_price  = document.getElementById('priceMax').value;
                const sort       = selectSort.value;

                const params = new URLSearchParams({ page, brand, min_price, max_price, sort });
                categories.forEach(c => params.append('categories[]', c));
                history.replaceState({}, '', '?' + params.toString());

                lockUI(true);
                productsContainer.style.opacity = '0.5';

                state.aborter?.abort();
                state.aborter = new AbortController();

                try {
                    const res = await fetch(`/catalog/filter?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal : state.aborter.signal
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    const data = await res.json();
                    renderHTML(data.html);
                    attachCardClickEvents();
                    attachPaginationLinks();
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Ошибка фильтрации:', err);
                        productsContainer.innerHTML = `
                    <p class="text-muted text-center my-5">Ошибка загрузки товаров. Попробуйте позже.</p>`;
                    }
                } finally {
                    lockUI(false);
                    productsContainer.style.opacity = '1';
                    state.loading = false;
                }
            }

            function attachPaginationLinks() {
                // Сначала очистим старые обработчики
                document.querySelectorAll('#catalogPagination .pagination a').forEach(link => {
                    const clone = link.cloneNode(true);
                    link.replaceWith(clone);
                });

                document.querySelectorAll('#catalogPagination .pagination a').forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        const url  = new URL(link.href, location.origin);
                        const page = url.searchParams.get('page') || 1;
                        applyFilters(page);
                    });
                });
            }

            function attachCardClickEvents() {
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
                document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
                document.getElementById('filterBrand').value = '';
                document.getElementById('priceMin').value   = '';
                document.getElementById('priceMax').value   = '';
                selectSort.value = 'default';
                applyFilters(1);
            }

            // Привязки
            btnApply .addEventListener('click', () => applyFilters(1));
            selectSort.addEventListener('change', () => applyFilters(1));
            btnReset .addEventListener('click', resetFilters);
            attachCardClickEvents();
            attachPaginationLinks();

            // Восстановление из URL
            const urlParams = new URLSearchParams(location.search);
            if (urlParams.has('brand'))    document.getElementById('filterBrand').value = urlParams.get('brand');
            if (urlParams.has('min_price'))document.getElementById('priceMin').value   = urlParams.get('min_price');
            if (urlParams.has('max_price'))document.getElementById('priceMax').value   = urlParams.get('max_price');
            if (urlParams.has('sort'))     selectSort.value = urlParams.get('sort');
            urlParams.getAll('categories[]').forEach(val => {
                const cb = document.querySelector(`input[type=checkbox][value="${val}"]`);
                if (cb) cb.checked = true;
            });

            if (urlParams.toString()) {
                const page = urlParams.get('page') || 1;
                applyFilters(page);
            }
        });
    </script>

    {{-- === Дополнительные стили === --}}
    <style>
        /* Левая колонка фильтров */
        aside .card h6 { color: #222; }
        aside .form-check-label { font-size: 0.9rem; color: #333; }
        aside input[type="number"] { font-size: 0.9rem; }

        /* Область карточек */
        .dz-clickable-card {
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .dz-clickable-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Toast */
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

        .pagination {
            transition: opacity 0.3s ease, filter 0.3s ease;
        }
        .pagination[style*="opacity: 0.4"] {
            filter: grayscale(0.6);
        }


    </style>
@endsection
