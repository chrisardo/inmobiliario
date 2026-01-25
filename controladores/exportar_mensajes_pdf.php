<?php
session_start();

/* =========================
   VALIDAR SESIÓN
========================= */
if (!isset($_SESSION['usId'])) {
    die("Acceso no autorizado");
    header("Location: ../login.php");
}

$usId = (int) $_SESSION['usId'];

/* =========================
   CONEXIÓN + FPDF
========================= */
require_once __DIR__ . "/conect_db.php";
require_once __DIR__ . "../../fpdf/fpdf.php";

/* =========================
   DATOS EMPRESA
========================= */
$empresa = "INMOBILIARIA";
$logo = null;

$resEmpresa = $conexion->query("
    SELECT nombreEmpresa, imagen
    FROM usuario_acceso
");

if ($resEmpresa && $rowEmp = $resEmpresa->fetch_assoc()) {
    $empresa = $rowEmp['nombreEmpresa'];
    $logo = $rowEmp['imagen'];
}

/* =========================
   CONSULTA MENSAJES + PROPIEDAD
========================= */
$query = "
    SELECT
        m.nombre,
        m.apellidos,
        m.email,
        m.celular,
        m.mensaje,
        m.fecha_registro,
        IFNULL(p.nombre, 'SIN PROPIEDAD') AS propiedad
    FROM mensajes m
    LEFT JOIN propiedades p ON p.id_propiedad = m.id_propiedad
    ORDER BY m.fecha_registro DESC
";

$resultado = $conexion->query($query);

if (!$resultado) {
    die("Error SQL: " . $conexion->error);
}

/* =========================
   FUNCIÓN IMAGEN LOGO
========================= */
function mostrarImagen($pdf, $blob, $x, $y, $w, $h)
{
    if (empty($blob)) return;

    $info = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_buffer($info, $blob);
    finfo_close($info);

    $ext = ($mime === 'image/png') ? 'png' : 'jpg';
    $tmp = tempnam(sys_get_temp_dir(), 'img_') . '.' . $ext;
    file_put_contents($tmp, $blob);

    $pdf->Image($tmp, $x, $y, $w, $h);
    unlink($tmp);
}

/* =========================
   CREAR PDF
========================= */
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

/* LOGO */
if ($logo) {
    mostrarImagen($pdf, $logo, 10, 8, 25, 25);
}

/* TÍTULOS */
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('LISTA DE MENSAJES'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode('Empresa: ' . $empresa), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(5);

/* =========================
   TABLA
========================= */
$w = [40, 45, 60, 30, 45, 90, 30];

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);

$headers = [
    'Nombre',
    'Apellidos',
    'Email',
    'Celular',
    'Propiedad',
    'Mensaje',
    'Fecha'
];

foreach ($headers as $i => $h) {
    $pdf->Cell($w[$i], 8, utf8_decode($h), 1, 0, 'C', true);
}
$pdf->Ln();

/* =========================
   CUERPO
========================= */
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0);

if ($resultado->num_rows === 0) {

    $pdf->Cell(array_sum($w), 10, 'No hay mensajes registrados.', 1, 1, 'C');
} else {

    while ($fila = $resultado->fetch_assoc()) {

        $pdf->Cell($w[0], 8, utf8_decode($fila['nombre']), 1);
        $pdf->Cell($w[1], 8, utf8_decode($fila['apellidos']), 1);
        $pdf->Cell($w[2], 8, utf8_decode($fila['email']), 1);
        $pdf->Cell($w[3], 8, $fila['celular'], 1);
        $pdf->Cell($w[4], 8, utf8_decode($fila['propiedad']), 1);

        // MENSAJE MULTILÍNEA
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($w[5], 8, utf8_decode($fila['mensaje']), 1);
        $pdf->SetXY($x + $w[5], $y);

        $pdf->Cell(
            $w[6],
            8,
            date('d/m/Y', strtotime($fila['fecha_registro'])),
            1
        );

        $pdf->Ln();
    }
}

/* =========================
   DESCARGAR PDF
========================= */
$pdf->Output('D', 'lista_mensajes_' . date('Ymd_His') . '.pdf');
exit;
