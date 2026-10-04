<?php

/**
 * =========================================================
 * CoDevPro Technology
 * Archivo: controladores/editar_imagenes_propiedad.php
 * Módulo: Propiedades
 *
 * Funciones:
 *   - listar imágenes
 *   - subir imágenes
 *   - cambiar imagen
 *   - eliminar imagen
 *   - establecer imagen principal
 * =========================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header(
    'Content-Type: application/json; charset=utf-8'
);


/* =========================================================
   AUTENTICACIÓN
========================================================= */

if (!isset($_SESSION['usId'])) {

    http_response_code(401);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Sesión no válida.'
    ], JSON_UNESCAPED_UNICODE);

    exit();
}


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
        'mensaje' =>
        'No se pudo establecer la conexión con la base de datos.'
    ], JSON_UNESCAPED_UNICODE);

    exit();
}


$conexion->set_charset('utf8mb4');


$usId =
    (int) ($_SESSION['usId'] ?? 0);


/* =========================================================
   CSRF
========================================================= */

if (empty($_SESSION['csrf_propiedades'])) {

    $_SESSION['csrf_propiedades'] =
        bin2hex(
            random_bytes(32)
        );
}


$csrfToken =
    $_SESSION['csrf_propiedades'];


/* =========================================================
   FUNCIÓN RESPUESTA
========================================================= */

