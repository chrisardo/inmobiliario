<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: controladores/ajax_editar_testimonio.php
// Módulo: Editar Testimonio
// ============================================================

session_start();

header('Content-Type: application/json; charset=utf-8');

// ============================================================
// FUNCIÓN RESPUESTA JSON
// ============================================================

function respuestaJson(
    bool $success,
    string $message,
    int $httpCode = 200,
    array $extra = []
): void {

    http_response_code($httpCode);

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
// VALIDAR SESIÓN
// ============================================================

if (!isset($_SESSION['usId'])) {

    respuestaJson(
        false,
        'La sesión ha expirado. Inicia sesión nuevamente.',
        401
    );
}

// ============================================================
// CONEXIÓN
// ============================================================

include 'conect_db.php';

$idUser = (int) $_SESSION['usId'];

// ============================================================
// VALIDAR MÉTODO
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    respuestaJson(
        false,
        'Método de solicitud no permitido.',
        405
    );
}

// ============================================================
// DATOS RECIBIDOS
// ============================================================

$idTestimonio = filter_input(
    INPUT_POST,
    'id_testimonio',
    FILTER_VALIDATE_INT
);

$nombre = trim(
    $_POST['nombre'] ?? ''
);

$apellidos = trim(
    $_POST['apellidos'] ?? ''
);

$comentario = trim(
    $_POST['comentario'] ?? ''
);

// ============================================================
// VALIDACIONES
// ============================================================

if (!$idTestimonio || $idTestimonio <= 0) {

    respuestaJson(
        false,
        'El identificador del testimonio no es válido.',
        400
    );
}

if ($nombre === '') {

    respuestaJson(
        false,
        'El nombre del cliente es obligatorio.',
        400
    );
}

if ($apellidos === '') {

    respuestaJson(
        false,
        'Los apellidos del cliente son obligatorios.',
        400
    );
}

if ($comentario === '') {

    respuestaJson(
        false,
        'El comentario del cliente es obligatorio.',
        400
    );
}

if (mb_strlen($nombre) > 100) {

    respuestaJson(
        false,
        'El nombre no puede superar los 100 caracteres.',
        400
    );
}

if (mb_strlen($apellidos) > 150) {

    respuestaJson(
        false,
        'Los apellidos no pueden superar los 150 caracteres.',
        400
    );
}

if (mb_strlen($comentario) > 1000) {

    respuestaJson(
        false,
        'El comentario no puede superar los 1000 caracteres.',
        400
    );
}

// ============================================================
// OBTENER TESTIMONIO ACTUAL
// ============================================================

$sqlBuscar = "
    SELECT
        id_testimonio,
        id_user,
        nombre,
        apellidos,
        comentario,
        imagen,
        video
    FROM testimonios
    WHERE
        id_testimonio = ?
        AND id_user = ?
    LIMIT 1
";

$stmtBuscar = mysqli_prepare(
    $conexion,
    $sqlBuscar
);

if (!$stmtBuscar) {

    respuestaJson(
        false,
        'No fue posible preparar la consulta del testimonio: ' .
        mysqli_error($conexion),
        500
    );
}

mysqli_stmt_bind_param(
    $stmtBuscar,
    'ii',
    $idTestimonio,
    $idUser
);

if (!mysqli_stmt_execute($stmtBuscar)) {

    $error = mysqli_stmt_error($stmtBuscar);

    mysqli_stmt_close($stmtBuscar);

    respuestaJson(
        false,
        'No fue posible consultar el testimonio: ' . $error,
        500
    );
}

$resultado = mysqli_stmt_get_result($stmtBuscar);

if (
    !$resultado ||
    mysqli_num_rows($resultado) === 0
) {

    mysqli_stmt_close($stmtBuscar);

    respuestaJson(
        false,
        'El testimonio no existe o no tienes permiso para modificarlo.',
        404
    );
}

$testimonioActual = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmtBuscar);

// ============================================================
// VIDEO ACTUAL
// ============================================================

$videoAnterior = trim(
    (string) ($testimonioActual['video'] ?? '')
);

// ============================================================
// DIRECTORIO DE VIDEOS
// ============================================================

$directorioVideos =
    dirname(__DIR__) .
    DIRECTORY_SEPARATOR .
    'uploads' .
    DIRECTORY_SEPARATOR .
    'testimonios' .
    DIRECTORY_SEPARATOR .
    'videos';

if (!is_dir($directorioVideos)) {

    if (!mkdir($directorioVideos, 0755, true)) {

        respuestaJson(
            false,
            'No fue posible crear el directorio de videos.',
            500
        );
    }
}

// ============================================================
// VARIABLES
// ============================================================

$imagenBinaria = null;

$actualizarImagen = false;
$actualizarVideo = false;
$eliminarVideo = false;

$videoNuevoNombre = '';
$rutaVideoNuevo = null;

