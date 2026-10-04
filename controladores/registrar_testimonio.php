<?php
// ============================================================
// Sistema Inmobiliario Iquitos
// Archivo: controladores/registrar_testimonio.php
// Módulo: Registrar Testimonio - AJAX
// ============================================================

session_start();

header('Content-Type: application/json; charset=utf-8');


// ============================================================
// FUNCIÓN RESPUESTA JSON
// ============================================================

function respuesta($success, $message, $extra = [])
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

    exit();
}


// ============================================================
// CONFIGURACIÓN
// ============================================================

$MAX_TESTIMONIOS = 4;

$MAX_VIDEO_SIZE =
    300 * 1024 * 1024;

$MAX_IMAGEN_SIZE =
    5 * 1024 * 1024;


// ============================================================
// VERIFICAR SESIÓN
// ============================================================

if (!isset($_SESSION['usId'])) {

    respuesta(
        false,
        'La sesión ha expirado. Inicia sesión nuevamente.'
    );
}

$idUser =
    (int) $_SESSION['usId'];


// ============================================================
// VERIFICAR MÉTODO POST
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {

    respuesta(
        false,
        'Método de solicitud no permitido.'
    );
}


// ============================================================
// DETECTAR SOLICITUD DEMASIADO GRANDE
// ============================================================
//
// Si post_max_size es menor al tamaño enviado,
// PHP puede dejar $_POST y $_FILES vacíos.
// ============================================================

$contentLength =
    isset($_SERVER['CONTENT_LENGTH'])
    ? (int) $_SERVER['CONTENT_LENGTH']
    : 0;


if (
    $contentLength > 0 &&
    empty($_POST) &&
    empty($_FILES)
) {

    respuesta(
        false,
        'La solicitud es demasiado grande. Verifica que el servidor permita cargas de hasta 300 MB.'
    );
}


// ============================================================
// CONEXIÓN
// ============================================================

require_once __DIR__ . '/conect_db.php';


// ============================================================
// VERIFICAR CONEXIÓN
// ============================================================

if (
    !isset($conexion) ||
    !$conexion
) {

    respuesta(
        false,
        'No fue posible establecer conexión con la base de datos.'
    );
}


// ============================================================
// CSRF
// ============================================================

$csrfToken =
    $_POST['csrf_token'] ?? '';


if (
    empty($csrfToken) ||
    empty($_SESSION['csrf_testimonio']) ||
    !hash_equals(
        $_SESSION['csrf_testimonio'],
        $csrfToken
    )
) {

    respuesta(
        false,
        'Token de seguridad inválido. Recarga la página e inténtalo nuevamente.'
    );
}


// ============================================================
// VERIFICAR LÍMITE DE TESTIMONIOS
// ============================================================
//
// Solo se permiten 4 testimonios.
// La validación se realiza en el servidor para evitar
// que alguien pueda registrar un quinto testimonio
// manipulando JavaScript.
// ============================================================

$sqlContador = "
    SELECT COUNT(*) AS total
    FROM testimonios
    WHERE id_user = ?
";


$stmtContador =
    mysqli_prepare(
        $conexion,
        $sqlContador
    );


if (!$stmtContador) {

    error_log(
        'Error preparar contador testimonios: ' .
            mysqli_error($conexion)
    );

    respuesta(
        false,
        'No fue posible verificar el límite de testimonios.'
    );
}


mysqli_stmt_bind_param(
    $stmtContador,
    "i",
    $idUser
);


if (
    !mysqli_stmt_execute(
        $stmtContador
    )
) {

    error_log(
        'Error ejecutar contador testimonios: ' .
            mysqli_stmt_error($stmtContador)
    );

    mysqli_stmt_close(
        $stmtContador
    );

    respuesta(
        false,
        'No fue posible verificar los testimonios registrados.'
    );
}


$resultadoContador =
    mysqli_stmt_get_result(
        $stmtContador
    );


$filaContador =
    mysqli_fetch_assoc(
        $resultadoContador
    );


$totalTestimonios =
    (int) (
        $filaContador['total'] ?? 0
    );


mysqli_stmt_close(
    $stmtContador
);


// ============================================================
// BLOQUEAR SI YA EXISTEN 4
// ============================================================

if (
    $totalTestimonios >=
    $MAX_TESTIMONIOS
) {

    respuesta(
        false,
        'Ya se han registrado los 4 testimonios permitidos. No es posible registrar un quinto testimonio.',
        [
            'total_testimonios' =>
            $totalTestimonios,

            'max_testimonios' =>
            $MAX_TESTIMONIOS
        ]
    );
}


