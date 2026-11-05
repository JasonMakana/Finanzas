<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Gasto Fijo / Suscripción
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                
                <form action="{{ route('gastos-fijos.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-700 mb-2">Concepto del Servicio (Ej. Netflix, Internet, Gimnasio)</label>
                        <input type="text" name="nombre" class="border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm block w-full" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Costo Mensual Exacto</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" name="monto" class="pl-7 block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" placeholder="0.00" required>
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Día de cobro en el mes</label>
                            <input type="number" name="dia_limite" min="1" max="31" class="border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm block w-full" placeholder="Ej. 15" required>
                            <span class="text-xs text-gray-500 block mt-1">Día exacto en que te hacen el cargo.</span>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 border-t pt-4">
                        <a href="{{ route('proyecciones.index') }}" class="mr-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:text-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase shadow-sm hover:bg-orange-700 transition">
                            Guardar Suscripción
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>