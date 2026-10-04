<?php
// =========================================================
// controladores/procesar_login.php
// Procesamiento AJAX del inicio de sesión
// =========================================================


// =========================================================
// SESIÓN
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =========================================================
// RESPUESTA JSON
// =========================================================

header('Content-Type: application/json; charset=UTF-8');


// =========================================================
// CONFIGURACIÓN DE ERRORES
// =========================================================

ini_set('display_errors', '0');


// =========================================================
// FUNCIÓN PARA RESPONDER JSON
// =========================================================

function respuesta_json(
    bool $success,
    string $message,
    array $extra = []
) {
    echo json_encode(
        array_merge(
            [
                'success' => $success,
                'message' => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


// =========================================================
// VERIFICAR MÉTODO
// =========================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    respuesta_json(
        false,
        'Método de solicitud no permitido.'
    );

}


// =========================================================
// VERIFICAR AJAX
// =========================================================
// No hacemos obligatorio el header AJAX porque algunos
// navegadores/proxies pueden eliminarlo.
// El procesamiento continúa si es POST.
// =========================================================


// =========================================================
// CONEXIÓN
// =========================================================

require_once __DIR__ . "/conect_db.php";


// =========================================================
// VERIFICAR CONEXIÓN
// =========================================================

if (!isset($conexion) || !($conexion instanceof mysqli)) {

    respuesta_json(
        false,
        'No se pudo establecer conexión con la base de datos.'
    );

}


// =========================================================
// COMPROBAR DATOS
// =========================================================

$username =
    isset($_POST['username'])
        ? trim((string) $_POST['username'])
        : '';

$password =
    isset($_POST['password'])
        ? (string) $_POST['password']
        : '';

$remember =
    isset($_POST['remember']) &&
    $_POST['remember'] === '1';


// =========================================================
// VALIDACIÓN
// =========================================================

if ($username === '') {

    respuesta_json(
        false,
        'Ingresa tu usuario o correo electrónico.'
    );

}


if ($password === '') {

    respuesta_json(
        false,
        'Ingresa tu contraseña.'
    );

}


// =========================================================
// CONSULTA
// =========================================================

$sql = "
    SELECT
        id_user,
        username,
        email,
        contrasena,
        nombreEmpresa,
        estado
    FROM usuario_acceso
    WHERE username = ?
       OR email = ?
    LIMIT 1
";


$stmt = $conexion->prepare($sql);


// =========================================================
// ERROR PREPARE
// =========================================================

if (!$stmt) {

    error_log(
        "Error prepare login: " .
        $conexion->error
    );

    respuesta_json(
        false,
        'Ocurrió un error interno del servidor.'
    );

}


// =========================================================
// BIND
// =========================================================

$stmt->bind_param(
    "ss",
    $username,
    $username
);


// =========================================================
// EXECUTE
// =========================================================

if (!$stmt->execute()) {

    error_log(
        "Error execute login: " .
        $stmt->error
    );

    $stmt->close();

    respuesta_json(
        false,
        'Ocurrió un error al procesar el inicio de sesión.'
    );

}


// =========================================================
// RESULTADO
// =========================================================

$result =
    $stmt->get_result();


if (!$result) {

    $stmt->close();

    respuesta_json(
        false,
        'No se pudo obtener la información del usuario.'
    );

}


// =========================================================
// USUARIO NO ENCONTRADO
// =========================================================

if ($result->num_rows !== 1) {

    $stmt->close();

    respuesta_json(
        false,
        'Usuario o contraseña incorrectos.'
    );

}


// =========================================================
// DATOS DEL USUARIO
// =========================================================

$row =
    $result->fetch_assoc();


// =========================================================
// VERIFICAR CONTRASEÑA
// =========================================================

if (
    !isset($row['contrasena']) ||
    !password_verify(
        $password,
        $row['contrasena']
    )
) {

    $stmt->close();

    respuesta_json(
        false,
        'Usuario o contraseña incorrectos.'
    );

}


// =========================================================
// VERIFICAR ESTADO
// =========================================================

$estado =
    strtolower(
        trim(
            (string) ($row['estado'] ?? '')
        )
    );


if ($estado !== 'activo') {

    $stmt->close();

    respuesta_json(
        false,
        'Tu cuenta está baneada o inactiva. Contacta al administrador.'
    );

}


// =========================================================
// LOGIN CORRECTO
// =========================================================

// Regenerar ID para evitar session fixation
session_regenerate_id(true);


// =========================================================
// VARIABLES DE SESIÓN
// =========================================================

$_SESSION['usId'] =
    (int) $row['id_user'];

$_SESSION['username'] =
    $row['username'];

$_SESSION['email'] =
    $row['email'] ?? '';

$_SESSION['nombreEmpresa'] =
    $row['nombreEmpresa'] ?? '';

$_SESSION['login'] =
    true;


// =========================================================
// RECORDARME
// =========================================================
// En esta versión dejamos la sesión normal.
// Si quieres implementar "Recordarme" realmente,
// debe hacerse mediante un token seguro almacenado
// en una tabla de sesiones/tokens y una cookie.
// =========================================================

if ($remember) {

    $_SESSION['remember'] = true;

} else {

    $_SESSION['remember'] = false;

}


// =========================================================
// CERRAR STATEMENT
// =========================================================

$stmt->close();


// =========================================================
// CERRAR CONEXIÓN
// =========================================================

$conexion->close();


// =========================================================
// RESPUESTA AJAX
// =========================================================

respuesta_json(
    true,
    'Inicio de sesión correcto.',
    [
        'redirect' => 'adm/adm_index.php'
    ]
);
