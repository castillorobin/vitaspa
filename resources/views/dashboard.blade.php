<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Panel Principal — VitaSpa') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Citas programadas para hoy: {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>
            <a href="{{ route('appointments.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Nueva Cita
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- TARJETAS DE MÉTRICAS SUPERIORES -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Caja: Ingresos del Día -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-emerald-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Recaudado Hoy (Completadas)</p>
                        <h3 class="text-3xl font-extrabold text-emerald-700 mt-1">
                            ${{ number_format($incomeToday, 2) }}
                        </h3>
                        <span class="text-xs text-gray-400 mt-1 block">Suma de citas completadas</span>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Caja: Citas Programadas Hoy -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Citas de Hoy</p>
                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1">
                            {{ $totalAppointmentsToday }}
                        </h3>
                        <span class="text-xs text-gray-400 mt-1 block">Agenda del día</span>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Caja: Resumen de Estados -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-amber-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Progreso de Citas</p>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">
                                {{ $completedCount }} listas
                            </span>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">
                                {{ $pendingCount }} pendientes
                            </span>
                        </div>
                        <span class="text-xs text-gray-400 mt-2 block">Estado actual del día</span>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- TABLA DE CITAS DEL DÍA -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        Agenda de Citas para Hoy
                    </h3>
                    <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition">
                        Ver todas las fechas &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-white text-gray-600 text-left font-medium uppercase tracking-wider text-xs">
                            <tr>
                                <th class="px-6 py-3">Hora</th>
                                <th class="px-6 py-3">Paciente</th>
                                <th class="px-6 py-3">Servicio</th>
                                <th class="px-6 py-3">Atiende</th>
                                <th class="px-6 py-3">Precio</th>
                                <th class="px-6 py-3">Pago</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($todayAppointments as $appointment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                        <div class="text-xs font-normal text-gray-500">{{ $appointment->duration_minutes }} min</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $appointment->patient->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->patient->phone ?? 'Sin teléfono' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $appointment->service }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                        {{ $appointment->attendedBy->name }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        ${{ number_format($appointment->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-xs">
                                        {{ $appointment->payment_method }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appointment->status === 'Completada')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Completada</span>
                                        @elseif($appointment->status === 'Cancelada')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Cancelada</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        @if(!empty($appointment->patient->phone))
                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $appointment->patient->phone);
                                                if (strlen($cleanPhone) === 8) {
                                                    $cleanPhone = '503' . $cleanPhone;
                                                }
                                                $horaCita = \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A');
                                                $waMessage = rawurlencode("Hola {$appointment->patient->name}, le saludamos de VitaSpa para recordarle su cita de {$appointment->service} de hoy a las {$horaCita}. ¡Le esperamos!");
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               title="Enviar recordatorio por WhatsApp"
                                               class="inline-flex items-center text-emerald-600 hover:text-emerald-800 transition">
                                                <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.031 2a9.97 9.97 0 0 0-8.625 15.012L2 22l5.125-1.344A9.97 9.97 0 0 0 12.03 22c5.519 0 10-4.481 10-10s-4.481-10-10-9.969zm0 18.234c-1.562 0-3.094-.406-4.437-1.188l-.313-.188-3.281.859.875-3.188-.203-.328a8.21 8.21 0 0 1-1.281-4.406c0-4.547 3.703-8.25 8.25-8.25 4.547 0 8.25 3.703 8.25 8.25s-3.703 8.25-8.25 8.25zm4.531-6.188c-.25-.125-1.469-.719-1.703-.797-.219-.078-.391-.125-.562.125-.172.25-.656.797-.797.969-.156.172-.313.188-.563.063-.25-.125-1.062-.391-2.031-1.25a7.587 7.587 0 0 1-1.391-1.734c-.156-.25-.016-.391.109-.516.109-.109.25-.281.375-.422.125-.141.172-.25.25-.422.078-.172.031-.328-.016-.453-.063-.125-.563-1.359-.766-1.859-.203-.5-.406-.438-.563-.438h-.484c-.172 0-.453.063-.688.328-.25.25-.938.922-.938 2.25s.953 2.609 1.094 2.797c.141.188 1.875 2.875 4.547 4.031.641.281 1.141.453 1.531.578.641.203 1.234.172 1.703.109.516-.078 1.594-.656 1.813-1.281.234-.625.234-1.172.156-1.281-.063-.125-.234-.188-.484-.313z"/>
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('appointments.edit', $appointment) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="font-medium text-gray-600">No hay citas registradas para el día de hoy.</p>
                                            <a href="{{ route('appointments.create') }}" class="text-emerald-600 font-bold hover:underline text-sm">
                                                + Programar una cita ahora
                                            </a>
                                        </div>
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