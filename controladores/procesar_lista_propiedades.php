<?php

/**
 * =========================================================
 * CoDevPro Technology
 * Archivo: controladores/procesar_lista_propiedades.php
 * Módulo: Lista de Propiedades
 *
 * Funciones:
 * - Obtener información del usuario
 * - Activar / inhabilitar propiedades
 * - Buscar propiedades
 * - Filtrar por categoría
 * - Paginar propiedades
 * - Obtener categoría e imagen principal
 * - Contabilizar propiedades activas
 * - Contabilizar propiedades inhabilitadas
 * - Contabilizar mensajes no leídos
 *
 * ESTADO DE PROPIEDAD:
 *
 * Eliminado = 0 / NULL -> ACTIVO
 * Eliminado = 1        -> INHABILITADO
 *
 * Las propiedades inhabilitadas NO se eliminan físicamente.
 * =========================================================
 */


/* =========================================================
   SESIÓN
========================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =========================================================
   SEGURIDAD
========================================================= */

if (!isset($_SESSION['usId'])) {

    header("Location: ../login.php");
    exit();
}

require_once 'conect_db.php';
$usId = (int) $_SESSION['usId'];


/* =========================================================
   CONEXIÓN
========================================================= */

if (!isset($conexion) || !($conexion instanceof mysqli)) {

    die("Error de conexión con la base de datos.");
}


/* =========================================================
   CSRF
========================================================= */

if (empty($_SESSION['csrf_propiedades'])) {

    $_SESSION['csrf_propiedades'] =
        bin2hex(random_bytes(32));
}

$csrfToken =
    $_SESSION['csrf_propiedades'];


/* =========================================================
   ACTIVAR / INHABILITAR PROPIEDAD
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['cambiar_estado_propiedad'])
) {

    $idPropiedad = filter_input(
        INPUT_POST,
        'id_propiedad',
        FILTER_VALIDATE_INT
    );

    $nuevoEstado = filter_input(
        INPUT_POST,
        'nuevo_estado',
        FILTER_VALIDATE_INT
    );

    $token =
        $_POST['csrf_token'] ?? '';


    /* =====================================================
       VALIDAR DATOS
    ===================================================== */

    if (
        !$idPropiedad ||
        ($nuevoEstado !== 0 && $nuevoEstado !== 1) ||
        !hash_equals($csrfToken, $token)
    ) {

        $_SESSION['mensaje_propiedad'] =
            'No fue posible cambiar el estado de la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] =
            'danger';

        header(
            "Location: ../adm/adm_lista_propiedades.php"
        );

        exit();
    }


    /* =====================================================
       VERIFICAR PROPIEDAD
    ===================================================== */

    $sqlVerificar = "
        SELECT
            id_propiedad,
            nombre,
            Eliminado
        FROM propiedades
        WHERE id_propiedad = ?
          AND id_user = ?
        LIMIT 1
    ";


    $stmt =
        $conexion->prepare($sqlVerificar);


    if (!$stmt) {

        $_SESSION['mensaje_propiedad'] =
            'Error al verificar la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] =
            'danger';

        header(
            "Location: ../adm/adm_lista_propiedades.php"
        );

        exit();
    }


    $stmt->bind_param(
        "ii",
        $idPropiedad,
        $usId
    );


    if (!$stmt->execute()) {

        $stmt->close();

        $_SESSION['mensaje_propiedad'] =
            'Error al verificar la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] =
            'danger';

        header(
            "Location: ../adm/adm_lista_propiedades.php"
        );

        exit();
    }


    $resultadoVerificar =
        $stmt->get_result();


    $propiedad =
        $resultadoVerificar
        ? $resultadoVerificar->fetch_assoc()
        : null;


    $stmt->close();


    /* =====================================================
       PROPIEDAD NO ENCONTRADA
    ===================================================== */

    if (!$propiedad) {

        $_SESSION['mensaje_propiedad'] =
            'La propiedad no existe o no tienes permiso para modificarla.';

        $_SESSION['tipo_alerta_propiedad'] =
            'warning';

        header(
            "Location: ../adm/adm_lista_propiedades.php"
        );

        exit();
    }


    /* =====================================================
       ACTUALIZAR ESTADO
    ===================================================== */

    $sqlEstado = "
        UPDATE propiedades
        SET
            Eliminado = ?,
            fecha_actualizacion = CURDATE()
        WHERE id_propiedad = ?
          AND id_user = ?
    ";


    $stmt =
        $conexion->prepare($sqlEstado);


    if (!$stmt) {

        $_SESSION['mensaje_propiedad'] =
            'No fue posible preparar el cambio de estado.';

        $_SESSION['tipo_alerta_propiedad'] =
            'danger';

        header(
            "Location: ../adm/adm_lista_propiedades.php"
        );

        exit();
    }


    $stmt->bind_param(
        "iii",
        $nuevoEstado,
        $idPropiedad,
        $usId
    );


    if ($stmt->execute()) {

        if ($nuevoEstado === 1) {

            $_SESSION['mensaje_propiedad'] =
                'La propiedad fue inhabilitada correctamente.';

            $_SESSION['tipo_alerta_propiedad'] =
                'warning';

        } else {

            $_SESSION['mensaje_propiedad'] =
                'La propiedad fue activada correctamente.';

            $_SESSION['tipo_alerta_propiedad'] =
                'success';
        }

    } else {

        $_SESSION['mensaje_propiedad'] =
            'Ocurrió un error al cambiar el estado de la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] =
            'danger';
    }


    $stmt->close();


    /* =====================================================
       PRG
    ===================================================== */

    header(
        "Location: ../adm/adm_lista_propiedades.php"
    );

    exit();
}


