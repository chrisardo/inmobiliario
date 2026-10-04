<?php
include 'controladores/procesar_recuperar_cuenta.php';
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentra tu cuenta | <?php echo $usuario['nombreEmpresa']; ?></title>
    <!--Poner icono de la pagina web-->
    <link rel="icon" href="img/logo.png" type="image/svg+xml" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>

<body>
    <!--Esta parte es recuperar_cuenta.php-->
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-2" style=" background: linear-gradient(135deg, #25cdfcff , #2ad867ff);">
        <div class="container">
            <!-- Logo -->

            <a class="navbar-brand fw-bold text-primary fs-3 text-dark" href="#">
                <?php if ($fotoPerfil): ?>
                    <img src="<?= $fotoPerfil ?>" class="rounded-circle border-success" height="45">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-2x"></i>
                <?php endif; ?>
            </a>

            <div class="d-flex align-items-center ms-auto">
                <a href="login.php" class="btn btn-primary">Iniciar sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <!-- Card del formulario -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="card-title">Encuentra tu cuenta</h3>
                            <p class="text-muted">Ingresa tu correo electrónico o número de celular para buscar tu cuenta.</p>
                        </div>

                        <form method="POST">
                            <div class="mb-3">
                                <input type="text"
                                    name="busqueda"
                                    class="form-control form-control-lg mb-3"
                                    placeholder="Correo o celular"
                                    required>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="login.php" class="btn btn-outline-secondary">Cancelar</a>
                                <!--Si el email o celular existe, enviar la foto de perfil y el usuario a restablecer_cpntrasena.php-->
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </form>
                        <!-- ✅ MENSAJE DE ERROR DEBAJO DEL FORMULARIO -->
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-danger mt-3 text-center">
                                <?= htmlspecialchars($mensaje) ?>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted">¿No puedes acceder a tu cuenta?</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>