<?php
//Toda esto es adm/adm_index.php
session_start();
//llamar al procesador de index
include '../controladores/procesar_index.php';

// include 'controladores/procesar_dashboards_index.php'
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Index — Panel</title>
    <!--Poner icono de la pagina web-->
    <link rel="icon" href="../img/logo.png" type="image/svg+xml" />
    <!-- Font Awesome para iconos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/menu_sidebar.css">
    <script src="../js/numero.js"></script>
</head>

<body>
    <div class="d-flex vh-100 overflow-hidden">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white p-3 " style="width:250px;">
            <!--poner logo de la empresa-->
            <img src="../img/logo3.png" alt="Logo" class=" rounded mb-4" width="220" height="60">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-info d-flex align-items-center" href="adm_index.php">
                        <i class="fas fa-home me-2"></i>
                        <span class="item-text">Inicio</span>
                    </a>

                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="adm_mensajes.php">
                        <i class="fa-solid fa-envelope me-2"></i>
                        Mensajes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse"
                        href="#menuInventario"
                        role="button"
                        aria-expanded="false">
                        <span>
                            <i class="fa-solid fa-building me-2"></i>Propiedades
                        </span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>

                    <ul class="collapse list-unstyled ps-4" id="menuInventario">
                        <li>
                            <a class="nav-link text-secondary" href="adm_lista_propiedades.php">
                                <i class="fa-solid fa-list me-2"></i> Ver propiedades
                            </a>
                        </li>
                        <li>
                            <a class="nav-link text-secondary" href="adm_registrar_propiedad.php">
                                <i class="fa-solid fa-circle-plus me-2"></i> Registrar propiedad
                            </a>
                        </li>
                        <li>
                            <a class="nav-link text-secondary" href="adm_categorias.php">
                                <!--Poner icono de categorias-->
                                <i class="fas fa-th-large"></i> Categorías
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse"
                        href="#menuClientes"
                        role="button"
                        aria-expanded="false">
                        <span>
                            <i class="fa-solid fa-user-tie me-2"></i>
                            Asesores
                        </span>
                        <i class="fa-solid fa-chevron-down small"></i>
                    </a>

                    <ul class="collapse list-unstyled ps-4" id="menuClientes">
                        <li>
                            <a class="nav-link text-secondary" href="adm_registrar_asesor.php">
                                <i class="fa-solid fa-user-plus me-2"></i>
                                Registrar asesor
                            </a>
                        </li>
                        <li>
                            <a class="nav-link text-secondary" href="adm_lista_asesores.php">
                                <i class="fa-solid fa-users me-2"></i>
                                Lista de asesores
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../controladores/desconectar.php">
                        <!--Poner icono de cerrar sesion-->
                        <i class="fas fa-sign-out-alt"></i>
                        Cerrar sesión
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Contenido principal -->
        <div id="content">
            <!-- Navbar superior FIXED -->
            <nav class="navbar bg-light border-bottom">
                <div class="container-fluid d-flex align-items-center">
                    <!-- Botón sidebar -->
                    <button id="toggleSidebar" class="btn btn-dark me-3  d-lg-none">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Título -->
                    <span class="navbar-brand mb-0">
                        Panel de control
                    </span>

                    <!-- Menú derecho (SIEMPRE visible) -->
                    <ul class="navbar-nav d-flex flex-row align-items-center ms-auto gap-3">

                        <li class="nav-item">
                            <a class="nav-link p-0" href="#">
                                <i class="fa-solid fa-bell fa-lg"></i>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 p-0" href="adm_perfil.php">
                                <?php if ($fotoPerfil): ?>
                                    <img src="<?= $fotoPerfil ?>" class="rounded-circle border-success" width="34" height="34">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x"></i>
                                <?php endif; ?>
                                <span class="d-none d-md-inline">

                                </span>
                            </a>
                        </li>

                    </ul>

                </div>
            </nav>


            <!-- Contenido -->
            <div class="container-fluid p-3">
                <!-- Título -->
                <p class="fs-3 lh-base mb-3 fw-bold">Bienvenido: <?php echo utf8_decode($usuario['nombreEmpresa']); ?></p>
                <div class="row g-3">
                    <div class="col-4 col-md-4 ">
                        <div class="card kpi-card border-success mb-3 h-100" style="max-width: 18rem;">
                            <div class="card-header text-black fw-bold bg-info text-center">Total Mensajes</div>
                            <div class="card-body">
                                <p class="card-text fs-4 lh-base  text-center">
                                    <span class="kpi-number" data-value="<?php echo number_format($totalContacto, 2); ?>">0</span>
                                </p>

                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4">
                        <div class="card kpi-card border-success h-100 mb-3" style="max-width: 18rem;">
                            <div class="card-header text-white bg-success text-center">Total Propiedades</div>
                            <div class="card-body">
                                <p class="card-text fs-4 lh-base  text-center">
                                    <span class="kpi-number" data-value="<?php echo $totalPropiedades; ?>">0</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col- col-md-4">
                        <div class="card kpi-card border-success h-100 mb-3" style="max-width: 18rem;">
                            <div class="card-header text-white bg-success text-center">Total Asesores</div>
                            <div class="card-body">
                                <p class="card-text fs-4 lh-base  text-center">
                                    <span class="kpi-number" data-value="<?php echo $totalAsesores; ?>">0</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/menu_sidebar.js"></script>
</body>

</html>