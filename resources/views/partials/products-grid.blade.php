<div id="productsContainer" class="row g-4">
    @foreach($products as $product)
        <div class="col-md-4 dz-product-card">
            <div class="card h-100 shadow-sm border-0 dz-clickable-card"
                 data-url="{{ route('products.show', $product->id) }}">
                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/300x200' }}"
                     class="card-img-top rounded-top"
                     alt="{{ $product->name }}">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                    <p class="text-muted mb-1">{{ $product->brand->name ?? 'Без бренда' }}</p>
                    <p class="small text-secondary flex-grow-1">{{ $product->product_description }}</p>
                    <p class="fw-bold text-dark fs-5 mb-3">
                        {{ number_format($product->price, 2, ',', ' ') }} ₽
                    </p>
                    @auth
                        <form method="POST" action="{{ url('/cart/add/'.$product->id.'/product') }}">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-orange w-100 fw-bold">Добавить в корзину</button>
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

{{-- === Контейнер пагинации с id (очень важно!) === --}}
<div class="mt-4" id="catalogPagination">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
