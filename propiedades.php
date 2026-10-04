<?php
//======================================================
// CoDevPro Technology
// Archivo: propiedades.php
// Módulo: Propiedades
// Sistema: Inmobiliario
//======================================================

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
        content="Propiedades disponibles de <?= e($nombreEmpresa) ?>">

    <meta
        name="theme-color"
        content="#198754">

    <title>
        Propiedades - <?= e($nombreEmpresa) ?>
    </title>


    <!--==================================================
      FAVICON
    ==================================================-->
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
    <!--==================================================
      BOOTSTRAP
    ==================================================-->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!--==================================================
      FONT AWESOME
    ==================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <!--==================================================
      BOOTSTRAP ICONS
    ==================================================-->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">


    <!--==================================================
      CSS GENERAL
    ==================================================-->

    <link
        rel="stylesheet"
        href="css/style.css">


    <!--==================================================
      CSS PROPIEDADES
    ==================================================-->

    <link
        rel="stylesheet"
        href="css/producto.css">


    <!--==================================================
      JQUERY
    ==================================================-->

    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js">
    </script>

</head>


<body>


    <!--======================================================
  BARRA SUPERIOR
=======================================================-->

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
                            class="nav-link "
                            aria-current="page"
                            href="nosotros.php">

                            Conócenos

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link active"
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


    <!--======================================================
  ENCABEZADO
=======================================================-->

    <header class="property-header">

        <div class="container">

            <div class="row align-items-center g-3">


                <!-- TÍTULO -->

                <div class="col-12 col-md-7">

                    <div class="property-header-content">

                        <span class="property-header-label">

                            <i class="bi bi-buildings"></i>

                            Encuentra tu próximo hogar

                        </span>


                        <h1 class="fw-bold mb-2">

                            Propiedades

                        </h1>


                        <p class="mb-0">

                            Descubre nuestras propiedades disponibles
                            y encuentra el espacio ideal para ti.

                        </p>

                    </div>

                </div>


                <!-- BREADCRUMB -->

                <div class="col-12 col-md-5">

                    <nav
                        aria-label="breadcrumb">

                        <ol
                            class="breadcrumb justify-content-md-end mb-0">

                            <li class="breadcrumb-item">

                                <a
                                    href="index.php"
                                    class="text-white text-decoration-none">

                                    <i class="bi bi-house-door me-1"></i>

                                    Inicio

                                </a>

                            </li>


                            <li
                                class="breadcrumb-item active text-white"
                                aria-current="page">

                                Propiedades

                            </li>

                        </ol>

                    </nav>

                </div>

            </div>

        </div>

    </header>


    <!--======================================================
  CONTENIDO PRINCIPAL
=======================================================-->

    <main>


        <!--==================================================
      CABECERA Y BUSCADOR
    ==================================================-->

        <div class="container py-4">


            <!--================================================
          CABECERA DE RESULTADOS
        =================================================-->

            <div class="row align-items-center mb-3">


                <div class="col-12 col-lg-7">

                    <div>

                        <h2 class="fw-bold mb-1">

                            Propiedades disponibles

                        </h2>


                        <p class="text-muted mb-0">

                            Explora nuestras opciones y solicita
                            información.

                        </p>

                    </div>

                </div>


                <!-- CONTADOR -->

                <div class="col-12 col-lg-5 mt-3 mt-lg-0">

                    <div class="property-count-box">

                        <div class="property-count-icon">

                            <i class="bi bi-house-check"></i>

                        </div>


                        <div>

                            <small class="text-muted d-block">

                                Propiedades disponibles

                            </small>


                            <strong class="fs-5">

                                <?= number_format(
                                    $totalPropiedades
                                ) ?>

                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!--================================================
  BUSCADOR Y FILTROS
