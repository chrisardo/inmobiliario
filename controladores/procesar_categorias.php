<?php
//Toda esta parte es controladores/procesar_categorias.php
include 'conect_db.php';

if (!isset($_SESSION['usId'])) die("No autorizado");

$mensaje = "";
$tipoAlerta = "";

// ================= PAGINACIÓN =================
$registrosPorPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $registrosPorPagina;

// ================= FORMULARIO =================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre']);
    $usId = intval($_SESSION['usId']);

    if ($nombre === "") {
        $mensaje = "Nombre vacío";
        $tipoAlerta = "danger";
    } else {
        $nombre = $conexion->real_escape_string($nombre);

        if ($_POST['accion'] === "registrar") {
            $conexion->query("INSERT INTO categoria (nombre,id_user,Eliminado) VALUES ('$nombre',$usId,0)");
        }

        if ($_POST['accion'] === "editar") {
            $id = intval($_POST['id_categorias']);
            $conexion->query("UPDATE categoria SET nombre='$nombre' WHERE id_categoria=$id AND id_user=$usId");
        }

        header("Location: ../adm/adm_categorias.php");
        exit();
    }
}

// ================= ELIMINAR =================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conexion->query("UPDATE categoria SET Eliminado=1 WHERE id_categoria=$id AND id_user=" . intval($_SESSION['usId']));
    header("Location: ../adm/adm_categorias.php");
    exit();
}

// ================= TOTAL =================
$resTotal = $conexion->query("SELECT COUNT(*) total FROM categoria WHERE Eliminado=0 AND id_user=" . intval($_SESSION['usId']));
$totalCategorias = $resTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalCategorias / $registrosPorPagina);

// ================= LISTA =================
$sql = "SELECT * FROM categoria
        WHERE Eliminado=0 AND id_user=" . intval($_SESSION['usId']) . "
        ORDER BY id_categoria DESC
        LIMIT $inicio,$registrosPorPagina";

$resultado = $conexion->query($sql);
