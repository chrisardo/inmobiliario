<?php
// ==========================================================
// CoDevPro Technology
// Archivo: nosotros.php
// Módulo: Página Nosotros
// Sistema: Inmobiliaria
// ==========================================================

require_once "controladores/index.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="<?= e($descripcionEmpresa); ?>">

    <meta
        name="author"
        content="<?= e($nombreEmpresa); ?>">

    <title>
        Nosotros - <?= e($nombreEmpresa); ?>
    </title>

    <!-- ======================================================
         FAVICON
    ======================================================= -->

    <?php if ($fotoPerfil): ?>
        <link
            rel="icon"
            href="<?= $fotoPerfil ?>"
            type="image/png">


    <?php else: ?>

        <span class="company-logo-placeholder">

            <i class="fa-solid fa-building"></i>

        </span>

    <?php endif; ?>

    <!-- ======================================================
         BOOTSTRAP
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- ======================================================
         FONT AWESOME
    ======================================================= -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ======================================================
         BOOTSTRAP ICONS
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- ======================================================
         CSS PRINCIPAL
    ======================================================= -->

    <link
        rel="stylesheet"
        href="css/style.css">

</head>

<body>

    <!-- ======================================================
         BARRA SUPERIOR
    ======================================================= -->

    <?php require "otros/barra_superior.php"; ?>


    <!-- ======================================================
         NAVBAR
    ======================================================= -->

    <nav class="navbar navbar-expand-lg main-navbar">

        <div class="container">

            <!-- LOGO + EMPRESA -->

            <a
                class="navbar-brand d-flex align-items-center"
                href="index.php">

                <?php if ($fotoPerfil): ?>

                    <img
                        src="<?= e($fotoPerfil); ?>"
                        alt="Logo <?= e($nombreEmpresa); ?>"
                        class="company-logo">

                <?php else: ?>

                    <span class="company-logo-placeholder">
                        <i class="fas fa-building"></i>
                    </span>

                <?php endif; ?>

                <span class="company-name">
                    <?= e($nombreEmpresa); ?>
                </span>

            </a>


            <!-- BOTÓN MOBILE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Abrir menú">

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- MENÚ -->

            <div
                class="collapse navbar-collapse"
                id="navbarNav">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="index.php">

                            Inicio

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link active"
                            aria-current="page"
                            href="nosotros.php">

                            Conócenos

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="propiedades.php">

                            Propiedades

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="asesores.php">

                            Asesores

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="contacto.php">

                            Contacto

                        </a>

                    </li>

                </ul>


                <!-- LOGIN -->

                <div class="ms-lg-3 mt-3 mt-lg-0">

                    <a
                        href="login.php"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-success px-4">

                        <i class="bi bi-person-circle me-1"></i>

                        Login

                    </a>

                </div>

            </div>

        </div>

    </nav>


    <!-- ======================================================
         ENCABEZADO / BREADCRUMB
    ======================================================= -->

    <header class="page-header">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-7">

                    <span class="page-header-eyebrow">
                        Conoce nuestra empresa
                    </span>

                    <h1>
                        Nosotros
                    </h1>

                    <p>
                        Descubre quiénes somos, nuestros valores y el compromiso
                        que tenemos con nuestros clientes.
                    </p>

                </div>


                <div class="col-lg-5">

                    <nav
                        aria-label="breadcrumb">

                        <ol class="breadcrumb justify-content-lg-end mb-0">

                            <li class="breadcrumb-item">

                                <a href="index.php">
                                    Inicio
                                </a>

                            </li>

                            <li
                                class="breadcrumb-item active"
                                aria-current="page">

                                Nosotros

                            </li>

                        </ol>

                    </nav>

                </div>

            </div>

        </div>

    </header>


    <!-- ======================================================
         PRESENTACIÓN DE LA EMPRESA
    ======================================================= -->

    <section class="about-hero-section">

        <div class="container">

            <div class="row align-items-center g-5">

                <!-- TEXTO -->

                <div class="col-lg-6">

                    <span class="section-eyebrow">
                        Sobre nosotros
                    </span>

                    <h2 class="section-title mb-4">

                        <?= e($nombreEmpresa); ?>

                    </h2>

                    <p class="about-description">

                        <?= nl2br(e($descripcionEmpresa)); ?>

                    </p>


                    <!-- INFORMACIÓN RÁPIDA -->

                    <div class="row g-3 mt-4">

                        <div class="col-sm-6">

                            <div class="about-mini-card">

                                <div class="about-mini-icon">

                                    <i class="bi bi-building"></i>

                                </div>

                                <div>

                                    <strong>
                                        <?= number_format($totalPropiedades); ?>
                                    </strong>

                                    <span>
                                        Propiedades disponibles
                                    </span>

                                </div>

                            </div>

                        </div>


                        <div class="col-sm-6">

                            <div class="about-mini-card">

                                <div class="about-mini-icon">

                                    <i class="bi bi-shield-check"></i>

                                </div>

                                <div>

                                    <strong>
                                        Calidad
                                    </strong>

                                    <span>
                                        Servicio profesional
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- BOTONES -->

                    <div class="about-actions mt-4">

                        <a
                            href="propiedades.php"
                            class="btn btn-success btn-lg">

                            <i class="bi bi-buildings me-2"></i>

                            Ver propiedades

                        </a>

                        <a
                            href="contacto.php"
                            class="btn btn-outline-success btn-lg">

                            <i class="bi bi-chat-dots me-2"></i>

                            Contáctanos

                        </a>

                    </div>

                </div>


                <!-- IMAGEN -->

                <div class="col-lg-6">

                    <div class="about-image-card">

                        <?php if ($fotoPerfil): ?>

                            <img
                                src="<?= e($fotoPerfil); ?>"
                                alt="<?= e($nombreEmpresa); ?>"
                                class="about-company-image">

                        <?php else: ?>

                            <div class="about-image-placeholder">

                                <i class="bi bi-building"></i>

                                <span>
                                    <?= e($nombreEmpresa); ?>
                                </span>

                            </div>

                        <?php endif; ?>


                        <div class="about-image-badge">

                            <i class="bi bi-check-circle-fill"></i>

                            <div>

                                <strong>
                                    Atención profesional
                                </strong>

                                <small>
                                    Siempre listos para ayudarte
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ======================================================
         MISIÓN / VISIÓN / VALORES
    ======================================================= -->

    <section class="about-values-section">

        <div class="container">

            <div class="section-heading text-center">

                <span class="section-eyebrow">
                    Lo que nos representa
                </span>

                <h2 class="section-title">
                    Nuestra misión, visión y valores
                </h2>

                <p class="section-description">
                    Trabajamos con principios claros para ofrecer una
                    experiencia confiable y profesional a nuestros clientes.
                </p>

            </div>


            <div class="row g-4">

                <!-- MISIÓN -->

                <div class="col-lg-4">

                    <article class="about-value-card">

                        <div class="about-value-icon">

                            <i class="bi bi-bullseye"></i>

                        </div>

                        <span class="about-value-number">
                            01
                        </span>

                        <h3>
                            Misión
                        </h3>

                        <p>
                            Brindar soluciones integrales a nuestros clientes,
                            enfocándonos en la seguridad, calidad y productividad,
                            contribuyendo al desarrollo sostenible de nuestra
                            sociedad.
                        </p>

                    </article>

                </div>


                <!-- VISIÓN -->

                <div class="col-lg-4">

                    <article class="about-value-card">

                        <div class="about-value-icon">

                            <i class="bi bi-eye"></i>

                        </div>

                        <span class="about-value-number">
                            02
                        </span>

                        <h3>
                            Visión
                        </h3>

                        <p>
                            Ser reconocidos como una empresa líder en el mercado
                            por nuestros servicios especializados, generando
                            confianza y relaciones duraderas con nuestros clientes.
                        </p>

                    </article>

                </div>


                <!-- VALORES -->

                <div class="col-lg-4">

                    <article class="about-value-card">

                        <div class="about-value-icon">

                            <i class="bi bi-stars"></i>

                        </div>

                        <span class="about-value-number">
                            03
                        </span>

                        <h3>
                            Valores
                        </h3>

                        <ul class="about-values-list">

                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                Vocación de servicio
                            </li>

                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                Ética profesional
                            </li>

                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                Compromiso con el cliente
                            </li>

                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                Transparencia
                            </li>

                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                Trabajo en equipo
                            </li>

                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                Responsabilidad social
                            </li>

                        </ul>

                    </article>

                </div>

            </div>

        </div>

    </section>


    <!-- ======================================================
         ESTADÍSTICAS
    ======================================================= -->

    <section class="about-stats-section">

        <div class="container">

            <div class="row g-0">

                <div class="col-md-4">

                    <div class="about-stat">

                        <div class="about-stat-icon">

                            <i class="bi bi-buildings"></i>

                        </div>

                        <div>

                            <strong>
                                <?= number_format($totalPropiedades); ?>
                            </strong>

                            <span>
                                Propiedades
                            </span>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="about-stat">

                        <div class="about-stat-icon">

                            <i class="bi bi-person-check"></i>

                        </div>

                        <div>

                            <strong>
                                <?= count($asesores); ?>
                            </strong>

                            <span>
                                Asesores
                            </span>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="about-stat">

                        <div class="about-stat-icon">

                            <i class="bi bi-headset"></i>

                        </div>

                        <div>

                            <strong>
                                100%
                            </strong>

                            <span>
                                Atención personalizada
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ======================================================
         CTA
    ======================================================= -->

    <section class="cta-section">

        <div class="container">

            <div class="row align-items-center g-4">

                <div class="col-lg-8">

                    <span class="section-eyebrow light">
                        Estamos para ayudarte
                    </span>

                    <h2>

                        ¿Buscas tu próximo
                        <strong>hogar o inversión?</strong>

                    </h2>

                    <p>
                        Nuestro equipo está listo para ayudarte a encontrar
                        la propiedad que necesitas.
                    </p>

                </div>


                <div class="col-lg-4 text-lg-end">

                    <a
                        href="contacto.php"
                        class="btn btn-success btn-lg px-5">

                        <i class="bi bi-chat-dots me-2"></i>

                        Contáctanos

                    </a>

                </div>

            </div>

        </div>

    </section>
    <?php include 'otros/informacion.php'; ?>
    <!-- ======================================================
         FOOTER
    ======================================================= -->

    <?php include 'otros/footer.php'; ?>


    <!-- ======================================================
         BOOTSTRAP JS
    ======================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>
    <script>
        /*======================================================
         ANIMACIONES
    ======================================================= */

        document.addEventListener("DOMContentLoaded", function() {

            const elements = document.querySelectorAll(
                ".about-hero-section, .about-value-card, .about-stat, .schedule-card"
            );

            if (!("IntersectionObserver" in window)) {
                elements.forEach(function(element) {
                    element.classList.add("is-visible");
                });

                return;
            }

            const observer = new IntersectionObserver(
                function(entries, observer) {

                    entries.forEach(function(entry) {

                        if (entry.isIntersecting) {

                            entry.target.classList.add("is-visible");

                            observer.unobserve(entry.target);
                        }

                    });

                }, {
                    threshold: 0.12
                }
            );

            elements.forEach(function(element) {
                observer.observe(element);
            });

        });
    </script>
    <!-- ======================================================
         JQUERY
    ======================================================= -->

    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js">
    </script>
    <!-- ======================================================
         JS DEL CHAT
    ======================================================= -->

    <script src="js/chat.js"></script>

</body>

</html>