<?php
// =========================================================
// CoDevPro Technology
// Archivo: adm/adm_categorias.php
// Módulo: Categorías
// Sistema: Panel Administrativo
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================
// SEGURIDAD
// =========================================================

if (!isset($_SESSION['usId']) || empty($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

// =========================================================
// CONEXIÓN
// =========================================================

require_once '../controladores/conect_db.php';

// =========================================================
// PROCESAMIENTO DE CATEGORÍAS
// =========================================================

require_once '../controladores/procesar_categorias.php';

// =========================================================
// DATOS DEL USUARIO
// =========================================================

$idUsuario = (int) $_SESSION['usId'];

$nombreEmpresa = 'Mi empresa';
$fotoPerfil = null;

// NO volver a inicializar $totalMensajes aquí.
// procesar_categorias.php ya lo obtiene.
$totalMensajes = isset($totalMensajes)
    ? (int) $totalMensajes
    : 0;

// =========================================================
// OBTENER DATOS DEL USUARIO / EMPRESA
// =========================================================

$sqlUsuario = "
    SELECT
        nombreEmpresa,
        imagen
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
";

$stmtUsuario = $conexion->prepare($sqlUsuario);

if ($stmtUsuario) {

    $stmtUsuario->bind_param(
        "i",
        $idUsuario
    );

    if ($stmtUsuario->execute()) {

        $resultUsuario = $stmtUsuario->get_result();

        if ($resultUsuario && $resultUsuario->num_rows > 0) {

            $usuario = $resultUsuario->fetch_assoc();

            // =================================================
            // NOMBRE DE EMPRESA
            // =================================================

            if (
                isset($usuario['nombreEmpresa']) &&
                trim((string) $usuario['nombreEmpresa']) !== ''
            ) {

                $nombreEmpresa = trim(
                    (string) $usuario['nombreEmpresa']
                );
            }

            // =================================================
            // IMAGEN DE PERFIL
            // =================================================

            if (
                isset($usuario['imagen']) &&
                !empty($usuario['imagen'])
            ) {

                $imagen = $usuario['imagen'];

                /*
                 * La imagen se almacena como BLOB.
                 * Se convierte a Base64 para mostrarla directamente.
                 */

                $fotoPerfil =
                    'data:image/jpeg;base64,' .
                    base64_encode($imagen);
            }
        }
    }

    $stmtUsuario->close();
}

// =========================================================
// BÚSQUEDA
// =========================================================

$busqueda = isset($_GET['buscar'])
    ? trim((string) $_GET['buscar'])
    : '';

// =========================================================
// VARIABLES DEVUELTAS POR EL CONTROLADOR
// =========================================================

$totalCategorias = isset($totalCategorias)
    ? (int) $totalCategorias
    : 0;

$totalPaginas = isset($totalPaginas)
    ? (int) $totalPaginas
    : 1;

$pagina = isset($pagina)
    ? (int) $pagina
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

if ($totalPaginas < 1) {
    $totalPaginas = 1;
}

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$resultado = isset($resultado)
    ? $resultado
    : false;

// =========================================================
// URL ACTUAL PARA PAGINACIÓN
// =========================================================

$urlBuscar = urlencode($busqueda);

?>

<!doctype html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="Administración de categorías de propiedades">

    <title>
        Categorías —
        <?= htmlspecialchars(
            $nombreEmpresa,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <?php if (!empty($fotoPerfil)): ?>

        <link
            rel="icon"
            href="<?= htmlspecialchars(
                        $fotoPerfil,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
            type="image/png">

    <?php else: ?>

        <link
            rel="icon"
            href="../img/logo.png"
            type="image/png">

    <?php endif; ?>


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         SIDEBAR / NAVBAR
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">


    <!-- =====================================================
         CSS CATEGORÍAS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../css/adm_categorias.css">


    <!-- =====================================================
         JS NÚMEROS
    ====================================================== -->

    <script src="../js/numero.js"></script>

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
                            Categorias
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
            <div class="categories-content">


                <!-- =================================================
                 ENCABEZADO
            ================================================== -->

                <section class="categories-header">

                    <div>

                        <div class="categories-eyebrow">

                            <i class="fa-solid fa-layer-group"></i>

                            ADMINISTRACIÓN

                        </div>


                        <h1>
                            Categorías
                        </h1>


                        <p>
                            Organiza y administra las categorías de tus propiedades.
                        </p>

                    </div>


                    <!-- NUEVA CATEGORÍA -->

                    <button
                        type="button"
                        class="btn btn-category-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalRegistrarCategoria">

                        <i class="fa-solid fa-plus"></i>

                        Nueva categoría

                    </button>

                </section>


                <!-- =================================================
                 KPIs
            ================================================== -->

                <section class="category-kpis">


                    <!-- TOTAL -->

                    <div class="category-kpi">

                        <div class="category-kpi-icon green">

                            <i class="fa-solid fa-layer-group"></i>

                        </div>

                        <div>

                            <span>
                                TOTAL
                            </span>

                            <strong>
                                <?= $totalCategorias ?>
                            </strong>

                            <small>
                                Categorías registradas
                            </small>

                        </div>

                    </div>


                    <!-- ORGANIZACIÓN -->

                    <div class="category-kpi">

                        <div class="category-kpi-icon blue">

                            <i class="fa-solid fa-building"></i>

                        </div>

                        <div>

                            <span>
                                ORGANIZACIÓN
                            </span>

                            <strong>
                                Activas
                            </strong>

                            <small>
                                Categorías disponibles
                            </small>

                        </div>

                    </div>


                    <!-- SEGURIDAD -->

                    <div class="category-kpi">

                        <div class="category-kpi-icon purple">

                            <i class="fa-solid fa-shield-halved"></i>

                        </div>

                        <div>

                            <span>
                                SEGURIDAD
                            </span>

                            <strong>
                                <?= $idUsuario ?>
                            </strong>

                            <small>
                                Usuario propietario
                            </small>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                 PANEL PRINCIPAL
            ================================================== -->

                <section class="categories-panel">


                    <!-- =================================================
                     HEADER PANEL
                ================================================== -->

                    <div class="categories-panel-header">


                        <div>

                            <div class="panel-title-row">

                                <div class="panel-title-icon">

                                    <i class="fa-solid fa-list-check"></i>

                                </div>


                                <div>

                                    <h2>
                                        Categorías registradas
                                    </h2>

                                    <p>
                                        Administra las categorías utilizadas en tus propiedades.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                         BUSCADOR
                    ================================================== -->

                        <form
                            method="GET"
                            action="adm_categorias.php"
                            class="category-search">

                            <i class="fa-solid fa-magnifying-glass"></i>


                            <input
                                type="search"
                                name="buscar"
                                value="<?= htmlspecialchars(
                                            $busqueda,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                placeholder="Buscar categoría..."
                                autocomplete="off">


                            <?php if ($busqueda !== ''): ?>

                                <a
                                    href="adm_categorias.php"
                                    class="search-clear"
                                    title="Limpiar búsqueda"
                                    aria-label="Limpiar búsqueda">

                                    <i class="fa-solid fa-xmark"></i>

                                </a>

                            <?php endif; ?>

                        </form>

                    </div>


                    <!-- =================================================
                     TABLA
                ================================================== -->

                    <div class="categories-table-wrapper">

                        <?php if (
                            $resultado &&
                            $resultado->num_rows > 0
                        ): ?>


                            <div class="table-responsive">

                                <table class="categories-table">

                                    <thead>

                                        <tr>

                                            <th class="category-col-name">
                                                CATEGORÍA
                                            </th>

                                            <th>
                                                FECHA DE REGISTRO
                                            </th>

                                            <th>
                                                ESTADO
                                            </th>

                                            <th class="text-end">
                                                ACCIONES
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <?php while (
                                            $fila = $resultado->fetch_assoc()
                                        ): ?>

                                            <?php

                                            // =====================================
                                            // NOMBRE
                                            // =====================================

                                            $nombreCategoria = trim(
                                                (string) (
                                                    $fila['nombre'] ?? ''
                                                )
                                            );

                                            if ($nombreCategoria === '') {
                                                $nombreCategoria = 'Sin nombre';
                                            }


                                            // =====================================
                                            // INICIAL
                                            // =====================================

                                            if (function_exists('mb_substr')) {

                                                $inicial = mb_substr(
                                                    $nombreCategoria,
                                                    0,
                                                    1,
                                                    'UTF-8'
                                                );

                                                if (
                                                    function_exists(
                                                        'mb_strtoupper'
                                                    )
                                                ) {

                                                    $inicial = mb_strtoupper(
                                                        $inicial,
                                                        'UTF-8'
                                                    );
                                                }
                                            } else {

                                                $inicial = strtoupper(
                                                    substr(
                                                        $nombreCategoria,
                                                        0,
                                                        1
                                                    )
                                                );
                                            }


                                            // =====================================
                                            // FECHA
                                            // =====================================

                                            $fechaRegistro = '';

                                            if (
                                                !empty($fila['fecha_registro'])
                                            ) {

                                                $fecha = DateTime::createFromFormat(
                                                    'Y-m-d',
                                                    $fila['fecha_registro']
                                                );

                                                if ($fecha) {

                                                    $fechaRegistro =
                                                        $fecha->format('d/m/Y');
                                                } else {

                                                    /*
                                             * Compatibilidad si el campo
                                             * contiene fecha y hora.
                                             */

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
                                            }


                                            // =====================================
                                            // ID
                                            // =====================================

                                            $idCategoria = (int) (
                                                $fila['id_categoria'] ?? 0
                                            );

                                            ?>


                                            <tr>


                                                <!-- =================================
                                             CATEGORÍA
                                        ================================== -->

                                                <td>

                                                    <div class="category-name">


                                                        <div class="category-avatar">

                                                            <?= htmlspecialchars(
                                                                $inicial,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>

                                                        </div>


                                                        <div class="category-name-info">

                                                            <strong>

                                                                <?= htmlspecialchars(
                                                                    $nombreCategoria,
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>

                                                            </strong>


                                                            <span>

                                                                ID #<?= $idCategoria ?>

                                                            </span>

                                                        </div>

                                                    </div>

                                                </td>


                                                <!-- =================================
                                             FECHA
                                        ================================== -->

                                                <td>

                                                    <div class="category-date">

                                                        <i class="fa-regular fa-calendar"></i>

                                                        <span>

                                                            <?= $fechaRegistro !== ''
                                                                ? htmlspecialchars(
                                                                    $fechaRegistro,
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                )
                                                                : 'Sin fecha'
                                                            ?>

                                                        </span>

                                                    </div>

                                                </td>


                                                <!-- =================================
                                             ESTADO
                                        ================================== -->

                                                <td>

                                                    <span class="category-status">

                                                        <span class="status-dot"></span>

                                                        Activa

                                                    </span>

                                                </td>


                                                <!-- =================================
                                             ACCIONES
                                        ================================== -->

                                                <td>

                                                    <div class="category-actions">


                                                        <!-- EDITAR -->

                                                        <button
                                                            type="button"
                                                            class="category-action edit"
                                                            title="Editar categoría"
                                                            aria-label="Editar categoría"
                                                            data-id="<?= $idCategoria ?>"
                                                            data-name="<?= htmlspecialchars(
                                                                            $nombreCategoria,
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditarCategoria">

                                                            <i class="fa-solid fa-pen"></i>

                                                        </button>


                                                        <!-- ELIMINAR -->

                                                        <button
                                                            type="button"
                                                            class="category-action delete"
                                                            title="Eliminar categoría"
                                                            aria-label="Eliminar categoría"
                                                            data-id="<?= $idCategoria ?>"
                                                            data-name="<?= htmlspecialchars(
                                                                            $nombreCategoria,
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEliminarCategoria">

                                                            <i class="fa-solid fa-trash"></i>

                                                        </button>

                                                    </div>

                                                </td>

                                            </tr>


                                        <?php endwhile; ?>

                                    </tbody>

                                </table>

                            </div>


                        <?php else: ?>


                            <!-- =================================================
                             ESTADO VACÍO
                        ================================================== -->

                            <div class="category-empty">


                                <div class="category-empty-icon">

                                    <i class="fa-solid fa-layer-group"></i>

                                </div>


                                <?php if ($busqueda !== ''): ?>


                                    <h3>
                                        No encontramos categorías
                                    </h3>


                                    <p>

                                        No existen categorías que coincidan con

                                        "<strong>
                                            <?= htmlspecialchars(
                                                $busqueda,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>".

                                    </p>


                                    <a
                                        href="adm_categorias.php"
                                        class="btn btn-outline-secondary">

                                        <i class="fa-solid fa-arrow-left"></i>

                                        Ver todas

                                    </a>


                                <?php else: ?>


                                    <h3>
                                        Aún no tienes categorías
                                    </h3>


                                    <p>
                                        Crea tu primera categoría para comenzar
                                        a organizar tus propiedades.
                                    </p>


                                    <button
                                        type="button"
                                        class="btn btn-category-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalRegistrarCategoria">

                                        <i class="fa-solid fa-plus"></i>

                                        Crear primera categoría

                                    </button>


                                <?php endif; ?>


                            </div>


                        <?php endif; ?>

                    </div>


                    <!-- =================================================
                     FOOTER / PAGINACIÓN
                ================================================== -->

                    <?php if ($totalCategorias > 0): ?>


                        <div class="categories-panel-footer">


                            <!-- INFORMACIÓN -->

                            <div class="pagination-info">

                                <i class="fa-solid fa-circle-info"></i>

                                Página

                                <strong>
                                    <?= $pagina ?>
                                </strong>

                                de

                                <strong>
                                    <?= $totalPaginas ?>
                                </strong>

                            </div>


                            <!-- PAGINACIÓN -->

                            <div class="category-pagination">


                                <!-- ANTERIOR -->

                                <?php if ($pagina > 1): ?>

                                    <a
                                        href="?pagina=<?= $pagina - 1 ?>&buscar=<?= $urlBuscar ?>"
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
                                        href="?pagina=<?= $pagina + 1 ?>&buscar=<?= $urlBuscar ?>"
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


                </section>


                <!-- =================================================
                 FOOTER
            ================================================== -->

                <footer class="categories-footer">

                    <span>
                        © <?= date('Y') ?> CoDevPro Technology
                    </span>

                    <span>
                        Gestión de categorías
                    </span>

                </footer>


            </div>
        </main>

    </div>

    <!-- =========================================================
     MODAL REGISTRAR CATEGORÍA
========================================================= -->

    <?php
    include '../modal/modal_registrar_categoria.php';
    ?>


    <!-- =========================================================
     MODAL EDITAR CATEGORÍA
========================================================= -->

    <?php
    include '../modal/modal_editar_categoria.php';
    ?>


    <!-- =========================================================
     MODAL ELIMINAR CATEGORÍA
========================================================= -->

    <div
        class="modal fade"
        id="modalEliminarCategoria"
        tabindex="-1"
        aria-labelledby="modalEliminarCategoriaLabel"
        aria-hidden="true">


        <div class="modal-dialog modal-dialog-centered">


            <div class="modal-content category-delete-modal">


                <div class="modal-body">


                    <!-- ICONO -->

                    <div class="delete-modal-icon">

                        <i class="fa-solid fa-trash-can"></i>

                    </div>


                    <!-- TÍTULO -->

                    <h3 id="modalEliminarCategoriaLabel">
                        ¿Eliminar categoría?
                    </h3>


                    <!-- MENSAJE -->

                    <p>

                        Estás a punto de eliminar la categoría

                        <strong id="nombreCategoriaEliminar">
                            categoría
                        </strong>.

                        <br>

                        Esta acción la ocultará de tus registros activos.

                    </p>


                    <!-- FORMULARIO -->

                    <form
                        method="GET"
                        action="adm_categorias.php"
                        id="formEliminarCategoria">


                        <input
                            type="hidden"
                            name="eliminar"
                            id="idCategoriaEliminar"
                            value="">


                        <div class="delete-modal-actions">


                            <!-- CANCELAR -->

                            <button
                                type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal">

                                Cancelar

                            </button>


                            <!-- ELIMINAR -->

                            <button
                                type="submit"
                                class="btn btn-danger">

                                <i class="fa-solid fa-trash me-1"></i>

                                Sí, eliminar

                            </button>


                        </div>


                    </form>


                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
     BOOTSTRAP
========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <script src="../js/menu_sidebar.js"></script>


    <!-- =========================================================
     CATEGORÍAS
========================================================= -->

    <script src="../js/categorias.js"></script>


    <!-- =========================================================
     COMPATIBILIDAD SIDEBAR
========================================================= -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function abrirSidebar() {

                if (!sidebar) {
                    return;
                }

                sidebar.classList.add('show');

                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('show');
                }

                document.body.classList.add('sidebar-open');
            }


            function cerrarSidebar() {

                if (!sidebar) {
                    return;
                }

                sidebar.classList.remove('show');

                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }

                document.body.classList.remove('sidebar-open');
            }


            if (sidebarToggle) {

                sidebarToggle.addEventListener(
                    'click',
                    function() {

                        if (
                            sidebar &&
                            sidebar.classList.contains('show')
                        ) {

                            cerrarSidebar();

                        } else {

                            abrirSidebar();

                        }

                    }
                );

            }


            if (sidebarClose) {

                sidebarClose.addEventListener(
                    'click',
                    cerrarSidebar
                );

            }


            if (sidebarOverlay) {

                sidebarOverlay.addEventListener(
                    'click',
                    cerrarSidebar
                );

            }


            /*
             * Al cambiar a escritorio, se limpia el estado
             * móvil del sidebar.
             */

            window.addEventListener(
                'resize',
                function() {

                    if (window.innerWidth > 991.98) {

                        cerrarSidebar();

                    }

                }
            );


            /*
             * Cerrar sidebar al seleccionar un enlace
             * en dispositivos móviles.
             */

            if (sidebar) {

                const sidebarLinks =
                    sidebar.querySelectorAll(
                        'a:not(.sidebar-collapse-link)'
                    );

                sidebarLinks.forEach(function(link) {

                    link.addEventListener(
                        'click',
                        function() {

                            if (window.innerWidth <= 991.98) {

                                cerrarSidebar();

                            }

                        }
                    );

                });

            }

        });
    </script>


</body>

</html>