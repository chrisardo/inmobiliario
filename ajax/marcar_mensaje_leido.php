<?php
//=========================================================
// CoDevPro Technology
// Archivo: ajax/marcar_mensaje_leido.php
// Módulo: Mensajes
// Sistema: Inmobiliario
//=========================================================

session_start();

header('Content-Type: application/json; charset=utf-8');

/* =========================================================
   VERIFICAR SESIÓN
========================================================= */

if (!isset($_SESSION['usId'])) {

    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida.'
    ]);

    exit();
}


/* =========================================================
   CONEXIÓN
========================================================= */

require_once '../controladores/conect_db.php';


/* =========================================================
   OBTENER ID DEL MENSAJE
========================================================= */

$idContacto = isset($_POST['id_contacto'])
    ? (int)$_POST['id_contacto']
    : 0;


if ($idContacto <= 0) {

    echo json_encode([
        'success' => false,
        'message' => 'ID de mensaje no válido.'
    ]);

    exit();
}


/* =========================================================
   VERIFICAR MENSAJE
========================================================= */

$sql = "
    SELECT
        id_contacto,
        estado_mensaje_leido,
        fecha_leido
    FROM mensajes
    WHERE id_contacto = ?
    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    echo json_encode([
        'success' => false,
        'message' => 'No se pudo preparar la consulta.'
    ]);

    exit();
}

$stmt->bind_param(
    "i",
    $idContacto
);

$stmt->execute();

$resultado = $stmt->get_result();

$mensaje = $resultado->fetch_assoc();

$stmt->close();


/* =========================================================
   VERIFICAR EXISTENCIA
========================================================= */

if (!$mensaje) {

    echo json_encode([
        'success' => false,
        'message' => 'El mensaje no existe.'
    ]);

    exit();
}


/* =========================================================
   SI YA ESTÁ LEÍDO
========================================================= */

if ((int)$mensaje['estado_mensaje_leido'] === 1) {

    echo json_encode([
        'success' => true,
        'ya_leido' => true,
        'estado' => 1,
        'fecha_leido' => $mensaje['fecha_leido']
    ]);

    exit();
}


/* =========================================================
   MARCAR COMO LEÍDO
========================================================= */

$sqlActualizar = "
    UPDATE mensajes
    SET
        estado_mensaje_leido = 1,
        fecha_leido = NOW()
    WHERE
        id_contacto = ?
        AND estado_mensaje_leido = 0
";

$stmtActualizar = $conexion->prepare($sqlActualizar);

if (!$stmtActualizar) {

    echo json_encode([
        'success' => false,
        'message' => 'No se pudo preparar la actualización.'
    ]);

    exit();
}

$stmtActualizar->bind_param(
    "i",
    $idContacto
);

$actualizado = $stmtActualizar->execute();

$stmtActualizar->close();


/* =========================================================
   RESPUESTA
========================================================= */

if ($actualizado) {

    /*
     * Obtenemos la fecha real registrada por MySQL.
     */
    $sqlFecha = "
        SELECT fecha_leido
        FROM mensajes
        WHERE id_contacto = ?
        LIMIT 1
    ";

    $stmtFecha = $conexion->prepare($sqlFecha);

    $fechaLeido = date('Y-m-d H:i:s');

    if ($stmtFecha) {

        $stmtFecha->bind_param(
            "i",
            $idContacto
        );

        $stmtFecha->execute();

        $resultadoFecha = $stmtFecha->get_result();

        if ($filaFecha = $resultadoFecha->fetch_assoc()) {

            if (!empty($filaFecha['fecha_leido'])) {

                $fechaLeido =
                    $filaFecha['fecha_leido'];
            }
        }

        $stmtFecha->close();
    }


    echo json_encode([
        'success' => true,
        'ya_leido' => false,
        'estado' => 1,
        'fecha_leido' => $fechaLeido
    ]);
} else {

    echo json_encode([
        'success' => false,
        'message' => 'No se pudo marcar el mensaje como leído.'
    ]);
}
