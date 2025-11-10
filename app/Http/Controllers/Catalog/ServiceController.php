<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog\Service;
use App\Http\Middleware\RoleMiddleware;

// Управляет услугами автосервиса:
// - предоставляет доступ к списку услуг,
// — детальной информации об услуге,
// — а также позволяет создавать, редактировать и удалять их.
// Нужен для заказа и администрирования сервисных услуг.
class ServiceController extends Controller
{
    /**
     * Список всех услуг
     */
    public function index(Request $request)
    {

        // Берём только доступные услуги
//        $services = \App\Models\Catalog\Service::where('status', 'Услуга доступна')->get();
        // Берём все услуги (включая доступные и недоступные)
        $services = \App\Models\Catalog\Service::all();

        // Если запрос API (например, Postman)
        if ($request->expectsJson()) {
            return response()->json($services);
        }

        // Если запрос из браузера — возвращаем Blade-шаблон каталога
        // И теперь мы также можем передавать товары, если нужно
        $products = \App\Models\Catalog\Product::with('brand')->get();

        return view('catalog.index', compact('products', 'services'));
    }

    public function category(string $category, Request $request)
    {
        // Берём все услуги выбранной категории, независимо от статуса
        $services = Service::where('category', $category)
            ->orderBy('name')
            ->get();

        // Если JSON-запрос (Postman)
        if ($request->expectsJson()) {
            return response()->json($services);
        }

        // Возвращаем шаблон services-categories.blade.php — он будет отображать услуги одной категории
        return view('catalog.services-categories', compact('category', 'services'));

    }

    /**
     * Просмотр одной услуги
     */
    public function showDetail(string $id)
    {
        $service = \App\Models\Catalog\Service::findOrFail($id);
        return view('catalog.service-detail', compact('service'));
    }


    /**
     * Страница "Все услуги"
     */
    public function showAll(Request $request)
    {
        // Получаем все доступные услуги
//        $services = \App\Models\Catalog\Service::where('status', 'Услуга доступна')
//            ->orderBy('category')
//            ->paginate(12); // пагинация по 12 карточек

        // Получаем все услуги (включая доступные и недоступные)
        $services = \App\Models\Catalog\Service::orderBy('category')
            ->paginate(12); // пагинация по 12 карточек на страницу


        // Если JSON-запрос (например, через API)
        if ($request->expectsJson()) {
            return response()->json($services);
        }

        // Возвращаем Blade-шаблон
        return view('catalog.all-services', compact('services'));
    }

    /**
     * Создание новой услуги (для админа/сотрудника)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:10',
            'category'    => 'nullable|string|max:255',
            'status'      => 'required|in:Услуга доступна,Не доступна',
        ]);

        $service = Service::create($request->all());

        return response()->json([
            'message' => 'Услуга создана',
            'service' => $service
        ], 201);
    } // store

    /**
     * Обновление услуги
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name'        => 'string|max:255',
            'description' => 'nullable|string|max:500',
            'price'       => 'numeric|min:0',
            'duration_minutes' => 'integer|min:10',
            'category'    => 'nullable|string|max:255',
            'status'      => 'in:Услуга доступна,Не доступна',
        ]);

        $service->update($request->all());

        return response()->json([
            'message' => 'Услуга обновлена',
            'service' => $service
        ]);
    } // update

    /**
     * Удаление услуги
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Услуга удалена']);
    } // destroy

    public function filter(Request $request)
    {
        $query = \App\Models\Catalog\Service::query();

        // === Маппинг машинных значений в человеко-читаемые (как в БД) ===
        $map = [
            'engine'       => 'Ремонт двигателя',
            'brakes'       => 'Тормозная система',
            'suspension'   => 'Ходовая часть',
            'diagnostics'  => 'Диагностика',
            'bodywork'     => 'Кузовные работы',
            'climate'      => 'Климат и кондиционирование',
            'electric'     => 'Электрика и освещение',
            'maintenance'  => 'Техническое обслуживание',
            'other'        => 'Прочее',
        ];

        // === Категория (важно: защита от пустых строк) ===
        if ($request->filled('category') && trim($request->category) !== '') {
            $category = strtolower(trim($request->category));
            $human = $map[$category] ?? $category;

            $query->whereRaw('LOWER(TRIM(category)) LIKE ?', ['%' . strtolower(trim($human)) . '%']);
        }


        // === Статус (также с защитой от пустых значений) ===
        if ($request->filled('status') && trim($request->status) !== '') {
            $query->whereRaw('LOWER(TRIM(status)) = ?', [strtolower(trim($request->status))]);
        }

        // === Диапазон цен ===
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // === Сортировка ===
        if ($request->filled('sort') && $request->sort !== 'default') {
            switch ($request->sort) {
                case 'price_asc':  $query->orderBy('price', 'asc'); break;
                case 'price_desc': $query->orderBy('price', 'desc'); break;
                case 'name_asc':   $query->orderBy('name', 'asc');  break;
                case 'name_desc':  $query->orderBy('name', 'desc'); break;
                default:           $query->latest();
            }
        } else {
            $query->latest();
        }

        // === Пагинация ===
        $services = $query->paginate(12)->appends($request->except('page'));

        // === Ответ JSON ===
        return response()->json([
            'html' => view('partials.services-grid', compact('services'))->render(),
        ]);
    }



} // ServiceController
