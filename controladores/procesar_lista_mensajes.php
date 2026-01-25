<?php
//Toda esta parte es controladores/procesar_lista_productos.php
// Conexión
//include 'conexion.php';
$usId = $_SESSION['usId'];

// ==== ELIMINAR ====
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    //$conexion->query("UPDATE propiedades SET Eliminado = 1 WHERE idProducto = $id  AND id_user = $usId");
    $conexion->query("DELETE FROM mensajes WHERE id_contacto= $id");
    //ir a la pagina de lista_productos.php
    @header("Location: ../adm/adm_mensajes.php");
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
            SELECT m.*, p.nombre AS nombre_propiedad
            FROM mensajes m
            INNER JOIN propiedades p ON p.id_propiedad = m.id_propiedad
            WHERE
                m.nombre LIKE '%$busquedaEsc%'
                OR m.apellidos LIKE '%$busquedaEsc%'
                OR m.mensaje LIKE '%$busquedaEsc%'
                OR p.fecha_registro LIKE '%$busquedaEsc%'
            
            ORDER BY m.id_contacto DESC
            LIMIT $inicio, $porPagina

    ";

    $resultado = $conexion->query($sql);

    $sqlTotal = "
        SELECT COUNT(*) AS total
        FROM mensajes
        WHERE 
            nombre LIKE '%$busquedaEsc%'
            OR apellidos LIKE '%$busquedaEsc%'
            OR fecha_registro LIKE '%$busquedaEsc%'
    ";

    $resultado2 = $conexion->query($sqlTotal);
} else {

    $resultado = $conexion->query("
        SELECT m.*,  p.nombre AS nombre_propiedad
        FROM mensajes m
        INNER JOIN propiedades p ON p.id_propiedad = m.id_propiedad
        ORDER BY m.id_contacto DESC
        LIMIT $inicio, $porPagina

    ");

    $resultado2 = $conexion->query("
        SELECT COUNT(*) AS total 
        FROM mensajes
    ");
}

// ==== TOTALES ====
$fila = $resultado2->fetch_assoc();
$totalMensaje = $fila['total'];
$totalPaginas = ceil($totalMensaje / $porPagina);
