<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveZone — Сервис и запчасти</title>

    <!-- CSRF-токен для AJAX и fetch -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Подключение Vite -->
    @vite([
        'resources/css/app.css',
        'resources/css/drivezone.css',
        'resources/js/app.js',
        'resources/css/layouts/layout.css',
        'resources/js/Layouts/Scripts/layout.js'
    ])

    <!-- Стили для конкретных страниц -->
    @stack('styles')

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-light @auth authenticated @endauth">

<!-- ===== ЕДИНАЯ ШАПКА (закреплённая) ===== -->
<header class="dz-header-fixed">
    @unless (Request::is('login') || Request::is('register'))
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <!-- Левая часть: меню + логотип -->
                <div class="d-flex align-items-center">
                    <button class="dz-menu btn p-0 me-3" type="button" aria-label="Menu">
                        <svg class="dz-menu-icon" viewBox="0 0 36 24" width="36" height="24" aria-hidden="true">
                            <rect x="2" y="2" width="32" height="4" rx="2" />
                            <rect x="2" y="10" width="26" height="4" rx="2" />
                            <rect x="2" y="18" width="20" height="4" rx="2" />
                        </svg>
                    </button>

{{--                    <a class="navbar-brand d-flex align-items-center drivezone-logo m-0"--}}
{{--                       href="{{ url('/catalog/products') }}">--}}
{{--                        <span>DriveZone</span>--}}
{{--                    </a>--}}
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <span class="navbar-brand d-flex align-items-center drivezone-logo m-0" style="cursor: default;">
                            <span>DriveZone</span>
                        </span>
                    @else
                        <a class="navbar-brand d-flex align-items-center drivezone-logo m-0"
                           href="{{ url('/catalog/products') }}">
                            <span>DriveZone</span>
                        </a>
                    @endif

                </div>

                <!-- Правая часть: корзина и профиль -->
                <div class="d-flex align-items-center">
{{--                    <!-- Корзина -->--}}
{{--                    <a href="{{ url('/cart') }}" class="position-relative me-4 text-decoration-none text-light">--}}
{{--                        <i class="bi bi-cart3 fs-5"></i>--}}
{{--                        <span id="cart-count-badge"--}}
{{--                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">--}}
{{--                            {{ auth()->check()--}}
{{--                                ? \App\Models\Orders\Cart::where('user_id', auth()->id())->sum('quantity')--}}
{{--                                : 0 }}--}}
{{--                        </span>--}}
{{--                    </a>--}}
                    @unless(auth()->check() && auth()->user()->role === 'admin')
                        <!-- Корзина -->
                        <a href="{{ url('/cart') }}" class="position-relative me-4 text-decoration-none text-light">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span id="cart-count-badge"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                            {{ auth()->check()
                                ? \App\Models\Orders\Cart::where('user_id', auth()->id())->sum('quantity')
                                : 0 }}
                            </span>
                        </a>
                    @endunless

                    <!-- Профиль / Войти -->
                    <div class="dropdown">
                        <button class="dz-avatar-btn dropdown-toggle" type="button" id="userMenu"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            @auth
                                @php
                                    $u = auth()->user();
                                    $isCustom = $u && $u->avatar_style === 'custom' && $u->photo_url;
                                    $isLetter = $u && $u->avatar_style === 'letter';
                                    $stethem = asset('storage/stethem.jpg');
                                @endphp

                                @if($isCustom)
                                    <img src="{{ $u->photo_url }}" alt="avatar"
                                         class="dz-avatar-img navbar-avatar"
                                         onmousedown="startPressTimer()" onmouseup="cancelPressTimer()" onmouseleave="cancelPressTimer()">
                                @elseif($isLetter)
                                    <div class="dz-avatar-fallback navbar-avatar"
                                         onmousedown="startPressTimer()" onmouseup="cancelPressTimer()" onmouseleave="cancelPressTimer()">
                                        {{ mb_substr($u->first_name ?? $u->name, 0, 1) }}
                                    </div>
                                @else
                                    <img src="{{ $stethem }}" alt="default avatar"
                                         class="dz-avatar-img navbar-avatar"
                                         onmousedown="startPressTimer()" onmouseup="cancelPressTimer()" onmouseleave="cancelPressTimer()">
                                @endif
                            @else
                                <i class="bi bi-person dz-avatar-icon"></i>
                            @endauth
                        </button>

                        @auth
                            <div class="dz-user-tooltip" id="userTooltip">
                                <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong><br>
                                <span class="dz-user-email">{{ auth()->user()->email }}</span>
                            </div>
                        @endauth

                        <!-- Меню -->
                        <div class="dropdown-menu dropdown-menu-end p-0 dz-account-panel" aria-labelledby="userMenu">
                            @guest
                                <div class="p-3 pb-2">
                                    <a href="{{ route('login') }}" class="btn dz-account-cta w-100">
                                        Войти или создать аккаунт
                                    </a>
                                </div>
                                <ul class="list-unstyled m-0">
                                    <li><a href="#" class="dz-item"><span class="dz-item-icon"><i class="bi bi-award"></i></span>
                                            <span class="dz-item-text"><span class="dz-item-title">Бонусы</span>
                                        <span class="dz-item-desc">Узнайте о нашей бонусной программе</span></span></a></li>
                                    <li><a href="#" class="dz-item"><span class="dz-item-icon"><i class="bi bi-box-seam"></i></span>
                                            <span class="dz-item-text"><span class="dz-item-title">Отследить заказ</span>
                                        <span class="dz-item-desc">Используйте email и номер заказа</span></span></a></li>
                                    <li><a href="#" class="dz-item"><span class="dz-item-icon"><i class="bi bi-geo-alt"></i></span>
                                            <span class="dz-item-text"><span class="dz-item-title">Найти автосервис</span>
                                        <span class="dz-item-desc">Подберём сервис рядом с вами</span></span></a></li>
                                </ul>
                            @endguest

                            @auth
                                <ul class="list-unstyled m-0">
                                    @unless(auth()->check() && auth()->user()->role === 'admin')
                                        <li><a href="{{ url('/profile') }}" class="dz-item">
                                                <span class="dz-item-icon"><i class="bi bi-person-lines-fill"></i></span>
                                                <span class="dz-item-text">
                                                <span class="dz-item-title">Профиль</span>
                                                <span class="dz-item-desc">Контакты и настройки</span>
                                            </span></a>
                                        </li>
                                    @endunless

                                    @if(auth()->user()->role === 'admin')
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}" class="dz-item">
                                                <span class="dz-item-icon"><i class="bi bi-gear-wide-connected"></i></span>
                                                <span class="dz-item-text">
                                                    <span class="dz-item-title">Админ-панель</span>
                                                    <span class="dz-item-desc">Управление пользователями</span>
                                                </span>
                                            </a>
                                        </li>
                                    @endif

                                        @unless(auth()->check() && auth()->user()->role === 'admin')
                                            <li><a href="#" class="dz-item">
                                                    <span class="dz-item-icon"><i class="bi bi-truck"></i></span>
                                                        <span class="dz-item-text">
                                                        <span class="dz-item-title">Мои заказы</span>
                                                        <span class="dz-item-desc">История и статусы</span>
                                                    </span></a>
                                            </li>
                                        @endunless

                                        <li class="dz-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">@csrf
                                            <button type="submit" class="dz-item dz-item-danger w-100 text-start">
                                                <span class="dz-item-icon"><i class="bi bi-box-arrow-right"></i></span>
                                                <span class="dz-item-text">
                                                    <span class="dz-item-title">Выйти</span>
                                                    <span class="dz-item-desc">Завершить сессию</span>
                                                </span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- ===== Верхняя панель (AutoZone-стиль) ===== -->
        @unless(auth()->check() && auth()->user()->role === 'admin')
            <div class="dz-topbar bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center shadow-sm">
                <button class="btn btn-outline-dark d-flex align-items-center gap-2 fw-semibold" id="btn-select-car">
                    <i class="bi bi-car-front-fill"></i><span>Добавить автомобиль</span>
                </button>

                <form class="dz-searchbar d-flex flex-grow-1 mx-4" action="{{ route('catalog.search') }}" method="GET">
                    <input type="search" name="q" class="form-control form-control-lg"
                           placeholder="Поиск по запчастям или услугам..." />
                    <button class="btn btn-orange px-4"><i class="bi bi-search"></i></button>
                </form>

                <button class="btn btn-outline-dark d-flex align-items-center gap-2 fw-semibold" id="btn-store-info">
                    <i class="bi bi-info-circle"></i><span>О магазине</span>
                </button>
            </div>
        @endunless
    @endunless
</header>

<!-- ================= МОДАЛКА ПРОФИЛЯ ================= -->
@auth
    @php
        $u = auth()->user();
        $previewUrl = ($u && $u->avatar_style === 'custom' && $u->photo_url)
            ? $u->photo_url
            : asset('storage/stethem.jpg');
    @endphp

    <div id="profileModal" class="dz-modal hidden" onclick="closeProfileModal()">
        <div class="dz-modal-content" onclick="event.stopPropagation()">
            <button type="button" class="dz-close-btn" onclick="closeProfileModal()">&times;</button>
            <img src="{{ $previewUrl }}" alt="avatar" class="dz-avatar-preview mb-3">

            <div class="profile-actions">
                <button type="button" class="btn-edit" onclick="triggerFileInput()">
                    <i class="bi bi-pencil-square"></i> Изменить
                </button>
                <button type="button" class="btn-delete">
                    <i class="bi bi-trash"></i> Удалить
                </button>
            </div>

            <input type="file" id="avatarInput" accept="image/*" style="display:none">
        </div>
    </div>
@endauth

<!-- Модалка -->
<div id="storeInfoModal">
    <div class="store-modal-content">
        <button class="store-modal-close" id="closeStoreModal">&times;</button>
        <h3>DriveZone</h3>
        <p>Добро пожаловать в <strong>DriveZone</strong> — современный онлайн-сервис автозапчастей и обслуживания автомобилей.</p>
        <hr>
        <p><strong>Адрес:</strong> г. Донецк, ул. 230 Стрелковой Дивизии, 40</p>
        <p><strong>Телефон:</strong> <a href="tel:+17047565439">+1 (704) 756-54-39</a></p>
        <p><strong>Email:</strong> <a href="mailto:illiareviakin2008@gmail.com">illiareviakin2008@gmail.com</a></p>
    </div>
</div>

<!-- ================= КАСТОМНОЕ ПОДТВЕРЖДЕНИЕ ================= -->
<div id="confirmModal" class="dz-modal hidden" onclick="closeConfirmModal()">
    <div class="dz-modal-content confirm" onclick="event.stopPropagation()">
        <button type="button" class="dz-close-btn" onclick="closeConfirmModal()">&times;</button>
        <h5 id="confirmTitle">Подтверждение</h5>
        <p id="confirmMessage">Вы уверены?</p>
        <div style="display:flex; justify-content:center; gap:12px;">
            <button id="confirmYes" class="btn btn-primary">Да</button>
            <button class="btn btn-secondary" onclick="closeConfirmModal()">Отмена</button>
        </div>
    </div>
</div>

<!-- ================= ALERT (ошибки) ================= -->
@if (session('error'))
    <div class="dz-alert-container">
        <div class="dz-alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

<!-- ================= LOGOUT TOAST ================= -->
@if (session('logout_message'))
    <div id="dz-toast" class="dz-toast show">
        {{ session('logout_message') }}
    </div>
@endif

<!-- ================= LOGIN SUCCESS ANIMATION ================= -->
@if (session('login_success'))
    <div id="dz-loader">
        <div class="dz-loader-icon"></div>
        DRIVEZONE
        <p class="mt-2 text-light fs-6" style="opacity: 0.8;">Вход выполнен...</p>
    </div>
@endif

<!-- ================= ОСНОВНОЙ КОНТЕНТ ================= -->
<main class="container">
    @yield('content')
</main>

<!-- Bootstrap JS (обязательно перед закрывающим body) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
