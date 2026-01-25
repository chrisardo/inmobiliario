<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conect_db.php';

$mensaje = "";
$tipoAlerta = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* =========================
       VALIDAR CAMPOS OBLIGATORIOS
    ========================== */
    if (
        empty(trim($_POST['codigo'])) ||
        empty(trim($_POST['nombre'])) ||
        empty($_POST['categoria']) ||
        empty($_POST['precio_venta']) ||
        empty($_POST['ubicacion']) ||
        empty($_POST['tamano_area'])
    ) {
        $mensaje = "⚠️ Todos los campos obligatorios deben completarse.";
        $tipoAlerta = "warning";
        return;
    }

    /* =========================
       LIMPIAR Y CONVERTIR DATOS
    ========================== */
    $codigo     = trim($_POST['codigo']);
    $nombre     = trim($_POST['nombre']);
    $ubicacion    = trim($_POST['ubicacion']);
    $precio_venta = floatval(str_replace(',', '.', $_POST['precio_venta']));
    $tamano_area = floatval(str_replace(',', '.', $_POST['tamano_area']));
    $categoria = (int) $_POST['categoria'];
    $id_user   = (int) $_SESSION['usId'];
    $Eliminado = 0;

    /* =========================
       VALIDACIONES NUMÉRICAS
    ========================== */
    if ($precio_venta < 0 || !is_numeric($precio_venta)) {
        $mensaje = "❌ El precio de venta ingresado no es válido.";
        $tipoAlerta = "danger";
        return;
    }
    if ($tamano_area < 0 || !is_numeric($tamano_area)) {
        $mensaje = "❌ El tamaño del área ingresado no es válido.";
        $tipoAlerta = "danger";
        return;
    }

    /* =========================
       VALIDAR CATEGORÍA
    ========================== */
    function validarRelacion($conexion, $tabla, $campo, $id, $id_user)
    {
        $sql = "SELECT 1 FROM $tabla WHERE $campo = ? AND id_user = ? AND Eliminado = 0";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $id, $id_user);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    if (!validarRelacion($conexion, 'categoria', 'id_categoria', $categoria, $id_user)) {
        $mensaje = "❌ Categoría inválida.";
        $tipoAlerta = "danger";
        return;
    }

    /* =========================
       CONTROLAR DUPLICADOS (CÓDIGO)
    ========================== */
    $stmt = $conexion->prepare(
        "SELECT 1 FROM propiedades 
         WHERE codigo = ? AND id_user = ?"
    );
    $stmt->bind_param("si", $codigo, $id_user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensaje = "⚠️ Ya existe una propiedad con ese código.";
        $tipoAlerta = "warning";
        $stmt->close();
        return;
    }
    $stmt->close();

    /* =========================
       VALIDAR IMAGEN (OPCIONAL)
    ========================== */
    $imagenBinaria = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $mensaje = "❌ Error al subir la imagen.";
            $tipoAlerta = "danger";
            return;
        }

        if ($_FILES['imagen']['size'] > (1.8 * 1024 * 1024)) {
            $mensaje = "❌ La imagen no puede superar 1.8 MB.";
            $tipoAlerta = "danger";
            return;
        }

        $mime = mime_content_type($_FILES['imagen']['tmp_name']);
        if (!in_array($mime, ['image/png', 'image/jpeg'])) {
            $mensaje = "❌ Solo se permiten imágenes PNG o JPG.";
            $tipoAlerta = "danger";
            return;
        }

        $imagenBinaria = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    /* =========================
       INSERTAR PRODUCTO
    ========================== */
    $sql = "INSERT INTO propiedades (
                id_user, nombre, codigo, imagen, id_categoria, tamano_area_metros, precio, ubicacion, fecha_registro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?,NOW())";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "issbidds",
        $id_user,
        $nombre,
        $codigo,
        $imagenBinaria,
        $categoria,
        $tamano_area,
        $precio_venta,
        $ubicacion
    );

    if ($imagenBinaria !== null) {
        $stmt->send_long_data(3, $imagenBinaria);
    }

    if ($stmt->execute()) {
        $mensaje = $imagenBinaria
            ? "✅ Registrado correctamente con imagen."
            : "✅ Registrado correctamente sin imagen.";
        $tipoAlerta = "success";
    } else {
        $mensaje = "❌ Error al registrar producto: " . $stmt->error;
        $tipoAlerta = "danger";
    }

    $stmt->close();
}
