<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 5 citas más próximas desde hoy
        $upcomingAppointments = Appointment::with(['patient', 'attendedBy'])
            ->whereDate('appointment_date', '>=', $today)
            ->where('status', '!=', 'Cancelada')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->take(5)
            ->get();

        // Ingresos: total del mes y total de hoy (solo completadas o pagadas)
        $incomeToday = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'Completada')
            ->sum('price');

        $incomeMonth = Appointment::whereMonth('appointment_date', $today->month)
            ->whereYear('appointment_date', $today->year)
            ->where('status', 'Completada')
            ->sum('price');

        return view('dashboard', compact('upcomingAppointments', 'incomeToday', 'incomeMonth'));
    }
}