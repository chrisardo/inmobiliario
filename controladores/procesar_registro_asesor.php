<?php

/* ============================================================
   CoDevPro Technology
   Archivo: controladores/procesar_registro_asesor.php
   Módulo: Registro de asesores
============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ============================================================
   VERIFICAR SESIÓN
============================================================ */

if (!isset($_SESSION['usId'])) {

    header("Location: ../login.php");
    exit();

}


/* ============================================================
   CONEXIÓN
============================================================ */

require_once 'conect_db.php';


/* ============================================================
   VARIABLES
============================================================ */

$mensaje = "";
$tipoAlerta = "";


/* ============================================================
   PROCESAR FORMULARIO
============================================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* ========================================================
       1. OBTENER DATOS
    ======================================================== */

    $nombre = isset($_POST['nombre'])
        ? trim($_POST['nombre'])
        : "";

    $apellidos = isset($_POST['apellidos'])
        ? trim($_POST['apellidos'])
        : "";

    $email = isset($_POST['email'])
        ? strtolower(trim($_POST['email']))
        : "";

    $celular = isset($_POST['celular'])
        ? trim($_POST['celular'])
        : "";

    $cargo = isset($_POST['cargo'])
        ? trim($_POST['cargo'])
        : "";

    $id_user = (int) $_SESSION['usId'];


    /* ========================================================
       2. VALIDAR CAMPOS
    ======================================================== */

    if (
        $nombre === "" ||
        $apellidos === "" ||
        $email === "" ||
        $celular === "" ||
        $cargo === ""
    ) {

        $mensaje =
            "⚠️ Todos los campos obligatorios deben completarse.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       3. VALIDAR ID USUARIO
    ======================================================== */

    if ($id_user <= 0) {

        $mensaje =
            "❌ No se pudo identificar al usuario actual.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       4. VALIDAR NOMBRE
    ======================================================== */

    if (mb_strlen($nombre) > 80) {

        $mensaje =
            "❌ El nombre no puede superar los 80 caracteres.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       5. VALIDAR APELLIDOS
    ======================================================== */

    if (mb_strlen($apellidos) > 120) {

        $mensaje =
            "❌ Los apellidos no pueden superar los 120 caracteres.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       6. VALIDAR CARGO
    ======================================================== */

    if (mb_strlen($cargo) > 100) {

        $mensaje =
            "❌ El cargo no puede superar los 100 caracteres.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       7. VALIDAR EMAIL
    ======================================================== */

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensaje =
            "❌ El correo ingresado no es válido.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       8. VALIDAR CELULAR
    ======================================================== */

    if (!preg_match('/^[0-9]{9}$/', $celular)) {

        $mensaje =
            "❌ El número de celular debe contener exactamente 9 dígitos.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       9. VALIDAR IMAGEN OBLIGATORIA
    ======================================================== */

    if (
        !isset($_FILES['imagen']) ||
        $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE
    ) {

        $mensaje =
            "⚠️ La fotografía del asesor es obligatoria.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       10. VALIDAR ERROR DE SUBIDA
    ======================================================== */

    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {

        $mensaje =
            "❌ Ocurrió un error al subir la fotografía.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       11. VALIDAR ARCHIVO TEMPORAL
    ======================================================== */

    if (
        empty($_FILES['imagen']['tmp_name']) ||
        !is_uploaded_file($_FILES['imagen']['tmp_name'])
    ) {

        $mensaje =
            "❌ La fotografía seleccionada no es válida.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       12. VALIDAR TAMAÑO
    ======================================================== */

    $maxFileSize = 1.8 * 1024 * 1024;


    if ($_FILES['imagen']['size'] <= 0) {

        $mensaje =
            "❌ La fotografía seleccionada está vacía.";

        $tipoAlerta = "danger";

        return;
    }


    if ($_FILES['imagen']['size'] > $maxFileSize) {

        $mensaje =
            "❌ La fotografía no puede superar 1.8 MB.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       13. VALIDAR MIME REAL
    ======================================================== */

    $mime = mime_content_type(
        $_FILES['imagen']['tmp_name']
    );


    $tiposPermitidos = [
        'image/jpeg',
        'image/png'
    ];


    if (!in_array($mime, $tiposPermitidos, true)) {

        $mensaje =
            "❌ Solo se permiten fotografías JPG o PNG.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       14. VALIDAR QUE SEA UNA IMAGEN REAL
    ======================================================== */

    $infoImagen = @getimagesize(
        $_FILES['imagen']['tmp_name']
    );


    if ($infoImagen === false) {

        $mensaje =
            "❌ El archivo seleccionado no es una imagen válida.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       15. LEER IMAGEN
    ======================================================== */

    $imagenBinaria = file_get_contents(
        $_FILES['imagen']['tmp_name']
    );


    if ($imagenBinaria === false || $imagenBinaria === '') {

        $mensaje =
            "❌ No se pudo procesar la fotografía.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       16. VERIFICAR CORREO DUPLICADO
    ======================================================== */

    $sqlDuplicado = "
        SELECT id_asesor
        FROM asesores
        WHERE email = ?
          AND id_user = ?
        LIMIT 1
    ";


    $stmtDuplicado = $conexion->prepare(
        $sqlDuplicado
    );


    if (!$stmtDuplicado) {

        $mensaje =
            "❌ No se pudo validar el correo del asesor.";

        $tipoAlerta = "danger";

        return;
    }


    $stmtDuplicado->bind_param(
        "si",
        $email,
        $id_user
    );


    $stmtDuplicado->execute();

    $stmtDuplicado->store_result();


    if ($stmtDuplicado->num_rows > 0) {

        $stmtDuplicado->close();

        $mensaje =
            "⚠️ Ya existe un asesor registrado con este correo.";

        $tipoAlerta = "warning";

        return;
    }


    $stmtDuplicado->close();


    /* ========================================================
       17. VERIFICAR CELULAR DUPLICADO
    ======================================================== */

    $sqlCelular = "
        SELECT id_asesor
        FROM asesores
        WHERE celular = ?
          AND id_user = ?
        LIMIT 1
    ";


    $stmtCelular = $conexion->prepare(
        $sqlCelular
    );


    if ($stmtCelular) {

        $stmtCelular->bind_param(
            "si",
            $celular,
            $id_user
        );


        $stmtCelular->execute();

        $stmtCelular->store_result();


        if ($stmtCelular->num_rows > 0) {

            $stmtCelular->close();

            $mensaje =
                "⚠️ Ya existe un asesor registrado con este número de celular.";

            $tipoAlerta = "warning";

            return;
        }


        $stmtCelular->close();

    }


    /* ========================================================
       18. INSERTAR ASESOR
    ======================================================== */

    $sqlInsert = "
        INSERT INTO asesores (
            id_user,
            nombre,
            apellidos,
            imagen,
            email,
            celular,
            cargo,
            fecha_registro,
            estado
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            NOW(),
            'ACTIVO'
        )
    ";


    $stmt = $conexion->prepare(
        $sqlInsert
    );


    if (!$stmt) {

        $mensaje =
            "❌ No se pudo preparar el registro del asesor.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       19. ASIGNAR PARÁMETROS
    ======================================================== */

    $stmt->bind_param(
        "issbsss",
        $id_user,
        $nombre,
        $apellidos,
        $imagenBinaria,
        $email,
        $celular,
        $cargo
    );


    /* ========================================================
       20. ENVIAR IMAGEN BLOB
    ======================================================== */

    $stmt->send_long_data(
        3,
        $imagenBinaria
    );


    /* ========================================================
       21. EJECUTAR
    ======================================================== */

    if ($stmt->execute()) {

        $mensaje =
            "✅ El asesor fue registrado correctamente con su fotografía.";

        $tipoAlerta = "success";

    } else {

        $mensaje =
            "❌ No se pudo registrar el asesor.";

        $tipoAlerta = "danger";

    }


    /* ========================================================
       22. CERRAR
    ======================================================== */

    $stmt->close();

}

?>
