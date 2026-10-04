<?php

// ============================================================
// Inmobiliaria Iquitos
// Archivo: controladores/ajax_lista_testimonios.php
// Módulo: Lista de Testimonios AJAX
// ============================================================

session_start();


// ============================================================
// VALIDAR SESIÓN
// ============================================================

if (!isset($_SESSION['usId'])) {

    http_response_code(401);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida.'
    ]);

    exit();
}


// ============================================================
// RESPUESTA JSON
// ============================================================

header(
    'Content-Type: application/json; charset=utf-8'
);


// ============================================================
// CONEXIÓN
// ============================================================

include 'conect_db.php';


// ============================================================
// USUARIO
// ============================================================

$idUser =
    (int) $_SESSION['usId'];


// ============================================================
// CONFIGURACIÓN
// ============================================================

$MAX_TESTIMONIOS = 4;


// ============================================================
// CONTAR TESTIMONIOS
// ============================================================

$totalTestimonios = 0;


$sqlContador = "
    SELECT COUNT(*) AS total
    FROM testimonios
    WHERE id_user = ?
";


$stmtContador =
    mysqli_prepare(
        $conexion,
        $sqlContador
    );


if (!$stmtContador) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar el contador.'
    ]);

    exit();
}


mysqli_stmt_bind_param(
    $stmtContador,
    "i",
    $idUser
);


mysqli_stmt_execute(
    $stmtContador
);


$resultadoContador =
    mysqli_stmt_get_result(
        $stmtContador
    );


if (
    $resultadoContador &&
    $filaContador =
    mysqli_fetch_assoc(
        $resultadoContador
    )
) {

    $totalTestimonios =
        (int) $filaContador['total'];
}


mysqli_stmt_close(
    $stmtContador
);


// ============================================================
// CALCULAR KPI
// ============================================================

$testimoniosRestantes =
    max(
        0,
        $MAX_TESTIMONIOS -
            $totalTestimonios
    );


$limiteAlcanzado =
    $totalTestimonios >=
    $MAX_TESTIMONIOS;


// ============================================================
// OBTENER TESTIMONIOS
// ============================================================

$testimonios = [];


$sqlTestimonios = "
    SELECT
        id_testimonio,
        id_user,
        comentario,
        fecha_registro,
        nombre,
        apellidos,
        fecha_actualizado,
        imagen,
        video
    FROM testimonios
    WHERE id_user = ?
    ORDER BY id_testimonio DESC
";


$stmtTestimonios =
    mysqli_prepare(
        $conexion,
        $sqlTestimonios
    );


if (!$stmtTestimonios) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' =>
        'Error al preparar la consulta de testimonios.'
    ]);

    exit();
}


mysqli_stmt_bind_param(
    $stmtTestimonios,
    "i",
    $idUser
);


mysqli_stmt_execute(
    $stmtTestimonios
);


$resultadoTestimonios =
    mysqli_stmt_get_result(
        $stmtTestimonios
    );


if ($resultadoTestimonios) {

    while (
        $fila =
        mysqli_fetch_assoc(
            $resultadoTestimonios
        )
    ) {

        $testimonios[] =
            $fila;
    }
}


mysqli_stmt_close(
    $stmtTestimonios
);


// ============================================================
// FUNCIONES
// ============================================================

function formatearFechaTestimonio($fecha)
{

    if (empty($fecha)) {
        return '-';
    }


    $timestamp =
        strtotime($fecha);


    if (!$timestamp) {
        return $fecha;
    }


    return date(
        'd/m/Y',
        $timestamp
    );
}


function obtenerInicialesTestimonio(
    $nombre,
    $apellidos
) {

    $nombre =
        trim(
            (string) $nombre
        );


    $apellidos =
        trim(
            (string) $apellidos
        );


    $inicialNombre = '';
    $inicialApellido = '';


    if ($nombre !== '') {

        $inicialNombre =
            mb_substr(
                $nombre,
                0,
                1,
                'UTF-8'
            );
    }


    if ($apellidos !== '') {

        $inicialApellido =
            mb_substr(
                $apellidos,
                0,
                1,
                'UTF-8'
            );
    }


    return mb_strtoupper(
        $inicialNombre .
            $inicialApellido,
        'UTF-8'
    );
}


// ============================================================
// CONSTRUIR HTML
// ============================================================

ob_start();


if (empty($testimonios)):

?>

    <!-- ========================================================
         SIN TESTIMONIOS
    ========================================================= -->

    <div class="testimonios-empty">

        <div class="testimonios-empty-icon">

            <i
                class="fa-regular fa-comments">
            </i>

        </div>


        <h3>
            No hay testimonios registrados
        </h3>


        <p>
            Todavía no has registrado ningún
            testimonio de clientes.
        </p>


        <a
            href="adm_registrar_testimonio.php"
            class="btn btn-success btn-admin">

            <i
                class="fa-solid fa-plus me-1">
            </i>

            Registrar primer testimonio

        </a>

    </div>


<?php

else:

