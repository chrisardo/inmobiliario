<?php
// =========================================================
// CoDevPro Technology
// Archivo: adm/adm_lista_asesores.php
// Módulo: Lista de Asesores
// Sistema: Inmobiliario
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================
   VALIDAR SESIÓN
========================================================= */

if (!isset($_SESSION['usId']) || !is_numeric($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

$idUser = (int) $_SESSION['usId'];

/* =========================================================
   CONEXIÓN
========================================================= */

//require_once __DIR__ . "/../controladores/conect_db.php";

/* =========================================================
   PROCESAMIENTO DE LISTA
========================================================= */

require_once '../controladores/procesar_lista_asesores.php';

/* =========================================================
   PROCESAMIENTO DE EDICIÓN
========================================================= */

//require_once '../controladores/editar_asesor.php';

/* =========================================================
   VARIABLES DE SEGURIDAD
========================================================= */

$busqueda = isset($busqueda) ? trim((string) $busqueda) : '';

$pagina = isset($pagina) && is_numeric($pagina)
    ? max(1, (int) $pagina)
    : 1;

$totalAsesores = isset($totalAsesores) && is_numeric($totalAsesores)
    ? (int) $totalAsesores
    : 0;

$totalPaginas = isset($totalPaginas) && is_numeric($totalPaginas)
    ? max(1, (int) $totalPaginas)
    : 1;

/* =========================================================
   FOTO DE PERFIL DEL USUARIO
========================================================= */

$fotoPerfil = null;
$nombreEmpresa = 'Mi empresa';

$stmtFoto = $conexion->prepare("
    SELECT imagen, nombreEmpresa
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
");

if ($stmtFoto) {

    $stmtFoto->bind_param("i", $idUser);
    $stmtFoto->execute();

    $resultFoto = $stmtFoto->get_result();

    if ($resultFoto && ($usuario = $resultFoto->fetch_assoc())) {

        if (!empty($usuario['imagen'])) {

            $fotoPerfil =
                'data:image/jpeg;base64,' .
                base64_encode($usuario['imagen']);
        }

        if (!empty($usuario['nombreEmpresa'])) {
            $nombreEmpresa = $usuario['nombreEmpresa'];
        }
    }

    $stmtFoto->close();
}

/* =========================================================
   KPI
========================================================= */

$totalActivos = 0;
$totalInactivos = 0;
$totalConEmail = 0;

/*
 * Una sola consulta para obtener todos los KPI.
 * Esto evita realizar 3 consultas independientes.
 */

$stmtKpi = $conexion->prepare("
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(
            CASE
                WHEN UPPER(TRIM(estado)) = 'ACTIVO'
                THEN 1
                ELSE 0
            END
        ), 0) AS activos,
        COALESCE(SUM(
            CASE
                WHEN UPPER(TRIM(estado)) = 'INACTIVO'
                THEN 1
                ELSE 0
            END
        ), 0) AS inactivos,
        COALESCE(SUM(
            CASE
                WHEN email IS NOT NULL
                AND TRIM(email) <> ''
                THEN 1
                ELSE 0
            END
        ), 0) AS con_email
    FROM asesores
    WHERE id_user = ?
");

if ($stmtKpi) {

    $stmtKpi->bind_param("i", $idUser);
    $stmtKpi->execute();

    $resKpi = $stmtKpi->get_result();

    if ($resKpi && ($rowKpi = $resKpi->fetch_assoc())) {

        $totalActivos = (int) ($rowKpi['activos'] ?? 0);
        $totalInactivos = (int) ($rowKpi['inactivos'] ?? 0);
        $totalConEmail = (int) ($rowKpi['con_email'] ?? 0);

        /*
         * Si procesar_lista_asesores.php no entregó
         * correctamente el total, usamos el KPI.
         */
        if ($totalAsesores <= 0) {
            $totalAsesores = (int) ($rowKpi['total'] ?? 0);
        }
    }

    $stmtKpi->close();
}

/* =========================================================
   CORREGIR TOTAL DE PÁGINAS
========================================================= */

if ($totalAsesores > 0 && $totalPaginas < 1) {
    $registrosPorPagina = 10;

    $totalPaginas = (int) ceil(
        $totalAsesores / $registrosPorPagina
    );
}

if ($totalPaginas < 1) {
    $totalPaginas = 1;
}

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

/* =========================================================
   MENSAJE DE SESIÓN
========================================================= */

$mensajeSesion = $_SESSION['mensajeAsesor'] ?? '';
$tipoSesion = $_SESSION['tipoAsesor'] ?? '';

unset(
    $_SESSION['mensajeAsesor'],
    $_SESSION['tipoAsesor']
);

/* =========================================================
   HELPERS DE ESCAPE
========================================================= */

function eAsesor($valor)
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

/* =========================================================
   URL DE BÚSQUEDA
========================================================= */

$urlBusqueda = !empty($busqueda)
    ? '&buscar=' . urlencode($busqueda)
    : '';
$totalMensajes = 0;


/*
|--------------------------------------------------------------------------
| CONSULTAR MENSAJES NO LEÍDOS
|--------------------------------------------------------------------------
*/

$sqlMensajesNoLeidos = "
    SELECT COUNT(*) AS total
    FROM mensajes AS m
    INNER JOIN propiedades AS p
        ON p.id_propiedad = m.id_propiedad
    WHERE p.id_user = ?
      AND m.estado_mensaje_leido = 0
";


$stmtMensajesNoLeidos = $conexion->prepare(
    $sqlMensajesNoLeidos
);


if ($stmtMensajesNoLeidos) {

    $stmtMensajesNoLeidos->bind_param(
        "i",
        $usId
    );


    if ($stmtMensajesNoLeidos->execute()) {

        $resultadoMensajesNoLeidos =
            $stmtMensajesNoLeidos->get_result();


        if ($resultadoMensajesNoLeidos) {

            $filaMensajesNoLeidos =
                $resultadoMensajesNoLeidos->fetch_assoc();


            if (
                isset(
                    $filaMensajesNoLeidos['total']
                )
            ) {

                $totalMensajes =
                    (int) $filaMensajesNoLeidos['total'];
            }
        }
    }


    $stmtMensajesNoLeidos->close();
}

?>

<!doctype html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Asesores — <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Panel') ?></title>

    <?php if (!empty($fotoPerfil)): ?>

        <link
            rel="icon"
            href="<?= htmlspecialchars($fotoPerfil) ?>"
            type="image/png">

    <?php else: ?>

        <link
            rel="icon"
            href="../img/logo.png"
            type="image/png">

    <?php endif; ?>


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Sidebar -->

    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">

    <!-- Estilos de asesores -->

    <link
        rel="stylesheet"
        href="../css/adm_lista_asesores.css">

</head>

<body>

    <div class="admin-layout">


        <!-- =====================================================
            SIDEBAR
        ====================================================== -->

        <aside id="sidebar" class="admin-sidebar">
            <!-- Empresa -->

            <div class="sidebar-company">

                <div class="company-icon">

                    <?php if (!empty($fotoPerfil)): ?>

                        <img
                            src="<?= htmlspecialchars($fotoPerfil) ?>"
                            alt="Perfil">

                    <?php else: ?>

                        <i class="fa-solid fa-building"></i>

                    <?php endif; ?>

                </div>

                <div class="company-info">

                    <strong>
                        <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Empresa') ?>
                    </strong>

                    <span>
                        Panel administrativo
                    </span>

                </div>

            </div>


            <!-- Navegación -->

            <nav class="sidebar-navigation">

                <div class="sidebar-section-title">
                    PRINCIPAL
                </div>


                <a
                    href="adm_index.php"
                    class="sidebar-link">

                    <span class="sidebar-link-icon">
                        <i class="fa-solid fa-house"></i>
                    </span>

                    <span>
                        Inicio
                    </span>

                </a>


                <a
                    href="adm_mensajes.php"
                    class="sidebar-link">

                    <span class="sidebar-link-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </span>

                    <span>
                        Mensajes
                    </span>

                    <?php if (!empty($totalContacto)): ?>

                        <span class="sidebar-badge">
                            <?= number_format($totalContacto) ?>
                        </span>

                    <?php endif; ?>

                </a>


                <div class="sidebar-section-title">
                    GESTIÓN
                </div>


                <!-- Propiedades -->

                <a
                    class="sidebar-link sidebar-collapse-link"
                    data-bs-toggle="collapse"
                    href="#menuPropiedades"
                    role="button"
                    aria-expanded="false"
                    aria-controls="menuPropiedades">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-building"></i>
                        </span>

                        <span>
                            Propiedades
                        </span>

                    </span>

                    <i class="fa-solid fa-chevron-down collapse-arrow"></i>

                </a>


                <div
                    class="collapse sidebar-submenu"
                    id="menuPropiedades">

                    <a
                        href="adm_lista_propiedades.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-list"></i>

                        <span>
                            Ver propiedades
                        </span>

                    </a>

                    <a
                        href="adm_registrar_propiedad.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-plus"></i>

                        <span>
                            Registrar propiedad
                        </span>

                    </a>

                    <a
                        href="adm_categorias.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-layer-group"></i>

                        <span>
                            Categorías
                        </span>

                    </a>

                </div>


                <!-- Asesores -->

                <a
                    class="sidebar-link sidebar-collapse-link"
                    data-bs-toggle="collapse"
                    href="#menuAsesores"
                    role="button"
                    aria-expanded="false"
                    aria-controls="menuAsesores">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>

                        <span>
                            Asesores
                        </span>

                    </span>

                    <i class="fa-solid fa-chevron-down collapse-arrow"></i>

                </a>


                <div
                    class="collapse sidebar-submenu"
                    id="menuAsesores">

                    <a
                        href="adm_lista_asesores.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-users"></i>

                        <span>
                            Lista de asesores
                        </span>

                    </a>

                    <a
                        href="adm_registrar_asesor.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-user-plus"></i>

                        <span>
                            Registrar asesor
                        </span>

                    </a>

                </div>

                <!-- =====================================================
                    TESTIMONIOS
                ====================================================== -->

                <a
                    class="sidebar-link sidebar-collapse-link"
                    data-bs-toggle="collapse"
                    href="#menuTestimonios"
                    role="button"
                    aria-expanded="false"
                    aria-controls="menuTestimonios">
                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-comments"></i>
                        </span>

                        <span>
                            Testimonios
                        </span>

                    </span>

                    <i class="fa-solid fa-chevron-down collapse-arrow"></i>

                </a>

                <div
                    class="collapse sidebar-submenu"
                    id="menuTestimonios">
                    <a
                        href="adm_lista_testimonios.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-list"></i>

                        <span>
                            Ver testimonios
                        </span>

                    </a>


                    <a
                        href="adm_registrar_testimonio.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-plus"></i>

                        <span>
                            Registrar testimonio
                        </span>

                    </a>


                </div>

                <div class="sidebar-section-title">
                    CUENTA
                </div>


                <a
                    href="adm_perfil.php"
                    class="sidebar-link">

                    <span class="sidebar-link-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>

                    <span>
                        Mi perfil
                    </span>

                </a>


                <a
                    href="../controladores/desconectar.php"
                    class="sidebar-link logout-link">

                    <span class="sidebar-link-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </span>

                    <span>
                        Cerrar sesión
                    </span>

                </a>

            </nav>


            <!-- Footer sidebar -->

            <div class="sidebar-footer">

                <i class="fa-solid fa-shield-halved"></i>

                <span>
                    Sesión segura
                </span>

            </div>

        </aside>


        <!-- Overlay móvil -->

        <div
            id="sidebarOverlay"
            class="sidebar-overlay">
        </div>


        <!-- =====================================================
            CONTENIDO
        ====================================================== -->

        <main id="content" class="admin-content">


            <!-- =================================================
                TOPBAR
            ================================================== -->

            <header class="admin-topbar">

                <div class="topbar-left">

                    <button
                        type="button"
                        id="toggleSidebar"
                        class="sidebar-toggle"
                        aria-label="Abrir menú">

                        <i class="fa-solid fa-bars"></i>

                    </button>


                    <div class="topbar-title">

                        <span>
                            Panel administrativo
                        </span>

                        <strong>
                            Gestión de asesores
                        </strong>

                    </div>

                </div>


                <div class="topbar-right">

                    <!-- Notificaciones -->

                    <a
                        href="adm_mensajes.php"
                        class="topbar-icon"
                        title="Mensajes">

                        <i class="fa-regular fa-bell"></i>

                        <?php if (!empty($totalContacto)): ?>

                            <span class="notification-dot">
                                <?= number_format($totalContacto) ?>
                            </span>

                        <?php endif; ?>

                    </a>


                    <!-- Perfil -->

                    <a
                        href="adm_perfil.php"
                        class="topbar-profile">

                        <div class="topbar-avatar">

                            <?php if (!empty($fotoPerfil)): ?>

                                <img
                                    src="<?= htmlspecialchars($fotoPerfil) ?>"
                                    alt="Perfil">

                            <?php else: ?>

                                <i class="fa-solid fa-user"></i>

                            <?php endif; ?>

                        </div>


                        <div class="topbar-user d-none d-md-flex">

                            <strong>
                                <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Administrador') ?>
                            </strong>

                            <span>
                                Administrador
                            </span>

                        </div>


                        <i class="fa-solid fa-chevron-down profile-arrow d-none d-md-block"></i>

                    </a>

                </div>

            </header>
            <!-- =================================================
                 CONTENIDO
            ================================================== -->

            <section class="asesores-content">
                <!-- =================================================
                     ALERTA
                ================================================== -->

                <?php if (!empty($mensajeSesion)): ?>

                    <div
                        class="alert alert-<?= $tipoSesion === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show asesor-alert"
                        role="alert">

                        <i
                            class="fa-solid <?= $tipoSesion === 'success'
                                                ? 'fa-circle-check'
                                                : 'fa-circle-exclamation' ?> me-2">
                        </i>

                        <?= eAsesor($mensajeSesion) ?>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Cerrar">
                        </button>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     ENCABEZADO RESUMEN
                ================================================== -->

                <div class="asesores-section-heading">

                    <div>

                        <span>
                            RESUMEN
                        </span>

                        <h2>
                            Estado de asesores
                        </h2>

                    </div>

                    <a
                        href="adm_registrar_asesor.php"
                        class="btn btn-success asesores-add-btn">

                        <i class="fa-solid fa-user-plus me-2"></i>

                        Nuevo asesor

                    </a>

                </div>


                <!-- =================================================
                     KPI
                ================================================== -->

                <div class="row g-3 mb-4">


                    <!-- TOTAL -->

                    <div class="col-12 col-sm-6 col-xl-3">

                        <div class="asesor-kpi kpi-total">

                            <div class="asesor-kpi-top">

                                <div class="asesor-kpi-icon">

                                    <i class="fa-solid fa-users"></i>

                                </div>

                                <span>
                                    TOTAL
                                </span>

                            </div>

                            <div class="asesor-kpi-number">
                                <?= $totalAsesores ?>
                            </div>

                            <div class="asesor-kpi-label">
                                Asesores registrados
                            </div>

                        </div>

                    </div>


                    <!-- ACTIVOS -->

                    <div class="col-12 col-sm-6 col-xl-3">

                        <div class="asesor-kpi kpi-activos">

                            <div class="asesor-kpi-top">

                                <div class="asesor-kpi-icon">

                                    <i class="fa-solid fa-user-check"></i>

                                </div>

                                <span>
                                    ACTIVOS
                                </span>

                            </div>

                            <div class="asesor-kpi-number">
                                <?= $totalActivos ?>
                            </div>

                            <div class="asesor-kpi-label">
                                Asesores activos
                            </div>

                        </div>

                    </div>


                    <!-- INACTIVOS -->

                    <div class="col-12 col-sm-6 col-xl-3">

                        <div class="asesor-kpi kpi-inactivos">

                            <div class="asesor-kpi-top">

                                <div class="asesor-kpi-icon">

                                    <i class="fa-solid fa-user-slash"></i>

                                </div>

                                <span>
                                    INACTIVOS
                                </span>

                            </div>

                            <div class="asesor-kpi-number">
                                <?= $totalInactivos ?>
                            </div>

                            <div class="asesor-kpi-label">
                                Asesores inactivos
                            </div>

                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="col-12 col-sm-6 col-xl-3">

                        <div class="asesor-kpi kpi-email">

                            <div class="asesor-kpi-top">

                                <div class="asesor-kpi-icon">

                                    <i class="fa-solid fa-envelope"></i>

                                </div>

                                <span>
                                    CONTACTO
                                </span>

                            </div>

                            <div class="asesor-kpi-number">
                                <?= $totalConEmail ?>
                            </div>

                            <div class="asesor-kpi-label">
                                Con correo registrado
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     HERRAMIENTAS
                ================================================== -->

                <div class="asesores-toolbar">


                    <!-- BUSCADOR -->

                    <div class="asesores-search-wrapper">

                        <div class="asesores-search-icon">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </div>

                        <form
                            id="formBuscar"
                            method="GET"
                            action="adm_lista_asesores.php"
                            class="asesores-search-form">

                            <input
                                type="search"
                                id="inputBuscar"
                                name="buscar"
                                value="<?= eAsesor($busqueda) ?>"
                                placeholder="Buscar por nombre, apellido, celular, cargo o fecha..."
                                autocomplete="off">

                            <?php if (!empty($busqueda)): ?>

                                <a
                                    href="adm_lista_asesores.php"
                                    class="asesores-search-clear"
                                    title="Limpiar búsqueda"
                                    aria-label="Limpiar búsqueda">

                                    <i class="fa-solid fa-xmark"></i>

                                </a>

                            <?php endif; ?>

                        </form>

                    </div>


                    <!-- ACCIONES -->

                    <div class="asesores-toolbar-actions">


                        <!-- EXPORTAR -->

                        <div class="dropdown">

                            <button
                                class="btn asesores-export-btn dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fa-solid fa-download me-2"></i>

                                Exportar

                            </button>


                            <ul class="dropdown-menu dropdown-menu-end asesores-export-menu">

                                <li class="dropdown-header">
                                    EXPORTAR INFORMACIÓN
                                </li>


                                <!-- PDF -->

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="../controladores/exportar_asesores_pdf.php"
                                        target="_blank"
                                        rel="noopener noreferrer">

                                        <span class="export-icon pdf">

                                            <i class="fa-solid fa-file-pdf"></i>

                                        </span>

                                        <span>

                                            <strong>
                                                PDF
                                            </strong>

                                            <small>
                                                Documento PDF
                                            </small>

                                        </span>

                                    </a>

                                </li>


                                <!-- EXCEL -->

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="../controladores/exportar_asesores_excel.php"
                                        target="_blank"
                                        rel="noopener noreferrer">

                                        <span class="export-icon excel">

                                            <i class="fa-solid fa-file-excel"></i>

                                        </span>

                                        <span>

                                            <strong>
                                                Excel
                                            </strong>

                                            <small>
                                                Hoja de cálculo
                                            </small>

                                        </span>

                                    </a>

                                </li>

                            </ul>

                        </div>
                    </div>

                </div>


                <!-- =================================================
                     TABLA
                ================================================== -->

                <div class="asesores-panel">


                    <!-- HEADER TABLA -->

                    <div class="asesores-panel-header">

                        <div>

                            <span class="panel-eyebrow">
                                DIRECTORIO
                            </span>

                            <h2>
                                Lista de asesores
                            </h2>

                        </div>

                        <div class="asesores-count">

                            <i class="fa-solid fa-users"></i>

                            <?= $totalAsesores ?>

                            <span>
                                registros
                            </span>

                        </div>

                    </div>


                    <!-- TABLA -->

                    <div class="table-responsive">

                        <table class="table asesores-table align-middle">

                            <thead>

                                <tr>

                                    <th class="col-actions">
                                        Acciones
                                    </th>

                                    <th>
                                        Asesor
                                    </th>

                                    <th>
                                        Contacto
                                    </th>

                                    <th>
                                        Cargo
                                    </th>

                                    <th>
                                        Estado
                                    </th>

                                    <th>
                                        Registro
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if (
                                    isset($resultado) &&
                                    $resultado &&
                                    $resultado->num_rows > 0
                                ): ?>

                                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                                        <?php

                                        /* =================================================
                                           DATOS DEL ASESOR
                                        ================================================== */

                                        $idAsesor = (int) (
                                            $fila['id_asesor'] ?? 0
                                        );

                                        $nombre = trim(
                                            (string) ($fila['nombre'] ?? '')
                                        );

                                        $apellidos = trim(
                                            (string) ($fila['apellidos'] ?? '')
                                        );

                                        $email = trim(
                                            (string) ($fila['email'] ?? '')
                                        );

                                        $celular = trim(
                                            (string) ($fila['celular'] ?? '')
                                        );

                                        $cargo = trim(
                                            (string) ($fila['cargo'] ?? '')
                                        );

                                        $estado = strtoupper(
                                            trim(
                                                (string) (
                                                    $fila['estado']
                                                    ?? 'ACTIVO'
                                                )
                                            )
                                        );

                                        $nombreCompleto = trim(
                                            $nombre . ' ' . $apellidos
                                        );

                                        if ($nombreCompleto === '') {
                                            $nombreCompleto = 'Sin nombre';
                                        }

                                        /* =================================================
                                           INICIALES
                                        ================================================== */

                                        $iniciales = '';

                                        if ($nombre !== '') {

                                            $iniciales .= strtoupper(
                                                function_exists('mb_substr')
                                                    ? mb_substr($nombre, 0, 1, 'UTF-8')
                                                    : substr($nombre, 0, 1)
                                            );
                                        }

                                        if ($apellidos !== '') {

                                            $iniciales .= strtoupper(
                                                function_exists('mb_substr')
                                                    ? mb_substr($apellidos, 0, 1, 'UTF-8')
                                                    : substr($apellidos, 0, 1)
                                            );
                                        }

                                        if ($iniciales === '') {
                                            $iniciales = 'AS';
                                        }

                                        /* =================================================
                                        FECHAS
                                        ================================================= */

                                        $fechaRegistro = 'Sin fecha';

                                        if (!empty($fila['fecha_registro'])) {

                                            $timestamp = strtotime(
                                                $fila['fecha_registro']
                                            );

                                            if ($timestamp !== false) {

                                                $fechaRegistro =
                                                    date(
                                                        'd/m/Y',
                                                        $timestamp
                                                    );
                                            }
                                        }


                                        $fechaActualizacion = 'Sin actualización';

                                        if (!empty($fila['fecha_actualizacion'])) {

                                            $timestampActualizacion = strtotime(
                                                $fila['fecha_actualizacion']
                                            );

                                            if ($timestampActualizacion !== false) {

                                                $fechaActualizacion =
                                                    date(
                                                        'd/m/Y',
                                                        $timestampActualizacion
                                                    );
                                            }
                                        }

                                        ?>

                                        <tr>


                                            <!-- =================================================
                                                 ACCIONES
                                            ================================================== -->

                                            <td>

                                                <div class="asesor-actions">
                                                    <!-- =================================================
             VER DETALLE
        ================================================== -->

                                                    <button
                                                        type="button"
                                                        class="asesor-action-btn view"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalVerAsesor"

                                                        data-id="<?= $idAsesor ?>"
                                                        data-id-user="<?= (int) ($fila['id_user'] ?? 0) ?>"

                                                        data-nombre="<?= eAsesor($nombre) ?>"
                                                        data-apellidos="<?= eAsesor($apellidos) ?>"
                                                        data-email="<?= eAsesor($email) ?>"
                                                        data-celular="<?= eAsesor($celular) ?>"
                                                        data-cargo="<?= eAsesor($cargo) ?>"

                                                        data-estado="<?= eAsesor($estado) ?>"

                                                        data-fecha-registro="<?= eAsesor($fechaRegistro) ?>"
                                                        data-fecha-actualizacion="<?= eAsesor($fechaActualizacion) ?>"

                                                        title="Ver detalle del asesor"
                                                        aria-label="Ver detalle del asesor">

                                                        <i class="fa-solid fa-eye"></i>

                                                    </button>
                                                    <!-- EDITAR -->

                                                    <button
                                                        type="button"
                                                        class="asesor-action-btn edit"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditar"
                                                        data-id="<?= $idAsesor ?>"
                                                        data-nombre="<?= eAsesor($nombre) ?>"
                                                        data-apellidos="<?= eAsesor($apellidos) ?>"
                                                        data-email="<?= eAsesor($email) ?>"
                                                        data-celular="<?= eAsesor($celular) ?>"
                                                        data-cargo="<?= eAsesor($cargo) ?>"
                                                        title="Editar asesor"
                                                        aria-label="Editar asesor">

                                                        <i class="fa-solid fa-pen-to-square"></i>

                                                    </button>


                                                    <!-- =================================================
                                                         CAMBIAR ESTADO
                                                    ================================================== -->

                                                    <?php if ($estado === 'ACTIVO'): ?>

                                                        <a
                                                            href="../controladores/procesar_lista_asesores.php?cambiar_estado=<?= $idAsesor ?><?= $urlBusqueda ?>"
                                                            class="asesor-action-btn delete"
                                                            title="Marcar como inactivo"
                                                            aria-label="Marcar como inactivo">

                                                            <i class="fa-solid fa-user-slash"></i>

                                                        </a>

                                                    <?php else: ?>

                                                        <a
                                                            href="../controladores/procesar_lista_asesores.php?cambiar_estado=<?= $idAsesor ?><?= $urlBusqueda ?>"
                                                            class="asesor-action-btn activate"
                                                            title="Activar asesor"
                                                            aria-label="Activar asesor">

                                                            <i class="fa-solid fa-user-check"></i>

                                                        </a>

                                                    <?php endif; ?>

                                                </div>

                                            </td>


                                            <!-- =================================================
                                                 ASESOR
                                            ================================================== -->

                                            <td>

                                                <div class="asesor-person">


                                                    <?php if (!empty($fila['imagen'])): ?>

                                                        <div class="asesor-avatar">

                                                            <img
                                                                src="data:image/jpeg;base64,<?= base64_encode($fila['imagen']) ?>"
                                                                alt="<?= eAsesor($nombreCompleto) ?>"
                                                                loading="lazy">

                                                        </div>

                                                    <?php else: ?>

                                                        <div class="asesor-avatar asesor-avatar-placeholder">

                                                            <?= eAsesor($iniciales) ?>

                                                        </div>

                                                    <?php endif; ?>


                                                    <div class="asesor-person-info">

                                                        <strong>
                                                            <?= eAsesor($nombreCompleto) ?>
                                                        </strong>

                                                        <span>
                                                            ID #<?= $idAsesor ?>
                                                        </span>

                                                    </div>

                                                </div>

                                            </td>


                                            <!-- =================================================
                                                 CONTACTO
                                            ================================================== -->

                                            <td>

                                                <div class="asesor-contact">


                                                    <!-- EMAIL -->

                                                    <?php if ($email !== ''): ?>

                                                        <a
                                                            href="mailto:<?= eAsesor($email) ?>"
                                                            class="contact-line">

                                                            <i class="fa-solid fa-envelope"></i>

                                                            <?= eAsesor($email) ?>

                                                        </a>

                                                    <?php else: ?>

                                                        <span class="contact-line muted">

                                                            <i class="fa-solid fa-envelope"></i>

                                                            Sin correo

                                                        </span>

                                                    <?php endif; ?>


                                                    <!-- CELULAR -->

                                                    <?php if ($celular !== ''): ?>

                                                        <span class="contact-line">

                                                            <i class="fa-solid fa-phone"></i>

                                                            <?= eAsesor($celular) ?>

                                                        </span>

                                                    <?php else: ?>

                                                        <span class="contact-line muted">

                                                            <i class="fa-solid fa-phone"></i>

                                                            Sin celular

                                                        </span>

                                                    <?php endif; ?>

                                                </div>

                                            </td>


                                            <!-- =================================================
                                                 CARGO
                                            ================================================== -->

                                            <td>

                                                <span class="asesor-cargo">

                                                    <i class="fa-solid fa-briefcase"></i>

                                                    <?= eAsesor(
                                                        $cargo !== ''
                                                            ? $cargo
                                                            : 'Sin cargo'
                                                    ) ?>

                                                </span>

                                            </td>


                                            <!-- =================================================
                                                 ESTADO
                                            ================================================== -->

                                            <td>

                                                <?php if ($estado === 'ACTIVO'): ?>

                                                    <span class="asesor-status active">

                                                        <span class="status-dot"></span>

                                                        Activo

                                                    </span>

                                                <?php else: ?>

                                                    <span class="asesor-status inactive">

                                                        <span class="status-dot"></span>

                                                        Inactivo

                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <!-- =================================================
                                                 FECHA
                                            ================================================== -->

                                            <td>

                                                <div class="asesor-date">
                                                    <span>
                                                        <?= eAsesor($fechaRegistro) ?>
                                                    </span>

                                                </div>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>


                                <?php else: ?>

                                    <!-- =================================================
                                         SIN RESULTADOS
                                    ================================================== -->

                                    <tr>

                                        <td
                                            colspan="6"
                                            class="p-0">

                                            <div class="asesores-empty">

                                                <div class="empty-icon">

                                                    <i class="fa-solid fa-user-group"></i>

                                                </div>

                                                <h3>

                                                    <?= !empty($busqueda)
                                                        ? 'No encontramos asesores'
                                                        : 'No hay asesores registrados'
                                                    ?>

                                                </h3>


                                                <p>

                                                    <?php if (!empty($busqueda)): ?>

                                                        No existen resultados para

                                                        <strong>
                                                            "<?= eAsesor($busqueda) ?>"
                                                        </strong>.

                                                    <?php else: ?>

                                                        Comienza registrando tu primer asesor.

                                                    <?php endif; ?>

                                                </p>


                                                <?php if (!empty($busqueda)): ?>

                                                    <a
                                                        href="adm_lista_asesores.php"
                                                        class="btn btn-outline-success btn-sm">

                                                        <i class="fa-solid fa-rotate-left me-2"></i>

                                                        Limpiar búsqueda

                                                    </a>

                                                <?php else: ?>

                                                    <a
                                                        href="adm_registrar_asesor.php"
                                                        class="btn btn-success btn-sm">

                                                        <i class="fa-solid fa-user-plus me-2"></i>

                                                        Registrar asesor

                                                    </a>

                                                <?php endif; ?>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- =================================================
                         FOOTER TABLA / PAGINACIÓN
                    ================================================== -->

                    <?php if ($totalAsesores > 0): ?>

                        <div class="asesores-pagination">


                            <!-- INFORMACIÓN -->

                            <div class="pagination-info">

                                <span>

                                    Página

                                    <strong>
                                        <?= $pagina ?>
                                    </strong>

                                    de

                                    <strong>
                                        <?= $totalPaginas ?>
                                    </strong>

                                </span>

                                <span class="separator">
                                    •
                                </span>

                                <span>

                                    <?= $totalAsesores ?>

                                    registros

                                </span>

                            </div>


                            <!-- BOTONES -->

                            <div class="pagination-buttons">


                                <!-- ANTERIOR -->

                                <?php if ($pagina > 1): ?>

                                    <a
                                        href="?pagina=<?= $pagina - 1 ?><?= $urlBusqueda ?>"
                                        class="pagination-btn">

                                        <i class="fa-solid fa-chevron-left"></i>

                                        Anterior

                                    </a>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="pagination-btn disabled"
                                        disabled>

                                        <i class="fa-solid fa-chevron-left"></i>

                                        Anterior

                                    </button>

                                <?php endif; ?>


                                <!-- PÁGINA ACTUAL -->

                                <div class="pagination-current">

                                    <?= $pagina ?>

                                </div>


                                <!-- SIGUIENTE -->

                                <?php if ($pagina < $totalPaginas): ?>

                                    <a
                                        href="?pagina=<?= $pagina + 1 ?><?= $urlBusqueda ?>"
                                        class="pagination-btn">

                                        Siguiente

                                        <i class="fa-solid fa-chevron-right"></i>

                                    </a>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="pagination-btn disabled"
                                        disabled>

                                        Siguiente

                                        <i class="fa-solid fa-chevron-right"></i>

                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>


                </div>


                <!-- =================================================
                     FOOTER
                ================================================== -->

                <footer class="asesores-footer">

                    <span>
                        © <?= date('Y') ?> CoDevPro Technology
                    </span>

                    <span>
                        Gestión de asesores
                    </span>

                </footer>


            </section>

        </main>

    </div>

    <!-- =========================================================
         MODAL EDITAR ASESOR
    ========================================================= -->
    <?php include '../modal/modal_ver_asesor.php'; ?>
    <?php include '../modal/modal_editar_asesor.php'; ?>
    <!-- =========================================================
         BOOTSTRAP
    ========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =========================================================
         JS LISTA ASESORES
    ========================================================= -->

    <script
        src="../js/lista_asesores.js">
    </script>


    <!-- =========================================================
         JS SIDEBAR
    ========================================================= -->

    <script
        src="../js/menu_sidebar.js">
    </script>

</body>

</html>