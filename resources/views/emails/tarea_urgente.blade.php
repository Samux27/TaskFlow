<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alerta de Tarea</title>
</head>
<body style="font-family: sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #e3342f;">⚠️ ¡Atención!</h2>
        <p>La siguiente tarea está próxima a su fecha límite:</p>

        <ul>
            <li><strong>Título:</strong> {{ $tarea->title }}</li>
            <li><strong>Importancia:</strong> {{ ucfirst($tarea->importancia) }}</li>
            <li><strong>Estado actual:</strong> {{ ucfirst($tarea->estado) }}</li>
            <li><strong>Fecha límite:</strong> {{ \Carbon\Carbon::parse($tarea->deadLine)->format('d/m/Y H:i') }}</li>
        </ul>

        <p>Por favor, revisa y actúa cuanto antes.</p>

        <hr>
        <p style="font-size: 12px; color: gray;">Este mensaje fue generado automáticamente por TaskFlow.</p>
    </div>
</body>
</html>
