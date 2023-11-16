<?php
$servidor = 'localhost';
$usuario = 'root';
$clave = '';
$bd = 'informescft';
$var = 'CREE SU CUENTA';

if (isset($_POST['bt1'])) {
    $conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

    // Encriptar la clave
    $claveEncriptada = password_hash($_POST['c'], PASSWORD_DEFAULT);

    // Sentencia SQL preparada
    $sql = "INSERT INTO usuario (rut, nombres, contraseña, correo, rol_id_rol) VALUES (?, ?, ?, ?, ?)";
    
    // Preparar la sentencia
    $stmt = mysqli_prepare($conexion, $sql);

    // Get the selected role ID from the form
    $selectedRole = $_POST['rol'];

    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, "ssssi", $_POST['r'], $_POST['n'], $claveEncriptada, $_POST['e'], $selectedRole);
    
    // Ejecutar la sentencia
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        // Éxito
        $var = 'Usuario registrado';
    } else {
        // Error en la ejecución
        $var = 'No fue posible registrar usuario: ' . mysqli_error($conexion);
    }

    // Cerrar la sentencia preparada
    mysqli_stmt_close($stmt);

    // Cerrar la conexión
    mysqli_close($conexion);
} else {
    // Datos no recibidos
    $var = 'Datos incompletos';
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Registro de Usuarios</title>   
    <link rel="stylesheet" type="text/css" href=''>
    <script>
    function mensaje(){
        if (document.getElementById("c").value!=document.getElementById("c2").value){
            document.getElementById("c").value="";
            document.getElementById("c2").value="";
            document.getElementById("c").focus();
            alert("Error: clave mal escrita");
        }
    }               
    </script>            
</head>
<body>                
    <form name="f1" action="registro.php" method="POST">
        <?php echo $var.'<br>'; ?>   
        rut <input type="text" name="r" maxlength="12"><br>
        nombre <input type="text" name="n" ><br>
        clave <input type="password" name="c" id="c"><br>
        repita clave <input type="password" name="c2" id="c2" OnChange=mensaje();><br>
        correo <input type="email" name="e"><br>
        <!-- Dropdown list for selecting the role -->
        Rol:
        <select name="rol">
            <!-- You can populate this dropdown dynamically from your database -->
            <option value="1">Admin</option>
            <option value="2">SuperUsuario</option>
            <!-- Add more options as needed -->
        </select><br>
        <input type="submit" name="bt1" value="Registrar">
        <button><a href="Index.php">Index</a></button>
    </form>
</body>
</html>
