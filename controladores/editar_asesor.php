<?php
// =========================================================
// CoDevPro Technology
// Archivo: controladores/editar_asesor.php
// Módulo: Editar Asesor
// Sistema: Inmobiliario
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================
   CONFIGURACIÓN
========================================================= */

const MAX_TAMANO_IMAGEN = 2.7 * 1024 * 1024;

const TIPOS_IMAGEN_PERMITIDOS = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

/* =========================================================
   FUNCIÓN DE REDIRECCIÓN
========================================================= */

function redirigirListaAsesores(): void
{
    header('Location: ../adm/adm_lista_asesores.php');
    exit();
}

/* =========================================================
   MENSAJE DE SESIÓN
========================================================= */

function establecerMensajeAsesor(
    string $mensaje,
    string $tipo = 'danger'
): void {

    $_SESSION['mensajeAsesor'] = $mensaje;
    $_SESSION['tipoAsesor'] = $tipo;
}

/* =========================================================
   VALIDAR SESIÓN
========================================================= */

if (
    !isset($_SESSION['usId']) ||
    !is_numeric($_SESSION['usId']) ||
    (int) $_SESSION['usId'] <= 0
) {

    establecerMensajeAsesor(
        'Tu sesión ha expirado. Inicia sesión nuevamente.'
    );

    header('Location: ../login.php');
    exit();
}

$idUser = (int) $_SESSION['usId'];

/* =========================================================
   VALIDAR MÉTODO
========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    establecerMensajeAsesor(
        'Solicitud no válida.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   VALIDAR ACCIÓN
========================================================= */

$accion = isset($_POST['accion'])
    ? trim((string) $_POST['accion'])
    : '';

