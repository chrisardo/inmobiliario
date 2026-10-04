<?php
// =========================================================
// CoDevPro Technology
// Archivo: controladores/exportar_propiedades_pdf.php
// Módulo: Propiedades
// Función: Exportar propiedades a PDF
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================
// VERIFICAR SESIÓN
// =========================================================

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit();
}

// =========================================================
// CONEXIÓN
// =========================================================

require_once __DIR__ . '/conect_db.php';

// =========================================================
// FPDF
// =========================================================
//
// Si la carpeta fpdf está en la raíz del proyecto:
//
// proyecto/
// ├── adm/
// ├── controladores/
// ├── fpdf/
// │   └── fpdf.php
//
// Desde controladores debemos subir un nivel.
//

require_once __DIR__ . '/../fpdf/fpdf.php';


// =========================================================
// VERIFICAR CONEXIÓN
// =========================================================

if (!isset($conexion) || !$conexion) {
    die('No se pudo establecer la conexión con la base de datos.');
}


// =========================================================
// FUNCIÓN PARA CONVERTIR TEXTO A ISO-8859-1
// =========================================================

function textoPDF($texto)
{
    $texto = (string) $texto;

    return iconv(
        'UTF-8',
        'ISO-8859-1//TRANSLIT',
        $texto
    );
}


// =========================================================
// FUNCIÓN PARA MOSTRAR IMÁGENES BLOB EN FPDF
// =========================================================

function mostrarImagenFPDF($pdf, $blob, $x, $y, $w, $h)
{
    if (empty($blob)) {
        return;
    }

    $info = finfo_open(FILEINFO_MIME_TYPE);

    if (!$info) {
        return;
    }

    $mime = finfo_buffer($info, $blob);

    finfo_close($info);

    $ext = null;

    switch ($mime) {

        case 'image/jpeg':
            $ext = 'jpg';
            break;

        case 'image/png':
            $ext = 'png';
            break;

        case 'image/webp':
            /*
             * FPDF tradicional no trabaja directamente con WEBP.
             * Intentamos convertirlo a JPG mediante GD.
             */
            if (function_exists('imagecreatefromwebp')) {

                $imagenWebp = @imagecreatefromwebp(
                    'data://image/webp;base64,' .
                    base64_encode($blob)
                );

                if ($imagenWebp) {

                    $tmpBase = tempnam(
                        sys_get_temp_dir(),
                        'img_'
                    );

                    $tmpJpg = $tmpBase . '.jpg';

                    @imagejpeg(
                        $imagenWebp,
                        $tmpJpg,
                        90
                    );

                    imagedestroy($imagenWebp);

                    if (file_exists($tmpJpg)) {

                        $pdf->Image(
                            $tmpJpg,
                            $x,
                            $y,
                            $w,
                            $h
                        );

                        @unlink($tmpBase);
                        @unlink($tmpJpg);

                        return;
                    }

                    @unlink($tmpBase);
                }
            }

            return;

        default:
            return;
    }


    // =====================================================
    // CREAR ARCHIVO TEMPORAL
    // =====================================================

    $tmpBase = tempnam(
        sys_get_temp_dir(),
        'img_'
    );

    if (!$tmpBase) {
        return;
    }

    $tmpArchivo = $tmpBase . '.' . $ext;

    if (@file_put_contents($tmpArchivo, $blob) === false) {

        @unlink($tmpBase);

        return;
    }


    // =====================================================
    // INSERTAR IMAGEN EN PDF
    // =====================================================

    try {

        $pdf->Image(
            $tmpArchivo,
            $x,
            $y,
            $w,
            $h
        );

    } catch (Throwable $e) {

        // No detener todo el PDF si una imagen falla.
    }


    // =====================================================
    // ELIMINAR TEMPORALES
    // =====================================================

    @unlink($tmpArchivo);
    @unlink($tmpBase);
}


// =========================================================
// DATOS DEL USUARIO / EMPRESA
// =========================================================

$usId = (int) $_SESSION['usId'];

$empresa = 'INMOBILIARIA';
$logo = null;


// =========================================================
// OBTENER DATOS DE LA EMPRESA
// =========================================================

