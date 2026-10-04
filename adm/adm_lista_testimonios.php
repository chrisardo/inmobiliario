<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: adm/adm_lista_testimonios.php
// Módulo: Lista de Testimonios
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

$stmtUsuario = mysqli_prepare(
    $conexion,
    $sqlUsuario
);

if ($stmtUsuario) {

    mysqli_stmt_bind_param(
        $stmtUsuario,
        "i",
        $idUser
    );

    mysqli_stmt_execute(
        $stmtUsuario
    );

    $resultadoUsuario =
        mysqli_stmt_get_result(
            $stmtUsuario
        );

    if (
        $resultadoUsuario &&
        $filaUsuario =
            mysqli_fetch_assoc(
                $resultadoUsuario
            )
    ) {

        $usuario = $filaUsuario;

        if (!empty($filaUsuario['imagen'])) {

            $fotoPerfil =
                'data:image/jpeg;base64,' .
                base64_encode(
                    $filaUsuario['imagen']
                );
        }
    }

    mysqli_stmt_close(
        $stmtUsuario
    );
}


// ============================================================
// TOKEN CSRF
// ============================================================

if (
    empty(
        $_SESSION['csrf_testimonio_lista']
    )
) {

    $_SESSION['csrf_testimonio_lista'] =
        bin2hex(
            random_bytes(32)
        );
}

$csrfToken =
    $_SESSION['csrf_testimonio_lista'];


// ============================================================
// CONFIGURACIÓN
// ============================================================

