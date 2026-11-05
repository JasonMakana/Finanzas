<?php

namespace App\Http\Controllers;

use App\Models\Deuda;
use App\Models\Cuenta;
use App\Models\Proyeccion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProyeccionController extends Controller
{
    public function index()
    {
        $hoy = \Carbon\Carbon::now();
        $tdcs = \App\Models\Cuenta::where('tipo', 'credito')->get();
        $tdcData = [];

        // 1. Calcular fechas y bolsas por CADA tarjeta de crédito
        foreach ($tdcs as $tdc) {
            $diaCorte = $tdc->dia_corte ?? 17;
            $diaPago = $tdc->dia_pago ?? 7;

            if ($hoy->day > $diaCorte) {
                $ultimoCorte = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->endOfDay();
                $proximoPago = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->addMonth()->setDay($diaPago)->endOfDay();
                $proximoCorte = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->addMonth()->endOfDay();
            } else {
                $ultimoCorte = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->subMonth()->endOfDay();
                $proximoPago = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaPago)->endOfDay();
                $proximoCorte = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaCorte)->endOfDay();
            }
            
            $pagoBolsaB = $proximoCorte->copy()->addMonth()->setDay($diaPago)->endOfDay();

            $pagosTotales = $tdc->movimientos()->whereIn('tipo', ['ingreso', 'Ingreso'])->sum('monto');
            $gastosTotales = $tdc->movimientos()->whereIn('tipo', ['gasto', 'Gasto'])->sum('monto');
            $saldoTotal = max(0, $gastosTotales - $pagosTotales);

            $gastosHastaCorte = $tdc->movimientos()->whereIn('tipo', ['gasto', 'Gasto'])->where('fecha', '<=', $ultimoCorte)->sum('monto');
            $bolsaA = max(0, $gastosHastaCorte - $pagosTotales);
            $bolsaB = max(0, $saldoTotal - $bolsaA);

            $inicioSemanaActual = $hoy->copy()->startOfWeek();
            
            $semanasParaPagoA = $inicioSemanaActual->diffInWeeks($proximoPago->copy()->startOfWeek()) + 1;
            if ($semanasParaPagoA <= 0) $semanasParaPagoA = 1;

            $inicioAhorroBolsaB = $proximoPago->copy()->addWeek()->startOfWeek();
            if ($inicioSemanaActual > $inicioAhorroBolsaB) {
                $inicioAhorroBolsaB = $inicioSemanaActual->copy();
            }
            $semanasParaPagoB = $inicioAhorroBolsaB->diffInWeeks($pagoBolsaB->copy()->startOfWeek()) + 1;
            if ($semanasParaPagoB <= 0) $semanasParaPagoB = 1;

            $tdcData[] = [
                'nombre' => $tdc->nombre,
                'ultimoCorte' => $ultimoCorte,
                'proximoPago' => $proximoPago,
                'proximoCorte' => $proximoCorte,
                'pagoBolsaB' => $pagoBolsaB,
                'bolsaA' => $bolsaA,
                'bolsaB' => $bolsaB,
                'semanasParaPagoA' => $semanasParaPagoA,
                'semanasParaPagoB' => $semanasParaPagoB,
            ];
        }

        // 2. Deudas fijas externas (Se dividen parejo)
        $deudasActivas = \App\Models\Deuda::where('saldo_restante', '>', 0)->get();
        $deudasFijasTotales = $deudasActivas->sum('monto_sugerido');
        $retencionDeudaSemanal = $deudasFijasTotales / 4; 

        // NUEVO: Traer los Gastos Fijos (Servicios, Suscripciones)
        $gastosFijos = \App\Models\GastoFijo::all();

        // 3. Consolidar el impacto semanal en la cuadrícula
        $semanas = [];
        $inicioSemanaActual = $hoy->copy()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $lunes = $inicioSemanaActual->copy()->addWeeks($i);
            $domingo = $lunes->copy()->endOfWeek();

            $proyeccion = \App\Models\Proyeccion::where('fecha_inicio_semana', $lunes->format('Y-m-d'))->first();
            $ingreso = $proyeccion ? $proyeccion->ingreso_estimado : 0;

            // Retención de TDCs (Tu código actual foreach tdcData...)
            $retencionTDCTotal = 0;
            $esSemanaDePago = false;
            $esSemanaDeCorte = false;
            $esSemanaDePagoB = false;

            foreach ($tdcData as $data) {
                if ($data['proximoPago']->between($lunes, $domingo)) $esSemanaDePago = true;
                if ($data['proximoCorte']->between($lunes, $domingo)) $esSemanaDeCorte = true;
                if ($data['pagoBolsaB']->between($lunes, $domingo)) $esSemanaDePagoB = true;

                if ($lunes <= $data['proximoPago']) {
                    $retencionTDCTotal += $data['bolsaA'] / $data['semanasParaPagoA'];
                } elseif ($lunes <= $data['pagoBolsaB']) {
                    $retencionTDCTotal += $data['bolsaB'] / $data['semanasParaPagoB'];
                }
            }

            // NUEVO: Algoritmo para asignar el Gasto Fijo a su semana exacta
            $retencionGastosFijos = 0;
            foreach ($gastosFijos as $gasto) {
                // Revisamos los 7 días de esta semana para ver si alguno coincide con el dia_limite del gasto
                for ($d = 0; $d < 7; $d++) {
                    if ($lunes->copy()->addDays($d)->day == $gasto->dia_limite) {
                        $retencionGastosFijos += $gasto->monto;
                        break; // Ya lo cobramos en esta semana, saltamos al siguiente gasto
                    }
                }
            }

            $semanas[] = [
                'fecha_inicio' => $lunes->format('Y-m-d'),
                'etiqueta' => $lunes->format('d M') . ' - ' . $domingo->format('d M'),
                'ingreso' => $ingreso,
                'retencion_tdc' => $retencionTDCTotal,
                'retencion_deuda' => $retencionDeudaSemanal,
                'retencion_gastos' => $retencionGastosFijos, // <-- Nueva variable
                'libre' => $ingreso - $retencionTDCTotal - $retencionDeudaSemanal - $retencionGastosFijos,
                'es_pago' => $esSemanaDePago,
                'es_corte' => $esSemanaDeCorte,
                'es_pago_b' => $esSemanaDePagoB
            ];
        }

        // Retornas tu vista (Asegúrate de mandar también $gastosFijos si quieres hacerles una tabla abajo)
        return view('proyecciones.index', compact('tdcData', 'semanas', 'deudasActivas', 'deudasFijasTotales', 'gastosFijos'));
    }

    // Método para guardar tu ingreso semanal usando AJAX sin recargar la página
    public function updateIngreso(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'monto' => 'required|numeric|min:0'
        ]);

        Proyeccion::updateOrCreate(
            ['fecha_inicio_semana' => $request->fecha_inicio],
            ['ingreso_estimado' => $request->monto]
        );

        return response()->json(['success' => true]);
    }
}