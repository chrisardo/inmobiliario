<?php
//=========================================================
// CoDevPro Technology
// Archivo: adm/adm_mensajes.php
// Módulo: Mensajes / Contactos
// Sistema: Inmobiliario
//=========================================================

session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../controladores/conect_db.php';
require_once '../controladores/procesar_lista_mensajes.php';

$mensaje = "";
$tipoAlerta = "";

// Protección para variables del controlador
$totalMensaje  = isset($totalMensaje) ? (int)$totalMensaje : 0;
$totalPaginas  = isset($totalPaginas) ? (int)$totalPaginas : 1;
$pagina        = isset($pagina) ? (int)$pagina : 1;
$busqueda      = isset($busqueda) ? $busqueda : '';
?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Mensajes — <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Panel') ?></title>

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

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Sidebar -->
    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">

    <!-- Estilos propios de mensajes -->
    <link
        rel="stylesheet"
        href="../css/adm_mensajes.css">

    <script src="../js/numero.js"></script>

</head>

<body>

    <div class="admin-layout">

        <!-- =====================================================
         SIDEBAR
    ====================================================== -->

        <aside id="sidebar" class="admin-sidebar">
            <!-- EMPRESA -->
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


            <!-- NAVEGACIÓN -->
            <div class="sidebar-navigation">

                <div class="sidebar-section-title">
                    PRINCIPAL
                </div>

                <!-- INICIO -->
                <a
                    href="adm_index.php"
                    class="sidebar-link">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-house"></i>
                        </span>

                        <span>
                            Inicio
                        </span>

                    </span>

                </a>


                <!-- MENSAJES -->
                <a
                    href="adm_mensajes.php"
                    class="sidebar-link active">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </span>

                        <span>
                            Mensajes
                        </span>

                    </span>

                </a>


                <div class="sidebar-section-title">
                    GESTIÓN
                </div>


                <!-- PROPIEDADES -->
                <a
                    href="#menuPropiedades"
                    class="sidebar-link sidebar-collapse-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    aria-expanded="false">

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

                        <i class="fa-solid fa-circle-plus"></i>

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


                <!-- ASESORES -->
                <a
                    href="#menuAsesores"
                    class="sidebar-link sidebar-collapse-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    aria-expanded="false">

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
                        href="adm_registrar_asesor.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-user-plus"></i>

                        <span>
                            Registrar asesor
                        </span>

                    </a>

                    <a
                        href="adm_lista_asesores.php"
                        class="sidebar-sublink">

                        <i class="fa-solid fa-users"></i>

                        <span>
                            Lista de asesores
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
                <!-- CERRAR SESIÓN -->
                <a
                    href="../controladores/desconectar.php"
                    class="sidebar-link logout-link">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </span>

                        <span>
                            Cerrar sesión
                        </span>

                    </span>

                </a>

            </div>


            <!-- FOOTER -->
            <div class="sidebar-footer">

                <i class="fa-solid fa-shield-halved sidebar-security-icon"></i>

                <span>
                    Sistema protegido
                </span>

            </div>

        </aside>


        <!-- OVERLAY -->
        <div
            id="sidebarOverlay"
            class="sidebar-overlay">
        </div>


        <!-- =====================================================
         CONTENIDO PRINCIPAL
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
                        aria-label="Abrir menú"
                        aria-expanded="false">

                        <i class="fa-solid fa-bars"></i>

                    </button>


                    <div class="topbar-title">

                        <span>
                            Panel administrativo
                        </span>

                        <strong>
                            Mensajes
                        </strong>

                    </div>

                </div>


                <div class="topbar-right">

                    <!-- NOTIFICACIONES -->
                    <a
                        href="adm_mensajes.php"
                        class="topbar-icon"
                        title="Mensajes">

                        <i class="fa-regular fa-bell"></i>

                    </a>


                    <!-- PERFIL -->
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

                        <div class="topbar-user">

                            <strong>
                                <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Administrador') ?>
                            </strong>

                            <span>
                                Administrador
                            </span>

                        </div>

                        <i class="fa-solid fa-chevron-down profile-arrow"></i>

                    </a>

                </div>

            </header>


            <!-- =================================================
             CONTENIDO
        ================================================== -->

            <div class="dashboard-content">


                <!-- =================================================
                 ENCABEZADO
            ================================================== -->

                <section class="messages-header">

                    <div>

                        <span class="messages-eyebrow">
                            COMUNICACIÓN
                        </span>

                        <h1>
                            Mensajes de clientes
                        </h1>

                        <p>
                            Administra y responde las consultas recibidas
                            sobre tus propiedades.
                        </p>

                    </div>


                    <div class="messages-header-icon">

                        <i class="fa-solid fa-comments"></i>

                    </div>

                </section>


                <!-- =================================================
     KPI