// ============================================================
// DATOS DEL FORMULARIO
// ============================================================

$nombre =
    trim(
        $_POST['nombre'] ?? ''
    );


$apellidos =
    trim(
        $_POST['apellidos'] ?? ''
    );


$comentario =
    trim(
        $_POST['comentario'] ?? ''
    );


// ============================================================
// VALIDAR NOMBRE
// ============================================================

if (
    $nombre === ''
) {

    respuesta(
        false,
        'El nombre es obligatorio.'
    );
}


if (
    mb_strlen($nombre) > 100
) {

    respuesta(
        false,
        'El nombre no puede superar los 100 caracteres.'
    );
}


// ============================================================
// VALIDAR APELLIDOS
// ============================================================

if (
    $apellidos === ''
) {

    respuesta(
        false,
        'Los apellidos son obligatorios.'
    );
}


if (
    mb_strlen($apellidos) > 150
) {

    respuesta(
        false,
        'Los apellidos no pueden superar los 150 caracteres.'
    );
}


// ============================================================
// VALIDAR COMENTARIO
// ============================================================

if (
    $comentario === ''
) {

    respuesta(
        false,
        'El testimonio es obligatorio.'
    );
}


if (
    mb_strlen($comentario) > 2000
) {

    respuesta(
        false,
        'El testimonio no puede superar los 2000 caracteres.'
    );
}


// ============================================================
// VARIABLES
// ============================================================

$imagenBinaria =
    null;

$rutaVideo =
    '';

$rutaFisicaVideo =
    null;


// ============================================================
// CARPETAS
// ============================================================

$carpetaBase =
    dirname(__DIR__) .
    DIRECTORY_SEPARATOR .
    'uploads';


$carpetaTestimonios =
    $carpetaBase .
    DIRECTORY_SEPARATOR .
    'testimonios';


$carpetaVideos =
    $carpetaTestimonios .
    DIRECTORY_SEPARATOR .
    'videos';


// ============================================================
// CREAR CARPETA DE VIDEOS
// ============================================================

if (
    !is_dir($carpetaVideos)
) {

    if (
        !mkdir(
            $carpetaVideos,
            0755,
            true
        )
    ) {

        respuesta(
            false,
            'No fue posible crear la carpeta donde se almacenarán los videos.'
        );
    }
}


// ============================================================
// VERIFICAR PERMISOS
// ============================================================

if (
    !is_writable($carpetaVideos)
) {

    respuesta(
        false,
        'La carpeta de videos no tiene permisos de escritura.'
    );
}


// ============================================================
// PROCESAR IMAGEN
// ============================================================

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] !==
    UPLOAD_ERR_NO_FILE
) {

    // --------------------------------------------------------
    // ERROR DE SUBIDA
    // --------------------------------------------------------

    if (
        $_FILES['imagen']['error'] !==
        UPLOAD_ERR_OK
    ) {

        switch ($_FILES['imagen']['error']) {

            case UPLOAD_ERR_INI_SIZE:

            case UPLOAD_ERR_FORM_SIZE:

                respuesta(
                    false,
                    'La imagen supera el tamaño permitido por el servidor.'
                );

                break;


            case UPLOAD_ERR_PARTIAL:

                respuesta(
                    false,
                    'La imagen se subió parcialmente. Inténtalo nuevamente.'
                );

                break;


            case UPLOAD_ERR_NO_TMP_DIR:

                respuesta(
                    false,
                    'El servidor no tiene configurada la carpeta temporal para archivos.'
                );

                break;


            case UPLOAD_ERR_CANT_WRITE:

                respuesta(
                    false,
                    'El servidor no pudo guardar temporalmente la imagen.'
                );

                break;


            case UPLOAD_ERR_EXTENSION:

                respuesta(
                    false,
                    'Una extensión de PHP detuvo la subida de la imagen.'
                );

                break;


            default:

                respuesta(
                    false,
                    'Ocurrió un error al subir la imagen.'
                );
        }
    }


    // --------------------------------------------------------
    // TAMAÑO
    // --------------------------------------------------------

    if (
        $_FILES['imagen']['size'] >
        $MAX_IMAGEN_SIZE
    ) {

        respuesta(
            false,
            'La imagen no puede superar los 5 MB.'
        );
    }


    // --------------------------------------------------------
    // ARCHIVO TEMPORAL
    // --------------------------------------------------------

    if (
        empty($_FILES['imagen']['tmp_name']) ||
        !is_uploaded_file(
            $_FILES['imagen']['tmp_name']
        )
    ) {

        respuesta(
            false,
            'El archivo de imagen no es válido.'
        );
    }


    // --------------------------------------------------------
    // MIME REAL
    // --------------------------------------------------------

    $finfoImagen =
        new finfo(
            FILEINFO_MIME_TYPE
        );


    $mimeImagen =
        $finfoImagen->file(
            $_FILES['imagen']['tmp_name']
        );


    // --------------------------------------------------------
    // TIPOS PERMITIDOS
    // --------------------------------------------------------

    $allowedMimeImagen = [

        'image/jpeg',

        'image/png',

        'image/webp'

    ];


    if (
        !in_array(
            $mimeImagen,
            $allowedMimeImagen,
            true
        )
    ) {

        respuesta(
            false,
            'El formato de la imagen no está permitido.'
        );
    }


    // --------------------------------------------------------
    // COMPROBAR IMAGEN REAL
    // --------------------------------------------------------

    if (
        @getimagesize(
            $_FILES['imagen']['tmp_name']
        ) === false
    ) {

        respuesta(
            false,
            'El archivo seleccionado no es una imagen válida.'
        );
    }


    // --------------------------------------------------------
    // LEER IMAGEN
    // --------------------------------------------------------

    $imagenBinaria =
        file_get_contents(
            $_FILES['imagen']['tmp_name']
        );


    if (
        $imagenBinaria === false
    ) {

        respuesta(
            false,
            'No fue posible procesar la imagen.'
        );
    }
}


