<?php

// =========================================================
// CoDevPro Technology
// Archivo: controladores/buscar_asesores.php
// Módulo: Lista de Asesores
// Sistema: Inmobiliario
// =========================================================

require_once __DIR__ . "/conect_db.php";

// =========================================================
// RESPUESTA HTML
// =========================================================

header("Content-Type: text/html; charset=utf-8");

// =========================================================
// ERRORES MYSQLI
// =========================================================

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);

// =========================================================
// FUNCIÓN DE ESCAPE HTML
// =========================================================

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

// =========================================================
// OBTENER PARÁMETROS
// =========================================================

$buscar = trim(
    $_GET["buscar"] ?? ""
);

// =========================================================
// BÚSQUEDA
// =========================================================

$buscarLike = "%" . $buscar . "%";

// =========================================================
// CONSULTA
//
// IMPORTANTE:
// estado = 'ACTIVO' debe estar unido con AND.
// =========================================================

$sql = "

    SELECT
        id_asesor,
        nombre,
        apellidos,
        imagen,
        email,
        celular,
        cargo,
        fecha_registro,
        fecha_actualizacion,
        estado

    FROM asesores

    WHERE
        (
            nombre LIKE ?
            OR apellidos LIKE ?
            OR email LIKE ?
            OR cargo LIKE ?
            OR fecha_registro LIKE ?
        )

        AND estado = 'ACTIVO'

    ORDER BY
        id_asesor ASC

";

// =========================================================
// PREPARAR
// =========================================================

try {

    $stmt = $conexion->prepare($sql);

} catch (Throwable $e) {

    error_log(
        "Error prepare buscar asesores: " .
        $e->getMessage()
    );

    echo '
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Error al preparar la búsqueda de asesores.
            </div>
        </div>
    ';

    exit;
}

// =========================================================
// BIND
// =========================================================

try {

    $stmt->bind_param(
        "sssss",
        $buscarLike,
        $buscarLike,
        $buscarLike,
        $buscarLike,
        $buscarLike
    );

} catch (Throwable $e) {

    error_log(
        "Error bind buscar asesores: " .
        $e->getMessage()
    );

    $stmt->close();

    echo '
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Error al aplicar la búsqueda.
            </div>
        </div>
    ';

    exit;
}

// =========================================================
// EJECUTAR
// =========================================================

try {

    $stmt->execute();

} catch (Throwable $e) {

    error_log(
        "Error execute buscar asesores: " .
        $e->getMessage()
    );

    $stmt->close();

    echo '
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Error al realizar la búsqueda.
            </div>
        </div>
    ';

    exit;
}

// =========================================================
// RESULTADOS
//
// Usamos bind_result() para no depender de get_result()
// =========================================================

$stmt->store_result();

$total = $stmt->num_rows;

// =========================================================
// VARIABLES
// =========================================================

$stmt->bind_result(
    $idAsesor,
    $nombre,
    $apellidos,
    $imagen,
    $email,
    $celular,
    $cargo,
    $fechaRegistro,
    $fechaActualizacion,
    $estado
);

// =========================================================
// RESULTADOS
// =========================================================

if ($total > 0):

?>

