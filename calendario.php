<?php
session_start();
$tiempoLimite = 600; // 1 minuto de inactividad

if (isset($_SESSION['usr'])) {
    if (isset($_SESSION['ultima_accion']) && (time() - $_SESSION['ultima_accion']) > $tiempoLimite) {
        // El usuario ha estado inactivo durante más tiempo del permitido, así que cerramos la sesión
        session_unset();
        session_destroy();
        header("location: Index.php"); // Redirige a la página de inicio de sesión
        exit;
    }

    // Actualiza el tiempo de la última actividad
    $_SESSION['ultima_accion'] = time();
}

// Crea un array de eventos
$eventos = array();

// Conexión con la base de datos
$mysqli = new mysqli("localhost", "root", "", "informescft");

if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT id_tarea, descripcion, fecha_comprometida FROM tarea");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = array(
            'id' => $row['id_tarea'],
            'title' => $row['descripcion'],
            'start' => $row['fecha_comprometida']
        );
    }
}

// Convierte el array en un objeto JSON
$eventos_json = json_encode($eventos);
?>

<!DOCTYPE html>
<html lang="es-419">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Tareas</title>
    <link rel="stylesheet" href="calendario.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/es-419.js'></script>
</head>
<body>
    <button onclick="location.href='index.php';">Cerrar sesión</button>
    <button><a href="Menu_Operador.php">Menú</a></button><br>
    <!-- Botón calendario -->
    <button onclick="location.href='calendario.php';">Calendario</button>

    <!-- Botón equipamiento -->
    <button onclick="location.href='equipamiento.php';">Equipamiento</button>

    <!-- Botón gestor de tareas -->
    <button onclick="location.href='gestor_tareas.php';">Gestor de Tareas</button>

    <!-- Botón reportes -->
    <button onclick="location.href='reportes.php';">Reportes</button>
    <span></span>
    <div id="calendar"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo $eventos_json; ?>,
                locale: 'es-419',
            });
            calendar.render();
        });
    </script>
</body>
</html>
