<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

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

    public function export(Request $request)
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

        $fileName = 'Reporte_VitaSpa_' . $startDate . '_al_' . $endDate . '.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'vitaspa_') . '.xlsx';

        $options = new Options();
        $writer = new Writer($options);
        $writer->openToFile($tempPath);

        // --- ESTILOS ---
        $titleStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(15)
            ->withFontColor(Color::rgb(6, 78, 59));

        $subTitleStyle = (new Style())
            ->withFontItalic(true)
            ->withFontSize(10)
            ->withFontColor(Color::rgb(107, 114, 128));

        $headerStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(11)
            ->withFontColor(Color::WHITE)
            ->withBackgroundColor(Color::rgb(5, 150, 105));

        $bodyStyle = (new Style())->withFontSize(10);
        $totalStyle = (new Style())->withFontBold(true)->withFontSize(11);

        // --- FILAS: Row::fromValues($valores, 0.0, $estilo) ---
        // 1. Título principal
        $writer->addRow(Row::fromValues(['Reporte de citas de VitaSpa'], 0.0, $titleStyle));

        // 2. Subtítulo con período
        $writer->addRow(Row::fromValues([
            'Período: ' . \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y') . ' | Generado: ' . now()->format('d/m/Y h:i A')
        ], 0.0, $subTitleStyle));

        // 3. Fila vacía de separación
        $writer->addRow(Row::fromValues(['']));

        // 4. Encabezados de columnas
        $headers = [
            'ID',
            'Fecha',
            'Hora',
            'Paciente',
            'Teléfono',
            'Servicio',
            'Duración (min)',
            'Atendido Por',
            'Precio ($)',
            'Método de Pago',
            'Estado',
            'Observaciones'
        ];
        $writer->addRow(Row::fromValues($headers, 0.0, $headerStyle));

        // 5. Filas de citas
        foreach ($appointments as $item) {
            $dataRow = [
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
            ];
            $writer->addRow(Row::fromValues($dataRow, 0.0, $bodyStyle));
        }

        // 6. Resumen de total recaudado
        $totalIngresos = $appointments->where('status', 'Completada')->sum('price');
        $writer->addRow(Row::fromValues(['']));
        $writer->addRow(Row::fromValues([
            '', '', '', '', '', '', '', 'Total Recaudado (Completadas):', 
            number_format($totalIngresos, 2, '.', '')
        ], 0.0, $totalStyle));

        $writer->close();

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}