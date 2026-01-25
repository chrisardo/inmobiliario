<?php
require_once "../controladores/conect_db.php";

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$sql = "
SELECT p.*, c.nombre AS noombre_categoria
FROM propiedades p
INNER JOIN categoria c ON p.id_categoria = c.id_categoria
WHERE 
  p.codigo LIKE ? OR
  p.nombre LIKE ? OR
  p.ubicacion LIKE ? OR
  p.precio LIKE ? OR
  p.fecha_registro LIKE ?
ORDER BY p.id_propiedad ASC
";

$stmt = $conexion->prepare($sql);
$param = "%$buscar%";
$stmt->bind_param("sssss", $param, $param, $param, $param, $param);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows > 0):
    $sqlEmpresa = "SELECT celular FROM usuario_acceso LIMIT 1";
    $resEmpresa = $conexion->query($sqlEmpresa);
    $empresa = $resEmpresa->fetch_assoc();

    $telefonoWhatsapp = "51" . preg_replace('/\D/', '', $empresa['celular']);

?>

    <div class="row g-2">
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <div class="col-md-3 mb-3 ">
                <div class="card card-hover h-100 shadow-sm border-success">
                    <div class="position-relative">

                        <?php if (!empty($fila['imagen'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($fila['imagen']) ?>" class="card-img-top">
                        <?php else: ?>
                            <img src="img/producto.png" class="card-img-top">
                        <?php endif; ?>

                        <span class="badge-precio">Desde S/. <?= number_format($fila['precio'], 0) ?></span>
                        <span class="badge-estado">ID: <?= $fila['codigo'] ?></span>

                    </div>

                    <div class="card-body">
                        <?php
                        $mensajeWhatsapp = urlencode(
                            "Estimado/a,\n\n" .
                                "Me comunico para solicitar información sobre la siguiente propiedad:\n\n" .
                                "Código: {$fila['codigo']}\n" .
                                "Nombre: {$fila['nombre']}\n" .
                                "Ubicación: {$fila['ubicacion']}\n" .
                                "Precio: S/. " . number_format($fila['precio'], 0) . "\n\n" .
                                "Quedo atento/a a su respuesta.\nMuchas gracias."
                        );

                        // NÚMERO DE WHATSAPP (del sistema o empresa)
                        //$telefonoWhatsapp = "51" . preg_replace('/\D/', '', $usuario['celular'] ?? '999999999');
                        //$telefonoWhatsapp = "51". $usuario['celular'];
                        ?>

                        <h5 class="card-title text-success"><?= $fila['nombre'] ?></h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?= $fila['ubicacion'] ?>
                        </p>
                        <p class="card-text">
                            <i class="bi bi-arrows-fullscreen"></i>
                            <?= $fila['tamano_area_metros'] ?> m2 |
                            <i class="bi bi-grid-fill"></i>
                            <?= $fila['noombre_categoria'] ?>
                        </p>
                        <!-- BOTÓN WHATSAPP -->
                        <a
                            href="https://wa.me/<?= $telefonoWhatsapp ?>?text=<?= $mensajeWhatsapp ?>"
                            target="_blank"
                            class="btn btn-success w-100 mt-2">
                            <i class="bi bi-whatsapp"></i> Cotizar por WhatsApp
                        </a>

                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning text-center">
        No se encontraron propiedades
    </div>
<?php endif; ?>