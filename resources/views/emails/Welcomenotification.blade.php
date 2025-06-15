<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bienvenido a TaskFlow</title>

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

        .welcome-message {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.6;
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
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <img src="https://ieslamarisma.net/proyectos/2025/samuelpichardo/TFG/Logo_TaskFlow.jpg" alt="TaskFlow Logo">
        </div>

        <h1>¡Bienvenido a TaskFlow, {{ $user->name }}!</h1>
        <p class="welcome-message">
            Gracias por registrarte en TaskFlow, el sistema que te ayudará a gestionar tus tareas de manera más eficiente.
        </p>

        <p class="task-details">
            <strong>Tu nombre:</strong> {{ $user->name }}<br>
            <strong>Correo electrónico:</strong> {{ $user->email }}<br>
            <strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y') }}
        </p>

        <p>
            Estamos encantados de tenerte con nosotros. Para comenzar a usar el sistema, simplemente inicia sesión en tu cuenta.
        </p>

        <footer>
            <p>&copy; 2025 TaskFlow. Todos los derechos reservados.</p>
            <div>
                <a href="#">Sobre nosotros</a> |
                <a href="#">Servicios</a> |
                <a href="#">Política de privacidad</a> |
                <a href="#">Términos y condiciones</a>
            </div>
        </footer>
    </div>

</body>

</html>
