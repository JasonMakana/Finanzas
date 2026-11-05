<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Nueva Deuda
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                
                <form action="{{ route('deudas.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Concepto / Nombre de la Deuda</label>
                        <input type="text" name="nombre" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm block w-full" placeholder="Ej. Préstamo Familiar, MSI Liverpool" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Monto Total a Pagar</label>
                        <input type="number" step="0.01" id="monto_inicial" name="monto_inicial" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm block w-full" placeholder="0.00" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Día de pago (Opcional)</label>
                            <input type="number" name="dia_pago" min="1" max="31" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm block w-full" placeholder="Ej. 15">
                        </div>
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Fecha límite de liquidación (Opcional)</label>
                            <input type="date" id="fecha_limite" name="fecha_limite" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm block w-full">
                        </div>
                    </div>

                    <!-- Panel de Cuotas -->
                    <div class="mb-6 bg-purple-50 p-4 rounded-md border border-purple-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-sm text-purple-800 mb-2">Cuota Sugerida (Mensual)</label>
                            <input type="number" step="0.01" id="monto_sugerido" name="monto_sugerido" class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm block w-full bg-white" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block font-bold text-sm text-purple-800 mb-2">Impacto Semanal (Aproximado)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="text" id="cuota_semanal" class="pl-7 block w-full border-gray-300 bg-gray-200 text-gray-700 rounded-md shadow-sm font-bold cursor-not-allowed" value="0.00" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <span id="calculo-ayuda" class="text-xs text-purple-600 font-semibold block mt-1"></span>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('cuentas.index') }}" class="mr-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:text-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase shadow-sm hover:bg-purple-700 transition">
                            Guardar Deuda
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputMonto = document.getElementById('monto_inicial');
            const inputFecha = document.getElementById('fecha_limite');
            const inputCuota = document.getElementById('monto_sugerido');
            const inputSemanal = document.getElementById('cuota_semanal');
            const textoAyuda = document.getElementById('calculo-ayuda');

            function calcularCuota() {
                const monto = parseFloat(inputMonto.value) || 0;
                const fechaLimiteStr = inputFecha.value;

                if (monto > 0 && fechaLimiteStr) {
                    // CORRECCIÓN: Separar manualmente para evitar el salto de zona horaria (UTC a Local)
                    const [year, month, day] = fechaLimiteStr.split('-');
                    const fechaLimite = new Date(year, month - 1, day);
                    const hoy = new Date();
                    
                    let mesesFaltantes = (fechaLimite.getFullYear() - hoy.getFullYear()) * 12;
                    mesesFaltantes -= hoy.getMonth();
                    mesesFaltantes += fechaLimite.getMonth();
                    
                    // Si el día del mes límite es anterior al día actual, descontamos un mes
                    if (fechaLimite.getDate() < hoy.getDate()) {
                        mesesFaltantes--;
                    }
                    
                    // Asegurar que al menos sea 1 mes para evitar división por cero
                    if (mesesFaltantes < 1) mesesFaltantes = 1;

                    const cuotaMensual = monto / mesesFaltantes;
                    const cuotaSemanal = cuotaMensual / 4;

                    inputCuota.value = cuotaMensual.toFixed(2);
                    inputSemanal.value = cuotaSemanal.toFixed(2);
                    textoAyuda.textContent = `Calculado automáticamente: $${monto} dividido en ${mesesFaltantes} meses.`;
                } else {
                    textoAyuda.textContent = '';
                }
            }

            // Si el usuario escribe la cuota mensual a mano, actualizamos la semanal
            inputCuota.addEventListener('input', function() {
                const manual = parseFloat(this.value) || 0;
                inputSemanal.value = (manual / 4).toFixed(2);
                textoAyuda.textContent = ''; // Limpiamos el texto si se edita manual
            });

            inputMonto.addEventListener('input', calcularCuota);
            inputFecha.addEventListener('change', calcularCuota);
        });
    </script>
</x-app-layout>