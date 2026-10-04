<?php
// =========================================================
// CoDevPro Technology
// Archivo: controladores/exportar_propiedades_excel.php
// Módulo: Propiedades
// Función: Exportar propiedades a Excel
// =========================================================

session_start();

/* =========================================================
   VALIDAR SESIÓN
========================================================= */

if (!isset($_SESSION['usId']) || (int) $_SESSION['usId'] <= 0) {
    header("Location: ../login.php");
    exit();
}

/* =========================================================
   CONEXIÓN Y PHPSPREADSHEET
========================================================= */

require_once __DIR__ . "/conect_db.php";
require_once __DIR__ . "/../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/* =========================================================
   ID DEL USUARIO
========================================================= */

$usId = (int) $_SESSION['usId'];

/* =========================================================
   VERIFICAR CONEXIÓN
========================================================= */

if (!isset($conexion) || !$conexion) {
    die("No se pudo establecer la conexión con la base de datos.");
}

/* =========================================================
   OBTENER DATOS DE LA EMPRESA
========================================================= */

$nombreEmpresa = "INMOBILIARIA";

$stmtEmpresa = $conexion->prepare("
    SELECT nombreEmpresa
    FROM usuario_acceso
    WHERE id_user = ?
    LIMIT 1
");

if ($stmtEmpresa) {

    $stmtEmpresa->bind_param("i", $usId);
    $stmtEmpresa->execute();

    $resultadoEmpresa = $stmtEmpresa->get_result();

    if ($resultadoEmpresa && $filaEmpresa = $resultadoEmpresa->fetch_assoc()) {

        if (!empty($filaEmpresa['nombreEmpresa'])) {
            $nombreEmpresa = $filaEmpresa['nombreEmpresa'];
        }
    }

    $stmtEmpresa->close();
}

/* =========================================================
   CONSULTAR PROPIEDADES
=========================================================

   IMPORTANTE:

   Eliminado = 0
   significa que la propiedad está activa.

   Las propiedades eliminadas no deben aparecer
   en la exportación.
========================================================= */

$sql = "
    SELECT
        p.id_propiedad,
        p.codigo,
        p.nombre,
        p.id_categoria,
        p.tamano_area_metros,
        p.precio,
        p.precio_anterior,
        p.ubicacion,
        p.fecha_registro,
        p.fecha_actualizacion,

        COALESCE(
            NULLIF(c.nombre, ''),
            'SIN CATEGORÍA'
        ) AS categoria

    FROM propiedades p

    LEFT JOIN categoria c
        ON c.id_categoria = p.id_categoria
        AND c.id_user = p.id_user
        AND c.Eliminado = 0

    WHERE
        p.id_user = ?
        AND p.Eliminado = 0

    ORDER BY
        p.id_propiedad DESC
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $usId);

if (!$stmt->execute()) {
    $stmt->close();
    die("Error al consultar las propiedades: " . $conexion->error);
}

$resultado = $stmt->get_result();

/* =========================================================
   CREAR ARCHIVO EXCEL
========================================================= */

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle("Propiedades");

/* =========================================================
   CONFIGURACIÓN GENERAL
========================================================= */

$sheet->setShowGridlines(false);

/* =========================================================
   TÍTULO
========================================================= */

$sheet->mergeCells("A1:H1");

$sheet->setCellValue(
    "A1",
    "LISTA DE PROPIEDADES"
);

$sheet->getStyle("A1:H1")->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 18
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheet->getRowDimension(1)->setRowHeight(30);

/* =========================================================
   INFORMACIÓN DE EMPRESA
========================================================= */

$sheet->mergeCells("A2:H2");

$sheet->setCellValue(
    "A2",
    "Empresa: " . $nombreEmpresa
);

$sheet->getStyle("A2:H2")->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 11
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheet->getRowDimension(2)->setRowHeight(22);

/* =========================================================
   ENCABEZADOS
========================================================= */

$encabezados = [

    "Código",
    "Propiedad",
    "Categoría",
    "Área (m²)",
    "Ubicación",
    "Precio",
    "Precio anterior",
    "Fecha registro"

];

