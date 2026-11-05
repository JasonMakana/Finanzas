<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Movimiento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('movimientos.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Tipo de Movimiento</label>
                            <select name="tipo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                                <option value="gasto">Gasto</option>
                                <option value="ingreso">Ingreso</option>
                            </select>
                        </div>

                        <!-- Monto -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Monto ($)</label>
                            <input type="number" step="0.01" name="monto" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                            <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Fecha</label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                        </div>

                        <!-- Método de Pago -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Método de Pago</label>
                            <select name="cuenta_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Selecciona una cuenta --</option>
                                @foreach($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}" {{ (isset($movimiento) && $movimiento->cuenta_id == $cuenta->id) ? 'selected' : '' }}>
                                        {{ $cuenta->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Categoría -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Categoría</label>
                            <select name="categoria_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('categorias.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900 mt-1 inline-block">+ Crear nueva categoría</a>
                        </div>

                        <!-- Deuda (Oculto por defecto) -->
                        <div id="div_deuda" style="display: none;">
                            <label class="block font-medium text-sm text-gray-700">Asociar a Deuda</label>
                            <select name="deuda_id" id="deuda_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                                <option value="">-- Selecciona la deuda --</option>
                                @foreach($deudas as $deuda)
                                    <option value="{{ $deuda->id }}">{{ $deuda->nombre }} (Resta: ${{ number_format($deuda->saldo_restante, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">Descripción</label>
                            <input type="text" name="descripcion" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 mt-6">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Regresar al Dashboard
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Guardar Movimiento
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriaSelect = document.querySelector('select[name="categoria_id"]');
            const deudaDiv = document.getElementById('div_deuda');
            const deudaSelect = document.getElementById('deuda_id');

            categoriaSelect.addEventListener('change', function() {
                const selectedText = this.options[this.selectedIndex].text;
                
                if (selectedText === 'Deudas') {
                    deudaDiv.style.display = 'block';
                    deudaSelect.setAttribute('required', 'required');
                } else {
                    deudaDiv.style.display = 'none';
                    deudaSelect.removeAttribute('required');
                    deudaSelect.value = ''; // Limpiamos el valor por si se arrepiente
                }
            });

            // Ejecutar al cargar la página
            categoriaSelect.dispatchEvent(new Event('change'));
        });
    </script>

</x-app-layout>