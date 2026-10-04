<?php

//======================================================
// CoDevPro Technology
// Archivo: controladores/buscar_propiedades.php
// Módulo: Propiedades
// Sistema: Inmobiliario
//======================================================

require_once __DIR__ . "/conect_db.php";


//======================================================
// RESPUESTA JSON
//======================================================

header("Content-Type: application/json; charset=utf-8");


//======================================================
// ERRORES MYSQLI
//======================================================

mysqli_report(
    MYSQLI_REPORT_ERROR |
        MYSQLI_REPORT_STRICT
);


//======================================================
// FUNCIÓN DE ESCAPE HTML
//
// El error que tenías era:
// Call to undefined function e()
//
// Este controlador necesita su propia función e()
// para poder generar correctamente el HTML.
//======================================================

if (!function_exists("e")) {

    function e($valor): string
    {
        return htmlspecialchars(
            (string) ($valor ?? ""),
            ENT_QUOTES,
            "UTF-8"
        );
    }
}


//======================================================
// FUNCIÓN RESPUESTA JSON
//======================================================

function responderJson(
    bool $success,
    string $mensaje = "",
    string $html = "",
    int $total = 0
): void {

    echo json_encode(
        [
            "success" => $success,
            "mensaje" => $mensaje,
            "html"    => $html,
            "total"   => $total
        ],
        JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
    );

    exit;
}


//======================================================
// OBTENER PARÁMETROS
//======================================================

$buscar = trim(
    $_GET["buscar"] ?? ""
);


$categoria = isset($_GET["categoria"])
    ? (int) $_GET["categoria"]
    : 0;


$precioMin = (
    isset($_GET["precio_min"]) &&
    $_GET["precio_min"] !== ""
)
    ? (float) $_GET["precio_min"]
    : null;


$precioMax = (
    isset($_GET["precio_max"]) &&
    $_GET["precio_max"] !== ""
)
    ? (float) $_GET["precio_max"]
    : null;


$areaMin = (
    isset($_GET["area_min"]) &&
    $_GET["area_min"] !== ""
)
    ? (float) $_GET["area_min"]
    : null;


$areaMax = (
    isset($_GET["area_max"]) &&
    $_GET["area_max"] !== ""
)
    ? (float) $_GET["area_max"]
    : null;


$fechaDesde = trim(
    $_GET["fecha_desde"] ?? ""
);


$orden = trim(
    $_GET["orden"] ?? "recientes"
);


//======================================================
// NORMALIZAR VALORES
//======================================================

if (
    $precioMin !== null &&
    $precioMin < 0
) {
    $precioMin = null;
}


if (
    $precioMax !== null &&
    $precioMax < 0
) {
    $precioMax = null;
}


if (
    $areaMin !== null &&
    $areaMin < 0
) {
    $areaMin = null;
}


if (
    $areaMax !== null &&
    $areaMax < 0
) {
    $areaMax = null;
}


//======================================================
// VALIDAR RANGO DE PRECIOS
//======================================================

if (
    $precioMin !== null &&
    $precioMax !== null &&
    $precioMin > $precioMax
) {

    responderJson(
        false,
        "El precio mínimo no puede ser mayor que el precio máximo."
    );
}


//======================================================
// VALIDAR RANGO DE ÁREA
//======================================================

if (
    $areaMin !== null &&
    $areaMax !== null &&
    $areaMin > $areaMax
) {

    responderJson(
        false,
        "El área mínima no puede ser mayor que el área máxima."
    );
}


//======================================================
// VALIDAR FECHA
//======================================================

if ($fechaDesde !== "") {

    $fechaValida = DateTime::createFromFormat(
        "Y-m-d",
        $fechaDesde
    );

    if (
        !$fechaValida ||
        $fechaValida->format("Y-m-d") !== $fechaDesde
    ) {

        responderJson(
            false,
            "La fecha seleccionada no es válida."
        );
    }
}


//======================================================
// CONSTRUIR WHERE
//======================================================

$where = [];

$params = [];

$types = "";
//======================================================
// SOLO PROPIEDADES NO ELIMINADAS
//======================================================

$where[] = "
    (p.Eliminado IS NULL OR p.Eliminado = 0)
";

//======================================================
// BÚSQUEDA GENERAL
//======================================================