// ============================================================
// DETECTAR ELIMINACIÓN DEL VIDEO
// ============================================================

if (
    isset($_POST['eliminar_video']) &&
    (string) $_POST['eliminar_video'] === '1'
) {

    $eliminarVideo = true;
}

// ============================================================
// SUBIR NUEVA IMAGEN
// ============================================================

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE
) {

    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {

        respuestaJson(
            false,
            'Ocurrió un error al subir la nueva imagen.',
            400
        );
    }

    $maximoImagen = 5 * 1024 * 1024;

    if ($_FILES['imagen']['size'] > $maximoImagen) {

        respuestaJson(
            false,
            'La imagen no puede superar los 5 MB.',
            400
        );
    }

    $finfoImagen = finfo_open(
        FILEINFO_MIME_TYPE
    );

    if (!$finfoImagen) {

        respuestaJson(
            false,
            'No fue posible validar la imagen.',
            500
        );
    }

    $mimeImagen = finfo_file(
        $finfoImagen,
        $_FILES['imagen']['tmp_name']
    );

    finfo_close($finfoImagen);

    $tiposImagenPermitidos = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (
        !in_array(
            $mimeImagen,
            $tiposImagenPermitidos,
            true
        )
    ) {

        respuestaJson(
            false,
            'La imagen debe estar en formato JPG, PNG o WEBP.',
            400
        );
    }

    $imagenBinaria = file_get_contents(
        $_FILES['imagen']['tmp_name']
    );

    if ($imagenBinaria === false) {

        respuestaJson(
            false,
            'No fue posible leer la nueva imagen.',
            500
        );
    }

    $actualizarImagen = true;
}

// ============================================================
// SUBIR NUEVO VIDEO
// ============================================================

if (
    isset($_FILES['video']) &&
    $_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE
) {

    if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) {

        respuestaJson(
            false,
            'Ocurrió un error al subir el nuevo video.',
            400
        );
    }

    $maximoVideo = 100 * 1024 * 1024;

    if ($_FILES['video']['size'] > $maximoVideo) {

        respuestaJson(
            false,
            'El video no puede superar los 100 MB.',
            400
        );
    }

    $finfoVideo = finfo_open(
        FILEINFO_MIME_TYPE
    );

    if (!$finfoVideo) {

        respuestaJson(
            false,
            'No fue posible validar el video.',
            500
        );
    }

    $mimeVideo = finfo_file(
        $finfoVideo,
        $_FILES['video']['tmp_name']
    );

    finfo_close($finfoVideo);

    $tiposVideoPermitidos = [
        'video/mp4',
        'video/webm',
        'video/ogg',
        'application/ogg'
    ];

    if (
        !in_array(
            $mimeVideo,
            $tiposVideoPermitidos,
            true
        )
    ) {

        respuestaJson(
            false,
            'El video debe estar en formato MP4, WEBM u OGG.',
            400
        );
    }

    $extensionOriginal = strtolower(
        pathinfo(
            $_FILES['video']['name'],
            PATHINFO_EXTENSION
        )
    );

    $extensionesPermitidas = [
        'mp4',
        'webm',
        'ogg'
    ];

    if (
        !in_array(
            $extensionOriginal,
            $extensionesPermitidas,
            true
        )
    ) {

        respuestaJson(
            false,
            'La extensión del video no es válida.',
            400
        );
    }

    $videoNuevoNombre =
        'testimonio_' .
        $idTestimonio .
        '_' .
        bin2hex(random_bytes(8)) .
        '.' .
        $extensionOriginal;

    $rutaVideoNuevo =
        $directorioVideos .
        DIRECTORY_SEPARATOR .
        $videoNuevoNombre;

    if (
        !move_uploaded_file(
            $_FILES['video']['tmp_name'],
            $rutaVideoNuevo
        )
    ) {

        respuestaJson(
            false,
            'No fue posible guardar el nuevo video.',
            500
        );
    }

    $actualizarVideo = true;

    /*
     * Si se sube un nuevo video,
     * el nuevo video tiene prioridad.
     */
    $eliminarVideo = false;
}

// ============================================================
// DETERMINAR ACCIÓN DEL VIDEO
// ============================================================
//
// IMPORTANTE:
//
// eliminar_video = 1
//      -> videoFinal = ''
//
// nuevo video
//      -> videoFinal = nombre nuevo
//
// sin cambios
//      -> videoFinal = video anterior
// ============================================================

$videoFinal = $videoAnterior;

if ($eliminarVideo) {

    /*
     * Usamos cadena vacía y NO NULL.
     *
     * Esto evita problemas si la columna video
     * está configurada como NOT NULL.
     */
    $videoFinal = '';
}

if ($actualizarVideo) {

    $videoFinal = $videoNuevoNombre;
}

// ============================================================
// FECHA
// ============================================================

$fechaActualizacion = date('Y-m-d');

// ============================================================
// ACTUALIZAR BASE DE DATOS
// ============================================================

