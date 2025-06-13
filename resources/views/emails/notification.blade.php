<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Notificación de Tarea Creada</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Estilos internos -->
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px;
        }

        h1 {
            color: #2c3e50;
        }

        .task-details {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.6;
        }

        .task-details strong {
            color: #3498db;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }

        footer a {
            color: #fff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .social-icons {
            margin-top: 10px;
        }

        .social-icons a {
            color: #fff;
            font-size: 20px;
            margin: 0 10px;
        }

        .social-icons a:hover {
            color: #1da1f2;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <!-- Aquí debes pegar la cadena base64 de tu imagen -->
            <img src="https://drive.google.com/file/d/1o_UdJmGvx5jxygdZVPVYVcuftNifxDwI/view?usp=drive_link" alt="TaskFlow Logo">
        </div>

        <h1>¡Nueva Tarea Asignada!</h1>
        <p>El jefe <strong>{{ $boss->name }}</strong> te ha asignado una nueva tarea:</p>

        <div class="task-details">
            <p><strong>Título:</strong> {{ $task->title }}</p>
            <p><strong>Descripción:</strong> {{ $task->description }}</p>
            <p><strong>Fecha de creación:</strong> {{ $task->create_date }}</p>
            <p><strong>Fecha de vencimiento:</strong> {{ $task->deadLine }}</p>
            <p><strong>Importancia:</strong> {{ $task->importancia }}</p>
            <p><strong>Estado:</strong> {{ $task->estado }}</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 TaskFlow. Todos los derechos reservados.</p>
        <div>
            <a href="#">Sobre nosotros</a> |
            <a href="#">Servicios</a> |
            <a href="#">Política de privacidad</a> |
            <a href="#">Términos y condiciones</a> |
            <a href="#">Contacto</a>
        </div>
        <div class="social-icons">
            <a href="#" class="fab fa-facebook"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-linkedin"></a>
            <a href="#" class="fab fa-instagram"></a>
        </div>
    </footer>

</body>

</html>
