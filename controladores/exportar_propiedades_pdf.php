<?php
session_start();

if (!isset($_SESSION['usId'])) {
    die("Acceso no autorizado");
    header("Location: ../login.php");
}

require_once __DIR__ . "/conect_db.php";
require_once __DIR__ . "../../fpdf/fpdf.php";

/* =====================================================
   FUNCIÓN PARA MOSTRAR IMÁGENES BLOB EN FPDF
===================================================== */
function mostrarImagenFPDF($pdf, $blob, $x, $y, $w, $h)
{
    if (empty($blob)) return;

    $info = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_buffer($info, $blob);
    finfo_close($info);

    switch ($mime) {
        case 'image/jpeg':
            $ext = 'jpg';
            break;
        case 'image/png':
            $ext = 'png';
            break;
        default:
            return;
    }

    $tmp = tempnam(sys_get_temp_dir(), 'img_') . '.' . $ext;
    file_put_contents($tmp, $blob);

    $pdf->Image($tmp, $x, $y, $w, $h);
    unlink($tmp);
}

/* =====================================================
   DATOS USUARIO / EMPRESA
===================================================== */
$usId = (int)$_SESSION['usId'];

$empresa = "INMOBILIARIA";
$logo = null;

$res = $conexion->query("
    SELECT nombreEmpresa, imagen 
    FROM usuario_acceso 
    WHERE id_user = $usId
");

if ($res && $row = $res->fetch_assoc()) {
    $empresa = $row['nombreEmpresa'];
    $logo = $row['imagen'];
}

/* =====================================================
   CONSULTA PROPIEDADES
===================================================== */
$query = "
    SELECT 
        p.codigo,
        p.nombre,
        p.tamano_area_metros,
        p.ubicacion,
        p.precio,
        p.fecha_registro,
        p.imagen,
        IFNULL(c.nombre,'SIN CATEGORÍA') AS categoria
    FROM propiedades p
    LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
    WHERE p.id_user = $usId
    ORDER BY p.id_propiedad DESC
";

$resultado = $conexion->query($query);

/* =====================================================
   CREAR PDF
===================================================== */
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

/* LOGO */
if ($logo) {
    mostrarImagenFPDF($pdf, $logo, 10, 8, 25, 25);
}

/* TÍTULOS */
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('LISTA DE PROPIEDADES'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode('Empresa: ' . $empresa), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1, 'C');

$pdf->Ln(5);

/* =====================================================
   TABLA
===================================================== */
$w = [30, 60, 45, 25, 55, 30, 30, 25];

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);

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

foreach ($headers as $i => $h) {
    $pdf->Cell($w[$i], 8, utf8_decode($h), 1, 0, 'C', true);
}
$pdf->Ln();

/* CUERPO */
$pdf->SetFont('Arial', '', 8);

if ($resultado->num_rows === 0) {
    $pdf->Cell(array_sum($w), 10, 'No hay propiedades registradas.', 1, 1, 'C');
} else {
    while ($fila = $resultado->fetch_assoc()) {

        $altura = 18;

        $pdf->Cell($w[0], $altura, $fila['codigo'], 1);
        $pdf->Cell($w[1], $altura, utf8_decode($fila['nombre']), 1);
        $pdf->Cell($w[2], $altura, utf8_decode($fila['categoria']), 1);
        $pdf->Cell($w[3], $altura, number_format($fila['tamano_area_metros'], 2), 1);
        $pdf->Cell($w[4], $altura, utf8_decode($fila['ubicacion']), 1);
        $pdf->Cell($w[5], $altura, 'S/. ' . number_format($fila['precio'], 2), 1);
        $pdf->Cell($w[6], $altura, date('d/m/Y', strtotime($fila['fecha_registro'])), 1);

        // Imagen
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        mostrarImagenFPDF($pdf, $fila['imagen'], $x + 3, $y + 2, 14, 14);
        $pdf->Cell($w[7], $altura, '', 1);

        $pdf->Ln();
    }
}

/* =====================================================
   SALIDA
===================================================== */
$pdf->Output('D', 'lista_propiedades.pdf');
exit;
