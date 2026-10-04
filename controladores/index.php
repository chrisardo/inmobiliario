<?php
//Toda esta parte es de controladores/index.php
require_once "controladores/conect_db.php";
/* ==========================================================
FUNCIONES AUXILIARES
========================================================== */

function e($valor)
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function telefonoWhatsapp($telefono)
{
    $telefono = preg_replace('/\D+/', '', (string)$telefono);

    if ($telefono === '') {
        return '';
    }

    // Si ya contiene 51, no lo duplicamos
    if (strpos($telefono, '51') === 0 && strlen($telefono) >= 11) {
        return $telefono;
    }

    return '51' . $telefono;
}

/* ==========================================================
DATOS DE LA EMPRESA
========================================================== */

$usuario = [
    'nombreEmpresa' => 'Nuestra Empresa',
    'ruc' => '',
    'fecha_registro' => '',
    'imagen' => null,
    'direccion' => '',
    'email' => '',
    'celular' => '',
    'estado' => '',
    'descripcion_acerca' => '',
    'video' => ''
];

$sqlUsuario = "
SELECT
nombreEmpresa,
ruc,
fecha_registro,
imagen,
direccion,
email,
celular,
estado,
descripcion_acerca,
video
FROM usuario_acceso
LIMIT 1
";

$stmtUsuario = $conexion->prepare($sqlUsuario);

if ($stmtUsuario) {
    $stmtUsuario->execute();
    $resultadoUsuario = $stmtUsuario->get_result();

    if ($filaUsuario = $resultadoUsuario->fetch_assoc()) {
        $usuario = array_merge($usuario, $filaUsuario);
    }

    $stmtUsuario->close();
}

/* ==========================================================
FOTO DE PERFIL / LOGO
========================================================== */

$fotoPerfil = '';

if (!empty($usuario['imagen'])) {
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($usuario['imagen']);
}

/* ==========================================================
ASESores
========================================================== */

$asesores = [];

$sqlAsesores = "
SELECT
id_asesor,
nombre,
apellidos,
celular,
estado
FROM asesores
Where estado = 'ACTIVO'
ORDER BY nombre ASC, apellidos ASC
";

$resultAsesores = $conexion->query($sqlAsesores);

if ($resultAsesores && $resultAsesores->num_rows > 0) {

    while ($row = $resultAsesores->fetch_assoc()) {
        $asesores[] = $row;
    }

    $resultAsesores->free();
}

/* ==========================================================
CONTADOR DE PROPIEDADES
========================================================== */

$totalPropiedades = 0;

$sqlTotalPropiedades = "
SELECT COUNT(*) AS total
FROM propiedades WHERE Eliminado IS NULL
       OR Eliminado = 0
    ORDER BY nombre ASC
";

$resultTotal = $conexion->query($sqlTotalPropiedades);

if ($resultTotal) {

    $filaTotal = $resultTotal->fetch_assoc();

    $totalPropiedades = (int)($filaTotal['total'] ?? 0);

    $resultTotal->free();
}

/* ==========================================================
PROPIEDADES DESTACADAS
========================================================== */

/*
* IMPORTANTE:
* La tabla propiedades NO tiene una columna imagen.
* Las imágenes están en la tabla imagenes.
*
* Por eso obtenemos la primera imagen de cada propiedad
* mediante una subconsulta.
*/

$propiedades = [];

$sqlPropiedades = "
SELECT
p.id_propiedad,
p.id_user,
p.nombre,
p.codigo,
p.id_categoria,
p.tamano_area_metros,
p.precio,
p.precio_anterior,
p.ubicacion,
p.fecha_registro,
p.fecha_actualizacion,

c.nombre AS nombre_categoria,

(
SELECT i.imagenes
FROM imagenes i
WHERE i.id_propiedad = p.id_propiedad
ORDER BY
CASE
WHEN i.orden IS NULL THEN 999
ELSE i.orden
END ASC,
i.id_imagen ASC
LIMIT 1
) AS imagen_principal

FROM propiedades p

LEFT JOIN categoria c
ON c.id_categoria = p.id_categoria
WHERE p.Eliminado IS NULL
       OR p.Eliminado = 0
ORDER BY p.id_propiedad DESC

LIMIT 8
";

$resultPropiedades = $conexion->query($sqlPropiedades);

if ($resultPropiedades && $resultPropiedades->num_rows > 0) {

    while ($fila = $resultPropiedades->fetch_assoc()) {
        $propiedades[] = $fila;
    }

    $resultPropiedades->free();
}

/* ==========================================================
LISTA DE PROPIEDADES PARA EL FORMULARIO
========================================================== */

$listaPropiedades = [];

$sqlListaPropiedades = "
SELECT
id_propiedad,
nombre,
codigo
FROM propiedades
WHERE Eliminado IS NULL
       OR Eliminado = 0
ORDER BY nombre ASC
";

$resultLista = $conexion->query($sqlListaPropiedades);

if ($resultLista && $resultLista->num_rows > 0) {

    while ($fila = $resultLista->fetch_assoc()) {
        $listaPropiedades[] = $fila;
    }

    $resultLista->free();
}

/* ==========================================================
WHATSAPP DE LA EMPRESA
========================================================== */

$whatsappEmpresa = telefonoWhatsapp($usuario['celular']);

/* ==========================================================
TÍTULO
========================================================== */

$nombreEmpresa = trim($usuario['nombreEmpresa']);

if ($nombreEmpresa === '') {
    $nombreEmpresa = 'Inmobiliaria';
}
/* ==========================================================
   DATOS PARA CONTACTO
   ========================================================== */

$telefonoEmpresa = telefonoWhatsapp($usuario['celular']);

$nombreEmpresa = !empty($usuario['nombreEmpresa'])
    ? $usuario['nombreEmpresa']
    : 'Nuestra Empresa';

$descripcionEmpresa = !empty($usuario['descripcion_acerca'])
    ? $usuario['descripcion_acerca']
    : 'Somos una empresa comprometida con brindar soluciones de calidad a nuestros clientes.';
