<?php

namespace App\Http\Controllers;

use App\Models\Deuda;
use Illuminate\Http\Request;

class DeudaController extends Controller
{
    public function create()
    {
        return view('deudas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'monto_inicial' => 'required|numeric|min:0.01',
            'dia_pago' => 'nullable|integer|min:1|max:31',
            'fecha_limite' => 'nullable|date|after:today',
            'monto_sugerido' => 'required|numeric|min:0',
        ]);

        Deuda::create([
            'nombre' => $request->nombre,
            'monto_inicial' => $request->monto_inicial,
            'saldo_restante' => $request->monto_inicial,
            'monto_sugerido' => $request->monto_sugerido,
            'dia_pago' => $request->dia_pago,
            'fecha_limite' => $request->fecha_limite,
        ]);

        return redirect()->route('cuentas.index');
    }

    public function ocultar(Deuda $deuda)
    {
        $deuda->update(['oculta' => 1]);
        return back();
    }

    public function mostrar(Deuda $deuda)
    {
        $deuda->update(['oculta' => 0]);
        return back();
    }
}