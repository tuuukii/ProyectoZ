<?php
session_start();
$tiempoLimite = 600; // 10 minutos de inactividad

if (isset($_SESSION['usr'])) {
    if (isset($_SESSION['ultima_accion']) && (time() - $_SESSION['ultima_accion']) > $tiempoLimite) {
        // El usuario ha estado inactivo durante más tiempo del permitido, así que cerramos la sesión
        session_unset();
        session_destroy();
        header("location: validacion.php"); // redirige a la página de inicio de sesión
        exit;
    }

    // actualiza el tiempo de la última actividad
    $_SESSION['ultima_accion'] = time();
$servidor = 'localhost';
$usuario = 'root';
$clave = '';
$bd = 'torre';
$var = 'registre la tarea';

// Mostrar todas las tareas cuando la página se carga
$conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Sentencia SQL para obtener todas las tareas registradas
$sql_select = "SELECT * FROM calendario ORDER BY id DESC";
$result_select = mysqli_query($conexion, $sql_select);

if ($result_select) {
    // Obtener la preferencia de vista del usuario (recuadro o lista)
    $vista = isset($_COOKIE['vista']) ? $_COOKIE['vista'] : 'recuadro';

    // Mostrar la vista de consulta después de cargar la página
    while ($row = mysqli_fetch_assoc($result_select)) {
        if ($vista === 'recuadro') {
            // Mostrar por recuadro
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
            echo "ID: " . $row['id'] . "<br>";
            echo "Título: " . $row['titulo'] . "<br>";
            echo "Descripción: " . $row['descripcion'] . "<br>";
            echo "</div>";
        } elseif ($vista === 'lista') {
            // Mostrar por lista
            echo "<ul>";
            echo "<li>ID: " . $row['id'] . "</li>";
            echo "<li>Título: " . $row['titulo'] . "</li>";
            echo "<li>Descripción: " . $row['descripcion'] . "</li>";
            echo "</ul>";
        }
    }
    // Cerrar el resultado de la consulta
    mysqli_free_result($result_select);
} else {
    // Error al obtener las tareas recién registradas
    echo 'Error al obtener las tareas recién registradas: ' . mysqli_error($conexion);
}
}
// Cerrar la conexión
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8' />
    <link rel="stylesheet" type="text/css" href="reportes.css">
</head>
<body>
    <h4>Reportes de tareas</h4>

    <!-- Agregar formulario para cambiar la preferencia de vista -->
    <form method="post">
        <label for="vista">Vista:</label>
        <select name="vista" id="vista">
            <option value="recuadro">Recuadro</option>
            <option value="lista">Lista</option>
        </select>
        <input type="submit" value="Cambiar Vista">
    </form>
    <!-- Botones de navegación -->
    <button onclick="location.href='menu_Operador.php';">Menu</button>
    <button onclick="location.href='calendario.php';">Calendario</button>
    <button onclick="location.href='equipamiento.php';">Equipamiento</button>
    <button onclick="location.href='gestor_tareas.php';">Gestor de Tareas</button>
    <button onclick="location.href='reportes.php';">Reportes</button>

    <!-- Botón Regresar -->
    <button onclick="location.href='Menu.php';">Regresar</button>
</body>
</html>
