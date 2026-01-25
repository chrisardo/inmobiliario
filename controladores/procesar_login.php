<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    include 'conect_db.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {

        $stmt = $conexion->prepare("
            SELECT id_user, username, email, contrasena, nombreEmpresa, estado
            FROM usuario_acceso
            WHERE username = ? OR email = ?
        ");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['contrasena'])) {

                if (strtolower($row['estado']) !== 'activo') {
                    $error = "Tu cuenta está baneada o inactiva. Contacta al administrador.";
                } else {
                    $_SESSION['usId'] = $row['id_user'];
                    $_SESSION['username'] = $row['username'];

                    header("Location: ./adm/adm_index.php");
                    
                    exit();
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }

        $stmt->close();
    }

    $conexion->close();
}
