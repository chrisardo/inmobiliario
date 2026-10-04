<?php
session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

include '../controladores/conect_db.php';
include '../controladores/procesar_registro_producto.php';
$usId = (int) $_SESSION['usId'];
/* ============================================================
   DATOS DEL USUARIO
============================================================ */

$sqlFoto = "SELECT imagen, nombreEmpresa 
            FROM usuario_acceso 
            WHERE id_user = ?";

$stmtFoto = $conexion->prepare($sqlFoto);
$stmtFoto->bind_param("i", $_SESSION['usId']);
$stmtFoto->execute();

$resultFoto = $stmtFoto->get_result();
$usuario = $resultFoto->fetch_assoc();

$fotoPerfil = null;
$nombreEmpresa = "Administración";

if (!empty($usuario['imagen'])) {
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($usuario['imagen']);
}

if (!empty($usuario['nombreEmpresa'])) {
    $nombreEmpresa = $usuario['nombreEmpresa'];
}

$stmtFoto->close();

/* ============================================================
   CATEGORÍAS
============================================================ */

$categorias = [];

$sqlCategorias = "SELECT id_categoria, nombre
                  FROM categoria
                  WHERE Eliminado = 0
                  AND id_user = ?
                  ORDER BY nombre ASC";

$stmtCategorias = $conexion->prepare($sqlCategorias);
$stmtCategorias->bind_param("i", $_SESSION['usId']);
$stmtCategorias->execute();

$resultCategorias = $stmtCategorias->get_result();

while ($fila = $resultCategorias->fetch_assoc()) {
    $categorias[] = $fila;
}

