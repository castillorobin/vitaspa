<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\CellAlignment;

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

        // Inicializar el escritor de XLSX
        $writer = new Writer();
        $writer->openToFile($tempPath);

        // --- ESTILOS ---
        // 1. Título principal
        $titleStyle = (new Style())
            ->setFontBold()
            ->setFontSize(15)
            ->setFontColor(Color::rgb(6, 78, 59)); // Verde esmeralda oscuro

        // 2. Subtítulo / Rango de fechas
        $subTitleStyle = (new Style())
            ->setFontItalic()
            ->setFontSize(10)
            ->setFontColor(Color::rgb(107, 114, 128));

        // 3. Encabezados de tabla
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor(Color::rgb(5, 150, 105)); // Verde esmeralda VitaSpa

        // 4. Filas normales
        $bodyStyle = (new Style())
            ->setFontSize(10);

        // --- CONTENIDO ---
        // Fila 1: Encabezado principal solicitado
        $writer->addRow(Row::fromValues(['Reporte de citas de VitaSpa'], $titleStyle));

        // Fila 2: Subtítulo con rango y fecha de emisión
        $writer->addRow(Row::fromValues([
            'Período: ' . \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y') . ' | Generado: ' . now()->format('d/m/Y h:i A')
        ], $subTitleStyle));

        // Fila 3: Espacio en blanco
        $writer->addRow(Row::fromValues(['']));

        // Fila 4: Columnas
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
        $writer->addRow(Row::fromValues($headers, $headerStyle));

        // Filas de datos
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
            $writer->addRow(Row::fromValues($dataRow, $bodyStyle));
        }

        // Fila de resumen al final
        $totalIngresos = $appointments->where('status', 'Completada')->sum('price');
        $writer->addRow(Row::fromValues(['']));
        $writer->addRow(Row::fromValues([
            '', '', '', '', '', '', '', 'Total Recaudado (Completadas):', 
            number_format($totalIngresos, 2, '.', '')
        ], (new Style())->setFontBold()->setFontSize(11)));

        $writer->close();

        // Enviar descarga y eliminar el archivo temporal
        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}