/* =========================================================
   MENSAJES DE SESIÓN
========================================================= */

$mensaje = '';
$tipoAlerta = '';


if (!empty($_SESSION['mensaje_propiedad'])) {

    /*
     * Compatible con el formato antiguo:
     *
     * $_SESSION['mensaje_propiedad'] = [
     *     'tipo' => 'success',
     *     'texto' => '...'
     * ];
     *
     * y también con el formato actual:
     *
     * $_SESSION['mensaje_propiedad'] = '...';
     */

    if (is_array($_SESSION['mensaje_propiedad'])) {

        $mensaje =
            $_SESSION['mensaje_propiedad']['texto'] ?? '';

        $tipoAlerta =
            $_SESSION['mensaje_propiedad']['tipo'] ?? '';

    } else {

        $mensaje =
            $_SESSION['mensaje_propiedad'];

        $tipoAlerta =
            $_SESSION['tipo_alerta_propiedad'] ?? '';
    }


    unset($_SESSION['mensaje_propiedad']);
    unset($_SESSION['tipo_alerta_propiedad']);
}


/* =========================================================
   INFORMACIÓN DEL USUARIO
========================================================= */

$sqlUsuario = "
    SELECT
        imagen,
        nombreEmpresa
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
";


$stmt =
    $conexion->prepare($sqlUsuario);


$usuario = null;


if ($stmt) {

    $stmt->bind_param(
        "i",
        $usId
    );


    if ($stmt->execute()) {

        $resultadoUsuario =
            $stmt->get_result();


        if ($resultadoUsuario) {

            $usuario =
                $resultadoUsuario->fetch_assoc();
        }
    }


    $stmt->close();
}


$fotoPerfil = null;
$nombreEmpresa = '';


if ($usuario) {

    $nombreEmpresa =
        $usuario['nombreEmpresa'] ?? '';


    if (!empty($usuario['imagen'])) {

        $fotoPerfil =
            'data:image/jpeg;base64,' .
            base64_encode(
                $usuario['imagen']
            );
    }
}


/* =========================================================
   KPI DE PROPIEDADES
========================================================= */

/*
 * IMPORTANTE:
 *
 * Los KPI se calculan independientemente de:
 *
 * - búsqueda
 * - categoría
 * - página actual
 *
 * Por lo tanto muestran el total real del usuario.
 *
 * ACTIVO:
 *     Eliminado = 0
 *     O Eliminado IS NULL
 *
 * INHABILITADO:
 *     Eliminado = 1
 */

$totalPropiedadesKPI = 0;
$totalActivas = 0;
$totalInhabilitadas = 0;


$sqlKpiPropiedades = "
    SELECT

        COUNT(*) AS total,

        SUM(
            CASE
                WHEN Eliminado = 1
                THEN 0
                ELSE 1
            END
        ) AS activas,

        SUM(
            CASE
                WHEN Eliminado = 1
                THEN 1
                ELSE 0
            END
        ) AS inhabilitadas

    FROM propiedades

    WHERE id_user = ?
";


$stmtKpi =
    $conexion->prepare(
        $sqlKpiPropiedades
    );


