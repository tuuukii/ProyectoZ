<?php
session_start();
$servidor = 'localhost';
$usuario = 'root';
$clave = '';
$bd = 'informescft';
$var = 'Ingrese sus Credenciales.';

if (isset($_POST['bt1'])) {
    $conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

    // Utiliza una consulta preparada para evitar SQL injection
    $sql = "SELECT * FROM usuario WHERE nombres=?";
    $stmt = mysqli_prepare($conexion, $sql);
    
    // Verifica si hay un error en la preparación de la consulta
    if (!$stmt) {
        die('Error en la preparación de la consulta: ' . mysqli_error($conexion));
    }

    // Vincula la variable al marcador de posición en la consulta
    mysqli_stmt_bind_param($stmt, 's', $_POST['n']);
    mysqli_stmt_execute($stmt);
    
    // Obtiene el resultado de la consulta
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $registro = mysqli_fetch_assoc($result);
        
        // Verifica la contraseña usando password_verify
        if (password_verify($_POST['c'], $registro['contraseña'])) {
            if ($registro['rol_id_rol'] == 1) {
                $_SESSION['usr'] = $registro['nombres'];
                header("Location: Menu_Admin.php");
                exit;
            } elseif ($registro['rol_id_rol'] == 2) {
                $_SESSION['n'] = $registro['contraseña'];
                header("Location: Menu_Operador.php");
                exit;
            }
        } else {
            $var = 'Nombre de usuario o contraseña incorrecta';
        }
    } else {
        $var = 'Nombre de usuario o contraseña incorrecta';
    }

    // Cierra la consulta preparada al finalizar
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Acceso al sistema</title>
    <link rel="stylesheet" type="text/css" href='Index.css'>
	<link rel="icon" type="text/css" href="img/cft_lotaarauco_logo.ico"/>
</head>
<body>
<div class='contenedor'>
    <img src="IMG/logooooo.png" alt="Imagen" />
    <form name="f1" action="Index.php" method="POST">

		<div class='titulo1'>
		<p style="font-weight: bold;">
		<?php
		 echo 'BIENVENIDO(A):<br>';?>
		</div>
    <div class='titulo2'>
		<?php
		 echo $var ;?>
		</div>


		
        <input type="text" name="n" placeholder='Usuario'><br>
        <input type="password" name="c" placeholder='Contraseña'><br>
        <input type="submit" name="bt1" value="Ingresar "><BR>
    </form>
</div>

<footer class="pie">
 <div class="Udec-img">
    <img src="IMG/udecb.png" alt="logo" style=' width: 200px; '>
 </div>

 <div class="otra-img">
    <img src="IMG/acreditacion_blanco.png" alt="logo2" style=' width: 200px;'>
 </div>
</footer>

</body>
</html>