// Este valor también se utiliza en el controlador AJAX.
// Se mantiene aquí únicamente como referencia visual/configuración.
$MAX_TESTIMONIOS = 4;

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

        Testimonios —

        <?= htmlspecialchars(
            $usuario['nombreEmpresa']
                ?? 'Panel',
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </title>


    <!-- ======================================================
         FAVICON
    ======================================================= -->

    <?php if (!empty($fotoPerfil)): ?>

        <link
            rel="icon"
            href="<?= htmlspecialchars(
                $fotoPerfil,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            type="image/png">

    <?php else: ?>

        <link
            rel="icon"
            href="../img/logo.png"
            type="image/png">

    <?php endif; ?>


    <!-- ======================================================
         BOOTSTRAP
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- ======================================================
         FONT AWESOME
    ======================================================= -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <!-- ======================================================
         SWEETALERT2
    ======================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>


    <!-- ======================================================
         CSS SIDEBAR
    ======================================================= -->

    <link
        rel="stylesheet"
        href="../css/menu_sidebar.css">


    <!-- ======================================================
         CSS LISTA TESTIMONIOS
    ======================================================= -->

    <link
        rel="stylesheet"
        href="../css/adm_lista_testimonios.css">

</head>


<body>


    <div class="admin-layout">


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->

        <aside
            id="sidebar"
            class="admin-sidebar">


            <!-- =================================================
                 EMPRESA
            ================================================== -->

            <div class="sidebar-company">

                <div class="company-icon">

                    <?php if (!empty($fotoPerfil)): ?>

                        <img
                            src="<?= htmlspecialchars(
                                $fotoPerfil,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="Perfil">

                    <?php else: ?>

                        <i class="fa-solid fa-building"></i>

                    <?php endif; ?>

                </div>


                <div class="company-info">

                    <strong>

                        <?= htmlspecialchars(
                            $usuario['nombreEmpresa']
                                ?? 'Empresa',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </strong>

                    <span>
                        Panel administrativo
                    </span>

                </div>

            </div>


            <!-- =================================================
                 NAVEGACIÓN
            ================================================== -->

            <nav class="sidebar-navigation">


                <!-- =================================================
                     PRINCIPAL
                ================================================== -->

                <div class="sidebar-section-title">
                    PRINCIPAL
                </div>


                <!-- INICIO -->

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


                <!-- MENSAJES -->

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


                <!-- =================================================
                     GESTIÓN
                ================================================== -->

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

                    <i
                        class="fa-solid fa-chevron-down collapse-arrow">
                    </i>

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

                    <i
                        class="fa-solid fa-chevron-down collapse-arrow">
                    </i>

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

                    <i
                        class="fa-solid fa-chevron-down collapse-arrow">
                    </i>

                </a>


                <div
                    class="collapse show sidebar-submenu"
                    id="menuTestimonios">


                    <!-- VER TESTIMONIOS - ACTIVO -->

                    <a
                        href="adm_lista_testimonios.php"
                        class="sidebar-sublink active">

                        <i class="fa-solid fa-list"></i>

                        <span>
                            Ver testimonios
                        </span>

                    </a>


                    <!-- REGISTRAR -->

                    <a
                        href="adm_registrar_testimonio.php"
                        class="sidebar-sublink">

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


                <!-- PERFIL -->

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

                    <span class="sidebar-link-icon">

                        <i class="fa-solid fa-right-from-bracket"></i>

                    </span>

                    <span>
                        Cerrar sesión
                    </span>

                </a>


            </nav>


            <!-- =================================================
                 FOOTER SIDEBAR
            ================================================== -->

            <div class="sidebar-footer">

                <i class="fa-solid fa-shield-halved"></i>

                <span>
                    Sesión segura
                </span>

            </div>


        </aside>


        <!-- =====================================================
             OVERLAY
        ====================================================== -->

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


            <!-- =================================================
                 TOPBAR
            ================================================== -->

            <header class="admin-topbar">


                <div class="topbar-left">


                    <!-- BOTÓN MENÚ -->

                    <button
                        type="button"
                        id="toggleSidebar"
                        class="sidebar-toggle"
                        aria-label="Abrir menú">

                        <i class="fa-solid fa-bars"></i>

                    </button>


                    <!-- TÍTULO -->

                    <div class="topbar-title">

                        <span>
                            Panel administrativo
                        </span>

                        <strong>
                            Testimonios
                        </strong>

                    </div>


                </div>


                <div class="topbar-right">


                    <!-- =================================================
                         MENSAJES
                    ================================================== -->

                    <a
                        href="adm_mensajes.php"
                        class="topbar-icon"
                        title="Mensajes">

                        <i class="fa-regular fa-bell"></i>

                    </a>


                    <!-- =================================================
                         PERFIL
                    ================================================== -->

                    <a
                        href="adm_perfil.php"
                        class="topbar-profile">


                        <div class="topbar-avatar">

                            <?php if (!empty($fotoPerfil)): ?>

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

                        </div>


                        <div
                            class="topbar-user d-none d-md-flex">

                            <strong>

                                <?= htmlspecialchars(
                                    $usuario['nombreEmpresa']
                                        ?? 'Administrador',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>

                            <span>
                                Administrador
                            </span>

                        </div>


                        <i
                            class="fa-solid fa-chevron-down profile-arrow d-none d-md-block">
                        </i>


                    </a>


                </div>


            </header>


            <!-- =================================================
                 CONTENIDO PRINCIPAL
            ================================================== -->

            <div class="dashboard-content">


                <div class="page-content">


                    <!-- =================================================
                         CABECERA
                    ================================================== -->

                    <div class="page-header">

                        <span class="page-eyebrow">
                            GESTIÓN DE TESTIMONIOS
                        </span>

                        <h1>
                            Testimonios
                        </h1>

                        <p>
                            Administra las experiencias y opiniones
                            de los clientes registradas en el sitio web.
                        </p>

                    </div>


                    <!-- =================================================
                         RESUMEN / KPI
                    ================================================== -->

                    <div class="row g-3 mb-4">


                        <!-- =================================================
                             TOTAL
                        ================================================== -->

                        <div class="col-12 col-md-4">

                            <div class="testimonial-summary-card">


                                <div
                                    class="summary-icon green">

                                    <i
                                        class="fa-solid fa-comments">
                                    </i>

                                </div>


                                <div
                                    class="summary-info">

                                    <span>
                                        Testimonios registrados
                                    </span>


                                    <strong
                                        id="kpiTotalTestimonios">

                                        <span
                                            class="placeholder-glow">

                                            <span
                                                class="placeholder col-4">
                                            </span>

                                        </span>

                                    </strong>

                                </div>


                            </div>

                        </div>


                        <!-- =================================================
                             DISPONIBLES
                        ================================================== -->

                        <div class="col-12 col-md-4">

                            <div
                                class="testimonial-summary-card">


                                <div
                                    class="summary-icon blue">

                                    <i
                                        class="fa-solid fa-circle-plus">
                                    </i>

                                </div>


                                <div
                                    class="summary-info">

                                    <span>
                                        Espacios disponibles
                                    </span>


                                    <strong
                                        id="kpiTestimoniosDisponibles">

                                        <span
                                            class="placeholder-glow">

                                            <span
                                                class="placeholder col-3">
                                            </span>

                                        </span>

                                    </strong>

                                </div>


                            </div>

                        </div>


                        <!-- =================================================
                             ESTADO
                        ================================================== -->

                        <div class="col-12 col-md-4">

                            <div
                                class="testimonial-summary-card">


                                <div
                                    id="kpiEstadoIcono"
                                    class="summary-icon green">

                                    <i
                                        class="fa-solid fa-circle-check">
                                    </i>

                                </div>


                                <div
                                    class="summary-info">

                                    <span>
                                        Estado
                                    </span>


                                    <strong
                                        id="kpiEstadoTestimonios"
                                        class="summary-status">

                                        Cargando...

                                    </strong>

                                </div>


                            </div>

                        </div>


                    </div>


                    <!-- =================================================
                         ACCIONES SUPERIORES
                    ================================================== -->

                    <div
                        class="testimonios-toolbar">


                        <div>

                            <h2>
                                Lista de testimonios
                            </h2>


                            <span
                                id="toolbarCantidadTestimonios">

                                Cargando testimonios...

                            </span>

                        </div>


                        <!-- BOTÓN DINÁMICO -->

                        <div
                            id="toolbarAccionTestimonio">

                            <button
                                type="button"
                                class="btn btn-secondary btn-admin"
                                disabled>

                                <span
                                    class="spinner-border spinner-border-sm me-1"
                                    aria-hidden="true">
                                </span>

                                Cargando...

                            </button>

                        </div>


                    </div>


                    <!-- =================================================
                         LISTA / TABLA
                    ================================================== -->

                    <div class="form-panel">


                        <div
                            id="contenedorTestimonios">


                            <!-- =================================================
                                 ESTADO INICIAL DE CARGA
                            ================================================== -->

                            <div
                                class="testimonios-loading">


                                <div
                                    class="spinner-border text-success"
                                    role="status">

                                    <span
                                        class="visually-hidden">

                                        Cargando...

                                    </span>

                                </div>


                                <p class="mt-3 mb-0">

                                    Cargando testimonios...

                                </p>


                            </div>


                        </div>


                    </div>


                    <!-- =================================================
                         FOOTER
                    ================================================== -->

                    <footer
                        class="dashboard-footer">


                        <span>

                            © <?= date('Y') ?>

                            <?= htmlspecialchars(
                                $usuario['nombreEmpresa']
                                    ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>


                        <span>
                            Panel administrativo
                        </span>


                    </footer>


                </div>


            </div>


        </main>


    </div>


    <!-- ============================================================
         MODALES
    ============================================================ -->

    <?php include '../modal/modal_ver_testimonio.php'; ?>

    <?php include '../modal/modal_editar_testimonio.php'; ?>

    <?php include '../modal/modal_eliminar_testimonio.php'; ?>


    <!-- ============================================================
         BOOTSTRAP
    ============================================================ -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- ============================================================
         SIDEBAR
    ============================================================ -->

    <script
        src="../js/menu_sidebar.js">
    </script>


    <!-- ============================================================
         DATOS JS
    ============================================================ -->

    

    <!-- ============================================================
         JS LISTA TESTIMONIOS
    ============================================================ -->

    <script
        src="../js/adm_lista_testimonios.js">
    </script>


</body>

</html>