if ($buscar !== "") {

    $buscarLike = "%" . $buscar . "%";

    $where[] = "
        (
            p.codigo LIKE ?
            OR p.nombre LIKE ?
            OR p.ubicacion LIKE ?
        )
    ";

    $params[] = $buscarLike;
    $params[] = $buscarLike;
    $params[] = $buscarLike;

    $types .= "sss";
}


//======================================================
// CATEGORÍA
//======================================================

if ($categoria > 0) {

    $where[] = "
        p.id_categoria = ?
    ";

    $params[] = $categoria;

    $types .= "i";
}


//======================================================
// PRECIO MÍNIMO
//======================================================

if ($precioMin !== null) {

    $where[] = "
        p.precio >= ?
    ";

    $params[] = $precioMin;

    $types .= "d";
}


//======================================================
// PRECIO MÁXIMO
//======================================================

if ($precioMax !== null) {

    $where[] = "
        p.precio <= ?
    ";

    $params[] = $precioMax;

    $types .= "d";
}


//======================================================
// ÁREA MÍNIMA
//======================================================

if ($areaMin !== null) {

    $where[] = "
        p.tamano_area_metros >= ?
    ";

    $params[] = $areaMin;

    $types .= "d";
}


//======================================================
// ÁREA MÁXIMA
//======================================================

if ($areaMax !== null) {

    $where[] = "
        p.tamano_area_metros <= ?
    ";

    $params[] = $areaMax;

    $types .= "d";
}


//======================================================
// FECHA DESDE
//======================================================

if ($fechaDesde !== "") {

    $where[] = "
        p.fecha_registro >= ?
    ";

    $params[] = $fechaDesde;

    $types .= "s";
}


//======================================================
// WHERE FINAL
//======================================================

$whereSQL = "";

if (!empty($where)) {

    $whereSQL =
        "WHERE " .
        implode(
            " AND ",
            $where
        );
}


//======================================================
// ORDEN
//======================================================

$ordenSQL = "
    p.id_propiedad DESC
";


switch ($orden) {

    case "antiguos":

        $ordenSQL = "
            p.id_propiedad ASC
        ";

        break;


    case "precio_asc":

        $ordenSQL = "
            p.precio ASC,
            p.id_propiedad DESC
        ";

        break;


    case "precio_desc":

        $ordenSQL = "
            p.precio DESC,
            p.id_propiedad DESC
        ";

        break;


    case "area_asc":

        $ordenSQL = "
            p.tamano_area_metros ASC,
            p.id_propiedad DESC
        ";

        break;


    case "area_desc":

        $ordenSQL = "
            p.tamano_area_metros DESC,
            p.id_propiedad DESC
        ";

        break;


    case "nombre":

        $ordenSQL = "
            p.nombre ASC,
            p.id_propiedad DESC
        ";

        break;


    case "recientes":

    default:

        $ordenSQL = "
            p.id_propiedad DESC
        ";

        break;
}


//======================================================
// CONSULTA
//
// Se obtiene solamente una imagen por propiedad.
// Se selecciona la imagen con menor orden.
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

        c.nombre AS nombre_categoria,

        img.imagenes AS imagen

    FROM propiedades p

    LEFT JOIN categoria c
        ON c.id_categoria = p.id_categoria

    LEFT JOIN imagenes img
        ON img.id_imagen = (

            SELECT
                i.id_imagen

            FROM imagenes i

            WHERE
                i.id_propiedad = p.id_propiedad

            ORDER BY
                COALESCE(i.orden, 999999) ASC,
                i.id_imagen ASC

            LIMIT 1
        )

    $whereSQL

    ORDER BY
        $ordenSQL
";


//======================================================
// PREPARAR CONSULTA
//======================================================

try {

    $stmt = $conexion->prepare(
        $sql
    );
} catch (Throwable $e) {

    error_log(
        "Error prepare propiedades: " .
            $e->getMessage()
    );

    responderJson(
        false,
        "Error al preparar la consulta de propiedades."
    );
}


//======================================================
// BIND DE PARÁMETROS
//======================================================

if (!empty($params)) {

    try {

        $stmt->bind_param(
            $types,
            ...$params
        );
    } catch (Throwable $e) {

        error_log(
            "Error bind propiedades: " .
                $e->getMessage()
        );

        $stmt->close();

        responderJson(
            false,
            "Error al aplicar los filtros."
        );
    }
}


