<div id="servicesContainer" class="row g-4">
    @forelse($services as $service)
        <div class="col-md-3 dz-product-card">
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
    @empty
        <p class="text-muted text-center mt-5 w-100">
            По вашему запросу ничего не найдено.
        </p>
    @endforelse
</div>

<div class="mt-4" id="servicesPagination">
    {{ $services->links('pagination::bootstrap-5') }}
</div>
