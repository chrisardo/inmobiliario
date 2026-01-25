 <!--Productos section-->
 <div id="resultadoAsesores" class="container position-relative">
     <div class="container my-0 mb-3 py-3 position-relative">
         <!--<div class="row mb-5">
             <div class="col text-center">
                 <h2 class="fw-bold text-dark">Propiedades Destacados</h2>
             </div>
         </div>-->
         <div class="row g-3">
             <?php
                //Lamar a la conexion
                include 'controladores/conect_db.php';
                // Consulta para obtener los 4 productos más recientes
                $resultado = $conexion->query("SELECT nombre, apellidos, imagen, email, celular, cargo, fecha_registro FROM asesores order by id_asesor ASC");
                ?>
             <?php if (mysqli_num_rows($resultado) > 0): ?>

                 <!-- AQUÍ VA TU BUCLE DE PROPIEDADES -->
                 <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>

                     <div class="col-md-3 mb-4">
                         <div class="card asesor-card text-center">

                             <!-- FOTO -->
                             <div class="asesor-foto">
                                 <?php if (!empty($fila['imagen'])): ?>
                                     <img src="data:image/jpeg;base64,<?= base64_encode($fila['imagen']) ?>">
                                 <?php else: ?>
                                     <img src="img/user-default.png">
                                 <?php endif; ?>
                             </div>

                             <!-- NOMBRE -->
                             <h6 class="mt-3 mb-1 fw-semibold">
                                 <?= $fila['nombre'] . " " . $fila['apellidos'] ?>
                             </h6>

                             <!-- EMPRESA -->
                             <p class="empresa mb-3">
                                 <?= $usuario['nombreEmpresa'] ?>
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

             <?php else: ?>

                 <!-- MENSAJE CUANDO NO HAY PROPIEDADES -->
                 <div class="col-12">
                     <div class="alert alert-warning text-center py-4">
                         <i class="fas fa-home fa-2x mb-2 text-success"></i>
                         <h5 class="mt-2">No hay asesores registradas</h5>
                         <p class="mb-0">
                             Actualmente no contamos con asesores disponibles.
                             Por favor, vuelve a visitarnos pronto.
                         </p>
                     </div>
                 </div>

             <?php endif; ?>
         </div>
     </div>
 </div>