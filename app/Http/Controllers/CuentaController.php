<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Deuda;
use App\Models\Cuenta;

class CuentaController extends Controller
{
    public function index()
    {
        // Usamos 'with' para precargar los movimientos y evitar consultas N+1
        $cuentasActivas = Cuenta::with('movimientos')->whereIn('tipo', ['efectivo', 'debito'])->get()->map(function($cuenta) {
            $ingresos = $cuenta->movimientos->whereIn('tipo', ['ingreso', 'Ingreso'])->sum('monto');
            $gastos = $cuenta->movimientos->whereIn('tipo', ['gasto', 'Gasto'])->sum('monto');
            $cuenta->saldo = $ingresos - $gastos;
            return $cuenta;
        });

        $cuentasCredito = Cuenta::with('movimientos')->where('tipo', 'credito')->get()->map(function($cuenta) {
            $gastos = $cuenta->movimientos->whereIn('tipo', ['gasto', 'Gasto'])->sum('monto');
            $pagos = $cuenta->movimientos->whereIn('tipo', ['ingreso', 'Ingreso'])->sum('monto');
            $cuenta->saldo_usado = $gastos - $pagos;
            // Protegemos el cálculo por si el límite es null
            $limite = $cuenta->limite_credito ?? 0;
            $cuenta->disponible = $limite - $cuenta->saldo_usado;
            return $cuenta;
        });

        $deudasActivas = Deuda::where('saldo_restante', '>', 0)->get();
        $deudasPagadas = Deuda::where('saldo_restante', '<=', 0)->get();

        return view('cuentas.index', compact('cuentasActivas', 'cuentasCredito', 'deudasActivas', 'deudasPagadas'));
    }

    public function create()
    {
        return view('cuentas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:efectivo,debito,credito',
            'limite_credito' => 'nullable|numeric|min:0|required_if:tipo,credito',
            'dia_corte' => 'nullable|integer|min:1|max:31|required_if:tipo,credito',
            'dia_pago' => 'nullable|integer|min:1|max:31|required_if:tipo,credito',
        ]);

        Cuenta::create([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'limite_credito' => $request->tipo === 'credito' ? $request->limite_credito : null,
            'dia_corte' => $request->tipo === 'credito' ? $request->dia_corte : null,
            'dia_pago' => $request->tipo === 'credito' ? $request->dia_pago : null,
        ]);

        return redirect()->route('cuentas.index');
    }

    public function edit(Cuenta $cuenta)
    {
        return view('cuentas.edit', compact('cuenta'));
    }

    public function update(Request $request, Cuenta $cuenta)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:efectivo,debito,credito',
            'limite_credito' => 'nullable|numeric|min:0|required_if:tipo,credito',
            'dia_corte' => 'nullable|integer|min:1|max:31|required_if:tipo,credito',
            'dia_pago' => 'nullable|integer|min:1|max:31|required_if:tipo,credito',
        ]);

        $cuenta->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'limite_credito' => $request->tipo === 'credito' ? $request->limite_credito : null,
            'dia_corte' => $request->tipo === 'credito' ? $request->dia_corte : null,
            'dia_pago' => $request->tipo === 'credito' ? $request->dia_pago : null,
        ]);

        return redirect()->route('cuentas.index');
    }
}