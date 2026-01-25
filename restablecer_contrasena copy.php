<?php
include 'controladores/procesar_restablecer_contrasena.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablece tu contraseña</title>
    <!--Poner icono de la pagina web-->
    <link rel="icon" href="img/logo_principal.png" type="image/svg+xml" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <!--Esta parte es restablecer_contrasena.php-->
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-2" style=" background: linear-gradient(135deg, #25cdfcff , #2ad867ff);">
        <div class="container">
            <!-- Logo -->

            <a class="navbar-brand fw-bold text-primary fs-3 text-dark" href="#">
                <img src="img/icono_dashboard.png" height="50" alt="expatul Logo" class="logo-img">
            </a>
            <div class="d-flex align-items-center ms-auto">
                <a href="login.php" class="btn btn-primary">Iniciar sesión</a>
            </div>
        </div>
    </nav>
    <!-- Contenido principal -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <!-- Título -->
                        <div class="text-center mb-4">
                            <h3 class="card-title mb-3">Restablece tu contraseña</h3>
                            <p class="text-muted">
                                ¿Cómo quieres que te enviemos el código para restablecer la contraseña?
                            </p>
                        </div>

                        <div class="row g-0 align-items-start">

                            <!-- Opciones -->
                            <div class="col-md-7">
                                <div class="mb-4">

                                    <div class="form-check mb-3 p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="method" id="method1" checked>
                                        <label class="form-check-label" for="method1">
                                            <strong>Enviar código a través de Email</strong>
                                            <p class="text-muted mb-0 mt-1">
                                                Te enviaremos un codigo al email: <?= htmlspecialchars($emailOculto); ?>
                                            </p>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3 p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="method" id="method2" disabled>
                                        <label class="form-check-label" for="method2">
                                            <strong>Enviar código por SMS</strong>
                                            <p class="text-muted mb-0 mt-1">
                                                Te enviaremos unn codigo al celular: <?= htmlspecialchars($celularOculto); ?>
                                            </p>
                                            <small class="text-danger">Próximamente disponible</small>
                                        </label>
                                    </div>

                                    <!--<div class="form-check p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="method" id="method3">
                                        <label class="form-check-label" for="method3">
                                            <strong>Ingresar contraseña para iniciar sesión</strong>
                                        </label>
                                    </div>-->

                                </div>
                            </div>

                            <!-- Perfil a la derecha -->
                            <div class="col-md-5 d-flex justify-content-center">
                                <div class="text-end">
                                    <?php
                                    $tieneImagen = !empty($u['imagen']);
                                    if ($tieneImagen):
                                    ?>
                                        <img src="data:image/jpeg;base64,<?= base64_encode($u['imagen']); ?>"
                                            class="rounded-circle"
                                            width="80"
                                            height="80"
                                            alt="Foto de perfil">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle text-secondary"
                                            style="font-size:80px;"></i>
                                    <?php endif; ?>
                                    <p class="mb-0 fw-bold"> <?= htmlspecialchars($u['nombreEmpresa']); ?></p>
                                    <small class="text-muted">Usuario de Inventa</small>
                                </div>
                            </div>

                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="recuperar_cuenta.php" class="btn btn-outline-secondary">Regresar</a>
                            <form action="controladores/enviar_codigo_verificacion.php" method="POST">
                                <input type="hidden" name="usId" value="<?= $usId ?>">
                                <input type="hidden" name="method" id="methodSelected" value="email">

                                <button type="submit" class="btn btn-primary">
                                    Continuar
                                </button>
                            </form>

                            <script>
                                document.querySelectorAll('input[name="method"]').forEach(radio => {
                                    radio.addEventListener('change', () => {
                                        document.getElementById('methodSelected').value =
                                            radio.id === 'method2' ? 'sms' : 'email';
                                    });
                                });
                            </script>
                        </div>

                        <!-- Aviso -->
                        <div class="alert alert-info mt-4">
                            <p class="mb-0">
                                Puedes ver tu nombre y foto del perfil porque estás usando un navegador
                                en el que ya habías iniciado sesión.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>