================================================== -->

                <section class="messages-kpis">


                    <!-- =================================================
         TOTAL DE MENSAJES
    ================================================== -->

                    <div class="message-kpi">

                        <div class="message-kpi-icon">

                            <i class="fa-solid fa-envelope"></i>

                        </div>

                        <div>

                            <span>
                                TOTAL DE MENSAJES
                            </span>

                            <strong>
                                <?= number_format($totalMensajesKpi) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- =================================================
         TOTAL LEÍDOS
    ================================================== -->

                    <div class="message-kpi">

                        <div class="message-kpi-icon blue">

                            <i class="fa-solid fa-envelope-open"></i>

                        </div>

                        <div>

                            <span>
                                TOTAL LEÍDOS
                            </span>

                            <strong>
                                <?= number_format($totalLeidos) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- =================================================
         TOTAL NO LEÍDOS
    ================================================== -->

                    <div class="message-kpi">

                        <div class="message-kpi-icon purple">

                            <i class="fa-solid fa-envelope"></i>

                        </div>

                        <div>

                            <span>
                                TOTAL NO LEÍDOS
                            </span>

                            <strong>
                                <?= number_format($totalNoLeidos) ?>
                            </strong>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                 PANEL PRINCIPAL
            ================================================== -->

                <section class="messages-panel">


                    <!-- PANEL HEADER -->
                    <div class="messages-panel-header">

                        <div>

                            <span class="panel-label">
                                BANDEJA DE ENTRADA
                            </span>

                            <h2>
                                Consultas recibidas
                            </h2>

                        </div>


                        <!-- EXPORTAR -->
                        <div class="dropdown">

                            <button
                                type="button"
                                class="btn-export"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fa-solid fa-download"></i>

                                <span>
                                    Exportar
                                </span>

                                <i class="fa-solid fa-chevron-down export-arrow"></i>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-end export-menu">

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="../controladores/exportar_mensajes_pdf.php"
                                        target="_blank">

                                        <i class="fa-solid fa-file-pdf"></i>

                                        <span>
                                            Exportar PDF
                                        </span>

                                    </a>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="../controladores/exportar_mensajes_excel.php"
                                        target="_blank">

                                        <i class="fa-solid fa-file-excel"></i>

                                        <span>
                                            Exportar Excel
                                        </span>

                                    </a>

                                </li>

                            </ul>

                        </div>

                    </div>


                    <!-- =================================================
     BÚSQUEDA Y FILTROS AUTOMÁTICOS
