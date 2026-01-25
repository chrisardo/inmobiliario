<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conect_db.php';

$mensaje = "";
$tipoAlerta = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* =========================
       VALIDAR CAMPOS OBLIGATORIOS
    ========================== */
    if (
        empty(trim($_POST['nombre'])) ||
        empty(trim($_POST['apellidos'])) ||
        empty($_POST['email']) ||
        empty($_POST['celular']) ||
        empty($_POST['cargo'])
    ) {
        $mensaje = "⚠️ Todos los campos obligatorios deben completarse.";
        $tipoAlerta = "warning";
        return;
    }

    /* =========================
       LIMPIAR Y CONVERTIR DATOS
    ========================== */
    $nombre     = trim($_POST['nombre']);
    $apellidos     = trim($_POST['apellidos']);
    $email    = trim($_POST['email']);
    $celular = (int) $_POST['celular'];
    $cargo    = trim($_POST['cargo']);
    $id_user   = (int) $_SESSION['usId'];
    $Eliminado = 0;

    /* =========================
   VALIDAR EMAIL
========================= */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ El correo ingresado no es válido. Ejemplo: email@email.com";
        $tipoAlerta = "danger";
        return;
    }

    /* =========================
   VALIDAR CELULAR
========================= */
    // Quitar espacios
    $celular = trim($_POST['celular']);

    // Solo números
    if (!preg_match('/^[0-9]{9}$/', $celular)) {
        $mensaje = "❌ El número de celular debe contener 9 dígitos numéricos. Ejemplo: 943239039";
        $tipoAlerta = "danger";
        return;
    }



    /* =========================
       CONTROLAR CORREOS DUPLICADOS (CÓDIGO)
    ========================== */
    $stmt = $conexion->prepare(
        "SELECT 1 FROM asesores WHERE email = ? AND id_user = ?"
    );
    $stmt->bind_param("si", $email, $id_user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensaje = "⚠️ Ya existe un asesor con este correo.";
        $tipoAlerta = "warning";
        $stmt->close();
        return;
    }
    $stmt->close();



    /* =========================
       VALIDAR IMAGEN (OPCIONAL)
    ========================== */
    $imagenBinaria = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $mensaje = "❌ Error al subir la imagen.";
            $tipoAlerta = "danger";
            return;
        }

        if ($_FILES['imagen']['size'] > (1.8 * 1024 * 1024)) {
            $mensaje = "❌ La imagen no puede superar 1.8 MB.";
            $tipoAlerta = "danger";
            return;
        }

        $mime = mime_content_type($_FILES['imagen']['tmp_name']);
        if (!in_array($mime, ['image/png', 'image/jpeg'])) {
            $mensaje = "❌ Solo se permiten imágenes PNG o JPG.";
            $tipoAlerta = "danger";
            return;
        }

        $imagenBinaria = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    /* =========================
       INSERTAR ASESORES
    ========================== */
    $sql = "INSERT INTO asesores (
                id_user, nombre, apellidos, imagen, email, celular, cargo, fecha_registro
            ) VALUES (?, ?, ?, ?, ?, ?, ?,NOW())";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "issbsis",
        $id_user,
        $nombre,
        $apellidos,
        $imagenBinaria,
        $email,
        $celular,
        $cargo
    );

    if ($imagenBinaria !== null) {
        $stmt->send_long_data(3, $imagenBinaria);
    }

    if ($stmt->execute()) {
        $mensaje = $imagenBinaria
            ? "✅ Registrado correctamente con imagen."
            : "✅ Registrado correctamente sin imagen.";
        $tipoAlerta = "success";
    } else {
        $mensaje = "❌ Error al registrar producto: " . $stmt->error;
        $tipoAlerta = "danger";
    }

    $stmt->close();
}
