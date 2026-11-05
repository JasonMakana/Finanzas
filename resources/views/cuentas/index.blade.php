<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Desglose de Cuentas y Deudas
            </h2>
            <div class="space-x-2">
                <a href="{{ route('cuentas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    + Nueva Cuenta
                </a>
                <a href="{{ route('deudas.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">
                    + Nueva Deuda
                </a>
                <a href="{{ route('gastos-fijos.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 transition">
                    + Nuevo Gasto Fijo
                </a>
                <a href="{{ route('transferencias.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    Mover Dinero / Pagar TDC
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- SECCIÓN 1: ACTIVOS (Liquidez) -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mis Cuentas (Activos)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($cuentasActivas as $cuenta)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $cuenta->tipo === 'efectivo' ? 'border-green-500' : 'border-blue-500' }}">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $cuenta->nombre }}
                            <a href="{{ route('cuentas.edit', $cuenta) }}" class="text-xs text-blue-500 hover:underline ml-2">Editar</a>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">
                            ${{ number_format($cuenta->saldo, 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- SECCIÓN 2: PASIVOS (Deudas) -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mis Obligaciones (Pasivos)</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Tarjetas de Crédito Dinámicas -->
                    @foreach($cuentasCredito as $tarjeta)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $tarjeta->nombre }}</div>
                        <div class="mt-2 text-3xl font-bold text-red-600">
                            ${{ number_format($tarjeta->saldo_usado, 2) }}
                        </div>
                        
                        <!-- Reemplaza el div de "Saldo consumido" por este bloque -->
                        <div class="mt-1 flex flex-col gap-2">
                            <span class="text-sm text-gray-500 font-semibold">Saldo consumido</span>
                            <div class="flex justify-between text-[11px] font-bold bg-gray-50 p-2 rounded border border-gray-100">
                                <span class="text-blue-600">Día de Corte: {{ $tarjeta->dia_corte }}</span>
                                <span class="text-red-500">Día de Pago: {{ $tarjeta->dia_pago }}</span>
                            </div>
                        </div>
                        <div class="mt-4 border-t pt-4 flex justify-between text-sm">
                            <span class="text-gray-600">Límite: ${{ number_format($tarjeta->limite_credito, 2) }}</span>
                            <span class="text-green-600 font-bold">Disponible: ${{ number_format($tarjeta->disponible, 2) }}</span>
                        </div>
                    </div>
                    @endforeach

                    <!-- Deudas Activas -->
                    @foreach($deudasActivas as $deuda)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500 flex flex-col justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $deuda->nombre }}</div>
                            <div class="mt-2 text-3xl font-bold text-purple-600">
                                ${{ number_format($deuda->saldo_restante, 2) }}
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                Inicial: ${{ number_format($deuda->monto_inicial, 2) }}
                            </div>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <span class="text-sm font-semibold text-gray-700">Cuota sugerida: </span>
                            <span class="text-sm text-gray-600">${{ number_format($deuda->monto_sugerido, 2) }}</span>
                            
                            <div class="mt-2 text-sm text-red-500 font-semibold">
                                Día límite de pago: {{ $deuda->dia_pago ?? 'No definido' }} de cada mes
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Barra Desplegable de Deudas Pagadas -->
                <div class="mt-8">
                    <details class="group bg-white shadow-sm sm:rounded-lg overflow-hidden cursor-pointer border border-gray-200">
                        
                        <!-- La Barra (Siempre visible) -->
                        <summary class="p-4 bg-gray-50 font-bold text-gray-600 flex justify-between items-center list-none hover:bg-gray-100 transition">
                            <span class="uppercase tracking-wider text-sm">
                                Historial: Deudas Pagadas ({{ $deudasPagadas->count() }})
                            </span>
                            <!-- Ícono de flecha que gira al abrir -->
                            <span class="transition-transform duration-300 group-open:rotate-180">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </span>
                        </summary>
                        
                        <!-- El Contenido Oculto (Se muestra al dar clic en la barra) -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-white cursor-default">
                            @forelse($deudasPagadas as $deuda)
                            <div class="bg-gray-50 overflow-hidden sm:rounded-lg p-6 border-l-4 border-gray-300 opacity-75">
                                <div>
                                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider line-through">{{ $deuda->nombre }}</div>
                                    <div class="mt-2 text-3xl font-bold text-gray-400">
                                        ${{ number_format($deuda->saldo_restante, 2) }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-400">
                                        Inicial: ${{ number_format($deuda->monto_inicial, 2) }}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full text-center text-sm text-gray-500 py-4">
                                Aún no tienes deudas liquidadas.
                            </div>
                            @endforelse
                        </div>
                        
                    </details>
                </div>
                
            </div>

        </div>
    </div>
</x-app-layout>