================================================== -->

                    <div class="messages-search-area">

                        <form
                            id="formFiltros"
                            method="GET"
                            action="adm_mensajes.php"
                            class="messages-search-filters-form">

                            <!-- =================================================
             BÚSQUEDA
        ================================================== -->

                            <div class="messages-search">

                                <div class="search-icon">

                                    <i class="fa-solid fa-magnifying-glass"></i>

                                </div>

                                <input
                                    type="search"
                                    id="inputBuscar"
                                    name="buscar"
                                    value="<?= htmlspecialchars(
                                                $busqueda,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                    placeholder="Buscar por nombre, apellido, mensaje o fecha..."
                                    autocomplete="off">

                                <?php if (!empty($busqueda)): ?>

                                    <button
                                        type="button"
                                        id="btnLimpiarBusqueda"
                                        class="search-clear"
                                        title="Limpiar búsqueda"
                                        aria-label="Limpiar búsqueda">

                                        <i class="fa-solid fa-xmark"></i>

                                    </button>

                                <?php endif; ?>

                            </div>


                            <!-- =================================================
             FILTROS
        ================================================== -->

                            <div class="messages-filters">

                                <div class="filters-header">

                                    <div class="filters-title">

                                        <i class="fa-solid fa-filter"></i>

                                        <div>

                                            <strong>
                                                Filtrar mensajes
                                            </strong>

                                            <span>
                                                Los resultados se actualizan automáticamente.
                                            </span>

                                        </div>

                                    </div>


                                    <!-- =================================================
                     LIMPIAR FILTROS
                ================================================== -->

                                    <?php if ($filtrosActivos > 0): ?>

                                        <a
                                            href="adm_mensajes.php"
                                            class="filters-clear">

                                            <i class="fa-solid fa-rotate-left"></i>

                                            Limpiar filtros

                                        </a>

                                    <?php endif; ?>

                                </div>


                                <div class="row g-3 align-items-end">


                                    <!-- =================================================
                     PROPIEDAD
                ================================================== -->

                                    <div class="col-12 col-md-6 col-lg-3">

                                        <label
                                            for="filtroPropiedad"
                                            class="form-label">

                                            <i class="fa-solid fa-house"></i>

                                            Propiedad

                                        </label>

                                        <select
                                            name="propiedad"
                                            id="filtroPropiedad"
                                            class="form-select auto-filtro">

                                            <option value="">
                                                Todas las propiedades
                                            </option>

                                            <?php foreach ($propiedadesFiltro as $propiedad): ?>

                                                <option
                                                    value="<?= (int)$propiedad['id_propiedad'] ?>"
                                                    <?= $filtroPropiedad === (int)$propiedad['id_propiedad']
                                                        ? 'selected'
                                                        : '' ?>>

                                                    <?= htmlspecialchars(
                                                        $propiedad['nombre'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>


                                    <!-- =================================================
                     ESTADO
                ================================================== -->

                                    <div class="col-12 col-md-6 col-lg-3">

                                        <label
                                            for="filtroEstado"
                                            class="form-label">

                                            <i class="fa-solid fa-envelope"></i>

                                            Estado

                                        </label>

                                        <select
                                            name="estado"
                                            id="filtroEstado"
                                            class="form-select auto-filtro">

                                            <option
                                                value=""
                                                <?= $filtroEstado === ''
                                                    ? 'selected'
                                                    : '' ?>>

                                                Todos los estados

                                            </option>

                                            <option
                                                value="no_leido"
                                                <?= $filtroEstado === 'no_leido'
                                                    ? 'selected'
                                                    : '' ?>>

                                                No leídos

                                            </option>

                                            <option
                                                value="leido"
                                                <?= $filtroEstado === 'leido'
                                                    ? 'selected'
                                                    : '' ?>>

                                                Leídos

                                            </option>

                                        </select>

                                    </div>


                                    <!-- =================================================
                     FECHA DESDE
                ================================================== -->

                                    <div class="col-12 col-md-6 col-lg-2">

                                        <label
                                            for="fechaDesde"
                                            class="form-label">

                                            <i class="fa-regular fa-calendar"></i>

                                            Desde

                                        </label>

                                        <input
                                            type="date"
                                            name="fecha_desde"
                                            id="fechaDesde"
                                            class="form-control auto-filtro"
                                            value="<?= htmlspecialchars(
                                                        $fechaDesde,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                    </div>


                                    <!-- =================================================
                     FECHA HASTA
                ================================================== -->

                                    <div class="col-12 col-md-6 col-lg-2">

                                        <label
                                            for="fechaHasta"
                                            class="form-label">

                                            <i class="fa-regular fa-calendar"></i>

                                            Hasta

                                        </label>

                                        <input
                                            type="date"
                                            name="fecha_hasta"
                                            id="fechaHasta"
                                            class="form-control auto-filtro"
                                            value="<?= htmlspecialchars(
                                                        $fechaHasta,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>


                    <!-- =================================================
                     TABLA
                ================================================== -->

                    <?php if ($resultado && $resultado->num_rows > 0): ?>

                        <div class="messages-table-wrapper">

                            <table class="messages-table">

                                <thead>

                                    <tr>

                                        <th class="th-client">
                                            CLIENTE
                                        </th>

                                        <th>
                                            PROPIEDAD
                                        </th>

                                        <th>
                                            CONTACTO
                                        </th>
                                        <th>
                                            ESTADO
                                        </th>
                                        <th>
                                            FECHA
                                        </th>

                                        <th class="th-actions">
                                            ACCIONES
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                                        <?php

                                        $nombreCliente = trim(
                                            ($fila['nombre'] ?? '') . ' ' .
                                                ($fila['apellidos'] ?? '')
                                        );

                                        $nombreClienteMostrar =
                                            $nombreCliente !== ''
                                            ? $nombreCliente
                                            : 'Cliente';

                                        $iniciales = '';

                                        if (!empty($fila['nombre'])) {
                                            $iniciales .= strtoupper(
                                                substr($fila['nombre'], 0, 1)
                                            );
                                        }

                                        if (!empty($fila['apellidos'])) {
                                            $iniciales .= strtoupper(
                                                substr($fila['apellidos'], 0, 1)
                                            );
                                        }

                                        if ($iniciales === '') {
                                            $iniciales = 'C';
                                        }

                                        $telefono = preg_replace(
                                            '/[^0-9]/',
                                            '',
                                            $fila['celular'] ?? ''
                                        );

                                        $empresa = $usuario['nombreEmpresa'] ?? 'nuestra inmobiliaria';

                                        $mensajeWhatsapp = urlencode(
                                            "Hola {$nombreClienteMostrar},\n\n" .
                                                "Te saluda *{$empresa}*.\n\n" .
                                                "Gracias por tu interés en la propiedad:\n" .
                                                "*{$fila['nombre_propiedad']}*.\n\n" .
                                                "Con gusto podemos brindarte más información."
                                        );

                                        $urlWhatsapp =
                                            "https://wa.me/51{$telefono}?text={$mensajeWhatsapp}";


                                        $email = $fila['email'] ?? '';

                                        $asunto = urlencode(
                                            "Información sobre {$fila['nombre_propiedad']}"
                                        );

                                        $cuerpo = urlencode(
                                            "Hola {$nombreClienteMostrar},\n\n" .
                                                "Te saluda {$empresa}.\n\n" .
                                                "Gracias por tu interés en la propiedad: " .
                                                "{$fila['nombre_propiedad']}.\n\n" .
                                                "Con gusto podemos brindarte más información.\n\n" .
                                                "Saludos."
                                        );

                                        $urlCorreo =
                                            "mailto:{$email}?subject={$asunto}&body={$cuerpo}";


                                        $fechaMostrar = '';

                                        if (!empty($fila['fecha_registro'])) {

                                            $timestamp = strtotime(
                                                $fila['fecha_registro']
                                            );

                                            if ($timestamp !== false) {

                                                $fechaMostrar = date(
                                                    'd/m/Y',
                                                    $timestamp
                                                );
                                            }
                                        }

                                        ?>

                                        <tr>


                                            <!-- CLIENTE -->
                                            <td>

                                                <div class="client-cell">
                                                    <div class="client-info">

                                                        <strong>
                                                            <?= htmlspecialchars($nombreClienteMostrar) ?>
                                                        </strong>

                                                        <span>
                                                            <?= htmlspecialchars($fila['email'] ?? 'Sin correo') ?>
                                                        </span>

                                                    </div>

                                                </div>

                                            </td>


                                            <!-- PROPIEDAD -->
                                            <td>

                                                <div class="property-cell">

                                                    <div class="property-icon">

                                                        <i class="fa-solid fa-house"></i>

                                                    </div>

                                                    <div>

                                                        <strong>
                                                            <?= htmlspecialchars($fila['nombre_propiedad'] ?? 'No especificada') ?>
                                                        </strong>

                                                        <span>
                                                            Consulta inmobiliaria
                                                        </span>

                                                    </div>

                                                </div>

                                            </td>


                                            <!-- CONTACTO -->
                                            <td>

                                                <div class="contact-cell">

                                                    <?php if (!empty($fila['celular'])): ?>

                                                        <span class="contact-item">

                                                            <i class="fa-solid fa-phone"></i>

                                                            <?= htmlspecialchars($fila['celular']) ?>

                                                        </span>

                                                    <?php endif; ?>

                                                    <?php if (!empty($fila['email'])): ?>

                                                        <span class="contact-item email">

                                                            <i class="fa-solid fa-envelope"></i>

                                                            <span>
                                                                <?= htmlspecialchars($fila['email']) ?>
                                                            </span>

                                                        </span>

                                                    <?php endif; ?>

                                                </div>

                                            </td>
                                            <!-- ESTADO -->
                                            <td>

                                                <?php
                                                $mensajeLeido =
                                                    (int)($fila['estado_mensaje_leido'] ?? 0) === 1;
                                                ?>

                                                <span
                                                    class="message-status <?= $mensajeLeido ? 'read' : 'unread' ?>"
                                                    data-message-status="<?= (int)$fila['id_contacto'] ?>">

                                                    <?php if ($mensajeLeido): ?>

                                                        <i class="fa-solid fa-envelope-open"></i>
                                                        Leído

                                                    <?php else: ?>

                                                        <i class="fa-solid fa-envelope"></i>
                                                        No leído

                                                    <?php endif; ?>

                                                </span>

                                            </td>
                                            <!-- FECHA -->
                                            <td>

                                                <div class="date-cell">

                                                    <span>
                                                        <?= htmlspecialchars($fechaMostrar) ?>
                                                    </span>

                                                </div>

                                            </td>


                                            <!-- ACCIONES -->
                                            <td>

                                                <div class="message-actions">


                                                    <!-- WHATSAPP -->
                                                    <a
                                                        href="<?= htmlspecialchars($urlWhatsapp) ?>"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="message-action whatsapp"
                                                        title="Responder por WhatsApp">

                                                        <i class="fa-brands fa-whatsapp"></i>

                                                    </a>


                                                    <!-- CORREO -->
                                                    <a
                                                        href="<?= htmlspecialchars($urlCorreo) ?>"
                                                        class="message-action email"
                                                        title="Enviar correo">

                                                        <i class="fa-solid fa-envelope"></i>

                                                    </a>


                                                    <!-- VER -->
                                                    <button
                                                        type="button"
                                                        class="message-action view btn-ver-mensaje"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalVerDetalles"

                                                        data-id="<?= (int)$fila['id_contacto'] ?>"

                                                        data-nombre="<?= htmlspecialchars(
                                                                            $fila['nombre'] ?? '',
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"

                                                        data-apellidos="<?= htmlspecialchars(
                                                                            $fila['apellidos'] ?? '',
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"

                                                        data-email="<?= htmlspecialchars(
                                                                        $fila['email'] ?? '',
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>"

                                                        data-celular="<?= htmlspecialchars(
                                                                            $fila['celular'] ?? '',
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"

                                                        data-propiedad="<?= htmlspecialchars(
                                                                            $fila['nombre_propiedad'] ?? 'No especificada',
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"

                                                        data-estado="<?= (int)($fila['estado_mensaje_leido'] ?? 0) ?>"

                                                        data-fecha-leido="<?= htmlspecialchars(
                                                                                $fila['fecha_leido'] ?? '',
                                                                                ENT_QUOTES,
                                                                                'UTF-8'
                                                                            ) ?>"

                                                        data-fecha="<?= htmlspecialchars(
                                                                        $fila['fecha_registro'] ?? '',
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>"

                                                        data-mensaje="<?= htmlspecialchars(
                                                                            $fila['mensaje'] ?? '',
                                                                            ENT_QUOTES,
                                                                            'UTF-8'
                                                                        ) ?>"

                                                        title="Ver mensaje">

                                                        <i class="fa-solid fa-eye"></i>

                                                    </button>
                                                </div>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                        <!-- =================================================
 PAGINACIÓN
