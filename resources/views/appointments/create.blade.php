<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Nueva Cita') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">

                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Selector de Paciente con Botón de Creación Rápida -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="patient_id" class="block text-sm font-medium text-gray-700">Paciente *</label>
                                <button type="button" onclick="openPatientModal()" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition">
                                    + Registrar
                                </button>
                            </div>
                            <div class="flex gap-2">
                                <select name="patient_id" id="patient_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="">Selecciona un paciente</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} ({{ $patient->phone ?? 'Sin teléfono' }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openPatientModal()" title="Agregar nuevo paciente" 
                                        class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            @error('patient_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Atendido por *</label>
                            <select name="user_id" id="user_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
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
                                @foreach(['Masaje Terapeutico', 'Masaje relajante', 'Masaje combinado', 'Masaje dreno linfatico', 'Masaje de descarga o deportivo', 'Masaje craneofacial', 'Masaje de reflexologia podal', 'Masaje piernas cansadas', 'Masaje terapeutico cuppin', 'Masaje relajante con piedras calientes', 'Masaje terapeutico con termoterapia', 'Masaje reductivo', 'Maderoterapia con drenolinfático', 'Facial Hidratante', 'Limpieza facial', 'Exfoliacion corporal', 'Depilalcion laser', 'Paquete 1 individual', 'Paquete 2 individual', 'Paquete 1 pareja', 'Paquete 2 pareja' ] as $srv)
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
                                <option value="Paquete" {{ old('payment_method') == 'Paquete' ? 'selected' : '' }}>Paquete</option>
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

    <!-- MODAL PARA CREAR PACIENTE RÁPIDO -->
    <div id="patientModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 relative">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Registrar Nuevo Paciente</h3>

            <div id="modalError" class="hidden mb-4 p-3 bg-rose-100 border border-rose-300 text-rose-700 text-xs rounded"></div>

            <form id="quickPatientForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700">Nombre completo *</label>
                    <input type="text" id="modal_name" required class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Teléfono</label>
                        <input type="text" id="modal_phone" placeholder="7123-4567" class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Correo</label>
                        <input type="email" id="modal_email" class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700">Dirección</label>
                    <input type="text" id="modal_address" class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closePatientModal()" class="px-4 py-2 text-xs text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancelar
                    </button>
                    <button type="submit" id="btnSavePatient" class="px-4 py-2 text-xs text-white bg-emerald-600 hover:bg-emerald-700 rounded-md font-semibold">
                        Guardar y Seleccionar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT PARA GESTIONAR EL MODAL Y LA PETICIÓN AJAX -->
    <script>
        function openPatientModal() {
            document.getElementById('patientModal').classList.remove('hidden');
            document.getElementById('modal_name').focus();
        }

        function closePatientModal() {
            document.getElementById('patientModal').classList.add('hidden');
            document.getElementById('quickPatientForm').reset();
            document.getElementById('modalError').classList.add('hidden');
        }

        document.getElementById('quickPatientForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSavePatient');
            btn.disabled = true;
            btn.innerText = 'Guardando...';

            const payload = {
                name: document.getElementById('modal_name').value,
                phone: document.getElementById('modal_phone').value,
                email: document.getElementById('modal_email').value,
                address: document.getElementById('modal_address').value,
            };

            fetch("{{ route('patients.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al registrar paciente');
                }
                return data;
            })
            .then(data => {
                const patient = data.patient;

                // Crear nueva opción en el select
                const select = document.getElementById('patient_id');
                const option = document.createElement('option');
                option.value = patient.id;
                option.text = `${patient.name} (${patient.phone || 'Sin teléfono'})`;
                option.selected = true;

                // Agregar y seleccionar
                select.appendChild(option);

                // Cerrar modal
                closePatientModal();
            })
            .catch(error => {
                const errBox = document.getElementById('modalError');
                errBox.innerText = error.message;
                errBox.classList.remove('hidden');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Guardar y Seleccionar';
            });
        });
    </script>
</x-app-layout>