if ($accion !== 'editar') {

    establecerMensajeAsesor(
        'La acción solicitada no es válida.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   VALIDAR CSRF
========================================================= */

$csrfSesion = $_SESSION['csrf_asesores'] ?? '';

$csrfFormulario = isset($_POST['csrf_token'])
    ? trim((string) $_POST['csrf_token'])
    : '';

if (
    empty($csrfSesion) ||
    empty($csrfFormulario) ||
    !hash_equals($csrfSesion, $csrfFormulario)
) {

    establecerMensajeAsesor(
        'La solicitud de edición no pudo ser validada. Recarga la página e inténtalo nuevamente.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   CONEXIÓN
========================================================= */

require_once __DIR__ . '/conect_db.php';

/*
 * Verificamos que exista una conexión válida.
 */

if (
    !isset($conexion) ||
    !($conexion instanceof mysqli)
) {

    establecerMensajeAsesor(
        'No fue posible establecer conexión con la base de datos.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   CONFIGURACIÓN MYSQL
========================================================= */

$conexion->set_charset('utf8mb4');

/* =========================================================
   OBTENER Y LIMPIAR DATOS
========================================================= */

$idAsesor = isset($_POST['id_asesor'])
    ? trim((string) $_POST['id_asesor'])
    : '';

$nombre = isset($_POST['nombre'])
    ? trim((string) $_POST['nombre'])
    : '';

$apellidos = isset($_POST['apellidos'])
    ? trim((string) $_POST['apellidos'])
    : '';

$email = isset($_POST['email'])
    ? trim((string) $_POST['email'])
    : '';

$celular = isset($_POST['celular'])
    ? trim((string) $_POST['celular'])
    : '';

$cargo = isset($_POST['cargo'])
    ? trim((string) $_POST['cargo'])
    : '';

/* =========================================================
   VALIDACIONES BÁSICAS
========================================================= */

$errores = [];

/* =========================================================
   ID ASESOR
========================================================= */

if (
    $idAsesor === '' ||
    !ctype_digit($idAsesor) ||
    (int) $idAsesor <= 0
) {

    $errores[] =
        'El asesor seleccionado no es válido.';
}

$idAsesor = (int) $idAsesor;

/* =========================================================
   NOMBRE
========================================================= */

if ($nombre === '') {

    $errores[] =
        'El nombre es obligatorio.';

} elseif (mb_strlen($nombre, 'UTF-8') > 100) {

    $errores[] =
        'El nombre no puede superar los 100 caracteres.';
}

/* =========================================================
   APELLIDOS
========================================================= */

if ($apellidos === '') {

    $errores[] =
        'Los apellidos son obligatorios.';

} elseif (mb_strlen($apellidos, 'UTF-8') > 150) {

    $errores[] =
        'Los apellidos no pueden superar los 150 caracteres.';
}

/* =========================================================
   EMAIL
========================================================= */

if ($email === '') {

    $errores[] =
        'El correo electrónico es obligatorio.';

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errores[] =
        'Ingrese un correo electrónico válido.';

} elseif (mb_strlen($email, 'UTF-8') > 150) {

    $errores[] =
        'El correo electrónico no puede superar los 150 caracteres.';
}

/* =========================================================
   CELULAR
========================================================= */

if ($celular === '') {

    $errores[] =
        'El celular es obligatorio.';

} elseif (mb_strlen($celular, 'UTF-8') > 20) {

    $errores[] =
        'El celular no puede superar los 20 caracteres.';

} elseif (!preg_match('/^[0-9+\-\s()]+$/', $celular)) {

    $errores[] =
        'El número de celular contiene caracteres no permitidos.';
}

/* =========================================================
   CARGO
========================================================= */

if ($cargo === '') {

    $errores[] =
        'El cargo es obligatorio.';

} elseif (mb_strlen($cargo, 'UTF-8') > 100) {

    $errores[] =
        'El cargo no puede superar los 100 caracteres.';
}

/* =========================================================
   SI HAY ERRORES, DETENER
========================================================= */

if (!empty($errores)) {

    establecerMensajeAsesor(
        implode(' ', $errores)
    );

    redirigirListaAsesores();
}

/* =========================================================
   VERIFICAR QUE EL ASESOR PERTENEZCA AL USUARIO
========================================================= */

$stmtExiste = $conexion->prepare("
    SELECT
        id_asesor,
        email,
        imagen
    FROM asesores
    WHERE id_asesor = ?
      AND id_user = ?
    LIMIT 1
");

if (!$stmtExiste) {

    establecerMensajeAsesor(
        'No fue posible validar el asesor seleccionado.'
    );

    redirigirListaAsesores();
}

$stmtExiste->bind_param(
    'ii',
    $idAsesor,
    $idUser
);

if (!$stmtExiste->execute()) {

    $stmtExiste->close();

    establecerMensajeAsesor(
        'No fue posible consultar la información del asesor.'
    );

    redirigirListaAsesores();
}

$resultadoExiste = $stmtExiste->get_result();

$asesorActual = $resultadoExiste
    ? $resultadoExiste->fetch_assoc()
    : null;

$stmtExiste->close();

if (!$asesorActual) {

    establecerMensajeAsesor(
        'El asesor seleccionado no existe o no tienes permisos para editarlo.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   VALIDAR EMAIL DUPLICADO
========================================================= */

/*
 * Permitimos conservar el mismo correo del asesor actual.
 *
 * Pero no permitimos que dos asesores del mismo usuario
 * tengan el mismo correo electrónico.
 */

$stmtEmail = $conexion->prepare("
    SELECT id_asesor
    FROM asesores
    WHERE id_user = ?
      AND LOWER(TRIM(email)) = LOWER(TRIM(?))
      AND id_asesor <> ?
    LIMIT 1
");

if (!$stmtEmail) {

    establecerMensajeAsesor(
        'No fue posible validar el correo electrónico.'
    );

    redirigirListaAsesores();
}

$stmtEmail->bind_param(
    'isi',
    $idUser,
    $email,
    $idAsesor
);

if (!$stmtEmail->execute()) {

    $stmtEmail->close();

    establecerMensajeAsesor(
        'No fue posible validar el correo electrónico.'
    );

    redirigirListaAsesores();
}

$resultadoEmail = $stmtEmail->get_result();

$emailDuplicado = (
    $resultadoEmail &&
    $resultadoEmail->num_rows > 0
);

$stmtEmail->close();

if ($emailDuplicado) {

    establecerMensajeAsesor(
        'El correo electrónico ya está registrado en otro asesor.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   PROCESAMIENTO DE IMAGEN
========================================================= */

$hayNuevaImagen = false;
$imagenNueva = null;

if (
    isset($_FILES['imagen']) &&
    is_array($_FILES['imagen'])
) {

    $archivo = $_FILES['imagen'];

    /*
     * UPLOAD_ERR_NO_FILE significa que el usuario
     * no seleccionó ninguna imagen.
     *
     * En ese caso NO modificamos la imagen existente.
     */

    if (
        isset($archivo['error']) &&
        $archivo['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        /* =================================================
           ERROR DE SUBIDA
        ================================================= */

        if ($archivo['error'] !== UPLOAD_ERR_OK) {

            $mensajeErrorImagen =
                'No fue posible subir la nueva imagen.';

            switch ($archivo['error']) {

                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:

                    $mensajeErrorImagen =
                        'La imagen supera el tamaño permitido.';
                    break;

                case UPLOAD_ERR_PARTIAL:

                    $mensajeErrorImagen =
                        'La imagen se subió parcialmente. Inténtalo nuevamente.';
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:

                    $mensajeErrorImagen =
                        'El servidor no tiene disponible la carpeta temporal.';
                    break;

                case UPLOAD_ERR_CANT_WRITE:

                    $mensajeErrorImagen =
                        'El servidor no pudo guardar temporalmente la imagen.';
                    break;

                case UPLOAD_ERR_EXTENSION:

                    $mensajeErrorImagen =
                        'Una extensión del servidor bloqueó la subida de la imagen.';
                    break;
            }

            establecerMensajeAsesor(
                $mensajeErrorImagen
            );

            redirigirListaAsesores();
        }

        /* =================================================
           VALIDAR TAMAÑO
        ================================================= */

        $tamanoImagen = isset($archivo['size'])
            ? (int) $archivo['size']
            : 0;

        if ($tamanoImagen <= 0) {

            establecerMensajeAsesor(
                'La imagen seleccionada no es válida.'
            );

            redirigirListaAsesores();
        }

        if ($tamanoImagen > MAX_TAMANO_IMAGEN) {

            establecerMensajeAsesor(
                'La imagen no debe superar 2.7 MB.'
            );

            redirigirListaAsesores();
        }

        /* =================================================
           VALIDAR ARCHIVO TEMPORAL
        ================================================= */

        $rutaTemporal = $archivo['tmp_name'] ?? '';

        if (
            $rutaTemporal === '' ||
            !is_uploaded_file($rutaTemporal)
        ) {

            establecerMensajeAsesor(
                'El archivo de imagen recibido no es válido.'
            );

            redirigirListaAsesores();
        }

        /* =================================================
           VALIDAR MIME REAL
        ================================================= */

        $tipoMime = '';

        if (class_exists('finfo')) {

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $tipoMime = (string) $finfo->file(
                $rutaTemporal
            );

        } elseif (function_exists('mime_content_type')) {

            $tipoMime = (string) mime_content_type(
                $rutaTemporal
            );
        }

        if (
            $tipoMime === '' ||
            !in_array(
                $tipoMime,
                TIPOS_IMAGEN_PERMITIDOS,
                true
            )
        ) {

            establecerMensajeAsesor(
                'La imagen debe ser JPG, JPEG, PNG o WEBP.'
            );

            redirigirListaAsesores();
        }

        /* =================================================
           VALIDAR QUE REALMENTE SEA UNA IMAGEN
        ================================================= */

        $informacionImagen =
            @getimagesize($rutaTemporal);

        if (
            $informacionImagen === false ||
            empty($informacionImagen[0]) ||
            empty($informacionImagen[1])
        ) {

            establecerMensajeAsesor(
                'El archivo seleccionado no es una imagen válida.'
            );

            redirigirListaAsesores();
        }

        /* =================================================
           VALIDAR MIME DETECTADO POR GETIMAGESIZE
        ================================================= */

        $mimeImagen = $informacionImagen['mime'] ?? '';

        if (
            !in_array(
                $mimeImagen,
                TIPOS_IMAGEN_PERMITIDOS,
                true
            )
        ) {

            establecerMensajeAsesor(
                'El formato de la imagen no está permitido.'
            );

            redirigirListaAsesores();
        }

        /* =================================================
           LEER CONTENIDO DE IMAGEN
        ================================================= */

        $contenidoImagen = @file_get_contents(
            $rutaTemporal
        );

        if (
            $contenidoImagen === false ||
            $contenidoImagen === ''
        ) {

            establecerMensajeAsesor(
                'No fue posible leer la imagen seleccionada.'
            );

            redirigirListaAsesores();
        }

        $imagenNueva = $contenidoImagen;
        $hayNuevaImagen = true;
    }
}

/* =========================================================
   ACTUALIZAR ASESOR
========================================================= */

if ($hayNuevaImagen) {

    /*
     * Se seleccionó una nueva imagen:
     * actualizamos todos los campos incluyendo imagen.
     */

    $stmtUpdate = $conexion->prepare("
        UPDATE asesores
        SET
            nombre = ?,
            apellidos = ?,
            imagen = ?,
            email = ?,
            celular = ?,
            cargo = ?,
            fecha_actualizacion = CURDATE()
        WHERE id_asesor = ?
          AND id_user = ?
        LIMIT 1
    ");

    if (!$stmtUpdate) {

        establecerMensajeAsesor(
            'No fue posible preparar la actualización del asesor.'
        );

        redirigirListaAsesores();
    }

    /*
     * BLOB:
     * usamos bind_param con 's' para enviar el contenido
     * binario de la imagen.
     */

    $stmtUpdate->bind_param(
        'ssssssii',
        $nombre,
        $apellidos,
        $imagenNueva,
        $email,
        $celular,
        $cargo,
        $idAsesor,
        $idUser
    );

} else {

    /*
     * No se seleccionó imagen:
     * conservamos la imagen existente.
     */

    $stmtUpdate = $conexion->prepare("
        UPDATE asesores
        SET
            nombre = ?,
            apellidos = ?,
            email = ?,
            celular = ?,
            cargo = ?,
            fecha_actualizacion = CURDATE()
        WHERE id_asesor = ?
          AND id_user = ?
        LIMIT 1
    ");

    if (!$stmtUpdate) {

        establecerMensajeAsesor(
            'No fue posible preparar la actualización del asesor.'
        );

        redirigirListaAsesores();
    }

    $stmtUpdate->bind_param(
        'sssssii',
        $nombre,
        $apellidos,
        $email,
        $celular,
        $cargo,
        $idAsesor,
        $idUser
    );
}

/* =========================================================
   EJECUTAR ACTUALIZACIÓN
========================================================= */

if (!$stmtUpdate->execute()) {

    $errorMysql = $stmtUpdate->error;

    $stmtUpdate->close();

    /*
     * No mostramos el error SQL directamente al usuario.
     * Solo lo dejamos registrado en el log del servidor.
     */

    error_log(
        'Error al editar asesor ID ' .
        $idAsesor .
        ': ' .
        $errorMysql
    );

    establecerMensajeAsesor(
        'No fue posible actualizar el asesor. Inténtalo nuevamente.'
    );

    redirigirListaAsesores();
}

/* =========================================================
   VERIFICAR FILAS AFECTADAS
========================================================= */

$filasAfectadas = $stmtUpdate->affected_rows;

$stmtUpdate->close();

/*
 * affected_rows puede ser 0 cuando el usuario guarda
 * exactamente los mismos datos.
 *
 * Eso NO significa necesariamente que haya ocurrido un error.
 *
 * Como la consulta se ejecutó correctamente, consideramos
 * la operación exitosa.
 */

/* =========================================================
   MENSAJE DE ÉXITO
========================================================= */

if ($hayNuevaImagen) {

    establecerMensajeAsesor(
        'El asesor fue actualizado correctamente y su fotografía fue reemplazada.',
        'success'
    );

} else {

    establecerMensajeAsesor(
        'El asesor fue actualizado correctamente.',
        'success'
    );
}

/* =========================================================
   REDIRECCIÓN FINAL
========================================================= */

redirigirListaAsesores();