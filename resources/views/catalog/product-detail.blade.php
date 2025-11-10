@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
@endpush

@section('content')
    <div class="container my-5">
        <div class="row g-5">
            <!-- Изображение -->
            <div class="col-md-5 text-center">
                <img src="{{ $product->image_url ?? '/storage/products/default.png' }}"
                     alt="{{ $product->name }}"
                     class="img-fluid rounded shadow-sm"
                     style="max-height: 400px; object-fit: contain;">
            </div>

            <!-- Описание -->
            <div class="col-md-7">
                <h2 class="fw-bold mb-2">{{ $product->name }}</h2>
                <p class="text-muted mb-1">Бренд: <strong>{{ $product->brand->name ?? 'Без бренда' }}</strong></p>

                <h4 class="text-orange fw-bold my-3">
                    {{ number_format($product->price, 2, ',', ' ') }} ₽
                </h4>

                <p class="mb-4 text-secondary" style="line-height:1.7;">
                    {{ $product->product_description }}
                </p>

                @auth
                    <form method="POST" action="{{ url('/cart/add/'.$product->id.'/product') }}">
                        @csrf
                        <div class="input-group mb-3" style="max-width: 160px;">
                            <button class="btn btn-outline-dark" type="button" id="minus">−</button>
                            <input type="number" class="form-control text-center" name="quantity" value="1" min="1">
                            <button class="btn btn-outline-dark" type="button" id="plus">+</button>
                        </div>
                        <button type="submit" class="btn btn-orange px-4 fw-bold">
                            <i class="bi bi-cart-plus"></i> Добавить в корзину
                        </button>
                    </form>
                @else
                    <button class="btn btn-orange fw-bold btn-login-required px-4"
                            data-login-url="{{ route('login') }}">
                        <i class="bi bi-cart-plus"></i> Добавить в корзину
                    </button>
                @endauth
            </div>
        </div>

        <hr class="my-5">

        <!-- Характеристики -->
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3 text-uppercase">Характеристики товара</h5>
                <ul class="list-unstyled small">
                    <li><strong>ID:</strong> {{ $product->id }}</li>
{{--                    <li><strong>Категория:</strong> {{ $product->category ?? '—' }}</li>--}}
                    <li><strong>Бренд:</strong> {{ $product->brand->name ?? '—' }}</li>
                    <li><strong>Цена:</strong> {{ number_format($product->price, 2, ',', ' ') }} ₽</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold mb-3 text-uppercase">Описание</h5>
                <p class="small text-muted">{{ $product->product_description }}</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('plus')?.addEventListener('click', () => {
            const input = document.querySelector('input[name="quantity"]');
            input.value = parseInt(input.value) + 1;
        });
        document.getElementById('minus')?.addEventListener('click', () => {
            const input = document.querySelector('input[name="quantity"]');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        });
    </script>
@endsection
