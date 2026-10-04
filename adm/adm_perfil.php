<?php

session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

include '../controladores/conect_db.php';
include '../controladores/procesar_perfil.php';

$usId = (int) $_SESSION['usId'];

?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Perfil — <?= htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8') ?>
    </title>

    <?php if (!empty($fotoPerfil)): ?>

        <link
            rel="icon"
            href="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') ?>"
            type="image/png">

    <?php else: ?>

        <link
            rel="icon"
            href="../img/logo.png"
            type="image/png">

    <?php endif; ?>


    <!-- =====================================================
         ICONOS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         CSS DEL PANEL
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">

    <link
        rel="stylesheet"
        href="../css/adm_perfil.css">

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
                            src="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') ?>"
                            alt="Empresa">

                    <?php else: ?>

                        <i class="fa-solid fa-building"></i>

                    <?php endif; ?>

                </div>


                <div class="company-info">

                    <strong>
                        <?= htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8') ?>
                    </strong>

                    <span>
                        Panel administrativo
                    </span>

                </div>

            </div>


            <!-- NAVEGACIÓN -->

            <nav class="sidebar-navigation">

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
                    class="sidebar-link">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </span>

                        <span>
                            Mensajes
                        </span>

                    </span>


                    <?php if ($totalMensajes > 0): ?>

                        <span class="sidebar-badge">
                            <?= $totalMensajes > 99 ? '99+' : $totalMensajes ?>
                        </span>

                    <?php endif; ?>

                </a>


                <div class="sidebar-section-title">
                    GESTIÓN
                </div>


                <!-- PROPIEDADES -->

                <a
                    class="sidebar-link sidebar-collapse-link"
                    data-bs-toggle="collapse"
                    href="#menuInventario"
                    role="button"
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


                <ul
                    class="collapse list-unstyled sidebar-submenu"
                    id="menuInventario">

                    <li>

                        <a
                            href="adm_lista_propiedades.php"
                            class="sidebar-sublink">

                            <i class="fa-solid fa-list"></i>

                            <span>
                                Ver propiedades
                            </span>

                        </a>

                    </li>


                    <li>

                        <a
                            href="adm_registrar_propiedad.php"
                            class="sidebar-sublink">

                            <i class="fa-solid fa-circle-plus"></i>

                            <span>
                                Registrar propiedad
                            </span>

                        </a>

                    </li>


                    <li>

                        <a
                            href="adm_categorias.php"
                            class="sidebar-sublink">

                            <i class="fa-solid fa-layer-group"></i>

                            <span>
                                Categorías
                            </span>

                        </a>

                    </li>

                </ul>


                <!-- ASESORES -->

                <a
                    class="sidebar-link sidebar-collapse-link"
                    data-bs-toggle="collapse"
                    href="#menuClientes"
                    role="button"
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


                <ul
                    class="collapse list-unstyled sidebar-submenu"
                    id="menuClientes">

                    <li>

                        <a
                            href="adm_registrar_asesor.php"
                            class="sidebar-sublink">

                            <i class="fa-solid fa-user-plus"></i>

                            <span>
                                Registrar asesor
                            </span>

                        </a>

                    </li>


                    <li>

                        <a
                            href="adm_lista_asesores.php"
                            class="sidebar-sublink">

                            <i class="fa-solid fa-users"></i>

                            <span>
                                Lista de asesores
                            </span>

                        </a>

                    </li>

                </ul>
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


                <!-- PERFIL -->

                <a
                    href="adm_perfil.php"
                    class="sidebar-link">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-user"></i>
                        </span>

                        <span>
                            Mi perfil
                        </span>

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

            </nav>


            <!-- FOOTER -->

            <div class="sidebar-footer">

                <i class="fa-solid fa-shield-halved sidebar-security-icon"></i>

                <span>
                    Sistema seguro
                </span>

            </div>

        </aside>


        <!-- OVERLAY -->

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
                        class="sidebar-toggle"
                        id="toggleSidebar"
                        aria-label="Abrir menú"
                        aria-expanded="false">

                        <i class="fa-solid fa-bars"></i>

                    </button>


                    <div class="topbar-title">

                        <span>
                            CUENTA
                        </span>

                        <strong>
                            Mi perfil
                        </strong>

                    </div>

                </div>


                <div class="topbar-right">


                    <!-- NOTIFICACIONES -->

                    <a
                        href="adm_mensajes.php"
                        class="topbar-icon"
                        aria-label="Mensajes">

                        <i class="fa-regular fa-bell"></i>

                        <?php if ($totalMensajes > 0): ?>

                            <span class="notification-dot">
                                <?= $totalMensajes > 99 ? '99+' : $totalMensajes ?>
                            </span>

                        <?php endif; ?>

                    </a>


                    <!-- PERFIL -->

                    <a
                        href="adm_perfil.php"
                        class="topbar-profile">

                        <div class="topbar-avatar">

                            <?php if (!empty($fotoPerfil)): ?>

                                <img
                                    src="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') ?>"
                                    alt="Perfil">

                            <?php else: ?>

                                <i class="fa-solid fa-user"></i>

                            <?php endif; ?>

                        </div>


                        <div class="topbar-user">

                            <strong>
                                <?= htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8') ?>
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
             PERFIL
        ================================================== -->

            <section class="profile-content">


                <!-- =================================================
                 CABECERA
            ================================================== -->

                <div class="profile-hero">

                    <div class="profile-hero-background"></div>


                    <div class="profile-hero-content">


                        <div class="profile-avatar-wrapper">

                            <div
                                class="profile-avatar"
                                id="profileAvatarPreview">

                                <?php if (!empty($fotoPerfil)): ?>

                                    <img
                                        src="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="Imagen de perfil"
                                        id="profileImage">

                                <?php else: ?>

                                    <i
                                        class="fa-solid fa-building"
                                        id="profileImagePlaceholder"></i>

                                <?php endif; ?>

                            </div>


                            <label
                                for="imagenPerfil"
                                class="profile-avatar-edit"
                                title="Cambiar imagen">

                                <i class="fa-solid fa-camera"></i>

                            </label>

                        </div>


                        <div class="profile-hero-info">

                            <span class="profile-eyebrow">
                                CUENTA ADMINISTRATIVA
                            </span>

                            <h1>
                                <?= htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8') ?>
                            </h1>

                            <p>
                                @<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                            </p>


                            <div class="profile-status-row">

                                <span class="profile-status active">

                                    <span class="status-dot"></span>

                                    <?= htmlspecialchars($estadoUsuario, ENT_QUOTES, 'UTF-8') ?>

                                </span>


                                <?php if (!empty($fechaRegistro)): ?>

                                    <span class="profile-date">

                                        <i class="fa-regular fa-calendar"></i>

                                        Desde
                                        <?= htmlspecialchars($fechaRegistro, ENT_QUOTES, 'UTF-8') ?>

                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- INPUT OCULTO PARA IMAGEN -->

                <input
                    type="file"
                    id="imagenPerfil"
                    accept="image/jpeg,image/png,image/webp"
                    hidden>


                <!-- =================================================
                 MENSAJE AJAX
            ================================================== -->

                <div
                    id="profileAlert"
                    class="profile-alert"
                    role="alert">

                    <i class="fa-solid fa-circle-check alert-icon"></i>

                    <span id="profileAlertText"></span>

                    <button
                        type="button"
                        class="alert-close"
                        id="profileAlertClose">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                </div>


                <!-- =================================================
                 GRID PRINCIPAL
            ================================================== -->

                <div class="profile-grid">


                    <!-- =================================================
                     INFORMACIÓN EMPRESA
                ================================================== -->

                    <section class="profile-card profile-main-card">

                        <div class="profile-card-header">

                            <div>

                                <span class="card-eyebrow">
                                    INFORMACIÓN
                                </span>

                                <h2>
                                    Datos de la empresa
                                </h2>

                                <p>
                                    Administra la información pública de tu empresa.
                                </p>

                            </div>


                            <div class="profile-card-icon green">

                                <i class="fa-solid fa-building"></i>

                            </div>

                        </div>


                        <form
                            id="profileForm"
                            autocomplete="off">


                            <div class="profile-form-grid">


                                <!-- NOMBRE -->

                                <div class="profile-field field-full">

                                    <label for="nombreEmpresa">
                                        Nombre de empresa
                                    </label>

                                    <div class="profile-input-wrapper">

                                        <i class="fa-solid fa-building"></i>

                                        <input
                                            type="text"
                                            id="nombreEmpresa"
                                            name="nombreEmpresa"
                                            maxlength="150"
                                            value="<?= htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8') ?>"
                                            required>

                                    </div>

                                </div>


                                <!-- RUC -->

                                <div class="profile-field">

                                    <label for="ruc">
                                        RUC
                                    </label>

                                    <div class="profile-input-wrapper">

                                        <i class="fa-solid fa-id-card"></i>

                                        <input
                                            type="text"
                                            id="ruc"
                                            name="ruc"
                                            maxlength="20"
                                            value="<?= htmlspecialchars($ruc, ENT_QUOTES, 'UTF-8') ?>">

                                    </div>

                                </div>


                                <!-- CELULAR -->

                                <div class="profile-field">

                                    <label for="celular">
                                        Celular
                                    </label>

                                    <div class="profile-input-wrapper">

                                        <i class="fa-solid fa-mobile-screen"></i>

                                        <input
                                            type="text"
                                            id="celular"
                                            name="celular"
                                            maxlength="30"
                                            value="<?= htmlspecialchars($celular, ENT_QUOTES, 'UTF-8') ?>">

                                    </div>

                                </div>


                                <!-- EMAIL -->

                                <div class="profile-field">

                                    <label for="email">
                                        Correo electrónico
                                    </label>

                                    <div class="profile-input-wrapper">

                                        <i class="fa-regular fa-envelope"></i>

                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            maxlength="150"
                                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                            required>

                                    </div>

                                </div>


                                <!-- USERNAME -->

                                <div class="profile-field">

                                    <label for="username">
                                        Usuario
                                    </label>

                                    <div class="profile-input-wrapper">

                                        <i class="fa-solid fa-at"></i>

                                        <input
                                            type="text"
                                            id="username"
                                            value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                                            readonly>

                                    </div>

                                    <small>
                                        El usuario de acceso no se puede modificar desde aquí.
                                    </small>

                                </div>


                                <!-- DIRECCIÓN -->

                                <div class="profile-field field-full">

                                    <label for="direccion">
                                        Dirección
                                    </label>

                                    <div class="profile-input-wrapper">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <input
                                            type="text"
                                            id="direccion"
                                            name="direccion"
                                            maxlength="250"
                                            value="<?= htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') ?>">

                                    </div>

                                </div>


                                <!-- DESCRIPCIÓN -->

                                <div class="profile-field field-full">

                                    <label for="descripcion">
                                        Acerca de la empresa
                                    </label>

                                    <div class="profile-input-wrapper textarea-wrapper">

                                        <i class="fa-solid fa-align-left"></i>

                                        <textarea
                                            id="descripcion"
                                            name="descripcion"
                                            maxlength="500"
                                            rows="4"><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></textarea>

                                    </div>

                                    <div class="field-counter">

                                        <span id="descripcionCounter">
                                            <?= mb_strlen($descripcion, 'UTF-8') ?>
                                        </span>
                                        / 500

                                    </div>

                                </div>

                            </div>


                            <div class="profile-form-footer">

                                <span class="save-info">

                                    <i class="fa-solid fa-circle-info"></i>

                                    Los cambios se guardan de forma segura.

                                </span>


                                <button
                                    type="submit"
                                    class="profile-save-button"
                                    id="saveProfileButton">

                                    <i class="fa-solid fa-floppy-disk"></i>

                                    <span>
                                        Guardar cambios
                                    </span>

                                </button>

                            </div>

                        </form>

                    </section>


                    <!-- =================================================
                     COLUMNA DERECHA
                ================================================== -->

                    <div class="profile-side-column">


                        <!-- =================================================
                         IMAGEN
                    ================================================== -->

                        <section class="profile-card">

                            <div class="profile-card-header compact">

                                <div>

                                    <span class="card-eyebrow">
                                        IDENTIDAD
                                    </span>

                                    <h2>
                                        Imagen de perfil
                                    </h2>

                                </div>


                                <div class="profile-card-icon purple">

                                    <i class="fa-solid fa-image"></i>

                                </div>

                            </div>


                            <div class="image-upload-content">

                                <div class="image-upload-preview">

                                    <?php if (!empty($fotoPerfil)): ?>

                                        <img
                                            src="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') ?>"
                                            alt="Imagen actual"
                                            id="imageUploadPreview">

                                    <?php else: ?>

                                        <div id="imageUploadPlaceholder">

                                            <i class="fa-solid fa-building"></i>

                                        </div>

                                    <?php endif; ?>

                                </div>


                                <strong>
                                    Logo o imagen de empresa
                                </strong>

                                <span>
                                    JPG, PNG o WEBP · máximo recomendado 2 MB
                                </span>


                                <label
                                    for="imagenPerfil"
                                    class="image-select-button">

                                    <i class="fa-solid fa-upload"></i>

                                    Seleccionar imagen

                                </label>


                                <button
                                    type="button"
                                    class="image-save-button"
                                    id="saveImageButton"
                                    disabled>

                                    <i class="fa-solid fa-check"></i>

                                    Guardar imagen

                                </button>

                            </div>

                        </section>

                        <!-- =================================================
     VIDEO INFORMATIVO
