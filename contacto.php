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
$sqlPropiedades = "SELECT COUNT(*) AS total FROM propiedades";
$resultado1 = $conexion->query($sqlPropiedades);
$fila0 = $resultado1->fetch_assoc();
$totalPropiedades = $fila0['total'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Index - <?php echo $usuario['nombreEmpresa']; ?></title>
  <!--Poner icono de la pagina web-->
  <link rel="icon" href="img/logo.png" type="image/svg+xml" />
  <!-- Open Graph (usado por Facebook, WhatsApp, Instagram, Telegram, TikTok, LinkedIn, etc.) -->
  <meta property="og:title" content="Inmobiliaria - Bienvenido" />
  <meta property="og:description" content="    Empresa de protección contra incendios y sistemas de seguridad
                desde un punto de vista global: Ingeniería, Instalación y
                Mantenimiento." />
  <meta property="og:image" content="https://extindustria.infinityfreeapp.com/img/Logo_Extindustria.svg" />
  <meta property="og:url" content="https://extindustria.infinityfreeapp.com/" />
  <meta property="og:type" content="website" />

  <!-- Twitter Cards (usado por Twitter/X y también reconocido por algunas apps) -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Extindustria - Bienvenido" />
  <meta name="twitter:description" content="Empresa de protección contra incendios y sistemas de seguridad
                desde un punto de vista global: Ingeniería, Instalación y
                Mantenimiento." />
  <meta name="twitter:image" content="https://extindustria.infinityfreeapp.com/img/Logo_Extindustria.svg" /> <!-- Bootstrap CSS -->
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap Icons (OBLIGATORIO para los íconos) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="css/producto.css" />
  <!--llamar a js/buscar_propiedades.js-->
  <!--<script src="js/buscar_propiedades.js"></script>-->
</head>

<body>
  <?php require "otros/barra_superior.php";?>
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
              <a class="nav-link fw-medium px-3" href="asesores.php">
                Asesores
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-medium text-success fw-bold px-3" href="contacto.php">Contacto</a>
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

  <!-- llamar a otros/form_contacto.php -->
  <?php include 'otros/form_contacto.php'; ?>

  <!-- llamar a otros/footer.php -->
  <?php include 'otros/footer.php'; ?>
  <!-- ================= JS ================= -->
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>