<?php

/**
 * ==========================================================
 * INDEX.PHP
 * Sistema inmobiliario
 * ==========================================================
 */

require_once "controladores/conect_db.php";

/* ==========================================================
   FUNCIONES AUXILIARES
   ========================================================== */

function e($valor)
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function telefonoWhatsapp($telefono)
{
    $telefono = preg_replace('/\D+/', '', (string)$telefono);

    if ($telefono === '') {
        return '';
    }

    // Si ya contiene 51, no lo duplicamos
    if (strpos($telefono, '51') === 0 && strlen($telefono) >= 11) {
        return $telefono;
    }

    return '51' . $telefono;
}

/* ==========================================================
   DATOS DE LA EMPRESA
   ========================================================== */

$usuario = [
    'id_user'           => 0,
    'nombreEmpresa'     => 'Nuestra Empresa',
    'ruc'               => '',
    'fecha_registro'    => '',
    'imagen'            => null,
    'direccion'         => '',
    'email'             => '',
    'celular'           => '',
    'estado'            => '',
    'descripcion_acerca' => '',
    'video'             => ''
];

$sqlUsuario = "
    SELECT id_user,
        nombreEmpresa,
        ruc,
        fecha_registro,
        imagen,
        direccion,
        email,
        celular,
        estado,
        descripcion_acerca,
        video
    FROM usuario_acceso
    LIMIT 1
";

$stmtUsuario = $conexion->prepare($sqlUsuario);

if ($stmtUsuario) {
    $stmtUsuario->execute();
    $resultadoUsuario = $stmtUsuario->get_result();

    if ($filaUsuario = $resultadoUsuario->fetch_assoc()) {
        $usuario = array_merge($usuario, $filaUsuario);
    }

    $stmtUsuario->close();
}
/* ==========================================================
   VIDEO / IMAGEN DE PRESENTACIÓN DE LA EMPRESA
   ========================================================== */

$imagenPresentacion = '';

/*
 * Primero intentamos obtener la primera imagen
 * de las propiedades pertenecientes a esta empresa.
 *
 * Se utiliza:
 *   orden ASC
 *   id_imagen ASC
 *
 * para obtener realmente la primera imagen registrada.
 */

$idUserEmpresa = (int)($usuario['id_user'] ?? 0);

if ($idUserEmpresa > 0) {

    $sqlImagenPresentacion = "
        SELECT
            i.imagenes
        FROM imagenes i
        INNER JOIN propiedades p
            ON p.id_propiedad = i.id_propiedad
        WHERE p.id_user = ?
          AND (p.Eliminado IS NULL OR p.Eliminado = 0)
          AND i.imagenes IS NOT NULL
          AND i.imagenes <> ''
        ORDER BY
            CASE
                WHEN i.orden IS NULL THEN 999
                ELSE i.orden
            END ASC,
            i.id_imagen ASC
        LIMIT 1
    ";

    $stmtImagenPresentacion = mysqli_prepare(
        $conexion,
        $sqlImagenPresentacion
    );

    if ($stmtImagenPresentacion) {

        mysqli_stmt_bind_param(
            $stmtImagenPresentacion,
            'i',
            $idUserEmpresa
        );

        mysqli_stmt_execute(
            $stmtImagenPresentacion
        );

        $resultadoImagenPresentacion =
            mysqli_stmt_get_result(
                $stmtImagenPresentacion
            );

        $filaImagenPresentacion =
            mysqli_fetch_assoc(
                $resultadoImagenPresentacion
            );

        mysqli_stmt_close(
            $stmtImagenPresentacion
        );

        /*
         * Convertir BLOB a Data URI
         */
        if (
            $filaImagenPresentacion &&
            !empty($filaImagenPresentacion['imagenes'])
        ) {

            $imagenBlob =
                $filaImagenPresentacion['imagenes'];

            $finfo =
                new finfo(FILEINFO_MIME_TYPE);

            $mime =
                $finfo->buffer($imagenBlob);

            $tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif'
            ];

            if (
                in_array(
                    $mime,
                    $tiposPermitidos,
                    true
                )
            ) {

                $imagenPresentacion =
                    'data:' .
                    $mime .
                    ';base64,' .
                    base64_encode($imagenBlob);
            }
        }
    }
}


/*
 * Si no existe ninguna imagen de propiedad,
 * utilizar una imagen de respaldo.
 */

if ($imagenPresentacion === '') {

    $imagenPresentacion = 'img/carrusel2.jpg';
}
/* ==========================================================
   RUTA DEL VIDEO DE PRESENTACIÓN
   ========================================================== */

$videoPresentacion = '';

if (!empty($usuario['video'])) {

    $videoNombre = trim((string)$usuario['video']);

    // Normalizar separadores
    $videoNombre = str_replace('\\', '/', $videoNombre);

    // Si la BD guarda la ruta completa, quedarnos solamente
    // con el nombre del archivo.
    $videoNombre = basename($videoNombre);

    if ($videoNombre !== '') {
        $videoPresentacion = 'uploads/videos_empresa/' . $videoNombre;
    }
}
/* ==========================================================
   FOTO DE PERFIL / LOGO
   ========================================================== */

$fotoPerfil = '';

if (!empty($usuario['imagen'])) {
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($usuario['imagen']);
}

/* ==========================================================
   ASESores
   ========================================================== */

$asesores = [];

$sqlAsesores = "
    SELECT
        id_asesor,
        nombre,
        apellidos,
        celular
    FROM asesores
    ORDER BY nombre ASC, apellidos ASC
";

$resultAsesores = $conexion->query($sqlAsesores);

if ($resultAsesores && $resultAsesores->num_rows > 0) {

    while ($row = $resultAsesores->fetch_assoc()) {
        $asesores[] = $row;
    }

    $resultAsesores->free();
}

/* ==========================================================
   CONTADOR DE PROPIEDADES NO ELIMINADAS
========================================================== */

$totalPropiedades = 0;

