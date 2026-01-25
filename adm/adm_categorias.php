<?php
session_start();
if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}
include '../controladores/procesar_categorias.php';
// ✅ EVITAR ERROR DE VARIABLE INDEFINIDA
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
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
                    <a class="nav-link text-info d-flex justify-content-between align-items-center"
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
                            <a class="nav-link text-info" href="adm_categorias.php">
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
            <div class="container-fluid p-4">

                <div class="card">
                    <div class="card-body">
                        <!--Al darle click al boton de editar debe cargar el rubro en el formulario, para eso verificar si existe el id en la url-->
                        <?php
                        if (isset($_GET['id'])) {
                            $id_categoria = intval($_GET['id']);
                            $resultadoEditar = $conexion->query("SELECT * FROM categoria WHERE id_categoria  = $id_categoria");
                            if ($filaEditar = $resultadoEditar->fetch_assoc()) {
                                $nombreCategoria = $filaEditar['nombre'];
                            } else {
                                echo "<div class='alert alert-danger'>Rubro no encontrado.</div>";
                                $nombreCategoria = "";
                            }
                        } else {
                            $nombreCategoria = "";
                        }
                        ?>
                        <div class="card mb-2">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?php echo isset($_GET['id']) ? 'Editar Categoria' : 'Registrar Nueva Categoria'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre de la categoria</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombreCategoria); ?>" required>
                                    </div>
                                    <?php if (isset($_GET['id'])): ?>
                                        <input type="hidden" name="id_categorias" value="<?php echo intval($_GET['id']); ?>">
                                        <button type="submit" name="accion" value="editar" class="btn btn-primary">Guardar Cambios</button>
                                        <!--Boton para cancelar la edicion y volver al formulario de registro-->
                                        <a href="adm_categorias.php" class="btn btn-secondary">Cancelar</a>
                                    <?php else: ?>
                                        <button type="submit" name="accion" value="registrar" class="btn btn-success">Registrar</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert">
                                <?= $mensaje ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Contenido -->
                <div class="container-fluid p-2">
                    <!-- Tabla -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Categorias registradas: <?php echo $totalCategorias; ?></h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                                <tr>

                                                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                                    <td>
                                                        <!--Boton para editar-->
                                                        <a href="adm_categorias.php?id=<?php echo $fila['id_categoria']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-pen-to-square"></i>
                                                        </a>
                                                        <!-- Botón para eliminar -->
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar" onclick="setEliminarId(<?php echo $fila['id_categoria']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>


                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <!--Mostrar el total de registro del la tabla, anterior, siguiente-->
                                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                        <div class="fw-bold text-success">
                                            Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?>
                                        </div>

                                        <!-- Botones de paginación -->
                                        <div>
                                            <?php if ($pagina > 1): ?>
                                                <a class="btn btn-outline-success btn-sm me-2"
                                                    href="?pagina=<?php echo $pagina - 1; ?>&buscar=<?php echo urlencode($busqueda); ?>">
                                                    ⬅ Anterior
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-success btn-sm me-2" disabled>⬅ Anterior</button>
                                            <?php endif; ?>

                                            <?php if ($pagina < $totalPaginas): ?>
                                                <a class="btn btn-outline-success btn-sm"
                                                    href="?pagina=<?php echo $pagina + 1; ?>&buscar=<?php echo urlencode($busqueda); ?>">
                                                    Siguiente ➡
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-success btn-sm" disabled>Siguiente ➡</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal de confirmación -->
            <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar esta categoria?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="#" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</a>
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
     <script src="../js/categorias.js"></script>
</body>

</html>