$stmtCategorias->close();
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

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registro Propiedad — <?= htmlspecialchars($usuario['nombreEmpresa'] ?? 'Panel') ?></title>

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



    <!-- Bootstrap Icons -->
    <!--<link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        rel="stylesheet">-->

    <!-- Sidebar -->
    <link rel="stylesheet" href="../css/menu_sidebar.css">

    <!-- Estilos de esta página -->
    <link rel="stylesheet" href="../css/registrar_propudcto.css">

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
                            Registro propiedad
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

            <div class="property-content">


                <!-- Encabezado -->

                <div class="property-page-header">

                    <div>

                        <div class="property-breadcrumb">

                            <a href="adm_index.php">
                                Inicio
                            </a>

                            <i class="fa-solid fa-chevron-right"></i>

                            <a href="adm_lista_propiedades.php">
                                Propiedades
                            </a>

                            <i class="fa-solid fa-chevron-right"></i>

                            <span>
                                Registrar
                            </span>

                        </div>


                        <h1>
                            Registrar propiedad
                        </h1>

                        <p>
                            Ingresa la información de la nueva propiedad
                            para incorporarla a tu catálogo inmobiliario.
                        </p>

                    </div>


                    <a
                        href="adm_lista_propiedades.php"
                        class="btn-back">

                        <i class="fa-solid fa-arrow-left"></i>

                        <span>
                            Volver a propiedades
                        </span>

                    </a>

                </div>


                <!-- =================================================
                MENSAJE DE ALERTA
                ================================================== -->

                <div
                    id="alertaRegistroPropiedad"
                    class="register-alert alert d-none"
                    role="alert"
                    aria-live="polite">

                    <div class="alert-icon" id="alertaRegistroIcon">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>

                    <div
                        class="alert-message"
                        id="alertaRegistroMensaje">
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        id="cerrarAlertaRegistro"
                        aria-label="Cerrar">
                    </button>

                </div>


                <?php if (!empty($mensaje)): ?>

                    <div
                        class="register-alert alert alert-<?= htmlspecialchars($tipoAlerta) ?> alert-dismissible fade show"
                        role="alert">

                        <div class="alert-icon">

                            <?php if ($tipoAlerta === 'success'): ?>

                                <i class="fa-solid fa-circle-check"></i>

                            <?php elseif ($tipoAlerta === 'warning'): ?>

                                <i class="fa-solid fa-triangle-exclamation"></i>

                            <?php else: ?>

                                <i class="fa-solid fa-circle-exclamation"></i>

                            <?php endif; ?>

                        </div>

                        <div class="alert-message">
                            <?= htmlspecialchars($mensaje) ?>
                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Cerrar">
                        </button>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                    FORMULARIO
                ================================================== -->

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    id="formPropiedad"
                    novalidate>


                    <div class="property-form-grid">


                        <!-- =========================================
                            COLUMNA PRINCIPAL
                        ========================================== -->

                        <div class="property-main-column">


                            <!-- INFORMACIÓN GENERAL -->

                            <section class="property-card">

                                <div class="property-card-header">

                                    <div class="property-card-icon green">

                                        <i class="fa-solid fa-building"></i>

                                    </div>

                                    <div>

                                        <span>
                                            INFORMACIÓN GENERAL
                                        </span>

                                        <h2>
                                            Datos de la propiedad
                                        </h2>

                                    </div>

                                </div>


                                <div class="property-card-body">

                                    <div class="row g-4">


                                        <!-- Código -->

                                        <div class="col-md-5">

                                            <label
                                                for="codigo"
                                                class="property-label">

                                                Código de propiedad

                                                <span>*</span>

                                            </label>

                                            <div class="property-input">

                                                <i class="fa-solid fa-hashtag"></i>

                                                <input
                                                    type="text"
                                                    id="codigo"
                                                    name="codigo"
                                                    placeholder="Ej. PROP-001"
                                                    maxlength="50"
                                                    autocomplete="off"
                                                    required>

                                            </div>

                                            <div class="property-help">
                                                Código único para identificar la propiedad.
                                            </div>

                                        </div>


                                        <!-- Nombre -->

                                        <div class="col-md-7">

                                            <label
                                                for="nombre"
                                                class="property-label">

                                                Nombre de la propiedad

                                                <span>*</span>

                                            </label>

                                            <div class="property-input">

                                                <i class="fa-solid fa-house"></i>

                                                <input
                                                    type="text"
                                                    id="nombre"
                                                    name="nombre"
                                                    placeholder="Ej. Casa moderna en Punchana"
                                                    maxlength="150"
                                                    required>

                                            </div>

                                        </div>


                                    </div>

                                </div>

                            </section>


                            <!-- CARACTERÍSTICAS -->

                            <section class="property-card">

                                <div class="property-card-header">

                                    <div class="property-card-icon blue">

                                        <i class="fa-solid fa-ruler-combined"></i>

                                    </div>

                                    <div>

                                        <span>
                                            CARACTERÍSTICAS
                                        </span>

                                        <h2>
                                            Área y precio
                                        </h2>

                                    </div>

                                </div>


                                <div class="property-card-body">

                                    <div class="row g-4">


                                        <!-- Área -->

                                        <div class="col-md-6">

                                            <label
                                                for="tamano_area"
                                                class="property-label">

                                                Área de la propiedad

                                                <span>*</span>

                                            </label>

                                            <div class="property-input property-input-unit">

                                                <i class="fa-solid fa-vector-square"></i>

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    id="tamano_area"
                                                    name="tamano_area"
                                                    placeholder="0.00"
                                                    required>

                                                <span class="input-unit">
                                                    m²
                                                </span>

                                            </div>

                                            <div class="property-help">
                                                Ingresa el área total en metros cuadrados.
                                            </div>

                                        </div>


                                        <!-- Precio -->

                                        <div class="col-md-6">

                                            <label
                                                for="precio_venta"
                                                class="property-label">

                                                Precio de venta

                                                <span>*</span>

                                            </label>

                                            <div class="property-input property-input-unit">

                                                <i class="fa-solid fa-tag"></i>

                                                <span class="currency-prefix">
                                                    S/
                                                </span>

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    id="precio_venta"
                                                    name="precio_venta"
                                                    placeholder="0.00"
                                                    required>

                                            </div>

                                            <div class="property-help">
                                                Precio actual de comercialización.
                                            </div>

                                        </div>
                                        <!-- Precio -->

                                        <div class="col-md-6">

                                            <label
                                                for="precio_venta"
                                                class="property-label">

                                                Precio de venta anterior

                                                <span>(Opcional)</span>

                                            </label>

                                            <div class="property-input property-input-unit">

                                                <span class="currency-prefix">
                                                    S/
                                                </span>

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    id="precio_anterior"
                                                    name="precio_anterior"
                                                    placeholder="0.00"
                                                    required>

                                            </div>

                                            <div class="property-help">
                                                Precio actual de comercialización.
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </section>


                            <!-- CLASIFICACIÓN -->

                            <section class="property-card">

                                <div class="property-card-header">

                                    <div class="property-card-icon purple">

                                        <i class="fa-solid fa-layer-group"></i>

                                    </div>

                                    <div>

                                        <span>
                                            CLASIFICACIÓN
                                        </span>

                                        <h2>
                                            Categoría y ubicación
                                        </h2>

                                    </div>

                                </div>


                                <div class="property-card-body">

                                    <div class="row g-4">


                                        <!-- Categoría -->

                                        <div class="col-md-6">

                                            <label
                                                for="categoria"
                                                class="property-label">

                                                Categoría

                                                <span>*</span>

                                            </label>

                                            <div class="property-input">

                                                <i class="fa-solid fa-shapes"></i>

                                                <select
                                                    id="categoria"
                                                    name="categoria"
                                                    required>

                                                    <option
                                                        value=""
                                                        selected
                                                        disabled>

                                                        Selecciona una categoría

                                                    </option>

                                                    <?php foreach ($categorias as $categoria): ?>

                                                        <option
                                                            value="<?= (int)$categoria['id_categoria'] ?>">

                                                            <?= htmlspecialchars($categoria['nombre']) ?>

                                                        </option>

                                                    <?php endforeach; ?>

                                                </select>

                                                <i class="fa-solid fa-chevron-down select-arrow"></i>

                                            </div>

                                        </div>


                                        <!-- Ubicación -->

                                        <div class="col-md-6">

                                            <label
                                                for="ubicacion"
                                                class="property-label">

                                                Ubicación / Zona

                                                <span>*</span>

                                            </label>

                                            <div class="property-input">

                                                <i class="fa-solid fa-location-dot"></i>

                                                <input
                                                    type="text"
                                                    id="ubicacion"
                                                    name="ubicacion"
                                                    placeholder="Ej. San Juan, Iquitos"
                                                    maxlength="200"
                                                    required>

                                            </div>

                                        </div>


                                    </div>

                                </div>

                            </section>


                            <!-- BOTONES -->

                            <div class="property-actions">

                                <a
                                    href="adm_lista_propiedades.php"
                                    class="btn-cancel">

                                    Cancelar

                                </a>


                                <button
                                    type="submit"
                                    class="btn-register">

                                    <i class="fa-solid fa-plus"></i>

                                    <span>
                                        Registrar propiedad
                                    </span>

                                </button>

                            </div>


                        </div>
                        <!-- =========================================
                            COLUMNA IMÁGENES
                        ========================================== -->

                        <div class="property-side-column">

                            <section class="property-card image-card">

                                <div class="property-card-header">

                                    <div class="property-card-icon orange">
                                        <i class="fa-solid fa-images"></i>
                                    </div>

                                    <div class="image-card-title">

                                        <span>
                                            IMÁGENES DE LA PROPIEDAD
                                        </span>

                                        <h2>
                                            Fotografías
                                        </h2>

                                    </div>

                                </div>


                                <div class="property-card-body image-upload-body">

                                    <!-- ENCABEZADO DE IMÁGENES -->

                                    <div class="image-section-heading">

                                        <div>
                                            <strong>
                                                Galería de imágenes
                                            </strong>

                                            <span>
                                                Selecciona hasta 4 fotografías
                                            </span>
                                        </div>

                                        <div
                                            class="image-counter"
                                            id="imageCounter">

                                            <strong id="imageCountNumber">
                                                0
                                            </strong>

                                            <span>/ 4</span>

                                        </div>

                                    </div>


                                    <!-- PREVIEW -->

                                    <div
                                        id="previewImagenes"
                                        class="images-preview-grid d-none">
                                    </div>


                                    <!-- ÁREA DE SELECCIÓN -->

                                    <label
                                        for="imagenes"
                                        class="image-upload-area"
                                        id="imageUploadArea">

                                        <div
                                            class="image-upload-placeholder"
                                            id="imagePlaceholder">

                                            <div class="upload-icon">

                                                <i class="fa-solid fa-cloud-arrow-up"></i>

                                            </div>

                                            <strong>
                                                Agregar fotografías
                                            </strong>

                                            <span>
                                                Arrastra imágenes aquí o haz clic para seleccionar
                                            </span>

                                            <small>
                                                JPG o PNG · Máximo 1.8 MB por imagen
                                            </small>

                                        </div>


                                        <input
                                            type="file"
                                            name="imagenes[]"
                                            id="imagenes"
                                            accept="image/jpeg,image/png"
                                            multiple
                                            hidden>

                                    </label>


                                    <!-- INFORMACIÓN -->

                                    <div
                                        id="imageInfo"
                                        class="image-info d-none">

                                        <div class="image-info-header">

                                            <div class="image-selected-status">

                                                <span class="image-status-icon">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>

                                                <div>

                                                    <strong id="imageCount">
                                                        0 imágenes seleccionadas
                                                    </strong>

                                                    <span id="imageRemaining">
                                                        Puedes agregar más imágenes
                                                    </span>

                                                </div>

                                            </div>


                                            <button
                                                type="button"
                                                id="removeImages"
                                                class="remove-image"
                                                title="Eliminar todas las imágenes">

                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </div>

                                    </div>


                                    <!-- CONSEJO -->

                                    <div class="image-tip">

                                        <i class="fa-solid fa-lightbulb"></i>

                                        <span>
                                            La primera fotografía será utilizada como
                                            <strong>imagen principal</strong>.
                                            Puedes deseleccionar cualquier imagen individualmente.
                                        </span>

                                    </div>

                                </div>

                            </section>


                            <!-- AYUDA -->

                            <div class="property-help-card">

                                <div class="help-icon">

                                    <i class="fa-solid fa-circle-info"></i>

                                </div>

                                <div>

                                    <strong>
                                        Antes de registrar
                                    </strong>

                                    <p>
                                        Verifica que el código sea único y que todos los datos
                                        de la propiedad sean correctos.
                                    </p>

                                </div>

                            </div>

                        </div>
                    </div>

                </form>


                <footer class="property-footer">

                    <span>
                        Sistema de gestión inmobiliaria
                    </span>

                    <span>
                        © <?= date('Y') ?>
                    </span>

                </footer>

            </div>
        </main>

    </div>


    <!-- Bootstrap -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Sidebar -->

    <script src="../js/menu_sidebar.js"></script>


    <!-- Vista previa -->

    <script src="../js/visualizar_registro_imagen.js"></script>


</body>

</html>

<?php
$conexion->close();
?>