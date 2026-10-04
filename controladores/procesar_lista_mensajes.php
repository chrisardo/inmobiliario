<?php
//=========================================================
// CoDevPro Technology
// Archivo: controladores/procesar_lista_mensajes.php
// Módulo: Mensajes
// Sistema: Inmobiliario
//=========================================================

/* =========================================================
   USUARIO LOGUEADO
========================================================= */

$usId = isset($_SESSION['usId'])
    ? (int)$_SESSION['usId']
    : 0;

if ($usId <= 0) {
    header("Location: ../login.php");
    exit();
}


/* =========================================================
   PERFIL DEL USUARIO
========================================================= */

$sqlFoto = "
    SELECT
        imagen,
        nombreEmpresa
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
";

$stmt = $conexion->prepare($sqlFoto);

if ($stmt) {

    $stmt->bind_param(
        "i",
        $usId
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $usuario = $result->fetch_assoc();

    $stmt->close();

} else {

    $usuario = [];
}


$fotoPerfil = null;

if (!empty($usuario['imagen'])) {

    $fotoPerfil =
        'data:image/jpeg;base64,' .
        base64_encode($usuario['imagen']);
}


/* =========================================================
   KPI DE MENSAJES
   SOLO DEL USUARIO LOGUEADO
========================================================= */

$sqlKpiMensajes = "
    SELECT

        COUNT(*) AS total_mensajes,

        COALESCE(
            SUM(
                CASE
                    WHEN m.estado_mensaje_leido = 1
                    THEN 1
                    ELSE 0
                END
            ),
            0
        ) AS total_leidos,

        COALESCE(
            SUM(
                CASE
                    WHEN m.estado_mensaje_leido = 0
                    OR m.estado_mensaje_leido IS NULL
                    THEN 1
                    ELSE 0
                END
            ),
            0
        ) AS total_no_leidos

    FROM mensajes m

    INNER JOIN propiedades p
        ON p.id_propiedad = m.id_propiedad

    WHERE p.id_user = ? 
";


$stmtKpiMensajes =
    $conexion->prepare($sqlKpiMensajes);

$totalMensajesKpi = 0;
$totalLeidos = 0;
$totalNoLeidos = 0;

if ($stmtKpiMensajes) {

    $stmtKpiMensajes->bind_param(
        "i",
        $usId
    );

    $stmtKpiMensajes->execute();

    $resultadoKpiMensajes =
        $stmtKpiMensajes->get_result();

    if ($resultadoKpiMensajes) {

        $kpi =
            $resultadoKpiMensajes->fetch_assoc();

        $totalMensajesKpi =
            (int)($kpi['total_mensajes'] ?? 0);

        $totalLeidos =
            (int)($kpi['total_leidos'] ?? 0);

        $totalNoLeidos =
            (int)($kpi['total_no_leidos'] ?? 0);
    }

    $stmtKpiMensajes->close();
}


/* =========================================================
   TOTAL DE MENSAJES PARA NOTIFICACIONES
========================================================= */

$totalMensaje = $totalNoLeidos;


/* =========================================================
   ELIMINAR
========================================================= */

if (isset($_GET['eliminar'])) {

    $id = (int)$_GET['eliminar'];

    $stmtEliminar = $conexion->prepare("
        DELETE m
        FROM mensajes m

        INNER JOIN propiedades p
            ON p.id_propiedad = m.id_propiedad

        WHERE
            m.id_contacto = ?
            AND p.id_user = ?
    ");

    if ($stmtEliminar) {

        $stmtEliminar->bind_param(
            "ii",
            $id,
            $usId
        );

        $stmtEliminar->execute();

        $stmtEliminar->close();
    }

    header(
        "Location: ../adm/adm_mensajes.php"
    );

    exit();
}


/* =========================================================
   PAGINACIÓN
========================================================= */

$porPagina = 5;

$pagina =
    isset($_GET['pagina']) &&
    is_numeric($_GET['pagina'])
        ? (int)$_GET['pagina']
        : 1;

if ($pagina < 1) {
    $pagina = 1;
}


/* =========================================================
   FILTROS
========================================================= */

/* BUSCADOR */

$busqueda =
    isset($_GET['buscar'])
        ? trim($_GET['buscar'])
        : '';


/* PROPIEDAD */

$filtroPropiedad =
    isset($_GET['propiedad']) &&
    is_numeric($_GET['propiedad'])
        ? (int)$_GET['propiedad']
        : 0;


/* ESTADO */

$filtroEstado =
    isset($_GET['estado'])
        ? trim($_GET['estado'])
        : '';


/*
 * Valores permitidos:
 *
 * ''         = todos
 * leido      = leídos
 * no_leido   = no leídos
 */

if (
    !in_array(
        $filtroEstado,
        ['', 'leido', 'no_leido'],
        true
    )
) {
    $filtroEstado = '';
}


/* FECHA DESDE */

$fechaDesde =
    isset($_GET['fecha_desde'])
        ? trim($_GET['fecha_desde'])
        : '';


/* FECHA HASTA */

$fechaHasta =
    isset($_GET['fecha_hasta'])
        ? trim($_GET['fecha_hasta'])
        : '';


/* =========================================================
   CONDICIONES
========================================================= */

$where = "
    WHERE p.id_user = ?
";

$parametros = [
    $usId
];

$tipos = "i";


/* =========================================================
   FILTRO DE BÚSQUEDA
========================================================= */

if ($busqueda !== '') {

    $where .= "
        AND (
            m.nombre LIKE ?
            OR m.apellidos LIKE ?
            OR m.email LIKE ?
            OR m.celular LIKE ?
            OR m.mensaje LIKE ?
            OR p.nombre LIKE ?
            OR DATE_FORMAT(m.fecha_registro, '%d/%m/%Y') LIKE ?
        )
    ";

    $termino =
        "%{$busqueda}%";

    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;

    $tipos .= "sssssss";
}


/* =========================================================
   FILTRO POR PROPIEDAD
========================================================= */

if ($filtroPropiedad > 0) {

    $where .= "
        AND m.id_propiedad = ?
    ";

    $parametros[] =
        $filtroPropiedad;

    $tipos .= "i";
}


/* =========================================================
   FILTRO POR ESTADO
========================================================= */

if ($filtroEstado === 'leido') {

    $where .= "
        AND m.estado_mensaje_leido = 1
    ";

} elseif ($filtroEstado === 'no_leido') {

    $where .= "
        AND (
            m.estado_mensaje_leido = 0
            OR m.estado_mensaje_leido IS NULL
        )
    ";
}


/* =========================================================
   FILTRO FECHA DESDE
========================================================= */

if ($fechaDesde !== '') {

    /*
     * Se utiliza >= sobre la fecha sin hora.
     */

    $where .= "
        AND m.fecha_registro >= ?
    ";

    $parametros[] =
        $fechaDesde . " 00:00:00";

    $tipos .= "s";
}


/* =========================================================
   FILTRO FECHA HASTA
========================================================= */

if ($fechaHasta !== '') {

    /*
     * Se utiliza < al día siguiente para incluir
     * todas las horas del día seleccionado.
     */

    $fechaHastaTimestamp =
        strtotime($fechaHasta . " +1 day");

    if ($fechaHastaTimestamp !== false) {

        $fechaHastaFinal =
            date(
                'Y-m-d 00:00:00',
                $fechaHastaTimestamp
            );

        $where .= "
            AND m.fecha_registro < ?
        ";

        $parametros[] =
            $fechaHastaFinal;

        $tipos .= "s";
    }
}


/* =========================================================
   TOTAL DE REGISTROS
========================================================= */

$sqlTotal = "
    SELECT
        COUNT(*) AS total

    FROM mensajes m

    INNER JOIN propiedades p
        ON p.id_propiedad = m.id_propiedad

    {$where}
";


$stmtTotal =
    $conexion->prepare($sqlTotal);

$totalMensaje = 0;

if ($stmtTotal) {

    $stmtTotal->bind_param(
        $tipos,
        ...$parametros
    );

    $stmtTotal->execute();

    $resultadoTotal =
        $stmtTotal->get_result();

    if ($resultadoTotal) {

        $filaTotal =
            $resultadoTotal->fetch_assoc();

        $totalMensaje =
            (int)($filaTotal['total'] ?? 0);
    }

    $stmtTotal->close();
}


/* =========================================================
   TOTAL DE PÁGINAS
========================================================= */

$totalPaginas =
    max(
        1,
        (int)ceil(
            $totalMensaje / $porPagina
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
   INICIO
========================================================= */

$inicio =
    ($pagina - 1) * $porPagina;


/* =========================================================
   CONSULTA DE MENSAJES
========================================================= */

$sql = "
    SELECT
        m.*,
        p.nombre AS nombre_propiedad

    FROM mensajes m

    INNER JOIN propiedades p
        ON p.id_propiedad = m.id_propiedad

    {$where}

    ORDER BY
        m.id_contacto DESC

    LIMIT ?, ?
";


$stmt =
    $conexion->prepare($sql);

if ($stmt) {

    $tiposConsulta =
        $tipos . "ii";

    $parametrosConsulta =
        array_merge(
            $parametros,
            [
                $inicio,
                $porPagina
            ]
        );

    $stmt->bind_param(
        $tiposConsulta,
        ...$parametrosConsulta
    );

    $stmt->execute();

    $resultado =
        $stmt->get_result();

} else {

    $resultado = false;
}


/* =========================================================
   LISTA DE PROPIEDADES PARA EL FILTRO
   SOLO PROPIEDADES DEL USUARIO LOGUEADO
========================================================= */

$propiedadesFiltro = [];

$sqlPropiedades = "
    SELECT
        id_propiedad,
        nombre
    FROM propiedades
    WHERE id_user = ?
    ORDER BY nombre ASC
";

$stmtPropiedades =
    $conexion->prepare($sqlPropiedades);

if ($stmtPropiedades) {

    $stmtPropiedades->bind_param(
        "i",
        $usId
    );

    $stmtPropiedades->execute();

    $resultadoPropiedades =
        $stmtPropiedades->get_result();

    if ($resultadoPropiedades) {

        while (
            $propiedad =
            $resultadoPropiedades->fetch_assoc()
        ) {

            $propiedadesFiltro[] =
                $propiedad;
        }
    }

    $stmtPropiedades->close();
}


/* =========================================================
   INDICADOR DE FILTROS ACTIVOS
========================================================= */

$filtrosActivos = 0;

if ($busqueda !== '') {
    $filtrosActivos++;
}

if ($filtroPropiedad > 0) {
    $filtrosActivos++;
}

if ($filtroEstado !== '') {
    $filtrosActivos++;
}

if ($fechaDesde !== '') {
    $filtrosActivos++;
}

if ($fechaHasta !== '') {
    $filtrosActivos++;
}