================================================== -->

                        <section class="profile-card video-profile-card">

                            <div class="profile-card-header compact">

                                <div>

                                    <span class="card-eyebrow">
                                        PRESENTACIÓN
                                    </span>

                                    <h2>
                                        Video informativo
                                    </h2>

                                    <p>
                                        Presenta tu empresa mediante un video.
                                    </p>

                                </div>

                                <div class="profile-card-icon red">

                                    <i class="fa-solid fa-video"></i>

                                </div>

                            </div>


                            <!-- VISTA PREVIA -->

                            <div class="video-upload-preview" id="videoUploadPreview">

                                <?php if (!empty($video)): ?>

                                    <video
                                        id="empresaVideoPreview"
                                        controls
                                        preload="metadata"
                                        playsinline>

                                        <source
                                            src="<?= htmlspecialchars($video, ENT_QUOTES, 'UTF-8') ?>">

                                        Tu navegador no soporta la reproducción de videos.

                                    </video>

                                <?php else: ?>

                                    <div
                                        class="video-upload-placeholder"
                                        id="videoUploadPlaceholder">

                                        <i class="fa-solid fa-film"></i>

                                        <span>
                                            No hay video informativo
                                        </span>

                                    </div>

                                <?php endif; ?>

                            </div>


                            <div class="video-upload-info">

                                <strong>
                                    Video de presentación de la empresa
                                </strong>

                                <span>
                                    MP4, WEBM u OGG · máximo 50 MB
                                </span>

                            </div>


                            <!-- INPUT -->

                            <input
                                type="file"
                                id="videoEmpresa"
                                accept="video/mp4,video/webm,video/ogg"
                                hidden>


                            <div class="video-upload-actions">

                                <label
                                    for="videoEmpresa"
                                    class="video-select-button">

                                    <i class="fa-solid fa-upload"></i>

                                    Seleccionar video

                                </label>


                                <button
                                    type="button"
                                    class="video-save-button"
                                    id="saveVideoButton"
                                    disabled>

                                    <i class="fa-solid fa-check"></i>

                                    Guardar video

                                </button>

                            </div>


                            <?php if (!empty($video)): ?>

                                <button
                                    type="button"
                                    class="video-delete-button"
                                    id="deleteVideoButton">

                                    <i class="fa-solid fa-trash"></i>

                                    Eliminar video actual

                                </button>

                            <?php endif; ?>

                        </section>
                        <!-- =================================================
                         SEGURIDAD
                    ================================================== -->

                        <section class="profile-card">

                            <div class="profile-card-header compact">

                                <div>

                                    <span class="card-eyebrow">
                                        SEGURIDAD
                                    </span>

                                    <h2>
                                        Acceso
                                    </h2>

                                </div>


                                <div class="profile-card-icon orange">

                                    <i class="fa-solid fa-shield-halved"></i>

                                </div>

                            </div>


                            <div class="security-summary">

                                <div class="security-row">

                                    <div class="security-icon">

                                        <i class="fa-solid fa-user-lock"></i>

                                    </div>

                                    <div>

                                        <strong>
                                            Usuario de acceso
                                        </strong>

                                        <span>
                                            <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                                        </span>

                                    </div>

                                </div>


                                <div class="security-row">

                                    <div class="security-icon">

                                        <i class="fa-solid fa-key"></i>

                                    </div>

                                    <div>

                                        <strong>
                                            Contraseña
                                        </strong>

                                        <span>
                                            <?= !empty($passwordChangedAt)
                                                ? 'Actualizada el ' . htmlspecialchars($passwordChangedAt, ENT_QUOTES, 'UTF-8')
                                                : 'No hay registro de cambio' ?>
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <button
                                type="button"
                                class="security-button"
                                data-bs-toggle="modal"
                                data-bs-target="#passwordModal">

                                <i class="fa-solid fa-key"></i>

                                Cambiar contraseña

                            </button>

                        </section>

                    </div>

                </div>


                <!-- =================================================
                 FOOTER
            ================================================== -->

                <footer class="profile-footer">

                    <span>
                        <i class="fa-solid fa-shield-halved"></i>
                        Tu información está protegida.
                    </span>

                    <span>
                        CoDevPro Technology
                    </span>

                </footer>

            </section>

        </main>

    </div>


    <!-- =========================================================
     MODAL CAMBIO CONTRASEÑA