$filaHeader = 4;

$columnas = [
    "A",
    "B",
    "C",
    "D",
    "E",
    "F",
    "G",
    "H"
];

foreach ($encabezados as $indice => $texto) {

    $columna = $columnas[$indice];

    $sheet->setCellValue(
        $columna . $filaHeader,
        $texto
    );
}

/* =========================================================
   ESTILO DE ENCABEZADOS
========================================================= */

$sheet->getStyle(
    "A{$filaHeader}:H{$filaHeader}"
)->applyFromArray([

    'font' => [
        'bold' => true,
        'color' => [
            'rgb' => 'FFFFFF'
        ],
        'size' => 10
    ],

    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => [
            'rgb' => '198754'
        ]
    ],

    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],

    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => [
                'rgb' => 'FFFFFF'
            ]
        ]
    ]

]);

$sheet->getRowDimension($filaHeader)->setRowHeight(25);

/* =========================================================
   LLENAR DATOS
========================================================= */

$filaExcel = 5;

while ($fila = $resultado->fetch_assoc()) {

    /* =====================================================
       DATOS
    ===================================================== */

    $codigo = $fila['codigo'] ?? '';

    $nombre = $fila['nombre'] ?? '';

    $categoria = $fila['categoria'] ?? 'SIN CATEGORÍA';

    $area = (float) ($fila['tamano_area_metros'] ?? 0);

    $ubicacion = $fila['ubicacion'] ?? '';

    $precio = (float) ($fila['precio'] ?? 0);

    $precioAnterior = (float) ($fila['precio_anterior'] ?? 0);

    /* =====================================================
       FECHA
    ===================================================== */

    $fecha = '';

    if (!empty($fila['fecha_registro'])) {

        $timestamp = strtotime(
            $fila['fecha_registro']
        );

        if ($timestamp !== false) {

            $fecha = date(
                "d/m/Y",
                $timestamp
            );
        }
    }

    /* =====================================================
       ESCRIBIR CELDAS
    ===================================================== */

    $sheet->setCellValue(
        "A{$filaExcel}",
        $codigo
    );

    $sheet->setCellValue(
        "B{$filaExcel}",
        $nombre
    );

    $sheet->setCellValue(
        "C{$filaExcel}",
        $categoria
    );

    $sheet->setCellValue(
        "D{$filaExcel}",
        $area
    );

    $sheet->setCellValue(
        "E{$filaExcel}",
        $ubicacion
    );

    $sheet->setCellValue(
        "F{$filaExcel}",
        $precio
    );

    $sheet->setCellValue(
        "G{$filaExcel}",
        $precioAnterior
    );

    $sheet->setCellValue(
        "H{$filaExcel}",
        $fecha
    );

    /* =====================================================
       FORMATO NUMÉRICO
    ===================================================== */

    $sheet->getStyle(
        "D{$filaExcel}"
    )->getNumberFormat()->setFormatCode(
        '#,##0.00'
    );

    $sheet->getStyle(
        "F{$filaExcel}:G{$filaExcel}"
    )->getNumberFormat()->setFormatCode(
        '"S/." #,##0.00'
    );

    /* =====================================================
       ALINEACIONES
    ===================================================== */

    $sheet->getStyle(
        "A{$filaExcel}"
    )->getAlignment()->setHorizontal(
        Alignment::HORIZONTAL_CENTER
    );

    $sheet->getStyle(
        "C{$filaExcel}"
    )->getAlignment()->setHorizontal(
        Alignment::HORIZONTAL_CENTER
    );

    $sheet->getStyle(
        "D{$filaExcel}"
    )->getAlignment()->setHorizontal(
        Alignment::HORIZONTAL_RIGHT
    );

    $sheet->getStyle(
        "F{$filaExcel}:G{$filaExcel}"
    )->getAlignment()->setHorizontal(
        Alignment::HORIZONTAL_RIGHT
    );

    $sheet->getStyle(
        "H{$filaExcel}"
    )->getAlignment()->setHorizontal(
        Alignment::HORIZONTAL_CENTER
    );

    /* =====================================================
       AJUSTE DE TEXTO
    ===================================================== */

    $sheet->getStyle(
        "A{$filaExcel}:H{$filaExcel}"
    )->getAlignment()->setVertical(
        Alignment::VERTICAL_CENTER
    );

    $sheet->getStyle(
        "B{$filaExcel}:E{$filaExcel}"
    )->getAlignment()->setWrapText(true);

    /* =====================================================
       FILAS ALTERNAS
    ===================================================== */

    if ($filaExcel % 2 === 1) {

        $sheet->getStyle(
            "A{$filaExcel}:H{$filaExcel}"
        )->applyFromArray([

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'F2F2F2'
                ]
            ]

        ]);
    }

    $sheet->getRowDimension(
        $filaExcel
    )->setRowHeight(22);

    $filaExcel++;
}