$sqlTotalPropiedades = "
    SELECT COUNT(*) AS total
    FROM propiedades
    WHERE Eliminado IS NULL
       OR Eliminado = 0
";

$resultTotal = $conexion->query($sqlTotalPropiedades);

if ($resultTotal) {

    $filaTotal = $resultTotal->fetch_assoc();

    $totalPropiedades = (int)($filaTotal['total'] ?? 0);

    $resultTotal->free();
}

/* ==========================================================
   PROPIEDADES DESTACADAS
   ========================================================== */

/*
 * IMPORTANTE:
 * La tabla propiedades NO tiene una columna imagen.
 * Las imágenes están en la tabla imagenes.
 *
 * Por eso obtenemos la primera imagen de cada propiedad
 * mediante una subconsulta.
 */

$propiedades = [];

$sqlPropiedades = "
    SELECT
        p.id_propiedad,
        p.id_user,
        p.nombre,
        p.codigo,
        p.id_categoria,
        p.tamano_area_metros,
        p.precio,
        p.precio_anterior,
        p.ubicacion,
        p.fecha_registro,
        p.fecha_actualizacion,

        c.nombre AS nombre_categoria,

        (
            SELECT i.imagenes
            FROM imagenes i
            WHERE i.id_propiedad = p.id_propiedad
            ORDER BY
                CASE
                    WHEN i.orden IS NULL THEN 999
                    ELSE i.orden
                END ASC,
                i.id_imagen ASC
            LIMIT 1
        ) AS imagen_principal

    FROM propiedades p

LEFT JOIN categoria c
    ON c.id_categoria = p.id_categoria

WHERE p.Eliminado IS NULL
   OR p.Eliminado = 0

ORDER BY p.id_propiedad DESC

LIMIT 8
";

$resultPropiedades = $conexion->query($sqlPropiedades);

if ($resultPropiedades && $resultPropiedades->num_rows > 0) {

    while ($fila = $resultPropiedades->fetch_assoc()) {
        $propiedades[] = $fila;
    }

    $resultPropiedades->free();
}

/* ==========================================================
   LISTA DE PROPIEDADES PARA EL FORMULARIO
   ========================================================== */

$listaPropiedades = [];

$sqlListaPropiedades = "
    SELECT
        id_propiedad,
        nombre,
        codigo
    FROM propiedades
    WHERE Eliminado IS NULL
       OR Eliminado = 0
    ORDER BY nombre ASC
";

$resultLista = $conexion->query($sqlListaPropiedades);

if ($resultLista && $resultLista->num_rows > 0) {

    while ($fila = $resultLista->fetch_assoc()) {
        $listaPropiedades[] = $fila;
    }

    $resultLista->free();
}
/* ==========================================================
   PRECIO MÍNIMO DE LAS PROPIEDADES
   ========================================================== */

$precioMinimo = 0;

$sqlPrecioMinimo = "
    SELECT MIN(precio) AS precio_minimo
    FROM propiedades
    WHERE precio IS NOT NULL
      AND precio > 0
      AND (Eliminado IS NULL OR Eliminado = 0)
";

$resultPrecioMinimo = $conexion->query($sqlPrecioMinimo);

if ($resultPrecioMinimo) {

    $filaPrecioMinimo = $resultPrecioMinimo->fetch_assoc();

    $precioMinimo = (float)($filaPrecioMinimo['precio_minimo'] ?? 0);

    $resultPrecioMinimo->free();
}
/* ==========================================================
   METRAJE MÍNIMO DE LAS PROPIEDADES
   ========================================================== */

$areaMinima = 0;

$sqlAreaMinima = "
    SELECT MIN(tamano_area_metros) AS area_minima
    FROM propiedades
    WHERE tamano_area_metros IS NOT NULL
      AND tamano_area_metros > 0
      AND (Eliminado IS NULL OR Eliminado = 0)
";

$resultAreaMinima = $conexion->query($sqlAreaMinima);

if ($resultAreaMinima) {

    $filaAreaMinima = $resultAreaMinima->fetch_assoc();

    $areaMinima = (float)($filaAreaMinima['area_minima'] ?? 0);

    $resultAreaMinima->free();
}
/* ==========================================================
    IMÁGENES PARA ÁREAS COMUNES
    Obtiene imágenes de propiedades con orden = 1
    ========================================================== */

$imagenesAreas = [];

$sqlImagenesAreas = "
    SELECT
        i.id_imagen,
        i.imagenes,
        i.id_propiedad,
        p.nombre AS nombre_propiedad
    FROM imagenes i
    INNER JOIN propiedades p
        ON p.id_propiedad = i.id_propiedad
    WHERE i.orden = 1
      AND (p.Eliminado IS NULL OR p.Eliminado = 0)
    ORDER BY i.id_imagen DESC
    LIMIT 6
";

$resultImagenesAreas = $conexion->query($sqlImagenesAreas);

if ($resultImagenesAreas && $resultImagenesAreas->num_rows > 0) {

    while ($filaImagen = $resultImagenesAreas->fetch_assoc()) {
        $imagenesAreas[] = $filaImagen;
    }

    $resultImagenesAreas->free();
}
/* ==========================================================
   WHATSAPP DE LA EMPRESA
   ========================================================== */

$whatsappEmpresa = telefonoWhatsapp($usuario['celular']);

/* ==========================================================
   TÍTULO
   ========================================================== */

$nombreEmpresa = trim($usuario['nombreEmpresa']);

if ($nombreEmpresa === '') {
    $nombreEmpresa = 'Inmobiliaria';
}
/* ==========================================================
   TESTIMONIOS
   Obtiene los últimos 4 testimonios registrados
   ========================================================== */

$testimonios = [];

$sqlTestimonios = "
    SELECT
        id_testimonio,
        nombre,
        apellidos,
        comentario,
        fecha_registro,
        fecha_actualizado,
        imagen,
        video
    FROM testimonios
    WHERE id_user = (
        SELECT id_user
        FROM usuario_acceso
        LIMIT 1
    )
    ORDER BY
        COALESCE(fecha_actualizado, fecha_registro) DESC,
        id_testimonio DESC
    LIMIT 4
