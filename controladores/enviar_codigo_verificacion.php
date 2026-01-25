<?php
include "conect_db.php";
require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;
/* ===============================
   ZONA HORARIA
================================ */

date_default_timezone_set('America/Lima');

/* ===============================
   VALIDACIÓN INICIAL
================================ */
if (!isset($_POST['usId'], $_POST['method'])) {
    header("Location: ../recuperar_cuenta.php");
    exit;
}

$usId = (int) $_POST['usId'];
$method  = $_POST['method'];
$sqlUsuario = "
    SELECT nombreEmpresa, email, celular 
    FROM usuario_acceso 
    WHERE id_user = ?
    LIMIT 1
";

$stmtUsuario = $conexion->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $usId);
$stmtUsuario->execute();
$usuario = $stmtUsuario->get_result()->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}
/* ===============================
   GENERAR CÓDIGO Y TOKEN
================================ */
$codigo = random_int(100000, 999999);
$token  = bin2hex(random_bytes(32));

/* ===============================
   VERIFICAR / GUARDAR CÓDIGO
================================ */
$sqlCheck = "SELECT id_codigo_verificacion, intentos_envio
             FROM codigo_verificacion
             WHERE id_user = ?";

$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("i", $usId);
$stmtCheck->execute();
$res = $stmtCheck->get_result();

if ($res->num_rows > 0) {

    $row = $res->fetch_assoc();
    $intentos = $row['intentos_envio'] + 1;

    $sql = "UPDATE codigo_verificacion
            SET codigo = ?, token = ?, intentos_envio = ?, fecha_creacion = NOW()
            WHERE id_user = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssii", $codigo, $token, $intentos, $usId);
} else {

    $sql = "INSERT INTO codigo_verificacion
            (id_user, codigo, token, intentos_envio, fecha_creacion)
            VALUES (?, ?, ?, 1, NOW())";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iss", $usId, $codigo, $token);
}

$stmt->execute();

/* ===============================
   OBTENER EMAIL
================================ */
$sqlUser = "SELECT email FROM usuario_acceso WHERE id_user = ?";
$stmtUser = $conexion->prepare($sqlUser);
$stmtUser->bind_param("i", $usId);
$stmtUser->execute();
$u = $stmtUser->get_result()->fetch_assoc();

/* ===============================
   ENVÍO EMAIL (PHPMailer)
================================ */
if ($method === 'email') {

    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'chrisardorolo02@gmail.com';
        $mail->Password   = 'ekjxzrlvqqfqnics';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(
            'chrisardorolo02@gmail.com',
            $usuario['nombreEmpresa']
        );
        $mail->addAddress($u['email']);

        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación';
        $mail->Body = "
        <h2>{$usuario['nombreEmpresa']}</h2>
<p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
<p><strong>Código de verificación:</strong></p>
<h1 style='color:#198754;'>$codigo</h1>
<p>Este código expira en 10 minutos.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        die("Error al enviar correo: " . $mail->ErrorInfo);
    }
}


if ($method === 'sms') {
    // 📞 Obtener celular
    $stmtUser = $conexion->prepare(
        "SELECT celular FROM usuario_acceso WHERE id_user = ?"
    );
    $stmtUser->bind_param("i", $idUser);
    $stmtUser->execute();
    $celular = $stmtUser->get_result()->fetch_assoc()['celular'];

    // 🔐 CREDENCIALES LIVE
    $sid   = 'AC60d5c3ddb3b15e8c033716abfa0864da';
    $token = '8155fe30e48a437386ccc52db96b7539';
    $from  = '+14155552671';

    try {
        // Usa el cliente normal - ahora debería funcionar con SSL
        $twilio = new Client($sid, $token);

        $twilio->messages->create(
            '+51' . $celular,
            [
                'from' => $from,
                'body' => "Tu código de verificación Inventa es: $codigo"
            ]
        );
    } catch (Exception $e) {
        die("Error Twilio: " . $e->getMessage());
    }
}


/* ===============================
   REDIRECCIÓN
================================ */
header("Location: ../codigo_seguridad.php?usId=$usId&token=$token");
exit;
