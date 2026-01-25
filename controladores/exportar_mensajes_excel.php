<?php
session_start();

/* =========================
   VALIDAR SESIÓN
========================= */
if (!isset($_SESSION['usId'])) {
    die("Acceso no autorizado.");
    header("Location: ../login.php");
}

$usId = (int) $_SESSION['usId'];

/* =========================
   CONEXIÓN + LIBRERÍA
========================= */
require_once __DIR__ . "/conect_db.php";
require_once __DIR__ . "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/* =========================
   CREAR EXCEL
========================= */

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Mensajes");

/* =========================
   TÍTULO
========================= */
$sheet->mergeCells("A1:G1");
$sheet->setCellValue("A1", "LISTA DE MENSAJES");
$sheet->getStyle("A1")->getFont()->setBold(true)->setSize(18);
$sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(30);

/* =========================
   ENCABEZADOS
========================= */
$encabezados = [
    "Nombre",
    "Apellidos",
    "Email",
    "Celular",
    "Propiedad",
    "Mensaje",
    "Fecha"
];

$filaHeader = 3;
$col = "A";

foreach ($encabezados as $texto) {
    $sheet->setCellValue($col . $filaHeader, $texto);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

/* =========================
   ESTILO ENCABEZADOS
========================= */
$sheet->getStyle("A{$filaHeader}:G{$filaHeader}")->applyFromArray([
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
    LEFT JOIN propiedades p 
        ON p.id_propiedad = m.id_propiedad
    ORDER BY m.fecha_registro DESC
";


$result = $conexion->query($query);

if (!$result) {
    die("Error SQL: " . $conexion->error);
}

/* =========================
   LLENAR DATOS
========================= */
$filaExcel = 4;

if ($result->num_rows === 0) {

    $sheet->mergeCells("A{$filaExcel}:G{$filaExcel}");
    $sheet->setCellValue("A{$filaExcel}", "No hay mensajes registrados.");
    $sheet->getStyle("A{$filaExcel}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
} else {

    while ($fila = $result->fetch_assoc()) {

        $fecha = "";
        if (!empty($fila['fecha_registro'])) {
            $fecha = date("d/m/Y H:i", strtotime($fila['fecha_registro']));
        }

        $sheet->setCellValue("A{$filaExcel}", $fila['nombre']);
        $sheet->setCellValue("B{$filaExcel}", $fila['apellidos']);
        $sheet->setCellValue("C{$filaExcel}", $fila['email']);
        $sheet->setCellValue("D{$filaExcel}", $fila['celular']);
        $sheet->setCellValue("E{$filaExcel}", $fila['propiedad']);
        $sheet->setCellValue("F{$filaExcel}", $fila['mensaje']);
        $sheet->setCellValue("G{$filaExcel}", $fecha);

        // Zebra (filas alternas)
        if ($filaExcel % 2 == 0) {
            $sheet->getStyle("A{$filaExcel}:G{$filaExcel}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F2F2F2']
                ]
            ]);
        }

        $filaExcel++;
    }
}

/* =========================
   BORDES
========================= */
$sheet->getStyle("A{$filaHeader}:G" . ($filaExcel - 1))->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

/* =========================
   DESCARGAR ARCHIVO
========================= */
$archivo = "lista_mensajes_" . date("Ymd_His") . ".xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$archivo\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
