<?php
// controladores/conect_db.php
$conexion = new mysqli("localhost", "root", "", "inmobiliaria_iquitos");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta para contar mensajes
/*$resultado = $conexion->query("SELECT COUNT(*) AS total FROM mensajes WHERE p.id_user = ?
      AND m.estado_mensaje_leido = 0");
$fila = $resultado->fetch_assoc();
$totalMensajes = $fila['total'];*/
?>
