<?php
// ============================================================
// CoDevPro Technology
// Archivo: adm/adm_registrar_testimonio.php
// Módulo: Registrar Testimonio
// ============================================================

session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

include '../controladores/conect_db.php';

// ============================================================
// DATOS DEL USUARIO / EMPRESA
// ============================================================

$idUser = (int) $_SESSION['usId'];

$usuario = [];
$fotoPerfil = null;

$sqlUsuario = "
    SELECT
        id_user,
        nombreEmpresa,
        imagen
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
";

$stmtUsuario = mysqli_prepare($conexion, $sqlUsuario);

if ($stmtUsuario) {

    mysqli_stmt_bind_param(
        $stmtUsuario,
        "i",
        $idUser
    );

    mysqli_stmt_execute($stmtUsuario);

    $resultadoUsuario = mysqli_stmt_get_result($stmtUsuario);

    if ($resultadoUsuario && $filaUsuario = mysqli_fetch_assoc($resultadoUsuario)) {

        $usuario = $filaUsuario;

        if (!empty($filaUsuario['imagen'])) {

            $fotoPerfil =
                'data:image/jpeg;base64,' .
                base64_encode($filaUsuario['imagen']);
        }
    }

    mysqli_stmt_close($stmtUsuario);
}


// ============================================================
// TOKEN CSRF
// ============================================================

