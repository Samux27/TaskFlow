<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ExportTaskController extends Controller
{
     public function export()
    {
        $tasks = Task::with('assignedUsers', 'boss')->get(); // Cargar tareas con usuarios asignados y jefe
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados de las columnas
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Título');
        $sheet->setCellValue('C1', 'Estado');
        $sheet->setCellValue('D1', 'Fecha creación');
        $sheet->setCellValue('E1', 'Jefe asignado');
        $sheet->setCellValue('F1', 'Usuarios asignados');

        // Aplicar negrita en las cabeceras
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        // Ajustar el tamaño de las columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Llenar datos de las tareas
        $row = 2; // Comienza en la fila 2
        foreach ($tasks as $task) {
            // Tareas generales
            $sheet->setCellValue("A{$row}", $task->id);
            $sheet->setCellValue("B{$row}", $task->title);
            $sheet->setCellValue("C{$row}", $task->estado);

            // Fecha de creación (verificación de null)
            $createdAt = $task->create_date ? Carbon::parse($task->create_date)->format('d/m/Y') : 'No disponible';
            $sheet->setCellValue("D{$row}", $createdAt);

            // Jefe asignado (si tiene)
            $sheet->setCellValue("E{$row}", $task->boss ? $task->boss->name : 'No asignado');

            // Usuarios asignados (si tiene)
            $assignedUsers = $task->assignedUsers->pluck('name')->join(', ');
            $sheet->setCellValue("F{$row}", $assignedUsers);

            $row++;
        }

        // Aplicar bordes a las celdas de los datos
        $sheet->getStyle("A1:F{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Descargar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'tareas_' . now()->format('Y-m-d') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

public function exportUsers()
{
    $users = User::all(); // Obtener todos los usuarios
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados de las columnas
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Nombre');
    $sheet->setCellValue('C1', 'Apellido');
    $sheet->setCellValue('D1', 'Correo electrónico');
    $sheet->setCellValue('E1', 'DNI');
    $sheet->setCellValue('F1', 'Estado activo');
    
    $sheet->setCellValue('H1', 'Avatar');
    $sheet->setCellValue('I1', 'Fecha de creación');
    

    // Aplicar negrita en las cabeceras
    $sheet->getStyle('A1:J1')->getFont()->setBold(true);
    // Ajustar el tamaño de las columnas
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Llenar los datos de los usuarios
    $row = 2; // Comienza en la fila 2
    foreach ($users as $user) {
        // Datos del usuario
        $sheet->setCellValue("A{$row}", $user->id);
        $sheet->setCellValue("B{$row}", $user->name);
        $sheet->setCellValue("C{$row}", $user->surname);
        $sheet->setCellValue("D{$row}", $user->email);
        $sheet->setCellValue("E{$row}", $user->dni);
        $sheet->setCellValue("F{$row}", $user->is_active ? 'Activo' : 'Inactivo');
        
       
        
        // Avatar (puedes cambiarlo por alguna URL o nombre si es necesario)
        $sheet->setCellValue("H{$row}", $user->avatar ?? 'No disponible');
        
        // Fechas
        $createdAt = $user->created_at ? Carbon::parse($user->created_at)->format('d/m/Y') : 'No disponible';
        
        $sheet->setCellValue("I{$row}", $createdAt);
        
    

        $row++;
    }

    // Aplicar bordes a las celdas de los datos
    $sheet->getStyle("A1:J{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Descargar el archivo
    $writer = new Xlsx($spreadsheet);
    $filename = 'usuarios_' . now()->format('Y-m-d') . '.xlsx';
    $temp_file = tempnam(sys_get_temp_dir(), $filename);
    $writer->save($temp_file);

    return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
}
}
