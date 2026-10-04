<?php

// ============================================================
// CoDevPro Technology
// Archivo: controladores/procesar_categorias.php
// Módulo: Categorías
// Sistema: Panel Administrativo
// ============================================================


/*
|--------------------------------------------------------------------------
| SESIÓN
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['usId']) ||
    !is_numeric($_SESSION['usId'])
) {
    header("Location: ../login.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| CONEXIÓN
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/conect_db.php';


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN
|--------------------------------------------------------------------------
*/

$usId = (int) $_SESSION['usId'];

$registrosPorPagina = 5;


/*
|--------------------------------------------------------------------------
| ============================================================
| TOTAL DE MENSAJES SIN LEER
| ============================================================
|
| IMPORTANTE:
|
| La tabla mensajes NO tiene id_user.
|
| La relación correcta es:
|
| mensajes.id_propiedad
|        ↓
| propiedades.id_propiedad
|        ↓
| propiedades.id_user
|
| De esta manera contamos únicamente los mensajes de las
| propiedades pertenecientes al usuario actual.
|
|--------------------------------------------------------------------------
*/

$totalMensajes = 0;


/*
|--------------------------------------------------------------------------
| CONSULTAR MENSAJES NO LEÍDOS
|--------------------------------------------------------------------------
*/

$sqlMensajesNoLeidos = "
    SELECT COUNT(*) AS total
    FROM mensajes AS m
    INNER JOIN propiedades AS p
        ON p.id_propiedad = m.id_propiedad
    WHERE p.id_user = ?
      AND m.estado_mensaje_leido = 0
";


$stmtMensajesNoLeidos = $conexion->prepare(
    $sqlMensajesNoLeidos
);


if ($stmtMensajesNoLeidos) {

    $stmtMensajesNoLeidos->bind_param(
        "i",
        $usId
    );


    if ($stmtMensajesNoLeidos->execute()) {

        $resultadoMensajesNoLeidos =
            $stmtMensajesNoLeidos->get_result();


        if ($resultadoMensajesNoLeidos) {

            $filaMensajesNoLeidos =
                $resultadoMensajesNoLeidos->fetch_assoc();


            if (
                isset(
                    $filaMensajesNoLeidos['total']
                )
            ) {

                $totalMensajes =
                    (int) $filaMensajesNoLeidos['total'];

            }

        }

    }


    $stmtMensajesNoLeidos->close();
}


/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES
|--------------------------------------------------------------------------
*/


/**
 * Redirige al listado de categorías.
 */
function redirigirCategorias(
    string $buscar = '',
    int $pagina = 1
): void {

    $parametros = [];


    if ($buscar !== '') {

        $parametros['buscar'] = $buscar;

    }


    if ($pagina > 1) {

        $parametros['pagina'] = $pagina;

    }


    $url = '../adm/adm_categorias.php';


    if (!empty($parametros)) {

        $url .= '?' . http_build_query($parametros);

    }


    header("Location: " . $url);
    exit();
}


/**
 * Normaliza el nombre de la categoría.
 */
function normalizarNombreCategoria(
    string $nombre
): string {

    /*
     * Elimina espacios al inicio y al final.
     */
    $nombre = trim($nombre);


    /*
     * Convierte múltiples espacios internos
     * en un solo espacio.
     */
    $nombre = preg_replace(
        '/\s+/u',
        ' ',
        $nombre
    );


    return trim($nombre);
}


/**
 * Guarda mensaje temporal en sesión.
 */
function guardarMensajeCategoria(
    string $mensaje,
    string $tipo = 'success'
): void {

    $_SESSION['categoria_mensaje'] = $mensaje;
    $_SESSION['categoria_tipo'] = $tipo;
}


/*
|--------------------------------------------------------------------------
| VARIABLES DE LISTADO
|--------------------------------------------------------------------------
*/

$busqueda = isset($_GET['buscar'])
    ? trim((string) $_GET['buscar'])
    : '';


$pagina = isset($_GET['pagina'])
    ? (int) $_GET['pagina']
    : 1;


if ($pagina < 1) {
    $pagina = 1;
}


