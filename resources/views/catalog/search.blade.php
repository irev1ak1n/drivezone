@extends('layouts.layout')

@push('styles')
    @vite(['resources/css/drivezone.css'])
@endpush

@section('content')
    <div class="container my-5">
        <h2 class="fw-bold text-uppercase text-center mb-4">
            Результаты поиска: "{{ $query }}"
        </h2>

        {{-- === Товары === --}}
        <h4 class="fw-bold mb-3">Товары</h4>
        @if($products->count())
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-md-3 dz-product-card">
                        <div class="card h-100 shadow-sm border-0 dz-clickable-card"
                             data-url="{{ route('products.show', $product->id) }}">
                            <img src="{{ $product->image_url ?? '/storage/products/default.png' }}"
                                 class="card-img-top rounded-top"
                                 alt="{{ $product->name }}">
                            <div class="card-body d-flex flex-column">
                                <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                                <p class="small text-muted flex-grow-1">{{ Str::limit($product->product_description, 80) }}</p>
                                <p class="fw-bold text-dark fs-6">{{ number_format($product->price, 2, ',', ' ') }} ₽</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
        @else
            <p class="text-muted">Нет товаров по вашему запросу.</p>
        @endif

        <hr class="my-5">

        {{-- === Услуги === --}}
        <h4 class="fw-bold mb-3">Услуги</h4>
        @if($services->count())
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-md-3 dz-product-card">
                        <div class="card h-100 shadow-sm border-0 dz-clickable-card"
                             data-url="{{ route('services.show', $service->id) }}">
                            <img src="{{ $service->image_url ?? '/storage/services/default.png' }}"
                                 class="card-img-top rounded-top"
                                 alt="{{ $service->name }}">
                            <div class="card-body d-flex flex-column">
                                <h6 class="fw-bold mb-1">{{ $service->name }}</h6>
                                <p class="small text-muted flex-grow-1">{{ Str::limit($service->description, 80) }}</p>
                                <p class="fw-bold text-orange fs-6">{{ number_format($service->price, 2, ',', ' ') }} ₽</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">{{ $services->links('pagination::bootstrap-5') }}</div>
        @else
            <p class="text-muted">Нет услуг по вашему запросу.</p>
        @endif
    </div>

    <script>
        document.querySelectorAll('.dz-clickable-card').forEach(card => {
            card.addEventListener('click', () => {
                window.location.href = card.dataset.url;
            });
        });
    </script>
@endsection