";

$resultTestimonios = $conexion->query($sqlTestimonios);

if ($resultTestimonios && $resultTestimonios->num_rows > 0) {

    while ($filaTestimonio = $resultTestimonios->fetch_assoc()) {
        $testimonios[] = $filaTestimonio;
    }

    $resultTestimonios->free();
}
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
        content="<?= e($usuario['descripcion_acerca'] ?: 'Venta de terrenos y propiedades inmobiliarias en Iquitos.') ?>">

    <meta
        name="theme-color"
        content="#198754">

    <title>
        Inicio - <?= e($nombreEmpresa) ?>
    </title>

    <!-- =====================================================
         FAVICON
    ====================================================== -->

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
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- =====================================================
         CSS DEL PROYECTO
    ====================================================== -->

    <link
        rel="stylesheet"
        href="css/style.css">
    <!--==================================================
      CSS PROPIEDADES
    ==================================================-->

    <link
        rel="stylesheet"
        href="css/producto.css">
</head>

<body>

    <!-- =========================================================
     TOPBAR
========================================================== -->

    <div class="topbar">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-7 text-center text-lg-start">

                    <?php if (!empty($usuario['celular'])): ?>

                        <a
                            href="tel:<?= e($usuario['celular']) ?>"
                            class="topbar-link me-3">

                            <i class="fa-solid fa-phone me-1"></i>

                            +51 <?= e($usuario['celular']) ?>

                        </a>

                    <?php endif; ?>


                    <?php if (!empty($usuario['email'])): ?>

                        <a
                            href="mailto:<?= e($usuario['email']) ?>"
                            class="topbar-link">

                            <i class="fa-solid fa-envelope me-1"></i>

                            <?= e($usuario['email']) ?>

                        </a>

                    <?php endif; ?>

                </div>


                <div class="col-lg-5 text-center text-lg-end">

                    <a
                        href="#"
                        class="topbar-social"
                        aria-label="Facebook">

                        <i class="fab fa-facebook-f"></i>

                    </a>

                    <a
                        href="#"
                        class="topbar-social"
                        aria-label="Instagram">

                        <i class="fab fa-instagram"></i>

                    </a>

                    <a
                        href="#"
                        class="topbar-social"
                        aria-label="TikTok">

                        <i class="fab fa-tiktok"></i>

                    </a>

                    <a
                        href="#"
                        class="topbar-social"
                        aria-label="WhatsApp">

                        <i class="fab fa-whatsapp"></i>

                    </a>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
     NAVBAR
========================================================== -->

    <nav class="navbar navbar-expand-lg main-navbar sticky-top">

        <div class="container">

            <!-- LOGO -->

            <a
                href="index.php"
                class="navbar-brand d-flex align-items-center">

                <?php if ($fotoPerfil): ?>

                    <img
                        src="<?= $fotoPerfil ?>"
                        class="company-logo"
                        alt="<?= e($nombreEmpresa) ?>">

                <?php else: ?>

                    <span class="company-logo-placeholder">

                        <i class="fa-solid fa-building"></i>

                    </span>

                <?php endif; ?>


                <span class="company-name">

                    <?= e($nombreEmpresa) ?>

                </span>

            </a>


            <!-- BOTÓN MOBILE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarPrincipal"
                aria-controls="navbarPrincipal"
                aria-expanded="false"
                aria-label="Abrir menú">

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- MENÚ -->

            <div
                class="collapse navbar-collapse"
                id="navbarPrincipal">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">

                        <a
                            class="nav-link active"
                            href="index.php">

                            Inicio

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
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


                <div class="ms-lg-3 mt-3 mt-lg-0">

                    <a
                        href="login.php"
                        class="btn btn-success px-4">

                        <i class="bi bi-person-circle me-1"></i>

                        Login

                    </a>

                </div>

            </div>

        </div>

    </nav>


    <!-- =========================================================
     HERO
========================================================== -->

    <section class="hero-section">

        <div
            id="heroCarousel"
            class="carousel slide carousel-fade"
            data-bs-ride="carousel"
            data-bs-interval="6000">

            <!-- INDICADORES -->

            <div class="carousel-indicators">

                <button
                    type="button"
                    data-bs-target="#heroCarousel"
                    data-bs-slide-to="0"
                    class="active"
                    aria-current="true"
                    aria-label="Slide 1">
                </button>

                <button
                    type="button"
                    data-bs-target="#heroCarousel"
                    data-bs-slide-to="1"
                    aria-label="Slide 2">
                </button>

            </div>


            <div class="carousel-inner">

                <!-- SLIDE 1 -->

                <div class="carousel-item active">

                    <img
                        src="img/carrusel2.jpg"
                        class="hero-image"
                        alt="Terrenos y proyectos inmobiliarios"
                        loading="eager">

                    <div class="hero-overlay"></div>


                    <div class="hero-caption">

                        <div class="container">

                            <div class="hero-content">

                                <span class="hero-label">

                                    INVERSIONES INMOBILIARIAS

                                </span>


                                <h1>

                                    Encuentra el lugar ideal
                                    para construir tu futuro

                                </h1>


                                <p>

                                    Descubre terrenos estratégicamente ubicados
                                    para vivienda e inversión en Iquitos.

                                </p>


                                <div class="hero-buttons">

                                    <a
                                        href="propiedades.php"
                                        class="btn btn-success btn-lg px-4">

                                        <i class="bi bi-search me-2"></i>

                                        Ver propiedades

                                    </a>


                                    <a
                                        href="asesores.php"
                                        class="btn btn-outline-light btn-lg px-4">

                                        <i class="bi bi-people me-2"></i>

                                        Hablar con un asesor

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- SLIDE 2 -->

                <div class="carousel-item">

                    <img
                        src="img/carrusel4.avif"
                        class="hero-image"
                        alt="Proyectos inmobiliarios"
                        loading="lazy">

                    <div class="hero-overlay"></div>


                    <div class="hero-caption">

                        <div class="container">

                            <div class="hero-content">

                                <span class="hero-label">

                                    PROYECTOS INMOBILIARIOS

                                </span>


                                <h2>

                                    Invierte hoy en zonas
                                    con potencial de crecimiento

                                </h2>


                                <p>

                                    Te acompañamos durante todo el proceso
                                    de compra con atención personalizada.

                                </p>


                                <div class="hero-buttons">

                                    <a
                                        href="#contacto"
                                        class="btn btn-success btn-lg px-4">

                                        Solicitar información

                                    </a>


                                    <a
                                        href="propiedades.php"
                                        class="btn btn-outline-light btn-lg px-4">

                                        Explorar terrenos

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- CONTROLES -->

            <button
                class="carousel-control-prev"
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide="prev">

                <span
                    class="carousel-control-prev-icon">
                </span>

                <span class="visually-hidden">
                    Anterior
                </span>

            </button>


            <button
                class="carousel-control-next"
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide="next">

                <span
                    class="carousel-control-next-icon">
                </span>

                <span class="visually-hidden">
                    Siguiente
                </span>

            </button>

        </div>

    </section>


    <!-- =========================================================
     PRESENTACIÓN