//======================================================
// EJECUTAR CONSULTA
//======================================================

try {

    $stmt->execute();
} catch (Throwable $e) {

    error_log(
        "Error execute propiedades: " .
            $e->getMessage()
    );

    $stmt->close();

    responderJson(
        false,
        "Error al ejecutar la búsqueda."
    );
}


//======================================================
// RESULTADOS
//
// Se utiliza bind_result() para no depender
// de mysqlnd / get_result().
//======================================================

$stmt->store_result();

$total = $stmt->num_rows;


//======================================================
// VARIABLES DEL RESULTADO
//======================================================

$stmt->bind_result(
    $idPropiedad,
    $idUser,
    $nombre,
    $codigo,
    $idCategoria,
    $area,
    $precio,
    $precioAnterior,
    $ubicacion,
    $fechaRegistro,
    $fechaActualizacion,
    $categoriaNombre,
    $imagen
);


//======================================================
// OBTENER TELÉFONO WHATSAPP
//======================================================

$telefonoWhatsapp = "";


try {

    $sqlEmpresa = "
        SELECT celular
        FROM usuario_acceso
        LIMIT 1
    ";

    $resEmpresa = $conexion->query(
        $sqlEmpresa
    );


    if (
        $resEmpresa &&
        ($empresa = $resEmpresa->fetch_assoc())
    ) {

        $telefono = preg_replace(
            "/\D/",
            "",
            $empresa["celular"] ?? ""
        );


        if ($telefono !== "") {

            if (
                substr($telefono, 0, 2) === "51"
            ) {

                $telefonoWhatsapp = $telefono;
            } else {

                $telefonoWhatsapp =
                    "51" . $telefono;
            }
        }
    }
} catch (Throwable $e) {

    error_log(
        "Error obteniendo teléfono WhatsApp: " .
            $e->getMessage()
    );

    $telefonoWhatsapp = "";
}


//======================================================
// GENERAR HTML
//======================================================

ob_start();


if ($total > 0):

    while ($stmt->fetch()):

        //==================================================
        // NORMALIZAR DATOS
        //==================================================

        $idPropiedad =
            (int) $idPropiedad;


        $nombrePropiedad =
            !empty($nombre)
            ? $nombre
            : "Propiedad";


        $codigoPropiedad =
            $codigo ?? "";


        $ubicacionPropiedad =
            !empty($ubicacion)
            ? $ubicacion
            : "Ubicación no disponible";


        $categoriaNombre =
            !empty($categoriaNombre)
            ? $categoriaNombre
            : "Propiedad";


        $area =
            (float) ($area ?? 0);


        $precio =
            (float) ($precio ?? 0);


        $precioAnterior =
            (float) ($precioAnterior ?? 0);


        //==================================================
        // IMAGEN
        //==================================================

        $imagenPropiedad = null;


        if (!empty($imagen)) {

            $imagenPropiedad =
                "data:image/jpeg;base64," .
                base64_encode($imagen);
        }


        //==================================================
        // MENSAJE WHATSAPP
        //==================================================

        $mensajeWhatsapp =
            "Estimado/a,\n\n" .
            "Me comunico para solicitar " .
            "información sobre la siguiente " .
            "propiedad:\n\n" .
            "Código: " .
            $codigoPropiedad .
            "\n" .
            "Nombre: " .
            $nombrePropiedad .
            "\n" .
            "Ubicación: " .
            $ubicacionPropiedad .
            "\n" .
            "Precio: S/. " .
            number_format(
                $precio,
                2
            ) .
            "\n\n" .
            "Quedo atento/a a su respuesta.\n" .
            "Muchas gracias.";


        $mensajeWhatsappEncoded =
            urlencode(
                $mensajeWhatsapp
            );

?>

        <!--======================================================
    TARJETA DE PROPIEDAD
