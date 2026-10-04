<?php
require_once "controladores/index.php";

// Consulta para contar propiedades
$sqlAsesores = "SELECT COUNT(*) AS total FROM asesores WHERE estado = 'ACTIVO'
    ORDER BY nombre ASC";
$resultado1 = $conexion->query($sqlAsesores);
$fila0 = $resultado1->fetch_assoc();
$totalAsesores = $fila0['total'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Asesores - <?php echo $usuario['nombreEmpresa']; ?> </title>
    <!--Poner icono de la pagina web-->
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
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <!--Llamar a css/style.css-->
    <link rel="stylesheet" href="css/style.css" />
    <!-- Bootstrap Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome para iconos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Bootstrap Icons (OBLIGATORIO para los íconos) -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/asesores.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--llamar a js/buscar_propiedades.js-->
    <script src="js/buscar_asesores.js"></script>
</head>

<body>
    <!-- Barra superior de información -->
    <?php require "otros/barra_superior.php"; ?>

    <!-- Barra de navegación principal -->
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
                            class="nav-link"
                            href="propiedades.php">

                            Propiedades

                        </a>

                    </li>


                    <li class="nav-item ">

                        <a
                            class="nav-link active"
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

    <div class="container  py-2 mt-2">
        <!-- Barra de búsqueda y botón de exportar -->
        <div class="row g-2 align-items-center">
            <!-- Buscador -->
            <div class="col-12 col-md">
                <div class="card p-2">
                    <form id="formBuscar" class="d-flex">
                        <input
                            id="inputBuscar"
                            class="form-control me-2"
                            type="search"
                            placeholder="Buscar propiedad por nombre, apellidos, cargo, fecha">
                    </form>

                </div>
            </div>
        </div>
        <p class="card-title mb-0 py-2 mt-2 fw-bold">Se encontraron<?php echo $totalAsesores; ?> asesores inmobiliarios.</p>
    </div>
    <!-- llamar a otros/productos_index.php -->
    <?php include 'otros/asesores._lista.php'; ?>
    <!-- llamar a otros/footer.php -->
    <?php include 'otros/footer.php'; ?>
    <script>
        // Al cargar, animar texto; cuando termine, animar imagen
        window.addEventListener("DOMContentLoaded", () => {
            const text = document.querySelector(".hero-text");
            const image = document.querySelector(".hero-image");

            // Asegura que la imagen permanezca oculta y fuera mientras entra el texto
            image.style.visibility = "hidden";

            // Disparar animación del texto
            text.classList.add("animate-text");

            // Cuando el texto termina, mostrar y animar la imagen
            text.addEventListener(
                "animationend",
                () => {
                    image.style.visibility = "visible";
                    image.classList.add("animate-image");
                }, {
                    once: true
                }
            );
        });
    </script>
    
    <!--llamar a js/script.js-->
    <script src="js/chat.js"></script>
</body>

</html>