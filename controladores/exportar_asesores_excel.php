<?php
session_start();

if (!isset($_SESSION['usId'])) {
    die("Acceso no autorizado.");
    header("Location: ../login.php");
}

require_once __DIR__ . "/conect_db.php";
require_once __DIR__ . "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/* =====================================================
   USUARIO
===================================================== */

$usId = (int) $_SESSION['usId'];

/* =====================================================
   CREAR EXCEL
===================================================== */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Asesores");

/* =====================================================
   TÍTULO
===================================================== */
$sheet->mergeCells("A1:F1");
$sheet->setCellValue("A1", "LISTA DE ASESORES");
$sheet->getStyle("A1")->getFont()->setBold(true)->setSize(18);
$sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(30);

/* =====================================================
   ENCABEZADOS
===================================================== */
$encabezados = [
    "Nombre",
    "Apellidos",
    "Email",
    "Celular",
    "Cargo",
    "Fecha registro"
];

$filaHeader = 3;
$col = "A";

foreach ($encabezados as $texto) {
    $sheet->setCellValue($col . $filaHeader, $texto);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

/* =====================================================
   ESTILO ENCABEZADOS
===================================================== */
$sheet->getStyle("A{$filaHeader}:F{$filaHeader}")->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['rgb' => '198754'] // verde Bootstrap
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheet->getRowDimension($filaHeader)->setRowHeight(22);

/* =====================================================
   CONSULTA ASESORES (SIN EMPRESA)
===================================================== */
$query = "
    SELECT 
        nombre,
        apellidos,
        email,
        celular,
        cargo,
        fecha_registro
    FROM asesores
    WHERE id_user = $usId
    ORDER BY id_asesor DESC
";

$result = $conexion->query($query);

if (!$result) {
    die("Error SQL: " . $conexion->error);
}

/* =====================================================
   LLENAR DATOS
===================================================== */
$filaExcel = 4;

while ($fila = $result->fetch_assoc()) {

    $fecha = "";
    if (!empty($fila['fecha_registro'])) {
        $fecha = date("d/m/Y", strtotime($fila['fecha_registro']));
    }

    $sheet->setCellValue("A{$filaExcel}", $fila['nombre']);
    $sheet->setCellValue("B{$filaExcel}", $fila['apellidos']);
    $sheet->setCellValue("C{$filaExcel}", $fila['email']);
    $sheet->setCellValue("D{$filaExcel}", $fila['celular']);
    $sheet->setCellValue("E{$filaExcel}", $fila['cargo']);
    $sheet->setCellValue("F{$filaExcel}", $fecha);

    // Zebra
    if ($filaExcel % 2 == 0) {
        $sheet->getStyle("A{$filaExcel}:F{$filaExcel}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'F2F2F2']
            ]
        ]);
    }

    $filaExcel++;
}

/* =====================================================
   BORDES
===================================================== */
$sheet->getStyle("A{$filaHeader}:F" . ($filaExcel - 1))->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

/* =====================================================
   DESCARGAR ARCHIVO
===================================================== */
$archivo = "lista_asesores_" . date("Ymd_His") . ".xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$archivo\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
