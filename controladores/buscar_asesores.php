<?php
require_once "../controladores/conect_db.php";

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$sql = "
SELECT  nombre, apellidos, imagen, email, celular, cargo, fecha_registro 
FROM asesores
WHERE 
  nombre LIKE ? OR
  apellidos LIKE ? OR
  email LIKE ? OR
  cargo LIKE ? OR
  fecha_registro LIKE ?
ORDER BY id_asesor ASC
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



?>

    <div class="row g-3">
        <?php while ($fila = $resultado->fetch_assoc()):
            //$telefonoWhatsapp = "51" . preg_replace('/\D/', '', $fila['celular']); 
        ?>
            <?php
            $telefonoAsesor = "51" . preg_replace('/\D/', '', $fila['celular']);

            $mensajeWhatsapp = urlencode(
                "Hola {$fila['nombre']} {$fila['apellidos']},\n\n" .
                    "Me comunico para solicitar información sobre una propiedad.\n\n" .
                    "Quedo atento/a a su respuesta.\nGracias."
            );
            ?>
            <div class="col-md-3 mb-2 py-3">
                <div class="card asesor-card text-center">

                    <!-- FOTO -->
                    <div class="asesor-foto border-success">
                        <?php if (!empty($fila['imagen'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($fila['imagen']) ?>">
                        <?php else: ?>
                            <img src="img/usuario_default.png">
                        <?php endif; ?>
                    </div>

                    <!-- NOMBRE -->
                    <h6 class="mt-3 mb-1 fw-semibold">
                        <?= $fila['nombre'] . " " . $fila['apellidos'] ?>
                    </h6>
                    <p class="text-center fw-bold text-uppercase text-success mb-3">
                        <?= $fila['cargo'] ?>
                    </p>
                    <!-- DATOS -->
                    <div class="asesor-info text-start px-3 pb-3">
                        <p><i class="bi bi-envelope"></i> <?= $fila['email'] ?></p>
                        <p><i class="bi bi-phone"></i> <?= $fila['celular'] ?></p>
                    </div>
                    <!-- BOTONES -->
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <?php if (!empty($fila['celular'])): ?>
                            <a href="https://wa.me/51<?= preg_replace('/\D/', '', $fila['celular']) ?>?text=<?= $mensajeWhatsapp ?>"
                                target="_blank"
                                class="btn btn-success btn-icon">
                                <i class="bi bi-whatsapp"></i>
                            </a>

                            <a href="tel:<?= $fila['celular'] ?>" class="btn btn-danger btn-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
                        <?php endif; ?>

                        <a href="mailto:<?= $fila['email'] ?>" class="btn btn-primary btn-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </a>
                    </div>
                    <!-- LINKS -->
                    <!--<div class="asesor-links">
                        <a href="#">VER PROPIEDADES</a>
                        <a href="#">VER OFICINA</a>
                    </div>-->

                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning text-center">
        No se encontraron asesores
    </div>
<?php endif; ?>