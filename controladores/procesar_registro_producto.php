<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ============================================================
   VARIABLES
============================================================ */

$mensaje = "";
$tipoAlerta = "";


/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['usId']) || !is_numeric($_SESSION['usId'])) {

    $mensaje = "La sesión no es válida. Inicia sesión nuevamente.";
    $tipoAlerta = "danger";

    return;
}

$id_user = (int) $_SESSION['usId'];


/* ============================================================
   PROCESAR FORMULARIO
============================================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* ========================================================
       VALIDAR CAMPOS
    ======================================================== */

    $codigo = trim($_POST['codigo'] ?? '');

    $nombre = trim($_POST['nombre'] ?? '');

    $ubicacion = trim($_POST['ubicacion'] ?? '');

    $categoria = isset($_POST['categoria'])
        ? (int) $_POST['categoria']
        : 0;


    $precio_venta = isset($_POST['precio_venta'])
        ? (float) str_replace(
            ',',
            '.',
            $_POST['precio_venta']
        )
        : 0;


    $precio_anterior = isset($_POST['precio_anterior'])
        ? (float) str_replace(
            ',',
            '.',
            $_POST['precio_anterior']
        )
        : 0;


    $tamano_area = isset($_POST['tamano_area'])
        ? (float) str_replace(
            ',',
            '.',
            $_POST['tamano_area']
        )
        : 0;


    /* ========================================================
       CAMPOS OBLIGATORIOS
    ======================================================== */

    if (
        $codigo === '' ||
        $nombre === '' ||
        $categoria <= 0 ||
        $precio_venta <= 0 ||
        $ubicacion === '' ||
        $tamano_area <= 0
    ) {

        $mensaje =
            "Todos los campos obligatorios deben completarse correctamente.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       VALIDAR LONGITUD
    ======================================================== */

    if (mb_strlen($codigo) > 50) {

        $mensaje =
            "El código de la propiedad no puede superar los 50 caracteres.";

        $tipoAlerta = "warning";

        return;
    }


    if (mb_strlen($nombre) > 150) {

        $mensaje =
            "El nombre de la propiedad no puede superar los 150 caracteres.";

        $tipoAlerta = "warning";

        return;
    }


    if (mb_strlen($ubicacion) > 200) {

        $mensaje =
            "La ubicación no puede superar los 200 caracteres.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       VALIDAR VALORES NUMÉRICOS
    ======================================================== */

    if (
        !is_finite($precio_venta) ||
        $precio_venta <= 0
    ) {

        $mensaje =
            "El precio de venta ingresado no es válido.";

        $tipoAlerta = "danger";

        return;
    }


    if (
        !is_finite($tamano_area) ||
        $tamano_area <= 0
    ) {

        $mensaje =
            "El tamaño del área ingresado no es válido.";

        $tipoAlerta = "danger";

        return;
    }


    if (
        $precio_anterior < 0 ||
        !is_finite($precio_anterior)
    ) {

        $mensaje =
            "El precio de venta anterior no es válido.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       VALIDAR CATEGORÍA
    ======================================================== */

    $sqlCategoria = "
        SELECT id_categoria
        FROM categoria
        WHERE id_categoria = ?
        AND id_user = ?
        AND Eliminado = 0
        LIMIT 1
    ";


    $stmtCategoria =
        $conexion->prepare($sqlCategoria);


    if (!$stmtCategoria) {

        $mensaje =
            "No fue posible validar la categoría.";

        $tipoAlerta = "danger";

        return;
    }


    $stmtCategoria->bind_param(
        "ii",
        $categoria,
        $id_user
    );


    $stmtCategoria->execute();


    $resultadoCategoria =
        $stmtCategoria->get_result();


    $categoriaExiste =
        $resultadoCategoria->num_rows > 0;


    $stmtCategoria->close();


    if (!$categoriaExiste) {

        $mensaje =
            "La categoría seleccionada no es válida.";

        $tipoAlerta = "danger";

        return;
    }


    /* ========================================================
       VALIDAR CÓDIGO DUPLICADO
    ======================================================== */

    $sqlDuplicado = "
        SELECT id_propiedad
        FROM propiedades
        WHERE codigo = ?
        AND id_user = ?
        AND (Eliminado = 0 OR Eliminado IS NULL)
        LIMIT 1
    ";


    $stmtDuplicado =
        $conexion->prepare($sqlDuplicado);


    if (!$stmtDuplicado) {

        $mensaje =
            "No fue posible verificar el código.";

        $tipoAlerta = "danger";

        return;
    }


    $stmtDuplicado->bind_param(
        "si",
        $codigo,
        $id_user
    );


    $stmtDuplicado->execute();


    $resultadoDuplicado =
        $stmtDuplicado->get_result();


    $existeCodigo =
        $resultadoDuplicado->num_rows > 0;


    $stmtDuplicado->close();


    if ($existeCodigo) {

        $mensaje =
            "Ya existe una propiedad con el código " .
            $codigo .
            ".";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       PROCESAR IMÁGENES
    ======================================================== */

    $imagenesValidas = [];


    /*
     * IMPORTANTE:
     *
     * El formulario utiliza:
     *
     * name="imagenes[]"
     *
     * Por eso PHP debe utilizar:
     *
     * $_FILES['imagenes']
     */


    if (
        !isset($_FILES['imagenes']) ||
        !isset($_FILES['imagenes']['name']) ||
        !is_array($_FILES['imagenes']['name'])
    ) {

        $mensaje =
            "Debes seleccionar al menos una imagen de la propiedad.";

        $tipoAlerta = "warning";

        return;
    }


    $cantidadArchivos =
        count($_FILES['imagenes']['name']);


    /* ========================================================
       VALIDAR QUE EXISTA AL MENOS UNA IMAGEN
    ======================================================== */

    if ($cantidadArchivos < 1) {

        $mensaje =
            "Debes seleccionar al menos una imagen de la propiedad antes de registrar.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       MÁXIMO 4 IMÁGENES
    ======================================================== */

    if ($cantidadArchivos > 4) {

        $mensaje =
            "Puedes seleccionar como máximo 4 imágenes.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       CONSTANTES DE VALIDACIÓN
    ======================================================== */

    $maxSize =
        (int) (1.8 * 1024 * 1024);


    $tiposPermitidos = [
        'image/jpeg',
        'image/png'
    ];


    /* ========================================================
       RECORRER IMÁGENES
    ======================================================== */

    for (
        $i = 0;
        $i < $cantidadArchivos;
        $i++
    ) {


        /* ====================================================
           DATOS DEL ARCHIVO
        ==================================================== */

        $nombreArchivo =
            $_FILES['imagenes']['name'][$i] ?? '';


        $tmpArchivo =
            $_FILES['imagenes']['tmp_name'][$i] ?? '';


        $errorArchivo =
            $_FILES['imagenes']['error'][$i]
            ?? UPLOAD_ERR_NO_FILE;


        $tamanoArchivo =
            isset($_FILES['imagenes']['size'][$i])
            ? (int) $_FILES['imagenes']['size'][$i]
            : 0;


        /* ====================================================
           IGNORAR CAMPOS VACÍOS
        ==================================================== */

        if (
            $errorArchivo === UPLOAD_ERR_NO_FILE
        ) {
            continue;
        }


        /* ====================================================
           VALIDAR ERROR DE SUBIDA
        ==================================================== */

        if (
            $errorArchivo !== UPLOAD_ERR_OK
        ) {

            $mensaje =
                "Ocurrió un error al subir la imagen número " .
                ($i + 1) .
                ".";

            $tipoAlerta = "danger";

            return;
        }


        /* ====================================================
           VALIDAR ARCHIVO TEMPORAL
        ==================================================== */

        if (
            $tmpArchivo === '' ||
            !is_uploaded_file($tmpArchivo)
        ) {

            $mensaje =
                "La imagen número " .
                ($i + 1) .
                " no es un archivo válido.";

            $tipoAlerta = "warning";

            return;
        }


        /* ====================================================
           VALIDAR TAMAÑO
        ==================================================== */

        if ($tamanoArchivo <= 0) {

            $mensaje =
                "La imagen número " .
                ($i + 1) .
                " está vacía.";

            $tipoAlerta = "warning";

            return;
        }


        if (
            $tamanoArchivo >
            $maxSize
        ) {

            $mensaje =
                "La imagen número " .
                ($i + 1) .
                " supera el tamaño máximo permitido de 1.8 MB.";

            $tipoAlerta = "warning";

            return;
        }


        /* ====================================================
           VALIDAR MIME REAL
        ==================================================== */

        $mime =
            mime_content_type($tmpArchivo);


        if (
            $mime === false ||
            !in_array(
                $mime,
                $tiposPermitidos,
                true
            )
        ) {

            $mensaje =
                "La imagen número " .
                ($i + 1) .
                " no tiene un formato válido. " .
                "Solo se permiten JPG o PNG.";

            $tipoAlerta = "warning";

            return;
        }


        /* ====================================================
           VERIFICAR QUE SEA REALMENTE UNA IMAGEN
        ==================================================== */

        $infoImagen =
            @getimagesize($tmpArchivo);


        if ($infoImagen === false) {

            $mensaje =
                "El archivo seleccionado como imagen número " .
                ($i + 1) .
                " no es una imagen válida.";

            $tipoAlerta = "warning";

            return;
        }


        /* ====================================================
           VALIDAR DIMENSIONES
        ==================================================== */

        $ancho =
            isset($infoImagen[0])
            ? (int) $infoImagen[0]
            : 0;


        $alto =
            isset($infoImagen[1])
            ? (int) $infoImagen[1]
            : 0;


        if (
            $ancho <= 0 ||
            $alto <= 0
        ) {

            $mensaje =
                "Las dimensiones de la imagen número " .
                ($i + 1) .
                " no son válidas.";

            $tipoAlerta = "warning";

            return;
        }


        /* ====================================================
           LEER BLOB
        ==================================================== */

        $contenidoImagen =
            file_get_contents($tmpArchivo);


        if (
            $contenidoImagen === false ||
            $contenidoImagen === ''
        ) {

            $mensaje =
                "No fue posible leer la imagen número " .
                ($i + 1) .
                ".";

            $tipoAlerta = "danger";

            return;
        }


        /* ====================================================
           GUARDAR EN MEMORIA
        ==================================================== */

        $imagenesValidas[] = [
            'nombre' => $nombreArchivo,
            'mime' => $mime,
            'tamano' => $tamanoArchivo,
            'contenido' => $contenidoImagen
        ];
    }


    /* ========================================================
       VALIDACIÓN FINAL DE IMÁGENES
    ======================================================== */

    $cantidadImagenes =
        count($imagenesValidas);


    if ($cantidadImagenes < 1) {

        $mensaje =
            "Debes seleccionar al menos una imagen válida de la propiedad antes de registrar.";

        $tipoAlerta = "warning";

        return;
    }


    if ($cantidadImagenes > 4) {

        $mensaje =
            "Puedes registrar como máximo 4 imágenes.";

        $tipoAlerta = "warning";

        return;
    }


    /* ========================================================
       TRANSACCIÓN
    ======================================================== */

    $conexion->begin_transaction();


    try {


        /* ====================================================
           INSERTAR PROPIEDAD
        ==================================================== */

        $sqlPropiedad = "
            INSERT INTO propiedades (
                id_user,
                nombre,
                codigo,
                id_categoria,
                tamano_area_metros,
                precio,
                precio_anterior,
                ubicacion,
                fecha_registro,
                Eliminado
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                0
            )
        ";


        $stmtPropiedad =
            $conexion->prepare($sqlPropiedad);


        if (!$stmtPropiedad) {

            throw new Exception(
                "No fue posible preparar el registro de la propiedad."
            );
        }


        $stmtPropiedad->bind_param(
            "issiddds",
            $id_user,
            $nombre,
            $codigo,
            $categoria,
            $tamano_area,
            $precio_venta,
            $precio_anterior,
            $ubicacion
        );


        if (!$stmtPropiedad->execute()) {

            throw new Exception(
                "No fue posible registrar la propiedad: " .
                $stmtPropiedad->error
            );
        }


        /* ====================================================
           OBTENER ID DE LA PROPIEDAD
        ==================================================== */

        $id_propiedad =
            $conexion->insert_id;


        $stmtPropiedad->close();


        if (!$id_propiedad) {

            throw new Exception(
                "No fue posible obtener el ID de la propiedad registrada."
            );
        }


        /* ====================================================
           INSERTAR IMÁGENES
        ==================================================== */

        $sqlImagen = "
            INSERT INTO imagenes (
                imagenes,
                id_propiedad,
                fecha_registro,
                fecha_actualizado,
                orden
            )
            VALUES (
                ?,
                ?,
                NOW(),
                NOW(),
                ?
            )
        ";


        $stmtImagen =
            $conexion->prepare($sqlImagen);


        if (!$stmtImagen) {

            throw new Exception(
                "No fue posible preparar el registro de las imágenes."
            );
        }


        /* ====================================================
           INSERTAR CADA IMAGEN
        ==================================================== */

        foreach (
            $imagenesValidas as $indice => $imagen
        ) {

            /*
             * El orden comienza en 1.
             */

            $orden =
                $indice + 1;


            $null = null;


            /*
             * b = BLOB
             * i = id_propiedad
             * i = orden
             */

            $stmtImagen->bind_param(
                "bii",
                $null,
                $id_propiedad,
                $orden
            );


            /*
             * Enviar el BLOB.
             */

            $stmtImagen->send_long_data(
                0,
                $imagen['contenido']
            );


            if (!$stmtImagen->execute()) {

                throw new Exception(
                    "No fue posible guardar la imagen número " .
                    $orden .
                    ": " .
                    $stmtImagen->error
                );
            }
        }


        $stmtImagen->close();


        /* ====================================================
           CONFIRMAR TRANSACCIÓN
        ==================================================== */

        $conexion->commit();


        /* ====================================================
           MENSAJE FINAL
        ==================================================== */

        if ($cantidadImagenes === 1) {

            $mensaje =
                "La propiedad fue registrada correctamente con 1 imagen.";

        } else {

            $mensaje =
                "La propiedad fue registrada correctamente con " .
                $cantidadImagenes .
                " imágenes.";
        }


        $tipoAlerta = "success";


    } catch (Exception $e) {


        /* ====================================================
           DESHACER TODO
        ==================================================== */

        $conexion->rollback();


        $mensaje =
            "No fue posible registrar la propiedad. " .
            $e->getMessage();


        $tipoAlerta = "danger";
    }
}
?>
