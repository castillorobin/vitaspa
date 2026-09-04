<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Reportes de Citas y Recaudación') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- FILTROS Y BOTONES DE ACCIÓN -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <!-- Desde -->
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Fecha Inicial</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>

                    <!-- Hasta -->
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Fecha Final</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>

                    <!-- Atendido por -->
                    <div>
                        <label for="user_id" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Atendido Por</label>
                        <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">Todos los terapeutas</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones Filtrar y Exportar -->
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold rounded-md transition shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtrar
                        </button>

                        <a href="{{ route('reports.export', request()->query()) }}" 
                        class="inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md transition shadow-sm"
                        title="Descargar reporte en formato Excel (.xlsx)">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                            </svg>
                            Descargar Excel
                        </a>
                    </div>
                </form>
            </div>

            <!-- TARJETAS DE RESUMEN DEL PERIODO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-emerald-500">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Recaudación Neta (Completadas)</span>
                    <div class="text-3xl font-extrabold text-emerald-700 mt-1">
                        ${{ number_format($totalIncome, 2) }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Citas en Rango</span>
                    <div class="text-3xl font-extrabold text-gray-800 mt-1">
                        {{ $totalAppointments }}
                    </div>
                </div>
            </div>

            <!-- TABLA DE RESULTADOS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-left font-medium uppercase tracking-wider text-xs">
                            <tr>
                                <th class="px-6 py-3">Fecha y Hora</th>
                                <th class="px-6 py-3">Paciente</th>
                                <th class="px-6 py-3">Servicio</th>
                                <th class="px-6 py-3">Duración</th>
                                <th class="px-6 py-3">Atendió</th>
                                <th class="px-6 py-3">Precio</th>
                                <th class="px-6 py-3">Método Pago</th>
                                <th class="px-6 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($appointments as $appointment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $appointment->patient->name }}
                                        <div class="text-xs text-gray-500">{{ $appointment->patient->phone ?? 'Sin teléfono' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $appointment->service }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $appointment->duration_minutes }} min</td>
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $appointment->attendedBy->name }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">${{ number_format($appointment->price, 2) }}</td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-xs">{{ $appointment->payment_method }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appointment->status === 'Completada')
                                            <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-800 font-semibold">Completada</span>
                                        @elseif($appointment->status === 'Cancelada')
                                            <span class="px-2 py-1 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">Cancelada</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800 font-semibold">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                        No se encontraron registros en el rango de fechas seleccionado.
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