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
    $bd = 'informescft';
    $var = 'Registre la tarea';

    // Verificar si se ha enviado el formulario para registrar o editar tarea
    if (isset($_POST['bt1'])) {
        $conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

        // Verificar la conexión
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        if (!empty($_POST['id_editar'])) {
            // Si hay un ID para editar, actualizar la tarea existente
            $sql_update = "UPDATE tarea SET descripcion=?, fecha_comprometida=?, hora_comprometida=? WHERE id_tarea=?";
            $stmt_update = mysqli_prepare($conexion, $sql_update);

            // Verificar si la preparación de la sentencia fue exitosa
            if ($stmt_update) {
                // Bind parameters
                mysqli_stmt_bind_param($stmt_update, 'sssi', $_POST['d'], $_POST['c'], $_POST['c2'], $_POST['id_editar']);

                // Execute the statement
                $resultado_update = mysqli_stmt_execute($stmt_update);

                // Cerrar la sentencia preparada de actualización
                mysqli_stmt_close($stmt_update);

                if ($resultado_update) {
                    // Éxito
                    $var = 'Tarea actualizada';
                } else {
                    // Error en la ejecución
                    $var = 'No fue posible actualizar la tarea: ' . mysqli_error($conexion);
                }
            } else {
                // Error en la preparación de la sentencia de actualización
                $var = 'Error en la preparación de la sentencia de actualización: ' . mysqli_error($conexion);
            }
        } else {
            // Si no hay un ID para editar, insertar una nueva tarea
            $sql_insert = "INSERT INTO tarea (descripcion, fecha_comprometida, hora_comprometida, estado_id_estado, lugar_id_lugar, tipo_tarea_id_tipo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conexion, $sql_insert);

            // Verificar si la preparación de la sentencia fue exitosa
            if ($stmt_insert) {
                // Asegúrate de obtener valores válidos para las claves foráneas
                $valor_estado_id = obtenerValorEstadoId(); // Reemplaza con la lógica real
                $valor_lugar_id = obtenerValorLugarId(); // Reemplaza con la lógica real
                $valor_tipo_tarea_id = obtenerValorTipoTareaId(); // Reemplaza con la lógica real

                // Bind parameters
                mysqli_stmt_bind_param($stmt_insert, 'sssiii', $_POST['d'], $_POST['c'], $_POST['c2'], $valor_estado_id, $valor_lugar_id, $valor_tipo_tarea_id);

                // Execute the statement
                $resultado_insert = mysqli_stmt_execute($stmt_insert);

                // Cerrar la sentencia preparada de inserción
                mysqli_stmt_close($stmt_insert);

                if ($resultado_insert) {
                    // Éxito
                    $var = 'Tarea registrada';
                } else {
                    // Error en la ejecución
                    $var = 'No fue posible registrar la tarea: ' . mysqli_error($conexion);
                }
            } else {
                // Error en la preparación de la sentencia de inserción
                $var = 'Error en la preparación de la sentencia de inserción: ' . mysqli_error($conexion);
            }
        }

        // Cerrar la conexión
        mysqli_close($conexion);
    } elseif (isset($_POST['bt2'])) { // Verificar si se ha enviado el formulario para eliminar por ID
        $conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

        // Verificar la conexión
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        // Sentencia SQL preparada para eliminar tarea por ID
        $sql_delete = "DELETE FROM tarea WHERE id_tarea = ?";
        $stmt_delete = mysqli_prepare($conexion, $sql_delete);

        // Verificar si la preparación de la sentencia fue exitosa
        if ($stmt_delete) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt_delete, 'i', $_POST['id']);

            // Execute the statement
            $resultado_delete = mysqli_stmt_execute($stmt_delete);

            // Cerrar la sentencia preparada de eliminación
            mysqli_stmt_close($stmt_delete);

            if ($resultado_delete) {
                // Éxito
                $var = 'Tarea eliminada';
            } else {
                // Error en la ejecución
                $var = 'No fue posible eliminar la tarea: ' . mysqli_error($conexion);
            }
        } else {
            // Error en la preparación de la sentencia de eliminación
            $var = 'Error en la preparación de la sentencia de eliminación: ' . mysqli_error($conexion);
        }

        // Cerrar la conexión
        mysqli_close($conexion);
    } elseif (isset($_POST['bt3'])) { // Verificar si se ha enviado el formulario para editar por ID
        $id_editar = $_POST['id_editar'];
        $conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

        // Verificar la conexión
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        // Obtener la tarea por ID para editar
        $sql_editar = "SELECT * FROM tarea WHERE id_tarea = ?";
        $stmt_editar = mysqli_prepare($conexion, $sql_editar);

        // Verificar si la preparación de la sentencia fue exitosa
        if ($stmt_editar) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt_editar, 'i', $id_editar);

            // Execute the statement
            $resultado_editar = mysqli_stmt_execute($stmt_editar);

            // Vincular variables a los resultados
            mysqli_stmt_bind_result($stmt_editar, $id, $descripcion, $fecha_comprometida, $hora_comprometida);

            // Obtener los resultados
            mysqli_stmt_fetch($stmt_editar);

            // Cerrar la sentencia preparada de edición
            mysqli_stmt_close($stmt_editar);

            // Verificar si se encontró la tarea
            if ($resultado_editar && isset($id)) {
                // Mostrar el formulario de edición con los datos actuales
                $var = 'Editando tarea: ID ' . $id;
            } else {
                // Error al obtener la tarea para editar
                $var = 'No se encontró la tarea para editar: ' . mysqli_error($conexion);
            }
        } else {
            // Error en la preparación de la sentencia de edición
            $var = 'Error en la preparación de la sentencia de edición: ' . mysqli_error($conexion);
        }

        // Cerrar la conexión
        mysqli_close($conexion);
    } else {
        // Datos no recibidos
        $var = 'Datos no ingresados';
    }

    // Mostrar todas las tareas después de registrar, editar o eliminar y también cuando la página se carga
    $conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

    // Verificar la conexión
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Sentencia SQL para obtener todas las tareas registradas
    $sql_select = "SELECT * FROM tarea ORDER BY id_tarea DESC";
    $result_select = mysqli_query($conexion, $sql_select);

    if ($result_select) {
        // Mostrar la vista de consulta después de registrar, editar o eliminar la tarea o al cargar la página
        while ($row = mysqli_fetch_assoc($result_select)) {
            echo "ID: " . $row['id_tarea'] . "<br>";
            echo "Descripción: " . $row['descripcion'] . "<br>";
            echo "Fecha Comprometida: " . $row['fecha_comprometida'] . "<br>";
            echo "Hora Comprometida: " . $row['hora_comprometida'] . "<br>";
            echo "-----------------------------<br>";
        }

        // Cerrar el resultado de la consulta
        mysqli_free_result($result_select);
    } else {
        // Error al obtener las tareas recién registradas
        echo 'Error al obtener las tareas recién registradas: ' . mysqli_error($conexion);
    }

    // Cerrar la conexión
    mysqli_close($conexion);
}

