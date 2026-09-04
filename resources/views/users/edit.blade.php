<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuario: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        @error('name') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        @error('email') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol del Sistema *</label>
                        <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="Recepcion" {{ old('role', $user->role) == 'Recepcion' ? 'selected' : '' }}>Recepción</option>
                            <option value="Administrador" {{ old('role', $user->role) == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('role') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contraseña (Dejar en blanco para mantener la actual)</label>
                        <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        @error('password') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md">Cancelar</a>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-emerald-600 hover:bg-emerald-700 rounded-md font-semibold">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>