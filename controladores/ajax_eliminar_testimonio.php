<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: controladores/ajax_eliminar_testimonio.php
// Módulo: Eliminar Testimonio
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
// CONEXIÓN
// ============================================================

include 'conect_db.php';

$idUser = (int) $_SESSION['usId'];

// ============================================================
// OBTENER ID DEL TESTIMONIO
// ============================================================

$idTestimonio = filter_input(
    INPUT_POST,
    'id_testimonio',
    FILTER_VALIDATE_INT
);

if (!$idTestimonio || $idTestimonio <= 0) {

    respuestaJson(
        false,
        'El identificador del testimonio no es válido.',
        400
    );
}

// ============================================================
// OBTENER TESTIMONIO
// ============================================================
//
// Primero verificamos que el testimonio exista
// y que pertenezca al usuario actualmente autenticado.
//
// También obtenemos el nombre del video para poder
// eliminar posteriormente el archivo físico.
//

$sqlBuscar = "
    SELECT
        id_testimonio,
        id_user,
        nombre,
        apellidos,
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
        'No fue posible consultar el testimonio: ' .
            $error,
        500
    );
}

$resultado = mysqli_stmt_get_result(
    $stmtBuscar
);

if (
    !$resultado ||
    mysqli_num_rows($resultado) === 0
) {

    mysqli_stmt_close($stmtBuscar);

    respuestaJson(
        false,
        'El testimonio no existe o no tienes permiso para eliminarlo.',
        404
    );
}

$testimonio = mysqli_fetch_assoc(
    $resultado
);

mysqli_stmt_close($stmtBuscar);

// ============================================================
// DATOS DEL TESTIMONIO
// ============================================================

$nombreCliente = trim(
    ($testimonio['nombre'] ?? '') .
        ' ' .
        ($testimonio['apellidos'] ?? '')
);

$videoAnterior = trim(
    (string) ($testimonio['video'] ?? '')
);

// ============================================================
// ELIMINAR REGISTRO DE LA BASE DE DATOS
// ============================================================
//
// Primero eliminamos el registro.
//
// El archivo físico del video se elimina después
// de que MySQL confirme correctamente la eliminación.
//

$sqlEliminar = "
    DELETE FROM testimonios
    WHERE
        id_testimonio = ?
        AND id_user = ?
    LIMIT 1
";

$stmtEliminar = mysqli_prepare(
    $conexion,
    $sqlEliminar
);

if (!$stmtEliminar) {

    respuestaJson(
        false,
        'No fue posible preparar la eliminación: ' .
            mysqli_error($conexion),
        500
    );
}

mysqli_stmt_bind_param(
    $stmtEliminar,
    'ii',
    $idTestimonio,
    $idUser
);

if (!mysqli_stmt_execute($stmtEliminar)) {

    $error = mysqli_stmt_error(
        $stmtEliminar
    );

    mysqli_stmt_close($stmtEliminar);

    respuestaJson(
        false,
        'No fue posible eliminar el testimonio de la base de datos: ' .
            $error,
        500
    );
}

// ============================================================
// VERIFICAR QUE SE ELIMINÓ
// ============================================================

$filasAfectadas = mysqli_stmt_affected_rows(
    $stmtEliminar
);

mysqli_stmt_close($stmtEliminar);

if ($filasAfectadas <= 0) {

    respuestaJson(
        false,
        'No se pudo eliminar el testimonio. Es posible que ya haya sido eliminado.',
        404
    );
}

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

// ============================================================
// ELIMINAR VIDEO FÍSICO
// ============================================================
//
// El campo video puede contener:
//
// 1. Solo nombre:
//    testimonio_1_abcd.mp4
//
// 2. Ruta Windows:
//    C:\wamp64\www\inmobiliaria\uploads\testimonios\videos\testimonio_1_abcd.mp4
//
// 3. Ruta relativa:
//    uploads/testimonios/videos/testimonio_1_abcd.mp4
//
// En todos los casos obtenemos solamente el nombre
// del archivo para construir la ruta física segura.
//

$videoEliminado = false;
$videoNoEncontrado = false;

if ($videoAnterior !== '') {

    // Normalizar separadores
    $videoNormalizado = str_replace(
        '\\',
        '/',
        $videoAnterior
    );

    // Obtener solamente el nombre del archivo
    $nombreArchivoVideo = basename(
        $videoNormalizado
    );

    if (
        $nombreArchivoVideo !== '' &&
        $nombreArchivoVideo !== '.' &&
        $nombreArchivoVideo !== '..'
    ) {

        $rutaVideo =
            $directorioVideos .
            DIRECTORY_SEPARATOR .
            $nombreArchivoVideo;

        // ----------------------------------------------------
        // ELIMINAR ARCHIVO
        // ----------------------------------------------------

        if (is_file($rutaVideo)) {

            if (@unlink($rutaVideo)) {

                $videoEliminado = true;
            } else {

                error_log(
                    'No se pudo eliminar el video del testimonio #' .
                        $idTestimonio .
                        ': ' .
                        $rutaVideo
                );
            }
        } else {

            $videoNoEncontrado = true;

            error_log(
                'El video del testimonio #' .
                    $idTestimonio .
                    ' no fue encontrado: ' .
                    $rutaVideo
            );
        }
    }
}

// ============================================================
// RESPUESTA FINAL
// ============================================================

respuestaJson(
    true,
    'El testimonio fue eliminado correctamente.',
    200,
    [
        'id_testimonio' => $idTestimonio,
        'nombre' => $nombreCliente,
        'video_anterior' => $videoAnterior,
        'video_eliminado' => $videoEliminado,
        'video_no_encontrado' => $videoNoEncontrado
    ]
);
