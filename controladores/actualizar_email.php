<?php
session_start();
include "conect_db.php";

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usId = $_POST['usId'];
    $emailActual = $_POST['emailActual'] ?? '';
    $emailNuevo  = $_POST['emailNuevo']  ?? '';


    $errors = [];

    if (empty($emailNuevo)) {
        $errors[] = "El email nuevo no puede estar vacío.";
    }
    if (!filter_var($emailNuevo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email nuevo no es válido.";
    }
    if ($emailNuevo === $emailActual) {
        $errors[] = "El email nuevo no puede ser igual al email actual.";
    }

    // Validar que no exista otro usuario con el mismo email
    $sqlCheck = "SELECT id_user FROM usuario_acceso WHERE email=? AND id_user<>?";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("si", $emailNuevo, $usId);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($resCheck->num_rows > 0) {
        $errors[] = "El email nuevo ya existe.";
    }

    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
        exit();
    }

    // Actualizar email
    $sqlUpdate = "UPDATE usuario_acceso SET email=? WHERE id_user=?";
    $stmtUpdate = $conexion->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $emailNuevo, $usId);

    if ($stmtUpdate->execute()) {
        echo '<div class="alert alert-success">Email actualizado correctamente.</div>';
    } else {
        echo '<div class="alert alert-danger">Error al actualizar: ' . $conexion->error . '</div>';
    }
}