========================================================== -->

    <section class="presentation-section">

        <div class="container">

            <div class="row align-items-center g-5">

                <!-- VIDEO -->

                <div class="col-lg-6">

                    <div class="video-wrapper">

                        <?php if ($videoPresentacion !== ''): ?>

                            <video
                                controls
                                playsinline
                                preload="metadata"
                                class="company-video">

                                <source
                                    src="<?= e($videoPresentacion) ?>"
                                    type="video/mp4">

                                Tu navegador no soporta videos HTML5.

                            </video>

                        <?php else: ?>

                            <!-- =========================================
                             NO EXISTE VIDEO
                             MOSTRAR PRIMERA IMAGEN DE PROPIEDADES
                        ========================================== -->

                            <img
                                src="<?= e($imagenPresentacion) ?>"
                                alt="<?= e($nombreEmpresa) ?>"
                                class="company-video presentation-image"
                                loading="lazy">

                        <?php endif; ?>

                    </div>

                </div>


                <!-- TEXTO -->

                <div class="col-lg-6">

                    <span class="section-eyebrow">

                        SOBRE NOSOTROS

                    </span>


                    <h2 class="section-title">

                        Venta de terrenos en Iquitos
                        para inversión y vivienda

                    </h2>


                    <p class="section-description">

                        Encuentra terrenos y proyectos inmobiliarios
                        pensados para quienes buscan construir su hogar
                        o realizar una inversión con visión de futuro.

                    </p>


                    <!-- TABS -->

                    <ul
                        class="nav custom-tabs"
                        id="infoTabs"
                        role="tablist">

                        <li
                            class="nav-item"
                            role="presentation">

                            <button
                                class="nav-link active"
                                id="terrenos-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#terrenos"
                                type="button"
                                role="tab">

                                Terrenos en venta

                            </button>

                        </li>


                        <li
                            class="nav-item"
                            role="presentation">

                            <button
                                class="nav-link"
                                id="proyectos-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#proyectos"
                                type="button"
                                role="tab">

                                Proyectos inmobiliarios

                            </button>

                        </li>

                    </ul>


                    <div
                        class="tab-content mt-4">

                        <!-- TERRENOS -->

                        <div
                            class="tab-pane fade show active"
                            id="terrenos"
                            role="tabpanel">

                            <div class="feature-list">

                                <div class="feature-item">

                                    <i class="bi bi-file-earmark-check"></i>

                                    <span>
                                        Terrenos titulados e inscritos
                                        en SUNARP.
                                    </span>

                                </div>


                                <div class="feature-item">

                                    <i class="bi bi-geo-alt"></i>

                                    <span>
                                        Ubicaciones estratégicas en
                                        zonas de crecimiento.
                                    </span>

                                </div>


                                <div class="feature-item">

                                    <i class="bi bi-cash-stack"></i>

                                    <span>
                                        Precios accesibles y alternativas
                                        de pago.
                                    </span>

                                </div>


                                <div class="feature-item">

                                    <i class="bi bi-shield-check"></i>

                                    <span>
                                        Proceso de compra seguro y transparente.
                                    </span>

                                </div>


                                <div class="feature-item">

                                    <i class="bi bi-person-check"></i>

                                    <span>
                                        Asesoría personalizada durante
                                        todo el proceso.
                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- PROYECTOS -->

                        <div
                            class="tab-pane fade"
                            id="proyectos"
                            role="tabpanel">

                            <div class="feature-list">

                                <div class="feature-item">

                                    <i class="bi bi-house-check"></i>

                                    <span>
                                        Proyectos orientados a vivienda.
                                    </span>

                                </div>


                                <div class="feature-item">

                                    <i class="bi bi-graph-up-arrow"></i>

                                    <span>
                                        Alternativas con potencial
                                        de valorización.
                                    </span>

                                </div>


                                <div class="feature-item">

                                    <i class="bi bi-map"></i>

                                    <span>
                                        Lotes delimitados y organizados.
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="mt-4">

                        <a
                            href="nosotros.php"
                            class="btn btn-outline-success px-4">

                            Conoce más sobre nosotros

                            <i class="bi bi-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
     PROPIEDADES DESTACADAS
