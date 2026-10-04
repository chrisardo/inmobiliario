<?php

/**
 * =========================================================
 * CoDevPro Technology
 * Archivo: ajax/eliminar_imagen_propiedad.php
 * Módulo: Propiedades
 * Función: Eliminar imagen de una propiedad
 *
 * TABLA:
 * imagenes
 *
 * Campos utilizados:
 * - id_imagen
 * - imagenes
 * - id_propiedad
 * - fecha_registro
 * - fecha_actualizado
 * - orden
 *
 * SEGURIDAD:
 * - Sesión obligatoria
 * - CSRF
 * - Verificación de propietario
 * - Verificación de pertenencia imagen/propiedad
 * - Prepared Statements
 * - Transacción
 *
 * RESPUESTA:
 * JSON
 * =========================================================
 */


/* =========================================================
   SESIÓN
========================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =========================================================
   RESPUESTA JSON
========================================================= */

header('Content-Type: application/json; charset=utf-8');


/* =========================================================
   FUNCIÓN RESPONDER
========================================================= */

function responder(
    bool $ok,
    string $mensaje = '',
    array $datos = []
): void {

    http_response_code(
        $ok ? 200 : 400
    );

    echo json_encode(
        array_merge(
            [
                'ok' => $ok,
                'mensaje' => $mensaje
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit();
}


/* =========================================================
   AUTENTICACIÓN
========================================================= */

if (!isset($_SESSION['usId'])) {

    http_response_code(401);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Sesión no válida. Inicia sesión nuevamente.'
    ], JSON_UNESCAPED_UNICODE);

    exit();
}


$usId = (int) $_SESSION['usId'];


/* =========================================================
   CONEXIÓN
========================================================= */

require_once '../controladores/conect_db.php';


if (
    !isset($conexion) ||
    !($conexion instanceof mysqli)
) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'No fue posible establecer la conexión con la base de datos.'
    ], JSON_UNESCAPED_UNICODE);

    exit();
}


/* =========================================================
   CONFIGURACIÓN MYSQLI
========================================================= */

mysqli_report(
    MYSQLI_REPORT_OFF
);


/* =========================================================
   MÉTODO
========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Método de solicitud no permitido.'
    ], JSON_UNESCAPED_UNICODE);

    exit();
}


/* =========================================================
   DATOS RECIBIDOS
========================================================= */

$idPropiedad = filter_input(
    INPUT_POST,
    'id_propiedad',
    FILTER_VALIDATE_INT
);

$idImagen = filter_input(
    INPUT_POST,
    'id_imagen',
    FILTER_VALIDATE_INT
);

$csrfRecibido = trim(
    (string) (
        $_POST['csrf_token'] ?? ''
    )
);


/* =========================================================
   VALIDAR ID PROPIEDAD
========================================================= */

if (
    $idPropiedad === false ||
    $idPropiedad === null ||
    (int) $idPropiedad <= 0
) {

    responder(
        false,
        'La propiedad seleccionada no es válida.'
    );
}

$idPropiedad = (int) $idPropiedad;


/* =========================================================
   VALIDAR ID IMAGEN
========================================================= */

if (
    $idImagen === false ||
    $idImagen === null ||
    (int) $idImagen <= 0
) {

    responder(
        false,
        'La imagen seleccionada no es válida.'
    );
}

$idImagen = (int) $idImagen;


/* =========================================================
   VALIDAR CSRF
========================================================= */

$csrfSesion =
    $_SESSION['csrf_propiedades'] ?? '';


if (
    empty($csrfSesion) ||
    empty($csrfRecibido) ||
    !hash_equals(
        (string) $csrfSesion,
        $csrfRecibido
    )
) {

    responder(
        false,
        'Token de seguridad inválido. Recarga la página e inténtalo nuevamente.'
    );
}


/* =========================================================
   VERIFICAR PROPIEDAD
========================================================= */

$sqlPropiedad = "
    SELECT
        id_propiedad
    FROM propiedades
    WHERE id_propiedad = ?
      AND id_user = ?
      AND (
            Eliminado = 0
            OR Eliminado IS NULL
          )
    LIMIT 1
";


$stmtPropiedad =
    $conexion->prepare(
        $sqlPropiedad
    );


if (!$stmtPropiedad) {

    responder(
        false,
        'No fue posible verificar la propiedad.'
    );
}


$stmtPropiedad->bind_param(
    "ii",
    $idPropiedad,
    $usId
);


if (!$stmtPropiedad->execute()) {

    $stmtPropiedad->close();

    responder(
        false,
        'No fue posible verificar la propiedad.'
    );
}


$resultadoPropiedad =
    $stmtPropiedad->get_result();


