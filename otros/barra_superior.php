  <!-- Barra superior de información -->
  <div class="bg-success text-white py-1 fixed-top">
      <div class="container">
          <div class="row align-items-center">
              <div class="col-md-6">
                  <span class="small me-3">
                      <i class="fas fa-phone me-1"></i> (+51)
                      <?php if (!empty($usuario['celular'])): ?>

                          <a href="tel:<?= $usuario['celular'] ?>" target="_blank" class="text-white">
                              <?php echo $usuario['celular']; 
                                ?>
                          </a>
                      <?php endif; ?>

                  </span>
                  <span class="small">
                      <i class="fas fa-envelope me-1"></i>
                      <a href="mailto:<?= $usuario['email'] ?>" class="text-white">
                          <?php echo $usuario['email']; ?>
                      </a>
                  </span>
              </div>
              <div class="col-md-6 text-md-end">
                  <a href="#" class="text-white text-decoration-none me-3">
                      <i class="fab fa-facebook-f"></i>
                  </a>
                  <a href="#" class="text-white text-decoration-none me-3">
                      <i class="fab fa-twitter"></i>
                  </a>
                  <a href="#" class="text-white text-decoration-none me-3">
                      <i class="fab fa-linkedin-in"></i>
                  </a>
                  <a href="#" class="text-white text-decoration-none">
                      <i class="fab fa-instagram"></i>
                  </a>
              </div>
          </div>
      </div>