function responder(
    bool $ok,
    string $mensaje = '',
    array $datos = []
): void {

    echo json_encode(
        array_merge(
            [
                'ok' => $ok,
                'mensaje' => $mensaje
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


/* =========================================================
   VERIFICAR PROPIEDAD
========================================================= */

function verificarPropiedad(
    mysqli $conexion,
    int $idPropiedad,
    int $usId
): bool {

    $sql = "
        SELECT id_propiedad
        FROM propiedades
        WHERE id_propiedad = ?
          AND id_user = ?
          AND (
                Eliminado = 0
                OR Eliminado IS NULL
          )
        LIMIT 1
    ";


    $stmt =
        $conexion->prepare($sql);


    if (!$stmt) {
        return false;
    }


    $stmt->bind_param(
        'ii',
        $idPropiedad,
        $usId
    );


    if (!$stmt->execute()) {

        $stmt->close();

        return false;
    }


    $resultado =
        $stmt->get_result();


    $existe =
        $resultado &&
        $resultado->num_rows > 0;


    $stmt->close();


    return $existe;
}


/* =========================================================
   OBTENER RESUMEN
========================================================= */

function obtenerResumenImagenes(
    mysqli $conexion,
    int $idPropiedad
): array {

    $totalImagenes = 0;
    $imagenPrincipal = '';


    $sql = "
        SELECT
            id_imagen,
            imagenes,
            orden
        FROM imagenes
        WHERE id_propiedad = ?
        ORDER BY
            orden ASC,
            id_imagen ASC
    ";


    $stmt =
        $conexion->prepare($sql);


    if (!$stmt) {

        return [
            'total_imagenes' => 0,
            'imagen_principal' => ''
        ];
    }


    $stmt->bind_param(
        'i',
        $idPropiedad
    );


    if (!$stmt->execute()) {

        $stmt->close();

        return [
            'total_imagenes' => 0,
            'imagen_principal' => ''
        ];
    }


    $resultado =
        $stmt->get_result();


    while (
        $fila =
        $resultado->fetch_assoc()
    ) {

        $totalImagenes++;


        if (
            $totalImagenes === 1 &&
            isset($fila['imagenes']) &&
            $fila['imagenes'] !== null
        ) {

            $imagenPrincipal =
                'data:image/jpeg;base64,' .
                base64_encode(
                    $fila['imagenes']
                );
        }
    }


    $stmt->close();


    return [
        'total_imagenes' =>
        $totalImagenes,

        'imagen_principal' =>
        $imagenPrincipal
    ];
}


/* =========================================================
   OBTENER ACCIÓN
========================================================= */

$accion =
    $_POST['accion']
    ??
    $_GET['accion']
    ??
    '';


$accion =
    strtolower(
        trim(
            (string) $accion
        )
    );


/* =========================================================
   OBTENER ID PROPIEDAD
========================================================= */

$idPropiedad =
    (int) (
        $_POST['id_propiedad']
        ??
        $_GET['id_propiedad']
        ??
        0
    );


/* =========================================================
   VALIDAR ID PROPIEDAD
========================================================= */

if ($idPropiedad <= 0) {

    responder(
        false,
        'La propiedad seleccionada no es válida.'
    );
}


/* =========================================================
   VERIFICAR PROPIEDAD Y USUARIO
========================================================= */

if (
    !verificarPropiedad(
        $conexion,
        $idPropiedad,
        $usId
    )
) {

    responder(
        false,
        'No tienes permiso para modificar esta propiedad.'
    );
}


/* =========================================================
   LISTAR IMÁGENES
========================================================= */

if ($accion === 'listar') {

    $sql = "
        SELECT
            id_imagen,
            imagenes,
            orden,
            fecha_registro,
            fecha_actualizado
        FROM imagenes
        WHERE id_propiedad = ?
        ORDER BY
            orden ASC,
            id_imagen ASC
    ";


    $stmt =
        $conexion->prepare($sql);


    if (!$stmt) {

        responder(
            false,
            'No fue posible consultar las imágenes.'
        );
    }


    $stmt->bind_param(
        'i',
        $idPropiedad
    );


    if (!$stmt->execute()) {

        $stmt->close();

        responder(
            false,
            'No fue posible consultar las imágenes.'
        );
    }


    $resultado =
        $stmt->get_result();


    $imagenes = [];


    while (
        $fila =
        $resultado->fetch_assoc()
    ) {

        $imagenes[] = [

            'id_imagen' =>
            (int) $fila['id_imagen'],

            'orden' =>
            (int) $fila['orden'],

            'imagen' =>
            'data:image/jpeg;base64,' .
                base64_encode(
                    $fila['imagenes']
                )
        ];
    }


    $stmt->close();


    $total =
        count($imagenes);


    $imagenPrincipal =
        $total > 0
        ? $imagenes[0]['imagen']
        : '';


    responder(
        true,
        '',
        [
            'id_propiedad' =>
            $idPropiedad,

            'imagenes' =>
            $imagenes,

            'total_imagenes' =>
            $total,

            'imagen_principal' =>
            $imagenPrincipal
        ]
    );
}


/* =========================================================
   VALIDAR CSRF PARA MODIFICACIONES
========================================================= */

$token =
    (string) (
        $_POST['csrf_token']
        ?? ''
    );


if (
    empty($csrfToken) ||
    empty($token) ||
    !hash_equals(
        $csrfToken,
        $token
    )
) {

    responder(
        false,
        'Token de seguridad inválido.'
    );
}


/* =========================================================
   SUBIR IMÁGENES
========================================================= */

if ($accion === 'subir') {

    if (
        !isset($_FILES['imagenes']) ||
        !is_array(
            $_FILES['imagenes']['name']
        )
    ) {

        responder(
            false,
            'No se seleccionaron imágenes.'
        );
    }


    $permitidos = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];


    $maxSize =
        (int) (
            2.7 * 1024 * 1024
        );


    /* =====================================================
       OBTENER ÚLTIMO ORDEN
    ===================================================== */

    $sqlOrden = "
        SELECT
            COALESCE(
                MAX(orden),
                0
            ) AS ultimo_orden
        FROM imagenes
        WHERE id_propiedad = ?
    ";


    $stmtOrden =
        $conexion->prepare(
            $sqlOrden
        );


    if (!$stmtOrden) {

        responder(
            false,
            'No se pudo preparar el orden de las imágenes.'
        );
    }


    $stmtOrden->bind_param(
        'i',
        $idPropiedad
    );


    if (!$stmtOrden->execute()) {

        $stmtOrden->close();

        responder(
            false,
            'No se pudo obtener el orden de las imágenes.'
        );
    }


    $filaOrden =
        $stmtOrden
        ->get_result()
        ->fetch_assoc();


    $stmtOrden->close();


    $orden =
        (
            (int) (
                $filaOrden['ultimo_orden']
                ?? 0
            )
        ) + 1;


    $cantidad =
        count(
            $_FILES['imagenes']['name']
        );


    $subidas = 0;

    $errores = [];


    /* =====================================================
       PROCESAR IMÁGENES
    ===================================================== */

    for (
        $i = 0;
        $i < $cantidad;
        $i++
    ) {

        $nombreArchivo =
            $_FILES['imagenes']['name'][$i]
            ?? 'imagen';


        $tmpName =
            $_FILES['imagenes']['tmp_name'][$i]
            ?? '';


        $size =
            (int) (
                $_FILES['imagenes']['size'][$i]
                ?? 0
            );


        $error =
            (int) (
                $_FILES['imagenes']['error'][$i]
                ?? UPLOAD_ERR_NO_FILE
            );


        if (
            $error !==
            UPLOAD_ERR_OK
        ) {

            $errores[] =
                $nombreArchivo .
                ': error al subir.';

            continue;
        }


        if (
            !is_uploaded_file(
                $tmpName
            )
        ) {

            $errores[] =
                $nombreArchivo .
                ': archivo no válido.';

            continue;
        }


        /* =================================================
           MIME REAL
        ================================================= */

        $mime =
            mime_content_type(
                $tmpName
            );


        if (
            !in_array(
                $mime,
                $permitidos,
                true
            )
        ) {

            $errores[] =
                $nombreArchivo .
                ': formato no permitido.';

            continue;
        }


        /* =================================================
           TAMAÑO
        ================================================= */

        if (
            $size >
            $maxSize
        ) {

            $errores[] =
                $nombreArchivo .
                ': supera los 2.7 MB.';

            continue;
        }


        $imagenBinaria =
            file_get_contents(
                $tmpName
            );


        if (
            $imagenBinaria === false
        ) {

            $errores[] =
                $nombreArchivo .
                ': no se pudo leer.';

            continue;
        }


        /* =================================================
           INSERTAR
        ================================================= */

        $sqlInsert = "
            INSERT INTO imagenes
            (
                imagenes,
                id_propiedad,
                fecha_registro,
                fecha_actualizado,
                orden
            )
            VALUES
            (
                ?,
                ?,
                CURDATE(),
                CURDATE(),
                ?
            )
        ";


        $stmtInsert =
            $conexion->prepare(
                $sqlInsert
            );


        if (!$stmtInsert) {

            $errores[] =
                $nombreArchivo .
                ': error al preparar inserción.';

            continue;
        }


        $null = null;


        $stmtInsert->bind_param(
            'bii',
            $null,
            $idPropiedad,
            $orden
        );


        $stmtInsert->send_long_data(
            0,
            $imagenBinaria
        );


        if (
            $stmtInsert->execute()
        ) {

            $subidas++;

            $orden++;
        } else {

            $errores[] =
                $nombreArchivo .
                ': no se pudo guardar.';
        }


        $stmtInsert->close();
    }


    if ($subidas === 0) {

        responder(
            false,
            !empty($errores)
                ? implode(
                    ' ',
                    $errores
                )
                : 'No se pudo subir ninguna imagen.'
        );
    }


    $resumen =
        obtenerResumenImagenes(
            $conexion,
            $idPropiedad
        );


    $mensaje =
        $subidas === 1
        ? 'Se agregó 1 imagen correctamente.'
        : "Se agregaron {$subidas} imágenes correctamente.";


    if (!empty($errores)) {

        $mensaje .=
            ' Algunas imágenes no pudieron agregarse: ' .
            implode(
                ' ',
                $errores
            );
    }


    responder(
        true,
        $mensaje,
        array_merge(
            [
                'id_propiedad' =>
                $idPropiedad
            ],
            $resumen
        )
    );
}


/* =========================================================
   CAMBIAR IMAGEN
========================================================= */

if ($accion === 'cambiar') {

    $idImagen =
        (int) (
            $_POST['id_imagen']
            ?? 0
        );


    if ($idImagen <= 0) {

        responder(
            false,
            'La imagen seleccionada no es válida.'
        );
    }


    if (
        !isset($_FILES['imagen']) ||
        !isset(
            $_FILES['imagen']['tmp_name']
        )
    ) {

        responder(
            false,
            'No se recibió la nueva imagen.'
        );
    }


    $archivo =
        $_FILES['imagen'];


    if (
        $archivo['error'] !==
        UPLOAD_ERR_OK
    ) {

        responder(
            false,
            'Ocurrió un error al subir la nueva imagen.'
        );
    }


    $tmpName =
        $archivo['tmp_name'];


    if (
        !is_uploaded_file(
            $tmpName
        )
    ) {

        responder(
            false,
            'El archivo recibido no es válido.'
        );
    }


    $permitidos = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];


    $mime =
        mime_content_type(
            $tmpName
        );


    if (
        !in_array(
            $mime,
            $permitidos,
            true
        )
    ) {

        responder(
            false,
            'El formato de la imagen no es válido.'
        );
    }


    $maxSize =
        (int) (
            2.7 * 1024 * 1024
        );


    if (
        (int) $archivo['size'] >
        $maxSize
    ) {

        responder(
            false,
            'La imagen supera el límite de 2.7 MB.'
        );
    }


    /* =====================================================
       VERIFICAR PERTENENCIA
    ===================================================== */

    $sqlExiste = "
        SELECT id_imagen
        FROM imagenes
        WHERE id_imagen = ?
          AND id_propiedad = ?
        LIMIT 1
    ";


    $stmtExiste =
        $conexion->prepare(
            $sqlExiste
        );


    if (!$stmtExiste) {

        responder(
            false,
            'No se pudo verificar la imagen.'
        );
    }


    $stmtExiste->bind_param(
        'ii',
        $idImagen,
        $idPropiedad
    );


    if (!$stmtExiste->execute()) {

        $stmtExiste->close();

        responder(
            false,
            'No se pudo verificar la imagen.'
        );
    }


    $existe =
        $stmtExiste
        ->get_result()
        ->num_rows > 0;


    $stmtExiste->close();


    if (!$existe) {

        responder(
            false,
            'La imagen no pertenece a esta propiedad.'
        );
    }


    $imagenBinaria =
        file_get_contents(
            $tmpName
        );


    if (
        $imagenBinaria === false
    ) {

        responder(
            false,
            'No se pudo leer la nueva imagen.'
        );
    }


    /* =====================================================
       ACTUALIZAR
    ===================================================== */

    $sqlUpdate = "
        UPDATE imagenes
        SET
            imagenes = ?,
            fecha_actualizado = CURDATE()
        WHERE id_imagen = ?
          AND id_propiedad = ?
    ";


    $stmtUpdate =
        $conexion->prepare(
            $sqlUpdate
        );


    if (!$stmtUpdate) {

        responder(
            false,
            'No se pudo preparar el cambio de imagen.'
        );
    }


    $null = null;


    $stmtUpdate->bind_param(
        'bii',
        $null,
        $idImagen,
        $idPropiedad
    );


    $stmtUpdate->send_long_data(
        0,
        $imagenBinaria
    );


    if (
        !$stmtUpdate->execute()
    ) {

        $stmtUpdate->close();

        responder(
            false,
            'No se pudo cambiar la imagen.'
        );
    }


    $stmtUpdate->close();


    $resumen =
        obtenerResumenImagenes(
            $conexion,
            $idPropiedad
        );


    responder(
        true,
        'La imagen fue cambiada correctamente.',
        array_merge(
            [
                'id_propiedad' =>
                $idPropiedad
            ],
            $resumen
        )
    );
}


/* =========================================================
   ELIMINAR IMAGEN DIRECTAMENTE
========================================================= */

if ($accion === 'eliminar') {

    $idImagen =
        (int) (
            $_POST['id_imagen']
            ?? 0
        );


    /* =====================================================
       VALIDAR ID
    ===================================================== */

    if ($idImagen <= 0) {

        responder(
            false,
            'La imagen seleccionada no es válida.'
        );
    }


    /* =====================================================
       VERIFICAR QUE LA IMAGEN PERTENECE
       A LA PROPIEDAD
    ===================================================== */

    $sqlImagen = "
        SELECT
            id_imagen,
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
            'No se pudo verificar la imagen seleccionada.'
        );
    }


    $stmtImagen->bind_param(
        'ii',
        $idImagen,
        $idPropiedad
    );


    if (
        !$stmtImagen->execute()
    ) {

        $stmtImagen->close();

        responder(
            false,
            'No se pudo verificar la imagen seleccionada.'
        );
    }


    $resultadoImagen =
        $stmtImagen->get_result();


    $imagen =
        $resultadoImagen->fetch_assoc();


    $stmtImagen->close();


    if (!$imagen) {

        responder(
            false,
            'La imagen no existe o no pertenece a esta propiedad.'
        );
    }


    /* =====================================================
       ELIMINAR
    ===================================================== */

    $sqlDelete = "
        DELETE FROM imagenes
        WHERE id_imagen = ?
          AND id_propiedad = ?
    ";


    $stmtDelete =
        $conexion->prepare(
            $sqlDelete
        );


    if (!$stmtDelete) {

        responder(
            false,
            'No se pudo preparar la eliminación de la imagen.'
        );
    }


    $stmtDelete->bind_param(
        'ii',
        $idImagen,
        $idPropiedad
    );


    if (
        !$stmtDelete->execute()
    ) {

        $error =
            $stmtDelete->error;

        $stmtDelete->close();


        responder(
            false,
            'No se pudo eliminar la imagen de la base de datos.' .
                (
                    $error
                    ? ' Error: ' . $error
                    : ''
                )
        );
    }


    $filasAfectadas =
        $stmtDelete->affected_rows;


    $stmtDelete->close();


    /* =====================================================
       VERIFICAR EL DELETE
    ===================================================== */

    if (
        $filasAfectadas !== 1
    ) {

        responder(
            false,
            'La imagen no fue eliminada.'
        );
    }


    /* =====================================================
       REORDENAR IMÁGENES
       
       Después de eliminar:
       
       1
       2
       3
       
       Siempre vuelve a:
       
       1
       2
       
       Esto evita saltos de orden.
    ===================================================== */

    $sqlReordenar = "
        SELECT
            id_imagen
        FROM imagenes
        WHERE id_propiedad = ?
        ORDER BY
            orden ASC,
            id_imagen ASC
    ";


    $stmtReordenar =
        $conexion->prepare(
            $sqlReordenar
        );


    if ($stmtReordenar) {

        $stmtReordenar->bind_param(
            'i',
            $idPropiedad
        );


        if (
            $stmtReordenar->execute()
        ) {

            $resultado =
                $stmtReordenar
                ->get_result();


            $ids = [];


            while (
                $fila =
                $resultado->fetch_assoc()
            ) {

                $ids[] =
                    (int)
                    $fila['id_imagen'];
            }


            $stmtReordenar->close();


            /* =============================================
               ACTUALIZAR ORDEN
            ============================================= */

            if (!empty($ids)) {

                $sqlUpdateOrden = "
                    UPDATE imagenes
                    SET
                        orden = ?,
                        fecha_actualizado = CURDATE()
                    WHERE id_imagen = ?
                      AND id_propiedad = ?
                ";


                $stmtUpdateOrden =
                    $conexion->prepare(
                        $sqlUpdateOrden
                    );


                if ($stmtUpdateOrden) {

                    $orden = 1;


                    foreach (
                        $ids as $id
                    ) {

                        $stmtUpdateOrden->bind_param(
                            'iii',
                            $orden,
                            $id,
                            $idPropiedad
                        );


                        $stmtUpdateOrden->execute();


                        $orden++;
                    }


                    $stmtUpdateOrden->close();
                }
            }
        } else {

            $stmtReordenar->close();
        }
    }


    /* =====================================================
       OBTENER NUEVO RESUMEN
    ===================================================== */

    $resumen =
        obtenerResumenImagenes(
            $conexion,
            $idPropiedad
        );


    /* =====================================================
       RESPUESTA
    ===================================================== */

    responder(
        true,
        'Imagen eliminada correctamente.',
        array_merge(
            [
                'id_propiedad' =>
                $idPropiedad,

                'id_imagen_eliminada' =>
                $idImagen
            ],
            $resumen
        )
    );
}