========================================================= -->

    <div
        class="modal fade"
        id="passwordModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content password-modal">

                <div class="modal-header">

                    <div class="modal-heading">

                        <div class="modal-icon">

                            <i class="fa-solid fa-key"></i>

                        </div>

                        <div>

                            <span>
                                SEGURIDAD
                            </span>

                            <h3>
                                Cambiar contraseña
                            </h3>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                    </button>

                </div>


                <form id="passwordForm">

                    <div class="modal-body">


                        <div class="password-field">

                            <label for="contrasenaActual">
                                Contraseña actual
                            </label>

                            <div class="password-input">

                                <i class="fa-solid fa-lock"></i>

                                <input
                                    type="password"
                                    id="contrasenaActual"
                                    name="contrasenaActual"
                                    autocomplete="current-password"
                                    required>

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="contrasenaActual">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>

                        </div>


                        <div class="password-field">

                            <label for="nuevaContrasena">
                                Nueva contraseña
                            </label>

                            <div class="password-input">

                                <i class="fa-solid fa-key"></i>

                                <input
                                    type="password"
                                    id="nuevaContrasena"
                                    name="nuevaContrasena"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required>

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="nuevaContrasena">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>

                        </div>


                        <div class="password-field">

                            <label for="confirmarContrasena">
                                Confirmar nueva contraseña
                            </label>

                            <div class="password-input">

                                <i class="fa-solid fa-check-double"></i>

                                <input
                                    type="password"
                                    id="confirmarContrasena"
                                    name="confirmarContrasena"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required>

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="confirmarContrasena">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>

                        </div>


                        <div class="password-requirements">

                            <strong>
                                Recomendación
                            </strong>

                            <span>
                                Utiliza al menos 8 caracteres y combina letras,
                                números y símbolos.
                            </span>

                        </div>


                        <div
                            id="passwordAlert"
                            class="password-alert">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <span id="passwordAlertText"></span>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="modal-cancel-button"
                            data-bs-dismiss="modal">

                            Cancelar

                        </button>


                        <button
                            type="submit"
                            class="modal-save-button"
                            id="savePasswordButton">

                            <i class="fa-solid fa-shield-halved"></i>

                            Actualizar contraseña

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- =========================================================
     JAVASCRIPT
========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

    <script
        src="../js/adm_perfil.js">
    </script>

    <script
        src="../js/menu_sidebar.js">
    </script>

</body>

</html>