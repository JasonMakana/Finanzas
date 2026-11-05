<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Categoria;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferenciaController extends Controller
{
    public function create()
    {
        $cuentas = Cuenta::all();
        return view('transferencias.create', compact('cuentas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'origen_id' => 'required|exists:cuentas,id',
            'destino_id' => 'required|exists:cuentas,id|different:origen_id',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $cuentaOrigen = Cuenta::find($request->origen_id);
        $cuentaDestino = Cuenta::find($request->destino_id);

        $categoriaSalida = Categoria::firstOrCreate(['nombre' => 'Transferencia (Salida)', 'tipo' => 'gasto']);
        $categoriaEntrada = Categoria::firstOrCreate(['nombre' => 'Transferencia (Entrada)', 'tipo' => 'ingreso']);

        $textoDescripcion = $request->descripcion ? $request->descripcion : 'Depósito';
        $transferenciaId = Str::uuid();

        DB::transaction(function () use ($request, $cuentaOrigen, $cuentaDestino, $categoriaSalida, $categoriaEntrada, $textoDescripcion, $transferenciaId) {
            
            // Salida de dinero
            Movimiento::create([
                'tipo' => 'gasto',
                'monto' => $request->monto,
                'fecha' => $request->fecha,
                'descripcion' => $textoDescripcion . ' (Hacia ' . $cuentaDestino->nombre . ')',
                'cuenta_id' => $request->origen_id,
                'categoria_id' => $categoriaSalida->id,
                'transferencia_id' => $transferenciaId,
            ]);

            // Entrada de dinero
            Movimiento::create([
                'tipo' => 'ingreso',
                'monto' => $request->monto,
                'fecha' => $request->fecha,
                'descripcion' => $textoDescripcion . ' (Desde ' . $cuentaOrigen->nombre . ')',
                'cuenta_id' => $request->destino_id,
                'categoria_id' => $categoriaEntrada->id,
                'transferencia_id' => $transferenciaId,
            ]);
        });

        return redirect()->route('cuentas.index');
    }
}