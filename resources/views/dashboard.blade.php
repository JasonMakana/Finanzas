<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Resumen Financiero') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            
                <!-- 1. Resumen Global (Activos y Pasivos) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Tarjeta 1: Efectivo / Liquidez -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500 flex flex-col justify-center">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Liquidez</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">
                            ${{ number_format($efectivoDisponible, 2) }}
                        </div>
                        <div class="mt-1 text-sm text-gray-600">Dinero libre para usar</div>
                    </div>

                    <!-- Tarjeta 2: Deudas Externas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500 flex flex-col justify-center">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Otras Deudas (Liquidar)</div>
                        <div class="mt-2 text-3xl font-bold text-purple-600">
                            ${{ number_format($deudaExternaTotal, 2) }}
                        </div>
                        <div class="mt-1 text-sm text-gray-600">Total de pasivos fijos activos</div>
                    </div>
                </div>

                <!-- 2. Tarjetas de Crédito Dinámicas -->
                @foreach($tarjetasCreditoResumen as $tarjeta)
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 shadow-sm">
                            
                            <!-- Cabecera de la TDC con Fechas y Límite -->
                            <div class="mb-4 flex flex-col xl:flex-row xl:items-center justify-between border-b pb-3 gap-2">
                                <div class="text-sm font-bold text-gray-800 uppercase tracking-wider">
                                    {{ $tarjeta->nombre }}
                                </div>
                                <div class="text-[11px] text-gray-600 font-semibold flex flex-wrap gap-3">
                                    <span class="bg-gray-200 px-2 py-1 rounded">Límite: <strong class="text-gray-800">${{ number_format($tarjeta->limite, 2) }}</strong></span>
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Corte: Día {{ $tarjeta->dia_corte }}</span>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Pago: Día {{ $tarjeta->dia_pago }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Bolsa A -->
                                <div class="bg-gray-800 text-white rounded-lg p-4 shadow-sm flex flex-col justify-center relative">
                                    <h3 class="text-[10px] md:text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">Último corte</h3>
                                    <p class="text-xl md:text-2xl font-black text-red-400">${{ number_format($tarjeta->corte_actual, 2) }}</p>
                                </div>
                                <!-- Bolsa B -->
                                <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-yellow-500 flex flex-col justify-center relative">
                                    <h3 class="text-[10px] md:text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Mes en Curso</h3>
                                    <p class="text-xl md:text-2xl font-black text-yellow-600">${{ number_format($tarjeta->proximo_corte, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

            <!-- Panel de Movimientos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Encabezado del panel con el botón -->
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Últimos Movimientos</h3>
                    <a href="{{ route('movimientos.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Registrar Movimiento
                    </a>
                </div>
                
                <!-- Contenedor de la futura tabla -->
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ultimosMovimientos as $movimiento)
                                <tr onclick="window.location='{{ route('movimientos.edit', $movimiento) }}';" class="cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($movimiento->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $movimiento->descripcion }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movimiento->categoria->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movimiento->cuenta->nombre ?? 'Sin cuenta' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $movimiento->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movimiento->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($movimiento->monto, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No hay movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>