<?php
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    include 'conect_db.php';

    // 🔐 Sanitizar y obtener datos
    $propiedad  = isset($_POST['propiedades']) ? intval($_POST['propiedades']) : 0;
    $nombre     = trim($_POST['nombre'] ?? '');
    $apellidos  = trim($_POST['apellidos'] ?? '');
    $correo     = trim($_POST['correo'] ?? '');
    $celular    = trim($_POST['celular'] ?? '');
    $mensajeTxt = trim($_POST['mensaje'] ?? '');

    // ❌ VALIDACIÓN DE CAMPOS VACÍOS
    if (
        $propiedad === 0 ||
        empty($nombre) ||
        empty($apellidos) ||
        empty($correo) ||
        empty($celular) ||
        empty($mensajeTxt)
    ) {
        $mensaje = "❌ Todos los campos son obligatorios y debes seleccionar una propiedad.";
        return;
    }

    // ❌ Validar email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ El correo electrónico no es válido.";
        return;
    }

    // ❌ Validar teléfono
    if (!preg_match('/^[0-9]{7,15}$/', $celular)) {
        $mensaje = "❌ El número de teléfono no es válido.";
        return;
    }

    // 🧠 Preparar consulta (PROTECCIÓN SQL INJECTION)
    $stmt = $conexion->prepare(
        "INSERT INTO mensajes 
        (id_propiedad, nombre, apellidos, email, celular, mensaje, fecha_registro)
        VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );

    $stmt->bind_param(
        "isssss",
        $propiedad,
        $nombre,
        $apellidos,
        $correo,
        $celular,
        $mensajeTxt
    );

    if ($stmt->execute()) {
        $mensaje = "✅ ¡Mensaje enviado correctamente! Nos contactaremos contigo pronto.";
    } else {
        $mensaje = "❌ Error al enviar el mensaje. Inténtalo nuevamente.";
    }

    $stmt->close();
    //$conexion->close();
}