========================================================== -->

    <section
        id="resultadoPropiedades"
        class="properties-section">

        <div class="container">

            <div class="section-heading text-center">

                <span class="section-eyebrow">

                    NUESTRO CATÁLOGO

                </span>


                <h2 class="section-title">

                    Propiedades destacadas

                </h2>


                <p class="section-description">

                    Explora algunas de nuestras propiedades disponibles
                    y encuentra la alternativa que mejor se adapte
                    a tus necesidades.

                </p>

            </div>


            <div class="row g-4">

                <?php if (!empty($propiedades)): ?>

                    <?php foreach ($propiedades as $fila): ?>

                        <?php

                        $imagenPropiedad = '';

                        if (!empty($fila['imagen_principal'])) {

                            $imagenPropiedad =
                                'data:image/jpeg;base64,' .
                                base64_encode($fila['imagen_principal']);
                        }

                        $precio = (float)($fila['precio'] ?? 0);

                        $area = (float)($fila['tamano_area_metros'] ?? 0);

                        $mensajeWhatsapp = urlencode(
                            "Hola, me gustaría recibir información sobre esta propiedad.\n\n" .
                                "Código: " . ($fila['codigo'] ?? '') . "\n" .
                                "Nombre: " . ($fila['nombre'] ?? '') . "\n" .
                                "Ubicación: " . ($fila['ubicacion'] ?? '') . "\n" .
                                "Precio: S/. " . number_format($precio, 0) . "\n\n" .
                                "Gracias."
                        );

                        ?>

                        <div class="col-sm-6 col-lg-3">

                            <article class="property-card">

                                <!-- IMAGEN -->

                                <div class="property-image-wrapper">

                                    <?php if ($imagenPropiedad): ?>

                                        <img
                                            src="<?= $imagenPropiedad ?>"
                                            class="property-image"
                                            alt="<?= e($fila['nombre']) ?>"
                                            loading="lazy">

                                    <?php else: ?>

                                        <img
                                            src="img/producto.png"
                                            class="property-image"
                                            alt="Propiedad sin imagen"
                                            loading="lazy">

                                    <?php endif; ?>


                                    <!-- PRECIO -->

                                    <span class="property-price">

                                        Desde
                                        S/. <?= number_format($precio, 0) ?>

                                    </span>


                                    <!-- CÓDIGO -->

                                    <span class="property-code">

                                        Código:
                                        <?= e($fila['codigo']) ?>

                                    </span>

                                </div>


                                <!-- CONTENIDO -->

                                <div class="property-body">

                                    <span class="property-category">

                                        <?= e(
                                            $fila['nombre_categoria']
                                                ?: 'Propiedad'
                                        ) ?>

                                    </span>


                                    <h3 class="property-title">

                                        <?= e($fila['nombre']) ?>

                                    </h3>


                                    <div class="property-location">

                                        <i class="bi bi-geo-alt-fill"></i>

                                        <span>
                                            <?= e($fila['ubicacion']) ?>
                                        </span>

                                    </div>


                                    <div class="property-data">

                                        <span>

                                            <i class="bi bi-arrows-fullscreen"></i>

                                            <?= number_format($area, 0) ?>
                                            m²

                                        </span>


                                        <span>

                                            <i class="bi bi-tag-fill"></i>

                                            <?= e(
                                                $fila['nombre_categoria']
                                                    ?: 'Terreno'
                                            ) ?>

                                        </span>

                                    </div>


                                    <div class="property-actions">

                                        <!-- VER DETALLE -->

                                        <button
                                            type="button"
                                            class="btn btn-outline-success w-100 btnVerDetalle"
                                            data-id="<?= (int)$fila['id_propiedad'] ?>"
                                            aria-label="Ver detalle de <?= (int)$fila['nombre'] ?>">

                                            <i class="bi bi-eye me-1"></i>

                                            Ver detalle

                                        </button>

                                        <?php if ($whatsappEmpresa): ?>

                                            <a
                                                href="https://wa.me/<?= e($whatsappEmpresa) ?>?text=<?= $mensajeWhatsapp ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-success"
                                                title="Consultar por WhatsApp">

                                                <i class="bi bi-whatsapp"></i>

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </article>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12">

                        <div class="empty-properties">

                            <i class="bi bi-house-exclamation"></i>

                            <h3>
                                No hay propiedades registradas
                            </h3>

                            <p class="text-muted mb-0">

                                Actualmente no contamos con propiedades
                                disponibles. Vuelve a visitarnos pronto.

                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>


            <?php if (!empty($propiedades)): ?>

                <div class="text-center mt-5">

                    <a
                        href="propiedades.php"
                        class="btn btn-success btn-lg px-5">

                        Ver todas las propiedades

                        <i class="bi bi-arrow-right ms-2"></i>

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =========================================================
     VENTAJAS
========================================================== -->

    <section class="advantages-section">

        <div class="advantages-overlay"></div>

        <div class="container position-relative">

            <div class="section-heading text-center">

                <span class="section-eyebrow light">

                    NUESTRA PROPUESTA

                </span>


                <h2 class="section-title text-white">

                    ¿Por qué elegirnos?

                </h2>


                <p class="text-white-50">

                    Trabajamos para que encuentres una propiedad
                    con información clara y acompañamiento profesional.

                </p>

            </div>


            <div class="row g-4">

                <!-- 1 -->

                <div class="col-md-6 col-lg-4">

                    <div class="advantage-card">

                        <div class="advantage-icon">

                            <i class="bi bi-file-earmark-check"></i>

                        </div>

                        <h3>
                            Venta de terrenos titulados
                        </h3>

                        <p>
                            Terrenos con documentación en regla
                            para realizar una compra segura.
                        </p>

                    </div>

                </div>


                <!-- 2 -->

                <div class="col-md-6 col-lg-4">

                    <div class="advantage-card">

                        <div class="advantage-icon">

                            <i class="bi bi-shield-check"></i>

                        </div>

                        <h3>
                            Compra segura
                        </h3>

                        <p>
                            Te orientamos durante el proceso para
                            que tomes decisiones informadas.
                        </p>

                    </div>

                </div>


                <!-- 3 -->

                <div class="col-md-6 col-lg-4">

                    <div class="advantage-card">

                        <div class="advantage-icon">

                            <i class="bi bi-graph-up-arrow"></i>

                        </div>

                        <h3>
                            Terrenos para inversión
                        </h3>

                        <p>
                            Alternativas ubicadas en zonas con
                            posibilidades de crecimiento.
                        </p>

                    </div>

                </div>


                <!-- 4 -->

                <div class="col-md-6 col-lg-4">

                    <div class="advantage-card">

                        <div class="advantage-icon">

                            <i class="bi bi-credit-card"></i>

                        </div>

                        <h3>
                            Planes de pago
                        </h3>

                        <p>
                            Alternativas de pago pensadas para
                            diferentes presupuestos.
                        </p>

                    </div>

                </div>


                <!-- 5 -->

                <div class="col-md-6 col-lg-4">

                    <div class="advantage-card">

                        <div class="advantage-icon">

                            <i class="bi bi-geo-alt"></i>

                        </div>

                        <h3>
                            Ubicaciones estratégicas
                        </h3>

                        <p>
                            Evaluamos ubicación, accesos y
                            características de cada terreno.
                        </p>

                    </div>

                </div>


                <!-- 6 -->

                <div class="col-md-6 col-lg-4">

                    <div class="advantage-card">

                        <div class="advantage-icon">

                            <i class="bi bi-people"></i>

                        </div>

                        <h3>
                            Atención personalizada
                        </h3>

                        <p>
                            Nuestro equipo está disponible para
                            orientarte durante todo el proceso.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
     ESTADÍSTICAS
