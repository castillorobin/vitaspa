<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Cell;
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

        // Estilo de Contabilidad en Dólares ($#,##0.00)
        /*
        $currencyStyle = (new Style())
            ->withFontSize(10)
            ->withFormat('"$"\#,##0.00');

        // Estilo de Contabilidad para Totales (Negrita + Dólar)
        $totalCurrencyStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(11)
            ->withFormat('"$"\#,##0.00');


        $totalLabelStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(11);
            */
            $currencyStyle = (new Style())
            ->withFontSize(10)
            ->withFormat('$#,##0.00');

        $totalCurrencyStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(11)
            ->withFormat('$#,##0.00');

            $totalLabelStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(11);

        // --- 1. ENCABEZADO Y FILAS PREVIAS ---
        $writer->addRow(Row::fromValues(['Reporte de citas de VitaSpa'], 0.0, $titleStyle));

        $writer->addRow(Row::fromValues([
            'Período: ' . \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y') . ' | Generado: ' . now()->format('d/m/Y h:i A')
        ], 0.0, $subTitleStyle));

        $writer->addRow(Row::fromValues(['']));

        $headers = [
            'ID',
            'Fecha',
            'Hora',
            'Paciente',
            'Teléfono',
            'Servicio',
            'Duración (min)',
            'Atendido Por',
            'Precio',
            'Método de Pago',
            'Estado',
            'Observaciones'
        ];
        $writer->addRow(Row::fromValues($headers, 0.0, $headerStyle));

        // --- 2. FILAS DE CITAS CON FORMATO CONTABLE ---
        foreach ($appointments as $item) {
            $rowCells = [
                Cell::fromValue($item->id, $bodyStyle),
                Cell::fromValue(\Carbon\Carbon::parse($item->appointment_date)->format('d/m/Y'), $bodyStyle),
                Cell::fromValue(\Carbon\Carbon::parse($item->appointment_time)->format('h:i A'), $bodyStyle),
                Cell::fromValue($item->patient->name ?? 'N/A', $bodyStyle),
                Cell::fromValue($item->patient->phone ?? 'Sin teléfono', $bodyStyle),
                Cell::fromValue($item->service, $bodyStyle),
                Cell::fromValue($item->duration_minutes, $bodyStyle),
                Cell::fromValue($item->attendedBy->name ?? 'N/A', $bodyStyle),
                // Precio como float con formato de contabilidad en dólares
                Cell::fromValue((float) $item->price, $currencyStyle),
                Cell::fromValue($item->payment_method, $bodyStyle),
                Cell::fromValue($item->status, $bodyStyle),
                Cell::fromValue($item->notes ?? '', $bodyStyle),
            ];

            $writer->addRow(new Row($rowCells));
        }

        // --- 3. FILA DE TOTALES ---
        // Suma de citas completadas excluyendo "Paquete"
        $totalIngresos = $appointments
            ->where('status', 'Completada')
            ->where('payment_method', '!=', 'Paquete')
            ->sum('price');

        $writer->addRow(Row::fromValues(['']));

        $totalRowCells = [
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('Total Recaudado (Completadas):', $totalLabelStyle),
            Cell::fromValue((float) $totalIngresos, $totalCurrencyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
            Cell::fromValue('', $bodyStyle),
        ];

        $writer->addRow(new Row($totalRowCells));

        $writer->close();

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}