$stmtEmpresa = $conexion->prepare("
    SELECT
        nombreEmpresa,
        imagen
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
");

if ($stmtEmpresa) {

    $stmtEmpresa->bind_param(
        'i',
        $usId
    );

    $stmtEmpresa->execute();

    $resultadoEmpresa =
        $stmtEmpresa->get_result();

    if (
        $resultadoEmpresa &&
        $filaEmpresa = $resultadoEmpresa->fetch_assoc()
    ) {

        if (!empty($filaEmpresa['nombreEmpresa'])) {

            $empresa =
                $filaEmpresa['nombreEmpresa'];
        }

        if (!empty($filaEmpresa['imagen'])) {

            $logo =
                $filaEmpresa['imagen'];
        }
    }

    $stmtEmpresa->close();
}


// =========================================================
// CONSULTA DE PROPIEDADES
// =========================================================
//
// IMPORTANTE:
//
// La tabla propiedades NO tiene columna imagen.
//
// Las imágenes están en:
//
// imagenes.id_propiedad
// imagenes.imagenes
// imagenes.orden
//
// Se obtiene solamente la primera imagen de cada propiedad.
//

$query = "
    SELECT

        p.id_propiedad,

        p.codigo,

        p.nombre,

        p.tamano_area_metros,

        p.ubicacion,

        p.precio,

        p.precio_anterior,

        p.fecha_registro,

        IFNULL(
            c.nombre,
            'SIN CATEGORÍA'
        ) AS categoria,

        (
            SELECT i.imagenes
            FROM imagenes i
            WHERE i.id_propiedad = p.id_propiedad
            ORDER BY
                i.orden ASC,
                i.id_imagen ASC
            LIMIT 1
        ) AS imagen_principal

    FROM propiedades p

    LEFT JOIN categoria c
        ON c.id_categoria = p.id_categoria
        AND c.Eliminado = 0

    WHERE
        p.id_user = ?
        AND p.Eliminado = 0

    ORDER BY
        p.id_propiedad DESC
";

$stmtPropiedades =
    $conexion->prepare($query);

if (!$stmtPropiedades) {

    die(
        'No se pudo preparar la consulta de propiedades.'
    );
}

$stmtPropiedades->bind_param(
    'i',
    $usId
);

$stmtPropiedades->execute();

$resultado =
    $stmtPropiedades->get_result();


// =========================================================
// CREAR PDF
// =========================================================

$pdf = new FPDF(
    'L',
    'mm',
    'A4'
);

$pdf->SetMargins(
    8,
    8,
    8
);

$pdf->SetAutoPageBreak(
    true,
    10
);

$pdf->AddPage();


// =========================================================
// LOGO
// =========================================================

if (!empty($logo)) {

    mostrarImagenFPDF(
        $pdf,
        $logo,
        10,
        8,
        25,
        25
    );
}


// =========================================================
// ENCABEZADO
// =========================================================

$pdf->SetFont(
    'Arial',
    'B',
    16
);

$pdf->Cell(
    0,
    8,
    textoPDF('LISTA DE PROPIEDADES'),
    0,
    1,
    'C'
);


$pdf->SetFont(
    'Arial',
    '',
    11
);

$pdf->Cell(
    0,
    7,
    textoPDF(
        'Empresa: ' . $empresa
    ),
    0,
    1,
    'C'
);


$pdf->SetFont(
    'Arial',
    '',
    9
);

$pdf->Cell(
    0,
    6,
    textoPDF(
        'Fecha de exportación: ' .
        date('d/m/Y H:i')
    ),
    0,
    1,
    'C'
);


$pdf->Ln(5);


// =========================================================
// INFORMACIÓN GENERAL
// =========================================================

$totalPropiedades = $resultado
    ? $resultado->num_rows
    : 0;

$pdf->SetFont(
    'Arial',
    '',
    9
);

$pdf->Cell(
    0,
    6,
    textoPDF(
        'Total de propiedades: ' .
        number_format($totalPropiedades)
    ),
    0,
    1,
    'L'
);

$pdf->Ln(2);


// =========================================================
// CONFIGURACIÓN DE TABLA
// =========================================================
//
// Orientación horizontal A4:
//
// Ancho útil aproximado: 281 mm
//
// Código       27
// Propiedad    55
// Categoría    42
// Área         25
// Ubicación    50
// Precio       32
// Fecha        25
// Imagen       25
//
// TOTAL        281
//

$w = [
    27,
    55,
    42,
    25,
    50,
    32,
    25,
    25
];

$alturaFila = 20;


// =========================================================
// ENCABEZADOS
// =========================================================

$headers = [

    'Código',

    'Propiedad',

    'Categoría',

    'Área (m²)',

    'Ubicación',

    'Precio',

    'Fecha',

    'Imagen'
];


$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->SetFillColor(
    220,
    220,
    220
);

$pdf->SetTextColor(
    0,
    0,
    0
);


foreach ($headers as $i => $header) {

    $pdf->Cell(
        $w[$i],
        8,
        textoPDF($header),
        1,
        0,
        'C',
        true
    );
}

$pdf->Ln();


// =========================================================
// CUERPO DE LA TABLA
// =========================================================

$pdf->SetFont(
    'Arial',
    '',
    7.5
);


// =========================================================
// SIN PROPIEDADES
// =========================================================

if (!$resultado || $resultado->num_rows === 0) {

    $pdf->Cell(
        array_sum($w),
        12,
        textoPDF(
            'No hay propiedades registradas.'
        ),
        1,
        1,
        'C'
    );

} else {


    // =====================================================
    // RECORRER PROPIEDADES
    // =====================================================

    while (
        $fila = $resultado->fetch_assoc()
    ) {

        $altura = $alturaFila;


        // =================================================
        // COMPROBAR ESPACIO EN LA PÁGINA
        // =================================================

        if (
            $pdf->GetY() + $altura >
            190
        ) {

            $pdf->AddPage();

            // Repetir encabezado

            $pdf->SetFont(
                'Arial',
                'B',
                8
            );

            $pdf->SetFillColor(
                220,
                220,
                220
            );

            foreach (
                $headers as $i => $header
            ) {

                $pdf->Cell(
                    $w[$i],
                    8,
                    textoPDF($header),
                    1,
                    0,
                    'C',
                    true
                );
            }

            $pdf->Ln();

            $pdf->SetFont(
                'Arial',
                '',
                7.5
            );
        }


        // =================================================
        // DATOS
        // =================================================

        $codigo =
            $fila['codigo'] ?? '';

        $nombre =
            $fila['nombre'] ?? '';

        $categoria =
            $fila['categoria'] ?? 'SIN CATEGORÍA';

        $area =
            (float) (
                $fila['tamano_area_metros']
                ?? 0
            );

        $ubicacion =
            $fila['ubicacion'] ?? '';

        $precio =
            (float) (
                $fila['precio']
                ?? 0
            );

        $fechaRegistro =
            $fila['fecha_registro']
            ?? null;


        // =================================================
        // FECHA
        // =================================================

        $fechaFormateada = '';

        if (
            !empty($fechaRegistro) &&
            strtotime($fechaRegistro)
        ) {

            $fechaFormateada =
                date(
                    'd/m/Y',
                    strtotime($fechaRegistro)
                );
        }


        // =================================================
        // CÓDIGO
        // =================================================

        $pdf->Cell(
            $w[0],
            $altura,
            textoPDF($codigo),
            1,
            0,
            'C'
        );


        // =================================================
        // PROPIEDAD
        // =================================================

        $pdf->Cell(
            $w[1],
            $altura,
            textoPDF($nombre),
            1,
            0,
            'L'
        );


        // =================================================
        // CATEGORÍA
        // =================================================

        $pdf->Cell(
            $w[2],
            $altura,
            textoPDF($categoria),
            1,
            0,
            'L'
        );


        // =================================================
        // ÁREA
        // =================================================

        $pdf->Cell(
            $w[3],
            $altura,
            number_format(
                $area,
                2
            ),
            1,
            0,
            'C'
        );


        // =================================================
        // UBICACIÓN
        // =================================================

        $pdf->Cell(
            $w[4],
            $altura,
            textoPDF($ubicacion),
            1,
            0,
            'L'
        );


        // =================================================
        // PRECIO
        // =================================================

        $pdf->Cell(
            $w[5],
            $altura,
            textoPDF(
                'S/. ' .
                number_format(
                    $precio,
                    2
                )
            ),
            1,
            0,
            'R'
        );


        // =================================================
        // FECHA
        // =================================================

        $pdf->Cell(
            $w[6],
            $altura,
            $fechaFormateada,
            1,
            0,
            'C'
        );


        // =================================================
        // IMAGEN
        // =================================================

        $x =
            $pdf->GetX();

        $y =
            $pdf->GetY();


        // Celda de imagen

        $pdf->Cell(
            $w[7],
            $altura,
            '',
            1,
            0,
            'C'
        );


        // Imagen principal

        if (
            !empty(
                $fila['imagen_principal']
            )
        ) {

            mostrarImagenFPDF(
                $pdf,
                $fila['imagen_principal'],
                $x + 5,
                $y + 3,
                15,
                14
            );
        }


        $pdf->Ln();
    }
}


// =========================================================
// PIE DE PÁGINA
// =========================================================

$pdf->Ln(4);

$pdf->SetFont(
    'Arial',
    'I',
    8
);

$pdf->Cell(
    0,
    5,
    textoPDF(
        'Documento generado automáticamente por el sistema administrativo.'
    ),
    0,
    1,
    'C'
);


// =========================================================
// CERRAR STATEMENT
// =========================================================

$stmtPropiedades->close();


// =========================================================
// SALIDA PDF
// =========================================================

$nombreArchivo =
    'lista_propiedades_' .
    date('Y-m-d_H-i-s') .
    '.pdf';

$pdf->Output(
    'D',
    $nombreArchivo
);

exit;
