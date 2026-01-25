<?php
session_start();

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/conect_db.php";
require_once __DIR__ . "../../fpdf/fpdf.php";

$usId = (int) $_SESSION['usId'];

/* =====================================
   OBTENER NOMBRE DE LA EMPRESA
===================================== */
$empresa = "Mi Inmobiliaria";

$stmtEmpresa = $conexion->prepare(
    "SELECT nombreEmpresa FROM usuario_acceso WHERE id_user = ?"
);
$stmtEmpresa->bind_param("i", $usId);
$stmtEmpresa->execute();
$resEmpresa = $stmtEmpresa->get_result();

if ($rowEmpresa = $resEmpresa->fetch_assoc()) {
    $empresa = $rowEmpresa['nombreEmpresa'];
}

/* =====================================
   CONSULTAR ASESORES
===================================== */
$stmt = $conexion->prepare("
    SELECT nombre, apellidos, email, celular, cargo, fecha_registro
    FROM asesores
    WHERE id_user = ?
    ORDER BY id_asesor DESC
");

$stmt->bind_param("i", $usId);
$stmt->execute();
$resultado = $stmt->get_result();

/* =====================================
   CREAR PDF
===================================== */
$pdf = new FPDF('L', 'mm', 'A4'); // Horizontal
$pdf->AddPage();

/* ---------- TÍTULO ---------- */
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Lista de Asesores - $empresa"), 0, 1, 'C');
$pdf->Ln(5);

/* ---------- FECHA ---------- */
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, "Fecha de exportacion: " . date('d/m/Y'), 0, 1, 'R');
$pdf->Ln(3);

/* ---------- ENCABEZADOS ---------- */
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(40, 167, 69); // Verde
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(50, 8, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Apellidos', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Email', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Celular', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Cargo', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Fecha', 1, 1, 'C', true);

/* ---------- CONTENIDO ---------- */
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);

while ($fila = $resultado->fetch_assoc()) {

    $pdf->Cell(50, 7, utf8_decode($fila['nombre']), 1);
    $pdf->Cell(50, 7, utf8_decode($fila['apellidos']), 1);
    $pdf->Cell(60, 7, utf8_decode($fila['email']), 1);
    $pdf->Cell(35, 7, $fila['celular'], 1);
    $pdf->Cell(45, 7, utf8_decode($fila['cargo']), 1);
    $pdf->Cell(35, 7, date('d/m/Y', strtotime($fila['fecha_registro'])), 1, 1);
}

/* ---------- SALIDA ---------- */
$pdf->Output(
    "I",
    "asesores_" . date('dmY_His') . ".pdf"
);
exit;