/* =========================================================
   SI NO EXISTEN PROPIEDADES
========================================================= */

if ($filaExcel === 5) {

    $sheet->mergeCells("A5:H5");

    $sheet->setCellValue(
        "A5",
        "No hay propiedades registradas."
    );

    $sheet->getStyle("A5:H5")->applyFromArray([

        'font' => [
            'bold' => true
        ],

        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]

    ]);

    $sheet->getRowDimension(5)->setRowHeight(25);

    $ultimaFila = 5;

} else {

    $ultimaFila = $filaExcel - 1;
}

/* =========================================================
   BORDES DE LA TABLA
========================================================= */

$sheet->getStyle(
    "A{$filaHeader}:H{$ultimaFila}"
)->applyFromArray([

    'borders' => [

        'allBorders' => [

            'borderStyle' => Border::BORDER_THIN,

            'color' => [
                'rgb' => 'D0D0D0'
            ]

        ]

    ]

]);

/* =========================================================
   ANCHOS DE COLUMNAS
========================================================= */

$anchos = [

    "A" => 18,
    "B" => 32,
    "C" => 24,
    "D" => 15,
    "E" => 40,
    "F" => 18,
    "G" => 18,
    "H" => 18

];

foreach ($anchos as $columna => $ancho) {

    $sheet->getColumnDimension(
        $columna
    )->setWidth($ancho);
}

/* =========================================================
   CONGELAR ENCABEZADOS
========================================================= */

$sheet->freezePane("A5");

/* =========================================================
   FILTRO AUTOMÁTICO
========================================================= */

if ($ultimaFila >= $filaHeader) {

    $sheet->setAutoFilter(
        "A{$filaHeader}:H{$ultimaFila}"
    );
}

/* =========================================================
   PIE DE DOCUMENTO
========================================================= */

$filaPie = $ultimaFila + 2;

$sheet->mergeCells(
    "A{$filaPie}:H{$filaPie}"
);

$sheet->setCellValue(
    "A{$filaPie}",
    "Generado el " . date("d/m/Y H:i:s")
);

$sheet->getStyle(
    "A{$filaPie}:H{$filaPie}"
)->applyFromArray([

    'font' => [
        'italic' => true,
        'size' => 9
    ],

    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT
    ]

]);

/* =========================================================
   LIMPIAR BUFFER DE SALIDA
========================================================= */

if (ob_get_length()) {
    ob_end_clean();
}

/* =========================================================
   NOMBRE DEL ARCHIVO
========================================================= */

$archivo =
    "lista_propiedades_" .
    date("Ymd_His") .
    ".xlsx";

/* =========================================================
   CABECERAS HTTP
========================================================= */

header(
    "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
);

header(
    'Content-Disposition: attachment; filename="' .
    $archivo .
    '"'
);

header(
    "Cache-Control: max-age=0"
);

header(
    "Cache-Control: max-age=1"
);

header(
    "Expires: Mon, 26 Jul 1997 05:00:00 GMT"
);

header(
    "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"
);

header(
    "Pragma: public"
);

/* =========================================================
   GENERAR EXCEL
========================================================= */

$writer = new Xlsx($spreadsheet);

$writer->save("php://output");

/* =========================================================
   CERRAR
========================================================= */

$stmt->close();

$conexion->close();

exit;