?>

    <!-- ========================================================
         TABLA RESPONSIVE
    ========================================================= -->

    <div class="table-responsive">

        <table
            class="table align-middle mb-0 testimonios-table">


            <thead>

                <tr>

                    <th>
                        Cliente
                    </th>

                    <th>
                        Testimonio
                    </th>

                    <th>
                        Multimedia
                    </th>

                    <th>
                        Fecha
                    </th>

                    <th class="text-center">
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php foreach (
                    $testimonios
                    as $testimonio
                ): ?>


                    <?php

                    $idTestimonio =
                        (int)
                        $testimonio['id_testimonio'];


                    $nombre =
                        trim(
                            $testimonio['nombre'] ?? ''
                        );


                    $apellidos =
                        trim(
                            $testimonio['apellidos'] ?? ''
                        );


                    $nombreCompleto =
                        trim(
                            $nombre .
                                ' ' .
                                $apellidos
                        );


                    $iniciales =
                        obtenerInicialesTestimonio(
                            $nombre,
                            $apellidos
                        );


                    $comentario =
                        trim(
                            $testimonio['comentario'] ?? ''
                        );


                    $tieneImagen =
                        !empty($testimonio['imagen']);


                    $tieneVideo =
                        !empty($testimonio['video']);

                    ?>


                    <tr
                        data-id="<?= $idTestimonio ?>">


                        <!-- ======================================
                             CLIENTE
                        ======================================= -->

                        <td>

                            <div
                                class="testimonial-client">


                                <div
                                    class="testimonial-avatar">


                                    <?php if (
                                        $tieneImagen
                                    ): ?>

                                        <img
                                            src="data:image/jpeg;base64,<?= base64_encode(
                                                                            $testimonio['imagen']
                                                                        ) ?>"
                                            alt="<?= htmlspecialchars(
                                                        $nombreCompleto,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                    <?php else: ?>

                                        <?= htmlspecialchars(
                                            $iniciales,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    <?php endif; ?>


                                </div>


                                <div
                                    class="testimonial-client-info">


                                    <strong>

                                        <?= htmlspecialchars(
                                            $nombreCompleto,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </strong>


                                    <span>

                                        ID #<?= $idTestimonio ?>

                                    </span>


                                </div>


                            </div>

                        </td>


                        <!-- ======================================
                             COMENTARIO
                        ======================================= -->

                        <td>

                            <div
                                class="testimonial-comment"
                                title="<?= htmlspecialchars(
                                            $comentario,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                                <?= htmlspecialchars(
                                    $comentario,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </td>


                        <!-- ======================================
                             MULTIMEDIA
                        ======================================= -->

                        <td>

                            <div
                                class="testimonial-media">


                                <?php if (
                                    $tieneImagen
                                ): ?>

                                    <span
                                        class="media-badge image">

                                        <i
                                            class="fa-regular fa-image">
                                        </i>

                                        Imagen

                                    </span>

                                <?php endif; ?>


                                <?php if (
                                    $tieneVideo
                                ): ?>

                                    <span
                                        class="media-badge video">

                                        <i
                                            class="fa-solid fa-video">
                                        </i>

                                        Video

                                    </span>

                                <?php endif; ?>


                                <?php if (
                                    !$tieneImagen &&
                                    !$tieneVideo
                                ): ?>

                                    <span
                                        class="media-badge none">

                                        Sin multimedia

                                    </span>

                                <?php endif; ?>


                            </div>

                        </td>


                        <!-- ======================================
                             FECHA
                        ======================================= -->

                        <td>

                            <span
                                class="testimonial-date">

                                <i
                                    class="fa-regular fa-calendar me-1">
                                </i>

                                <?= formatearFechaTestimonio(
                                    $testimonio['fecha_registro']
                                ) ?>

                            </span>

                        </td>


                        <!-- ======================================
                             ACCIONES
                        ======================================= -->

                        <td>

                            <div
                                class="testimonial-actions">


                                <!-- VER -->

                                <button
                                    type="button"
                                    class="btn-action btn-view-testimonio"
                                    data-id="<?= $idTestimonio ?>"
                                    title="Ver testimonio">

                                    <i
                                        class="fa-regular fa-eye">
                                    </i>

                                </button>


                                <!-- EDITAR -->
                                <button
                                    type="button"
                                    class="btn-action btn-editar-testimonio"
                                    data-id="<?= $idTestimonio ?>"
                                    title="Editar testimonio">
                                    <i
                                        class="fa-regular fa-pen-to-square">
                                    </i>
                                </button>

                                <!-- ELIMINAR -->

                                <button
                                    type="button"
                                    class="btn-action btn-delete-testimonio"
                                    data-id="<?= $idTestimonio ?>"
                                    data-nombre="<?= htmlspecialchars(
                                                        $nombreCompleto,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                    title="Eliminar testimonio">

                                    <i
                                        class="fa-regular fa-trash-can">
                                    </i>

                                </button>


                            </div>

                        </td>


                    </tr>


                <?php endforeach; ?>


            </tbody>

        </table>

    </div>


<?php

endif;


$html =
    ob_get_clean();


// ============================================================
// RESPUESTA
// ============================================================

echo json_encode(
    [
        'success' =>
        true,

        'total' =>
        $totalTestimonios,

        'maximo' =>
        $MAX_TESTIMONIOS,

        'disponibles' =>
        $testimoniosRestantes,

        'limite_alcanzado' =>
        $limiteAlcanzado,

        'html' =>
        $html
    ],
    JSON_UNESCAPED_UNICODE
);

exit();
