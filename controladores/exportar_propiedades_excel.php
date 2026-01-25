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
   DATOS USUARIO
===================================================== */

$usId = (int)$_SESSION['usId'];

/* =====================================================
   CREAR EXCEL
===================================================== */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Propiedades");

/* =====================================================
   TÍTULO
===================================================== */
$sheet->mergeCells("A1:H1");
$sheet->setCellValue("A1", "LISTA DE PROPIEDADES");
$sheet->getStyle("A1")->getFont()->setBold(true)->setSize(18);
$sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(30);

/* =====================================================
   ENCABEZADOS
===================================================== */
$encabezados = [
    "Código",
    "Propiedad",
    "Categoría",
    "Área (m²)",
    "Ubicación",
    "Precio",
    "Fecha registro",
    "Empresa"
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
$sheet->getStyle("A{$filaHeader}:H{$filaHeader}")->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['rgb' => '198754'] // verde bootstrap
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheet->getRowDimension($filaHeader)->setRowHeight(22);

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
        IFNULL(c.nombre,'SIN CATEGORÍA') AS categoria,
        u.nombreEmpresa
    FROM propiedades p
    LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
    LEFT JOIN usuario_acceso u ON u.id_user = p.id_user
    WHERE p.id_user = $usId
    ORDER BY p.id_propiedad DESC
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

    $sheet->setCellValue("A{$filaExcel}", $fila['codigo']);
    $sheet->setCellValue("B{$filaExcel}", $fila['nombre']);
    $sheet->setCellValue("C{$filaExcel}", $fila['categoria']);
    $sheet->setCellValue("D{$filaExcel}", number_format($fila['tamano_area_metros'], 2));
    $sheet->setCellValue("E{$filaExcel}", $fila['ubicacion']);
    $sheet->setCellValue("F{$filaExcel}", $fila['precio']);
    $sheet->setCellValue("G{$filaExcel}", $fecha);
    $sheet->setCellValue("H{$filaExcel}", $fila['nombreEmpresa']);

    // Zebra (filas alternas)
    if ($filaExcel % 2 == 0) {
        $sheet->getStyle("A{$filaExcel}:H{$filaExcel}")->applyFromArray([
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
$sheet->getStyle("A{$filaHeader}:H" . ($filaExcel - 1))->applyFromArray([
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
$archivo = "lista_propiedades_" . date("Ymd_His") . ".xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$archivo\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