/*
|--------------------------------------------------------------------------
| ============================================================
| PROCESAMIENTO DE FORMULARIOS POST
| ============================================================
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    |--------------------------------------------------------------------------
    | ACCIÓN
    |--------------------------------------------------------------------------
    */

    $accion = isset($_POST['accion'])
        ? trim((string) $_POST['accion'])
        : '';


    /*
    |--------------------------------------------------------------------------
    | NOMBRE
    |--------------------------------------------------------------------------
    */

    $nombre = isset($_POST['nombre'])
        ? normalizarNombreCategoria(
            (string) $_POST['nombre']
        )
        : '';


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN DEL NOMBRE
    |--------------------------------------------------------------------------
    */

    if ($nombre === '') {

        guardarMensajeCategoria(
            'Debes ingresar el nombre de la categoría.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LONGITUD
    |--------------------------------------------------------------------------
    */

    $longitudNombre = function_exists('mb_strlen')
        ? mb_strlen($nombre, 'UTF-8')
        : strlen($nombre);


    if ($longitudNombre < 2) {

        guardarMensajeCategoria(
            'El nombre de la categoría debe tener al menos 2 caracteres.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    if ($longitudNombre > 100) {

        guardarMensajeCategoria(
            'El nombre de la categoría no puede superar los 100 caracteres.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ========================================================
    | REGISTRAR CATEGORÍA
    | ========================================================
    |--------------------------------------------------------------------------
    */

    if ($accion === 'registrar') {


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR DUPLICADO
        |--------------------------------------------------------------------------
        */

        $sqlDuplicado = "
            SELECT id_categoria
            FROM categoria
            WHERE id_user = ?
              AND Eliminado = 0
              AND LOWER(TRIM(nombre)) =
                  LOWER(TRIM(?))
            LIMIT 1
        ";


        $stmtDuplicado = $conexion->prepare(
            $sqlDuplicado
        );


        if (!$stmtDuplicado) {

            guardarMensajeCategoria(
                'No se pudo verificar la categoría. Inténtalo nuevamente.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $stmtDuplicado->bind_param(
            "is",
            $usId,
            $nombre
        );


        if (!$stmtDuplicado->execute()) {

            $stmtDuplicado->close();

            guardarMensajeCategoria(
                'No se pudo verificar la categoría. Inténtalo nuevamente.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $resultadoDuplicado =
            $stmtDuplicado->get_result();


        $categoriaExiste =
            $resultadoDuplicado &&
            $resultadoDuplicado->num_rows > 0;


        $stmtDuplicado->close();


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA DUPLICADA
        |--------------------------------------------------------------------------
        */

        if ($categoriaExiste) {

            guardarMensajeCategoria(
                'Ya existe una categoría con ese nombre.',
                'warning'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA ACTUAL
        |--------------------------------------------------------------------------
        */

        $fechaActual = date('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | INSERTAR
        |--------------------------------------------------------------------------
        */

        $sqlInsertar = "
            INSERT INTO categoria
            (
                nombre,
                id_user,
                Eliminado,
                fecha_registro,
                fecha_actualizacion
            )
            VALUES
            (
                ?,
                ?,
                0,
                ?,
                ?
            )
        ";


        $stmtInsertar = $conexion->prepare(
            $sqlInsertar
        );


        if (!$stmtInsertar) {

            guardarMensajeCategoria(
                'No se pudo preparar el registro de la categoría.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $stmtInsertar->bind_param(
            "siss",
            $nombre,
            $usId,
            $fechaActual,
            $fechaActual
        );


        if ($stmtInsertar->execute()) {

            $stmtInsertar->close();


            guardarMensajeCategoria(
                'La categoría se registró correctamente.',
                'success'
            );


            redirigirCategorias();
        }


        $stmtInsertar->close();


        guardarMensajeCategoria(
            'No se pudo registrar la categoría. Inténtalo nuevamente.',
            'danger'
        );


        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ========================================================
    | EDITAR CATEGORÍA
    | ========================================================
    |--------------------------------------------------------------------------
    */

    if ($accion === 'editar') {


        /*
        |--------------------------------------------------------------------------
        | ID
        |--------------------------------------------------------------------------
        */

        $idCategoria = isset($_POST['id_categorias'])
            ? (int) $_POST['id_categorias']
            : 0;


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ID
        |--------------------------------------------------------------------------
        */

        if ($idCategoria <= 0) {

            guardarMensajeCategoria(
                'La categoría seleccionada no es válida.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        $sqlCategoria = "
            SELECT
                id_categoria,
                nombre
            FROM categoria
            WHERE id_categoria = ?
              AND id_user = ?
              AND Eliminado = 0
            LIMIT 1
        ";


        $stmtCategoria = $conexion->prepare(
            $sqlCategoria
        );


        if (!$stmtCategoria) {

            guardarMensajeCategoria(
                'No se pudo verificar la categoría.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $stmtCategoria->bind_param(
            "ii",
            $idCategoria,
            $usId
        );


        if (!$stmtCategoria->execute()) {

            $stmtCategoria->close();

            guardarMensajeCategoria(
                'No se pudo verificar la categoría.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $resultadoCategoria =
            $stmtCategoria->get_result();


        $categoria =
            $resultadoCategoria
                ? $resultadoCategoria->fetch_assoc()
                : null;


        $stmtCategoria->close();


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA NO ENCONTRADA
        |--------------------------------------------------------------------------
        */

        if (!$categoria) {

            guardarMensajeCategoria(
                'La categoría no existe o no tienes permiso para modificarla.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR DUPLICADO
        |--------------------------------------------------------------------------
        */

        $sqlDuplicadoEditar = "
            SELECT id_categoria
            FROM categoria
            WHERE id_user = ?
              AND id_categoria <> ?
              AND Eliminado = 0
              AND LOWER(TRIM(nombre)) =
                  LOWER(TRIM(?))
            LIMIT 1
        ";


        $stmtDuplicadoEditar =
            $conexion->prepare(
                $sqlDuplicadoEditar
            );


        if (!$stmtDuplicadoEditar) {

            guardarMensajeCategoria(
                'No se pudo verificar el nuevo nombre.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $stmtDuplicadoEditar->bind_param(
            "iis",
            $usId,
            $idCategoria,
            $nombre
        );


        if (!$stmtDuplicadoEditar->execute()) {

            $stmtDuplicadoEditar->close();

            guardarMensajeCategoria(
                'No se pudo verificar el nuevo nombre.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $resultadoDuplicadoEditar =
            $stmtDuplicadoEditar->get_result();


        $nombreDuplicado =
            $resultadoDuplicadoEditar &&
            $resultadoDuplicadoEditar->num_rows > 0;


        $stmtDuplicadoEditar->close();


        /*
        |--------------------------------------------------------------------------
        | NOMBRE YA EXISTENTE
        |--------------------------------------------------------------------------
        */

        if ($nombreDuplicado) {

            guardarMensajeCategoria(
                'Ya existe otra categoría con ese nombre.',
                'warning'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA ACTUALIZACIÓN
        |--------------------------------------------------------------------------
        */

        $fechaActualizacion = date('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $sqlActualizar = "
            UPDATE categoria
            SET
                nombre = ?,
                fecha_actualizacion = ?
            WHERE id_categoria = ?
              AND id_user = ?
              AND Eliminado = 0
        ";


        $stmtActualizar = $conexion->prepare(
            $sqlActualizar
        );


        if (!$stmtActualizar) {

            guardarMensajeCategoria(
                'No se pudo preparar la actualización.',
                'danger'
            );

            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $stmtActualizar->bind_param(
            "ssii",
            $nombre,
            $fechaActualizacion,
            $idCategoria,
            $usId
        );


        if ($stmtActualizar->execute()) {

            $filasAfectadas =
                $stmtActualizar->affected_rows;


            $stmtActualizar->close();


            guardarMensajeCategoria(
                $filasAfectadas > 0
                    ? 'La categoría se actualizó correctamente.'
                    : 'La categoría ya tenía esos datos.',
                'success'
            );


            redirigirCategorias(
                $busqueda,
                $pagina
            );
        }


        $stmtActualizar->close();


        guardarMensajeCategoria(
            'No se pudo actualizar la categoría.',
            'danger'
        );


        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACCIÓN DESCONOCIDA
    |--------------------------------------------------------------------------
    */

    guardarMensajeCategoria(
        'La acción solicitada no es válida.',
        'danger'
    );


    redirigirCategorias(
        $busqueda,
        $pagina
    );
}


/*
|--------------------------------------------------------------------------
| ============================================================
| ELIMINAR CATEGORÍA
| ============================================================
|--------------------------------------------------------------------------
|
| La eliminación es lógica:
|
| Eliminado = 1
|
|--------------------------------------------------------------------------
*/

if (isset($_GET['eliminar'])) {


    $idCategoriaEliminar =
        (int) $_GET['eliminar'];


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ID
    |--------------------------------------------------------------------------
    */

    if ($idCategoriaEliminar <= 0) {

        guardarMensajeCategoria(
            'La categoría seleccionada no es válida.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR QUE PERTENEZCA AL USUARIO
    |--------------------------------------------------------------------------
    */

    $sqlVerificarEliminar = "
        SELECT
            id_categoria,
            nombre
        FROM categoria
        WHERE id_categoria = ?
          AND id_user = ?
          AND Eliminado = 0
        LIMIT 1
    ";


    $stmtVerificarEliminar =
        $conexion->prepare(
            $sqlVerificarEliminar
        );


    if (!$stmtVerificarEliminar) {

        guardarMensajeCategoria(
            'No se pudo verificar la categoría.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    $stmtVerificarEliminar->bind_param(
        "ii",
        $idCategoriaEliminar,
        $usId
    );


    if (!$stmtVerificarEliminar->execute()) {

        $stmtVerificarEliminar->close();

        guardarMensajeCategoria(
            'No se pudo verificar la categoría.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    $resultadoEliminar =
        $stmtVerificarEliminar->get_result();


    $categoriaEliminar =
        $resultadoEliminar
            ? $resultadoEliminar->fetch_assoc()
            : null;


    $stmtVerificarEliminar->close();


    /*
    |--------------------------------------------------------------------------
    | NO EXISTE
    |--------------------------------------------------------------------------
    */

    if (!$categoriaEliminar) {

        guardarMensajeCategoria(
            'La categoría no existe o ya fue eliminada.',
            'warning'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ELIMINADO
    |--------------------------------------------------------------------------
    */

    $sqlEliminar = "
        UPDATE categoria
        SET
            Eliminado = 1,
            fecha_actualizacion = ?
        WHERE id_categoria = ?
          AND id_user = ?
          AND Eliminado = 0
    ";


    $stmtEliminar =
        $conexion->prepare(
            $sqlEliminar
        );


    if (!$stmtEliminar) {

        guardarMensajeCategoria(
            'No se pudo preparar la eliminación.',
            'danger'
        );

        redirigirCategorias(
            $busqueda,
            $pagina
        );
    }


    $fechaEliminacion = date('Y-m-d');


    $stmtEliminar->bind_param(
        "sii",
        $fechaEliminacion,
        $idCategoriaEliminar,
        $usId
    );


    /*
    |--------------------------------------------------------------------------
    | EJECUTAR
    |--------------------------------------------------------------------------
    */

    if ($stmtEliminar->execute()) {

        $stmtEliminar->close();


        guardarMensajeCategoria(
            'La categoría "' .
            $categoriaEliminar['nombre'] .
            '" fue eliminada correctamente.',
            'success'
        );


        redirigirCategorias();
    }


    $stmtEliminar->close();


    guardarMensajeCategoria(
        'No se pudo eliminar la categoría.',
        'danger'
    );


    redirigirCategorias(
        $busqueda,
        $pagina
    );
}


/*
|--------------------------------------------------------------------------
| ============================================================
| PAGINACIÓN
| ============================================================
|--------------------------------------------------------------------------
*/

$inicio =
    ($pagina - 1) *
    $registrosPorPagina;


/*
|--------------------------------------------------------------------------
| ============================================================
| CONTAR CATEGORÍAS
| ============================================================
|--------------------------------------------------------------------------
*/

if ($busqueda !== '') {


    $buscarLike =
        '%' . $busqueda . '%';


    $sqlTotal = "
        SELECT COUNT(*) AS total
        FROM categoria
        WHERE id_user = ?
          AND Eliminado = 0
          AND nombre LIKE ?
    ";


    $stmtTotal =
        $conexion->prepare(
            $sqlTotal
        );


    if (!$stmtTotal) {

        $totalCategorias = 0;

    } else {

        $stmtTotal->bind_param(
            "is",
            $usId,
            $buscarLike
        );


        if ($stmtTotal->execute()) {

            $resultadoTotal =
                $stmtTotal->get_result();


            $filaTotal =
                $resultadoTotal
                    ? $resultadoTotal->fetch_assoc()
                    : null;


            $totalCategorias =
                isset($filaTotal['total'])
                    ? (int) $filaTotal['total']
                    : 0;

        } else {

            $totalCategorias = 0;
        }


        $stmtTotal->close();
    }


} else {


    $sqlTotal = "
        SELECT COUNT(*) AS total
        FROM categoria
        WHERE id_user = ?
          AND Eliminado = 0
    ";


    $stmtTotal =
        $conexion->prepare(
            $sqlTotal
        );


    if (!$stmtTotal) {

        $totalCategorias = 0;

    } else {

        $stmtTotal->bind_param(
            "i",
            $usId
        );


        if ($stmtTotal->execute()) {

            $resultadoTotal =
                $stmtTotal->get_result();


            $filaTotal =
                $resultadoTotal
                    ? $resultadoTotal->fetch_assoc()
                    : null;


            $totalCategorias =
                isset($filaTotal['total'])
                    ? (int) $filaTotal['total']
                    : 0;

        } else {

            $totalCategorias = 0;
        }


        $stmtTotal->close();
    }
}


/*
|--------------------------------------------------------------------------
| TOTAL DE PÁGINAS
|--------------------------------------------------------------------------
*/

$totalPaginas =
    $totalCategorias > 0
        ? (int) ceil(
            $totalCategorias /
            $registrosPorPagina
        )
        : 0;


/*
|--------------------------------------------------------------------------
| CORREGIR PÁGINA SI SUPERA EL TOTAL
|--------------------------------------------------------------------------
*/

if (
    $totalPaginas > 0 &&
    $pagina > $totalPaginas
) {

    $pagina = $totalPaginas;


    $inicio =
        ($pagina - 1) *
        $registrosPorPagina;
}


/*
|--------------------------------------------------------------------------
| ============================================================
| OBTENER LISTA DE CATEGORÍAS
| ============================================================
|--------------------------------------------------------------------------
*/

$resultado = false;


if ($busqueda !== '') {


    $buscarLike =
        '%' . $busqueda . '%';


    $sqlLista = "
        SELECT
            id_categoria,
            nombre,
            id_user,
            Eliminado,
            fecha_registro,
            fecha_actualizacion
        FROM categoria
        WHERE id_user = ?
          AND Eliminado = 0
          AND nombre LIKE ?
        ORDER BY id_categoria DESC
        LIMIT ?, ?
    ";


    $stmtLista =
        $conexion->prepare(
            $sqlLista
        );


    if ($stmtLista) {

        $stmtLista->bind_param(
            "isii",
            $usId,
            $buscarLike,
            $inicio,
            $registrosPorPagina
        );


        if ($stmtLista->execute()) {

            $resultado =
                $stmtLista->get_result();

        }

    }


} else {


    $sqlLista = "
        SELECT
            id_categoria,
            nombre,
            id_user,
            Eliminado,
            fecha_registro,
            fecha_actualizacion
        FROM categoria
        WHERE id_user = ?
          AND Eliminado = 0
        ORDER BY id_categoria DESC
        LIMIT ?, ?
    ";


    $stmtLista =
        $conexion->prepare(
            $sqlLista
        );


    if ($stmtLista) {

        $stmtLista->bind_param(
            "iii",
            $usId,
            $inicio,
            $registrosPorPagina
        );


        if ($stmtLista->execute()) {

            $resultado =
                $stmtLista->get_result();

        }

    }
}


/*
|--------------------------------------------------------------------------
| ============================================================
| MENSAJES FLASH
| ============================================================
|--------------------------------------------------------------------------
*/

$mensaje =
    $_SESSION['categoria_mensaje'] ?? '';


$tipoAlerta =
    $_SESSION['categoria_tipo'] ?? '';


unset(
    $_SESSION['categoria_mensaje']
);


unset(
    $_SESSION['categoria_tipo']
);


/*
|--------------------------------------------------------------------------
| VARIABLES DISPONIBLES PARA adm_categorias.php
|--------------------------------------------------------------------------
|
| $busqueda
| $pagina
| $registrosPorPagina
| $inicio
| $totalCategorias
| $totalPaginas
| $resultado
| $totalMensajes
| $mensaje
| $tipoAlerta
|
|--------------------------------------------------------------------------
*/

?>