// ============================================================
// PROCESAR VIDEO
// ============================================================

if (
    isset($_FILES['video']) &&
    $_FILES['video']['error'] !==
    UPLOAD_ERR_NO_FILE
) {

    // --------------------------------------------------------
    // ERROR DE SUBIDA
    // --------------------------------------------------------

    if (
        $_FILES['video']['error'] !==
        UPLOAD_ERR_OK
    ) {

        switch ($_FILES['video']['error']) {

            case UPLOAD_ERR_INI_SIZE:

                respuesta(
                    false,
                    'El video supera el tamaño máximo permitido por PHP en el servidor.'
                );

                break;


            case UPLOAD_ERR_FORM_SIZE:

                respuesta(
                    false,
                    'El video supera el tamaño máximo permitido por el formulario.'
                );

                break;


            case UPLOAD_ERR_PARTIAL:

                respuesta(
                    false,
                    'El video se subió parcialmente. Inténtalo nuevamente.'
                );

                break;


            case UPLOAD_ERR_NO_TMP_DIR:

                respuesta(
                    false,
                    'El servidor no tiene configurada la carpeta temporal para archivos.'
                );

                break;


            case UPLOAD_ERR_CANT_WRITE:

                respuesta(
                    false,
                    'El servidor no pudo escribir temporalmente el video.'
                );

                break;


            case UPLOAD_ERR_EXTENSION:

                respuesta(
                    false,
                    'Una extensión de PHP detuvo la subida del video.'
                );

                break;


            default:

                respuesta(
                    false,
                    'Ocurrió un error al subir el video.'
                );
        }
    }


    // --------------------------------------------------------
    // MÁXIMO 300 MB
    // --------------------------------------------------------

    if (
        $_FILES['video']['size'] >
        $MAX_VIDEO_SIZE
    ) {

        respuesta(
            false,
            'El video no puede superar los 300 MB.'
        );
    }


    // --------------------------------------------------------
    // ARCHIVO TEMPORAL
    // --------------------------------------------------------

    if (
        empty($_FILES['video']['tmp_name']) ||
        !is_uploaded_file(
            $_FILES['video']['tmp_name']
        )
    ) {

        respuesta(
            false,
            'El archivo de video no es válido.'
        );
    }


    // --------------------------------------------------------
    // MIME REAL
    // --------------------------------------------------------

    $finfoVideo =
        new finfo(
            FILEINFO_MIME_TYPE
        );


    $mimeVideo =
        $finfoVideo->file(
            $_FILES['video']['tmp_name']
        );


    // --------------------------------------------------------
    // FORMATOS PERMITIDOS
    // --------------------------------------------------------

    $allowedVideos = [

        'video/mp4' =>
        'mp4',

        'video/webm' =>
        'webm',

        'video/ogg' =>
        'ogg',

        'application/ogg' =>
        'ogg'

    ];


    // --------------------------------------------------------
    // VALIDAR MIME
    // --------------------------------------------------------

    if (
        !array_key_exists(
            $mimeVideo,
            $allowedVideos
        )
    ) {

        respuesta(
            false,
            'El formato del video no está permitido. Solo se permiten MP4, WEBM u OGG.'
        );
    }


    // --------------------------------------------------------
    // EXTENSIÓN SEGURA
    // --------------------------------------------------------

    $extensionVideo =
        $allowedVideos[$mimeVideo];


    // --------------------------------------------------------
    // GENERAR NOMBRE ÚNICO
    // --------------------------------------------------------

    try {

        $nombreVideo =
            'testimonio_' .
            bin2hex(
                random_bytes(16)
            ) .
            '.' .
            $extensionVideo;
    } catch (
        Exception $e
    ) {

        respuesta(
            false,
            'No fue posible generar un nombre seguro para el video.'
        );
    }


    // --------------------------------------------------------
    // RUTA FÍSICA
    // --------------------------------------------------------

    $rutaFisicaVideo =
        $carpetaVideos .
        DIRECTORY_SEPARATOR .
        $nombreVideo;


    // --------------------------------------------------------
    // MOVER VIDEO
    // --------------------------------------------------------

    if (
        !move_uploaded_file(
            $_FILES['video']['tmp_name'],
            $rutaFisicaVideo
        )
    ) {

        respuesta(
            false,
            'No fue posible guardar el video en el servidor.'
        );
    }


    // --------------------------------------------------------
    // RUTA PARA MYSQL
    // --------------------------------------------------------

    $rutaVideo =
        'uploads/testimonios/videos/' .
        $nombreVideo;
}


