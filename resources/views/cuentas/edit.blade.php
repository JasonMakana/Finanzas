<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cuenta: {{ $cuenta->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                
                <form action="{{ route('cuentas.update', $cuenta) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Nombre de la Cuenta / Tarjeta</label>
                        <input type="text" name="nombre" value="{{ $cuenta->nombre }}" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Tipo de Cuenta</label>
                        <select id="tipo_cuenta" name="tipo" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" required>
                            <option value="efectivo" {{ $cuenta->tipo === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="debito" {{ $cuenta->tipo === 'debito' ? 'selected' : '' }}>Débito / Cuenta Bancaria</option>
                            <option value="credito" {{ $cuenta->tipo === 'credito' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                        </select>
                    </div>

                    <div id="panel_credito" class="{{ $cuenta->tipo === 'credito' ? '' : 'hidden' }} bg-gray-50 p-4 rounded-md border border-gray-200 mb-4 space-y-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b pb-2">Configuración de Crédito</h3>
                        
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Límite de Crédito Autorizado</label>
                            <input type="number" step="0.01" name="limite_credito" value="{{ $cuenta->limite_credito }}" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Día de Corte al mes</label>
                                <input type="number" name="dia_corte" value="{{ $cuenta->dia_corte }}" min="1" max="31" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full">
                            </div>
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Día Límite de Pago al mes</label>
                                <input type="number" name="dia_pago" value="{{ $cuenta->dia_pago }}" min="1" max="31" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('cuentas.index') }}" class="mr-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:text-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase shadow-sm hover:bg-blue-700 transition">
                            Actualizar Cuenta
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectorTipo = document.getElementById('tipo_cuenta');
            const panelCredito = document.getElementById('panel_credito');
            const inputsCredito = panelCredito.querySelectorAll('input');

            function togglePanel() {
                if (selectorTipo.value === 'credito') {
                    panelCredito.classList.remove('hidden');
                    inputsCredito.forEach(input => input.setAttribute('required', 'required'));
                } else {
                    panelCredito.classList.add('hidden');
                    inputsCredito.forEach(input => input.removeAttribute('required'));
                }
            }

            selectorTipo.addEventListener('change', togglePanel);
            togglePanel(); // Ejecutar al cargar para validar el estado actual
        });
    </script>
</x-app-layout>