================================================== -->

                        <div class="messages-pagination">

                            <div class="pagination-info">

                                <i class="fa-solid fa-layer-group"></i>

                                <span>
                                    Página
                                    <strong><?= $pagina ?></strong>
                                    de
                                    <strong><?= max(1, $totalPaginas) ?></strong>
                                </span>

                                <span class="pagination-separator">
                                    •
                                </span>

                                <span>
                                    <?= number_format($totalMensaje) ?>
                                    registros
                                </span>

                            </div>


                            <div class="pagination-buttons">


                                <!-- =================================================
             ANTERIOR
        ================================================== -->

                                <?php if ($pagina > 1): ?>

                                    <a
                                        href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($busqueda) ?>&propiedad=<?= $filtroPropiedad ?>&estado=<?= urlencode($filtroEstado) ?>&fecha_desde=<?= urlencode($fechaDesde) ?>&fecha_hasta=<?= urlencode($fechaHasta) ?>"
                                        class="pagination-button">

                                        <i class="fa-solid fa-chevron-left"></i>

                                        <span>
                                            Anterior
                                        </span>

                                    </a>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="pagination-button disabled"
                                        disabled>

                                        <i class="fa-solid fa-chevron-left"></i>

                                        <span>
                                            Anterior
                                        </span>

                                    </button>

                                <?php endif; ?>


                                <!-- =================================================
             PÁGINA ACTUAL
        ================================================== -->

                                <div class="pagination-current">

                                    <?= $pagina ?>

                                </div>


                                <!-- =================================================
             SIGUIENTE
        ================================================== -->

                                <?php if ($pagina < $totalPaginas): ?>

                                    <a
                                        href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($busqueda) ?>&propiedad=<?= $filtroPropiedad ?>&estado=<?= urlencode($filtroEstado) ?>&fecha_desde=<?= urlencode($fechaDesde) ?>&fecha_hasta=<?= urlencode($fechaHasta) ?>"
                                        class="pagination-button">

                                        <span>
                                            Siguiente
                                        </span>

                                        <i class="fa-solid fa-chevron-right"></i>

                                    </a>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="pagination-button disabled"
                                        disabled>

                                        <span>
                                            Siguiente
                                        </span>

                                        <i class="fa-solid fa-chevron-right"></i>

                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>
                    <?php else: ?>


                        <!-- =================================================
                         ESTADO VACÍO
                    ================================================== -->

                        <div class="messages-empty">

                            <div class="empty-message-icon">

                                <i class="fa-regular fa-envelope-open"></i>

                            </div>

                            <h3>
                                No hay mensajes
                            </h3>

                            <?php if (!empty($busqueda)): ?>

                                <p>
                                    No encontramos mensajes que coincidan
                                    con tu búsqueda.
                                </p>

                                <a
                                    href="adm_mensajes.php"
                                    class="empty-button">

                                    <i class="fa-solid fa-arrow-left"></i>

                                    Ver todos los mensajes

                                </a>

                            <?php else: ?>

                                <p>
                                    Cuando un cliente envíe una consulta
                                    desde una propiedad, aparecerá aquí.
                                </p>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>


                </section>


                <!-- FOOTER -->
                <footer class="dashboard-footer">

                    <span>
                        © <?= date('Y') ?> CoDevPro Technology
                    </span>

                    <span>
                        Panel de administración inmobiliaria
                    </span>

                </footer>


            </div>

        </main>

    </div>


    <!-- =========================================================
     MODALES
========================================================= -->

    <?php include '../modal/modal_mensaje.php'; ?>


    <!-- =========================================================
     SCRIPTS
========================================================= -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="../js/lista_mensajes.js"></script>
    <script src="../js/menu_sidebar.js"></script>

</body>

</html>