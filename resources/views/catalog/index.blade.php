@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endpush

@section('content')
    <div class="container my-5">
        {{--        <h2 class="fw-bold mb-4 text-uppercase text-start">Каталог товаров</h2>--}}
        {{--        --}}{{-- === Разделитель после товаров === --}}
        {{--        <div class="catalog-divider my-4"></div> <!-- ← добавляем под заголовок -->--}}
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3 ms-2 me-2">
            <h2 class="fw-bold text-uppercase mb-0">Каталог товаров</h2>
            <a href="{{ route('products.all') }}" class="dz-viewall-link">Смотреть все</a>
        </div>
        <div class="catalog-divider ms-2 mb-4"></div>

        <div id="productsContainer" class="row g-4">
            @foreach ($products->take(8) as $product)
                <div class="col-md-3 dz-product-card">
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
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('products.all') }}" class="dz-showmore-btn" id="showMoreBtn">
                 БОЛЬШЕ
{{--                <i class="bi bi-arrow-right-short"></i>--}}
            </a>
        </div>

        {{-- === Товары, основанные на отзывах === --}}
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3 ms-2 me-2">
            <h2 class="fw-bold text-uppercase mb-0">РЕКОМЕНДУЕМ</h2>
        </div>
        <div class="catalog-divider ms-2 mb-4"></div>
        <div class="dz-carousel-wrapper position-relative">
            <button class="dz-carousel-btn prev">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="dz-product-carousel d-flex overflow-hidden">
                @foreach ($products as $product)
                    <div class="dz-product-card flex-shrink-0 me-3" style="width:220px;">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="{{ $product->image_url ?? 'https://via.placeholder.com/200x150' }}"
                                 alt="{{ $product->name }}"
                                 class="card-img-top"
                                 style="object-fit:contain; height:150px;">
                            <div class="card-body d-flex flex-column text-center">
                                <h6 class="fw-bold mb-2">{{ $product->name }}</h6>
                                <p class="fw-bold text-dark mb-3">
                                    {{ number_format($product->price, 2, ',', ' ') }} ₽
                                </p>

                                @auth
                                    <form method="POST" action="{{ url('/cart/add/'.$product->id.'/product') }}">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-orange w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-cart-plus"></i> Добавить
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                            class="btn btn-orange w-100 fw-bold btn-login-required d-flex align-items-center justify-content-center gap-2"
                                            data-login-url="{{ route('login') }}">
                                        <i class="bi bi-cart-plus"></i> Добавить
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="dz-carousel-btn next">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        {{-- === Категории услуг автосервиса === --}}
        @if(isset($services) && $services->count())
{{--            <p class="text-muted ms-3">Количество услуг: {{ $services->count() }}</p>--}}
            @php
                // Группируем услуги по категориям
                $categories = $services->groupBy('category');


                // Сопоставление категорий с изображениями
                $categoryImages = [
                    'Диагностика'                => '/storage/services/diagnostic.png',
                    'Техническое обслуживание'   => '/storage/services/maintenance.png',
                    'Ремонт двигателя'           => '/storage/services/engine.png',
                    'Ходовая часть'              => '/storage/services/suspension.png',
                    'Электрика и освещение'      => '/storage/services/electrics.png',
                    'Кузовные работы'            => '/storage/services/bodywork.png',
                    'Тормозная система'          => '/storage/services/brakes.png',
                    'Климат и кондиционирование' => '/storage/services/climate.png',
                ];
            @endphp

                <!-- Заголовок и кнопка -->
            <div class="d-flex justify-content-between align-items-center mt-5 mb-3 ms-2 me-2">
                <h2 class="fw-bold text-uppercase mb-0">Категории автосервиса</h2>
                <a href="{{ route('services.all') }}" class="dz-viewall-link">Смотреть все</a>
            </div>
            <div class="catalog-divider ms-2 mb-4"></div>

            <!-- Сетка категорий -->
            <div class="row g-4 justify-content-center">
                @foreach($categories as $category => $items)
                    @php
                        $imagePath = $categoryImages[$category] ?? '/storage/services/maintenance.png';
                    @endphp

                    <div class="col-6 col-md-3">
                        <a href="{{ route('services.category', ['category' => $category]) }}"
                           class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm border-0 text-center dz-category-card overflow-hidden">
                                <div class="dz-category-img-wrapper">
                                    <img src="{{ $imagePath }}" alt="{{ $category }}" class="dz-category-img">
                                </div>
                                <div class="p-3">
                                    <h6 class="fw-bold mb-1">{{ $category }}</h6>
                                    <p class="small text-muted mb-0">{{ $items->count() }} услуг</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tools" style="font-size:3rem;color:#ff6a00"></i>
                <h5 class="fw-bold mt-3">Пока нет категорий</h5>
                <p class="text-muted">Следите за обновлениями сервиса</p>
            </div>
        @endif

    </div>

    {{-- Toast уведомление --}}
    <div id="dz-toast" class="dz-toast">Пожалуйста, войдите, чтобы добавить товары в корзину</div>

    {{-- JS логика --}}
    <script>
        // ======== КЛИК ПО КАРТОЧКЕ ТОВАРА ========
        document.querySelectorAll('.dz-clickable-card').forEach(card => {
            card.addEventListener('click', e => {
                // Игнорируем клики по кнопкам или формам
                if (e.target.closest('button') || e.target.closest('form')) return;
                const url = card.dataset.url;
                if (url) window.location.href = url;
            });
        });


        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('dz-toast');
            const loginButtons = document.querySelectorAll('.btn-login-required');

            loginButtons.forEach(btn => {
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

            const carousel = document.querySelector('.dz-product-carousel');
            const prevBtn = document.querySelector('.dz-carousel-btn.prev');
            const nextBtn = document.querySelector('.dz-carousel-btn.next');
            const cardWidth = document.querySelector('.dz-product-card').offsetWidth + 16;
            const scrollStep = cardWidth * 5;

            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollStep, behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollStep, behavior: 'smooth' });
            });

            const showMoreBtn = document.getElementById('showMoreBtn');
            showMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showMoreBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Загрузка...`;
                setTimeout(() => {
                    window.location.href = showMoreBtn.getAttribute('href');
                }, 1200);
            });
        });
    </script>

    {{-- Стили --}}
    <style>
        .btn-orange {
            background-color: var(--dz-orange);
            border-color: var(--dz-orange);
            color: #fff;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-orange:hover {
            background-color: #ff7a1a;
            border-color: #ff7a1a;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 106, 0, 0.3);
        }
        .btn-orange:active {
            transform: scale(0.96);
            box-shadow: none;
        }
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
        .dz-carousel-btn {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            background: #fff;
            color: var(--dz-dark);
            border: 2px solid #ddd;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            transition: all 0.25s ease-in-out;
        }
        .dz-carousel-btn:hover {
            background: var(--dz-orange);
            color: #fff;
            border-color: var(--dz-orange);
            transform: translateY(-50%) scale(1.1);
        }
        .dz-carousel-btn.prev { left: -25px; }
        .dz-carousel-btn.next { right: -25px; }
        .btn-orange i { font-size: 1.1rem; }

        .catalog-divider {
            width: 100px;               /* длина линии */
            height: 3px;                /* толщина */
            background: var(--dz-orange);
            border-radius: 3px;
        }

    </style>
@endsection