================================================-->

            <div class="search-property-card mb-4">

                <form id="formFiltrosPropiedades">

                    <!--============================================
          BUSCADOR PRINCIPAL
        =============================================-->

                    <div class="row g-3">

                        <div class="col-12">

                            <label
                                for="inputBuscar"
                                class="form-label fw-semibold">

                                <i class="bi bi-search me-1 text-success"></i>

                                Buscar propiedad

                            </label>

                            <div class="position-relative">

                                <i
                                    class="bi bi-search search-icon"
                                    aria-hidden="true">
                                </i>

                                <input
                                    id="inputBuscar"
                                    name="buscar"
                                    class="form-control property-search-input"
                                    type="search"
                                    autocomplete="off"
                                    placeholder="Código, nombre o ubicación..."
                                    aria-label="Buscar propiedades">

                                <button
                                    type="button"
                                    id="btnLimpiarBusqueda"
                                    class="btn btn-sm btn-light search-clear-button"
                                    title="Limpiar búsqueda"
                                    aria-label="Limpiar búsqueda">

                                    <i class="bi bi-x-lg"></i>

                                </button>

                            </div>

                        </div>


                        <!--============================================
              CATEGORÍA
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="filtroCategoria"
                                class="form-label small fw-semibold">

                                <i class="bi bi-tag me-1 text-success"></i>

                                Categoría

                            </label>

                            <select
                                id="filtroCategoria"
                                name="categoria"
                                class="form-select">

                                <option value="">
                                    Todas las categorías
                                </option>

                                <?php

                                $sqlCategorias = "
                        SELECT
                            id_categoria,
                            nombre
                        FROM categoria
                        WHERE
                            COALESCE(Eliminado, 0) = 0
                        ORDER BY nombre ASC
                    ";

                                $resultadoCategorias =
                                    $conexion->query(
                                        $sqlCategorias
                                    );

                                if (
                                    $resultadoCategorias &&
                                    $resultadoCategorias->num_rows > 0
                                ):

                                    while (
                                        $categoriaFiltro =
                                        $resultadoCategorias->fetch_assoc()
                                    ):

                                ?>

                                        <option
                                            value="<?= (int)$categoriaFiltro['id_categoria'] ?>">

                                            <?= e(
                                                $categoriaFiltro['nombre']
                                            ) ?>

                                        </option>

                                <?php

                                    endwhile;

                                    $resultadoCategorias->free();

                                endif;

                                ?>

                            </select>

                        </div>


                        <!--============================================
              PRECIO MÍNIMO
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="precioMin"
                                class="form-label small fw-semibold">

                                <i class="bi bi-currency-dollar me-1 text-success"></i>

                                Precio mínimo

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    S/.
                                </span>

                                <input
                                    type="number"
                                    id="precioMin"
                                    name="precio_min"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    placeholder="0">

                            </div>

                        </div>


                        <!--============================================
              PRECIO MÁXIMO
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="precioMax"
                                class="form-label small fw-semibold">

                                <i class="bi bi-cash-stack me-1 text-success"></i>

                                Precio máximo

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    S/.
                                </span>

                                <input
                                    type="number"
                                    id="precioMax"
                                    name="precio_max"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    placeholder="Sin límite">

                            </div>

                        </div>


                        <!--============================================
              ÁREA MÍNIMA
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="areaMin"
                                class="form-label small fw-semibold">

                                <i class="bi bi-arrows-fullscreen me-1 text-success"></i>

                                Área mínima

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    id="areaMin"
                                    name="area_min"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    placeholder="0">

                                <span class="input-group-text">
                                    m²
                                </span>

                            </div>

                        </div>


                        <!--============================================
              ÁREA MÁXIMA
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="areaMax"
                                class="form-label small fw-semibold">

                                <i class="bi bi-bounding-box me-1 text-success"></i>

                                Área máxima

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    id="areaMax"
                                    name="area_max"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    placeholder="Sin límite">

                                <span class="input-group-text">
                                    m²
                                </span>

                            </div>

                        </div>


                        <!--============================================
              FECHA DESDE
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="fechaDesde"
                                class="form-label small fw-semibold">

                                <i class="bi bi-calendar-event me-1 text-success"></i>

                                Registrada desde

                            </label>

                            <input
                                type="date"
                                id="fechaDesde"
                                name="fecha_desde"
                                class="form-control">

                        </div>


                        <!--============================================
              ORDEN
            =============================================-->

                        <div class="col-12 col-md-6 col-lg-3">

                            <label
                                for="orden"
                                class="form-label small fw-semibold">

                                <i class="bi bi-sort-down me-1 text-success"></i>

                                Ordenar por

                            </label>

                            <select
                                id="orden"
                                name="orden"
                                class="form-select">

                                <option value="recientes">
                                    Más recientes
                                </option>

                                <option value="antiguos">
                                    Más antiguos
                                </option>

                                <option value="precio_asc">
                                    Precio: menor a mayor
                                </option>

                                <option value="precio_desc">
                                    Precio: mayor a menor
                                </option>

                                <option value="area_asc">
                                    Área: menor a mayor
                                </option>

                                <option value="area_desc">
                                    Área: mayor a menor
                                </option>

                                <option value="nombre">
                                    Nombre A-Z
                                </option>

                            </select>

                        </div>


                        <!--============================================
              BOTONES
            =============================================-->

                        <div class="col-12">

                            <div class="d-flex flex-wrap justify-content-end gap-2 pt-2">

                                <button
                                    type="button"
                                    id="btnLimpiarFiltros"
                                    class="btn btn-outline-secondary">

                                    <i class="bi bi-arrow-counterclockwise me-1"></i>

                                    Limpiar filtros

                                </button>

                            </div>

                        </div>

                    </div>

                </form>


                <!--============================================
      ESTADO DE BÚSQUEDA
    =============================================-->

                <div
                    id="loadingBusqueda"
                    class="search-loading d-none">

                    <div
                        class="spinner-border spinner-border-sm text-success me-2"
                        role="status">

                    </div>

                    Buscando propiedades...

                </div>

            </div>


            <!--================================================
  CONTADOR RESULTADOS
