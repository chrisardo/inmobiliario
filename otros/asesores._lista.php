<!-- =========================================================
     ASESORES
========================================================= -->

<div id="resultadoAsesores" class="container position-relative">

    <div class="container my-0 mb-3 py-3 position-relative">

        <div class="row g-3">

            <?php
            // =========================================================
            // CONEXIÓN
            // =========================================================

            require_once 'controladores/conect_db.php';


            // =========================================================
            // FUNCIÓN DE ESCAPE HTML
            // =========================================================

            if (!function_exists('e')) {

                function e($valor): string
                {
                    return htmlspecialchars(
                        (string)($valor ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                }
            }


            // =========================================================
            // CONSULTA DE ASESORES ACTIVOS
            // =========================================================

            $sqlAsesores = "
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
                WHERE estado = 'ACTIVO'
                ORDER BY id_asesor ASC
            ";

            $resultadoAsesores = $conexion->query($sqlAsesores);
            ?>


            <?php if ($resultadoAsesores && $resultadoAsesores->num_rows > 0): ?>

                <!-- =================================================
                     LISTADO DE ASESORES
                ================================================== -->

                <?php while ($fila = $resultadoAsesores->fetch_assoc()): ?>

                    <?php

                    // =================================================
                    // DATOS DEL ASESOR
                    // =================================================

                    $idAsesor = (int)($fila['id_asesor'] ?? 0);

                    $nombreAsesor = trim(
                        ($fila['nombre'] ?? '') . ' ' .
                        ($fila['apellidos'] ?? '')
                    );

                    if ($nombreAsesor === '') {
                        $nombreAsesor = 'Asesor inmobiliario';
                    }

                    $emailAsesor = trim(
                        $fila['email'] ?? ''
                    );

                    $celularAsesor = trim(
                        $fila['celular'] ?? ''
                    );

                    $cargoAsesor = trim(
                        $fila['cargo'] ?? ''
                    );

                    if ($cargoAsesor === '') {
                        $cargoAsesor = 'Asesor inmobiliario';
                    }


                    // =================================================
                    // TELÉFONO WHATSAPP
                    // =================================================

                    $telefonoWhatsapp = '';

                    if ($celularAsesor !== '') {

                        $telefonoWhatsapp = preg_replace(
                            '/\D/',
                            '',
                            $celularAsesor
                        );

                        if ($telefonoWhatsapp !== '') {

                            if (
                                substr(
                                    $telefonoWhatsapp,
                                    0,
                                    2
                                ) !== '51'
                            ) {

                                $telefonoWhatsapp =
                                    '51' . $telefonoWhatsapp;
                            }
                        }
                    }


                    // =================================================
                    // MENSAJE WHATSAPP
                    // =================================================

                    $mensajeWhatsapp =
                        "Estimado/a " .
                        $nombreAsesor .
                        ",\n\n" .
                        "Me comunico para solicitar " .
                        "información sobre sus servicios " .
                        "como asesor inmobiliario.\n\n" .
                        "Quedo atento/a a su respuesta.\n" .
                        "Muchas gracias.";


                    $mensajeWhatsappEncoded = urlencode(
                        $mensajeWhatsapp
                    );


                    // =================================================
                    // IMAGEN
                    // =================================================

                    $imagenAsesor = null;

                    if (!empty($fila['imagen'])) {

                        $imagenAsesor =
                            'data:image/jpeg;base64,' .
                            base64_encode(
                                $fila['imagen']
                            );
                    }

                    ?>

                    <!-- =================================================
                         TARJETA DEL ASESOR
                    ================================================== -->

                    <div
                        class="col-12 col-sm-6 col-lg-4 col-xl-3">

                        <article
                            class="card asesor-card h-100 text-center">

                            <!-- =================================================
                                 FOTO
                            ================================================== -->

                            <div class="asesor-foto">

                                <?php if ($imagenAsesor): ?>

                                    <img
                                        src="<?= e($imagenAsesor) ?>"
                                        alt="<?= e($nombreAsesor) ?>"
                                        loading="lazy">

                                <?php else: ?>

                                    <img
                                        src="img/user-default.png"
                                        alt="Asesor sin fotografía"
                                        loading="lazy">

                                <?php endif; ?>

                            </div>


                            <!-- =================================================
                                 INFORMACIÓN PRINCIPAL
                            ================================================== -->

                            <div class="card-body">

                                <h6
                                    class="mt-2 mb-1 fw-semibold">

                                    <?= e($nombreAsesor) ?>

                                </h6>


                                <!-- CARGO -->

                                <p class="empresa mb-3">

                                    <?= e($cargoAsesor) ?>

                                </p>


                                <!-- =================================================
                                     DATOS
                                ================================================== -->

                                <div
                                    class="asesor-info text-start">

                                    <?php if ($emailAsesor !== ''): ?>

                                        <p class="mb-2">

                                            <i
                                                class="bi bi-envelope me-2">
                                            </i>

                                            <span>
                                                <?= e($emailAsesor) ?>
                                            </span>

                                        </p>

                                    <?php endif; ?>


                                    <?php if ($celularAsesor !== ''): ?>

                                        <p class="mb-0">

                                            <i
                                                class="bi bi-phone me-2">
                                            </i>

                                            <span>
                                                <?= e($celularAsesor) ?>
                                            </span>

                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <!-- =================================================
                                 BOTONES
                            ================================================== -->

                            <div
                                class="d-flex justify-content-center gap-2 pb-3 px-3">

                                <!-- WHATSAPP -->

                                <?php if ($telefonoWhatsapp !== ''): ?>

                                    <a
                                        href="https://wa.me/<?= e($telefonoWhatsapp) ?>?text=<?= e($mensajeWhatsappEncoded) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-success btn-icon"
                                        title="Enviar WhatsApp"
                                        aria-label="Enviar WhatsApp a <?= e($nombreAsesor) ?>">

                                        <i
                                            class="bi bi-whatsapp">
                                        </i>

                                    </a>

                                <?php endif; ?>


                                <!-- LLAMAR -->

                                <?php if ($celularAsesor !== ''): ?>

                                    <a
                                        href="tel:<?= e($celularAsesor) ?>"
                                        class="btn btn-danger btn-icon"
                                        title="Llamar"
                                        aria-label="Llamar a <?= e($nombreAsesor) ?>">

                                        <i
                                            class="bi bi-telephone-fill">
                                        </i>

                                    </a>

                                <?php endif; ?>


                                <!-- CORREO -->

                                <?php if ($emailAsesor !== ''): ?>

                                    <a
                                        href="mailto:<?= e($emailAsesor) ?>"
                                        class="btn btn-primary btn-icon"
                                        title="Enviar correo"
                                        aria-label="Enviar correo a <?= e($nombreAsesor) ?>">

                                        <i
                                            class="bi bi-envelope-fill">
                                        </i>

                                    </a>

                                <?php endif; ?>


                                <!-- =================================================
                                     VER DETALLE
                                ================================================== -->

                                <?php if ($idAsesor > 0): ?>

                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-icon btnVerDetalleAsesor"
                                        data-id="<?= $idAsesor ?>"
                                        title="Ver detalle"
                                        aria-label="Ver detalle de <?= e($nombreAsesor) ?>">

                                        <i
                                            class="bi bi-eye-fill">
                                        </i>

                                    </button>

                                <?php endif; ?>

                            </div>

                        </article>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <!-- =========================================================
                     SIN ASESORES
                ========================================================== -->

                <div class="col-12">

                    <div
                        class="alert alert-warning text-center py-4">

                        <i
                            class="bi bi-person-x fs-1 text-success d-block mb-2">
                        </i>

                        <h5 class="mt-2">
                            No hay asesores registrados
                        </h5>

                        <p class="mb-0">

                            Actualmente no contamos con asesores
                            disponibles.

                            Por favor, vuelve a visitarnos pronto.

                        </p>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>