// Función para obtener el valor correcto de estado_id_estado
function obtenerValorEstadoId() {
    // Lógica para obtener el valor correcto, por ejemplo, desde un formulario o base de datos
    // Debes implementar esta función según tus necesidades
    return $_POST['estado_id_estado'] ?? 1; // Valor predeterminado 1 en caso de que no se proporcione en el formulario
}

// Función para obtener el valor correcto de lugar_id_lugar
function obtenerValorLugarId() {
    // Lógica para obtener el valor correcto, por ejemplo, desde un formulario o base de datos
    // Debes implementar esta función según tus necesidades
    return $_POST['lugar_id_lugar'] ?? 1; // Valor predeterminado 1 en caso de que no se proporcione en el formulario
}

// Función para obtener el valor correcto de tipo_tarea_id_tipo
function obtenerValorTipoTareaId() {
    // Lógica para obtener el valor correcto, por ejemplo, desde un formulario o base de datos
    // Debes implementar esta función según tus necesidades
    return $_POST['tipo_tarea_id_tipo'] ?? 1; // Valor predeterminado 1 en caso de que no se proporcione en el formulario
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8' />
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h4>Gestor de tareas</h4>

    <!-- Formulario de inserción y edición -->
    <form name="f1" action="gestor_tareas.php" method="POST">
        <?php echo $var .'<br>'; ?>
        Descripción <input type="text" name="d" value="<?php echo isset($descripcion) ? $descripcion : ''; ?>"><br>
        Fecha Comprometida <input type="date" name="c" id="c" value="<?php echo isset($fecha_comprometida) ? $fecha_comprometida : ''; ?>"><br>
        Hora Comprometida <input type="time" name="c2" id="c2" value="<?php echo isset($hora_comprometida) ? $hora_comprometida : ''; ?>"><br>

        <!-- Añadido para las claves foráneas -->
        Estado ID <input type="text" name="estado_id_estado" value="<?php echo obtenerValorEstadoId(); ?>"><br>
        Lugar ID <input type="text" name="lugar_id_lugar" value="<?php echo obtenerValorLugarId(); ?>"><br>
        Tipo Tarea ID <input type="text" name="tipo_tarea_id_tipo" value="<?php echo obtenerValorTipoTareaId(); ?>"><br>

        <?php if (isset($id)) : ?>
            <!-- Si estamos editando, incluir el campo oculto con el ID -->
            <input type="hidden" name="id_editar" value="<?php echo $id; ?>">
            <input type="submit" name="bt1" value="Editar">
        <?php else : ?>
            <!-- Si no estamos editando, mostrar el botón de registro -->
            <input type="submit" name="bt1" value="Registrar">
        <?php endif; ?>
    </form>

    <!-- Formulario de eliminación -->
    <form name="f2" action="gestor_tareas.php" method="POST">
        Eliminar por ID <input type="text" name="id"><br>
        <input type="submit" name="bt2" value="Eliminar por ID">
    </form>

    <!-- Formulario de edición por ID -->
    <form name="f3" action="gestor_tareas.php" method="POST">
        Editar por ID <input type="text" name="id_editar"><br>
        <input type="submit" name="bt3" value="Editar por ID">
    </form>

    <!-- Botones de navegación -->
    <button><a href="index.php">Cerrar Sesion</a></button>
    <button onclick="location.href='Menu_Operador.php';">Regresar</button>
    <button onclick="location.href='calendario.php';">Calendario</button>
    <button onclick="location.href='equipamiento.php';">Equipamiento</button>
    <button onclick="location.href='gestor_tareas.php';">Gestor de Tareas</button>
    <button onclick="location.href='reportes.php';">Reportes</button>
</body>
</html>