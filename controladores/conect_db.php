<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "inmobiliaria_iquitos");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta para contar mensajes
$resultado = $conexion->query("SELECT COUNT(*) AS total FROM mensajes");
$fila = $resultado->fetch_assoc();
$totalMensajes = $fila['total'];
?>