========================================================== -->

    <section class="stats-section">

        <div class="container">

            <div class="row">

                <!-- PRECIO -->

                <div class="col-md-3">

                    <div class="stat-item">

                        <div class="stat-icon">

                            <i class="bi bi-cash-stack"></i>

                        </div>

                        <div>

                            <span class="stat-label">
                                Desde
                            </span>

                            <strong class="stat-value">
                                <?php if ($precioMinimo > 0): ?>
                                    S/. <?= number_format($precioMinimo, 0) ?>
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- UBICACIÓN -->

                <div class="col-md-3">

                    <div class="stat-item">

                        <div class="stat-icon">

                            <i class="bi bi-geo-alt"></i>

                        </div>

                        <div>

                            <span class="stat-label">
                                Ubicación
                            </span>

                            <span class="stat-text">
                                <?= e($usuario['direccion'] ?: 'Iquitos') ?>
                            </span>

                        </div>

                    </div>

                </div>


                <!-- PROPIEDADES -->

                <div class="col-md-3">

                    <div class="stat-item">

                        <div class="stat-icon">

                            <i class="bi bi-houses"></i>

                        </div>

                        <div>

                            <span class="stat-label">
                                Propiedades
                            </span>

                            <strong class="stat-value">

                                <?= $totalPropiedades ?>

                            </strong>

                        </div>

                    </div>

                </div>


                <!-- ÁREA -->

                <div class="col-md-3">

                    <div class="stat-item">

                        <div class="stat-icon">

                            <i class="bi bi-arrows-fullscreen"></i>

                        </div>

                        <div>

                            <span class="stat-label">
                                Metraje
                            </span>

                            <span class="stat-text">
                                <?php if ($areaMinima > 0): ?>
                                    Desde <?= number_format($areaMinima, 0) ?> m²
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
     CTA
========================================================== -->

    <section class="cta-section">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <h2>

                        Cuéntanos sobre

                        <strong>
                            tu próximo lugar ideal
                        </strong>

                    </h2>

                    <p>

                        Nuestro equipo está listo para ayudarte
                        a encontrar una propiedad.

                    </p>

                </div>


                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                    <a
                        href="#contacto"
                        class="btn btn-success btn-lg px-5">

                        Cotizar ahora

                        <i class="bi bi-arrow-right ms-2"></i>

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
     ESPACIOS / IMÁGENES DE PROPIEDADES
========================================================== -->

    <section class="spaces-section">

        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-5">

                    <span class="section-eyebrow">

                        NUESTROS ESPACIOS

                    </span>


                    <h2 class="section-title">

                        Áreas comunes

                    </h2>


                    <p class="section-description">

                        Conoce algunos de nuestros proyectos y propiedades
                        a través de las imágenes de nuestro catálogo.

                    </p>


                    <p class="text-muted">

                        Las imágenes corresponden a la fotografía principal
                        de cada propiedad registrada en nuestro sistema.

                    </p>

                </div>


                <div class="col-lg-7">

                    <?php if (!empty($imagenesAreas)): ?>

                        <div
                            id="spacesCarousel"
                            class="carousel slide spaces-carousel"
                            data-bs-ride="carousel">

                            <!-- INDICADORES -->

                            <div class="carousel-indicators">

                                <?php foreach ($imagenesAreas as $indice => $imagenArea): ?>

                                    <button
                                        type="button"
                                        data-bs-target="#spacesCarousel"
                                        data-bs-slide-to="<?= $indice ?>"
                                        class="<?= $indice === 0 ? 'active' : '' ?>"
                                        <?= $indice === 0 ? 'aria-current="true"' : '' ?>
                                        aria-label="Imagen <?= $indice + 1 ?>">
                                    </button>

                                <?php endforeach; ?>

                            </div>


                            <!-- IMÁGENES -->

                            <div class="carousel-inner">

                                <?php foreach ($imagenesAreas as $indice => $imagenArea): ?>

                                    <?php

                                    $imagenAreaBase64 = '';

                                    if (!empty($imagenArea['imagenes'])) {

                                        $imagenAreaBase64 =
                                            'data:image/jpeg;base64,' .
                                            base64_encode($imagenArea['imagenes']);
                                    }

                                    ?>

                                    <div
                                        class="carousel-item <?= $indice === 0 ? 'active' : '' ?>">

                                        <?php if ($imagenAreaBase64): ?>

                                            <img
                                                src="<?= $imagenAreaBase64 ?>"
                                                class="d-block w-100"
                                                alt="<?= e($imagenArea['nombre_propiedad']) ?>"
                                                loading="<?= $indice === 0 ? 'eager' : 'lazy' ?>">

                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            </div>


                            <!-- CONTROL ANTERIOR -->

                            <?php if (count($imagenesAreas) > 1): ?>

                                <button
                                    class="carousel-control-prev"
                                    type="button"
                                    data-bs-target="#spacesCarousel"
                                    data-bs-slide="prev">

                                    <span
                                        class="carousel-control-prev-icon">
                                    </span>

                                    <span class="visually-hidden">
                                        Anterior
                                    </span>

                                </button>


                                <!-- CONTROL SIGUIENTE -->

                                <button
                                    class="carousel-control-next"
                                    type="button"
                                    data-bs-target="#spacesCarousel"
                                    data-bs-slide="next">

                                    <span
                                        class="carousel-control-next-icon">
                                    </span>

                                    <span class="visually-hidden">
                                        Siguiente
                                    </span>

                                </button>

                            <?php endif; ?>

                        </div>

                    <?php else: ?>

                        <div class="spaces-placeholder">

                            <i class="bi bi-images"></i>

                            <p class="mb-0">
                                No hay imágenes de propiedades disponibles.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>
    <!-- =========================================================
 TESTIMONIOS
