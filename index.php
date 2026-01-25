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
                class="nav-link fw-medium text-success px-3 fw-bold active"
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
  <!-- Carousel Section -->
  <section>
    <div id="carouselExampleIndicators" class="carousel slide">
      <div class="carousel-indicators">
        <button
          type="button"
          data-bs-target="#carouselExampleIndicators"
          data-bs-slide-to="0"
          class="active"
          aria-current="true"
          aria-label="Slide 1"></button>
        <button
          type="button"
          data-bs-target="#carouselExampleIndicators"
          data-bs-slide-to="1"
          aria-label="Slide 2"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img
            src="img/carrusel2.jpg"
            class="d-block w-100"
            height="480"
            alt="..." />
          <div class="carousel-caption d-none d-md-block">
            <h1 class="text-info">Venta de departamentos e inmuebles en Iquitos</h1>
            <p>
              <span class="badge">
                Inmobiliaria especializada en la venta de terrenos de campo y urbanos.<br>
                Ofrecemos oportunidades seguras en Iquitos y sus alrededores, con asesoría confiable y atención personalizada.
              </span>
            </p>

            <a href="asesores.php" class="btn btn-success btn-lg px-4">Asesores</a>
            <a
              href="propiedades.php"
              class="btn btn-outline-info btn-lg px-4">Buscar propiedades</a>
          </div>
        </div>
        <div class="carousel-item">
          <img
            src="img/carrusel4.avif"
            class="d-block w-100"
            height="480"
            alt="..." />
        </div>
      </div>
      <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#carouselExampleIndicators"
        data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#carouselExampleIndicators"
        data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>
  <div class="container section-container">
    <div
      class="row align-items-center"
      style="
      background-image: url('img/fondo1.png');
      background-size: cover;
      background-position: center;
    ">
      <!-- Imagen -->
      <div class="col-md-6 mb-4 mb-md-0">
        <img
          src="img/carrusel3.jpeg"
          alt="Venta de terrenos en Iquitos"
          class="img-fluid rounded img-thumbnail" />
      </div>

      <!-- Texto -->
      <div class="col-md-6">
        <h2 class="section-title mb-3 fs-3">
          Venta de terrenos en Iquitos <br />
          para inversión y vivienda
        </h2>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="securityTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              class="nav-link active"
              id="terrenos-tab"
              data-bs-toggle="tab"
              data-bs-target="#terrenos"
              type="button"
              role="tab"
              aria-controls="terrenos"
              aria-selected="true">
              Terrenos en venta
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="proyectos-tab"
              data-bs-toggle="tab"
              data-bs-target="#proyectos"
              type="button"
              role="tab"
              aria-controls="proyectos"
              aria-selected="false">
              Proyectos inmobiliarios
            </button>
          </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content mt-3" id="securityTabsContent">
          <!-- Tab 1 -->
          <div
            class="tab-pane fade show active"
            id="terrenos"
            role="tabpanel"
            aria-labelledby="terrenos-tab">
            <div class="feature-list">
              <div class="feature-item">
                <i class="fas fa-file-contract text-success me-2"></i>
                Terrenos titulados e inscritos en SUNARP
              </div>
              <div class="feature-item">
                <i class="fas fa-map-marker-alt text-success me-2"></i>
                Ubicaciones estratégicas en zonas de crecimiento
              </div>
              <div class="feature-item">
                <i class="fas fa-hand-holding-usd text-success me-2"></i>
                Precios accesibles y planes de pago
              </div>
              <div class="feature-item">
                <i class="fas fa-shield-alt text-success me-2"></i>
                Compra segura y sin riesgos legales
              </div>
              <div class="feature-item">
                <i class="fas fa-user-check text-success me-2"></i>
                Asesoría personalizada durante todo el proceso
              </div>
            </div>
          </div>

          <!-- Tab 2 -->
          <div
            class="tab-pane fade"
            id="proyectos"
            role="tabpanel"
            aria-labelledby="proyectos-tab">
            <div class="feature-list">
              <div class="feature-item">
                <i class="fas fa-home text-success me-2"></i>
                Proyectos habilitados para vivienda
              </div>
              <div class="feature-item">
                <i class="fas fa-chart-line text-success me-2"></i>
                Alta valorización para inversión
              </div>
              <div class="feature-item">
                <i class="fas fa-map text-success me-2"></i>
                Lotes delimitados y listos para entrega
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Productos section-->
  <div id="resultadoPropiedades" class="container-fluid bg-secondary-subtle position-relative">
    <div class="container my-0 mb-3 py-3 position-relative">
      <div class="row mb-3">
             <div class="col text-center">
                 <h2 class="fw-bold text-dark">Propiedades Destacados</h2>
             </div>
         </div>
      <div class="row g-3">
        <?php
        //Lamar a la conexion
        include 'controladores/conect_db.php';
        // Consulta para obtener los 4 productos más recientes
        $resultado = $conexion->query("SELECT p.*, c.nombre as noombre_categoria 
                      FROM propiedades p inner join categoria c on p.id_categoria = c.id_categoria order by id_propiedad ASC LIMIT 8");
        ?>
        <?php if (mysqli_num_rows($resultado) > 0): ?>

          <!-- AQUÍ VA TU BUCLE DE PROPIEDADES -->
          <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>

            <div class="col-md-3 mb-3">
              <div class="card card-hover h-100 shadow-sm  border-success">
                <div class="position-relative">
                  <?php if (!empty($fila['imagen'])): ?>
                    <?php $imagenBinaria = base64_encode($fila['imagen']); ?>
                    <img
                      src="data:image/jpeg;base64,<?= $imagenBinaria ?>"
                      class="card-img-top"
                      alt="Propiedad" />
                  <?php else: ?>
                    <img
                      src="img/producto.png"
                      class="card-img-top"
                      alt="Propiedad sin imagen" />
                  <?php endif; ?>

                  <!-- PRECIO -->
                  <span class="badge-precio">
                    Desde S/. <?= number_format($fila['precio'], 0) ?>
                  </span>
                  <span class="badge-estado">
                    ID: <?= $fila['codigo'] ?>
                  </span>

                </div>
                <div class="card-body">
                  <?php
                  $mensajeWhatsapp = urlencode(
                    "Estimado/a,\n\n" .
                      "Me comunico para solicitar información sobre la siguiente propiedad:\n\n" .
                      "Código: {$fila['codigo']}\n" .
                      "Nombre: {$fila['nombre']}\n" .
                      "Ubicación: {$fila['ubicacion']}\n" .
                      "Precio: S/. " . number_format($fila['precio'], 0) . "\n\n" .
                      "Quedo atento/a a su respuesta.\nMuchas gracias."
                  );

                  // NÚMERO DE WHATSAPP (del sistema o empresa)
                  //$telefonoWhatsapp = "51" . preg_replace('/\D/', '', $usuario['celular'] ?? '999999999');
                  //$telefonoWhatsapp = "51". $usuario['celular'];
                  ?>
                  <h5 class="card-title text-success"><?= $fila['nombre'] ?></h5>
                  <p class="card-text">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <?= $fila['ubicacion'] ?>
                  </p>
                  <p class="card-text">
                    <i class="bi bi-arrows-fullscreen"></i>
                    <?= $fila['tamano_area_metros'] ?> m2 | <i class="bi bi-grid-fill"></i>
                    <?= $fila['noombre_categoria'] ?>
                  </p>
                  <!-- BOTÓN WHATSAPP -->
                  <a
                    href="https://wa.me/51<?= $usuario['celular'] ?>?text=<?= $mensajeWhatsapp ?>"
                    target="_blank"
                    class="btn btn-success w-100 mt-2">
                    <i class="bi bi-whatsapp"></i> Cotizar
                  </a>
                </div>

              </div>
            </div>

          <?php endwhile; ?>

        <?php else: ?>

          <!-- MENSAJE CUANDO NO HAY PROPIEDADES -->
          <div class="col-12">
            <div class="alert alert-warning text-center py-4">
              <i class="fas fa-home fa-2x mb-2 text-success"></i>
              <h5 class="mt-2">No hay propiedades registradas</h5>
              <p class="mb-0">
                Actualmente no contamos con propiedades disponibles.
                Por favor, vuelve a visitarnos pronto.
              </p>
            </div>
          </div>

        <?php endif; ?>
      </div>
      <!--Poner boton en el centro de "Ver detalle de cada servicio"-->
      <div class="text-center mt-4">
        <a href="propiedades.php" class="btn btn-success btn-lg px-4">Ver más</a>
      </div>
    </div>
  </div>
  <!-- Servicios Section -->
  <div
    class="container-fluid  position-relative"
    style="
    background-image: url('img/carrusel1.jpg');
    background-size: cover;
    background-position: center;
  ">

    <!-- Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
    <div class="container my-0 py-5 position-relative">

      <!-- Título -->
      <div class="row mb-3">
        <div class="col text-center text-white">
          <h2 class="fw-bold">¿Por qué elegirnos como su mejor opción?</h2>
          <!--<p class="mt-3">
            Brindamos soluciones inmobiliarias seguras y confiables en la venta
            de terrenos y proyectos en Iquitos.
          </p>-->
        </div>
      </div>

      <!-- Servicios -->
      <div class="row g-4">

        <!-- Servicio 1 -->
        <div class="col-md-4">
          <div class="card card-hover h-100 border-0 shadow-lg">
            <div class="card-body text-center p-4">

              <div class="icon-service mb-3">
                <i class="fas fa-file-contract"></i>
              </div>

              <h5 class="fw-bold">Venta de terrenos titulados</h5>
              <p class="text-muted small">
                Terrenos con documentación en regla, inscritos en SUNARP y listos para transferir.
              </p>

            </div>
          </div>
        </div>

        <!-- Servicio 2 -->
        <div class="col-md-4">
          <div class="card card-hover h-100 border-0 shadow-lg">
            <div class="card-body text-center p-4">

              <div class="icon-service mb-3">
                <i class="fas fa-gavel"></i>
              </div>

              <h5 class="fw-bold">Asesoría legal inmobiliaria</h5>
              <p class="text-muted small">
                Acompañamiento legal completo para una compra segura y transparente.
              </p>

            </div>
          </div>
        </div>

        <!-- Servicio 3 -->
        <div class="col-md-4">
          <div class="card card-hover h-100 border-0 shadow-lg">
            <div class="card-body text-center p-4">

              <div class="icon-service mb-3">
                <i class="fas fa-chart-line"></i>
              </div>

              <h5 class="fw-bold">Terrenos para inversión</h5>
              <p class="text-muted small">
                Lotes estratégicos con alto potencial de valorización en Iquitos.
              </p>

            </div>
          </div>
        </div>

        <!-- Servicio 4 -->
        <div class="col-md-4">
          <div class="card card-hover h-100 border-0 shadow-lg">
            <div class="card-body text-center p-4">

              <div class="icon-service mb-3">
                <i class="fas fa-hand-holding-usd"></i>
              </div>

              <h5 class="fw-bold">Planes de pago flexibles</h5>
              <p class="text-muted small">
                Facilidades de pago adaptadas a tu presupuesto y necesidades.
              </p>

            </div>
          </div>
        </div>

        <!-- Servicio 5 -->
        <div class="col-md-4">
          <div class="card card-hover h-100 border-0 shadow-lg">
            <div class="card-body text-center p-4">

              <div class="icon-service mb-3">
                <i class="fas fa-map-marker-alt"></i>
              </div>

              <h5 class="fw-bold">Ubicación y evaluación</h5>
              <p class="text-muted small">
                Evaluamos ubicación, accesos y proyección de cada terreno.
              </p>

            </div>
          </div>
        </div>

        <!-- Servicio 6 -->
        <div class="col-md-4">
          <div class="card card-hover h-100 border-0 shadow-lg">
            <div class="card-body text-center p-4">

              <div class="icon-service mb-3">
                <i class="fas fa-city"></i>
              </div>

              <h5 class="fw-bold">Proyectos inmobiliarios</h5>
              <p class="text-muted small">
                Proyectos planificados para vivienda e inversión listos para desarrollar.
              </p>

            </div>
          </div>
        </div>

      </div>

      <!-- Botón -->
      <!--<div class="text-center mt-5">
        <a href="servicios.php" class="btn btn-danger btn-lg px-5">
          Ver todos los servicios
        </a>
      </div>-->

    </div>
  </div>


  <!-- Stats Section -->
  <section class="border-top border-bottom bg-white py-4">
    <div class="container">
      <div class="row align-items-center text-center text-md-start">

        <!-- Precio -->
        <div class="col-md-3 border-end">
          <div class="fw-semibold text-dark">Desde</div>
          <div class="fw-bold fs-4">
            S/. 656,258<sup>*</sup>
          </div>
        </div>

        <!-- Ubicación -->
        <div class="col-md-3 border-end">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-geo-alt fs-2 text-secondary"></i>
            <div>
              <div class="small text-uppercase text-secondary fw-semibold">
                Ubicación
              </div>
              <div class="small text-dark">
                <?php echo $usuario['direccion']; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Dormitorios -->
        <div class="col-md-3 border-end">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-house-door fs-2 text-secondary"></i>
            <div>
              <div class="small text-uppercase text-secondary fw-semibold">
                Propiedades
              </div>
              <div class="small text-dark">
                <?php echo $totalPropiedades; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Metraje -->
        <div class="col-md-3">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-arrows-fullscreen fs-2 text-secondary"></i>
            <div>
              <div class="small text-uppercase text-secondary fw-semibold">
                Metraje total
              </div>
              <div class="small text-dark">
                Desde 68 m²
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>



  <section class="cta-section bg-dark text-white py-5">
    <div
      class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
      <h2 class="cta-text mb-3 mb-md-0 text-center text-md-start">
        Cuéntanos sobre <strong>TU próximo lugar ideal</strong>
      </h2>
      <a href="contacto.php">
        <button class="btn btn-success btn-lg px-4">Cotiza</button>
      </a>
    </div>
  </section>
  <!-- Nuestros espacios Section -->
  <section class="clientes-section container py-4">
    <div class="row align-items-center">
      <!-- Texto -->
      <div class="col-md-6 mb-4 mb-md-0">
        <h6 class="text-danger">Nuestros espacios</h6>
        <h2 class="fw-bold">Áreas comunes</h2>
        <p>
          Nuestros proyectos cuentan con áreas comunes pensadas para el confort,
          la seguridad y la convivencia, integrando espacios funcionales que
          aportan valor y calidad de vida a cada propiedad.
        </p>
      </div>
      <!-- Carrusel -->
      <div class="col-md-12">
        <div
          id="clientesCarousel"
          class="carousel slide"
          data-bs-ride="carousel">
          <!-- Indicadores -->
          <div class="carousel-indicators">
            <button
              type="button"
              data-bs-target="#clientesCarousel"
              data-bs-slide-to="0"
              class="active"
              aria-current="true"
              aria-label="Slide 1"
              class="bg-danger"></button>
            <button
              type="button"
              data-bs-target="#clientesCarousel"
              data-bs-slide-to="1"
              aria-label="Slide 2"
              class="bg-danger"></button>
            <button
              type="button"
              data-bs-target="#clientesCarousel"
              data-bs-slide-to="2"
              aria-label="Slide 3"
              class="bg-danger"></button>
          </div>

          <!-- Slides -->
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img
                src="img/carrusel1.jpg"
                class="d-inline-block"
                width="32%"
                height="32%"
                alt="Cliente 1" />
              <img
                src="img/carrusel2.jpg"
                class="d-inline-block"
                width="32%"
                height="32%"
                alt="Cliente 2" />
              <img
                src="img/carrusel3.jpeg"
                class="d-inline-block"
                width="32%"
                height="32%"
                alt="Cliente 3" />
            </div>
            <!--<div class="carousel-item">
              <img
                src="img/logo_rey.png"
                class="d-inline-block"
                alt="Cliente 4" />
              <img
                src="img/logo_perufarma.png"
                class="d-inline-block"
                alt="Cliente 5" />
              <img
                src="img/logo_Famesa.png"
                class="d-inline-block"
                alt="Cliente 6" />
            </div>
            <div class="carousel-item">
              <img
                src="img/logo_victaulic.png"
                class="d-inline-block"
                alt="Cliente 7" />
              <img
                src="img/logo_metrocolor.png"
                class="d-inline-block"
                alt="Cliente 8" />
              <img
                src="img/logo_ shell.jpg"
                class="d-inline-block"
                alt="Cliente 9" />
            </div>-->
          </div>

          <!-- Controles -->
          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#clientesCarousel"
            data-bs-slide="prev"
            class="bg-danger">
            <span
              class="carousel-control-prev-icon bg-danger"
              aria-hidden="true"></span>
            <span class="visually-hidden bg-danger"></span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#clientesCarousel"
            data-bs-slide="next"
            class="bg-danger">
            <span
              class="carousel-control-next-icon bg-danger"
              aria-hidden="true"></span>
            <span class="visually-hidde"></span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!--Testimonios Section -->
  <section class="testimonios-section bg-light py-3">
    <div class="container">
      <div class="row mb-5">
        <div class="col">
          <h2 class="fw-bold text-success">Testimonios de Clientes</h2>
          <p class="">
            Lo que nuestros clientes dicen sobre nosotros y nuestros
            servicios.
          </p>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card mb-4">
            <div class="row g-0">
              <div class="col-md-4">
                <img
                  src="img/usuario.jpg"
                  class="img-fluid rounded-start"
                  alt="..." />
                <!--<i class="fas fa-user-circle text-danger me-2"></i>-->
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title fw-bold">Carlos Ramirez</h5>
                  <p class="card-text">
                    <small class="text-body-secondary">Cidelsa</small>
                  </p>
                  <!--poner icono de entre comillas-->

                  <p class="card-text">
                    <i class="fas fa-quote-left fa-lg mb-2"></i>
                    Excelente Atención y Trabajo de Equipo en todo el
                    desarrollo del proyecto. Gran comunicación.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-4">
                <img
                  src="img/usuario.jpg"
                  class="img-fluid rounded-start"
                  alt="..." />
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title fw-bold">John Vargas</h5>
                  <p class="card-text">
                    <small class="text-body-secondary">Corporación Rey</small>
                  </p>
                  <!--poner icono de entre comillas-->

                  <p class="card-text">
                    <i class="fas fa-quote-left bg-danger fa-lg mb-2"></i>
                    Trabajo de Realizado en los Tiempos Proyectados
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb-3" style="max-width: 540px">
            <div class="row g-0">
              <div class="col-md-4">
                <img
                  src="img/usuario.jpg"
                  class="img-fluid rounded-start"
                  alt="..." />
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title fw-bold">John Doe</h5>
                  <p class="card-text">
                    <small class="text-body-secondary">Shell</small>
                  </p>
                  <!--poner icono de entre comillas-->

                  <p class="card-text">
                    <i class="fas fa-quote-left fa-lg mb-2"></i>
                    Un gran socio estrátegico que nos permitió posicionarnos
                    mejor en el mercado, así como brindarnos el mejor soporte
                    en el servicio.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--Productos Section -->
  </section>

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