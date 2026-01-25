<?php
session_start();
if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}
include '../controladores/conect_db.php';
require '../controladores/procesar_lista_asesores.php';
require '../controladores/editar_asesor.php';
$mensaje = "";
$tipoAlerta = "";
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
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asesores -— Panel</title>
    <!--Poner icono de la pagina web-->
    <link rel="icon" href="../img/logo.png" type="image/svg+xml" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/menu_sidebar.css">
    <!-- Font Awesome para iconos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex vh-100 overflow-hidden">
        <!-- Sidebar -->
          <nav id="sidebar" class="bg-dark text-white p-3 " style="width:250px;">
            <!--poner logo de la empresa-->
            <img src="../img/logo3.png" alt="Logo" class=" rounded mb-4" width="220" height="60">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center" href="adm_index.php">
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
                    <a class="nav-link text-info d-flex justify-content-between align-items-center"
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
                            <a class="nav-link text-info" href="adm_lista_asesores.php">
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
            <div id="content" class="container-fluid p-3">
                <!-- Barra de búsqueda y botón de exportar -->
                <div class="row g-2 align-items-center">
                    <!-- Buscador -->
                    <div class="col-12 col-md">
                        <div class="card p-2">
                            <form id="formBuscar" class="d-flex" method="GET" action="adm_lista_asesores.php">
                                <input
                                    id="inputBuscar"
                                    class="form-control me-2"
                                    type="search"
                                    name="buscar"
                                    placeholder="Buscar propiedad por codigo, nombre o fecha"
                                    value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                                <button class="btn btn-outline-success" type="submit">
                                    Buscar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Dropdown Exportar -->
                    <div class="col-12 col-md-auto text-md-end">
                        <div class="card p-2">
                            <div class="btn-group">
                                <button
                                    class="btn btn-secondary dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                    Exportar datos
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="../controladores/exportar_asesores_pdf.php" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Exportar PDF
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="../controladores/exportar_asesores_excel.php" target="_blank">
                                            <i class="fas fa-file-excel"></i> Exportar Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Tabla -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Asesores registrados: <?php echo $totalAsesores; ?></h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm w-100">

                                    <thead class="table-success text-center">
                                        <tr>
                                            <th>Acciones</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Email</th>
                                            <th>Celular</th>
                                            <th>Cargo</th>
                                            <th>Fecha</th>
                                            <th>Imagen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="text-truncate-custom">
                                                        <button
                                                            class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditar"
                                                            data-id="<?= $fila['id_asesor']; ?>"
                                                            data-nombre="<?= htmlspecialchars($fila['nombre']); ?>"
                                                            data-apellidos="<?= htmlspecialchars($fila['apellidos']); ?>"
                                                            data-email="<?= $fila['email']; ?>"
                                                            data-celular="<?= $fila['celular']; ?>"
                                                            data-cargo="<?= $fila['cargo']; ?>">
                                                            <i class="fas fa-pen-to-square"></i>
                                                        </button>


                                                        <!-- Botón para eliminar -->
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar" onclick="setEliminarId(<?php echo $fila['id_asesor']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <td class="text-truncate-custom"><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                                    <td class="text-truncate-custom"><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                                                    <td class="text-truncate-custom">
                                                        <?= htmlspecialchars($fila['email'] ?? 'Sin email'); ?>
                                                    </td>

                                                    <td class="text-truncate-custom"><?php echo htmlspecialchars($fila['celular']); ?></td>
                                                    <td class="text-truncate-custom"><?php echo htmlspecialchars($fila['cargo']); ?></td>

                                                    <td>
                                                        <?= date('d/m/Y', strtotime($fila['fecha_registro'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($fila['imagen']): ?>
                                                            <img src="data:image/jpeg;base64,<?= base64_encode($fila['imagen']); ?>"
                                                                width="50" height="50" />
                                                        <?php else: ?>
                                                            <i class="fas fa-box"></i> <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    No hay asesores registrados
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Mostrar el total de registro del la tabla, anterior, siguiente-->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

                    <div class="fw-bold text-success">
                        Página <?= $pagina ?> de <?= $totalPaginas ?>
                        | Total registros: <?= $totalAsesores; ?>
                    </div>

                    <div>
                        <!-- BOTÓN ANTERIOR -->
                        <?php if ($pagina > 1): ?>
                            <a class="btn btn-outline-success btn-sm me-2"
                                href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($busqueda) ?>">
                                ⬅ Anterior
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm me-2" disabled>
                                ⬅ Anterior
                            </button>
                        <?php endif; ?>

                        <!-- BOTÓN SIGUIENTE -->
                        <?php if ($pagina < $totalPaginas): ?>
                            <a class="btn btn-outline-success btn-sm"
                                href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($busqueda) ?>">
                                Siguiente ➡
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Siguiente ➡
                            </button>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- llamar modal_clientes.php -->
    <?php include '../modal/modal_editar_asesor.php'; ?>
    <script src="../js/lista_asesores.js"></script>
    <script src="../js/visualizar_imagen.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/menu_sidebar.js"></script>
</body>

</html>