========================================================== -->

    <section class="testimonials-section">

        <div class="container">

            <div class="section-heading">

                <span class="section-eyebrow">

                    EXPERIENCIAS

                </span>


                <h2 class="section-title">

                    Lo que nuestros clientes dicen

                </h2>


                <p class="section-description">

                    La confianza de nuestros clientes es parte
                    fundamental de nuestro trabajo.

                </p>

            </div>


            <div class="row g-4">

                <?php if (!empty($testimonios)): ?>

                    <?php foreach ($testimonios as $testimonio): ?>

                        <?php

                        /* ==================================================
                       NOMBRE COMPLETO
                    ================================================== */

                        $nombreTestimonio = trim(
                            ($testimonio['nombre'] ?? '') . ' ' .
                                ($testimonio['apellidos'] ?? '')
                        );

                        if ($nombreTestimonio === '') {
                            $nombreTestimonio = 'Cliente';
                        }


                        /* ==================================================
                       IMAGEN
                    ================================================== */

                        $imagenTestimonio = '';

                        if (!empty($testimonio['imagen'])) {

                            $imagenTestimonio =
                                'data:image/jpeg;base64,' .
                                base64_encode($testimonio['imagen']);
                        } else {

                            $imagenTestimonio = 'img/usuario.jpg';
                        }


                        /* ==================================================
                       COMENTARIO
                    ================================================== */

                        $comentarioTestimonio = trim(
                            (string)($testimonio['comentario'] ?? '')
                        );


                        /* ==================================================
                       FECHA
                    ================================================== */

                        $fechaTestimonio =
                            $testimonio['fecha_actualizado']
                            ?: $testimonio['fecha_registro']
                            ?: '';

                        $fechaFormateada = '';

                        if ($fechaTestimonio !== '') {

                            $timestamp = strtotime($fechaTestimonio);

                            if ($timestamp !== false) {

                                $fechaFormateada = date(
                                    'd/m/Y',
                                    $timestamp
                                );
                            }
                        }


                        /* ==================================================
                       VIDEO
                    ================================================== */

                        $videoTestimonio = trim(
                            (string)($testimonio['video'] ?? '')
                        );

                        ?>

                        <div class="col-md-6 col-lg-3">

                            <article class="testimonial-card">

                                <!-- ==================================================
                                 CABECERA
                            ================================================== -->

                                <div class="testimonial-header">

                                    <img
                                        src="<?= e($imagenTestimonio) ?>"
                                        alt="<?= e($nombreTestimonio) ?>"
                                        loading="lazy">

                                    <div>

                                        <h3>

                                            <?= e($nombreTestimonio) ?>

                                        </h3>

                                        <?php if ($fechaFormateada): ?>

                                            <span>

                                                Cliente
                                                ·
                                                <?= e($fechaFormateada) ?>

                                            </span>

                                        <?php else: ?>

                                            <span>
                                                Cliente
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- ==================================================
                                 ICONO DE CITA
                            ================================================== -->

                                <div class="testimonial-quote">

                                    <i class="fa-solid fa-quote-left"></i>

                                </div>


                                <!-- ==================================================
                                 COMENTARIO
                            ================================================== -->

                                <p>

                                    <?= nl2br(e($comentarioTestimonio)) ?>

                                </p>


                                <!-- ==================================================
                                 VIDEO
                            ================================================== -->

                                <?php if ($videoTestimonio !== ''): ?>

                                    <?php

                                    /*
                                 * La BD normalmente guarda solamente
                                 * el nombre del archivo.
                                 *
                                 * Ejemplo:
                                 * cliente_juan.mp4
                                 */

                                    $videoTestimonio = str_replace(
                                        '\\',
                                        '/',
                                        $videoTestimonio
                                    );

                                    $nombreVideoTestimonio =
                                        basename($videoTestimonio);

                                    ?>

                                    <?php if ($nombreVideoTestimonio !== ''): ?>

                                        <div class="testimonial-video mt-3">

                                            <video
                                                controls
                                                preload="metadata"
                                                playsinline
                                                class="w-100">

                                                <source
                                                    src="uploads/testimonios/videos/<?= rawurlencode($nombreVideoTestimonio) ?>">

                                                Tu navegador no soporta la reproducción de videos.

                                            </video>

                                        </div>

                                    <?php endif; ?>

                                <?php endif; ?>

                            </article>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <!-- ==================================================
                     SIN TESTIMONIOS
                ================================================== -->

                    <div class="col-12">

                        <div class="text-center py-2">

                            <div class="mb-3">

                                <i
                                    class="fa-regular fa-comments fa-3x text-success">
                                </i>

                            </div>

                            <h3 class="h5">

                                Aún no tenemos testimonios publicados.

                            </h3>

                            <p class="text-muted mb-0">

                                Próximamente podrás conocer las experiencias
                                de nuestros clientes.

                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>



    <!-- =========================================================
     CONTACTO