if (empty($_SESSION['csrf_testimonio'])) {

    $_SESSION['csrf_testimonio'] =
        bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_testimonio'];

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
        Registrar testimonio —
        <?= htmlspecialchars(
            $usuario['nombreEmpresa'] ?? 'Panel'
        ) ?>
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


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- CSS Sidebar -->
    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">
    <link
        rel="stylesheet"
        href="../css/adm_registrar_testimonios.css">



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
                        <?= htmlspecialchars(
                            $usuario['nombreEmpresa'] ?? 'Empresa'
                        ) ?>
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

                </a>


                <div class="sidebar-section-title">
                    GESTIÓN
                </div>


                <!-- =================================================
                 PROPIEDADES
            ================================================== -->

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


                <!-- =================================================
                 ASESORES
            ================================================== -->

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


                <!-- =================================================
                 TESTIMONIOS
            ================================================== -->

                <a
                    class="sidebar-link sidebar-collapse-link"
                    data-bs-toggle="collapse"
                    href="#menuTestimonios"
                    role="button"
                    aria-expanded="true"
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
                    class="collapse show sidebar-submenu"
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
                        class="sidebar-sublink active">

                        <i class="fa-solid fa-plus"></i>

                        <span>
                            Registrar testimonio
                        </span>

                    </a>

                </div>


                <!-- =================================================
                 CUENTA
            ================================================== -->

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


            <!-- Footer -->

            <div class="sidebar-footer">

                <i class="fa-solid fa-shield-halved"></i>

                <span>
                    Sesión segura
                </span>

            </div>

        </aside>


        <!-- Overlay -->

        <div
            id="sidebarOverlay"
            class="sidebar-overlay">
        </div>


        <!-- =====================================================
         CONTENIDO
    ====================================================== -->

        <main
            id="content"
            class="admin-content">


            <!-- TOPBAR -->

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
                            Registrar testimonio
                        </strong>

                    </div>

                </div>


                <div class="topbar-right">

                    <a
                        href="adm_mensajes.php"
                        class="topbar-icon"
                        title="Mensajes">

                        <i class="fa-regular fa-bell"></i>

                    </a>


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
                                <?= htmlspecialchars(
                                    $usuario['nombreEmpresa'] ?? 'Administrador'
                                ) ?>
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
             CONTENIDO PRINCIPAL
        ================================================== -->

            <div class="dashboard-content">


                <div class="page-content">


                    <!-- CABECERA -->

                    <div class="page-header">

                        <span class="page-eyebrow">
                            GESTIÓN DE TESTIMONIOS
                        </span>

                        <h1>
                            Registrar testimonio
                        </h1>

                        <p>
                            Agrega la experiencia y opinión de un cliente
                            para mostrarla en el sitio web.
                        </p>

                    </div>


                    <!-- FORMULARIO -->

                    <form
                        id="formTestimonio"
                        enctype="multipart/form-data"
                        autocomplete="off">


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken) ?>">


                        <div class="form-panel">


                            <div class="form-panel-header">

                                <h2>
                                    Información del testimonio
                                </h2>

                                <p>
                                    Completa los datos que deseas mostrar
                                    públicamente.
                                </p>

                            </div>


                            <div class="form-panel-body">


                                <div class="row g-4">


                                    <!-- =================================================
                                     DATOS PERSONA
                                ================================================== -->

                                    <div class="col-12 col-lg-8">


                                        <div class="row g-3">


                                            <!-- NOMBRE -->

                                            <div class="col-12 col-md-6">

                                                <label
                                                    for="nombre"
                                                    class="form-label">

                                                    Nombre
                                                    <span class="required">*</span>

                                                </label>

                                                <input
                                                    type="text"
                                                    id="nombre"
                                                    name="nombre"
                                                    class="form-control"
                                                    maxlength="100"
                                                    required>

                                            </div>


                                            <!-- APELLIDOS -->

                                            <div class="col-12 col-md-6">

                                                <label
                                                    for="apellidos"
                                                    class="form-label">

                                                    Apellidos
                                                    <span class="required">*</span>

                                                </label>

                                                <input
                                                    type="text"
                                                    id="apellidos"
                                                    name="apellidos"
                                                    class="form-control"
                                                    maxlength="150"
                                                    required>

                                            </div>


                                            <!-- COMENTARIO -->

                                            <div class="col-12">

                                                <label
                                                    for="comentario"
                                                    class="form-label">

                                                    Testimonio
                                                    <span class="required">*</span>

                                                </label>

                                                <textarea
                                                    id="comentario"
                                                    name="comentario"
                                                    class="form-control"
                                                    maxlength="2000"
                                                    required
                                                    placeholder="Escribe la opinión o experiencia del cliente..."></textarea>

                                                <div class="form-text mt-1">

                                                    Máximo 2000 caracteres.

                                                </div>

                                            </div>


                                            <!-- VIDEO -->
                                            <div class="col-12">

                                                <label
                                                    for="video"
                                                    class="form-label">

                                                    Video del testimonio

                                                </label>

                                                <input
                                                    type="file"
                                                    id="video"
                                                    name="video"
                                                    class="form-control"
                                                    accept="video/mp4,video/webm,video/ogg">

                                                <div class="form-text mt-1">

                                                    Opcional. Selecciona un video desde tu equipo.
                                                    Formatos permitidos: MP4, WEBM u OGG.
                                                    Tamaño máximo recomendado: 100 MB.

                                                </div>

                                                <div
                                                    id="videoPreviewContainer"
                                                    class="mt-3"
                                                    style="display:none;">

                                                    <video
                                                        id="videoPreview"
                                                        controls
                                                        style="
                width:100%;
                max-height:280px;
                border-radius:10px;
                background:#0f172a;
            ">
                                                    </video>

                                                </div>

                                            </div>
                                        </div>

                                    </div>


                                    <!-- =================================================
                                     IMAGEN
                                ================================================== -->

                                    <div class="col-12 col-lg-4">


                                        <label
                                            class="form-label">

                                            Imagen
                                            <span class="required">*</span>
                                        </label>


                                        <label
                                            for="imagen"
                                            class="image-upload-box"
                                            id="imageUploadBox">


                                            <div
                                                class="image-upload-icon"
                                                id="imageUploadIcon">

                                                <i class="fa-solid fa-camera"></i>

                                            </div>


                                            <strong id="imageUploadText">
                                                Seleccionar imagen
                                            </strong>


                                            <span id="imageUploadDescription">

                                                JPG, JPEG, PNG o WEBP<br>
                                                Máximo 5 MB

                                            </span>


                                            <img
                                                id="imagenPreview"
                                                alt="Vista previa">


                                        </label>


                                        <input
                                            type="file"
                                            id="imagen"
                                            name="imagen"
                                            class="d-none"
                                            accept="image/jpeg,image/png,image/webp">


                                        <button
                                            type="button"
                                            id="btnRemoveImage"
                                            class="btn btn-sm btn-outline-danger btn-remove-image">

                                            <i class="fa-solid fa-trash me-1"></i>

                                            Quitar imagen

                                        </button>


                                    </div>


                                </div>


                                <!-- INFORMACIÓN -->

                                <div class="info-box">

                                    <i class="fa-solid fa-circle-info"></i>

                                    <span>

                                        El testimonio se asociará automáticamente
                                        al usuario administrador que inició sesión.
                                        La imagen y el video son opcionales.

                                    </span>

                                </div>


                            </div>


                            <!-- ACCIONES -->

                            <div class="form-actions">
                                <button
                                    type="submit"
                                    id="btnGuardar"
                                    class="btn btn-admin btn-success"
                                    disabled>

                                    <i class="fa-solid fa-floppy-disk me-1"></i>

                                    Guardar testimonio

                                </button>

                            </div>


                        </div>


                    </form>


                </div>


                <!-- FOOTER -->

                <footer class="dashboard-footer">

                    <span>

                        © <?= date('Y') ?>

                        <?= htmlspecialchars(
                            $usuario['nombreEmpresa'] ?? ''
                        ) ?>

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
    <!-- Sidebar -->
    <script src="../js/menu_sidebar.js"></script>
    <script src="../js/adm_registrar_testimonios.js"></script>



</body>

</html>