// ============================================================
// FECHA
// ============================================================

$fechaRegistro =
    date('Y-m-d');


// ============================================================
// INSERTAR TESTIMONIO
// ============================================================

$sql = "
    INSERT INTO testimonios
    (
        id_user,
        comentario,
        fecha_registro,
        nombre,
        apellidos,
        fecha_actualizado,
        imagen,
        video
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )
";


$stmt =
    mysqli_prepare(
        $conexion,
        $sql
    );


// ============================================================
// VERIFICAR PREPARACIÓN
// ============================================================

if (!$stmt) {

    // --------------------------------------------------------
    // ELIMINAR VIDEO
    // --------------------------------------------------------

    if (
        !empty($rutaFisicaVideo) &&
        file_exists($rutaFisicaVideo)
    ) {

        @unlink(
            $rutaFisicaVideo
        );
    }


    error_log(
        'Error preparar registrar_testimonio: ' .
            mysqli_error($conexion)
    );


    respuesta(
        false,
        'No fue posible preparar el registro del testimonio.'
    );
}


// ============================================================
// BIND
// ============================================================

mysqli_stmt_bind_param(
    $stmt,
    "isssssss",
    $idUser,
    $comentario,
    $fechaRegistro,
    $nombre,
    $apellidos,
    $fechaRegistro,
    $imagenBinaria,
    $rutaVideo
);


// ============================================================
// EJECUTAR
// ============================================================

if (
    !mysqli_stmt_execute($stmt)
) {

    $error =
        mysqli_stmt_error($stmt);


    mysqli_stmt_close(
        $stmt
    );


    // --------------------------------------------------------
    // ELIMINAR VIDEO SI FALLÓ EL INSERT
    // --------------------------------------------------------

    if (
        !empty($rutaFisicaVideo) &&
        file_exists($rutaFisicaVideo)
    ) {

        @unlink(
            $rutaFisicaVideo
        );
    }


    error_log(
        'Error registrar_testimonio: ' .
            $error
    );


    respuesta(
        false,
        'No fue posible registrar el testimonio.'
    );
}


// ============================================================
// ID GENERADO
// ============================================================

$idTestimonio =
    mysqli_insert_id(
        $conexion
    );


// ============================================================
// CERRAR
// ============================================================

mysqli_stmt_close(
    $stmt
);


// ============================================================
// CONTAR NUEVAMENTE
// ============================================================
//
// Esto permite informar al JavaScript cuántos testimonios
// quedan disponibles.
// ============================================================

$totalDespues =
    $totalTestimonios + 1;


$restantes =
    max(
        0,
        $MAX_TESTIMONIOS -
            $totalDespues
    );


// ============================================================
// RESPUESTA EXITOSA
// ============================================================

respuesta(
    true,
    'El testimonio se registró correctamente.',
    [

        'id_testimonio' =>
        $idTestimonio,

        'video' =>
        $rutaVideo,

        'total_testimonios' =>
        $totalDespues,

        'max_testimonios' =>
        $MAX_TESTIMONIOS,

        'testimonios_restantes' =>
        $restantes

    ]
);