$propiedadExiste =
    $resultadoPropiedad &&
    $resultadoPropiedad->num_rows > 0;


$stmtPropiedad->close();


if (!$propiedadExiste) {

    responder(
        false,
        'La propiedad no existe o no tienes permiso para modificarla.'
    );
}


/* =========================================================
   VERIFICAR IMAGEN
========================================================= */

$sqlImagen = "
    SELECT
        id_imagen,
        id_propiedad,
        orden
    FROM imagenes
    WHERE id_imagen = ?
      AND id_propiedad = ?
    LIMIT 1
";


$stmtImagen =
    $conexion->prepare(
        $sqlImagen
    );


if (!$stmtImagen) {

    responder(
        false,
        'No fue posible verificar la imagen.'
    );
}


$stmtImagen->bind_param(
    "ii",
    $idImagen,
    $idPropiedad
);


if (!$stmtImagen->execute()) {

    $stmtImagen->close();

    responder(
        false,
        'No fue posible verificar la imagen.'
    );
}


$resultadoImagen =
    $stmtImagen->get_result();


$imagen =
    $resultadoImagen
        ? $resultadoImagen->fetch_assoc()
        : null;


$stmtImagen->close();


if (!$imagen) {

    responder(
        false,
        'La imagen no existe o no pertenece a esta propiedad.'
    );
}


/* =========================================================
   INICIAR TRANSACCIÓN
========================================================= */

if (!$conexion->begin_transaction()) {

    responder(
        false,
        'No fue posible iniciar la operación de eliminación.'
    );
}


