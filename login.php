<?php
// =========================================================
// login.php
// Página de inicio de sesión
// =========================================================

require_once __DIR__ . "/controladores/conect_db.php";

// =========================================================
// FUNCIÓN DE ESCAPE
// =========================================================

if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars(
            (string) $valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

// =========================================================
// VARIABLES DE EMPRESA
// =========================================================

$nombreEmpresa = "Mi Empresa";
$fotoPerfil = "";

// =========================================================
// OBTENER DATOS DE LA EMPRESA
// =========================================================
//
// El nombre y logo se obtienen de:
// usuario_acceso.nombreEmpresa
// usuario_acceso.imagen
//
// Se toma el primer registro disponible.
// =========================================================

try {

    $sqlEmpresa = "
        SELECT
            nombreEmpresa,
            imagen
        FROM usuario_acceso
        ORDER BY id_user ASC
        LIMIT 1
    ";

    $stmtEmpresa = $conexion->prepare($sqlEmpresa);

    if ($stmtEmpresa) {

        $stmtEmpresa->execute();

        $resultadoEmpresa = $stmtEmpresa->get_result();

        if ($resultadoEmpresa && $resultadoEmpresa->num_rows > 0) {

            $empresa = $resultadoEmpresa->fetch_assoc();

            // -------------------------------------------------
            // NOMBRE EMPRESA
            // -------------------------------------------------

            if (
                isset($empresa['nombreEmpresa']) &&
                trim($empresa['nombreEmpresa']) !== ''
            ) {
                $nombreEmpresa = trim($empresa['nombreEmpresa']);
            }

            // -------------------------------------------------
            // LOGO EMPRESA
            // -------------------------------------------------

            if (
                isset($empresa['imagen']) &&
                !empty($empresa['imagen'])
            ) {

                $imagenBlob = $empresa['imagen'];

                // Detectar MIME de la imagen almacenada en LONGBLOB
                $mime = 'image/jpeg';

                if (function_exists('finfo_open')) {

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);

                    if ($finfo) {

                        $mimeDetectado = finfo_buffer(
                            $finfo,
                            $imagenBlob
                        );

                        if (
                            $mimeDetectado &&
                            strpos($mimeDetectado, 'image/') === 0
                        ) {
                            $mime = $mimeDetectado;
                        }

                        finfo_close($finfo);
                    }
                }

                // Convertir el BLOB en Data URI
                $fotoPerfil =
                    'data:' .
                    $mime .
                    ';base64,' .
                    base64_encode($imagenBlob);
            }
        }

        $stmtEmpresa->close();
    }
} catch (Throwable $e) {

    // Si ocurre algún problema, se mantienen los valores
    // por defecto para que el login pueda seguir cargando.
    $nombreEmpresa = "Mi Empresa";
    $fotoPerfil = "";
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Iniciar sesión - <?= e($nombreEmpresa) ?>
    </title>

    <?php if (!empty($fotoPerfil)): ?>

        <link
            rel="icon"
            href="<?= e($fotoPerfil) ?>"
            type="image/png">

    <?php endif; ?>


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <!-- =====================================================
         CSS LOGIN
    ====================================================== -->

    <link
        rel="stylesheet"
        href="css/login.css">


    <!-- =====================================================
         ESTILOS AJAX
    ====================================================== -->

    <style>
        .login-success {
            display: flex;
            align-items: center;
            gap: 10px;

            margin-bottom: 22px;
            padding: 12px 14px;

            border: 1px solid #bbf7d0;
            border-radius: 10px;

            background: #f0fdf4;
            color: #15803d;

            font-size: 13px;
        }

        .login-success i {
            flex-shrink: 0;
        }

        .login-button.loading {
            opacity: 0.75;
            cursor: not-allowed;
            pointer-events: none;
        }

        .login-button.loading i {
            animation: loginSpin 0.8s linear infinite;
        }

        @keyframes loginSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .custom-input.input-error {
            border-color: #ef4444;
            background: #fffafa;
        }

        .custom-input.input-success {
            border-color: #22c55e;
        }
    </style>

</head>


<body>

    <main class="login-page">


        <!-- =====================================================
         DECORACIÓN DE FONDO
    ====================================================== -->

        <div class="login-background-shape shape-one"></div>

        <div class="login-background-shape shape-two"></div>


        <!-- =====================================================
         TARJETA PRINCIPAL
    ====================================================== -->

        <div class="login-card">


            <!-- =================================================
             PANEL IZQUIERDO
        ================================================== -->

            <section class="login-brand">

                <div class="brand-content">


                    <!-- =================================================
                     LOGO DE LA EMPRESA
                ================================================== -->

                    <div class="brand-logo">

                        <?php if (!empty($fotoPerfil)): ?>

                            <img
                                src="<?= e($fotoPerfil) ?>"
                                alt="Logo de <?= e($nombreEmpresa) ?>"
                                id="companyLogo"
                                onerror="this.style.display='none'; document.getElementById('companyLogoFallback').style.display='flex';">


                            <!-- Fallback -->
                            <div
                                id="companyLogoFallback"
                                class="company-logo-placeholder"
                                style="display:none;">

                                <i class="fa-solid fa-building"></i>

                            </div>

                        <?php else: ?>

                            <div
                                id="companyLogoFallback"
                                class="company-logo-placeholder">

                                <i class="fa-solid fa-building"></i>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- =================================================
                     NOMBRE REAL DE LA EMPRESA
                ================================================== -->

                    <h1>
                        <?= e($nombreEmpresa) ?>
                    </h1>


                    <!-- =================================================
                     DESCRIPCIÓN
                ================================================== -->

                    <p class="brand-description">

                        Bienvenido de nuevo.

                        Inicia sesión para acceder a tu cuenta
                        y continuar trabajando.

                    </p>


                    <!-- =================================================
                     DECORACIÓN
                ================================================== -->

                    <div class="brand-decoration">

                        <span></span>
                        <span></span>
                        <span></span>

                    </div>

                </div>


                <!-- =================================================
                 FOOTER
            ================================================== -->

                <div class="brand-footer">

                    <i class="fa-solid fa-shield-halved"></i>

                    <span>
                        Acceso seguro y protegido
                    </span>

                </div>

            </section>


            <!-- =================================================
             PANEL DERECHO
        ================================================== -->

            <section class="login-form-container">


                <!-- =================================================
                 HEADER
            ================================================== -->

                <div class="login-header">

                    <div>

                        <span class="login-welcome">
                            BIENVENIDO
                        </span>

                        <h2>
                            Iniciar sesión
                        </h2>

                        <p>
                            Ingresa tus datos para continuar.
                        </p>

                    </div>


                    <a
                        href="index.php"
                        class="close-button"
                        aria-label="Volver al inicio"
                        title="Volver al inicio">

                        <i class="fa-solid fa-xmark"></i>

                    </a>

                </div>


                <!-- =================================================
                 ERROR AJAX
            ================================================== -->

                <div
                    id="loginError"
                    class="login-error"
                    role="alert"
                    style="display:none;">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span id="loginErrorMessage"></span>

                </div>


                <!-- =================================================
                 ÉXITO AJAX
            ================================================== -->

                <div
                    id="loginSuccess"
                    class="login-success"
                    role="alert"
                    style="display:none;">

                    <i class="fa-solid fa-circle-check"></i>

                    <span id="loginSuccessMessage"></span>

                </div>


                <!-- =================================================
                 FORMULARIO
            ================================================== -->

                <form
                    method="POST"
                    action="controladores/procesar_login.php"
                    class="login-form"
                    id="loginForm"
                    autocomplete="on">


                    <!-- =================================================
                     USUARIO
                ================================================== -->

                    <div class="form-field">

                        <label for="username">
                            Usuario o correo electrónico
                        </label>

                        <div class="input-wrapper">

                            <i class="fa-regular fa-user input-icon"></i>

                            <input
                                type="text"
                                class="form-control custom-input"
                                id="username"
                                name="username"
                                placeholder="Ingresa tu usuario o correo"
                                autocomplete="username"
                                maxlength="150"
                                required>

                        </div>

                    </div>


                    <!-- =================================================
                     CONTRASEÑA
                ================================================== -->

                    <div class="form-field">

                        <label for="password">
                            Contraseña
                        </label>

                        <div class="input-wrapper">

                            <i class="fa-solid fa-lock input-icon"></i>

                            <input
                                type="password"
                                class="form-control custom-input password-input"
                                id="password"
                                name="password"
                                placeholder="Ingresa tu contraseña"
                                autocomplete="current-password"
                                maxlength="255"
                                required>

                            <button
                                class="password-toggle"
                                type="button"
                                id="togglePassword"
                                aria-label="Mostrar contraseña">

                                <i class="fa-regular fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- =================================================
                     OPCIONES
                ================================================== -->

                    <div class="login-options">

                        <label class="remember-option">

                            <input
                                type="checkbox"
                                name="remember"
                                id="rememberMe"
                                value="1">

                            <span class="custom-checkbox"></span>

                            <span>
                                Recordarme
                            </span>

                        </label>


                        <a href="recuperar_cuenta.php">

                            ¿Olvidaste tu contraseña?

                        </a>

                    </div>


                    <!-- =================================================
                     BOTÓN LOGIN
                ================================================== -->

                    <button
                        type="submit"
                        class="login-button"
                        id="loginButton">

                        <span id="loginButtonText">
                            Ingresar
                        </span>

                        <i
                            class="fa-solid fa-arrow-right"
                            id="loginButtonIcon">
                        </i>

                    </button>


                </form>


                <!-- =================================================
                 SEGURIDAD
            ================================================== -->

                <div class="login-security">

                    <i class="fa-solid fa-lock"></i>

                    <span>
                        Tus datos están protegidos
                    </span>

                </div>

            </section>

        </div>


        <!-- =====================================================
         COPYRIGHT
    ====================================================== -->

        <div class="login-copyright">

            © <?= date('Y') ?>

            <?= e($nombreEmpresa) ?>

            · Todos los derechos reservados

        </div>


    </main>


    <!-- =========================================================
     JAVASCRIPT AJAX
========================================================== -->

    <script src="js/login.js"></script>

</body>

</html>