========================================================== -->

    <section
        id="contacto"
        class="contact-section">

        <div class="container">

            <div class="row g-5 align-items-start">

                <!-- INFORMACIÓN -->

                <div class="col-lg-5">

                    <span class="section-eyebrow">

                        CONTACTO

                    </span>


                    <h2 class="section-title">

                        Estamos listos para atenderte

                    </h2>


                    <p class="section-description">

                        Déjanos tus datos y cuéntanos qué propiedad
                        estás buscando. Un asesor se pondrá en
                        contacto contigo.

                    </p>


                    <div class="contact-info">

                        <?php if (!empty($usuario['celular'])): ?>

                            <div class="contact-item">

                                <div class="contact-icon">

                                    <i class="fa-solid fa-phone"></i>

                                </div>

                                <div>

                                    <span>
                                        Llámanos
                                    </span>

                                    <a
                                        href="tel:<?= e($usuario['celular']) ?>">

                                        +51 <?= e($usuario['celular']) ?>

                                    </a>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if (!empty($usuario['email'])): ?>

                            <div class="contact-item">

                                <div class="contact-icon">

                                    <i class="fa-solid fa-envelope"></i>

                                </div>

                                <div>

                                    <span>
                                        Escríbenos
                                    </span>

                                    <a
                                        href="mailto:<?= e($usuario['email']) ?>">

                                        <?= e($usuario['email']) ?>

                                    </a>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if (!empty($usuario['direccion'])): ?>

                            <div class="contact-item">

                                <div class="contact-icon">

                                    <i class="fa-solid fa-location-dot"></i>

                                </div>

                                <div>

                                    <span>
                                        Dirección
                                    </span>

                                    <strong>
                                        <?= e($usuario['direccion']) ?>
                                    </strong>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if ($whatsappEmpresa): ?>

                            <div class="contact-item">

                                <div class="contact-icon">

                                    <i class="fab fa-whatsapp"></i>

                                </div>

                                <div>

                                    <span>
                                        WhatsApp
                                    </span>

                                    <a
                                        href="https://wa.me/<?= e($whatsappEmpresa) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer">

                                        Escríbenos por WhatsApp

                                    </a>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- FORMULARIO -->

                <div class="col-lg-7">

                    <div class="contact-form-card">

                        <h3 class="fw-bold mb-4">

                            Solicita información

                        </h3>
                        <div
                            id="respuestaContacto"
                            class="mb-4"
                            style="display:none;">
                        </div>
                        <form
                            method="POST"
                            action="ajax/procesar_contacto.php"
                            class="contact-form"
                            id="formContacto">


                            <!-- PROPIEDAD -->

                            <div class="mb-3">

                                <label
                                    for="propiedades"
                                    class="form-label">

                                    Propiedad de interés

                                </label>


                                <select
                                    class="form-select"
                                    id="propiedades"
                                    name="propiedades"
                                    required>

                                    <option
                                        value=""
                                        selected
                                        disabled>

                                        Selecciona una propiedad

                                    </option>


                                    <?php foreach ($listaPropiedades as $propiedad): ?>

                                        <option
                                            value="<?= (int)$propiedad['id_propiedad'] ?>">

                                            <?= e($propiedad['nombre']) ?>

                                            <?php if (!empty($propiedad['codigo'])): ?>

                                                - Código:
                                                <?= e($propiedad['codigo']) ?>

                                            <?php endif; ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- NOMBRES -->

                            <div class="row g-3 mb-3">

                                <div class="col-md-6">

                                    <label
                                        for="nombre"
                                        class="form-label">

                                        Nombres

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="nombre"
                                        name="nombre"
                                        autocomplete="given-name"
                                        required>

                                </div>


                                <div class="col-md-6">

                                    <label
                                        for="apellidos"
                                        class="form-label">

                                        Apellidos

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="apellidos"
                                        name="apellidos"
                                        autocomplete="family-name"
                                        required>

                                </div>

                            </div>


                            <!-- EMAIL -->

                            <div class="mb-3">

                                <label
                                    for="correo"
                                    class="form-label">

                                    Correo electrónico

                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="correo"
                                    name="correo"
                                    autocomplete="email"
                                    required>

                            </div>


                            <!-- CELULAR -->

                            <div class="mb-3">

                                <label
                                    for="celular"
                                    class="form-label">

                                    Celular

                                </label>

                                <input
                                    type="tel"
                                    class="form-control"
                                    id="celular"
                                    name="celular"
                                    placeholder="Ejemplo: 943239039"
                                    autocomplete="tel"
                                    required>

                            </div>


                            <!-- MENSAJE -->

                            <div class="mb-4">

                                <label
                                    for="mensaje"
                                    class="form-label">

                                    Mensaje

                                </label>

                                <textarea
                                    class="form-control"
                                    id="mensaje"
                                    name="mensaje"
                                    rows="5"
                                    placeholder="Cuéntanos qué propiedad estás buscando..."
                                    required></textarea>

                            </div>


                            <!-- BOTÓN -->

                            <button
                                type="submit"
                                class="btn btn-success btn-lg px-4"
                                id="btnEnviarCotizacion">

                                <i class="bi bi-send me-2"></i>

                                <span id="textoBtnEnviar">
                                    Enviar y cotizar
                                </span>

                            </button>

                        </form>
                    </div>

                </div>

            </div>

        </div>

    </section>
    <?php include 'modal/modal_detalle_propiedad.php'; ?>
    <!-- llamar a otros/footer.php -->
    <?php include 'otros/footer.php'; ?>
    <!-- =========================================================
     JAVASCRIPT
========================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

    <script
        src="js/detalle_propiedad.js">
    </script>
    <script
        src="js/chat.js">
    </script>
    <script
        src="js/contacto.js">
    </script>

</body>

</html>