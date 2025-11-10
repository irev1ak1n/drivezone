<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\Vehicle;

class VehicleController extends Controller
{
    // POST /vehicles/add
    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1990|max:' . date('Y'),
            'engine' => 'nullable|string|max:50',
            'plate' => 'nullable|string|max:20'
        ]);

        Vehicle::create([
            'user_id' => auth()->id(),
            'brand'   => $request->brand,
            'model'   => $request->model,
            'year'    => $request->year,
            'engine'  => $request->engine,
            'plate'   => $request->plate,
        ]);

        return response()->json(['success' => true, 'message' => 'Автомобиль добавлен!']);
    }
}
