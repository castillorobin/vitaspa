<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $userId = $request->input('user_id');

        $users = User::orderBy('name')->get();

        $query = Appointment::with(['patient', 'attendedBy'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $totalIncome = $appointments->where('status', 'Completada')->sum('price');
        $totalAppointments = $appointments->count();

        return view('reports.index', compact(
            'appointments',
            'startDate',
            'endDate',
            'userId',
            'users',
            'totalIncome',
            'totalAppointments'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $userId = $request->input('user_id');

        $query = Appointment::with(['patient', 'attendedBy'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $fileName = 'Reporte_VitaSpa_' . $startDate . '_al_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // BOM para compatibilidad con Microsoft Excel y caracteres en español (tildes, ñ)
            fputs($file, "\xEF\xBB\xBF");

            // Cabecera del archivo
            fputcsv($file, [
                'ID',
                'Fecha',
                'Hora',
                'Paciente',
                'Teléfono Paciente',
                'Servicio',
                'Duración (min)',
                'Atendido Por',
                'Precio ($)',
                'Método de Pago',
                'Estado',
                'Notas'
            ]);

            foreach ($appointments as $item) {
                fputcsv($file, [
                    $item->id,
                    \Carbon\Carbon::parse($item->appointment_date)->format('d/m/Y'),
                    \Carbon\Carbon::parse($item->appointment_time)->format('h:i A'),
                    $item->patient->name ?? 'N/A',
                    $item->patient->phone ?? 'Sin teléfono',
                    $item->service,
                    $item->duration_minutes,
                    $item->attendedBy->name ?? 'N/A',
                    number_format($item->price, 2, '.', ''),
                    $item->payment_method,
                    $item->status,
                    $item->notes ?? '',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}