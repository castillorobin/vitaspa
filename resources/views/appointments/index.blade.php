<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control de Citas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtros + Botón Nueva Cita -->
            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col md:flex-row gap-3 justify-between items-center">
                <form method="GET" action="{{ route('appointments.index') }}" class="w-full md:w-auto flex flex-wrap gap-2 items-center">
                    <input type="date" name="date" value="{{ $date }}" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">

                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Todos los Estados</option>
                        <option value="Pendiente" {{ $status === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Completada" {{ $status === 'Completada' ? 'selected' : '' }}>Completada</option>
                        <option value="Cancelada" {{ $status === 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
                        Filtrar
                    </button>

                    @if($date || $status)
                        <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm transition">
                            Limpiar
                        </a>
                    @endif
                </form>

                <a href="{{ route('appointments.create') }}" 
                   class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Agendar Cita
                </a>
            </div>

            <!-- Tabla de Citas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-left font-medium uppercase tracking-wider text-xs">
                            <tr>
                                <th class="px-6 py-3">Fecha y Hora</th>
                                <th class="px-6 py-3">Paciente</th>
                                <th class="px-6 py-3">Servicio</th>
                                <th class="px-6 py-3">Duración</th>
                                <th class="px-6 py-3">Atendido por</th>
                                <th class="px-6 py-3">Precio</th>
                                <th class="px-6 py-3">Pago</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
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
                                    <td class="px-6 py-4 font-bold text-gray-900">${{ number_format($appointment->price, 2) }}</td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $appointment->payment_method }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appointment->status === 'Completada')
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium">Completada</span>
                                        @elseif($appointment->status === 'Cancelada')
                                            <span class="px-2 py-1 text-xs rounded bg-rose-100 text-rose-800 font-medium">Cancelada</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-amber-100 text-amber-800 font-medium">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta cita?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-medium">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                        <p>No se encontraron citas agendadas.</p>
                                        <a href="{{ route('appointments.create') }}" class="text-emerald-600 hover:underline font-semibold text-sm">
                                            Agendar una cita ahora
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($appointments->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $appointments->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>