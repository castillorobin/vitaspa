<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Nueva Cita') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">

                @if($patients->isEmpty())
                    <div class="mb-4 p-4 bg-amber-50 border-l-4 border-amber-400 text-amber-800 text-sm">
                        No hay pacientes registrados aún. Primero debes <a href="{{ route('patients.create') }}" class="font-bold underline">crear un paciente</a>.
                    </div>
                @endif

                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">Paciente *</label>
                            <select name="patient_id" id="patient_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">Selecciona un paciente</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} ({{ $patient->phone ?? 'Sin teléfono' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Atendido por *</label>
                            <select name="user_id" id="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
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
                            <input type="date" name="appointment_date" id="appointment_date" value="{{ old('appointment_date', date('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('appointment_date') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700">Hora *</label>
                            <input type="time" name="appointment_time" id="appointment_time" value="{{ old('appointment_time', '10:00') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('appointment_time') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duración (minutos) *</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" step="15" min="15" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('duration_minutes') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="service" class="block text-sm font-medium text-gray-700">Servicio *</label>
                            <select name="service" id="service" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">Selecciona un servicio</option>
                                @foreach(['Combinado', 'Craneo', 'Pies', 'Espalda'] as $srv)
                                    <option value="{{ $srv }}" {{ old('service') == $srv ? 'selected' : '' }}>{{ $srv }}</option>
                                @endforeach
                            </select>
                            @error('service') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado *</label>
                            <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="Pendiente" {{ old('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Completada" {{ old('status') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                <option value="Cancelada" {{ old('status') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            @error('status') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Precio ($) *</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', '25.00') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('price') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago *</label>
                            <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Tarjeta" {{ old('payment_method') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            @error('payment_method') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notas u observaciones</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Detalles particulares del paciente, alergias, preferencias..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
                        @error('notes') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('appointments.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-emerald-600 rounded-md hover:bg-emerald-700 font-semibold shadow">
                            Guardar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>