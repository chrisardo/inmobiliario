<?php
// adm/adm_index.php

session_start();

// Procesador del dashboard
include '../controladores/procesar_index.php';
?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="theme-color"
        content="#0f172a">

    <title>
        Dashboard — <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Panel') ?>
    </title>

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


    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <!-- CSS del dashboard -->
    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">

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
                    class="sidebar-link active">

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
                            Dashboard
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
                DASHBOARD
            ================================================== -->

            <div class="dashboard-content">


                <!-- HERO -->

                <section class="dashboard-hero">

                    <div>

                        <span class="dashboard-eyebrow">
                            RESUMEN GENERAL
                        </span>

                        <h1>
                            ¡Bienvenido,
                            <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Administrador') ?>!
                        </h1>

                        <p>
                            Aquí tienes una vista general de la actividad
                            de tu empresa.
                        </p>

                    </div>


                    <div class="hero-date">

                        <i class="fa-regular fa-calendar"></i>

                        <span>
                            <?= date('d/m/Y') ?>
                        </span>

                    </div>

                </section>


                <!-- =================================================
                    KPIs
                ================================================== -->

                <section class="dashboard-section">

                    <div class="section-heading">

                        <div>

                            <span class="section-label">
                                INDICADORES
                            </span>

                            <h2>
                                Resumen de actividad
                            </h2>

                        </div>

                    </div>


                    <div class="row g-4">


                        <!-- MENSAJES -->

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="dashboard-kpi kpi-blue">

                                <div class="kpi-top">

                                    <div class="kpi-icon">

                                        <i class="fa-solid fa-envelope"></i>

                                    </div>

                                    <span class="kpi-status">
                                        Mensajes
                                    </span>

                                </div>


                                <div class="kpi-content">

                                    <span
                                        class="kpi-number"
                                        data-value="<?= (int)$totalContacto ?>">
                                        0
                                    </span>

                                    <span class="kpi-label">
                                        Contactos recibidos
                                    </span>

                                </div>


                                <a
                                    href="adm_mensajes.php"
                                    class="kpi-link">

                                    Ver mensajes

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </div>


                        <!-- PROPIEDADES -->

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="dashboard-kpi kpi-green">

                                <div class="kpi-top">

                                    <div class="kpi-icon">

                                        <i class="fa-solid fa-building"></i>

                                    </div>

                                    <span class="kpi-status">
                                        Propiedades
                                    </span>

                                </div>


                                <div class="kpi-content">

                                    <span
                                        class="kpi-number"
                                        data-value="<?= (int)$totalPropiedades ?>">
                                        0
                                    </span>

                                    <span class="kpi-label">
                                        Propiedades registradas
                                    </span>

                                </div>


                                <a
                                    href="adm_lista_propiedades.php"
                                    class="kpi-link">

                                    Ver propiedades

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </div>


                        <!-- ASESORES -->

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="dashboard-kpi kpi-purple">

                                <div class="kpi-top">

                                    <div class="kpi-icon">

                                        <i class="fa-solid fa-user-tie"></i>

                                    </div>

                                    <span class="kpi-status">
                                        Equipo
                                    </span>

                                </div>


                                <div class="kpi-content">

                                    <span
                                        class="kpi-number"
                                        data-value="<?= (int)$totalAsesores ?>">
                                        0
                                    </span>

                                    <span class="kpi-label">
                                        Asesores registrados
                                    </span>

                                </div>


                                <a
                                    href="adm_lista_asesores.php"
                                    class="kpi-link">

                                    Ver asesores

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                    CONTENIDO INFERIOR
                ================================================== -->

                <div class="row g-4 dashboard-lower">


                    <!-- ACCIONES RÁPIDAS -->

                    <div class="col-12 col-xl-5">

                        <section class="dashboard-panel">

                            <div class="panel-header">

                                <div>

                                    <span class="section-label">
                                        ATAJOS
                                    </span>

                                    <h2>
                                        Acciones rápidas
                                    </h2>

                                </div>

                            </div>


                            <div class="quick-actions">

                                <a
                                    href="adm_registrar_propiedad.php"
                                    class="quick-action">

                                    <div class="quick-action-icon green">
                                        <i class="fa-solid fa-building-circle-check"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            Nueva propiedad
                                        </strong>

                                        <span>
                                            Registrar una propiedad
                                        </span>

                                    </div>

                                    <i class="fa-solid fa-chevron-right"></i>

                                </a>


                                <a
                                    href="adm_registrar_asesor.php"
                                    class="quick-action">

                                    <div class="quick-action-icon purple">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            Nuevo asesor
                                        </strong>

                                        <span>
                                            Agregar integrante al equipo
                                        </span>

                                    </div>

                                    <i class="fa-solid fa-chevron-right"></i>

                                </a>


                                <a
                                    href="adm_categorias.php"
                                    class="quick-action">

                                    <div class="quick-action-icon orange">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            Categorías
                                        </strong>

                                        <span>
                                            Administrar categorías
                                        </span>

                                    </div>

                                    <i class="fa-solid fa-chevron-right"></i>

                                </a>


                                <a
                                    href="adm_mensajes.php"
                                    class="quick-action">

                                    <div class="quick-action-icon blue">
                                        <i class="fa-solid fa-envelope-open-text"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            Revisar mensajes
                                        </strong>

                                        <span>
                                            Consultar contactos recientes
                                        </span>

                                    </div>

                                    <i class="fa-solid fa-chevron-right"></i>

                                </a>

                            </div>

                        </section>

                    </div>


                    <!-- ACTIVIDAD -->

                    <div class="col-12 col-xl-7">

                        <section class="dashboard-panel">

                            <div class="panel-header">

                                <div>

                                    <span class="section-label">
                                        ACTIVIDAD
                                    </span>

                                    <h2>
                                        Últimos registros
                                    </h2>

                                </div>

                            </div>


                            <div class="activity-list">


                                <?php if (!empty($ultimosMensajes)): ?>

                                    <?php foreach ($ultimosMensajes as $mensaje): ?>

                                        <div class="activity-item">

                                            <div class="activity-avatar">

                                                <i class="fa-solid fa-user"></i>

                                            </div>

                                            <div class="activity-info">

                                                <strong>
                                                    <?= htmlspecialchars(
                                                        trim(
                                                            ($mensaje['nombre'] ?? '') .
                                                                ' ' .
                                                                ($mensaje['apellidos'] ?? '')
                                                        )
                                                    ) ?>
                                                </strong>

                                                <span>
                                                    <?= htmlspecialchars($mensaje['mensaje'] ?? '') ?>
                                                </span>

                                            </div>

                                            <div class="activity-date">

                                                <?= !empty($mensaje['fecha_registro'])
                                                    ? date(
                                                        'd/m/Y',
                                                        strtotime($mensaje['fecha_registro'])
                                                    )
                                                    : ''
                                                ?>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <div class="empty-state">

                                        <div class="empty-icon">
                                            <i class="fa-regular fa-envelope"></i>
                                        </div>

                                        <strong>
                                            No hay mensajes recientes
                                        </strong>

                                        <span>
                                            Los nuevos contactos aparecerán aquí.
                                        </span>

                                    </div>

                                <?php endif; ?>


                            </div>


                            <div class="panel-footer">

                                <a href="adm_mensajes.php">

                                    Ver todos los mensajes

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </section>

                    </div>

                </div>


                <!-- =================================================
                    FOOTER
                ================================================== -->

                <footer class="dashboard-footer">

                    <span>
                        © <?= date('Y') ?>
                        <?= htmlspecialchars($usuario['nombreEmpresa'] ?? '') ?>
                    </span>

                    <span>
                        Panel administrativo
                    </span>

                </footer>


            </div>

        </main>

    </div>


    <!-- Bootstrap -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- Dashboard JS -->

    <script src="../js/menu_sidebar.js"></script>
    <script src="../js/numero.js"></script>
</body>

</html>