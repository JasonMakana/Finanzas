<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Movimiento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($esComplejo)
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Nota de seguridad:</strong> Este movimiento es parte de una transferencia o abono a deuda. El método de pago está bloqueado por seguridad contable. Si te equivocaste de cuenta origen/destino, elimina el registro y créalo de nuevo.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('movimientos.update', $movimiento) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Monto -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Monto ($)</label>
                            <input type="number" step="0.01" name="monto" value="{{ $movimiento->monto }}" 
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Fecha</label>
                            <input type="date" name="fecha" value="{{ $movimiento->fecha }}" max="{{ date('Y-m-d') }}" 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        </div>

                        <!-- Método de Pago -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Método de Pago</label>
                            @if($esComplejo)
                                <input type="text" name="cuenta_id" value="{{ $movimiento->cuenta->nombre }}" class="mt-1 block w-full rounded-md shadow-sm bg-gray-100 border-gray-300 text-gray-500 cursor-not-allowed" readonly>
                            @else
                                <select name="cuenta_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Selecciona una cuenta --</option>
                                @foreach($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}" {{ (isset($movimiento) && $movimiento->cuenta_id == $cuenta->id) ? 'selected' : '' }}>
                                        {{ $cuenta->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @endif
                        </div>

                        <!-- Categoría -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Categoría</label>
                            <select name="categoria_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ $movimiento->categoria_id == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }} ({{ ucfirst($categoria->tipo) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700">Descripción</label>
                        <input type="text" name="descripcion" value="{{ $movimiento->descripcion }}" 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    </div>

                    <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                        <!-- Botón Eliminar (Formulario independiente) -->
                        <button type="button" onclick="if(confirm('¿Estás seguro de eliminar este registro? Esto revertirá los saldos.')) document.getElementById('form-delete').submit();" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none transition">
                            Eliminar Registro
                        </button>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Formulario oculto para eliminar -->
                <form id="form-delete" action="{{ route('movimientos.destroy', $movimiento) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>
    </div>
</x-app-layout>