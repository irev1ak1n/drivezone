<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Brand;
use App\Models\Catalog\Service;
use Illuminate\Http\Request;
use App\Models\Catalog\Product;
use App\Http\Middleware\RoleMiddleware;

// Контролер для контроля над управлением товарами в системе:
// позволяет получать список товаров, просматривать один товар, а также добавлять, изменять и удалять их.
// Нужен, чтобы пользователь мог выбирать продукцию для покупки.
class ProductController extends Controller
{
    /**
     * Список всех товаров
     */
    public function index(Request $request)
    {
        // Подгружаем все товары с брендами
        $products = \App\Models\Catalog\Product::with('brand')->get();

        // Подгружаем услуги — только доступные
        $services = \App\Models\Catalog\Service::whereRaw("LOWER(TRIM(status)) = 'услуга доступна'")
            ->orWhereNull('status')
            ->get();

        // Если это API-запрос (например, Postman)
        if ($request->expectsJson()) {
            return response()->json([
                'products' => $products,
                'services' => $services,
            ]);
        }

        // Если запрос из браузера — отрисовываем Blade-шаблон
        return view('catalog.index', compact('products', 'services'));
    }


    /**
     * Просмотр одного товара
     */
    public function show(string $id)
    {
        $product = \App\Models\Catalog\Product::with('brand')->findOrFail($id);
        return view('catalog.product-detail', compact('product'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'product_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['brand_id', 'name', 'product_description', 'price']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = "/storage/$path";
        }

        $product = Product::create($data);

        return response()->json([
            'message' => 'Товар создан',
            'product' => $product
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'brand_id' => 'exists:brands,id',
            'name' => 'string|max:255',
            'product_description' => 'nullable|string|max:500',
            'price' => 'numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['brand_id', 'name', 'product_description', 'price']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = "/storage/$path";
        }

        $product->update($data);

        return response()->json([
            'message' => 'Товар обновлён',
            'product' => $product
        ]);
    }

    /**
     * Удаление товара
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Товар удалён']);
    } // destroy

    public function showAll(Request $request)
    {
        $products = Product::with('brand')->paginate(12);
        $brands = Brand::orderBy('name')->get();

        // пока категорий нет — просто пустой массив, чтобы шаблон не падал
        $categories = [];

        return view('catalog.all-products', compact('products', 'brands', 'categories'));
    }

    public function filter(Request $request)
    {
        $query = Product::query();

        if ($request->filled('categories')) {
            $query->whereIn('category', (array) $request->input('categories'));
        }

        if ($request->filled('brand') && $request->brand !== '') {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', $request->brand);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc': $query->orderBy('price', 'asc'); break;
                case 'price_desc': $query->orderBy('price', 'desc'); break;
                case 'name_asc': $query->orderBy('name', 'asc'); break;
                case 'name_desc': $query->orderBy('name', 'desc'); break;
                default: $query->latest();
            }
        } else {
            // по умолчанию сортировка — последние
            $query->latest();
        }

        // ограничиваем до 12 товаров на страницу
        $products = $query->paginate(12)->appends($request->except('page'));

        return response()->json([
            'html' => view('partials.products-grid', compact('products'))->render(),
        ]);
    }


    public function search(Request $request)
    {
        $query = trim($request->input('q'));

        if (!$query) {
            return redirect()->back()->with('error', 'Введите запрос для поиска.');
        }

        // === Поиск по товарам ===
        $products = Product::where('name', 'like', "%$query%")
            ->orWhere('product_description', 'like', "%$query%")
            ->paginate(12, ['*'], 'products_page');

        // === Поиск по услугам ===
        $services = Service::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->paginate(12, ['*'], 'services_page');

        return view('catalog.search', compact('query', 'products', 'services'));
    }


} // ProductController
