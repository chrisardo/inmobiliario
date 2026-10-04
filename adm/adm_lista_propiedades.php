<?php

session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

$mensaje = $_SESSION['mensaje_propiedad'] ?? '';
$tipoAlerta = $_SESSION['tipo_alerta_propiedad'] ?? '';

unset($_SESSION['mensaje_propiedad']);
unset($_SESSION['tipo_alerta_propiedad']);

require '../controladores/conect_db.php';
require '../controladores/eliminar_propiedad.php';
require '../controladores/procesar_lista_propiedades.php';
require '../controladores/editar_propiedad.php';
$nombreEmpresa = 'Mi empresa';

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
        content="Administración de propiedades">

    <title>Propiedades — <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Panel') ?></title>

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


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Sidebar -->
    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">

    <!-- Estilos de propiedades -->
    <link
        rel="stylesheet"
        href="../css/adm_lista_propiedades.css">

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
                    class="sidebar-link ">

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

        <main class="admin-content">


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
                            PANEL ADMINISTRATIVO
                        </span>

                        <strong>
                            Propiedades
                        </strong>

                    </div>

                </div>


                <div class="topbar-right">

                    <!-- MENSAJES -->

                    <a
                        href="adm_mensajes.php"
                        class="topbar-icon"
                        title="Mensajes"
                        aria-label="Mensajes">

                        <i class="fa-regular fa-bell"></i>

                        <?php if ($totalMensajes > 0): ?>

                            <span class="sidebar-badge">
                                <?= $totalMensajes ?>
                            </span>

                        <?php endif; ?>

                    </a>


                    <a
                        href="adm_perfil.php"
                        class="topbar-profile">

                        <span class="topbar-avatar">

                            <?php if ($fotoPerfil): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                                $fotoPerfil,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                    alt="Perfil">

                            <?php else: ?>

                                <i class="fa-solid fa-user"></i>

                            <?php endif; ?>

                        </span>


                        <span class="topbar-user">

                            <strong>
                                Mi perfil
                            </strong>

                            <span>
                                Administrador
                            </span>

                        </span>


                        <i class="fa-solid fa-chevron-down profile-arrow"></i>

                    </a>

                </div>

            </header>


            <!-- =================================================
                CONTENIDO SCROLL
            ================================================== -->

            <div class="dashboard-content">


                <!-- =================================================
                    ENCABEZADO
                ================================================== -->

                <section class="property-header">

                    <div>

                        <span class="property-eyebrow">
                            INVENTARIO
                        </span>

                        <h1>
                            Gestión de propiedades
                        </h1>

                        <p>
                            Administra, busca y actualiza las propiedades
                            registradas en tu sistema.
                        </p>

                    </div>


                    <div class="property-header-actions">

                        <a
                            href="adm_registrar_propiedad.php"
                            class="btn btn-success property-new-btn">

                            <i class="fa-solid fa-plus"></i>

                            Nueva propiedad

                        </a>

                    </div>

                </section>


                <!-- =================================================
                    ALERTA
                ================================================== -->

                <?php if ($mensaje): ?>

                    <div
                        class="alert alert-<?= htmlspecialchars(
                                                $tipoAlerta,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?> alert-dismissible fade show property-alert"
                        role="alert">

                        <i class="fa-solid
                        <?= $tipoAlerta === 'success'
                            ? 'fa-circle-check'
                            : 'fa-circle-exclamation'
                        ?> me-2">
                        </i>

                        <?= htmlspecialchars(
                            $mensaje,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Cerrar"></button>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                    KPIs
                ================================================== -->

                <section class="property-kpis">

                    <!-- =====================================================
                        TOTAL PROPIEDADES
                    ====================================================== -->

                    <div class="property-kpi">

                        <div class="property-kpi-icon green">
                            <i class="fa-solid fa-building"></i>
                        </div>

                        <div>

                            <span>
                                PROPIEDADES
                            </span>

                            <strong>
                                <?= number_format(
                                    $totalPropiedadesKPI
                                ) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- =====================================================
                        PROPIEDADES ACTIVAS
                    ====================================================== -->

                    <div class="property-kpi">

                        <div class="property-kpi-icon blue">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <div>

                            <span>
                                ACTIVAS
                            </span>

                            <strong>
                                <?= number_format(
                                    $totalActivas
                                ) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- =====================================================
                        PROPIEDADES INHABILITADAS
                    ====================================================== -->

                    <div class="property-kpi">

                        <div class="property-kpi-icon purple">
                            <i class="fa-solid fa-ban"></i>
                        </div>

                        <div>

                            <span>
                                INHABILITADAS
                            </span>

                            <strong>
                                <?= number_format(
                                    $totalInhabilitadas
                                ) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- =====================================================
                        CATEGORÍAS
                    ====================================================== -->

                    <div class="property-kpi">

                        <div class="property-kpi-icon orange">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>

                        <div>

                            <span>
                                CATEGORÍAS
                            </span>

                            <strong>
                                <?= number_format(
                                    count($categorias)
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                    FILTROS
                ================================================== -->

                <section class="property-toolbar">

                    <form
                        id="formBuscar"
                        method="GET"
                        action="adm_lista_propiedades.php"
                        class="property-search-form">

                        <div class="search-box">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input
                                id="inputBuscar"
                                type="search"
                                name="buscar"
                                value="<?= htmlspecialchars(
                                            $busqueda,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                placeholder="Buscar por nombre, código, ubicación o categoría..."
                                autocomplete="off">

                            <?php if ($busqueda !== ''): ?>

                                <a
                                    href="<?= $idCategoriaFiltro > 0
                                                ? '?categoria=' . $idCategoriaFiltro
                                                : 'adm_lista_propiedades.php'
                                            ?>"
                                    class="search-clear"
                                    title="Limpiar búsqueda">

                                    <i class="fa-solid fa-xmark"></i>

                                </a>

                            <?php endif; ?>

                        </div>


                        <div class="property-filter">

                            <i class="fa-solid fa-filter"></i>

                            <select
                                name="categoria"
                                id="filtroCategoria"
                                class="form-select">

                                <option value="0">
                                    Todas las categorías
                                </option>

                                <?php foreach ($categorias as $categoria): ?>

                                    <option
                                        value="<?= (int) $categoria['id_categoria'] ?>"
                                        <?= $idCategoriaFiltro ==
                                            $categoria['id_categoria']
                                            ? 'selected'
                                            : ''
                                        ?>>

                                        <?= htmlspecialchars(
                                            $categoria['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-success property-search-btn">

                            <i class="fa-solid fa-search"></i>

                            Buscar

                        </button>

                    </form>


                    <div class="property-export">

                        <div class="dropdown">

                            <button
                                type="button"
                                class="btn btn-light border dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fa-solid fa-download me-2"></i>

                                Exportar

                            </button>


                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="../controladores/exportar_propiedades_pdf.php"
                                        target="_blank">

                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>

                                        Exportar PDF

                                    </a>

                                </li>


                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="../controladores/exportar_propiedades_excel.php"
                                        target="_blank">

                                        <i class="fa-solid fa-file-excel text-success me-2"></i>

                                        Exportar Excel

                                    </a>

                                </li>

                            </ul>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                    TABLA
                ================================================== -->

                <section class="property-panel">

                    <div class="property-panel-header">

                        <div>

                            <h2>
                                Listado de propiedades
                            </h2>

                            <span>
                                <?= $totalPropiedades === 1
                                    ? '1 propiedad encontrada'
                                    : number_format($totalPropiedades)
                                    . ' propiedades encontradas'
                                ?>
                            </span>

                        </div>


                        <?php if (
                            $busqueda !== '' ||
                            $idCategoriaFiltro > 0
                        ): ?>

                            <a
                                href="adm_lista_propiedades.php"
                                class="btn btn-sm btn-outline-secondary">

                                <i class="fa-solid fa-rotate-left me-1"></i>

                                Limpiar filtros

                            </a>

                        <?php endif; ?>

                    </div>


                    <div class="table-responsive property-table-wrapper">

                        <?php if ($totalPropiedades > 0): ?>

                            <table class="table property-table align-middle">

                                <thead>

                                    <tr>

                                        <th>
                                            Propiedad
                                        </th>

                                        <th>
                                            Categoría
                                        </th>

                                        <th>
                                            Área
                                        </th>

                                        <th>
                                            Ubicación
                                        </th>

                                        <th>
                                            Precio
                                        </th>

                                        <th>
                                            Registro
                                        </th>
                                        <th>
                                            Estado
                                        </th>
                                        <th class="text-end">
                                            Acciones
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php while (
                                        $fila = $resultado->fetch_assoc()
                                    ): ?>

                                        <?php

                                        $idPropiedad =
                                            (int) $fila['id_propiedad'];

                                        $nombre =
                                            htmlspecialchars(
                                                $fila['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );

                                        $codigo =
                                            htmlspecialchars(
                                                $fila['codigo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );

                                        $categoria =
                                            htmlspecialchars(
                                                $fila['nombre_categoria'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );

                                        $ubicacion =
                                            htmlspecialchars(
                                                $fila['ubicacion'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );

                                        $precio =
                                            (float) $fila['precio'];

                                        $precioAnterior =
                                            (float) $fila['precio_anterior'];

                                        $area =
                                            (float) $fila['tamano_area_metros'];

                                        $totalImagenes =
                                            (int) $fila['total_imagenes'];

                                        $imagenPrincipal = null;

                                        if (
                                            !empty($fila['imagen_principal'])
                                        ) {

                                            $imagenPrincipal =
                                                'data:image/jpeg;base64,' .
                                                base64_encode(
                                                    $fila['imagen_principal']
                                                );
                                        }

                                        ?>

                                        <tr>

                                            <!-- Propiedad -->

                                            <td>

                                                <div class="property-cell">

                                                    <div
                                                        class="property-thumbnail"
                                                        data-property-thumbnail="<?= $idPropiedad ?>">

                                                        <?php if ($imagenPrincipal): ?>

                                                            <img
                                                                class="property-main-image"
                                                                src="<?= $imagenPrincipal ?>"
                                                                alt="<?= $nombre ?>"
                                                                loading="lazy">

                                                        <?php else: ?>

                                                            <div class="property-no-image">

                                                                <i class="fa-regular fa-image"></i>

                                                            </div>

                                                        <?php endif; ?>

                                                        <span
                                                            class="image-count <?= $totalImagenes > 0 ? '' : 'd-none' ?>"
                                                            data-image-count="<?= $idPropiedad ?>">

                                                            <i class="fa-solid fa-images"></i>

                                                            <span class="image-count-number">
                                                                <?= $totalImagenes ?>
                                                            </span>

                                                        </span>

                                                    </div>


                                                    <div class="property-name">

                                                        <strong
                                                            title="<?= $nombre ?>">
                                                            <?= $nombre ?>
                                                        </strong>

                                                        <span class="property-code">
                                                            Código: <?= $codigo ?>
                                                        </span>
                                                    </div>

                                                </div>

                                            </td>
                                            <!-- Categoría -->

                                            <td>

                                                <span class="category-badge">
                                                    <?= $categoria ?>
                                                </span>

                                            </td>


                                            <!-- Área -->

                                            <td>

                                                <strong>
                                                    <?= number_format(
                                                        $area,
                                                        1
                                                    ) ?>
                                                </strong>

                                                <small>
                                                    m²
                                                </small>

                                            </td>


                                            <!-- Ubicación -->

                                            <td>

                                                <div class="location-cell">

                                                    <i class="fa-solid fa-location-dot"></i>

                                                    <span
                                                        title="<?= $ubicacion ?>">
                                                        <?= $ubicacion ?>
                                                    </span>

                                                </div>

                                            </td>


                                            <!-- Precio -->

                                            <td>

                                                <div class="price-cell">

                                                    <strong>
                                                        S/.
                                                        <?= number_format(
                                                            $precio,
                                                            2
                                                        ) ?>
                                                    </strong>

                                                    <?php if (
                                                        $precioAnterior > 0 &&
                                                        $precioAnterior > $precio
                                                    ): ?>

                                                        <small>
                                                            <del>
                                                                S/.
                                                                <?= number_format(
                                                                    $precioAnterior,
                                                                    2
                                                                ) ?>
                                                            </del>
                                                        </small>

                                                    <?php endif; ?>

                                                </div>

                                            </td>


                                            <!-- Fecha -->

                                            <td>

                                                <span class="date-cell">
                                                    <?= date(
                                                        'd/m/Y',
                                                        strtotime(
                                                            $fila['fecha_registro']
                                                        )
                                                    ) ?>

                                                </span>

                                            </td>
                                            <!-- ESTADO -->

                                            <td>

                                                <?php if ((int)$fila['eliminado'] === 1): ?>

                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        <i class="fa-solid fa-ban me-1"></i>
                                                        INHABILITADO
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                        <i class="fa-solid fa-circle-check me-1"></i>
                                                        ACTIVO
                                                    </span>

                                                <?php endif; ?>

                                            </td>
                                            <!-- Acciones -->

                                            <td>

                                                <div class="property-actions">

                                                    <!-- EDITAR -->

                                                    <button
                                                        type="button"
                                                        class="action-btn edit"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditar"

                                                        data-id="<?= $idPropiedad ?>"

                                                        data-nombre="<?= $nombre ?>"

                                                        data-codigo="<?= $codigo ?>"

                                                        data-precio="<?= $precio ?>"

                                                        data-precio-anterior="<?= $precioAnterior ?>"

                                                        data-categoria="<?= (int) $fila['id_categoria'] ?>"

                                                        data-tamano-area-metros="<?= $area ?>"

                                                        data-ubicacion="<?= $ubicacion ?>"
                                                        data-fecha-registro="<?= htmlspecialchars(
                                                                                    $fila['fecha_registro'] ?? '',
                                                                                    ENT_QUOTES,
                                                                                    'UTF-8'
                                                                                ) ?>"

                                                        data-fecha-actualizacion="<?= htmlspecialchars(
                                                                                        $fila['fecha_actualizacion'] ?? '',
                                                                                        ENT_QUOTES,
                                                                                        'UTF-8'
                                                                                    ) ?>"
                                                        title="Editar propiedad">

                                                        <i class="fa-solid fa-pen"></i>

                                                    </button>
                                                    <!-- EDITAR IMÁGENES -->

                                                    <button
                                                        type="button"
                                                        class="action-btn images"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditarImagenes"

                                                        data-id="<?= $idPropiedad ?>"
                                                        data-nombre="<?= $nombre ?>"

                                                        title="Editar imágenes">

                                                        <i class="fa-solid fa-images"></i>

                                                    </button>


                                                    <!-- ACTIVAR / INHABILITAR -->

                                                    <?php if ((int)$fila['eliminado'] === 1): ?>

                                                        <button
                                                            type="button"
                                                            class="action-btn activate"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalCambiarEstado"
                                                            data-id="<?= $idPropiedad ?>"
                                                            data-nombre="<?= $nombre ?>"
                                                            data-estado="1"
                                                            title="Activar propiedad">

                                                            <i class="fa-solid fa-toggle-on"></i>

                                                        </button>

                                                    <?php else: ?>

                                                        <button
                                                            type="button"
                                                            class="action-btn disable"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalCambiarEstado"
                                                            data-id="<?= $idPropiedad ?>"
                                                            data-nombre="<?= $nombre ?>"
                                                            data-estado="0"
                                                            title="Inhabilitar propiedad">

                                                            <i class="fa-solid fa-toggle-off"></i>

                                                        </button>

                                                    <?php endif; ?>


                                                </div>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        <?php else: ?>


                            <!-- ESTADO VACÍO -->

                            <div class="property-empty">

                                <div class="property-empty-icon">

                                    <i class="fa-solid fa-building-circle-xmark"></i>

                                </div>

                                <h3>
                                    No encontramos propiedades
                                </h3>

                                <p>

                                    <?php if (
                                        $busqueda !== '' ||
                                        $idCategoriaFiltro > 0
                                    ): ?>

                                        Prueba modificando los filtros de búsqueda.

                                    <?php else: ?>

                                        Todavía no tienes propiedades registradas.

                                    <?php endif; ?>

                                </p>


                                <?php if (
                                    $busqueda !== '' ||
                                    $idCategoriaFiltro > 0
                                ): ?>

                                    <a
                                        href="adm_lista_propiedades.php"
                                        class="btn btn-outline-secondary">

                                        Limpiar filtros

                                    </a>

                                <?php else: ?>

                                    <a
                                        href="adm_registrar_propiedad.php"
                                        class="btn btn-success">

                                        <i class="fa-solid fa-plus me-1"></i>

                                        Registrar propiedad

                                    </a>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- =================================================
                     FOOTER TABLA / PAGINACIÓN
                ================================================== -->

                    <?php if ($totalPropiedades > 0): ?>

                        <div class="property-table-footer">

                            <div class="property-result-info">

                                Mostrando

                                <strong>
                                    <?= $registroInicio ?>–<?= $registroFin ?>
                                </strong>

                                de

                                <strong>
                                    <?= number_format(
                                        $totalPropiedades
                                    ) ?>
                                </strong>

                                propiedades

                            </div>


                            <nav
                                aria-label="Paginación de propiedades">

                                <ul class="pagination property-pagination">


                                    <!-- ANTERIOR -->

                                    <li
                                        class="page-item
                                        <?= $pagina <= 1
                                            ? 'disabled'
                                            : ''
                                        ?>">

                                        <?php if ($pagina > 1): ?>

                                            <a
                                                class="page-link"
                                                href="<?= urlPropiedades(
                                                            $pagina - 1
                                                        ) ?>"
                                                aria-label="Anterior">

                                                <i class="fa-solid fa-chevron-left"></i>

                                            </a>

                                        <?php else: ?>

                                            <span class="page-link">

                                                <i class="fa-solid fa-chevron-left"></i>

                                            </span>

                                        <?php endif; ?>

                                    </li>


                                    <?php

                                    /*
                                 * Mostrar máximo 5 páginas.
                                 */

                                    $inicioPagina =
                                        max(
                                            1,
                                            $pagina - 2
                                        );

                                    $finPagina =
                                        min(
                                            $totalPaginas,
                                            $pagina + 2
                                        );

                                    ?>


                                    <?php if ($inicioPagina > 1): ?>

                                        <li class="page-item">

                                            <a
                                                class="page-link"
                                                href="<?= urlPropiedades(1) ?>">
                                                1
                                            </a>

                                        </li>

                                        <?php if ($inicioPagina > 2): ?>

                                            <li class="page-item disabled">

                                                <span class="page-link">
                                                    …
                                                </span>

                                            </li>

                                        <?php endif; ?>

                                    <?php endif; ?>


                                    <?php for (
                                        $i = $inicioPagina;
                                        $i <= $finPagina;
                                        $i++
                                    ): ?>

                                        <li
                                            class="page-item
                                            <?= $i === $pagina
                                                ? 'active'
                                                : ''
                                            ?>">

                                            <a
                                                class="page-link"
                                                href="<?= urlPropiedades($i) ?>">
                                                <?= $i ?>
                                            </a>

                                        </li>

                                    <?php endfor; ?>


                                    <?php if ($finPagina < $totalPaginas): ?>

                                        <?php if (
                                            $finPagina <
                                            $totalPaginas - 1
                                        ): ?>

                                            <li class="page-item disabled">

                                                <span class="page-link">
                                                    …
                                                </span>

                                            </li>

                                        <?php endif; ?>


                                        <li class="page-item">

                                            <a
                                                class="page-link"
                                                href="<?= urlPropiedades(
                                                            $totalPaginas
                                                        ) ?>">
                                                <?= $totalPaginas ?>
                                            </a>

                                        </li>

                                    <?php endif; ?>


                                    <!-- SIGUIENTE -->

                                    <li
                                        class="page-item
                                        <?= $pagina >= $totalPaginas
                                            ? 'disabled'
                                            : ''
                                        ?>">

                                        <?php if (
                                            $pagina < $totalPaginas
                                        ): ?>

                                            <a
                                                class="page-link"
                                                href="<?= urlPropiedades(
                                                            $pagina + 1
                                                        ) ?>"
                                                aria-label="Siguiente">

                                                <i class="fa-solid fa-chevron-right"></i>

                                            </a>

                                        <?php else: ?>

                                            <span class="page-link">

                                                <i class="fa-solid fa-chevron-right"></i>

                                            </span>

                                        <?php endif; ?>

                                    </li>

                                </ul>

                            </nav>

                        </div>

                    <?php endif; ?>

                </section>


                <!-- =================================================
                 FOOTER
            ================================================== -->

                <footer class="dashboard-footer">

                    <span>
                        Sistema administrativo
                    </span>

                    <span>
                        Gestión de propiedades
                    </span>

                </footer>

            </div>

        </main>


    </div>


    <!-- =========================================================
     MODALES
========================================================= -->

    <?php include '../modal/modal_editar_propiedad.php'; ?>
    <?php include '../modal/modal_editar_imagenes_propiedad.php'; ?>
    <?php include '../modal/modal_activar_inhabilitar_propiedad.php'; ?>
    <!-- =========================================================
     JAVASCRIPT
========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="../js/menu_sidebar.js"></script>

    <script src="../js/lista_propiedades.js"></script>

    <script src="../js/visualizar_editar_imagen.js"></script>

</body>

</html>