<?php
session_start();
$tiempoLimite = 300; //1 minuto de inactividad
//todo el codigo para usuario validado
if(isset($_SESSION['usr'])){
    if (isset($_SESSION['ultima_accion']) && (time() - $_SESSION['ultima_accion'] > $tiempoLimite)) {
// El usuario ha estaado inactivo durante mas tiempo del permitido, asi que cerramos la sesion
session_unset();
session_destroy();
header("location: Index.php");//redirige a la pagina de incicio de sesion
exit;
}

    //actualiza el tiempo de la ultima actividad
    $_SESSION['ultima_accion'] = time();
?>
<html>
    <head>
        <meta charset='utf-8' />
    </head>
    <body>
      <h1> Menu Usuario</h1>
      <h2>Hola @Usuario</h2>
    <button onclick="location.href='Index.php';">Cerrar Sesión</button>

    <!-- botón calendario -->
 <button onclick="location.href='calendario.php';">Calendario</button>

 <!-- botón equipamiento -->
 <button onclick="location.href='equipamiento.php';">Equipamiento</button>

 <!-- botón gestor de tareas -->
 <button onclick="location.href='gestor_tareas.php';">Gestor de Tareas</button>

 <!-- botón reportes -->
 <button onclick="location.href='reportes.php';">Reportes</button>
    </body>
</html>
<?php
    //fin usuario validado
}else{
    //informaremos que no tiene permisos
    echo 'acceso denegado XD';
    #header("location:validacion.php")
}
?>