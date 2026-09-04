<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cita #') . $appointment->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form action="{{ route('appointments.update', $appointment) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">Paciente *</label>
                            <select name="patient_id" id="patient_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Atendido por *</label>
                            <select name="user_id" id="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $appointment->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700">Fecha *</label>
                            <input type="date" name="appointment_date" id="appointment_date" 
                                value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('appointment_date') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700">Hora *</label>
                            <input type="time" name="appointment_time" id="appointment_time" 
                                value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('appointment_time') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duración (minutos) *</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $appointment->duration_minutes) }}" step="15" min="15" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('duration_minutes') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="service" class="block text-sm font-medium text-gray-700">Servicio *</label>
                            <select name="service" id="service" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
    @foreach(['Masaje Terapeutico', 'Masaje relajante', 'Masaje combinado', 'Masaje dreno linfatico', 'Masaje de descarga o deportivo', 'Masaje craneofacial', 'Masaje de reflexologia podal', 'Masaje piernas cansadas', 'Masaje terapeutico cuppin', 'Masaje relajante con piedras calientes', 'Masaje terapeutico con termoterapia', 'Masaje reductivo', 'Maderoterapia con drenolinfático', 'Facial Hidratante', 'Limpieza facial', 'Exfoliacion corporal', 'Depilalcion laser', 'Paquete 1 individual', 'Paquete 2 individual', 'Paquete 1 pareja', 'Paquete 2 pareja'] as $srv)
        <option value="{{ $srv }}" {{ old('service', $appointment->service) == $srv ? 'selected' : '' }}>{{ $srv }}</option>
    @endforeach
</select>
                            @error('service') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado *</label>
                            <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="Pendiente" {{ old('status', $appointment->status) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Completada" {{ old('status', $appointment->status) == 'Completada' ? 'selected' : '' }}>Completada</option>
                                <option value="Cancelada" {{ old('status', $appointment->status) == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            @error('status') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Precio ($) *</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $appointment->price) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('price') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago *</label>
                            <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="Efectivo" {{ old('payment_method', $appointment->payment_method) == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Tarjeta" {{ old('payment_method', $appointment->payment_method) == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="Transferencia" {{ old('payment_method', $appointment->payment_method) == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="Paquete" {{ old('payment_method', $appointment->payment_method) == 'Paquete' ? 'selected' : '' }}>Paquete</option>
                            </select>
                            @error('payment_method') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notas u observaciones</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes', $appointment->notes) }}</textarea>
                        @error('notes') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('appointments.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-emerald-600 rounded-md hover:bg-emerald-700 font-semibold shadow">
                            Actualizar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>