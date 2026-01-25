<?php
//Toda esta parte es controladores/procesar_index.php

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

include 'conect_db.php';

$sqlFoto = "SELECT imagen, nombreEmpresa FROM usuario_acceso WHERE id_user = ?";
$stmt = $conexion->prepare($sqlFoto);
$stmt->bind_param("i", $_SESSION['usId']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$fotoPerfil = null;
if (!empty($usuario['imagen'])) {
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($usuario['imagen']);
}


// Consulta para contar propiedades
$sqlPropiedades = "SELECT COUNT(*) AS total FROM propiedades WHERE id_user = " . $_SESSION['usId'];
$resultado1 = $conexion->query($sqlPropiedades);
$fila0 = $resultado1->fetch_assoc();
$totalPropiedades = $fila0['total'];

// Consulta para contar clientes (con filtro por año si existe)
$sqlContacto = "SELECT COUNT(*) AS total FROM mensajes";

$resultado2 = $conexion->query($sqlContacto);
$fila = $resultado2->fetch_assoc();
$totalContacto = $fila['total'];

// Consulta para contar productos
$sqlAsesores = "SELECT COUNT(*) AS total FROM asesores WHERE id_user = " . $_SESSION['usId'];
$resultado3 = $conexion->query($sqlAsesores);
$fila2 = $resultado3->fetch_assoc();
$totalAsesores = $fila2['total'];