if ($stmtKpi) {

    $stmtKpi->bind_param(
        "i",
        $usId
    );


    if ($stmtKpi->execute()) {

        $resultadoKpi =
            $stmtKpi->get_result();


        if ($resultadoKpi) {

            $filaKpi =
                $resultadoKpi->fetch_assoc();


            $totalPropiedadesKPI =
                (int) (
                    $filaKpi['total'] ?? 0
                );


            $totalActivas =
                (int) (
                    $filaKpi['activas'] ?? 0
                );


            $totalInhabilitadas =
                (int) (
                    $filaKpi['inhabilitadas'] ?? 0
                );
        }
    }


    $stmtKpi->close();
}


/* =========================================================
   PAGINACIÓN
========================================================= */

$porPagina = 10;


$pagina = filter_input(
    INPUT_GET,
    'pagina',
    FILTER_VALIDATE_INT
);


if (!$pagina || $pagina < 1) {

    $pagina = 1;
}


/* =========================================================
   BÚSQUEDA
========================================================= */

$busqueda =
    trim(
        $_GET['buscar'] ?? ''
    );


if (mb_strlen($busqueda) > 100) {

    $busqueda =
        mb_substr(
            $busqueda,
            0,
            100
        );
}


/* =========================================================
   FILTRO DE CATEGORÍA
========================================================= */

$idCategoriaFiltro =
    filter_input(
        INPUT_GET,
        'categoria',
        FILTER_VALIDATE_INT
    );


if (
    !$idCategoriaFiltro ||
    $idCategoriaFiltro < 1
) {

    $idCategoriaFiltro = 0;
}


/* =========================================================
   OBTENER CATEGORÍAS ACTIVAS
========================================================= */

$sqlCategorias = "
    SELECT
        id_categoria,
        nombre
    FROM categoria
    WHERE id_user = ?
      AND (
            Eliminado = 0
            OR Eliminado IS NULL
      )
    ORDER BY nombre ASC
";


$stmt =
    $conexion->prepare(
        $sqlCategorias
    );


$categorias = [];


if ($stmt) {

    $stmt->bind_param(
        "i",
        $usId
    );


    if ($stmt->execute()) {

        $resultadoCategorias =
            $stmt->get_result();


        if ($resultadoCategorias) {

            while (
                $categoria =
                $resultadoCategorias->fetch_assoc()
            ) {

                $categorias[] =
                    $categoria;
            }
        }
    }


    $stmt->close();
}


/* =========================================================
   CONSTRUCCIÓN DE FILTROS
========================================================= */

/*
 * NO se filtra Eliminado aquí.
 *
 * Se muestran:
 *
 * 0 / NULL = ACTIVO
 * 1        = INHABILITADO
 */

$where = [
    "p.id_user = ?"
];


$types = "i";


$params = [
    $usId
];


/* =========================================================
   BÚSQUEDA GENERAL
========================================================= */

if ($busqueda !== '') {

    $where[] = "
        (
            p.nombre LIKE ?
            OR p.codigo LIKE ?
            OR p.ubicacion LIKE ?
            OR c.nombre LIKE ?
            OR DATE_FORMAT(
                p.fecha_registro,
                '%d/%m/%Y'
            ) LIKE ?
        )
    ";


    $termino =
        '%' . $busqueda . '%';


    $types .= "sssss";


    $params[] =
        $termino;

    $params[] =
        $termino;

    $params[] =
        $termino;

    $params[] =
        $termino;

    $params[] =
        $termino;
}


/* =========================================================
   FILTRO POR CATEGORÍA
========================================================= */

if ($idCategoriaFiltro > 0) {

    $where[] =
        "p.id_categoria = ?";


    $types .= "i";


    $params[] =
        $idCategoriaFiltro;
}


/* =========================================================
   SQL WHERE
========================================================= */

$whereSql =
    implode(
        " AND ",
        $where
    );


/* =========================================================
   TOTAL DE PROPIEDADES SEGÚN FILTROS
========================================================= */

$sqlTotal = "
    SELECT
        COUNT(*) AS total

    FROM propiedades p

    LEFT JOIN categoria c
        ON c.id_categoria = p.id_categoria
        AND c.id_user = p.id_user

    WHERE $whereSql
";


$stmt =
    $conexion->prepare(
        $sqlTotal
    );


$totalPropiedades = 0;


if ($stmt) {

    $stmt->bind_param(
        $types,
        ...$params
    );


    if ($stmt->execute()) {

        $resultadoTotal =
            $stmt->get_result();


        if ($resultadoTotal) {

            $filaTotal =
                $resultadoTotal->fetch_assoc();


            $totalPropiedades =
                (int) (
                    $filaTotal['total'] ?? 0
                );
        }
    }


    $stmt->close();
}


