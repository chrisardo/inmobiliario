<?php
//======================================================
// CoDevPro Technology
// Archivo: ajax/obtener_detalle_propiedad.php
// Módulo: Propiedades
// Sistema: Inmobiliario
//======================================================

declare(strict_types=1);


//======================================================
// CONEXIÓN
//======================================================

require_once "../controladores/conect_db.php";


//======================================================
// RESPUESTA JSON
//======================================================

header(
    "Content-Type: application/json; charset=utf-8"
);

header(
    "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
);

header(
    "Pragma: no-cache"
);


//======================================================
// MYSQLI
//======================================================

mysqli_report(
    MYSQLI_REPORT_OFF
);


//======================================================
// FUNCIÓN RESPUESTA
//======================================================

function responderJson(
    bool $success,
    string $mensaje = "",
    ?array $propiedad = null
): void {

    echo json_encode(
        [
            "success"   => $success,
            "mensaje"   => $mensaje,
            "propiedad" => $propiedad
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;

}


//======================================================
// VERIFICAR CONEXIÓN
//======================================================

if (
    !isset($conexion) ||
    !($conexion instanceof mysqli)
) {

    responderJson(
        false,
        "No se pudo establecer conexión con la base de datos."
    );

}


//======================================================
// OBTENER ID
//======================================================

$idPropiedad =
    isset($_GET["id_propiedad"])
        ? (int) $_GET["id_propiedad"]
        : 0;


//======================================================
// VALIDAR ID
//======================================================

if ($idPropiedad <= 0) {

    responderJson(
        false,
        "El identificador de la propiedad no es válido."
    );

}


//======================================================
// CONSULTA PRINCIPAL
//
// IMPORTANTE:
//
// Aquí NO obtenemos imágenes.
//
// Las imágenes se consultan posteriormente,
// permitiendo obtener todas las imágenes de
// la propiedad.
//
//======================================================

$sql = "

    SELECT

        p.id_propiedad,
        p.id_user,
        p.nombre,
        p.codigo,
        p.id_categoria,
        p.tamano_area_metros,
        p.precio,
        p.precio_anterior,
        p.ubicacion,
        p.fecha_registro,
        p.fecha_actualizacion,

        c.nombre AS nombre_categoria

    FROM propiedades p

    LEFT JOIN categoria c
        ON c.id_categoria = p.id_categoria

    WHERE
        p.id_propiedad = ?

    LIMIT 1

";


//======================================================
// PREPARAR
//======================================================

$stmt =
    $conexion->prepare(
        $sql
    );


if (!$stmt) {

    error_log(
        "Error prepare detalle propiedad: " .
        $conexion->error
    );


    responderJson(
        false,
        "No se pudo preparar la consulta de la propiedad."
    );

}


//======================================================
// BIND
//======================================================

if (
    !$stmt->bind_param(
        "i",
        $idPropiedad
    )
) {

    error_log(
        "Error bind detalle propiedad: " .
        $stmt->error
    );


    $stmt->close();


    responderJson(
        false,
        "No se pudo procesar la propiedad."
    );

}


//======================================================
// EJECUTAR
//======================================================

if (
    !$stmt->execute()
) {

    error_log(
        "Error execute detalle propiedad: " .
        $stmt->error
    );


    $stmt->close();


    responderJson(
        false,
        "No se pudo obtener la información de la propiedad."
    );

}


//======================================================
// RESULTADO
//======================================================

$resultado =
    $stmt->get_result();


if (
    !$resultado ||
    $resultado->num_rows === 0
) {

    if ($resultado) {

        $resultado->free();

    }


    $stmt->close();


    responderJson(
        false,
        "La propiedad no existe o ya no está disponible."
    );

}


//======================================================
// DATOS
//======================================================

$datos =
    $resultado->fetch_assoc();


$resultado->free();

$stmt->close();


//======================================================
// NORMALIZAR DATOS
//======================================================

$idPropiedadDB =
    (int) (
        $datos["id_propiedad"] ?? 0
    );


$idUser =
    (int) (
        $datos["id_user"] ?? 0
    );


$nombre =
    trim(
        (string) (
            $datos["nombre"] ?? ""
        )
    );


$codigo =
    trim(
        (string) (
            $datos["codigo"] ?? ""
        )
    );


$idCategoria =
    (int) (
        $datos["id_categoria"] ?? 0
    );


$nombreCategoria =
    trim(
        (string) (
            $datos["nombre_categoria"] ?? ""
        )
    );


$area =
    (float) (
        $datos["tamano_area_metros"] ?? 0
    );


$precio =
    (float) (
        $datos["precio"] ?? 0
    );


$precioAnterior =
    (float) (
        $datos["precio_anterior"] ?? 0
    );


$ubicacion =
    trim(
        (string) (
            $datos["ubicacion"] ?? ""
        )
    );


$fechaRegistro =
    (string) (
        $datos["fecha_registro"] ?? ""
    );


$fechaActualizacion =
    (string) (
        $datos["fecha_actualizacion"] ?? ""
    );


//======================================================
// OBTENER TODAS LAS IMÁGENES
//======================================================

$imagenes =
    [];


//======================================================
// CONSULTA IMÁGENES
//======================================================

$sqlImagenes = "

    SELECT

        id_imagen,
        imagenes,
        fecha_registro,
        fecha_actualizado,
        orden

    FROM imagenes

    WHERE
        id_propiedad = ?

    ORDER BY

        CASE
            WHEN orden IS NULL THEN 999999
            ELSE orden
        END ASC,

        id_imagen ASC

";


//======================================================
// PREPARAR
//======================================================

$stmtImagenes =
    $conexion->prepare(
        $sqlImagenes
    );


if ($stmtImagenes) {

    //==================================================
    // BIND
    //==================================================

    $stmtImagenes->bind_param(
        "i",
        $idPropiedadDB
    );


    //==================================================
    // EJECUTAR
    //==================================================

    if (
        $stmtImagenes->execute()
    ) {

        $resultadoImagenes =
            $stmtImagenes->get_result();


        if ($resultadoImagenes) {

            while (
                $filaImagen =
                    $resultadoImagenes->fetch_assoc()
            ) {

                //==========================================
                // DATOS DE IMAGEN
                //==========================================

                $idImagen =
                    (int) (
                        $filaImagen["id_imagen"] ?? 0
                    );


                $imagenBinaria =
                    $filaImagen["imagenes"]
                    ?? null;


                //==========================================
                // VALIDAR
                //==========================================

                if (
                    $imagenBinaria === null ||
                    $imagenBinaria === ""
                ) {

                    continue;

                }


                //==========================================
                // MIME
                //==========================================

                $mime =
                    "image/jpeg";


                //==========================================
                // DETECTAR MIME
                //==========================================

                if (
                    function_exists(
                        "finfo_open"
                    )
                ) {

                    $finfo =
                        finfo_open(
                            FILEINFO_MIME_TYPE
                        );


                    if ($finfo) {

                        $mimeDetectado =
                            finfo_buffer(
                                $finfo,
                                $imagenBinaria
                            );


                        if (
                            is_string(
                                $mimeDetectado
                            ) &&
                            strpos(
                                $mimeDetectado,
                                "image/"
                            ) === 0
                        ) {

                            $mime =
                                $mimeDetectado;

                        }


                        finfo_close(
                            $finfo
                        );

                    }

                }


                //==========================================
                // BASE64
                //==========================================

                $imagenBase64 =
                    "data:" .
                    $mime .
                    ";base64," .
                    base64_encode(
                        $imagenBinaria
                    );


                //==========================================
                // AGREGAR IMAGEN
                //==========================================

                $imagenes[] = [

                    "id_imagen" =>
                        $idImagen,

                    "src" =>
                        $imagenBase64,

                    "orden" =>
                        isset(
                            $filaImagen["orden"]
                        )
                            ? (int) $filaImagen["orden"]
                            : null,

                    "fecha_registro" =>
                        (string) (
                            $filaImagen[
                                "fecha_registro"
                            ] ?? ""
                        ),

                    "fecha_actualizado" =>
                        (string) (
                            $filaImagen[
                                "fecha_actualizado"
                            ] ?? ""
                        )

                ];

            }


            $resultadoImagenes->free();

        }

    } else {

        error_log(
            "Error obteniendo imágenes de propiedad " .
            $idPropiedadDB .
            ": " .
            $stmtImagenes->error
        );

    }


    $stmtImagenes->close();

}


//======================================================
// TELÉFONO WHATSAPP
//======================================================

$telefonoWhatsapp = "";


//======================================================
// FUNCIÓN EXISTE COLUMNA
//======================================================

function existeColumna(
    mysqli $conexion,
    string $tabla,
    string $columna
): bool {

    $sql = "

        SELECT COUNT(*) AS total

        FROM INFORMATION_SCHEMA.COLUMNS

        WHERE
            TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?

        LIMIT 1

    ";


    $stmt =
        $conexion->prepare(
            $sql
        );


    if (!$stmt) {

        return false;

    }


    $stmt->bind_param(
        "ss",
        $tabla,
        $columna
    );


    if (
        !$stmt->execute()
    ) {

        $stmt->close();

        return false;

    }


    $resultado =
        $stmt->get_result();


    if (
        !$resultado ||
        $resultado->num_rows === 0
    ) {

        if ($resultado) {

            $resultado->free();

        }

        $stmt->close();

        return false;

    }


    $fila =
        $resultado->fetch_assoc();


    $resultado->free();

    $stmt->close();


    return
        ((int) (
            $fila["total"] ?? 0
        )) > 0;

}


//======================================================
// BUSCAR TELÉFONO DE EMPRESA
//======================================================

try {

    $columnasTelefono = [

        "celular",

        "telefono",

        "whatsapp"

    ];


    $columnaEncontrada = null;


    foreach (
        $columnasTelefono as $columna
    ) {

        if (
            existeColumna(
                $conexion,
                "usuario_acceso",
                $columna
            )
        ) {

            $columnaEncontrada =
                $columna;

            break;

        }

    }


    if (
        $columnaEncontrada !== null
    ) {

        $sqlTelefono = "

            SELECT
                `$columnaEncontrada` AS telefono

            FROM usuario_acceso

            WHERE
                `$columnaEncontrada` IS NOT NULL

                AND TRIM(
                    `$columnaEncontrada`
                ) <> ''

            LIMIT 1

        ";


        $resultadoTelefono =
            $conexion->query(
                $sqlTelefono
            );


        if (
            $resultadoTelefono &&
            $resultadoTelefono->num_rows > 0
        ) {

            $filaTelefono =
                $resultadoTelefono->fetch_assoc();


            $telefonoWhatsapp =
                trim(
                    (string) (
                        $filaTelefono[
                            "telefono"
                        ] ?? ""
                    )
                );

        }


        if ($resultadoTelefono) {

            $resultadoTelefono->free();

        }

    }

} catch (Throwable $e) {

    error_log(
        "Error obteniendo teléfono WhatsApp: " .
        $e->getMessage()
    );


    $telefonoWhatsapp =
        "";

}


//======================================================
// NORMALIZAR TELÉFONO
//======================================================

$telefonoWhatsapp =
    preg_replace(
        "/\D/",
        "",
        $telefonoWhatsapp
    );


if (
    $telefonoWhatsapp === null
) {

    $telefonoWhatsapp = "";

}


//======================================================
// TELÉFONO PERUANO
//======================================================

if (
    strlen($telefonoWhatsapp) === 9 &&
    substr(
        $telefonoWhatsapp,
        0,
        1
    ) === "9"
) {

    $telefonoWhatsapp =
        "51" .
        $telefonoWhatsapp;

}


//======================================================
// SI TIENE 00
//======================================================

if (
    substr(
        $telefonoWhatsapp,
        0,
        2
    ) === "00"
) {

    $telefonoWhatsapp =
        substr(
            $telefonoWhatsapp,
            2
        );

}


//======================================================
// VALIDAR LONGITUD
//======================================================

if (
    strlen($telefonoWhatsapp) < 10 ||
    strlen($telefonoWhatsapp) > 15
) {

    $telefonoWhatsapp = "";

}


//======================================================
// RESPUESTA PROPIEDAD
//======================================================

$propiedad = [

    "id_propiedad" =>
        $idPropiedadDB,

    "id_user" =>
        $idUser,

    "nombre" =>
        $nombre,

    "codigo" =>
        $codigo,

    "id_categoria" =>
        $idCategoria,

    "nombre_categoria" =>
        $nombreCategoria !== ""
            ? $nombreCategoria
            : "Propiedad",

    "tamano_area_metros" =>
        $area,

    "precio" =>
        $precio,

    "precio_anterior" =>
        $precioAnterior,

    "ubicacion" =>
        $ubicacion !== ""
            ? $ubicacion
            : "Ubicación no disponible",

    "fecha_registro" =>
        $fechaRegistro,

    "fecha_actualizacion" =>
        $fechaActualizacion,

    //==================================================
    // TODAS LAS IMÁGENES
    //==================================================

    "imagenes" =>
        $imagenes,

    //==================================================
    // COMPATIBILIDAD
    //
    // Conservamos imagen con la primera imagen
    // por si algún código antiguo todavía la utiliza.
    //==================================================

    "imagen" =>
        !empty($imagenes)
            ? $imagenes[0]["src"]
            : "",

    //==================================================
    // WHATSAPP
    //==================================================

    "telefono_whatsapp" =>
        $telefonoWhatsapp

];


//======================================================
// RESPONDER
//======================================================

responderJson(
    true,
    "",
    $propiedad
);