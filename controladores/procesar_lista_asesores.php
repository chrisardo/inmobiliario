<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usId'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/conect_db.php";

$usId = (int) $_SESSION['usId'];

/* =====================================
   ELIMINAR ASESOR
===================================== */
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $idEliminar = (int) $_GET['eliminar'];

    $stmt = $conexion->prepare(
        "DELETE FROM asesores 
         WHERE id_asesor = ? AND id_user = ?"
    );
    $stmt->bind_param("ii", $idEliminar, $usId);
    $stmt->execute();
    $stmt->close();

    header("Location: ../adm/adm_lista_asesores.php");
    exit;
}

/* =====================================
   PAGINACIÓN
===================================== */
$porPagina = 5;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina'])
    ? (int) $_GET['pagina']
    : 1;

if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $porPagina;

/* =====================================
   BÚSQUEDA
===================================== */
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

/* =====================================
   CONSULTA PRINCIPAL
===================================== */
if ($busqueda !== "") {

    $busquedaEsc = "%" . $conexion->real_escape_string($busqueda) . "%";

    // ---- LISTA ----
    $stmt = $conexion->prepare("
        SELECT a.*
        FROM asesores a
        WHERE a.id_user = ?
          AND (
              a.nombre LIKE ?
              OR a.apellidos LIKE ?
              OR a.celular LIKE ?
              OR a.cargo LIKE ?
              OR a.fecha_registro LIKE ?
          )
        ORDER BY a.id_asesor DESC
        LIMIT ?, ?
    ");

    $stmt->bind_param(
        "isssssii",
        $usId,
        $busquedaEsc,
        $busquedaEsc,
        $busquedaEsc,
        $busquedaEsc,
        $busquedaEsc,
        $inicio,
        $porPagina
    );

    $stmt->execute();
    $resultado = $stmt->get_result();

    // ---- TOTAL ----
    $stmtTotal = $conexion->prepare("
        SELECT COUNT(*) AS total
        FROM asesores
        WHERE id_user = ?
          AND (
              nombre LIKE ?
              OR apellidos LIKE ?
              OR celular LIKE ?
              OR cargo LIKE ?
              OR fecha_registro LIKE ?
          )
    ");

    $stmtTotal->bind_param(
        "isssss",
        $usId,
        $busquedaEsc,
        $busquedaEsc,
        $busquedaEsc,
        $busquedaEsc,
        $busquedaEsc
    );

    $stmtTotal->execute();
    $resultado2 = $stmtTotal->get_result();
} else {

    // ---- LISTA ----
    $stmt = $conexion->prepare("
        SELECT a.*
        FROM asesores a
        WHERE a.id_user = ?
        ORDER BY a.id_asesor DESC
        LIMIT ?, ?
    ");

    $stmt->bind_param("iii", $usId, $inicio, $porPagina);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // ---- TOTAL ----
    $stmtTotal = $conexion->prepare("
        SELECT COUNT(*) AS total
        FROM asesores
        WHERE id_user = ?
    ");

    $stmtTotal->bind_param("i", $usId);
    $stmtTotal->execute();
    $resultado2 = $stmtTotal->get_result();
}

/* =====================================
   TOTALES
===================================== */
$totalAsesores = 0;
$totalPaginas = 1;

if ($resultado2 && $filaTotal = $resultado2->fetch_assoc()) {
    $totalAsesores = (int) $filaTotal['total'];
    $totalPaginas = max(1, ceil($totalAsesores / $porPagina));
}