<div class="row g-3">

    <?php while ($stmt->fetch()): ?>

        <?php

        // =================================================
        // NORMALIZAR DATOS
        // =================================================

        $idAsesor = (int) $idAsesor;

        $nombreAsesor = trim(
            ($nombre ?? "") . " " . ($apellidos ?? "")
        );

        if ($nombreAsesor === "") {
            $nombreAsesor = "Asesor inmobiliario";
        }

        $emailAsesor = trim(
            $email ?? ""
        );

        $celularAsesor = trim(
            $celular ?? ""
        );

        $cargoAsesor = trim(
            $cargo ?? ""
        );

        if ($cargoAsesor === "") {
            $cargoAsesor = "Asesor inmobiliario";
        }

        // =================================================
        // TELÉFONO WHATSAPP
        // =================================================

        $telefonoWhatsapp = "";

        if ($celularAsesor !== "") {

            $telefonoWhatsapp = preg_replace(
                "/\D/",
                "",
                $celularAsesor
            );

            if ($telefonoWhatsapp !== "") {

                // Si ya tiene código de país 51,
                // no volver a agregarlo.

                if (
                    substr(
                        $telefonoWhatsapp,
                        0,
                        2
                    ) !== "51"
                ) {

                    $telefonoWhatsapp =
                        "51" .
                        $telefonoWhatsapp;
                }
            }
        }

        // =================================================
        // MENSAJE WHATSAPP
        // =================================================

        $mensajeWhatsapp =
            "Hola " .
            $nombreAsesor .
            ",\n\n" .

            "Me comunico para solicitar " .
            "información sobre una propiedad.\n\n" .

            "Quedo atento/a a su respuesta.\n" .

            "Muchas gracias.";

        $mensajeWhatsappEncoded = urlencode(
            $mensajeWhatsapp
        );

        // =================================================
        // IMAGEN
        // =================================================

        $imagenAsesor = "";

        if (!empty($imagen)) {

            $imagenAsesor =
                "data:image/jpeg;base64," .
                base64_encode($imagen);
        }

        ?>

        <!-- =================================================
             COLUMNA ASESOR
        ================================================== -->

        <div
            class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-2 py-2"
        >

            <!-- =================================================
                 TARJETA ASESOR
            ================================================== -->

            <div class="card asesor-card text-center h-100">

                <!-- =================================================
                     FOTO
                ================================================== -->

                <div class="asesor-foto border-success">

                    <?php if ($imagenAsesor !== ""): ?>

                        <img
                            src="<?= e($imagenAsesor) ?>"
                            alt="Foto de <?= e($nombreAsesor) ?>"
                            loading="lazy"
                        >

                    <?php else: ?>

                        <img
                            src="img/usuario_default.png"
                            alt="Usuario sin fotografía"
                            loading="lazy"
                        >

                    <?php endif; ?>

                </div>

                <!-- =================================================
                     NOMBRE
                ================================================== -->

                <h6 class="mt-3 mb-1 fw-semibold">

                    <?= e($nombreAsesor) ?>

                </h6>

                <!-- =================================================
                     CARGO
                ================================================== -->

                <p
                    class="text-center fw-bold text-uppercase text-success mb-3"
                >

                    <?= e($cargoAsesor) ?>

                </p>

                <!-- =================================================
                     DATOS
                ================================================== -->

                <div class="asesor-info text-start px-3 pb-3">

                    <?php if ($emailAsesor !== ""): ?>

                        <p class="mb-2">

                            <i class="bi bi-envelope me-1"></i>

                            <span>
                                <?= e($emailAsesor) ?>
                            </span>

                        </p>

                    <?php endif; ?>


                    <?php if ($celularAsesor !== ""): ?>

                        <p class="mb-0">

                            <i class="bi bi-phone me-1"></i>

                            <span>
                                <?= e($celularAsesor) ?>
                            </span>

                        </p>

                    <?php endif; ?>

                </div>

                <!-- =================================================
                     BOTONES DE CONTACTO
                ================================================== -->

                <div
                    class="d-flex justify-content-center gap-2 mb-3"
                >

                    <!-- =================================================
                         WHATSAPP
                    ================================================== -->

                    <?php if ($telefonoWhatsapp !== ""): ?>

                        <a
                            href="https://wa.me/<?= e($telefonoWhatsapp) ?>?text=<?= e($mensajeWhatsappEncoded) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-success btn-icon"
                            title="Contactar por WhatsApp"
                            aria-label="Contactar por WhatsApp con <?= e($nombreAsesor) ?>"
                        >

                            <i class="bi bi-whatsapp"></i>

                        </a>

                    <?php endif; ?>


                    <!-- =================================================
                         LLAMAR
                    ================================================== -->

                    <?php if ($celularAsesor !== ""): ?>

                        <a
                            href="tel:<?= e($celularAsesor) ?>"
                            class="btn btn-danger btn-icon"
                            title="Llamar a <?= e($nombreAsesor) ?>"
                            aria-label="Llamar a <?= e($nombreAsesor) ?>"
                        >

                            <i class="bi bi-telephone-fill"></i>

                        </a>

                    <?php endif; ?>


                    <!-- =================================================
                         EMAIL
                    ================================================== -->

                    <?php if ($emailAsesor !== ""): ?>

                        <a
                            href="mailto:<?= e($emailAsesor) ?>"
                            class="btn btn-primary btn-icon"
                            title="Enviar correo a <?= e($nombreAsesor) ?>"
                            aria-label="Enviar correo a <?= e($nombreAsesor) ?>"
                        >

                            <i class="bi bi-envelope-fill"></i>

                        </a>

                    <?php endif; ?>

                </div>


            </div>

        </div>

    <?php endwhile; ?>

</div>

<?php

else:

?>

<!-- =========================================================
     SIN RESULTADOS
========================================================= -->

<div class="col-12">

    <div class="alert alert-warning text-center py-4">

        <div class="mb-2">

            <i
                class="bi bi-person-x"
                style="font-size: 2rem;"
            ></i>

        </div>

        <h5 class="mt-2 mb-2">

            No se encontraron asesores

        </h5>

        <p class="mb-0">

            No existen asesores activos que coincidan
            con los criterios de búsqueda.

        </p>

    </div>

</div>

<?php

endif;

// =========================================================
// CERRAR
// =========================================================

$stmt->free_result();

$stmt->close();

?>
