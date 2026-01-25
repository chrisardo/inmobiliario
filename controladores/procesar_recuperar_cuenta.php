<?php
//Esta parte es controladores/procesar_recuperar_cuenta.php
include "conect_db.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["busqueda"])) {
        $mensaje = "Ingrese un correo o celular.";
        return;
    }

    $busqueda = trim($_POST["busqueda"]);

    $sql = "SELECT id_user
            FROM usuario_acceso
            WHERE email = ? OR celular = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $busqueda, $busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $u = $resultado->fetch_assoc();
        header("Location: ./restablecer_contrasena.php?uid=" . $u["id_user"]);
        exit;
    } else {
        $mensaje = "No se encontró ninguna cuenta con ese correo o celular.";
    }
}
