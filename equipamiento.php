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
$var = 'Registre el equipamiento';

// Función para mostrar mensajes de error
function mostrarError($mensaje) {
    echo '<p style="color: red;">' . $mensaje . '</p>';
}

// Conexión a la base de datos
$conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar si se ha enviado el formulario para inserción o edición
if (isset($_POST['bt1'])) {
    // Verificar si se proporcionó un valor válido para la edición
    $valor_editar = $_POST['id_editar'];

    if (!empty($valor_editar)) {
        // Sentencia SQL preparada para actualizar equipamiento
        $sql_update = "UPDATE equipamiento SET nombre=?, descripcion=?, cantidad=? WHERE id_equipo=?";
        $stmt_update = mysqli_prepare($conexion, $sql_update);

        // Verificar si la preparación de la sentencia fue exitosa
        if ($stmt_update) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt_update, 'sssi', $_POST['n'], $_POST['d'], $_POST['c'], $valor_editar);

            // Ejecutar la sentencia
            $resultado_update = mysqli_stmt_execute($stmt_update);

            // Cerrar la sentencia preparada
            mysqli_stmt_close($stmt_update);

            if ($resultado_update) {
                // Éxito
                $var = 'Equipamiento actualizado';
            } else {
                // Error en la ejecución
                mostrarError('No fue posible actualizar el equipamiento: ' . mysqli_stmt_error($stmt_update));
            }
        } else {
            // Error en la preparación de la sentencia de actualización
            mostrarError('Error en la preparación de la sentencia de actualización: ' . mysqli_error($conexion));
        }
    } else {
        // Sentencia SQL preparada para insertar equipamiento
        $sql_insert = "INSERT INTO equipamiento (nombre, descripcion, cantidad) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conexion, $sql_insert);

        // Verificar si la preparación de la sentencia fue exitosa
        if ($stmt_insert) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt_insert, 'sss', $_POST['n'], $_POST['d'], $_POST['c']);

            // Ejecutar la sentencia
            $resultado_insert = mysqli_stmt_execute($stmt_insert);

            // Cerrar la sentencia preparada de inserción
            mysqli_stmt_close($stmt_insert);

            if ($resultado_insert) {
                // Éxito
                $var = 'Equipamiento registrado';
            } else {
                // Error en la ejecución
                mostrarError('No fue posible registrar el equipamiento: ' . mysqli_stmt_error($stmt_insert));
            }
        } else {
            // Error en la preparación de la sentencia de inserción
            mostrarError('Error en la preparación de la sentencia de inserción: ' . mysqli_error($conexion));
        }
    }
}

// Verificar si se ha enviado el formulario para eliminación
if (isset($_POST['bt2'])) {
    // Verificar si se proporcionó un valor válido (ID o nombre)
    $valor_eliminar = $_POST['id_eliminar'];

    // Sentencia SQL para eliminar equipamiento por ID o nombre
    $sql_delete = "DELETE FROM equipamiento WHERE id_equipo = ? OR nombre = ?";
    $stmt_delete = mysqli_prepare($conexion, $sql_delete);

    // Verificar si la preparación de la sentencia fue exitosa
    if ($stmt_delete) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt_delete, 'ss', $valor_eliminar, $valor_eliminar);

        // Ejecutar la sentencia
        $resultado_delete = mysqli_stmt_execute($stmt_delete);

        // Cerrar la sentencia preparada de eliminación
        mysqli_stmt_close($stmt_delete);

        if ($resultado_delete) {
            // Éxito
            $var = 'Equipamiento eliminado';
        } else {
            // Error en la ejecución
            mostrarError('No fue posible eliminar el equipamiento: ' . mysqli_stmt_error($stmt_delete));
        }
    } else {
        // Error en la preparación de la sentencia de eliminación
        mostrarError('Error en la preparación de la sentencia de eliminación: ' . mysqli_error($conexion));
    }
}

// Sentencia SQL para obtener todo el equipamiento
$sql_select = "SELECT * FROM equipamiento ORDER BY Id DESC";
$result_select = mysqli_query($conexion, $sql_select);

if ($result_select) {
    // Mostrar la vista de equipamiento
    if (mysqli_num_rows($result_select) > 0) {
        while ($row = mysqli_fetch_assoc($result_select)) {
            if (isset($row['id_equipo'])) {
                echo "ID: " . $row['id_equipo'] . "<br>";
            }
            echo "Nombre: " . $row['nombre'] . "<br>";
            echo "Descripción: " . $row['descripcion'] . "<br>";
            echo "Cantidad: " . $row['cantidad'] . "<br>";
            echo "-----------------------------<br>";
        }
    } else {
        echo "No se encuentran tareas";
    }

    // Cerrar el resultado de la consulta
    mysqli_free_result($result_select);
} else {
    // Error al obtener el equipamiento
    mostrarError('Error al obtener el equipamiento: ' . mysqli_error($conexion));
}
}
// Cerrar la conexión
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8' />
    <link rel="stylesheet" type="text/css" href="equipamiento.css">
</head>
<body>
    <h4>Gestor de equipamiento</h4>

    <!-- Formulario de inserción o edición -->
    <form name="f1" action="equipamiento.php" method="POST">
        <?php echo $var . '<br>'; ?>
        ID a editar (dejar en blanco para registrar nuevo): <input type="text" name="id_editar"><br>
        Nombre <input type="text" name="n"><br>
        Descripción <input type="text" name="d"><br>
        Cantidad <input type="text" name="c"><br>
        <input type="submit" name="bt1" value="Registrar/Editar">
    </form>

    <!-- Formulario de eliminación -->
    <form name="f2" action="equipamiento.php" method="POST">
        ID o Nombre a eliminar <input type="text" name="id_eliminar"><br>
        <input type="submit" name="bt2" value="Eliminar">
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