try {

    /* =====================================================
       ELIMINAR IMAGEN
    ===================================================== */

    $sqlEliminar = "
        DELETE FROM imagenes
        WHERE id_imagen = ?
          AND id_propiedad = ?
        LIMIT 1
    ";


    $stmtEliminar =
        $conexion->prepare(
            $sqlEliminar
        );


    if (!$stmtEliminar) {

        throw new Exception(
            'No fue posible preparar la eliminación.'
        );
    }


    $stmtEliminar->bind_param(
        "ii",
        $idImagen,
        $idPropiedad
    );


    if (!$stmtEliminar->execute()) {

        $stmtEliminar->close();

        throw new Exception(
            'No fue posible eliminar la imagen.'
        );
    }


    /*
     * Verificar que realmente se eliminó
     * un registro.
     */

    $filasAfectadas =
        $stmtEliminar->affected_rows;


    $stmtEliminar->close();


    if ($filasAfectadas !== 1) {

        throw new Exception(
            'La imagen no pudo ser eliminada.'
        );
    }


    /* =====================================================
       REORDENAR IMÁGENES
    ===================================================== */

    $sqlObtenerImagenes = "
        SELECT
            id_imagen
        FROM imagenes
        WHERE id_propiedad = ?
        ORDER BY
            orden ASC,
            id_imagen ASC
    ";


    $stmtObtener =
        $conexion->prepare(
            $sqlObtenerImagenes
        );


    if (!$stmtObtener) {

        throw new Exception(
            'No fue posible reorganizar las imágenes.'
        );
    }


    $stmtObtener->bind_param(
        "i",
        $idPropiedad
    );


    if (!$stmtObtener->execute()) {

        $stmtObtener->close();

        throw new Exception(
            'No fue posible obtener las imágenes restantes.'
        );
    }


    $resultado =
        $stmtObtener->get_result();


    $idsImagenes = [];


    while (
        $fila =
            $resultado->fetch_assoc()
    ) {

        $idsImagenes[] =
            (int) $fila['id_imagen'];
    }


    $stmtObtener->close();


    /* =====================================================
       ACTUALIZAR ORDEN
    ===================================================== */

    if (!empty($idsImagenes)) {

        $sqlActualizarOrden = "
            UPDATE imagenes
            SET
                orden = ?,
                fecha_actualizado = CURDATE()
            WHERE id_imagen = ?
              AND id_propiedad = ?
        ";


        $stmtOrden =
            $conexion->prepare(
                $sqlActualizarOrden
            );


        if (!$stmtOrden) {

            throw new Exception(
                'No fue posible preparar la reorganización.'
            );
        }


        $orden = 1;


        foreach (
            $idsImagenes as $id
        ) {

            $stmtOrden->bind_param(
                "iii",
                $orden,
                $id,
                $idPropiedad
            );


            if (!$stmtOrden->execute()) {

                $stmtOrden->close();

                throw new Exception(
                    'No fue posible actualizar el orden de las imágenes.'
                );
            }


            $orden++;
        }


        $stmtOrden->close();
    }


    /* =====================================================
       OBTENER IMAGEN PRINCIPAL ACTUAL
       
       La principal será la que tenga:
       orden = 1
    ===================================================== */

    $sqlPrincipal = "
        SELECT
            imagenes
        FROM imagenes
        WHERE id_propiedad = ?
        ORDER BY
            orden ASC,
            id_imagen ASC
        LIMIT 1
    ";


    $stmtPrincipal =
        $conexion->prepare(
            $sqlPrincipal
        );


    if (!$stmtPrincipal) {

        throw new Exception(
            'No fue posible obtener la nueva imagen principal.'
        );
    }


    $stmtPrincipal->bind_param(
        "i",
        $idPropiedad
    );


    if (!$stmtPrincipal->execute()) {

        $stmtPrincipal->close();

        throw new Exception(
            'No fue posible obtener la nueva imagen principal.'
        );
    }


    $resultadoPrincipal =
        $stmtPrincipal->get_result();


    $filaPrincipal =
        $resultadoPrincipal
            ? $resultadoPrincipal->fetch_assoc()
            : null;


    $stmtPrincipal->close();


    /* =====================================================
       TOTAL DE IMÁGENES
    ===================================================== */

    $sqlTotal = "
        SELECT
            COUNT(*) AS total
        FROM imagenes
        WHERE id_propiedad = ?
    ";


    $stmtTotal =
        $conexion->prepare(
            $sqlTotal
        );


    if (!$stmtTotal) {

        throw new Exception(
            'No fue posible obtener el total de imágenes.'
        );
    }


    $stmtTotal->bind_param(
        "i",
        $idPropiedad
    );


    if (!$stmtTotal->execute()) {

        $stmtTotal->close();

        throw new Exception(
            'No fue posible obtener el total de imágenes.'
        );
    }


    $resultadoTotal =
        $stmtTotal->get_result();


    $filaTotal =
        $resultadoTotal
            ? $resultadoTotal->fetch_assoc()
            : null;


    $stmtTotal->close();


    $totalImagenes =
        (int) (
            $filaTotal['total'] ?? 0
        );


    /* =====================================================
       CONSTRUIR URL DE LA NUEVA PRINCIPAL
    ===================================================== */

    $imagenPrincipal = "";


    if (
        $filaPrincipal &&
        isset(
            $filaPrincipal['imagenes']
        ) &&
        $filaPrincipal['imagenes'] !== null
    ) {

        $binarioPrincipal =
            $filaPrincipal['imagenes'];


        if (
            is_string(
                $binarioPrincipal
            ) &&
            $binarioPrincipal !== ''
        ) {

            /*
             * Detectar MIME real.
             */

            $mimePrincipal =
                'image/jpeg';


            if (
                function_exists(
                    'finfo_open'
                )
            ) {

                $finfo =
                    finfo_open(
                        FILEINFO_MIME_TYPE
                    );


                if ($finfo) {

                    $mimeDetectado =
                        finfo_buffer(
                            $finfo,
                            $binarioPrincipal
                        );


                    finfo_close(
                        $finfo
                    );


                    if (
                        in_array(
                            $mimeDetectado,
                            [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/gif'
                            ],
                            true
                        )
                    ) {

                        $mimePrincipal =
                            $mimeDetectado;
                    }
                }
            }


            $imagenPrincipal =
                'data:' .
                $mimePrincipal .
                ';base64,' .
                base64_encode(
                    $binarioPrincipal
                );
        }
    }


    /* =====================================================
       CONFIRMAR TRANSACCIÓN
    ===================================================== */

    if (!$conexion->commit()) {

        throw new Exception(
            'No fue posible confirmar la eliminación.'
        );
    }


    /* =====================================================
       RESPUESTA EXITOSA
    ===================================================== */

    responder(
        true,
        $totalImagenes === 0
            ? 'La imagen fue eliminada correctamente. La propiedad ya no tiene imágenes.'
            : 'La imagen fue eliminada correctamente.',
        [
            'id_propiedad' =>
                $idPropiedad,

            'id_imagen_eliminada' =>
                $idImagen,

            'total_imagenes' =>
                $totalImagenes,

            'imagen_principal' =>
                $imagenPrincipal
        ]
    );


} catch (
    Throwable $e
) {

    /* =====================================================
       REVERTIR SI OCURRIÓ UN ERROR
    ===================================================== */

    $conexion->rollback();


    /*
     * Registrar el error real en el log del servidor,
     * sin mostrar información sensible al usuario.
     */

    error_log(
        'Error eliminar_imagen_propiedad.php: ' .
        $e->getMessage()
    );


    responder(
        false,
        'No fue posible eliminar la imagen. Inténtalo nuevamente.'
    );
}