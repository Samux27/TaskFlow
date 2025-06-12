<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportTaskController extends Controller
{
    public function export()
    {
        $tasks = Task::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Título');
        $sheet->setCellValue('C1', 'Estado');
        $sheet->setCellValue('D1', 'Fecha creación');

        // Datos
        $row = 2;
        foreach ($tasks as $task) {
            $sheet->setCellValue("A{$row}", $task->id);
            $sheet->setCellValue("B{$row}", $task->title);
            $sheet->setCellValue("C{$row}", $task->status);
            $sheet->setCellValue("D{$row}", $task->created_at);
            $row++;
        }

        // Descargar como respuesta
        $filename = 'tareas.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
