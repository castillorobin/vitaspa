<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
{
    $today = Carbon::today();

    // 1. Citas del día actual ordenadas por hora de atención
    $todayAppointments = Appointment::with(['patient', 'attendedBy'])
        ->whereDate('appointment_date', $today)
        ->orderBy('appointment_time', 'asc')
        ->get();

    // 2. Dinero recaudado hoy: Completadas y EXCLUYENDO método de pago 'Paquete'
    $incomeToday = $todayAppointments
        ->where('status', 'Completada')
        ->where('payment_method', '!=', 'Paquete')
        ->sum('price');

    // 3. Métricas del día
    $totalAppointmentsToday = $todayAppointments->count();
    $completedCount = $todayAppointments->where('status', 'Completada')->count();
    $pendingCount = $todayAppointments->where('status', 'Pendiente')->count();

    return view('dashboard', compact(
        'todayAppointments',
        'incomeToday',
        'totalAppointmentsToday',
        'completedCount',
        'pendingCount',
        'today'
    ));
}
}