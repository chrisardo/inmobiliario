<?php
session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

include '../controladores/conect_db.php';
include '../controladores/procesar_registro_asesor.php';
$usId = (int) $_SESSION['usId'];
/* ============================================================
   DATOS DEL USUARIO / EMPRESA
============================================================ */

$sqlFoto = "SELECT imagen, nombreEmpresa FROM usuario_acceso WHERE id_user = ?";
$stmt = $conexion->prepare($sqlFoto);
$stmt->bind_param("i", $_SESSION['usId']);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$fotoPerfil = null;

if (!empty($usuario['imagen'])) {
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($usuario['imagen']);
}

$nombreEmpresa = $usuario['nombreEmpresa'] ?? 'Panel administrativo';

$stmt->close();
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

    <title>
        Registrar asesor — <?= htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8') ?>
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
        href="../css/adm_registro_asesor.css">

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

                    <?php if ($fotoPerfil): ?>

                        <img
                            src="<?= htmlspecialchars($fotoPerfil) ?>"
                            alt="Empresa">

                    <?php else: ?>

                        <i class="fa-solid fa-building"></i>

                    <?php endif; ?>

                </div>

                <div class="company-info">

                    <strong>
                        <?= htmlspecialchars($nombreEmpresa) ?>
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
                    class="sidebar-link sidebar-collapse-link active"
                    data-bs-toggle="collapse"
                    href="#menuClientes"
                    role="button"
                    aria-expanded="true">

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
                    class="collapse show list-unstyled sidebar-submenu"
                    id="menuClientes">

                    <li>

                        <a
                            href="adm_registrar_asesor.php"
                            class="sidebar-sublink active">

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


                <div class="sidebar-section-title">
                    CUENTA
                </div>
                <a
                    href="adm_perfil.php"
                    class="sidebar-link">

                    <span class="sidebar-link-left">

                        <span class="sidebar-link-icon">
                            <i class="fa-solid fa-user"></i>
                        </span>

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

            </nav>


            <!-- FOOTER -->
            <div class="sidebar-footer">

                <i class="fa-solid fa-shield-halved sidebar-security-icon"></i>

                <span>
                    Sistema seguro
                </span>

            </div>

        </aside>


        <!-- OVERLAY MÓVIL -->
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
                        id="toggleSidebar"
                        type="button"
                        class="sidebar-toggle"
                        aria-label="Abrir menú"
                        aria-expanded="false">

                        <i class="fa-solid fa-bars"></i>

                    </button>


                    <div class="topbar-title">

                        <span>
                            Administración
                        </span>

                        <strong>
                            Registrar asesor
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

                        <?php if (!empty($totalMensajes)): ?>

                            <span class="notification-dot">
                                <?= (int)$totalMensajes ?>
                            </span>

                        <?php endif; ?>

                    </a>


                    <!-- PERFIL -->
                    <a
                        href="adm_perfil.php"
                        class="topbar-profile">

                        <div class="topbar-avatar">

                            <?php if ($fotoPerfil): ?>

                                <img
                                    src="<?= htmlspecialchars($fotoPerfil) ?>"
                                    alt="Perfil">

                            <?php else: ?>

                                <i class="fa-solid fa-user"></i>

                            <?php endif; ?>

                        </div>


                        <div class="topbar-user">

                            <strong>
                                <?= htmlspecialchars($nombreEmpresa) ?>
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


                <!-- ENCABEZADO -->
                <section class="page-header">

                    <div>

                        <span class="page-eyebrow">
                            GESTIÓN DE ASESORES
                        </span>

                        <h1>
                            Registrar asesor
                        </h1>

                        <p>
                            Completa la información para incorporar un nuevo asesor a tu equipo.
                        </p>

                    </div>


                    <div class="page-header-icon">

                        <i class="fa-solid fa-user-plus"></i>

                    </div>

                </section>


                <!-- ALERTAS -->
                <?php if (!empty($mensaje)): ?>

                    <div
                        class="form-alert alert alert-<?= htmlspecialchars($tipoAlerta) ?> alert-dismissible fade show"
                        role="alert">

                        <div class="form-alert-icon">

                            <?php if ($tipoAlerta === 'success'): ?>

                                <i class="fa-solid fa-circle-check"></i>

                            <?php elseif ($tipoAlerta === 'warning'): ?>

                                <i class="fa-solid fa-triangle-exclamation"></i>

                            <?php else: ?>

                                <i class="fa-solid fa-circle-exclamation"></i>

                            <?php endif; ?>

                        </div>

                        <div>
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
                    id="formRegistroAsesor"
                    novalidate>


                    <div class="form-layout">


                        <!-- =================================================
                         INFORMACIÓN PERSONAL
                    ================================================== -->

                        <section class="form-card">

                            <div class="form-card-header">

                                <div class="form-section-icon green">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                                <div>

                                    <h2>
                                        Información personal
                                    </h2>

                                    <p>
                                        Datos básicos del asesor.
                                    </p>

                                </div>

                            </div>


                            <div class="form-card-body">

                                <div class="row g-4">


                                    <!-- NOMBRE -->
                                    <div class="col-md-6">

                                        <label
                                            for="nombre"
                                            class="modern-label">

                                            Nombre

                                            <span>*</span>

                                        </label>

                                        <div class="modern-input-group">

                                            <span class="modern-input-icon">
                                                <i class="fa-solid fa-user"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control modern-input"
                                                id="nombre"
                                                name="nombre"
                                                placeholder="Ej. Christian"
                                                autocomplete="given-name"
                                                maxlength="80"
                                                required>

                                        </div>

                                        <div class="invalid-feedback">
                                            Ingresa el nombre del asesor.
                                        </div>

                                    </div>


                                    <!-- APELLIDOS -->
                                    <div class="col-md-6">

                                        <label
                                            for="apellidos"
                                            class="modern-label">

                                            Apellidos

                                            <span>*</span>

                                        </label>

                                        <div class="modern-input-group">

                                            <span class="modern-input-icon">
                                                <i class="fa-solid fa-user-tag"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control modern-input"
                                                id="apellidos"
                                                name="apellidos"
                                                placeholder="Ej. Rojas García"
                                                autocomplete="family-name"
                                                maxlength="120"
                                                required>

                                        </div>

                                        <div class="invalid-feedback">
                                            Ingresa los apellidos del asesor.
                                        </div>

                                    </div>


                                    <!-- EMAIL -->
                                    <div class="col-md-7">

                                        <label
                                            for="email"
                                            class="modern-label">

                                            Correo corporativo

                                            <span>*</span>

                                        </label>

                                        <div class="modern-input-group">

                                            <span class="modern-input-icon">
                                                <i class="fa-solid fa-envelope"></i>
                                            </span>

                                            <input
                                                type="email"
                                                class="form-control modern-input"
                                                id="email"
                                                name="email"
                                                placeholder="asesor@empresa.com"
                                                autocomplete="email"
                                                maxlength="150"
                                                required>

                                        </div>

                                        <div class="invalid-feedback">
                                            Ingresa un correo electrónico válido.
                                        </div>

                                    </div>


                                    <!-- CELULAR -->
                                    <div class="col-md-5">

                                        <label
                                            for="celular"
                                            class="modern-label">

                                            Celular

                                            <span>*</span>

                                        </label>

                                        <div class="modern-input-group">

                                            <span class="modern-input-icon">
                                                <i class="fa-solid fa-mobile-screen-button"></i>
                                            </span>

                                            <input
                                                type="tel"
                                                class="form-control modern-input"
                                                id="celular"
                                                name="celular"
                                                placeholder="943239039"
                                                maxlength="9"
                                                pattern="[0-9]{9}"
                                                inputmode="numeric"
                                                autocomplete="tel"
                                                required>

                                        </div>

                                        <div class="input-helper">
                                            9 dígitos numéricos.
                                        </div>

                                        <div class="invalid-feedback">
                                            El celular debe contener 9 dígitos.
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </section>


                        <!-- =================================================
                         INFORMACIÓN LABORAL
                    ================================================== -->

                        <section class="form-card">

                            <div class="form-card-header">

                                <div class="form-section-icon blue">

                                    <i class="fa-solid fa-briefcase"></i>

                                </div>

                                <div>

                                    <h2>
                                        Información laboral
                                    </h2>

                                    <p>
                                        Define el cargo que tendrá dentro de la empresa.
                                    </p>

                                </div>

                            </div>


                            <div class="form-card-body">

                                <label
                                    for="cargo"
                                    class="modern-label">

                                    Cargo

                                    <span>*</span>

                                </label>

                                <div class="modern-input-group">

                                    <span class="modern-input-icon">
                                        <i class="fa-solid fa-id-badge"></i>
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control modern-input"
                                        id="cargo"
                                        name="cargo"
                                        placeholder="Ej. Asesor inmobiliario"
                                        maxlength="100"
                                        required>

                                </div>

                                <div class="input-helper">
                                    Ejemplo: Asesor inmobiliario, Ejecutivo comercial, Supervisor de ventas.
                                </div>

                                <div class="invalid-feedback">
                                    Ingresa el cargo del asesor.
                                </div>

                            </div>

                        </section>


                        <!-- =================================================
                         FOTO
                    ================================================== -->

                        <section class="form-card">

                            <div class="form-card-header">

                                <div class="form-section-icon purple">

                                    <i class="fa-solid fa-camera"></i>

                                </div>

                                <div>

                                    <h2>
                                        Fotografía del asesor
                                    </h2>

                                    <p>
                                        Puedes agregar una fotografía para identificarlo.
                                    </p>

                                </div>

                                <span class="optional-badge">
                                    OPCIONAL
                                </span>

                            </div>


                            <div class="form-card-body">

                                <div class="upload-area" id="uploadArea">

                                    <input
                                        type="file"
                                        name="imagen"
                                        id="imagen"
                                        class="upload-input"
                                        accept="image/jpeg,image/png">


                                    <label
                                        for="imagen"
                                        class="upload-label">

                                        <div class="upload-icon">

                                            <i class="fa-solid fa-cloud-arrow-up"></i>

                                        </div>

                                        <div class="upload-content">

                                            <strong>
                                                Selecciona una fotografía
                                            </strong>

                                            <span>
                                                JPG o PNG · máximo 1.8 MB
                                            </span>

                                        </div>

                                        <div class="upload-button">
                                            Examinar
                                        </div>

                                    </label>

                                </div>


                                <!-- PREVIEW -->
                                <div
                                    id="previewImagen"
                                    class="image-preview d-none">

                                    <div class="preview-image-wrapper">

                                        <img
                                            id="previewImg"
                                            src=""
                                            alt="Vista previa">

                                    </div>


                                    <div class="preview-information">

                                        <strong>
                                            Vista previa
                                        </strong>

                                        <div class="preview-details">

                                            <span>
                                                <i class="fa-solid fa-file-image"></i>

                                                <span id="imgTipo">
                                                    -
                                                </span>
                                            </span>

                                            <span>
                                                <i class="fa-solid fa-hard-drive"></i>

                                                <span id="imgSize">
                                                    -
                                                </span>
                                            </span>

                                        </div>

                                    </div>


                                    <button
                                        type="button"
                                        id="removeImagen"
                                        class="preview-remove"
                                        title="Eliminar imagen"
                                        aria-label="Eliminar imagen">

                                        <i class="fa-solid fa-trash"></i>

                                    </button>

                                </div>

                            </div>

                        </section>


                        <!-- =================================================
                         ACCIONES
                    ================================================== -->

                        <div class="form-actions">

                            <a
                                href="adm_lista_asesores.php"
                                class="btn-secondary-modern">

                                <i class="fa-solid fa-arrow-left"></i>

                                Cancelar

                            </a>


                            <button
                                type="submit"
                                class="btn-primary-modern"
                                id="btnRegistrar">

                                <span class="button-normal">

                                    <i class="fa-solid fa-user-plus"></i>

                                    Registrar asesor

                                </span>

                                <span class="button-loading d-none">

                                    <span
                                        class="spinner-border spinner-border-sm"
                                        aria-hidden="true">
                                    </span>

                                    Registrando...

                                </span>

                            </button>

                        </div>


                    </div>

                </form>


                <!-- FOOTER -->
                <footer class="dashboard-footer">

                    <span>
                        © <?= date('Y') ?> <?= htmlspecialchars($nombreEmpresa) ?>
                    </span>

                    <span>
                        Gestión de asesores
                    </span>

                </footer>

            </div>

        </main>

    </div>


    <!-- =========================================================
     JAVASCRIPT
========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

    <script
        src="../js/visualizar_imagen.js">
    </script>

    <script
        src="../js/adm_regitro_asesor.js">
    </script>

    <script
        src="../js/menu_sidebar.js">
    </script>

</body>

</html>

<?php
$conexion->close();
?>