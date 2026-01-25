 <div
     class="container py-2"
     style="
        background-image: url('img/fondo1.png');
        background-size: cover;
        background-position: center;
      ">
     <div class="row border p-4" id="contacto">
         <!-- Columna de información de contacto -->
         <div class="col-md-6">
             <h1 class="fw-bold mb-5">Contáctanos</h1>
             <div class="mb-4">
                 <!--Poner icono de telefono-->

                 <h2 class="h5 fw-bold">
                     <i class="fas fa-phone fa-lg text-success me-2"></i>
                     Llámanos
                 </h2>
                 <p>+51 <?php echo $usuario['celular']; ?></p>
             </div>
             <div class="mb-4">
                 <!--Poner icono de correo-->
                 <h2 class="h5 fw-bold">
                     <i class="fas fa-envelope fa-lg text-success me-2"></i>
                     Escríbenos
                 </h2>
                 <p><?php echo $usuario['email']; ?></p>
             </div>
             <div class="mb-4">
                 <!--poner icono de whatsapp-->
                 <h2 class="h5 fw-bold">
                     <i class="fab fa-whatsapp fa-lg text-success me-2"></i>
                     WhatsApp
                 </h2>
                 <p>+51 <?php echo $usuario['celular']; ?></p>
             </div>
         </div>

         <!-- Columna del formulario -->
         <div class="col-md-6">
             <h3 class="fw-bold mb-5">
                 ESTAMOS LISTOS PARA ATENDERTE
                 <div class="container bg-danger"></div>
             </h3>
             <?php include 'controladores/procesar_contacto.php'; ?>
             <form method="POST" action="" class="mt-4">
                 <div class="col">
                     <label for="propiedades" class="form-label">Propiedades</label>
                     <div class="input-group">
                         <span class="input-group-text bg-success text-white">
                             <!--Poner icono de marca-->
                             <i class="fas fa-industry me-2"></i>
                         </span>
                         <!--poner un select con opciones de rubro y mostrar los rubros de la base de datos-->
                         <?php
                            $sqlPropiedades = "SELECT id_propiedad, nombre, id_user FROM propiedades";
                            $resultado = $conexion->query($sqlPropiedades);
                            ?>
                         <select class="form-select" id="propiedades" name="propiedades" required>
                             <option value="" disabled selected>Selecciona</option>
                             <?php
                                if ($resultado->num_rows > 0) {
                                    while ($fila = $resultado->fetch_assoc()) {
                                        echo '<option value="' . $fila['id_propiedad'] . '">' . $fila['nombre'] . '</option>';
                                    }
                                }
                                ?>
                         </select>
                     </div>
                 </div>
                 <div class="row g-2 mb-3">
                     <div class="col">
                         <label for="nombre" class="form-label">Nombres:</label>
                         <div class="input-group">
                             <span class="input-group-text bg-success text-white"><i class="bi bi-person"></i></span>
                             <input
                                 type="text"
                                 class="form-control"
                                 id="nombre"
                                 name="nombre"
                                 required />
                         </div>
                     </div>
                     <div class="col">
                         <label for="apellidos" class="form-label">Apellidos</label>
                         <div class="input-group">
                             <span class="input-group-text bg-success text-white"><i class="bi bi-person"></i></span>
                             <input
                                 type="text"
                                 class="form-control"
                                 id="apellidos"
                                 name="apellidos"
                                 required />
                         </div>
                     </div>
                 </div>
                 <div class="mb-3">
                     <label for="correo" class="form-label">Tu Email:</label>
                     <div class="input-group">
                         <span class="input-group-text bg-success text-white"><i class="fas fa-envelope me-2"></i></span>
                         <input
                             type="email"
                             class="form-control"
                             id="correo"
                             name="correo"
                             required />
                     </div>
                 </div>
                 <div class="mb-3">
                     <label for="celular" class="form-label">Tu Celular:</label>
                     <div class="input-group">
                         <span class="input-group-text bg-success text-white"><i class="fas fa-phone me-2"></i></span>
                         <input
                             type="tel"
                             class="form-control"
                             id="celular"
                             name="celular"
                             placeholder="Ejemplo: 943239039"
                             required />
                     </div>
                 </div>
                 <div class="mb-3">
                     <label for="mensaje" class="form-label">Mensaje</label>
                     <textarea
                         class="form-control"
                         id="mensaje"
                         name="mensaje"
                         rows="4"
                         required></textarea>
                 </div>
                 <button type="submit" class="btn btn-success">ENVIAR Y COTIZAR</button>
             </form>
             <?php if (!empty($mensaje)): ?>
                 <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                     <?= $mensaje ?>
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                 </div>
             <?php endif; ?>
         </div>
     </div>
 </div>