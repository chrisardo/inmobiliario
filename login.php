<?php
//Llamar al procesar_login.php
include 'controladores/procesar_login.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inventa - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png" type="image/svg+xml" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body style=" background: linear-gradient(135deg, #25cdfcff ,#2ad867ff);">

    <div class="container login-container d-flex justify-content-center align-items-center">
        <div class="login-card">
            <div class="login-left" style=" background: linear-gradient(135deg, rgb(60, 237, 187) ,rgb(128, 227, 106));">
                <!-- Aquí puedes agregar un logo o imagen si lo deseas -->
                <img src="img/logo.png" alt="Inventa Logo" class="mb-4" style="width:360px; height:200px;">
                <p>Por favor, inicia sesión para continuar.</p>
            </div>
            <div class="login-right">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="mb-0">Iniciar sesión</h3>

                    <a href="index.php"
                        class="d-flex justify-content-center align-items-center bg-danger text-white fw-bold"
                        style="width:28px; height:28px; border-radius:4px; text-decoration:none;"
                        aria-label="Cerrar y volver al login">
                        ×
                    </a>
                </div>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username o Email</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su usuario o correo">
                    </div>
                    <div class="mb-2 position-relative">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                👁️
                            </button>
                        </div>
                    </div>


                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Recordar</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Ingresar</button>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mt-2" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                </form>
                <!-- Puedes agregar enlaces adicionales aquí, como "¿Olvidaste tu contraseña? y registrarse" -->
                <div class="mt-2 text-center ">
                    <a href="recuperar_cuenta.php" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>