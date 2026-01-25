<?php
//Toda esta parte es controladores/procesar_lista_propiedades.php
// Conexión
//include 'conexion.php';
$usId = intval($_SESSION['usId']);

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    $sql = "DELETE FROM propiedades 
            WHERE id_propiedad = ? AND id_user = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $id, $usId);
    $stmt->execute();

    header("Location: ../adm/adm_lista_propiedades.php");
    exit();
}



// ==== PAGINACIÓN ====
$porPagina = 5; // cantidad de productos por página
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $porPagina;
// ==== CONSULTAS PARA MOSTRAR EN LA TABLA ====

// ==== BUSQUEDA ====
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '') {

    $busquedaEsc = $conexion->real_escape_string($busqueda);

    $sql = "
            SELECT p.*, c.nombre AS nombre_categoria
            FROM propiedades p
            INNER JOIN categoria c
                ON p.id_categoria = c.id_categoria 
            WHERE p.id_user = $usId 
            AND (
                p.nombre LIKE '%$busquedaEsc%'
                OR p.codigo LIKE '%$busquedaEsc%'
                OR p.ubicacion LIKE '%$busquedaEsc%'
                OR p.fecha_registro LIKE '%$busquedaEsc%'
            )
            ORDER BY p.id_propiedad DESC
            LIMIT $inicio, $porPagina

    ";

    $resultado = $conexion->query($sql);

    $sqlTotal = "
        SELECT COUNT(*) AS total
        FROM propiedades
        WHERE id_user = $usId 
        AND (
            nombre LIKE '%$busquedaEsc%'
            OR codigo LIKE '%$busquedaEsc%'
            OR ubicacion LIKE '%$busquedaEsc%'
            OR fecha_registro LIKE '%$busquedaEsc%'
        )
    ";

    $resultado2 = $conexion->query($sqlTotal);
} else {

    $resultado = $conexion->query("
        SELECT p.*, 
        c.nombre AS nombre_categoria
        FROM propiedades p
        INNER JOIN categoria c
            ON p.id_categoria = c.id_categoria
        WHERE p.id_user = $usId
        ORDER BY p.id_propiedad DESC
        LIMIT $inicio, $porPagina

    ");

    $resultado2 = $conexion->query("
        SELECT COUNT(*) AS total 
        FROM propiedades
        WHERE id_user = $usId
    ");
}

// ==== TOTALES ====
$fila = $resultado2->fetch_assoc();
$totalPropiedades = $fila['total'];
$totalPaginas = ceil($totalPropiedades / $porPagina);
