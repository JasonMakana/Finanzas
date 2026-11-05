<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Planeador de Flujo de Efectivo (7 Semanas)
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Resumen Dinámico de Tarjetas de Crédito -->
            @foreach($tdcData as $tdc)
            <div class="mb-2 text-lg font-bold text-gray-800 border-b pb-1 mt-4">
                {{ $tdc['nombre'] }}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <div class="bg-gray-800 text-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm uppercase tracking-wider text-gray-400 font-bold mb-1">Último Corte (Bolsa A)</h3>
                    <p class="text-3xl font-black text-red-400">${{ number_format($tdc['bolsaA'], 2) }}</p>
                    <p class="text-xs text-gray-400 mt-2">Límite de pago: <strong>{{ $tdc['proximoPago']->format('d/M') }}</strong></p>
                </div>
                
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500 relative">
                    <h3 class="text-sm uppercase tracking-wider text-gray-500 font-bold mb-1">Mes en Curso (Bolsa B)</h3>
                    <p class="text-3xl font-black text-yellow-600">${{ number_format($tdc['bolsaB'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Límite de pago: <strong>{{ $tdc['pagoBolsaB']->format('d/M') }}</strong></p>
                    
                    <!-- Etiqueta pequeña de Nuevo Corte integrada aquí -->
                    <div class="absolute top-6 right-6">
                        <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-1 rounded shadow-sm border border-blue-200 uppercase">
                            Corte: {{ $tdc['proximoCorte']->format('d/M') }}
                        </span>
                    </div>
                </div>

            </div>
            @endforeach

<!-- Tabla de 7 Semanas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-900 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider w-1/5">Concepto</th>
                                @foreach($semanas as $index => $semana)
                                    <th class="px-4 py-4 text-center text-xs font-medium uppercase tracking-wider w-32 border-l border-gray-700 {{ $semana['es_pago'] ? 'bg-red-900' : '' }} {{ $semana['es_corte'] ? 'bg-yellow-600' : '' }} {{ $semana['es_pago_b'] ? 'bg-orange-800' : '' }}">
                                        Semana {{ $index + 1 }}
                                        <div class="text-[10px] text-gray-300 font-normal mt-1">{{ $semana['etiqueta'] }}</div>
                                        @if($semana['es_pago']) <div class="text-red-300 text-[10px] mt-1 font-bold">LÍMITE CORTE</div> @endif
                                        @if($semana['es_corte']) <div class="text-yellow-200 text-[10px] mt-1 font-bold">NUEVO CORTE</div> @endif
                                        @if($semana['es_pago_b']) <div class="text-orange-300 text-[10px] mt-1 font-bold">LÍMITE SIG. MES</div> @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Ingresos -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">Ingreso Estimado</td>
                                @foreach($semanas as $index => $semana)
                                    <td class="px-2 py-4 border-l text-center">
                                        <input type="number" step="0.01" 
                                            class="ingreso-input w-24 text-sm text-center border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white" 
                                            data-fecha="{{ $semana['fecha_inicio'] }}" 
                                            data-index="{{ $index }}"
                                            value="{{ $semana['ingreso'] == 0 ? '' : $semana['ingreso'] }}" 
                                            placeholder="0.00">
                                    </td>
                                @endforeach
                            </tr>
                            <!-- Retención Deudas -->
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600">Retención Deudas Fijas</td>
                                @foreach($semanas as $index => $semana)
                                    <td class="px-2 py-4 border-l text-center text-sm font-semibold text-red-500">
                                        -${{ number_format($semana['retencion_deuda'], 2) }}
                                    </td>
                                @endforeach
                            </tr>
                            <!-- Retención Gastos Fijos (Servicios) -->
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600">Servicios / Suscripciones</td>
                                @foreach($semanas as $index => $semana)
                                    <td class="px-2 py-4 border-l text-center text-sm font-semibold {{ $semana['retencion_gastos'] > 0 ? 'text-orange-500 bg-orange-50' : 'text-gray-400' }}">
                                        -${{ number_format($semana['retencion_gastos'], 2) }}
                                    </td>
                                @endforeach
                            </tr>
                            <!-- Retención TDC -->
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600">Retención TDC BBVA</td>
                                @foreach($semanas as $index => $semana)
                                    <td class="px-2 py-4 border-l text-center text-sm font-semibold {{ $semana['es_corte'] ? 'text-yellow-600 bg-yellow-50' : 'text-red-500' }}">
                                        -${{ number_format($semana['retencion_tdc'], 2) }}
                                    </td>
                                @endforeach
                            </tr>
                            <!-- Total Libre (Con Arrastre) -->
                            <tr class="bg-gray-900">
                                <td class="px-6 py-4 text-sm font-bold text-white tracking-wider">
                                    DINERO LIBRE
                                    <span class="block text-[10px] text-gray-400 font-normal mt-1">*Saldo negativo se arrastra</span>
                                </td>
                                @foreach($semanas as $index => $semana)
                                    <td class="px-2 py-4 border-l border-gray-700 text-center font-bold text-sm libre-{{ $index }}">
                                        <!-- Se llena con JS -->
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- NUEVO: Tabla de Desglose de Obligaciones -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-gray-800 mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Desglose de Deudas Activas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Concepto</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Saldo Restante</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Cuota Mensual</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-800 uppercase bg-gray-200">Impacto Semanal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($deudasActivas as $deuda)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $deuda->nombre }}</td>
                                <td class="px-6 py-4 text-gray-600">${{ number_format($deuda->saldo_restante, 2) }}</td>
                                <td class="px-6 py-4 text-red-500 font-medium">${{ number_format($deuda->monto_sugerido, 2) }}</td>
                                <td class="px-6 py-4 text-red-600 font-bold bg-red-50">${{ number_format($deuda->monto_sugerido / 4, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No tienes deudas activas registradas.</td>
                            </tr>
                            @endforelse
                            <tr class="bg-gray-900 text-white font-bold">
                                <td colspan="2" class="px-6 py-4 text-right">TOTALES:</td>
                                <td class="px-6 py-4">${{ number_format($deudasFijasTotales, 2) }} /mes</td>
                                <td class="px-6 py-4 text-red-400 bg-gray-800">${{ number_format($deudasFijasTotales / 4, 2) }} /sem</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Script para cálculos en tiempo real y guardado AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.ingreso-input');
            const dataSemanas = @json($semanas);

            // Función maestra que calcula todas las semanas en cadena
            function calcularFlujoCascada() {
                let arrastreNegativo = 0;

                dataSemanas.forEach((semana, idx) => {
                    const input = document.querySelector(`.ingreso-input[data-index="${idx}"]`);
                    const ingreso = parseFloat(input.value) || 0;
                    const retTDC = parseFloat(semana.retencion_tdc) || 0;
                    const retDeuda = parseFloat(semana.retencion_deuda) || 0;
                    const retGastos = parseFloat(semana.retencion_gastos) || 0; // <-- Leer nuevo valor
                    
                    // Se restan todos los pasivos y se suma el déficit arrastrado
                    let libre = ingreso - retTDC - retDeuda - retGastos + arrastreNegativo;
                    const celdaLibre = document.querySelector(`.libre-${idx}`);
                    
                    celdaLibre.textContent = '$' + libre.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    
                    if (libre < 0) {
                        celdaLibre.classList.remove('text-green-400');
                        celdaLibre.classList.add('text-red-500');
                        arrastreNegativo = libre; // Se arrastra el negativo a la próxima semana
                    } else {
                        celdaLibre.classList.remove('text-red-500');
                        celdaLibre.classList.add('text-green-400');
                        arrastreNegativo = 0; // Si sobra dinero, se asume como libre, no se pasa a pagar deudas futuras solas.
                    }
                });
            }

            // Ejecutar al cargar la página para pintar los saldos iniciales con arrastre
            calcularFlujoCascada();

            inputs.forEach(input => {
                input.addEventListener('input', calcularFlujoCascada);

                input.addEventListener('change', function() {
                    const monto = parseFloat(this.value) || 0;
                    const fecha = this.dataset.fecha;

                    fetch('{{ route('proyecciones.ingreso') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ fecha_inicio: fecha, monto: monto })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            this.classList.add('bg-green-50');
                            setTimeout(() => this.classList.remove('bg-green-50'), 1000);
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>