<?php
include "conect_db.php";

if (!isset($_GET["uid"])) {
    header("Location: recuperar_cuenta.php");
    exit;
}

$usId = (int) $_GET["uid"];

$sql = "SELECT nombreEmpresa, email, celular, imagen
        FROM usuario_acceso
        WHERE id_user = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en prepare(): " . $conexion->error);
}

$stmt->bind_param("i", $usId);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    header("Location: recuperar_cuenta.php");
    exit;
}

$u = $resultado->fetch_assoc();

/* EMAIL OCULTO */
$email = $u['email'] ?? '';
$partes = explode('@', $email);
$emailOculto = substr($partes[0], 0, 1)
    . str_repeat('*', max(strlen($partes[0]) - 2, 1))
    . substr($partes[0], -1)
    . '@' . ($partes[1] ?? '');

/* CELULAR OCULTO */
$celularLimpio = preg_replace('/\D/', '', $u['celular'] ?? '');
$celularOculto = '+51 ***' . substr($celularLimpio, -3);