if ($actualizarImagen) {

    $sqlUpdate = "
        UPDATE testimonios
        SET
            nombre = ?,
            apellidos = ?,
            comentario = ?,
            imagen = ?,
            video = ?,
            fecha_actualizado = ?
        WHERE
            id_testimonio = ?
            AND id_user = ?
    ";

    $stmtUpdate = mysqli_prepare(
        $conexion,
        $sqlUpdate
    );

    if (!$stmtUpdate) {

        if (
            $rutaVideoNuevo !== null &&
            is_file($rutaVideoNuevo)
        ) {

            @unlink($rutaVideoNuevo);
        }

        respuestaJson(
            false,
            'No fue posible preparar la actualización: ' .
            mysqli_error($conexion),
            500
        );
    }

    mysqli_stmt_bind_param(
        $stmtUpdate,
        'ssssssii',
        $nombre,
        $apellidos,
        $comentario,
        $imagenBinaria,
        $videoFinal,
        $fechaActualizacion,
        $idTestimonio,
        $idUser
    );

} else {

    $sqlUpdate = "
        UPDATE testimonios
        SET
            nombre = ?,
            apellidos = ?,
            comentario = ?,
            video = ?,
            fecha_actualizado = ?
        WHERE
            id_testimonio = ?
            AND id_user = ?
    ";

    $stmtUpdate = mysqli_prepare(
        $conexion,
        $sqlUpdate
    );

    if (!$stmtUpdate) {

        if (
            $rutaVideoNuevo !== null &&
            is_file($rutaVideoNuevo)
        ) {

            @unlink($rutaVideoNuevo);
        }

        respuestaJson(
            false,
            'No fue posible preparar la actualización: ' .
            mysqli_error($conexion),
            500
        );
    }

    mysqli_stmt_bind_param(
        $stmtUpdate,
        'sssssii',
        $nombre,
        $apellidos,
        $comentario,
        $videoFinal,
        $fechaActualizacion,
        $idTestimonio,
        $idUser
    );
}

// ============================================================
// EJECUTAR UPDATE
// ============================================================

if (!mysqli_stmt_execute($stmtUpdate)) {

    $errorMysql = mysqli_stmt_error(
        $stmtUpdate
    );

    mysqli_stmt_close($stmtUpdate);

    /*
     * Si se había subido un video nuevo
     * y el UPDATE falló, eliminamos el archivo nuevo.
     */
    if (
        $rutaVideoNuevo !== null &&
        is_file($rutaVideoNuevo)
    ) {

        @unlink($rutaVideoNuevo);
    }

    respuestaJson(
        false,
        'No fue posible actualizar el testimonio: ' .
        $errorMysql,
        500
    );
}

mysqli_stmt_close($stmtUpdate);

// ============================================================
// ELIMINAR FÍSICAMENTE EL VIDEO ANTERIOR
// ============================================================
//
// Se ejecuta DESPUÉS de actualizar correctamente
// la base de datos.
//
// Esto evita eliminar el archivo si el UPDATE
// de MySQL falla.
// ============================================================

if (
    $videoAnterior !== '' &&
    (
        $eliminarVideo ||
        $actualizarVideo
    )
) {

    /*
     * Normalizar separadores.
     *
     * Ejemplo:
     *
     * C:\wamp64\www\inmobiliaria\uploads\testimonios\videos\cliente.mp4
     *
     * se convierte en:
     *
     * C:/wamp64/www/inmobiliaria/uploads/testimonios/videos/cliente.mp4
     */

    $videoAnteriorNormalizado = str_replace(
        '\\',
        '/',
        $videoAnterior
    );

    /*
     * Obtener únicamente el nombre.
     */
    $videoAnteriorNombre = basename(
        $videoAnteriorNormalizado
    );

    if (
        $videoAnteriorNombre !== '' &&
        $videoAnteriorNombre !== '.' &&
        $videoAnteriorNombre !== '..'
    ) {

        $rutaVideoAnterior =
            $directorioVideos .
            DIRECTORY_SEPARATOR .
            $videoAnteriorNombre;

        /*
         * Eliminar archivo físico.
         */
        if (is_file($rutaVideoAnterior)) {

            if (!@unlink($rutaVideoAnterior)) {

                error_log(
                    'No se pudo eliminar el video anterior: ' .
                    $rutaVideoAnterior
                );
            }
        }
    }
}

// ============================================================
// RESPUESTA FINAL
// ============================================================

respuestaJson(
    true,
    $eliminarVideo
        ? 'El video del testimonio fue eliminado correctamente.'
        : 'El testimonio fue actualizado correctamente.',
    200,
    [
        'testimonio' => [
            'id_testimonio' => $idTestimonio,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'comentario' => $comentario,
            'fecha_actualizado' => $fechaActualizacion,
            'video' => $videoFinal
        ]
    ]
);
?>
