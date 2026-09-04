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

            <!-- Barra de Filtros, Buscador y Botón Agendar Cita -->
<div class="bg-white p-4 rounded-lg shadow-sm flex flex-col xl:flex-row gap-3 justify-between items-start xl:items-center">
    <form method="GET" action="{{ route('appointments.index') }}" class="w-full xl:w-auto flex flex-wrap gap-2 items-center">
        <!-- Campo Buscador General -->
        <div class="relative flex-1 min-w-[220px]">
            <input type="text" name="search" value="{{ $search ?? '' }}" 
                placeholder="Buscar paciente, teléfono, terapeuta..." 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm pl-9">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Filtro por Fecha -->
        <input type="date" name="date" value="{{ $date ?? '' }}" 
            class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">

        <!-- Filtro por Estado -->
        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            <option value="">Todos los Estados</option>
            <option value="Pendiente" {{ ($status ?? '') === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="Completada" {{ ($status ?? '') === 'Completada' ? 'selected' : '' }}>Completada</option>
            <option value="Cancelada" {{ ($status ?? '') === 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>

        <!-- Botón Buscar / Filtrar -->
        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
            Buscar
        </button>

        <!-- Botón Limpiar si hay parámetros activos -->
        @if(!empty($search) || !empty($date) || !empty($status))
            <a href="{{ route('appointments.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm transition">
                Limpiar
            </a>
        @endif
    </form>

    <!-- Botón Agendar Cita -->
    <a href="{{ route('appointments.create') }}" 
       class="w-full xl:w-auto inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow transition whitespace-nowrap">
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
    @if(!empty($appointment->patient->phone))
        @php
            // 1. Extraer únicamente los dígitos numéricos
            $cleanPhone = preg_replace('/[^0-9]/', '', $appointment->patient->phone);

            // 2. Respaldo: si ingresaron un número local de 8 dígitos sin código, asumir El Salvador (503)
            if (strlen($cleanPhone) === 8) {
                $cleanPhone = '503' . $cleanPhone;
            }

            // 3. Formato legible de fecha y hora
            $fechaCita = \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y');
            $horaCita = \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A');

            // 4. Mensaje preparado
            $waMessage = rawurlencode("Hola {$appointment->patient->name}, le saludamos de VitaSpa para recordarle su cita de {$appointment->service} programada para el día {$fechaCita} a las {$horaCita}. ¡Le esperamos!");
        @endphp

        <!-- Botón de WhatsApp Internacional -->
        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" 
           target="_blank" 
           rel="noopener noreferrer"
           title="Enviar WhatsApp ({{ $appointment->patient->phone }})"
           class="inline-flex items-center text-emerald-600 hover:text-emerald-800 transition">
            <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 2a9.97 9.97 0 0 0-8.625 15.012L2 22l5.125-1.344A9.97 9.97 0 0 0 12.03 22c5.519 0 10-4.481 10-10s-4.481-10-10-9.969zm0 18.234c-1.562 0-3.094-.406-4.437-1.188l-.313-.188-3.281.859.875-3.188-.203-.328a8.21 8.21 0 0 1-1.281-4.406c0-4.547 3.703-8.25 8.25-8.25 4.547 0 8.25 3.703 8.25 8.25s-3.703 8.25-8.25 8.25zm4.531-6.188c-.25-.125-1.469-.719-1.703-.797-.219-.078-.391-.125-.562.125-.172.25-.656.797-.797.969-.156.172-.313.188-.563.063-.25-.125-1.062-.391-2.031-1.25a7.587 7.587 0 0 1-1.391-1.734c-.156-.25-.016-.391.109-.516.109-.109.25-.281.375-.422.125-.141.172-.25.25-.422.078-.172.031-.328-.016-.453-.063-.125-.563-1.359-.766-1.859-.203-.5-.406-.438-.563-.438h-.484c-.172 0-.453.063-.688.328-.25.25-.938.922-.938 2.25s.953 2.609 1.094 2.797c.141.188 1.875 2.875 4.547 4.031.641.281 1.141.453 1.531.578.641.203 1.234.172 1.703.109.516-.078 1.594-.656 1.813-1.281.234-.625.234-1.172.156-1.281-.063-.125-.234-.188-.484-.313z"/>
            </svg>
        </a>
    @endif

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