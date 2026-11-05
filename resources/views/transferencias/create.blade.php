<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Transferencia entre Cuentas / Pago de TDC
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('transferencias.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                       <!-- Origen -->
                        <div class="bg-red-50 p-4 rounded-md border border-red-100">
                            <label class="block font-bold text-sm text-red-700 mb-2">Cuenta Origen (Sale el dinero)</label>
                            <select name="origen_id" class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm block w-full" required>
                                <option value="">-- Selecciona origen --</option>
                                @foreach($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Destino -->
                        <div class="bg-green-50 p-4 rounded-md border border-green-100">
                            <label class="block font-bold text-sm text-green-700 mb-2">Cuenta Destino (Entra el dinero)</label>
                            <select name="destino_id" class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm block w-full" required>
                                <option value="">-- Selecciona destino --</option>
                                @foreach($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }}</option>
                                @endforeach
                            </select>
                            @error('destino_id')
                                <span class="text-sm text-red-600 mt-1 block">El destino no puede ser el mismo que el origen.</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Monto -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Monto a transferir ($)</label>
                            <input type="number" step="0.01" name="monto" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Fecha</label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" required>
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">Concepto o Descripción (Opcional)</label>
                            <input type="text" name="descripcion" value="Depósito" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 mt-6">
                        <a href="{{ route('cuentas.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                            Regresar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                            Procesar Transferencia
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>