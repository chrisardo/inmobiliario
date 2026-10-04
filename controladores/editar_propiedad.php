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
    $id_propiedad = intval($_POST['id_propiedad'] ?? 0);

    /* ================= VALIDACIONES ================= */

    /* ===== VALIDACIONES ===== */
    $campos = ['nombre', 'codigo', 'precio', 'tamano_area', 'categoria', 'ubicacion'];

    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            $mensaje = "Todos los campos son obligatorios.";
            return;
        }
    }
    if ($_POST['precio_anterior'] < 0) {
        $mensaje = "El precio anterior no puede ser negativo.";
        return;
    }
    if ($_POST['precio'] <= 0) {
        $mensaje = "El precio debe ser mayor a 0.";
        return;
    }

    if ($_POST['tamano_area'] < 0) {
        $mensaje = "El tamaño del área no puede ser negativo.";
        return;
    }

    /* ================= DATOS ================= */

    $nombre       = trim($_POST['nombre']);
    $codigo       = trim($_POST['codigo']);
    $precio_anterior = floatval($_POST['precio_anterior'] ?? 0);
    $precio       = floatval($_POST['precio']);
    $tamano_area   = floatval($_POST['tamano_area']);
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $id_categoria = !empty($_POST['categoria']) ? intval($_POST['categoria']) : null;

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

        $sql = "UPDATE propiedades SET 
                    nombre = ?, codigo = ?, id_categoria = ?, tamano_area_metros = ?, precio = ?, precio_anterior = ? ,ubicacion = ?, fecha_actualizacion = CURDATE()
                WHERE id_propiedad = ? AND id_user = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param(
            "ssidddsii",
            $nombre,
            $codigo,
            $id_categoria,
            $tamano_area,
            $precio,
            $precio_anterior,
            $ubicacion,
            $id_propiedad,
            $usId
        );
    } else {

        $sql = "UPDATE propiedades SET
                    nombre = ?, codigo = ?, imagen = ?, id_categoria = ?, tamano_area_metros = ?, precio = ?, precio_anterior = ?, ubicacion = ?
                WHERE id_propiedad = ? AND id_user = ?";

        $stmt = $conexion->prepare($sql);
        $imagen = null;

        $stmt->bind_param(
            "ssbidddsii",
            $nombre,
            $codigo,
            $imagen,
            $id_categoria,
            $tamano_area,
            $precio,
            $precio_anterior,
            $ubicacion,
            $id_propiedad,
            $usId
        );

        $stmt->send_long_data(2, file_get_contents($_FILES['imagen']['tmp_name']));
    }

    /* ================= EJECUTAR ================= */

    if ($stmt->execute()) {
        $_SESSION['mensajePropiedad'] = "Propiedad actualizado correctamente.";
        $_SESSION['tipoPropiedad'] = "success";
        header("Location: ../adm/adm_lista_propiedades.php");
        exit();
    } else {
        $mensaje = "Error al actualizar el propiedad." . $stmt->error;
    }
}
