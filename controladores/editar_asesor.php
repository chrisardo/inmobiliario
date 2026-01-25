<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";
$tipoAlerta = "danger"; // success | danger | warning

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {

    $usId       = intval($_SESSION['usId']);
    $id_asesor = intval($_POST['id_asesor'] ?? 0);

    /* ================= VALIDACIONES ================= */

    /* ===== VALIDACIONES ===== */
    $campos = ['nombre', 'apellidos', 'email', 'celular', 'cargo'];

    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            $mensaje = "Todos los campos son obligatorios.";
            return;
        }
    }

    if ($_POST['celular'] < 0) {
        $mensaje = "El tamaño del área no puede ser negativo.";
        return;
    }

    /* ================= DATOS ================= */

    $nombre       = trim($_POST['nombre']);
    $apellidos       = trim($_POST['apellidos']);
    $email       = trim($_POST['email']);
    $cargo = trim($_POST['cargo'] ?? '');
    $celular = intval($_POST['celular']);

    /* ================= IMAGEN ================= */

    $hayImagen = (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&
        is_uploaded_file($_FILES['imagen']['tmp_name'])
    );

    if ($hayImagen) {
        $permitidos = ['image/jpeg', 'image/png'];
        $mime       = mime_content_type($_FILES['imagen']['tmp_name']);
        $maxSize    = 1.8 * 1024 * 1024;

        if (!in_array($mime, $permitidos)) {
            $mensaje = "Solo se permiten imágenes JPG o PNG.";
            return;
        }

        if ($_FILES['imagen']['size'] > $maxSize) {
            $mensaje = "La imagen no debe superar 1.8 MB.";
            return;
        }
    }

    /* ================= SQL ================= */

    if (!$hayImagen) {

        $sql = "UPDATE asesores SET
                    nombre = ?, apellidos = ?, email = ?, celular = ?, cargo = ?
                WHERE id_asesor = ? AND id_user = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param(
            "sssisii",
            $nombre,
            $apellidos,
            $email,
            $celular,
            $cargo,
            $id_asesor,
            $usId
        );
    } else {

        $sql = "UPDATE asesores SET
                    nombre = ?, apellidos = ?, imagen = ?, email = ?, celular = ?, cargo = ?
                WHERE id_asesor = ? AND id_user = ?";

        $stmt = $conexion->prepare($sql);
        $imagen = null;
        $stmt->bind_param(
            "ssbsisii",
            $nombre,
            $apellidos,
            $imagen,
            $email,
            $celular,
            $cargo,
            $id_asesor,
            $usId
        );

        $stmt->send_long_data(2, file_get_contents($_FILES['imagen']['tmp_name']));
    }

    /* ================= EJECUTAR ================= */

    if ($stmt->execute()) {
        $_SESSION['mensajeAsesor'] = "Actualizado correctamente.";
        $_SESSION['tipoAsesor'] = "success";
        header("Location: ../adm/adm_lista_asesores.php");
        exit();
    } else {
        $mensaje = "Error al actualizar." . $stmt->error;
    }
}
