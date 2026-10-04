<?php
// ============================================================
// CoDevPro Technology
// Archivo: controladores/procesar_index.php
// Módulo: Dashboard Administrativo
// ============================================================


// ============================================================
// VERIFICAR SESIÓN
// ============================================================

if (!isset($_SESSION['usId'])) {

    header("Location: ../login.php");

    exit();
}


require_once 'conect_db.php';


$idUser =
    (int) $_SESSION['usId'];


// ============================================================
// INFORMACIÓN DEL USUARIO
// ============================================================

$sqlUsuario = "
    SELECT
        imagen,
        nombreEmpresa
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
";


$stmtUsuario =
    $conexion->prepare(
        $sqlUsuario
    );


$usuario = [];


if ($stmtUsuario) {

    $stmtUsuario->bind_param(
        "i",
        $idUser
    );

    $stmtUsuario->execute();

    $resultUsuario =
        $stmtUsuario->get_result();

    $usuario =
        $resultUsuario->fetch_assoc()
        ?? [];

    $stmtUsuario->close();
}


// ============================================================
// FOTO DE PERFIL
// ============================================================

$fotoPerfil = null;


if (
    !empty($usuario['imagen'])
) {

    $fotoPerfil =
        'data:image/jpeg;base64,' .
        base64_encode(
            $usuario['imagen']
        );
}


// ============================================================
// TOTAL PROPIEDADES
// ============================================================

$totalPropiedades = 0;


$sqlPropiedades = "
    SELECT COUNT(*) AS total
    FROM propiedades
    WHERE id_user = ?
";


$stmtPropiedades =
    $conexion->prepare(
        $sqlPropiedades
    );


if ($stmtPropiedades) {

    $stmtPropiedades->bind_param(
        "i",
        $idUser
    );

    $stmtPropiedades->execute();

    $resultPropiedades =
        $stmtPropiedades->get_result();

    $filaPropiedades =
        $resultPropiedades->fetch_assoc();

    $totalPropiedades =
        (int) (
            $filaPropiedades['total']
            ?? 0
        );

    $stmtPropiedades->close();
}


// ============================================================
// TOTAL ASESORES
// ============================================================

$totalAsesores = 0;


$sqlAsesores = "
    SELECT COUNT(*) AS total
    FROM asesores
    WHERE id_user = ?
";


$stmtAsesores =
    $conexion->prepare(
        $sqlAsesores
    );


if ($stmtAsesores) {

    $stmtAsesores->bind_param(
        "i",
        $idUser
    );

    $stmtAsesores->execute();

    $resultAsesores =
        $stmtAsesores->get_result();

    $filaAsesores =
        $resultAsesores->fetch_assoc();

    $totalAsesores =
        (int) (
            $filaAsesores['total']
            ?? 0
        );

    $stmtAsesores->close();
}


// ============================================================
// TOTAL MENSAJES
//
// mensajes no posee id_user.
//
// La relación es:
//
// mensajes.id_propiedad
//          ↓
// propiedades.id_propiedad
//          ↓
// propiedades.id_user
// ============================================================

$totalContacto = 0;


$sqlContacto = "
    SELECT COUNT(*) AS total

    FROM mensajes AS m

    INNER JOIN propiedades AS p
        ON p.id_propiedad = m.id_propiedad

    WHERE p.id_user = ? and m.estado_mensaje_leido = 0
";


$stmtContacto =
    $conexion->prepare(
        $sqlContacto
    );


if ($stmtContacto) {

    $stmtContacto->bind_param(
        "i",
        $idUser
    );

    $stmtContacto->execute();

    $resultContacto =
        $stmtContacto->get_result();

    $filaContacto =
        $resultContacto->fetch_assoc();

    $totalContacto =
        (int) (
            $filaContacto['total']
            ?? 0
        );

    $stmtContacto->close();
}


// ============================================================
// ÚLTIMOS MENSAJES
// ============================================================

$ultimosMensajes = [];


$sqlUltimosMensajes = "
    SELECT

        m.id_contacto,

        m.nombre,

        m.apellidos,

        m.email,

        m.mensaje,

        m.fecha_registro,

        p.nombre AS propiedad

    FROM mensajes AS m

    INNER JOIN propiedades AS p
        ON p.id_propiedad = m.id_propiedad

    WHERE p.id_user = ?

    ORDER BY
        m.id_contacto DESC

    LIMIT 5
";


$stmtUltimosMensajes =
    $conexion->prepare(
        $sqlUltimosMensajes
    );


if ($stmtUltimosMensajes) {

    $stmtUltimosMensajes->bind_param(
        "i",
        $idUser
    );

    $stmtUltimosMensajes->execute();

    $resultUltimosMensajes =
        $stmtUltimosMensajes->get_result();


    while (
        $row =
        $resultUltimosMensajes->fetch_assoc()
    ) {

        $ultimosMensajes[] =
            $row;
    }


    $stmtUltimosMensajes->close();
}
