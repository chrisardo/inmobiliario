<?php

/* ============================================================
   Inmobiliaria Iquitos
   Archivo: controladores/procesar_perfil.php
   Módulo: Perfil Administrativo

   FUNCIONES:
   - Actualizar información de empresa
   - Actualizar imagen de perfil
   - Subir / reemplazar video informativo
   - Eliminar video informativo
   - Cambiar contraseña
   - Cargar datos del perfil
============================================================ */


/* ============================================================
   SESIÓN
============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['usId'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        http_response_code(401);

        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'La sesión ha expirado.'
        ], JSON_UNESCAPED_UNICODE);

        exit();
    }

    header("Location: ../login.php");

    exit();
}


$usId = (int) $_SESSION['usId'];


/* ============================================================
   CONEXIÓN
============================================================ */

if (!isset($conexion)) {

    require_once __DIR__ . '/conect_db.php';
}


/* ============================================================
   FUNCIÓN RESPUESTA JSON
============================================================ */

function respuestaPerfil(
    string $estado,
    string $mensaje,
    array $datos = []
): void {

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode(
        array_merge(
            [
                'estado' => $estado,
                'mensaje' => $mensaje
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit();
}


/* ============================================================
   PETICIONES AJAX POST
============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $accion =
        isset($_POST['accion'])
        ? trim($_POST['accion'])
        : '';


    /* ========================================================
       ACTUALIZAR PERFIL
    ======================================================== */

    if ($accion === 'actualizar_perfil') {

        try {

            $nombreEmpresa =
                trim($_POST['nombreEmpresa'] ?? '');

            $ruc =
                trim($_POST['ruc'] ?? '');

            $celular =
                trim($_POST['celular'] ?? '');

            $email =
                trim($_POST['email'] ?? '');

            $direccion =
                trim($_POST['direccion'] ?? '');

            $descripcion =
                trim($_POST['descripcion'] ?? '');


            /* =================================================
               VALIDACIONES
            ================================================= */

            if ($nombreEmpresa === '') {

                respuestaPerfil(
                    'error',
                    'El nombre de la empresa es obligatorio.'
                );
            }


            if (
                $email === '' ||
                !filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                respuestaPerfil(
                    'error',
                    'Ingresa un correo electrónico válido.'
                );
            }


            if (
                mb_strlen(
                    $nombreEmpresa,
                    'UTF-8'
                ) > 150
            ) {

                respuestaPerfil(
                    'error',
                    'El nombre de la empresa no puede superar los 150 caracteres.'
                );
            }


            if (
                mb_strlen(
                    $descripcion,
                    'UTF-8'
                ) > 500
            ) {

                respuestaPerfil(
                    'error',
                    'La descripción no puede superar los 500 caracteres.'
                );
            }


            /* =================================================
               COMPROBAR EMAIL EN OTRA CUENTA
            ================================================= */

            $sqlEmail = "
                SELECT id_user
                FROM usuario_acceso
                WHERE email = ?
                AND id_user <> ?
                LIMIT 1
            ";


            $stmtEmail =
                mysqli_prepare(
                    $conexion,
                    $sqlEmail
                );


            if (!$stmtEmail) {

                throw new Exception(
                    'No se pudo preparar la validación del correo.'
                );
            }


            mysqli_stmt_bind_param(
                $stmtEmail,
                'si',
                $email,
                $usId
            );


            mysqli_stmt_execute(
                $stmtEmail
            );


            $resultadoEmail =
                mysqli_stmt_get_result(
                    $stmtEmail
                );


            if (
                mysqli_num_rows(
                    $resultadoEmail
                ) > 0
            ) {

                mysqli_stmt_close(
                    $stmtEmail
                );

                respuestaPerfil(
                    'error',
                    'El correo electrónico ya está registrado en otra cuenta.'
                );
            }


            mysqli_stmt_close(
                $stmtEmail
            );


            /* =================================================
               ACTUALIZAR DATOS
            ================================================= */

            $sql = "
                UPDATE usuario_acceso
                SET
                    nombreEmpresa = ?,
                    email = ?,
                    direccion = ?,
                    celular = ?,
                    ruc = ?,
                    descripcion_acerca = ?
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmt =
                mysqli_prepare(
                    $conexion,
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    'No se pudo preparar la actualización del perfil.'
                );
            }


            mysqli_stmt_bind_param(
                $stmt,
                'ssssssi',
                $nombreEmpresa,
                $email,
                $direccion,
                $celular,
                $ruc,
                $descripcion,
                $usId
            );


            if (
                !mysqli_stmt_execute(
                    $stmt
                )
            ) {

                mysqli_stmt_close(
                    $stmt
                );

                throw new Exception(
                    'No se pudieron guardar los cambios.'
                );
            }


            mysqli_stmt_close(
                $stmt
            );


            /* =================================================
               RESPUESTA
            ================================================= */

            respuestaPerfil(
                'ok',
                'Los datos del perfil fueron actualizados correctamente.',
                [
                    'datos' => [
                        'nombreEmpresa' => $nombreEmpresa,
                        'email' => $email,
                        'direccion' => $direccion,
                        'celular' => $celular,
                        'ruc' => $ruc,
                        'descripcion' => $descripcion
                    ]
                ]
            );


        } catch (Throwable $e) {

            respuestaPerfil(
                'error',
                'Ocurrió un error al actualizar el perfil.'
            );
        }
    }


    /* ========================================================
       ACTUALIZAR IMAGEN
    ======================================================== */

    if ($accion === 'actualizar_imagen') {

        try {

            /* =================================================
               VALIDAR ARCHIVO
            ================================================= */

            if (
                !isset($_FILES['imagen']) ||
                $_FILES['imagen']['error'] !== UPLOAD_ERR_OK
            ) {

                respuestaPerfil(
                    'error',
                    'No se recibió una imagen válida.'
                );
            }


            $archivo =
                $_FILES['imagen'];


            /* =================================================
               TAMAÑO MÁXIMO
            ================================================= */

            $maxSize =
                2 * 1024 * 1024;


            if (
                $archivo['size'] >
                $maxSize
            ) {

                respuestaPerfil(
                    'error',
                    'La imagen no puede superar los 2 MB.'
                );
            }


            /* =================================================
               MIME REAL
            ================================================= */

            $finfo =
                new finfo(
                    FILEINFO_MIME_TYPE
                );


            $mime =
                $finfo->file(
                    $archivo['tmp_name']
                );


            $tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];


            if (
                !in_array(
                    $mime,
                    $tiposPermitidos,
                    true
                )
            ) {

                respuestaPerfil(
                    'error',
                    'El formato de imagen no está permitido.'
                );
            }


            /* =================================================
               LEER IMAGEN
            ================================================= */

            $imagenBinaria =
                file_get_contents(
                    $archivo['tmp_name']
                );


            if (
                $imagenBinaria === false
            ) {

                throw new Exception(
                    'No se pudo leer la imagen.'
                );
            }


            /* =================================================
               ACTUALIZAR BLOB
            ================================================= */

            $sql = "
                UPDATE usuario_acceso
                SET imagen = ?
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmt =
                mysqli_prepare(
                    $conexion,
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    'No se pudo preparar la actualización de la imagen.'
                );
            }


            mysqli_stmt_bind_param(
                $stmt,
                'bi',
                $imagenBinaria,
                $usId
            );


            mysqli_stmt_send_long_data(
                $stmt,
                0,
                $imagenBinaria
            );


            if (
                !mysqli_stmt_execute(
                    $stmt
                )
            ) {

                mysqli_stmt_close(
                    $stmt
                );

                throw new Exception(
                    'No se pudo guardar la imagen.'
                );
            }


            mysqli_stmt_close(
                $stmt
            );


            /* =================================================
               DATA URI
            ================================================= */

            $imagenBase64 =
                base64_encode(
                    $imagenBinaria
                );


            $dataUri =
                'data:' .
                $mime .
                ';base64,' .
                $imagenBase64;


            /* =================================================
               RESPUESTA
            ================================================= */

            respuestaPerfil(
                'ok',
                'La imagen de perfil fue actualizada correctamente.',
                [
                    'imagen' => $dataUri
                ]
            );


        } catch (Throwable $e) {

            respuestaPerfil(
                'error',
                'Ocurrió un error al actualizar la imagen.'
            );
        }
    }


    /* ========================================================
       ACTUALIZAR VIDEO
    ======================================================== */

    if ($accion === 'actualizar_video') {

        $rutaFisicaNueva = '';


        try {

            /* =================================================
               VALIDAR ARCHIVO
            ================================================= */

            if (
                !isset($_FILES['video']) ||
                $_FILES['video']['error'] !== UPLOAD_ERR_OK
            ) {

                respuestaPerfil(
                    'error',
                    'No se recibió un video válido.'
                );
            }


            $archivo =
                $_FILES['video'];


            /* =================================================
               VALIDAR TAMAÑO
               MÁXIMO: 50 MB
            ================================================= */

            $maxSize =
                50 * 1024 * 1024;


            if (
                $archivo['size'] >
                $maxSize
            ) {

                respuestaPerfil(
                    'error',
                    'El video no puede superar los 50 MB.'
                );
            }


            /* =================================================
               MIME REAL
            ================================================= */

            $finfo =
                new finfo(
                    FILEINFO_MIME_TYPE
                );


            $mime =
                $finfo->file(
                    $archivo['tmp_name']
                );


            $tiposPermitidos = [

                'video/mp4' =>
                    'mp4',

                'video/webm' =>
                    'webm',

                'video/ogg' =>
                    'ogv'

            ];


            if (
                !isset(
                    $tiposPermitidos[$mime]
                )
            ) {

                respuestaPerfil(
                    'error',
                    'El formato del video no está permitido. Usa MP4, WEBM u OGG.'
                );
            }


            /* =================================================
               DIRECTORIO DE VIDEOS
            ================================================= */

            $directorioVideos =
                dirname(__DIR__) .
                DIRECTORY_SEPARATOR .
                'uploads' .
                DIRECTORY_SEPARATOR .
                'videos_empresa' .
                DIRECTORY_SEPARATOR;


            /* =================================================
               CREAR DIRECTORIO
            ================================================= */

            if (
                !is_dir(
                    $directorioVideos
                )
            ) {

                if (
                    !mkdir(
                        $directorioVideos,
                        0755,
                        true
                    )
                ) {

                    throw new Exception(
                        'No se pudo crear el directorio de videos.'
                    );
                }
            }


            /* =================================================
               COMPROBAR ESCRITURA
            ================================================= */

            if (
                !is_writable(
                    $directorioVideos
                )
            ) {

                throw new Exception(
                    'El directorio de videos no tiene permisos de escritura.'
                );
            }


            /* =================================================
               EXTENSIÓN
            ================================================= */

            $extension =
                $tiposPermitidos[$mime];


            /* =================================================
               NOMBRE ÚNICO
            ================================================= */

            $nombreArchivo =
                'video_empresa_' .
                $usId .
                '_' .
                bin2hex(
                    random_bytes(8)
                ) .
                '.' .
                $extension;


            /* =================================================
               RUTA FÍSICA NUEVA
            ================================================= */

            $rutaFisicaNueva =
                $directorioVideos .
                $nombreArchivo;


            /* =================================================
               MOVER ARCHIVO
            ================================================= */

            if (
                !move_uploaded_file(
                    $archivo['tmp_name'],
                    $rutaFisicaNueva
                )
            ) {

                throw new Exception(
                    'No se pudo guardar el video.'
                );
            }


            /* =================================================
               RUTA WEB
            ================================================= */

            $rutaWeb =
                '../uploads/videos_empresa/' .
                $nombreArchivo;


            /* =================================================
               OBTENER VIDEO ANTERIOR
            ================================================= */

            $sqlAnterior = "
                SELECT video
                FROM usuario_acceso
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmtAnterior =
                mysqli_prepare(
                    $conexion,
                    $sqlAnterior
                );


            if (!$stmtAnterior) {

                @unlink(
                    $rutaFisicaNueva
                );

                throw new Exception(
                    'No se pudo consultar el video anterior.'
                );
            }


            mysqli_stmt_bind_param(
                $stmtAnterior,
                'i',
                $usId
            );


            mysqli_stmt_execute(
                $stmtAnterior
            );


            $resultadoAnterior =
                mysqli_stmt_get_result(
                    $stmtAnterior
                );


            $perfilAnterior =
                mysqli_fetch_assoc(
                    $resultadoAnterior
                );


            mysqli_stmt_close(
                $stmtAnterior
            );


            $videoAnterior =
                $perfilAnterior['video'] ?? '';


            /* =================================================
               ACTUALIZAR BASE DE DATOS
            ================================================= */

            $sql = "
                UPDATE usuario_acceso
                SET video = ?
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmt =
                mysqli_prepare(
                    $conexion,
                    $sql
                );


            if (!$stmt) {

                @unlink(
                    $rutaFisicaNueva
                );

                throw new Exception(
                    'No se pudo preparar la actualización del video.'
                );
            }


            mysqli_stmt_bind_param(
                $stmt,
                'si',
                $rutaWeb,
                $usId
            );


            if (
                !mysqli_stmt_execute(
                    $stmt
                )
            ) {

                mysqli_stmt_close(
                    $stmt
                );

                @unlink(
                    $rutaFisicaNueva
                );

                throw new Exception(
                    'No se pudo guardar el video en la base de datos.'
                );
            }


            mysqli_stmt_close(
                $stmt );


            /* =================================================
               ELIMINAR VIDEO ANTERIOR
            ================================================= */

            if (
                !empty($videoAnterior)
            ) {

                /*
                 * Solo permitimos eliminar archivos
                 * pertenecientes al directorio
                 * videos_empresa.
                 */

                $nombreVideoAnterior =
                    basename(
                        parse_url(
                            $videoAnterior,
                            PHP_URL_PATH
                        )
                    );


                if (
                    $nombreVideoAnterior !== '' &&
                    preg_match(
                        '/^video_empresa_' .
                        preg_quote(
                            (string)$usId,
                            '/'
                        ) .
                        '_[a-f0-9]+\.(mp4|webm|ogv)$/i',
                        $nombreVideoAnterior
                    )
                ) {

                    $rutaFisicaAnterior =
                        $directorioVideos .
                        $nombreVideoAnterior;


                    if (
                        is_file(
                            $rutaFisicaAnterior
                        )
                    ) {

                        @unlink(
                            $rutaFisicaAnterior
                        );
                    }
                }
            }


            /* =================================================
               RESPUESTA
            ================================================= */

            respuestaPerfil(
                'ok',
                'El video informativo fue actualizado correctamente.',
                [
                    'video' => $rutaWeb
                ]
            );


        } catch (Throwable $e) {

            /*
             * Si hubo un error después de crear
             * el nuevo archivo, intentamos eliminarlo.
             */

            if (
                !empty($rutaFisicaNueva) &&
                is_file($rutaFisicaNueva)
            ) {

                @unlink(
                    $rutaFisicaNueva
                );
            }


            respuestaPerfil(
                'error',
                'Ocurrió un error al actualizar el video.'
            );
        }
    }


    /* ========================================================
       ELIMINAR VIDEO
    ======================================================== */

    if ($accion === 'eliminar_video') {

        try {

            /* =================================================
               OBTENER VIDEO ACTUAL
            ================================================= */

            $sql = "
                SELECT video
                FROM usuario_acceso
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmt =
                mysqli_prepare(
                    $conexion,
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    'No se pudo consultar el video.'
                );
            }


            mysqli_stmt_bind_param(
                $stmt,
                'i',
                $usId
            );


            mysqli_stmt_execute(
                $stmt
            );


            $resultado =
                mysqli_stmt_get_result(
                    $stmt
                );


            $perfil =
                mysqli_fetch_assoc(
                    $resultado
                );


            mysqli_stmt_close(
                $stmt
            );


            $videoActual =
                $perfil['video'] ?? '';


            /* =================================================
               DIRECTORIO DE VIDEOS
            ================================================= */

            $directorioVideos =
                dirname(__DIR__) .
                DIRECTORY_SEPARATOR .
                'uploads' .
                DIRECTORY_SEPARATOR .
                'videos_empresa' .
                DIRECTORY_SEPARATOR;


            /* =================================================
               ELIMINAR ARCHIVO
            ================================================= */

            if (
                !empty($videoActual)
            ) {

                $nombreVideo =
                    basename(
                        parse_url(
                            $videoActual,
                            PHP_URL_PATH
                        )
                    );


                if (
                    $nombreVideo !== '' &&
                    preg_match(
                        '/^video_empresa_' .
                        preg_quote(
                            (string)$usId,
                            '/'
                        ) .
                        '_[a-f0-9]+\.(mp4|webm|ogv)$/i',
                        $nombreVideo
                    )
                ) {

                    $rutaFisica =
                        $directorioVideos .
                        $nombreVideo;


                    if (
                        is_file(
                            $rutaFisica
                        )
                    ) {

                        if (
                            !@unlink(
                                $rutaFisica
                            )
                        ) {

                            throw new Exception(
                                'No se pudo eliminar el archivo de video.'
                            );
                        }
                    }
                }
            }


            /* =================================================
               LIMPIAR BASE DE DATOS
            ================================================= */

            $sqlDelete = "
                UPDATE usuario_acceso
                SET video = NULL
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmtDelete =
                mysqli_prepare(
                    $conexion,
                    $sqlDelete
                );


            if (!$stmtDelete) {

                throw new Exception(
                    'No se pudo preparar la eliminación del video.'
                );
            }


            mysqli_stmt_bind_param(
                $stmtDelete,
                'i',
                $usId
            );


            if (
                !mysqli_stmt_execute(
                    $stmtDelete
                )
            ) {

                mysqli_stmt_close(
                    $stmtDelete
                );

                throw new Exception(
                    'No se pudo eliminar el video.'
                );
            }


            mysqli_stmt_close(
                $stmtDelete
            );


            /* =================================================
               RESPUESTA
            ================================================= */

            respuestaPerfil(
                'ok',
                'El video informativo fue eliminado correctamente.'
            );


        } catch (Throwable $e) {

            respuestaPerfil(
                'error',
                'Ocurrió un error al eliminar el video.'
            );
        }
    }


    /* ========================================================
       ACTUALIZAR CONTRASEÑA
    ======================================================== */

    if ($accion === 'actualizar_contrasena') {

        try {

            $contrasenaActual =
                $_POST['contrasenaActual'] ?? '';

            $nuevaContrasena =
                $_POST['nuevaContrasena'] ?? '';

            $confirmarContrasena =
                $_POST['confirmarContrasena'] ?? '';


            /* =================================================
               VALIDACIONES
            ================================================= */

            if (
                $contrasenaActual === '' ||
                $nuevaContrasena === '' ||
                $confirmarContrasena === ''
            ) {

                respuestaPerfil(
                    'error',
                    'Completa todos los campos de contraseña.'
                );
            }


            if (
                mb_strlen(
                    $nuevaContrasena,
                    'UTF-8'
                ) < 8
            ) {

                respuestaPerfil(
                    'error',
                    'La nueva contraseña debe tener al menos 8 caracteres.'
                );
            }


            if (
                $nuevaContrasena !==
                $confirmarContrasena
            ) {

                respuestaPerfil(
                    'error',
                    'Las nuevas contraseñas no coinciden.'
                );
            }


            if (
                $contrasenaActual ===
                $nuevaContrasena
            ) {

                respuestaPerfil(
                    'error',
                    'La nueva contraseña debe ser diferente a la actual.'
                );
            }


            /* =================================================
               OBTENER PASSWORD ACTUAL
            ================================================= */

            $sql = "
                SELECT contrasena
                FROM usuario_acceso
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmt =
                mysqli_prepare(
                    $conexion,
                    $sql
                );


            if (!$stmt) {

                throw new Exception(
                    'No se pudo consultar la contraseña.'
                );
            }


            mysqli_stmt_bind_param(
                $stmt,
                'i',
                $usId
            );


            mysqli_stmt_execute(
                $stmt
            );


            $resultado =
                mysqli_stmt_get_result(
                    $stmt
                );


            $usuario =
                mysqli_fetch_assoc(
                    $resultado
                );


            mysqli_stmt_close(
                $stmt
            );


            if (!$usuario) {

                respuestaPerfil(
                    'error',
                    'No se encontró la cuenta administrativa.'
                );
            }


            /* =================================================
               VERIFICAR PASSWORD
            ================================================= */

            if (
                !password_verify(
                    $contrasenaActual,
                    $usuario['contrasena']
                )
            ) {

                respuestaPerfil(
                    'error',
                    'La contraseña actual no es correcta.'
                );
            }


            /* =================================================
               GENERAR HASH
            ================================================= */

            $hash =
                password_hash(
                    $nuevaContrasena,
                    PASSWORD_DEFAULT
                );


            if (
                $hash === false
            ) {

                throw new Exception(
                    'No se pudo generar la nueva contraseña.'
                );
            }


            /* =================================================
               ACTUALIZAR PASSWORD
            ================================================= */

            $sqlUpdate = "
                UPDATE usuario_acceso
                SET
                    contrasena = ?,
                    password_changed_at = NOW()
                WHERE id_user = ?
                LIMIT 1
            ";


            $stmtUpdate =
                mysqli_prepare(
                    $conexion,
                    $sqlUpdate
                );


            if (!$stmtUpdate) {

                throw new Exception(
                    'No se pudo preparar el cambio de contraseña.'
                );
            }


            mysqli_stmt_bind_param(
                $stmtUpdate,
                'si',
                $hash,
                $usId
            );


            if (
                !mysqli_stmt_execute(
                    $stmtUpdate
                )
            ) {

                mysqli_stmt_close(
                    $stmtUpdate
                );

                throw new Exception(
                    'No se pudo actualizar la contraseña.'
                );
            }


            mysqli_stmt_close(
                $stmtUpdate
            );


            /* =================================================
               RESPUESTA
            ================================================= */

            respuestaPerfil(
                'ok',
                'La contraseña fue actualizada correctamente.'
            );


        } catch (Throwable $e) {

            respuestaPerfil(
                'error',
                'Ocurrió un error al actualizar la contraseña.'
            );
        }
    }


    /* ========================================================
       ACCIÓN DESCONOCIDA
    ======================================================== */

    respuestaPerfil(
        'error',
        'La operación solicitada no es válida.'
    );
}


