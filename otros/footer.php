  <!-- Footer -->
  <footer class="bg-dark text-white py-5">
      <div class="container">
          <div class="row">
              <div class="col-lg-4 mb-4 mb-lg-0">
                  <h4 class="text-info mb-3"><?php echo $usuario['nombreEmpresa']; ?></h4>
                  <p class="mb-3">
                      <?php echo $usuario['descripcion_acerca']; ?>
                  </p>
                  <div class="d-flex">
                      <a href="#" class="text-white me-3">
                          <i class="fab fa-facebook-f fa-lg"></i>
                      </a>
                      <a href="#" class="text-white me-3">
                          <i class="fab fa-twitter fa-lg"></i>
                      </a>
                      <a href="#" class="text-white me-3">
                          <i class="fab fa-linkedin-in fa-lg"></i>
                      </a>
                      <a href="#" class="text-white">
                          <i class="fab fa-instagram fa-lg"></i>
                      </a>
                  </div>
              </div>
              <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                  <h5 class="mb-3">Enlaces</h5>
                  <ul class="list-unstyled">
                      <li class="mb-2">
                          <a href="./index.php" class="text-white text-decoration-none">Inicio</a>
                      </li>
                      <li class="mb-2">
                          <a href="./nosotros.php" class="text-white text-decoration-none">Conócenos</a>
                      </li>
                      <li class="mb-2">
                          <a href="./propiedades.php" class="text-white text-decoration-none">Propiedades</a>
                      </li>
                      <li class="mb-2">
                          <a href="./asesores.php" class="text-white text-decoration-none">Asesores</a>
                      </li>
                      <li class="mb-2">
                          <a href="./contacto.php" class="text-white text-decoration-none">Contacto</a>
                      </li>
                  </ul>
              </div>
              <div class="col-lg-3">
                  <h5 class="mb-3">Contacto</h5>
                  <ul class="list-unstyled">
                      <li class="mb-2">
                          <i class="fas fa-phone me-2"></i> (+51) <?php if (!empty($usuario['celular'])): ?>

                              <a href="tel:<?= $usuario['celular'] ?>" target="_blank" class="text-white">
                                  <?php echo $usuario['celular'];
                                    ?>
                              </a>
                          <?php endif; ?>
                      </li>
                      <li class="mb-2">
                          <i class="fas fa-envelope me-2"></i> <a href="mailto:<?= $usuario['email'] ?>" class="text-white">
                              <?php echo $usuario['email']; ?>
                          </a>
                      </li>
                      <li class="mb-2">
                          <i class="fas fa-map-marker-alt me-2"></i> <?php echo $usuario['direccion']; ?>, Perú
                      </li>
                  </ul>
              </div>
              <div class="col-lg-3">
                  <h5 class="mb-3">Horario de apertura</h5>
                  <ul class="list-unstyled">
                      <li class="mb-2">Lun - Vi: 8:30 am to 6:30 pm</li>
                      <li class="mb-2">Sábado: 9:30 am to 1:00 pm</li>
                      <li class="mb-2">Domingo: Closed</li>
                  </ul>
              </div>
          </div>
          <hr class="my-4 bg-secondary" />
          <div class="row">
              <div class="col-md-6">
                  <p class="mb-0 small">
                      &copy; Todos los derechos reservadoos 2024. Desarrollado por
                      CoDevPro Technology.
                  </p>
              </div>
              <div class="col-md-6 text-md-end">
                  <a href="#" class="text-white text-decoration-none small me-3">Aviso Legal</a>
                  <a href="#" class="text-white text-decoration-none small">Política de Privacidad</a>
              </div>
          </div>
      </div>

  </footer>
  <!-- Floating Chat Button -->
  <!-- Floating Chat Button -->
  <div id="chatContainer" class="position-fixed bottom-0 end-0 p-4" style="z-index:99999">

      <div id="chatButtonContainer">
          <button id="chatButton" class="btn btn-success btn-lg d-flex align-items-center">
              <i class="bi bi-whatsapp fs-4 me-2"></i>
              Chatea con nosotros
          </button>
      </div>

      <div id="chatFormContainer" class="card d-none shadow" style="width:320px">
          <div class="card-header bg-success text-white d-flex justify-content-between">
              <strong>WhatsApp</strong>
              <button type="button" id="closeChatForm" class="btn-close btn-close-white"></button>
          </div>

          <div class="card-body">
              <form id="chatForm">
                  <div class="mb-2">
                      <label class="form-label">Nombre</label>
                      <input type="text" id="chat_nombre" class="form-control" required>
                  </div>

                  <div class="mb-2">
                      <label class="form-label">Elige un asesor:</label>
                      <div class="input-group">
                          <select id="chat_asesor" class="form-select" required>
                              <option value="" selected disabled>Selecciona un asesor</option>

                              <?php foreach ($asesores as $asesor): ?>
                                  <option
                                      value="<?= htmlspecialchars($asesor['celular']) ?>"
                                      data-nombre="<?= htmlspecialchars($asesor['nombre'] . ' ' . $asesor['apellidos']) ?>">
                                      <?= htmlspecialchars($asesor['nombre'] . ' ' . $asesor['apellidos']) ?>
                                      – <?= htmlspecialchars($asesor['celular']) ?>
                                  </option>
                              <?php endforeach; ?>

                              <?php if (empty($asesores)): ?>
                                  <option disabled>No hay asesores disponibles</option>
                              <?php endif; ?>
                          </select>

                      </div>
                  </div>

                  <div class="mb-2">
                      <label class="form-label">Mensaje</label>
                      <textarea id="chat_mensaje" class="form-control" rows="2" required></textarea>
                  </div>

                  <button class="btn btn-success w-100">Iniciar chat</button>
              </form>
          </div>
      </div>
  </div>

  </div>