================================================-->

            <div
                class="d-flex flex-wrap justify-content-between align-items-center mb-3">

                <p
                    id="contadorResultados"
                    class="text-muted mb-0">

                    Resultado:

                    <strong>
                        <?= number_format($totalPropiedades) ?>
                    </strong>

                    en la zona.

                </p>


                <span
                    id="textoResultados"
                    class="small text-muted">

                    Mostrando
                    <?= number_format($totalPropiedades) ?>
                    propiedades

                </span>

            </div>

            <!--==================================================
  RESULTADO DE PROPIEDADES
==================================================-->

            <div
                id="resultadoPropiedades"
                class="container position-relative ">

                <div class="row g-4">

                    <div class="col-12">

                        <div class="property-initial-loading">

                            <div
                                class="spinner-border text-success"
                                role="status">

                            </div>

                            <p class="mt-3 mb-0 text-muted">

                                Cargando propiedades...

                            </p>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </main>
    <?php include 'modal/modal_detalle_propiedad.php'; ?>

    <!--======================================================
  FOOTER
=======================================================-->

    <?php include 'otros/footer.php'; ?>


    <!--======================================================
  BOOTSTRAP JS
=======================================================-->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>


    <!--======================================================
  BUSCADOR DE PROPIEDADES
=======================================================-->

    <script
        src="js/buscar_propiedades.js">
    </script>
    <script
        src="js/detalle_propiedad.js">
    </script>

    <!--======================================================
  NAVBAR + ANIMACIONES + CHAT
=======================================================-->
    <script
        src="js/script.js">
    </script>
    <!--======================================================
  SCRIPT GENERAL
=======================================================-->
    <script
        src="js/chat.js">
    </script>


</body>

</html>