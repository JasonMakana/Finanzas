<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Nueva Cuenta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                
                <form action="{{ route('cuentas.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Nombre de la Cuenta / Tarjeta</label>
                        <input type="text" name="nombre" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" placeholder="Ej. Nu Bank, Efectivo Cartera, BBVA Débito" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Tipo de Cuenta</label>
                        <select id="tipo_cuenta" name="tipo" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" required>
                            <option value="" disabled selected>Selecciona un tipo...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="debito">Débito / Cuenta Bancaria</option>
                            <option value="credito">Tarjeta de Crédito</option>
                        </select>
                    </div>

                    <!-- Panel exclusivo para Tarjetas de Crédito (Oculto por defecto) -->
                    <div id="panel_credito" class="hidden bg-gray-50 p-4 rounded-md border border-gray-200 mb-4 space-y-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b pb-2">Configuración de Crédito</h3>
                        
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Límite de Crédito Autorizado</label>
                            <input type="number" step="0.01" name="limite_credito" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" placeholder="0.00">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Día de Corte al mes</label>
                                <input type="number" name="dia_corte" min="1" max="31" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" placeholder="Ej. 17">
                            </div>
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Día Límite de Pago al mes</label>
                                <input type="number" name="dia_pago" min="1" max="31" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" placeholder="Ej. 7">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('cuentas.index') }}" class="mr-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:text-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase shadow-sm hover:bg-gray-700 transition">
                            Guardar Cuenta
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

            selectorTipo.addEventListener('change', function() {
                if (this.value === 'credito') {
                    panelCredito.classList.remove('hidden');
                    inputsCredito.forEach(input => input.setAttribute('required', 'required'));
                } else {
                    panelCredito.classList.add('hidden');
                    inputsCredito.forEach(input => {
                        input.removeAttribute('required');
                        input.value = ''; // Limpiamos los campos si cambia de opinión
                    });
                }
            });
        });
    </script>
</x-app-layout>