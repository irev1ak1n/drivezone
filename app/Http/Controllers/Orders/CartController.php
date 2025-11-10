<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders\Cart;

class CartController extends Controller
{
//    /**
//     * Показать содержимое корзины текущего пользователя.
//     */
//    public function index(Request $request)
//    {
//        if (!auth()->check()) {
//            // Если гость — показываем пустую корзину (без редиректа)
//            return view('cart.index', ['cartItems' => collect()]);
//        }
//
//        $cartItems = Cart::where('user_id', auth()->id())
//            ->with(['product' => fn($q) => $q->select('id', 'name', 'price', 'brand_id', 'image_url')])
//            ->get();
//
//        if ($request->expectsJson()) {
//            return response()->json($cartItems);
//        }
//
//        return view('cart.index', compact('cartItems'));
//    }
//
//    /**
//     * Добавить товар или услугу в корзину.
//     */
//    public function add(Request $request, $id, $type)
//    {
//        if (!auth()->check()) {
//            // Если запрос через браузер — редирект на логин
//            if (!$request->expectsJson()) {
//                return redirect('/login')->with('error', 'Пожалуйста, войдите, чтобы добавить товар в корзину.');
//            }
//            // Если через API (Postman)
//            return response()->json(['error' => 'Требуется авторизация'], 401);
//        }
//
//        $request->validate([
//            'quantity' => 'nullable|integer|min:1|max:100',
//        ]);
//
//        if (!in_array($type, ['product', 'service'], true)) {
//            return response()->json(['message' => 'Недопустимый тип'], 422);
//        }
//
//        Cart::create([
//            'user_id' => auth()->id(),
//            'subject_id' => $id,
//            'subject_type' => $type,
//            'quantity' => $request->input('quantity', 1),
//        ]);
//
//        return $request->expectsJson()
//            ? response()->json(['message' => 'Товар добавлен в корзину'])
//            : redirect()->back()->with('success', 'Товар добавлен в корзину!');
//    }

    /**
     * Показать содержимое корзины текущего пользователя.
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            // Если гость — показываем пустую корзину
            return view('cart.index', [
                'cartItems' => collect(),
                'totalQuantity' => 0,
            ]);
        }

        // Загружаем товары и услуги
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('subject')
            ->get();

        // Считаем общее количество единиц
        $totalQuantity = $cartItems->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'items' => $cartItems,
                'totalQuantity' => $totalQuantity,
            ]);
        }

        // Передаём totalQuantity во view
        return view('cart.index', compact('cartItems', 'totalQuantity'));
    }


    /**
     * Добавить товар или услугу в корзину.
     */
//    public function add(Request $request, $id, $type)
//    {
//        if (!auth()->check()) {
//            // Если запрос через браузер — редирект на логин
//            if (!$request->expectsJson()) {
//                return redirect('/login')->with('error', 'Пожалуйста, войдите, чтобы добавить элемент в корзину.');
//            }
//            // Если через API (Postman)
//            return response()->json(['error' => 'Требуется авторизация'], 401);
//        }
//
//        $request->validate([
//            'quantity' => 'nullable|integer|min:1|max:100',
//        ]);
//
//        // Карта допустимых типов
//        $map = [
//            'product' => \App\Models\Catalog\Product::class,
//            'service' => \App\Models\Catalog\Service::class,
//        ];
//
//        if (!isset($map[$type])) {
//            return response()->json(['message' => 'Недопустимый тип'], 422);
//        }
//
//        Cart::create([
//            'user_id'      => auth()->id(),
//            'subject_id'   => $id,
//            'subject_type' => $map[$type], // теперь сохраняется полное имя класса
//            'quantity'     => $request->input('quantity', 1),
//        ]);
//
//        return $request->expectsJson()
//            ? response()->json(['message' => 'Элемент добавлен в корзину'])
//            : redirect()->back()->with('success', 'Элемент добавлен в корзину!');
//    }

    /**
     * Добавить товар или услугу в корзину.
     */
    public function add(Request $request, $id, $type)
    {
        if (!auth()->check()) {
            if (!$request->expectsJson()) {
                return redirect('/login')->with('error', 'Пожалуйста, войдите, чтобы добавить товар в корзину.');
            }
            return response()->json(['error' => 'Требуется авторизация'], 401);
        }

        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        // Полный путь к модели
        $modelClass = match ($type) {
            'product' => \App\Models\Catalog\Product::class,
            'service' => \App\Models\Catalog\Service::class,
            default => null,
        };

        if (!$modelClass) {
            return response()->json(['message' => 'Недопустимый тип'], 422);
        }

        $quantity = $request->input('quantity', 1);

        // Проверяем, есть ли уже этот товар/услуга у пользователя
        $existingItem = \App\Models\Orders\Cart::where('user_id', auth()->id())
            ->where('subject_id', $id)
            ->where('subject_type', $modelClass)
            ->first();

        if ($existingItem) {
            // Если уже есть — увеличиваем количество
            $existingItem->increment('quantity', $quantity);
            $message = 'Количество обновлено в корзине.';
        } else {
            // Если нет — создаём новую запись
            \App\Models\Orders\Cart::create([
                'user_id' => auth()->id(),
                'subject_id' => $id,
                'subject_type' => $modelClass,
                'quantity' => $quantity,
            ]);
            $message = 'Добавлено в корзину.';
        }

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->back()->with('success', $message);
    }


    /**
     * Обновить элемент в корзине.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Неавторизованный доступ'], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'note' => 'nullable|string|max:255',
        ]);

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Элемент не найден'], 404);
        }

        // Обновляем количество и заметку
        $cartItem->update([
            'quantity' => $request->quantity,
            'note' => $request->note,
        ]);

        return response()->json(['message' => 'Элемент обновлён']);
    }

    /**
     * Удалить элемент из корзины.
     */
    public function remove(Request $request, $id)
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Неавторизованный доступ'], 401);
            }
            return redirect('/login')->with('error', 'Войдите, чтобы удалить товары.');
        }

        $cartItem = Cart::where('user_id', auth()->id())->where('id', $id)->first();

        if (!$cartItem) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Товар не найден'], 404);
            }
            return redirect()->back()->withErrors(['error' => 'Товар не найден в корзине.']);
        }

        $cartItem->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Товар удалён из корзины!');
    }

    /**
     * Оформить заказ (условная оплата).
     */
    public function checkout(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Пожалуйста, войдите, чтобы оформить заказ.');
        }

        $cartItems = Cart::where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста']);
        }

        Cart::where('user_id', auth()->id())->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Заказ оформлен и корзина очищена'])
            : redirect()->back()->with('success', 'Заказ оформлен!');
    }

    /**
     * Получить общее количество товаров в корзине текущего пользователя
     */
    public function count()
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }

        $totalCount = \App\Models\Orders\Cart::where('user_id', auth()->id())->sum('quantity');

        return response()->json(['count' => $totalCount]);
    }

}
