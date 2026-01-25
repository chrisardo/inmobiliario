<?php
require_once "controladores/conect_db.php";

$asesores = [];

$sql = "SELECT id_asesor, nombre, apellidos, celular 
        FROM asesores
        ORDER BY nombre ASC";

$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $asesores[] = $row;
    }
}
$sqlUsuario = "SELECT nombreEmpresa, ruc, fecha_registro, imagen , direccion, email, celular, estado, descripcion_acerca
               FROM usuario_acceso";
$stmt = $conexion->prepare($sqlUsuario);
//$stmt->bind_param("i", $_SESSION['usId']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$fotoPerfil = null;
if (!empty($usuario['imagen'])) {
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($usuario['imagen']);
}
// Consulta para contar propiedades
$sqlAsesores = "SELECT COUNT(*) AS total FROM asesores";
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

    <link rel="icon" href="img/logo.png" type="image/svg+xml" />
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
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-1 shadow-sm">
        <div class="container">
            <div class="d-flex flex-column">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <!--<img
              src="img/logo.png"
              alt="Logo"
              width="50"
              height="50" />-->
                    <?php if ($fotoPerfil): ?>
                        <img src="<?= $fotoPerfil ?>" class="rounded-circle border-success" width="44" height="44">
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-2x"></i>
                    <?php endif; ?>
                </a>
                <small class="text-muted"><?php ?><?php //echo utf8_decode($usuario['nombreEmpresa']); 
                                                    ?></small>
            </div>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a
                            class="nav-link fw-medium text-dark px-3  active"
                            href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link fw-medium text-dark px-3"
                            href="nosotros.php">Conócenos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark px-3" href="propiedades.php">Propiedades</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-success fw-bold fw-medium px-3" href="asesores.php">
                            Asesores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark px-3" href="contacto.php">Contacto</a>
                    </li>
                </ul>
                <!--Agregar boton de login-->
                <div class="ms-3">
                    <a href="login.php" target="_blank" class="btn btn-success btn-sm px-4">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </nav>
    </div>
    <br /><br />
    <!-- Encabezado estilo Bootstrap -->
    <header class="bg-dark text-white py-4 mt-4">
        <div class="container">
            <div class="row align-items-center">
                <!-- Título a la izquierda -->
                <div class="col-12 col-md-6">
                    <h1 class="h4 fw-bold mb-0">Propiedades</h1>
                </div>
                <!-- Breadcrumb a la derecha -->
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-md-end mb-0">
                            <li class="breadcrumb-item">
                                <a href="/" class="text-white text-decoration-none">HOME</a>
                            </li>
                            <li class="breadcrumb-item">></li>

                            <li
                                class="breadcrumb-item active text-white"
                                aria-current="page">
                                PROPIEDADES
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

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
        <p class="card-title mb-0 py-2 mt-2 fw-bold">Resultado: <?php echo $totalAsesores; ?> asesores inmobiliarios.</p>
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
    <script>
        $(function() {

            /* ===============================
               ABRIR CHAT
            =============================== */
            $("#chatButton").on("click", function() {
                $("#chatButtonContainer").hide();
                $("#chatFormContainer").removeClass("d-none");
            });

            /* ===============================
               CERRAR CHAT
            =============================== */
            $("#closeChatForm").on("click", function() {
                $("#chatFormContainer").addClass("d-none");
                $("#chatButtonContainer").show();
            });

            /* ===============================
               ENVIAR WHATSAPP
            =============================== */
            $("#chatForm").on("submit", function(e) {
                e.preventDefault();

                const nombre = $("#chat_nombre").val().trim();
                const mensaje = $("#chat_mensaje").val().trim();
                const asesorTelefono = $("#chat_asesor").val();
                const asesorNombre = $("#chat_asesor option:selected").data("nombre");

                /* ===============================
                   VALIDACIONES
                =============================== */
                if (!asesorTelefono) {
                    alert("⚠️ Seleccione un asesor.");
                    return;
                }

                if (nombre.length < 3) {
                    alert("⚠️ Ingrese su nombre.");
                    return;
                }

                if (mensaje.length < 0) {
                    alert("⚠️ Escriba un mensaje.");
                    return;
                }

                if (!/^[0-9]{9}$/.test(asesorTelefono)) {
                    alert("⚠️ Número de WhatsApp inválido.");
                    return;
                }

                /* ===============================
                   MENSAJE FORMAL
                =============================== */
                const empresa = "Mi Inmobiliaria";

                const texto = `
${mensaje}
        `.trim();

                /* ===============================
                   ABRIR WHATSAPP
                =============================== */
                const url = `https://api.whatsapp.com/send?phone=51${asesorTelefono}&text=${encodeURIComponent(texto)}`;
                window.open(url, "_blank");
            });

        });
    </script>
    <!--llamar a js/script.js-->
    <script src="js/script.js"></script>
</body>

</html>