/* =========================================================
   ESTABLECER PRINCIPAL
========================================================= */

if ($accion === 'principal') {

    $idImagen =
        (int) (
            $_POST['id_imagen']
            ?? 0
        );


    if ($idImagen <= 0) {

        responder(
            false,
            'La imagen seleccionada no es válida.'
        );
    }


    /* =====================================================
       COMPROBAR PERTENENCIA
    ===================================================== */

    $sqlExiste = "
        SELECT
            id_imagen
        FROM imagenes
        WHERE id_imagen = ?
          AND id_propiedad = ?
        LIMIT 1
    ";


    $stmtExiste =
        $conexion->prepare(
            $sqlExiste
        );


    if (!$stmtExiste) {

        responder(
            false,
            'No se pudo verificar la imagen.'
        );
    }


    $stmtExiste->bind_param(
        'ii',
        $idImagen,
        $idPropiedad
    );


    if (!$stmtExiste->execute()) {

        $stmtExiste->close();

        responder(
            false,
            'No se pudo verificar la imagen.'
        );
    }


    $existe =
        $stmtExiste
        ->get_result()
        ->num_rows > 0;


    $stmtExiste->close();


    if (!$existe) {

        responder(
            false,
            'La imagen no pertenece a esta propiedad.'
        );
    }


    /* =====================================================
       OBTENER TODAS LAS IMÁGENES
    ===================================================== */

    $sqlTodas = "
        SELECT
            id_imagen
        FROM imagenes
        WHERE id_propiedad = ?
        ORDER BY
            orden ASC,
            id_imagen ASC
    ";


    $stmtTodas =
        $conexion->prepare(
            $sqlTodas
        );


    if (!$stmtTodas) {

        responder(
            false,
            'No se pudieron obtener las imágenes.'
        );
    }


    $stmtTodas->bind_param(
        'i',
        $idPropiedad
    );


    if (!$stmtTodas->execute()) {

        $stmtTodas->close();

        responder(
            false,
            'No se pudieron obtener las imágenes.'
        );
    }


    $resultado =
        $stmtTodas
        ->get_result();


    $ids = [];


    while (
        $fila =
        $resultado->fetch_assoc()
    ) {

        $ids[] =
            (int)
            $fila['id_imagen'];
    }


    $stmtTodas->close();


    /* =====================================================
       SELECCIONADA PRIMERO
    ===================================================== */

    $ids =
        array_values(
            array_unique(
                array_merge(
                    [$idImagen],
                    $ids
                )
            )
        );


    /* =====================================================
       ACTUALIZAR ORDEN
    ===================================================== */

    $sqlOrdenar = "
        UPDATE imagenes
        SET
            orden = ?,
            fecha_actualizado = CURDATE()
        WHERE id_imagen = ?
          AND id_propiedad = ?
    ";


    $stmtOrdenar =
        $conexion->prepare(
            $sqlOrdenar
        );


    if (!$stmtOrdenar) {

        responder(
            false,
            'No se pudo actualizar el orden de las imágenes.'
        );
    }


    $orden = 1;


    foreach (
        $ids as $id
    ) {

        $stmtOrdenar->bind_param(
            'iii',
            $orden,
            $id,
            $idPropiedad
        );


        if (
            !$stmtOrdenar->execute()
        ) {

            $stmtOrdenar->close();

            responder(
                false,
                'No se pudo actualizar el orden de las imágenes.'
            );
        }


        $orden++;
    }


    $stmtOrdenar->close();


    /* =====================================================
       NUEVO RESUMEN
    ===================================================== */

    $resumen =
        obtenerResumenImagenes(
            $conexion,
            $idPropiedad
        );


    responder(
        true,
        'La imagen principal fue actualizada correctamente.',
        array_merge(
            [
                'id_propiedad' =>
                $idPropiedad
            ],
            $resumen
        )
    );
}


/* =========================================================
   ACCIÓN DESCONOCIDA
========================================================= */

responder(
    false,
    'Acción no válida.'
);