/* ============================================================
   CARGAR DATOS DEL PERFIL
   SOLO CUANDO EL ARCHIVO ES INCLUIDO
============================================================ */

try {

    $sqlPerfil = "
        SELECT
            nombreEmpresa,
            email,
            username,
            imagen,
            direccion,
            celular,
            estado,
            fecha_registro,
            ruc,
            password_changed_at,
            descripcion_acerca,
            video
        FROM usuario_acceso
        WHERE id_user = ?
        LIMIT 1
    ";


    $stmtPerfil =
        mysqli_prepare(
            $conexion,
            $sqlPerfil
        );


    if (!$stmtPerfil) {

        throw new Exception(
            'No se pudo consultar el perfil.'
        );
    }


    mysqli_stmt_bind_param(
        $stmtPerfil,
        'i',
        $usId
    );


    mysqli_stmt_execute(
        $stmtPerfil
    );


    $resultadoPerfil =
        mysqli_stmt_get_result(
            $stmtPerfil
        );


    $perfil =
        mysqli_fetch_assoc(
            $resultadoPerfil
        );


    mysqli_stmt_close(
        $stmtPerfil
    );


    /* ========================================================
       VALIDAR PERFIL
    ======================================================== */

    if (!$perfil) {

        session_destroy();

        header(
            "Location: ../login.php"
        );

        exit();
    }


    /* ========================================================
       VARIABLES
    ======================================================== */

    $nombreEmpresa =
        $perfil['nombreEmpresa'] ?? '';

    $email =
        $perfil['email'] ?? '';

    $username =
        $perfil['username'] ?? '';

    $direccion =
        $perfil['direccion'] ?? '';

    $celular =
        $perfil['celular'] ?? '';

    $ruc =
        $perfil['ruc'] ?? '';

    $descripcion =
        $perfil['descripcion_acerca'] ?? '';

    $estadoUsuario =
        $perfil['estado'] ?? '';

    $video =
        $perfil['video'] ?? '';

    $fechaRegistro =
        $perfil['fecha_registro'] ?? '';

    $passwordChangedAt =
        $perfil['password_changed_at'] ?? '';


    /* ========================================================
       FECHA REGISTRO
    ======================================================== */

    if (
        !empty($fechaRegistro)
    ) {

        $fechaObjeto =
            DateTime::createFromFormat(
                'Y-m-d',
                $fechaRegistro
            );


        if ($fechaObjeto) {

            $fechaRegistro =
                $fechaObjeto->format(
                    'd/m/Y'
                );
        }
    }


    /* ========================================================
       FECHA PASSWORD
    ======================================================== */

    if (
        !empty($passwordChangedAt)
    ) {

        $fechaPassword =
            DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $passwordChangedAt
            );


        if ($fechaPassword) {

            $passwordChangedAt =
                $fechaPassword->format(
                    'd/m/Y H:i'
                );
        }
    }


    /* ========================================================
       IMAGEN BLOB
    ======================================================== */

    $fotoPerfil = '';


    if (
        isset($perfil['imagen']) &&
        $perfil['imagen'] !== null &&
        $perfil['imagen'] !== ''
    ) {

        $imagenBlob =
            $perfil['imagen'];


        $finfo =
            new finfo(
                FILEINFO_MIME_TYPE
            );


        $mime =
            $finfo->buffer(
                $imagenBlob
            );


        $tiposPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif'
        ];


        if (
            in_array(
                $mime,
                $tiposPermitidos,
                true
            )
        ) {

            $fotoPerfil =
                'data:' .
                $mime .
                ';base64,' .
                base64_encode(
                    $imagenBlob
                );
        }
    }


    /* ========================================================
       TOTAL MENSAJES NO LEÍDOS
    ======================================================== */

    $totalMensajes = 0;


    $sqlMensajes = "
        SELECT COUNT(*) AS total
        FROM mensajes
        INNER JOIN propiedades
            ON propiedades.id_propiedad =
               mensajes.id_propiedad
        WHERE propiedades.id_user = ?
        AND mensajes.estado_mensaje_leido = 0
    ";


    $stmtMensajes =
        mysqli_prepare(
            $conexion,
            $sqlMensajes
        );


    if ($stmtMensajes) {

        mysqli_stmt_bind_param(
            $stmtMensajes,
            'i',
            $usId
        );


        mysqli_stmt_execute(
            $stmtMensajes
        );


        $resultadoMensajes =
            mysqli_stmt_get_result(
                $stmtMensajes
            );


        $filaMensajes =
            mysqli_fetch_assoc(
                $resultadoMensajes
            );


        $totalMensajes =
            (int) (
                $filaMensajes['total'] ?? 0
            );


        mysqli_stmt_close(
            $stmtMensajes
        );
    }


} catch (Throwable $e) {

    /* ========================================================
       VALORES SEGUROS
    ======================================================== */

    $nombreEmpresa = '';
    $email = '';
    $username = '';
    $direccion = '';
    $celular = '';
    $ruc = '';
    $descripcion = '';
    $estadoUsuario = '';
    $video = '';
    $fechaRegistro = '';
    $passwordChangedAt = '';
    $fotoPerfil = '';
    $totalMensajes = 0;
}
