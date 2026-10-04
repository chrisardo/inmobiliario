<?php
// =========================================================
// CoDevPro Technology
// Archivo: controladores/eliminar_propiedad.php
// Módulo: Propiedades
// Función: Eliminación lógica de propiedades
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'conect_db.php';

// =========================================================
// VARIABLES
// =========================================================

$mensajeEliminar = '';
$tipoAlertaEliminar = '';

$idUser = (int) $_SESSION['usId'];

// =========================================================
// PROCESAR ELIMINACIÓN
// =========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion !== 'eliminar') {
        return;
    }

    // -----------------------------------------------------
    // ID DE LA PROPIEDAD
    // -----------------------------------------------------

    $idPropiedad = isset($_POST['id_propiedad'])
        ? (int) $_POST['id_propiedad']
        : 0;

    if ($idPropiedad <= 0) {

        $_SESSION['mensaje_propiedad'] = 'La propiedad seleccionada no es válida.';
        $_SESSION['tipo_alerta_propiedad'] = 'danger';

        header("Location: ../adm/adm_lista_propiedades.php");
        exit();
    }

    // -----------------------------------------------------
    // VERIFICAR QUE LA PROPIEDAD PERTENEZCA AL USUARIO
    // -----------------------------------------------------

    $sqlVerificar = "
        SELECT
            id_propiedad,
            nombre,
            Eliminado
        FROM propiedades
        WHERE
            id_propiedad = ?
            AND id_user = ?
        LIMIT 1
    ";

    $stmtVerificar = $conexion->prepare($sqlVerificar);

    if (!$stmtVerificar) {

        $_SESSION['mensaje_propiedad'] =
            'No se pudo verificar la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] = 'danger';

        header("Location: ../adm/adm_lista_propiedades.php");
        exit();
    }

    $stmtVerificar->bind_param(
        "ii",
        $idPropiedad,
        $idUser
    );

    $stmtVerificar->execute();

    $resultado = $stmtVerificar->get_result();

    if (!$resultado || $resultado->num_rows === 0) {

        $stmtVerificar->close();

        $_SESSION['mensaje_propiedad'] =
            'La propiedad no existe o no tienes permiso para eliminarla.';

        $_SESSION['tipo_alerta_propiedad'] = 'danger';

        header("Location: ../adm/adm_lista_propiedades.php");
        exit();
    }

    $propiedad = $resultado->fetch_assoc();

    $stmtVerificar->close();

    // -----------------------------------------------------
    // VERIFICAR SI YA ESTÁ ELIMINADA
    // -----------------------------------------------------

    if ((int) $propiedad['Eliminado'] === 1) {

        $_SESSION['mensaje_propiedad'] =
            'La propiedad ya se encuentra eliminada.';

        $_SESSION['tipo_alerta_propiedad'] = 'warning';

        header("Location: ../adm/adm_lista_propiedades.php");
        exit();
    }

    // -----------------------------------------------------
    // ELIMINACIÓN LÓGICA
    // -----------------------------------------------------

    $sqlEliminar = "
        UPDATE propiedades
        SET
            Eliminado = 1,
            fecha_actualizacion = CURDATE()
        WHERE
            id_propiedad = ?
            AND id_user = ?
            AND Eliminado = 0
    ";

    $stmtEliminar = $conexion->prepare($sqlEliminar);

    if (!$stmtEliminar) {

        $_SESSION['mensaje_propiedad'] =
            'No se pudo preparar la eliminación de la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] = 'danger';

        header("Location: ../adm/adm_lista_propiedades.php");
        exit();
    }

    $stmtEliminar->bind_param(
        "ii",
        $idPropiedad,
        $idUser
    );

    if ($stmtEliminar->execute()) {

        if ($stmtEliminar->affected_rows > 0) {

            $_SESSION['mensaje_propiedad'] =
                'La propiedad "' .
                $propiedad['nombre'] .
                '" fue eliminada correctamente.';

            $_SESSION['tipo_alerta_propiedad'] = 'success';
        } else {

            $_SESSION['mensaje_propiedad'] =
                'No se pudo eliminar la propiedad.';

            $_SESSION['tipo_alerta_propiedad'] = 'danger';
        }
    } else {

        $_SESSION['mensaje_propiedad'] =
            'Ocurrió un error al eliminar la propiedad.';

        $_SESSION['tipo_alerta_propiedad'] = 'danger';
    }

    $stmtEliminar->close();

    // -----------------------------------------------------
    // VOLVER AL LISTADO
    // -----------------------------------------------------

    header("Location: ../adm/adm_lista_propiedades.php");
    exit();
}
