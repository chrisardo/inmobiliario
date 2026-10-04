<?php
// =========================================================
// CoDevPro Technology
// Archivo: controladores/procesar_lista_asesores.php
// Módulo: Lista de Asesores
// Sistema: Inmobiliario
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================
   SEGURIDAD DE SESIÓN
========================================================= */

if (
    !isset($_SESSION['usId']) ||
    !is_numeric($_SESSION['usId'])
) {
    header("Location: ../login.php");
    exit();
}

/* =========================================================
   CONEXIÓN
========================================================= */

require_once __DIR__ . '/conect_db.php';

/* =========================================================
   VALIDAR CONEXIÓN
========================================================= */

if (!isset($conexion) || !($conexion instanceof mysqli)) {
    die('Error: No se pudo establecer la conexión con la base de datos.');
}

/* =========================================================
   USUARIO ACTUAL
========================================================= */

$usId = (int) $_SESSION['usId'];

/* =========================================================
   CSRF
========================================================= */

if (empty($_SESSION['csrf_asesores'])) {

    try {

        $_SESSION['csrf_asesores'] =
            bin2hex(random_bytes(32));

    } catch (Throwable $e) {

        $_SESSION['csrf_asesores'] =
            hash(
                'sha256',
                uniqid(
                    (string) mt_rand(),
                    true
                )
            );
    }
}

/* =========================================================
   VARIABLES PARA LA VISTA
========================================================= */

$porPagina = 5;

$pagina = 1;

$busqueda = '';

$resultado = false;

$totalAsesores = 0;

$totalPaginas = 1;

/* =========================================================
   FUNCIÓN REDIRECCIÓN
========================================================= */

function redirigirListaAsesores($buscar = '')
{
    $url = '../adm/adm_lista_asesores.php';

    $buscar = trim((string) $buscar);

    if ($buscar !== '') {

        $url .=
            '?buscar=' .
            urlencode($buscar);
    }

    header('Location: ' . $url);
    exit();
}

/* =========================================================
   CAMBIAR ESTADO DEL ASESOR
========================================================= */

