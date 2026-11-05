<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuenta;
use App\Models\Deuda;
use App\Models\Movimiento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Cálculo de Liquidez (Efectivo + Débito)
        $ingresosLiquidos = Movimiento::where('tipo', 'ingreso')
            ->whereHas('cuenta', fn($q) => $q->whereIn('tipo', ['efectivo', 'debito']))
            ->sum('monto');
            
        $gastosLiquidos = Movimiento::where('tipo', 'gasto')
            ->whereHas('cuenta', fn($q) => $q->whereIn('tipo', ['efectivo', 'debito']))
            ->sum('monto');
            
        $efectivoDisponible = $ingresosLiquidos - $gastosLiquidos;

        // 2. Cálculo dinámico de todas las TDC
        $hoy = \Carbon\Carbon::now();
        $tdcs = Cuenta::with('movimientos')->where('tipo', 'credito')->get();
        
        $tarjetasCreditoResumen = [];

        foreach ($tdcs as $tdc) {
            $diaCorte = $tdc->dia_corte ?? 17;
            
            if ($hoy->day > $diaCorte) {
                $ultimoCorte = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->endOfDay();
            } else {
                $ultimoCorte = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->subMonth()->endOfDay();
            }

            $pagosTotales = $tdc->movimientos->whereIn('tipo', ['ingreso', 'Ingreso'])->sum('monto');
            $gastosTotales = $tdc->movimientos->whereIn('tipo', ['gasto', 'Gasto'])->sum('monto');
            $saldoTotal = max(0, $gastosTotales - $pagosTotales);

            $gastosHastaCorte = $tdc->movimientos
                ->whereIn('tipo', ['gasto', 'Gasto'])
                ->where('fecha', '<=', $ultimoCorte)
                ->sum('monto');
            
            $deudaCorteActual = max(0, $gastosHastaCorte - $pagosTotales);
            $deudaProximoCorte = max(0, $saldoTotal - $deudaCorteActual);

            $tarjetasCreditoResumen[] = (object)[
                'nombre' => $tdc->nombre,
                'corte_actual' => $deudaCorteActual,
                'proximo_corte' => $deudaProximoCorte,
                'limite' => $tdc->limite_credito ?? 0,
                'dia_corte' => $tdc->dia_corte ?? 17,
                'dia_pago' => $tdc->dia_pago ?? 7
            ];
        }

        // 3. Deudas Externas Fijas
        $deudaExternaTotal = Deuda::where('saldo_restante', '>', 0)->sum('saldo_restante');

        // 4. Últimos Movimientos
        $ultimosMovimientos = Movimiento::with(['categoria', 'cuenta'])
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'efectivoDisponible', 
            'deudaCorteActual', 
            'deudaProximoCorte', 
            'deudaExternaTotal',
            'ultimosMovimientos',
            'tarjetasCreditoResumen'
        ));
    }
}