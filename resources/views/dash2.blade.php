<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Panel Principal — VitaSpa') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                   Citas programadas para hoy: {{ \Carbon\Carbon::now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
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