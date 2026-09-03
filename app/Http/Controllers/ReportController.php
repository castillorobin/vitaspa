<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $appointments = Appointment::with(['patient', 'attendedBy'])
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        $totalIncome = $appointments->where('status', 'Completada')->sum('price');

        return view('reports.index', compact('appointments', 'startDate', 'endDate', 'totalIncome'));
    }
}