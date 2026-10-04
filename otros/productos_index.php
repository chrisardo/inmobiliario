 <!--Productos section: otros/productos_index.php-->
 <div id="resultadoPropiedades" class="container position-relative">
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
                $resultado = $conexion->query("SELECT p.*, c.nombre as noombre_categoria FROM propiedades p inner join categoria c on p.id_categoria = c.id_categoria order by id_propiedad ASC");
                ?>
             <?php if (mysqli_num_rows($resultado) > 0): ?>

                 <!-- AQUÍ VA TU BUCLE DE PROPIEDADES -->
                 <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>

                     <div class="col-md-3 mb-3 ">
                         <div class="card card-hover h-100 shadow-sm  border-success">
                             <div class="position-relative">
                                 <?php if (!empty($fila['imagen'])): ?>
                                     <?php $imagenBinaria = base64_encode($fila['imagen']); ?>
                                     <img
                                         src="data:image/jpeg;base64,<?= $imagenBinaria ?>"
                                         class="card-img-top" height="260"
                                         alt="Propiedad" />
                                 <?php else: ?>
                                     <img
                                         src="img/producto.png"
                                         class="card-img-top" height="260"
                                         alt="Propiedad sin imagen" />
                                 <?php endif; ?>

                                 <!-- PRECIO -->
                                 <span class="badge-precio">
                                     Desde S/. <?= number_format($fila['precio'], 0) ?>
                                 </span>
                                 <span class="badge-estado">
                                     ID: <?= $fila['codigo'] ?>
                                 </span>

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
                                     <?= $fila['tamano_area_metros'] ?> m2 | <i class="bi bi-grid-fill"></i>
                                     <?= $fila['noombre_categoria'] ?>
                                 </p>
                                 <!-- BOTÓN WHATSAPP -->
                                 <a
                                     href="https://wa.me/51<?= $usuario['celular']?>?text=<?= $mensajeWhatsapp ?>"
                                     target="_blank"
                                     class="btn btn-success w-100 mt-2">
                                     <i class="bi bi-whatsapp"></i> Cotizar
                                 </a>
                             </div>

                         </div>
                     </div>

                 <?php endwhile; ?>

             <?php else: ?>

                 <!-- MENSAJE CUANDO NO HAY PROPIEDADES -->
                 <div class="col-12">
                     <div class="alert alert-warning text-center py-4">
                         <i class="fas fa-home fa-2x mb-2 text-success"></i>
                         <h5 class="mt-2">No hay propiedades registradas</h5>
                         <p class="mb-0">
                             Actualmente no contamos con propiedades disponibles.
                             Por favor, vuelve a visitarnos pronto.
                         </p>
                     </div>
                 </div>

             <?php endif; ?>
         </div>
         <!--Poner boton en el centro de "Ver detalle de cada servicio"-->
         <!--<div class="text-center mt-4">
             <a href="propiedades" class="btn btn-success btn-lg px-4">Ver más</a>
         </div>-->
     </div>
 </div>