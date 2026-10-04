<?php
require_once "controladores/index.php";


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
  <title>Contacto - <?php echo $usuario['nombreEmpresa']; ?></title>
  <!--Poner icono de la pagina web-->
  <!--==================================================
      FAVICON
    ==================================================-->
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
  <!-- Open Graph (usado por Facebook, WhatsApp, Instagram, Telegram, TikTok, LinkedIn, etc.) -->
  <meta property="og:title" content="<?php echo $usuario['nombreEmpresa']; ?> - Contacto" />
  <meta property="og:description" content="<?php echo $usuario['descripcion_acerca']; ?>" />
  <meta property=" og:image" content="<?= $fotoPerfil ?>" />
  <meta property="og:url" content="https://extindustria.infinityfreeapp.com/" />
  <meta property="og:type" content="website" />

  <!-- Twitter Cards (usado por Twitter/X y también reconocido por algunas apps) -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?php echo $usuario['nombreEmpresa']; ?> - Contacto" />
  <meta name="twitter:description" content="<?php echo $usuario['descripcion_acerca']; ?>" />
  <meta name="twitter:image" content="<?= $fotoPerfil ?>" /> <!-- Bootstrap CSS -->
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
  <?php require "otros/barra_superior.php"; ?>
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


          <li class="nav-item">

            <a
              class="nav-link"
              href="asesores.php">

              Asesores

            </a>

          </li>


          <li class="nav-item">

            <a
              class="nav-link active"
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
  <!-- =========================================================
     CONTACTO
========================================================== -->

  <section
    id="contacto"
    class="contact-section">

    <div class="container">

      <div class="row g-5 align-items-start">

        <!-- INFORMACIÓN -->

        <div class="col-lg-5">

          <span class="section-eyebrow">

            CONTACTO

          </span>


          <h2 class="section-title">

            Estamos listos para atenderte

          </h2>


          <p class="section-description">

            Déjanos tus datos y cuéntanos qué propiedad
            estás buscando. Un asesor se pondrá en
            contacto contigo.

          </p>


          <div class="contact-info">

            <?php if (!empty($usuario['celular'])): ?>

              <div class="contact-item">

                <div class="contact-icon">

                  <i class="fa-solid fa-phone"></i>

                </div>

                <div>

                  <span>
                    Llámanos
                  </span>

                  <a
                    href="tel:<?= e($usuario['celular']) ?>">

                    +51 <?= e($usuario['celular']) ?>

                  </a>

                </div>

              </div>

            <?php endif; ?>


            <?php if (!empty($usuario['email'])): ?>

              <div class="contact-item">

                <div class="contact-icon">

                  <i class="fa-solid fa-envelope"></i>

                </div>

                <div>

                  <span>
                    Escríbenos
                  </span>

                  <a
                    href="mailto:<?= e($usuario['email']) ?>">

                    <?= e($usuario['email']) ?>

                  </a>

                </div>

              </div>

            <?php endif; ?>


            <?php if (!empty($usuario['direccion'])): ?>

              <div class="contact-item">

                <div class="contact-icon">

                  <i class="fa-solid fa-location-dot"></i>

                </div>

                <div>

                  <span>
                    Dirección
                  </span>

                  <strong>
                    <?= e($usuario['direccion']) ?>
                  </strong>

                </div>

              </div>

            <?php endif; ?>


            <?php if ($whatsappEmpresa): ?>

              <div class="contact-item">

                <div class="contact-icon">

                  <i class="fab fa-whatsapp"></i>

                </div>

                <div>

                  <span>
                    WhatsApp
                  </span>

                  <a
                    href="https://wa.me/<?= e($whatsappEmpresa) ?>"
                    target="_blank"
                    rel="noopener noreferrer">

                    Escríbenos por WhatsApp

                  </a>

                </div>

              </div>

            <?php endif; ?>

          </div>

        </div>


        <!-- FORMULARIO -->

        <div class="col-lg-7">

          <div class="contact-form-card">

            <h3 class="fw-bold mb-4">

              Solicita información

            </h3>
            <div
              id="respuestaContacto"
              class="mb-4"
              style="display:none;">
            </div>
            <form
              method="POST"
              action="ajax/procesar_contacto.php"
              class="contact-form"
              id="formContacto">


              <!-- PROPIEDAD -->

              <div class="mb-3">

                <label
                  for="propiedades"
                  class="form-label">

                  Propiedad de interés

                </label>


                <select
                  class="form-select"
                  id="propiedades"
                  name="propiedades"
                  required>

                  <option
                    value=""
                    selected
                    disabled>

                    Selecciona una propiedad

                  </option>


                  <?php foreach ($listaPropiedades as $propiedad): ?>

                    <option
                      value="<?= (int)$propiedad['id_propiedad'] ?>">

                      <?= e($propiedad['nombre']) ?>

                      <?php if (!empty($propiedad['codigo'])): ?>

                        - Código:
                        <?= e($propiedad['codigo']) ?>

                      <?php endif; ?>

                    </option>

                  <?php endforeach; ?>

                </select>

              </div>


              <!-- NOMBRES -->

              <div class="row g-3 mb-3">

                <div class="col-md-6">

                  <label
                    for="nombre"
                    class="form-label">

                    Nombres

                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="nombre"
                    name="nombre"
                    autocomplete="given-name"
                    required>

                </div>


                <div class="col-md-6">

                  <label
                    for="apellidos"
                    class="form-label">

                    Apellidos

                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="apellidos"
                    name="apellidos"
                    autocomplete="family-name"
                    required>

                </div>

              </div>


              <!-- EMAIL -->

              <div class="mb-3">

                <label
                  for="correo"
                  class="form-label">

                  Correo electrónico

                </label>

                <input
                  type="email"
                  class="form-control"
                  id="correo"
                  name="correo"
                  autocomplete="email"
                  required>

              </div>


              <!-- CELULAR -->

              <div class="mb-3">

                <label
                  for="celular"
                  class="form-label">

                  Celular

                </label>

                <input
                  type="tel"
                  class="form-control"
                  id="celular"
                  name="celular"
                  placeholder="Ejemplo: 943239039"
                  autocomplete="tel"
                  required>

              </div>


              <!-- MENSAJE -->

              <div class="mb-4">

                <label
                  for="mensaje"
                  class="form-label">

                  Mensaje

                </label>

                <textarea
                  class="form-control"
                  id="mensaje"
                  name="mensaje"
                  rows="5"
                  placeholder="Cuéntanos qué propiedad estás buscando..."
                  required></textarea>

              </div>


              <!-- BOTÓN -->

              <button
                type="submit"
                class="btn btn-success btn-lg px-4"
                id="btnEnviarCotizacion">


                <span id="textoBtnEnviar">
                  Enviar y cotizar
                </span>

              </button>

            </form>
          </div>

        </div>

      </div>

    </div>

  </section>

  <!-- llamar a otros/footer.php -->
  <?php include 'otros/footer.php'; ?>
  <!-- ================= JS ================= -->
  <script
    src="js/chat.js">
  </script>
  <script
    src="js/contacto.js">
  </script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>