<?php

/**
 * ==========================================================
 * CoDevPro Technology
 * Archivo: ajax/procesar_contacto.php
 * Módulo: Formulario de contacto AJAX
 * Sistema: Inmobiliario
 * ==========================================================
 */

header('Content-Type: application/json; charset=UTF-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controladores/conect_db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/* =========================================================
   FUNCIÓN RESPUESTA JSON
========================================================= */

function respuestaJSON($success, $message, $extra = [])
{
    echo json_encode(
        array_merge(
            [
                'success' => $success,
                'message' => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* =========================================================
   SOLO POST
========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    respuestaJSON(
        false,
        '❌ Método de solicitud no permitido.'
    );
}


/* =========================================================
   OBTENER DATOS DEL FORMULARIO
========================================================= */

$propiedad = (int)($_POST['propiedades'] ?? 0);

$nombre = trim(
    $_POST['nombre'] ?? ''
);

$apellidos = trim(
    $_POST['apellidos'] ?? ''
);

$correo = trim(
    $_POST['correo'] ?? ''
);

$celular = trim(
    $_POST['celular'] ?? ''
);

$mensajeTxt = trim(
    $_POST['mensaje'] ?? ''
);


/* =========================================================
   VALIDAR CAMPOS
========================================================= */

if (
    $propiedad <= 0 ||
    $nombre === '' ||
    $apellidos === '' ||
    $correo === '' ||
    $celular === '' ||
    $mensajeTxt === ''
) {

    respuestaJSON(
        false,
        '❌ Todos los campos son obligatorios y debes seleccionar una propiedad.'
    );
}


/* =========================================================
   VALIDAR CORREO DEL CLIENTE
========================================================= */

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    respuestaJSON(
        false,
        '❌ El correo electrónico no es válido.'
    );
}


/* =========================================================
   VALIDAR CELULAR
========================================================= */

$celularLimpio = preg_replace(
    '/\D+/',
    '',
    $celular
);

if (
    $celularLimpio === '' ||
    strlen($celularLimpio) < 7 ||
    strlen($celularLimpio) > 15
) {

    respuestaJSON(
        false,
        '❌ El número de celular no es válido.'
    );
}


/* =========================================================
   VERIFICAR PROPIEDAD
========================================================= */

$stmtPropiedad = $conexion->prepare("

    SELECT
        id_propiedad,
        id_user,
        nombre,
        codigo,
        ubicacion,
        precio,
        precio_anterior

    FROM propiedades

    WHERE id_propiedad = ?

      AND (
            Eliminado IS NULL
            OR Eliminado = 0
          )

    LIMIT 1

");


if (!$stmtPropiedad) {

    error_log(
        'ERROR preparar propiedad: ' .
        $conexion->error
    );

    respuestaJSON(
        false,
        '❌ No se pudo verificar la propiedad.'
    );
}


$stmtPropiedad->bind_param(
    'i',
    $propiedad
);


if (!$stmtPropiedad->execute()) {

    error_log(
        'ERROR ejecutar propiedad: ' .
        $stmtPropiedad->error
    );

    $stmtPropiedad->close();

    respuestaJSON(
        false,
        '❌ No se pudo verificar la propiedad.'
    );
}


$resultadoPropiedad =
    $stmtPropiedad->get_result();

$propData =
    $resultadoPropiedad->fetch_assoc();

$stmtPropiedad->close();


if (!$propData) {

    respuestaJSON(
        false,
        '❌ La propiedad seleccionada no existe o ya no está disponible.'
    );
}


/* =========================================================
   INSERTAR MENSAJE
========================================================= */

$stmt = $conexion->prepare("

    INSERT INTO mensajes
    (
        id_propiedad,
        nombre,
        apellidos,
        email,
        celular,
        mensaje,
        fecha_registro,
        estado_mensaje_leido
    )

    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        CURDATE(),
        0
    )

");


if (!$stmt) {

    error_log(
        'ERROR preparar INSERT mensajes: ' .
        $conexion->error
    );

    respuestaJSON(
        false,
        '❌ Error al preparar el registro del mensaje.'
    );
}


/*
 * La tabla mensajes tiene celular como INT.
 */

$celularBD = (int)$celularLimpio;


$stmt->bind_param(
    'isssis',
    $propiedad,
    $nombre,
    $apellidos,
    $correo,
    $celularBD,
    $mensajeTxt
);


if (!$stmt->execute()) {

    $error = $stmt->error;

    error_log(
        'ERROR INSERT mensajes: ' .
        $error
    );

    $stmt->close();

    respuestaJSON(
        false,
        '❌ Error al guardar el mensaje.'
    );
}


/* =========================================================
   ID DEL MENSAJE
========================================================= */

$idMensaje = $conexion->insert_id;

$stmt->close();


/* =========================================================
   OBTENER ADMINISTRADOR DESDE usuario_acceso
========================================================= */

/*
 * El correo NO está escrito manualmente.
 *
 * Se obtiene directamente de:
 *
 * usuario_acceso.email
 */

$sqlAdmin = "

    SELECT
        id_user,
        email,
        nombreEmpresa

    FROM usuario_acceso

    WHERE email IS NOT NULL
      AND TRIM(email) <> ''

    ORDER BY id_user ASC

    LIMIT 1

";


$resultAdmin = $conexion->query(
    $sqlAdmin
);


if (!$resultAdmin) {

    error_log(
        'ERROR obtener administrador: ' .
        $conexion->error
    );

    respuestaJSON(
        true,
        '✅ ¡Tu solicitud fue registrada correctamente! Nuestro equipo se pondrá en contacto contigo.',
        [
            'id_mensaje' => $idMensaje,
            'correo_enviado' => false
        ]
    );
}


$admin = $resultAdmin->fetch_assoc();


if (!$admin) {

    respuestaJSON(
        true,
        '✅ ¡Tu solicitud fue registrada correctamente! Nuestro equipo se pondrá en contacto contigo.',
        [
            'id_mensaje' => $idMensaje,
            'correo_enviado' => false
        ]
    );
}


/* =========================================================
   DATOS DEL ADMINISTRADOR
========================================================= */

$correoAdmin = trim(
    $admin['email'] ?? ''
);

$empresa = trim(
    $admin['nombreEmpresa'] ?? ''
);


if ($empresa === '') {
    $empresa = 'Inmobiliaria';
}


/* =========================================================
   VALIDAR CORREO DEL ADMINISTRADOR
========================================================= */

if (
    $correoAdmin === '' ||
    !filter_var(
        $correoAdmin,
        FILTER_VALIDATE_EMAIL
    )
) {

    error_log(
        'ERROR: correo administrador inválido: ' .
        $correoAdmin
    );

    respuestaJSON(
        true,
        '✅ ¡Tu solicitud fue registrada correctamente! Nuestro equipo se pondrá en contacto contigo.',
        [
            'id_mensaje' => $idMensaje,
            'correo_enviado' => false
        ]
    );
}


/* =========================================================
   PREPARAR DATOS PARA HTML
========================================================= */

$empresaHTML = htmlspecialchars(
    $empresa,
    ENT_QUOTES,
    'UTF-8'
);

$nombreHTML = htmlspecialchars(
    $nombre,
    ENT_QUOTES,
    'UTF-8'
);

$apellidosHTML = htmlspecialchars(
    $apellidos,
    ENT_QUOTES,
    'UTF-8'
);

$correoHTML = htmlspecialchars(
    $correo,
    ENT_QUOTES,
    'UTF-8'
);

$celularHTML = htmlspecialchars(
    $celular,
    ENT_QUOTES,
    'UTF-8'
);

$mensajeHTML = nl2br(
    htmlspecialchars(
        $mensajeTxt,
        ENT_QUOTES,
        'UTF-8'
    )
);

$nombrePropiedadHTML = htmlspecialchars(
    $propData['nombre'] ?? 'No especificada',
    ENT_QUOTES,
    'UTF-8'
);

$codigoPropiedadHTML = htmlspecialchars(
    $propData['codigo'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$ubicacionPropiedadHTML = htmlspecialchars(
    $propData['ubicacion'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);


/* =========================================================
   PRECIO
========================================================= */

$precioPropiedad = (float)(
    $propData['precio'] ?? 0
);

$precioPropiedadTexto =
    'S/ ' .
    number_format(
        $precioPropiedad,
        2,
        '.',
        ','
    );

$precioPropiedadHTML = htmlspecialchars(
    $precioPropiedadTexto,
    ENT_QUOTES,
    'UTF-8'
);


/* =========================================================
   ENVIAR CORREO
========================================================= */

$correoEnviado = false;


try {

    $mail = new PHPMailer(true);


    /* =====================================================
       CONFIGURACIÓN GENERAL
    ===================================================== */

    $mail->CharSet = 'UTF-8';

    $mail->Encoding = 'base64';


    /* =====================================================
       SMTP
    ===================================================== */

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;


    /*
     * =====================================================
     * REMITENTE / CUENTA ADMINISTRADOR
     * =====================================================
     *
     * IMPORTANTE:
     *
     * Aquí utilizamos el MISMO correo que está
     * registrado en usuario_acceso.email.
     *
     * Si usuario_acceso.email contiene:
     *
     * ventas.codevpro@gmail.com
     *
     * entonces esta cuenta será:
     *
     * - usuario SMTP
     * - remitente
     * - destinatario
     *
     */

    $mail->Username = $correoAdmin;


    /*
     * CONTRASEÑA DE APLICACIÓN
     *
     * Debe corresponder a la cuenta almacenada
     * en usuario_acceso.email.
     */

    $mail->Password =
        'crirojganmvdfqpj'; //Cambiar contraseña de aplicacion del Gmail


    /* =====================================================
       SEGURIDAD SMTP
    ===================================================== */

    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;


    /* =====================================================
       REMITENTE
    ===================================================== */

    /*
     * El remitente también es el administrador.
     */

    $mail->setFrom(
        $correoAdmin,
        $empresa
    );


    /* =====================================================
       DESTINATARIO
    ===================================================== */

    /*
     * El destinatario también es el administrador.
     *
     * Se obtiene de la BD.
     */

    $mail->addAddress(
        $correoAdmin,
        'Administrador'
    );


    /* =====================================================
       RESPONDER AL CLIENTE
    ===================================================== */

    /*
     * El cliente no es el remitente.
     *
     * Esto solamente permite que al pulsar
     * "Responder" se responda al cliente.
     */

    $mail->addReplyTo(
        $correo,
        $nombre . ' ' . $apellidos
    );


    /* =====================================================
       ASUNTO
    ===================================================== */

    $mail->isHTML(true);

    $mail->Subject =
        'Nueva solicitud de cotización - ' .
        $empresa;


    /* =====================================================
       CUERPO DEL CORREO
    ===================================================== */

    $mail->Body = "

    <!DOCTYPE html>

    <html lang='es'>

    <head>

        <meta charset='UTF-8'>

        <meta
            name='viewport'
            content='width=device-width, initial-scale=1.0'
        >

        <title>
            Nueva solicitud de cotización
        </title>

    </head>


    <body
        style='
            margin:0;
            padding:0;
            background:#f4f6f8;
            font-family:Arial,Helvetica,sans-serif;
            color:#333;
        '
    >

        <div
            style='
                max-width:700px;
                margin:30px auto;
                background:#ffffff;
                border-radius:10px;
                overflow:hidden;
                box-shadow:0 3px 15px rgba(0,0,0,.08);
            '
        >


            <!-- ENCABEZADO -->

            <div
                style='
                    background:#198754;
                    color:#ffffff;
                    padding:25px;
                '
            >

                <h2
                    style='
                        margin:0;
                        font-size:24px;
                    '
                >
                    {$empresaHTML}
                </h2>

                <p
                    style='
                        margin:8px 0 0;
                        font-size:14px;
                    '
                >
                    Nueva solicitud de cotización
                    desde la página web.
                </p>

            </div>


            <!-- CONTENIDO -->

            <div
                style='
                    padding:25px;
                    border:1px solid #ddd;
                    border-top:0;
                '
            >


                <!-- CLIENTE -->

                <h3
                    style='
                        color:#198754;
                        margin-top:0;
                    '
                >
                    Datos del cliente
                </h3>


                <table
                    width='100%'
                    cellpadding='8'
                    cellspacing='0'
                    style='
                        border-collapse:collapse;
                    '
                >

                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                width:150px;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Nombre:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                            '
                        >
                            {$nombreHTML}
                            {$apellidosHTML}
                        </td>

                    </tr>


                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Correo:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                            '
                        >
                            {$correoHTML}
                        </td>

                    </tr>


                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Celular:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                            '
                        >
                            {$celularHTML}
                        </td>

                    </tr>

                </table>


                <hr
                    style='
                        margin:25px 0;
                        border:0;
                        border-top:1px solid #ddd;
                    '
                >


                <!-- PROPIEDAD -->

                <h3
                    style='
                        color:#198754;
                    '
                >
                    Propiedad de interés
                </h3>


                <table
                    width='100%'
                    cellpadding='8'
                    cellspacing='0'
                    style='
                        border-collapse:collapse;
                    '
                >

                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                width:150px;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Propiedad:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                            '
                        >
                            {$nombrePropiedadHTML}
                        </td>

                    </tr>


                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Código:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                            '
                        >
                            {$codigoPropiedadHTML}
                        </td>

                    </tr>


                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Ubicación:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                            '
                        >
                            {$ubicacionPropiedadHTML}
                        </td>

                    </tr>


                    <tr>

                        <td
                            style='
                                font-weight:bold;
                                border-bottom:1px solid #eee;
                            '
                        >
                            Precio:
                        </td>

                        <td
                            style='
                                border-bottom:1px solid #eee;
                                color:#198754;
                                font-weight:bold;
                            '
                        >
                            {$precioPropiedadHTML}
                        </td>

                    </tr>

                </table>


                <hr
                    style='
                        margin:25px 0;
                        border:0;
                        border-top:1px solid #ddd;
                    '
                >


                <!-- MENSAJE -->

                <h3
                    style='
                        color:#198754;
                    '
                >
                    Mensaje del cliente
                </h3>


                <div
                    style='
                        background:#f8f9fa;
                        border-left:4px solid #198754;
                        padding:15px;
                        border-radius:6px;
                        line-height:1.6;
                    '
                >
                    {$mensajeHTML}
                </div>


                <!-- ID -->

                <p
                    style='
                        color:#888;
                        font-size:13px;
                        margin-top:25px;
                    '
                >
                    ID del mensaje registrado:
                    <strong>
                        #{$idMensaje}
                    </strong>
                </p>

            </div>


            <!-- PIE -->

            <div
                style='
                    background:#f8f9fa;
                    padding:15px;
                    text-align:center;
                    color:#777;
                    font-size:12px;
                '
            >

                Este mensaje fue generado automáticamente
                desde el sitio web.

            </div>


        </div>

    </body>

    </html>

    ";


    /* =====================================================
       VERSIÓN TEXTO
    ===================================================== */

    $mail->AltBody =

        "NUEVA SOLICITUD DE COTIZACIÓN\n\n"

        . "EMPRESA: "
        . $empresa
        . "\n\n"

        . "DATOS DEL CLIENTE\n"

        . "Nombre: "
        . $nombre
        . " "
        . $apellidos
        . "\n"

        . "Correo: "
        . $correo
        . "\n"

        . "Celular: "
        . $celular
        . "\n\n"

        . "PROPIEDAD DE INTERÉS\n"

        . "Propiedad: "
        . ($propData['nombre'] ?? '')
        . "\n"

        . "Código: "
        . ($propData['codigo'] ?? '')
        . "\n"

        . "Ubicación: "
        . ($propData['ubicacion'] ?? '')
        . "\n"

        . "Precio: S/ "
        . number_format(
            $precioPropiedad,
            2,
            '.',
            ','
        )
        . "\n\n"

        . "MENSAJE DEL CLIENTE\n"

        . $mensajeTxt
        . "\n\n"

        . "ID DEL MENSAJE: #"
        . $idMensaje;


    /* =====================================================
       ENVIAR CORREO
    ===================================================== */

    $mail->send();

    $correoEnviado = true;


} catch (Exception $e) {

    $correoEnviado = false;

    error_log(
        'ERROR PHPMailer - procesar_contacto.php: ' .
        $mail->ErrorInfo
    );
}


/* =========================================================
   RESPUESTA FINAL
========================================================= */

if ($correoEnviado) {

    respuestaJSON(
        true,
        '✅ ¡Solicitud enviada correctamente! Nuestro equipo se pondrá en contacto contigo.',
        [
            'id_mensaje' => $idMensaje,
            'correo_enviado' => true
        ]
    );

} else {

    respuestaJSON(
        true,
        '✅ ¡Tu solicitud fue registrada correctamente! Nuestro equipo se pondrá en contacto contigo.',
        [
            'id_mensaje' => $idMensaje,
            'correo_enviado' => false
        ]
    );
}