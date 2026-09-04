<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
{
    $status = $request->input('status');
    $date = $request->input('date');

    $appointments = Appointment::with(['patient', 'attendedBy'])
        ->when($status, fn($q) => $q->where('status', $status))
        ->when($date, fn($q) => $q->whereDate('appointment_date', $date))
        ->orderBy('appointment_date', 'desc') // Agrupa por día
        ->orderBy('appointment_time', 'asc')  // Ordena de más temprano a más tarde
        ->paginate(15)
        ->withQueryString();

    return view('appointments.index', compact('appointments', 'status', 'date'));
}

    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('appointments.create', compact('patients', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'user_id'          => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'service'          => 'required|string|max:150',
            'status'           => 'required|in:Pendiente,Completada,Cancelada',
            'price'            => 'required|numeric|min:0',
            'payment_method'   => 'required|in:Efectivo,Tarjeta,Transferencia',
            'notes'            => 'nullable|string|max:1000',
        ]);

        Appointment::create($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Cita agendada exitosamente.');
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'users'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'user_id'          => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'service'          => 'required|string|max:150',
            'status'           => 'required|in:Pendiente,Completada,Cancelada',
            'price'            => 'required|numeric|min:0',
            'payment_method'   => 'required|in:Efectivo,Tarjeta,Transferencia',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Cita eliminada correctamente.');
    }
}