if (isset($_GET['cambiar_estado'])) {

    /* =====================================================
       VALIDAR ID
    ===================================================== */

    $idAsesor = filter_var(
        $_GET['cambiar_estado'],
        FILTER_VALIDATE_INT
    );

    if ($idAsesor === false || $idAsesor <= 0) {

        $_SESSION['mensajeAsesor'] =
            'El asesor seleccionado no es válido.';

        $_SESSION['tipoAsesor'] =
            'danger';

        redirigirListaAsesores(
            $_GET['buscar'] ?? ''
        );
    }

    $idAsesor = (int) $idAsesor;

    /* =====================================================
       OBTENER ESTADO ACTUAL
    ===================================================== */

    $stmtEstado = $conexion->prepare("
        SELECT
            id_asesor,
            estado
        FROM asesores
        WHERE id_asesor = ?
          AND id_user = ?
        LIMIT 1
    ");

    if (!$stmtEstado) {

        $_SESSION['mensajeAsesor'] =
            'No fue posible consultar el estado del asesor.';

        $_SESSION['tipoAsesor'] =
            'danger';

        redirigirListaAsesores(
            $_GET['buscar'] ?? ''
        );
    }

    $stmtEstado->bind_param(
        'ii',
        $idAsesor,
        $usId
    );

    if (!$stmtEstado->execute()) {

        $stmtEstado->close();

        $_SESSION['mensajeAsesor'] =
            'No fue posible consultar el estado del asesor.';

        $_SESSION['tipoAsesor'] =
            'danger';

        redirigirListaAsesores(
            $_GET['buscar'] ?? ''
        );
    }

    $resultadoEstado =
        $stmtEstado->get_result();

    if (
        !$resultadoEstado ||
        $resultadoEstado->num_rows === 0
    ) {

        $stmtEstado->close();

        $_SESSION['mensajeAsesor'] =
            'El asesor no existe o no tienes permiso para modificarlo.';

        $_SESSION['tipoAsesor'] =
            'danger';

        redirigirListaAsesores(
            $_GET['buscar'] ?? ''
        );
    }

    $filaEstado =
        $resultadoEstado->fetch_assoc();

    $estadoActual = strtoupper(
        trim(
            (string) (
                $filaEstado['estado']
                ?? 'ACTIVO'
            )
        )
    );

    $stmtEstado->close();

    /* =====================================================
       DETERMINAR NUEVO ESTADO
    ===================================================== */

    if ($estadoActual === 'ACTIVO') {

        $nuevoEstado = 'INACTIVO';

    } else {

        $nuevoEstado = 'ACTIVO';
    }

    /* =====================================================
       ACTUALIZAR ESTADO
       
       La tabla asesores tiene:
       fecha_actualizacion DATE
    ===================================================== */

    $stmtActualizar = $conexion->prepare("
        UPDATE asesores
        SET
            estado = ?,
            fecha_actualizacion = CURDATE()
        WHERE id_asesor = ?
          AND id_user = ?
        LIMIT 1
    ");

    if (!$stmtActualizar) {

        $_SESSION['mensajeAsesor'] =
            'No fue posible preparar el cambio de estado.';

        $_SESSION['tipoAsesor'] =
            'danger';

        redirigirListaAsesores(
            $_GET['buscar'] ?? ''
        );
    }

    $stmtActualizar->bind_param(
        'sii',
        $nuevoEstado,
        $idAsesor,
        $usId
    );

    if ($stmtActualizar->execute()) {

        if ($stmtActualizar->affected_rows > 0) {

            if ($nuevoEstado === 'ACTIVO') {

                $_SESSION['mensajeAsesor'] =
                    'El asesor ha sido activado correctamente.';

            } else {

                $_SESSION['mensajeAsesor'] =
                    'El asesor ha sido marcado como inactivo.';
            }

            $_SESSION['tipoAsesor'] =
                'success';

        } else {

            $_SESSION['mensajeAsesor'] =
                'No se realizaron cambios en el estado del asesor.';

            $_SESSION['tipoAsesor'] =
                'warning';
        }

    } else {

        $_SESSION['mensajeAsesor'] =
            'No fue posible cambiar el estado del asesor.';

        $_SESSION['tipoAsesor'] =
            'danger';
    }

    $stmtActualizar->close();

    /* =====================================================
       REGRESAR CONSERVANDO BÚSQUEDA
    ===================================================== */

    redirigirListaAsesores(
        $_GET['buscar'] ?? ''
    );
}

/* =========================================================
   PAGINACIÓN
========================================================= */

if (
    isset($_GET['pagina']) &&
    is_numeric($_GET['pagina'])
) {

    $pagina =
        (int) $_GET['pagina'];
}

if ($pagina < 1) {
    $pagina = 1;
}

/* =========================================================
   BÚSQUEDA
========================================================= */

$busqueda = isset($_GET['buscar'])
    ? trim((string) $_GET['buscar'])
    : '';

/* =========================================================
   LIMITAR BÚSQUEDA EXTREMADAMENTE LARGA
========================================================= */

if (mb_strlen($busqueda, 'UTF-8') > 150) {

    $busqueda =
        mb_substr(
            $busqueda,
            0,
            150,
            'UTF-8'
        );
}

/* =========================================================
   OBTENER TOTAL DE ASESORES
========================================================= */

if ($busqueda !== '') {

    $busquedaLike =
        '%' . $busqueda . '%';

    $stmtTotal = $conexion->prepare("
        SELECT
            COUNT(*) AS total
        FROM asesores
        WHERE id_user = ?
          AND (
                nombre LIKE ?
                OR apellidos LIKE ?
                OR email LIKE ?
                OR celular LIKE ?
                OR cargo LIKE ?
                OR estado LIKE ?
                OR DATE_FORMAT(
                    fecha_registro,
                    '%d/%m/%Y'
                ) LIKE ?
                OR DATE_FORMAT(
                    fecha_registro,
                    '%Y-%m-%d'
                ) LIKE ?
              )
    ");

    if (!$stmtTotal) {

        die(
            'Error preparando total de asesores: ' .
            $conexion->error
        );
    }

    /*
     * 1 entero + 8 strings
     *
     * i s s s s s s s s
     */

    $stmtTotal->bind_param(
        'issssssss',
        $usId,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike
    );

} else {

    $stmtTotal = $conexion->prepare("
        SELECT
            COUNT(*) AS total
        FROM asesores
        WHERE id_user = ?
    ");

    if (!$stmtTotal) {

        die(
            'Error preparando total de asesores: ' .
            $conexion->error
        );
    }

    $stmtTotal->bind_param(
        'i',
        $usId
    );
}

/* =========================================================
   EJECUTAR TOTAL
========================================================= */

if (!$stmtTotal->execute()) {

    $errorTotal =
        $stmtTotal->error;

    $stmtTotal->close();

    die(
        'Error calculando total de asesores: ' .
        $errorTotal
    );
}

$resultadoTotal =
    $stmtTotal->get_result();

if (
    $resultadoTotal &&
    ($filaTotal = $resultadoTotal->fetch_assoc())
) {

    $totalAsesores =
        (int) (
            $filaTotal['total']
            ?? 0
        );
}

$stmtTotal->close();

/* =========================================================
   CALCULAR TOTAL DE PÁGINAS
========================================================= */

$totalPaginas = max(
    1,
    (int) ceil(
        $totalAsesores /
        $porPagina
    )
);

/* =========================================================
   CORREGIR PÁGINA
========================================================= */

if ($pagina > $totalPaginas) {

    $pagina =
        $totalPaginas;
}

/* =========================================================
   OFFSET
========================================================= */

$inicio =
    ($pagina - 1) *
    $porPagina;

/* =========================================================
   CONSULTAR ASESORES
========================================================= */

if ($busqueda !== '') {

    $busquedaLike =
        '%' . $busqueda . '%';

    $stmt = $conexion->prepare("
        SELECT
            a.id_asesor,
            a.id_user,
            a.nombre,
            a.apellidos,
            a.imagen,
            a.email,
            a.celular,
            a.cargo,
            a.fecha_registro,
            a.fecha_actualizacion,
            a.estado
        FROM asesores AS a
        WHERE a.id_user = ?
          AND (
                a.nombre LIKE ?
                OR a.apellidos LIKE ?
                OR a.email LIKE ?
                OR a.celular LIKE ?
                OR a.cargo LIKE ?
                OR a.estado LIKE ?
                OR DATE_FORMAT(
                    a.fecha_registro,
                    '%d/%m/%Y'
                ) LIKE ?
                OR DATE_FORMAT(
                    a.fecha_registro,
                    '%Y-%m-%d'
                ) LIKE ?
              )
        ORDER BY
            a.id_asesor DESC
        LIMIT ?, ?
    ");

    if (!$stmt) {

        die(
            'Error preparando consulta de asesores: ' .
            $conexion->error
        );
    }

    /*
     * 1 entero
     * 8 strings
     * 2 enteros
     *
     * i s s s s s s s s i i
     */

    $stmt->bind_param(
        'issssssssii',
        $usId,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $busquedaLike,
        $inicio,
        $porPagina
    );

} else {

    $stmt = $conexion->prepare("
        SELECT
            a.id_asesor,
            a.id_user,
            a.nombre,
            a.apellidos,
            a.imagen,
            a.email,
            a.celular,
            a.cargo,
            a.fecha_registro,
            a.fecha_actualizacion,
            a.estado
        FROM asesores AS a
        WHERE a.id_user = ?
        ORDER BY
            a.id_asesor DESC
        LIMIT ?, ?
    ");

    if (!$stmt) {

        die(
            'Error preparando consulta de asesores: ' .
            $conexion->error
        );
    }

    $stmt->bind_param(
        'iii',
        $usId,
        $inicio,
        $porPagina
    );
}

/* =========================================================
   EJECUTAR LISTA
========================================================= */

if (!$stmt->execute()) {

    $errorLista =
        $stmt->error;

    $stmt->close();

    die(
        'Error ejecutando consulta de asesores: ' .
        $errorLista
    );
}

/* =========================================================
   RESULTADO
========================================================= */

$resultado =
    $stmt->get_result();

/*
 * IMPORTANTE:
 *
 * No cerramos $stmt aquí porque $resultado
 * todavía será utilizado por adm_lista_asesores.php.
 */

/* =========================================================
   VARIABLES DISPONIBLES PARA LA VISTA
========================================================= */

/*
 * $resultado
 * $busqueda
 * $pagina
 * $totalAsesores
 * $totalPaginas
 * $porPagina
 *
 */

/* =========================================================
   FIN
========================================================= */