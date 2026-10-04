<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: controladores/ajax_obtener_testimonio.php
// Módulo: Obtener Testimonio
// ============================================================

session_start();


// ============================================================
// RESPUESTA JSON
// ============================================================

header('Content-Type: application/json; charset=utf-8');


// ============================================================
// VALIDAR SESIÓN
// ============================================================

if (!isset($_SESSION['usId'])) {

    http_response_code(401);

    echo json_encode(
        [
            'success' => false,
            'message' => 'La sesión ha expirado. Inicia sesión nuevamente.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


// ============================================================
// CONEXIÓN
// ============================================================

include 'conect_db.php';


// ============================================================
// ID DEL USUARIO
// ============================================================

$idUser = (int) $_SESSION['usId'];


// ============================================================
// OBTENER ID DEL TESTIMONIO
// ============================================================

$idTestimonio = 0;

if (isset($_GET['id'])) {

    $idTestimonio = filter_var(
        $_GET['id'],
        FILTER_VALIDATE_INT
    );
}


// ============================================================
// VALIDAR ID
// ============================================================

if (
    !$idTestimonio ||
    $idTestimonio <= 0
) {

    http_response_code(400);

    echo json_encode(
        [
            'success' => false,
            'message' => 'El identificador del testimonio no es válido.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


// ============================================================
// CONSULTAR TESTIMONIO
// ============================================================

$sql = "
    SELECT
        id_testimonio,
        id_user,
        comentario,
        fecha_registro,
        nombre,
        apellidos,
        fecha_actualizado,
        imagen,
        video
    FROM testimonios
    WHERE
        id_testimonio = ?
        AND id_user = ?
    LIMIT 1
";


$stmt = mysqli_prepare(
    $conexion,
    $sql
);


if (!$stmt) {

    http_response_code(500);

    echo json_encode(
        [
            'success' => false,
            'message' => 'No fue posible preparar la consulta del testimonio.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


// ============================================================
// PARÁMETROS
// ============================================================

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $idTestimonio,
    $idUser
);


// ============================================================
// EJECUTAR
// ============================================================

if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    http_response_code(500);

    echo json_encode(
        [
            'success' => false,
            'message' => 'No fue posible consultar el testimonio.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


// ============================================================
// RESULTADO
// ============================================================

$resultado = mysqli_stmt_get_result($stmt);


if (
    !$resultado ||
    mysqli_num_rows($resultado) === 0
) {

    mysqli_stmt_close($stmt);

    http_response_code(404);

    echo json_encode(
        [
            'success' => false,
            'message' => 'El testimonio no existe o no tienes permiso para consultarlo.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


// ============================================================
// DATOS
// ============================================================

$testimonio = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);


// ============================================================
// NOMBRE COMPLETO
// ============================================================

$nombre = trim(
    $testimonio['nombre'] ?? ''
);

$apellidos = trim(
    $testimonio['apellidos'] ?? ''
);

$nombreCompleto = trim(
    $nombre . ' ' . $apellidos
);

if ($nombreCompleto === '') {

    $nombreCompleto = 'Cliente';
}


// ============================================================
// FORMATEAR FECHA
// ============================================================

$fecha = $testimonio['fecha_registro'] ?? null;

if (!empty($fecha)) {

    $fechaObjeto = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    if ($fechaObjeto) {

        $fecha = $fechaObjeto->format(
            'd/m/Y'
        );
    }
}


// ============================================================
// IMAGEN
// ============================================================

$imagenBase64 = null;

if (
    isset($testimonio['imagen']) &&
    $testimonio['imagen'] !== null &&
    $testimonio['imagen'] !== ''
) {

    $imagenBinaria = $testimonio['imagen'];

    $mime = 'image/jpeg';

    if (function_exists('finfo_open')) {

        $finfo = finfo_open(
            FILEINFO_MIME_TYPE
        );

        if ($finfo) {

            $mimeDetectado = finfo_buffer(
                $finfo,
                $imagenBinaria
            );

            if (
                is_string($mimeDetectado) &&
                strpos(
                    $mimeDetectado,
                    'image/'
                ) === 0
            ) {

                $mime = $mimeDetectado;
            }

            finfo_close($finfo);
        }
    }

    $imagenBase64 =
        'data:' .
        $mime .
        ';base64,' .
        base64_encode(
            $imagenBinaria
        );
}


// ============================================================
// VIDEO
// ============================================================
//
// La BD tiene VARCHAR.
//
// Ejemplo almacenado:
//     video_cliente.mp4
//
// Se transforma en:
//     ../uploads/testimonios/videos/video_cliente.mp4
//
// IMPORTANTE:
// Nunca se utiliza directamente una ruta física:
// C:\wamp64\www\inmobiliaria\...
//
// ============================================================

$videoBD = trim(
    $testimonio['video'] ?? ''
);

$video = null;

if ($videoBD !== '') {

    // --------------------------------------------------------
    // Convertir barras de Windows a /
    // --------------------------------------------------------

    $videoBD = str_replace(
        '\\',
        '/',
        $videoBD
    );


    // --------------------------------------------------------
    // Si por error se guardó una ruta física completa,
    // nos quedamos solamente con el nombre del archivo.
    // --------------------------------------------------------

    if (
        strpos($videoBD, 'C:/') === 0 ||
        strpos($videoBD, 'c:/') === 0 ||
        strpos($videoBD, 'C:\\') === 0 ||
        strpos($videoBD, 'c:\\') === 0
    ) {

        $videoBD = basename(
            $videoBD
        );
    }


    // --------------------------------------------------------
    // Si contiene uploads/testimonios/videos/,
    // extraemos solamente el nombre del archivo.
    // --------------------------------------------------------

    if (
        strpos(
            $videoBD,
            'uploads/testimonios/videos/'
        ) !== false
    ) {

        $videoBD = basename(
            $videoBD
        );
    }


    // --------------------------------------------------------
    // Si contiene solamente una ruta relativa,
    // también nos quedamos con el archivo.
    // --------------------------------------------------------

    $nombreVideo = basename(
        $videoBD
    );


    // --------------------------------------------------------
    // Validar que exista un nombre de archivo
    // --------------------------------------------------------

    if ($nombreVideo !== '') {

        // ----------------------------------------------------
        // Codificar correctamente el nombre del archivo
        // ----------------------------------------------------

        $nombreVideoURL = rawurlencode(
            $nombreVideo
        );


        // ----------------------------------------------------
        // URL WEB DEL VIDEO
        // ----------------------------------------------------

        $video =
            '../uploads/testimonios/videos/' .
            $nombreVideoURL;
    }
}


// ============================================================
// RESPUESTA JSON
// ============================================================

echo json_encode(
    [
        'success' => true,

        'message' =>
        'Testimonio obtenido correctamente.',

        'testimonio' => [

            'id_testimonio' =>
            (int) $testimonio['id_testimonio'],

            'id_user' =>
            (int) $testimonio['id_user'],

            'nombre' =>
            $nombre,

            'apellidos' =>
            $apellidos,

            'nombreCompleto' =>
            $nombreCompleto,

            'comentario' =>
            $testimonio['comentario'] ?? '',

            'fecha' =>
            $fecha,

            'fecha_registro' =>
            $testimonio['fecha_registro'] ?? null,

            'fecha_actualizado' =>
            $testimonio['fecha_actualizado'] ?? null,

            'imagen' =>
            $imagenBase64,

            'video' =>
            $video,

            // También enviamos el valor original
            // para poder comprobarlo durante pruebas.
            'video_original' =>
            $videoBD !== '' ? $videoBD : null
        ]
    ],
    JSON_UNESCAPED_UNICODE
);

exit();
