<?php
include "conect_db.php";
date_default_timezone_set('America/Lima');

/* ===============================
   VALIDACIÓN INICIAL
================================ */
if (!isset($_POST['usId'], $_POST['token'], $_POST['codigo'])) {
    die("Solicitud inválida.");
    header("Location: ../restablecer_contrasena.php");
}

$usId = (int) $_POST['usId'];
$token   = $_POST['token'];
$codigo  = trim($_POST['codigo']);

/* ===============================
   BUSCAR CÓDIGO
================================ */
$sql = "SELECT codigo, fecha_creacion
        FROM codigo_verificacion
        WHERE id_user = ? AND token = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("is", $usId, $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    mostrarError("Código inválido. Solicita uno nuevo.");
}

$data = $res->fetch_assoc();

/* ===============================
   VALIDAR EXPIRACIÓN (10 MIN)
================================ */
$fechaCreacion = strtotime($data['fecha_creacion']);
$ahora = time();

if (($ahora - $fechaCreacion) > 600) {

    $stmt = $conexion->prepare(
        "DELETE FROM codigo_verificacion WHERE id_user = ?"
    );
    $stmt->bind_param("i", $usId);
    $stmt->execute();

    mostrarError("El código ha expirado. Solicita uno nuevo.");
}

/* ===============================
   VALIDAR CÓDIGO
================================ */
if ($codigo !== $data['codigo']) {
    mostrarError("El código ingresado es incorrecto.");
}

/* ===============================
   CORRECTO
================================ */
header("Location: ../nueva_contrasena.php?usId=$usId&token=$token");
exit;

/* ===============================
   FUNCIÓN ERROR
================================ */
function mostrarError($mensaje)
{
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-danger text-center">
                <h5><?= htmlspecialchars($mensaje) ?></h5>
                <a href="../restablecer_contrasena.php" class="btn btn-primary mt-3">
                    Solicitar código nuevo
                </a>
            </div>
        </div>
    </body>

    </html>
<?php
    exit;
}