/* =========================================================
   PAGINACIÓN
========================================================= */

$totalPaginas =
    max(
        1,
        (int) ceil(
            $totalPropiedades /
            $porPagina
        )
    );


if ($pagina > $totalPaginas) {

    $pagina =
        $totalPaginas;
}


$inicio =
    ($pagina - 1) *
    $porPagina;


/* =========================================================
   CONSULTAR PROPIEDADES
========================================================= */

$sqlPropiedades = "
    SELECT

        p.id_propiedad,
        p.nombre,
        p.codigo,
        p.id_categoria,
        p.tamano_area_metros,
        p.precio,
        p.precio_anterior,
        p.ubicacion,
        p.fecha_registro,
        p.fecha_actualizacion,

        /*
         * Estado lógico
         *
         * 0 / NULL = ACTIVO
         * 1        = INHABILITADO
         */

        CASE
            WHEN p.Eliminado = 1
                THEN 'INHABILITADO'
            ELSE 'ACTIVO'
        END AS estado_propiedad,

        COALESCE(
            p.Eliminado,
            0
        ) AS eliminado,

        c.nombre AS nombre_categoria,

        i.id_imagen,

        i.imagenes AS imagen_principal,

        (
            SELECT COUNT(*)
            FROM imagenes ix
            WHERE ix.id_propiedad =
                  p.id_propiedad
        ) AS total_imagenes

    FROM propiedades p

    LEFT JOIN categoria c
        ON c.id_categoria = p.id_categoria
        AND c.id_user = p.id_user

    LEFT JOIN imagenes i
        ON i.id_imagen = (

            SELECT
                i2.id_imagen

            FROM imagenes i2

            WHERE i2.id_propiedad =
                  p.id_propiedad

            ORDER BY
                i2.orden ASC,
                i2.id_imagen ASC

            LIMIT 1
        )

    WHERE $whereSql

    ORDER BY
        p.id_propiedad DESC

    LIMIT ?, ?
";


$typesPropiedades =
    $types . "ii";


$paramsPropiedades =
    $params;


$paramsPropiedades[] =
    $inicio;


$paramsPropiedades[] =
    $porPagina;


$stmt =
    $conexion->prepare(
        $sqlPropiedades
    );


$resultado = false;


if ($stmt) {

    $stmt->bind_param(
        $typesPropiedades,
        ...$paramsPropiedades
    );


    if ($stmt->execute()) {

        $resultado =
            $stmt->get_result();
    }
}


/* =========================================================
   FUNCIÓN URL PAGINACIÓN
========================================================= */

function urlPropiedades(
    int $pagina
): string {

    global
        $busqueda,
        $idCategoriaFiltro;


    $parametros = [
        'pagina' => $pagina
    ];


    if ($busqueda !== '') {

        $parametros['buscar'] =
            $busqueda;
    }


    if ($idCategoriaFiltro > 0) {

        $parametros['categoria'] =
            $idCategoriaFiltro;
    }


    return '?' .
        http_build_query(
            $parametros
        );
}


/* =========================================================
   RANGO DE REGISTROS
========================================================= */

$registroInicio =
    $totalPropiedades > 0
        ? $inicio + 1
        : 0;


$registroFin =
    min(
        $inicio + $porPagina,
        $totalPropiedades
    );


/* =========================================================
   MENSAJES NO LEÍDOS
========================================================= */

$totalMensajes = 0;


$sqlMensajesNoLeidos = "
    SELECT
        COUNT(*) AS total

    FROM mensajes AS m

    INNER JOIN propiedades AS p
        ON p.id_propiedad =
           m.id_propiedad

    WHERE p.id_user = ?

      AND m.estado_mensaje_leido = 0
";


$stmtMensajesNoLeidos =
    $conexion->prepare(
        $sqlMensajesNoLeidos
    );


if ($stmtMensajesNoLeidos) {

    $stmtMensajesNoLeidos->bind_param(
        "i",
        $usId
    );


    if (
        $stmtMensajesNoLeidos->execute()
    ) {

        $resultadoMensajesNoLeidos =
            $stmtMensajesNoLeidos
            ->get_result();


        if ($resultadoMensajesNoLeidos) {

            $filaMensajesNoLeidos =
                $resultadoMensajesNoLeidos
                ->fetch_assoc();


            $totalMensajes =
                (int) (
                    $filaMensajesNoLeidos['total']
                    ?? 0
                );
        }
    }


    $stmtMensajesNoLeidos->close();
}


/* =========================================================
   FIN DEL PROCESADOR
========================================================= */