=======================================================-->

        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 property-item">

            <article class="property-card h-100">

                <!--==================================================
            IMAGEN
        ==================================================-->

                <div class="property-image-container">

                    <?php if ($imagenPropiedad): ?>

                        <img
                            src="<?= e($imagenPropiedad) ?>"
                            class="property-image"
                            alt="<?= e($nombrePropiedad) ?>"
                            loading="lazy">

                    <?php else: ?>

                        <div class="property-no-image">

                            <i class="bi bi-house-door"></i>

                            <span>
                                Sin imagen
                            </span>

                        </div>

                    <?php endif; ?>


                    <!--================================================
                PRECIO SOBRE IMAGEN
            =================================================-->

                    <div class="property-price-badge">

                        <small>
                            Desde
                        </small>

                        <strong>

                            S/.
                            <?= number_format(
                                $precio,
                                0
                            ) ?>

                        </strong>
                        <?php if (
                            $precioAnterior > $precio &&
                            $precio > 0
                        ): ?>

                            <del>

                                S/.

                                <?= number_format(
                                    $precioAnterior,
                                    2
                                ) ?>

                            </del>

                        <?php endif; ?>
                    </div>


                    <!--================================================
                CÓDIGO
            =================================================-->

                    <?php if ($codigoPropiedad !== ""): ?>

                        <div class="property-code-badge">

                            <i class="bi bi-hash"></i>

                            <?= e($codigoPropiedad) ?>

                        </div>

                    <?php endif; ?>

                </div>


                <!--==================================================
            CUERPO DE LA TARJETA
        ==================================================-->

                <div class="property-card-body">


                    <!--================================================
                NOMBRE
            =================================================-->

                    <h3 class="property-title">

                        <?= e($nombrePropiedad) ?>

                    </h3>


                    <!--================================================
                UBICACIÓN
            =================================================-->

                    <div class="property-location">

                        <i class="bi bi-geo-alt-fill"></i>

                        <span>

                            <?= e(
                                $ubicacionPropiedad
                            ) ?>

                        </span>

                    </div>


                    <!--================================================
                CARACTERÍSTICAS
            =================================================-->

                    <div class="property-features">


                        <?php if ($area > 0): ?>

                            <div class="property-feature">

                                <i class="bi bi-arrows-fullscreen"></i>

                                <span>

                                    <?= number_format(
                                        $area,
                                        2
                                    ) ?>

                                    m²

                                </span>

                            </div>

                        <?php endif; ?>


                        <div class="property-feature">

                            <i class="bi bi-house"></i>

                            <span>

                                <?= e(
                                    $categoriaNombre
                                ) ?>

                            </span>

                        </div>

                    </div>

                    <!--================================================
    ACCIONES
================================================-->

                    <div class="d-grid gap-2">

                        <!-- VER DETALLE -->

                        <button
                            type="button"
                            class="btn btn-outline-success w-100 btnVerDetalle"
                            data-id="<?= $idPropiedad ?>"
                            aria-label="Ver detalle de <?= e($nombrePropiedad) ?>">

                            <i class="bi bi-eye me-1"></i>

                            Ver detalle

                        </button>


                        <!-- WHATSAPP -->

                        <?php if (
                            $telefonoWhatsapp !== ""
                        ): ?>

                            <a
                                href="https://wa.me/<?= e($telefonoWhatsapp) ?>?text=<?= $mensajeWhatsappEncoded ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-success w-100 property-whatsapp-button">

                                <i class="bi bi-whatsapp me-1"></i>

                                Solicitar información

                            </a>

                        <?php else: ?>

                            <a
                                href="contacto.php"
                                class="btn btn-success w-100 property-whatsapp-button">

                                <i class="bi bi-envelope me-1"></i>

                                Solicitar información

                            </a>

                        <?php endif; ?>

                    </div>
                </div>

            </article>

        </div>

    <?php

    endwhile;

else:

    ?>

    <!--======================================================
    SIN RESULTADOS
=======================================================-->

    <div class="col-12">

        <div class="empty-properties">

            <div class="empty-properties-icon">

                <i class="bi bi-search"></i>

            </div>


            <h3>
                No encontramos propiedades
            </h3>


            <p class="text-muted">

                No existen propiedades que coincidan
                con los criterios de búsqueda.

            </p>
        </div>

    </div>

<?php

endif;


//======================================================
// OBTENER HTML
//======================================================

$html = ob_get_clean();


//======================================================
// CERRAR STATEMENT
//======================================================

$stmt->free_result();

$stmt->close();


//======================================================
// RESPUESTA FINAL JSON
//======================================================

responderJson(
    true,
    "",
    $html,
    $total
);
