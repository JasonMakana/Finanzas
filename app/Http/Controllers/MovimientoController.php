<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Categoria;
use App\Models\Deuda;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function create()
    {
        $categorias = Categoria::all();
        $deudas = Deuda::where('saldo_restante', '>', 0)->get();
        $cuentas = Cuenta::all(); // <--- Agregar esto
        return view('movimientos.create', compact('categorias', 'deudas', 'cuentas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ingreso,gasto',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'required|string|max:255',
            'cuenta_id' => 'required|exists:cuentas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'deuda_id' => 'nullable|exists:deudas,id'
        ]);

        // Prevenir que se pague más de lo que se debe
        if ($request->filled('deuda_id') && $request->tipo === 'gasto') {
            $deuda = Deuda::find($request->deuda_id);
            if ($deuda && $request->monto > $deuda->saldo_restante) {
                return back()->withErrors([
                    'monto' => 'El monto ($' . $request->monto . ') supera el saldo de la deuda ($' . $deuda->saldo_restante . ').'
                ])->withInput();
            }
        }

        // 1. Guardar el movimiento
        Movimiento::create($request->all());

        // 2. Si el movimiento está asociado a una deuda, descontar el saldo
        if ($request->filled('deuda_id') && $request->tipo === 'gasto') {
            $deuda = Deuda::find($request->deuda_id);
            
            if ($deuda) {
                $deuda->saldo_restante -= $request->monto;
                
                // Evitar saldos negativos si pagas de más
                if ($deuda->saldo_restante < 0) {
                    $deuda->saldo_restante = 0;
                }
                
                $deuda->save();
            }
        }

        return redirect()->route('dashboard');
    }

    public function edit(Movimiento $movimiento)
    {
        $categorias = Categoria::all();
        $cuentas = Cuenta::all(); // <--- Agregar esto
        $esComplejo = $movimiento->deuda_id !== null || str_contains($movimiento->categoria->nombre, 'Transferencia');
        return view('movimientos.edit', compact('movimiento', 'categorias', 'cuentas', 'esComplejo'));
    }

    public function update(Request $request, Movimiento $movimiento)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'required|string|max:255',
            'cuenta_id' => 'required|exists:cuentas,id',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        DB::transaction(function () use ($request, $movimiento) {
            // Caso A: Es un abono a deuda
            if ($movimiento->deuda_id && $movimiento->tipo === 'gasto') {
                $deuda = Deuda::find($movimiento->deuda_id);
                if ($deuda) {
                    // Devolver el dinero viejo a la deuda
                    $deuda->saldo_restante += $movimiento->monto;
                    // Restar el nuevo monto
                    $deuda->saldo_restante -= $request->monto;
                    if ($deuda->saldo_restante < 0) $deuda->saldo_restante = 0;
                    $deuda->save();
                }
            }

            // Caso B: Es una transferencia vinculada
            if ($movimiento->transferencia_id) {
                // Encontrar el registro espejo y actualizar su monto y fecha también
                Movimiento::where('transferencia_id', $movimiento->transferencia_id)
                          ->where('id', '!=', $movimiento->id)
                          ->update(['monto' => $request->monto, 'fecha' => $request->fecha]);
            }

            // Actualizar el registro actual
            $movimiento->update($request->all());
        });

        return redirect()->route('dashboard');
    }

    public function destroy(Movimiento $movimiento)
    {
        DB::transaction(function () use ($movimiento) {
            // Caso A: Revertir pago a deuda
            if ($movimiento->deuda_id && $movimiento->tipo === 'gasto') {
                $deuda = Deuda::find($movimiento->deuda_id);
                if ($deuda) {
                    $deuda->saldo_restante += $movimiento->monto; // Regresar el saldo
                    $deuda->save();
                }
            }

            // Caso B: Borrar transferencia vinculada
            if ($movimiento->transferencia_id) {
                Movimiento::where('transferencia_id', $movimiento->transferencia_id)->delete();
            }

            // Borrar el registro actual
            $movimiento->delete();
        });

        return redirect()->route('dashboard');
    }
    
}