<?php

namespace App\Http\Controllers;

use App\Models\GastoFijo;
use Illuminate\Http\Request;

class GastoFijoController extends Controller
{
    public function create()
    {
        return view('gastos_fijos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'dia_limite' => 'required|integer|min:1|max:31',
        ]);

        GastoFijo::create([
            'nombre' => $request->nombre,
            'monto' => $request->monto,
            'dia_limite' => $request->dia_limite,
            'estado' => 'activo', // Por defecto lo marcamos activo al crearlo
        ]);

        // Redirigimos al planeador para que veas el impacto de inmediato
        return redirect()->route('proyecciones.index');
    }
}