<?php
session_start();
if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}
include '../controladores/conect_db.php';
include '../controladores/procesar_registro_producto.php';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/menu_sidebar.css">
    <script src="../js/numero.js"></script>
</head>

<body>
    <div class="d-flex vh-100 overflow-hidden">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white p-3 " style="width:250px;">
            <!--poner logo de la empresa-->
            <img src="../img/logo3.png" alt="Logo" class=" rounded mb-4" width="220" height="70">
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
                            <a class="nav-link text-info" href="adm_registrar_propiedad.php">
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
                <h2 class="mb-3">Registrar propiedad</h2>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Imagen -->
                            <div class="mb-2">
                                <div class="card border-0 shadow-sm">
                                    <!-- Input -->
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-image-fill"></i>
                                        </span>

                                        <input
                                            type="file"
                                            name="imagen"
                                            id="imagen"
                                            class="form-control"
                                            accept="image/png, image/jpeg">
                                    </div>

                                    <div class="form-text">
                                        Formatos permitidos: JPG, PNG · Tamaño máximo: 1.8 MB
                                    </div>
                                    <div class="card-body p-2">
                                        <!-- Vista previa -->
                                        <div id="previewImagen" class="mt-0 d-none">
                                            <div class="row align-items-center g-3">

                                                <!-- Imagen -->
                                                <div class="col-auto">
                                                    <div class="border rounded p-2 bg-light">
                                                        <img
                                                            id="previewImg"
                                                            class="img-fluid rounded"
                                                            style="width: 70px; height: 60px; object-fit: cover;">
                                                    </div>
                                                </div>

                                                <!-- Detalles -->
                                                <div class="col">
                                                    <ul class="list-group list-group-flush small">
                                                        <!--<li class="list-group-item px-0">
                                                        <i class="bi bi-file-earmark-text text-success me-2"></i>
                                                        <strong>Nombre:</strong>
                                                        <span id="imgNombre"></span>
                                                    </li>-->
                                                        <li class="list-group-item px-0">
                                                            <i class="bi bi-aspect-ratio text-info me-2"></i>
                                                            <strong>Tipo:</strong>
                                                            <span id="imgTipo"></span>
                                                        </li>
                                                        <li class="list-group-item px-0">
                                                            <i class="bi bi-hdd text-warning me-2"></i>
                                                            <strong>Tamaño:</strong>
                                                            <span id="imgSize"></span>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <label for="codigo" class="form-label">CÓDIGO</label>
                                    <div class="input-group">
                                        <!--codigo del producto-->
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-upc-scan"></i>
                                        </span>
                                        <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Codigo de la propiedad" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="producto" class="form-label">Propiedad</label>
                                    <div class="input-group">
                                        <!--nombre del producto-->
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-house-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Propiedad" required>
                                    </div>
                                </div>
                            </div>
                            <!--prcio venta + precio compra -->
                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <label for="costo" class="form-label">Tamaño del Area</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="tamano_area" name="tamano_area" placeholder="Area de la propiedad" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="precio" class="form-label">Precio a vender</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-currency-dollar"></i>
                                        </span>

                                        <input type="number" step="0.01" min="0" id="precio_venta"
                                            class="form-control" name="precio_venta"
                                            placeholder="Precio de venta" required>
                                    </div>
                                </div>
                            </div>

                            <!-- opciones de categoria + opciones de departamento -->
                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <label for="categoria" class="form-label">Categoria</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-grid-fill"></i>
                                        </span>
                                        <!--poner un select con opciones de rubro y mostrar los rubros de la base de datos-->
                                        <?php
                                        $sql = "SELECT id_categoria, nombre, id_user FROM categoria where Eliminado = 0 AND id_user=" . intval($_SESSION['usId']) . "";
                                        $resultado = $conexion->query($sql);
                                        ?>
                                        <select class="form-select" id="categoria" name="categoria" required>
                                            <option value="" disabled selected>Selecciona</option>
                                            <?php
                                            if ($resultado->num_rows > 0) {
                                                while ($fila = $resultado->fetch_assoc()) {
                                                    echo '<option value="' . $fila['id_categoria'] . '">' . $fila['nombre'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="ubicacion" class="form-label">Ubicación / Zona</label>
                                    <div class="input-group">
                                        <!--nombre del producto-->
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ubicación" required>
                                    </div>
                                </div>
                            </div>
                            <!--Boton registrar-->
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Registrar</button>
                        </form>
                    </div>

                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert">
                            <?= $mensaje ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    <?php
                    endif;
                    $conexion->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/visualizar_imagen.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/menu_sidebar.js"></script>
</body>

</html>