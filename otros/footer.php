<!-- ==========================================================
     FOOTER
     CoDevPro Technology
     Sistema: Inmobiliaria Iquitos
     ========================================================== -->

<footer class="main-footer">

    <div class="container">

        <div class="row g-4">

            <!-- ==================================================
                 EMPRESA
                 ================================================== -->

            <div class="col-lg-4 col-md-6">

                <div class="footer-brand">
                    <?php if ($fotoPerfil): ?>

                    <img
                        src="<?= e($fotoPerfil); ?>"
                        alt="Logo <?= e($nombreEmpresa); ?>"
                        class="company-logo">

                <?php else: ?>

                    <span class="company-logo-placeholder">
                        <i class="fas fa-building"></i>
                    </span>

                <?php endif; ?>
                    <div>
                        <h3>
                            <?= htmlspecialchars($usuario['nombreEmpresa']) ?>
                        </h3>
                    </div>

                </div>


                <p class="footer-description">

                    <?= htmlspecialchars($usuario['descripcion_acerca']) ?>

                </p>


                <!-- Redes sociales -->

                <div class="footer-social">

                    <a
                        href="#"
                        aria-label="Facebook"
                        title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <a
                        href="#"
                        aria-label="Twitter"
                        title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>

                    <a
                        href="#"
                        aria-label="LinkedIn"
                        title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>

                    <a
                        href="#"
                        aria-label="Instagram"
                        title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>

                </div>

            </div>


            <!-- ==================================================
                 ENLACES
                 ================================================== -->

            <div class="col-lg-2 col-md-6">

                <h4 class="footer-title">
                    Enlaces
                </h4>

                <ul class="footer-links">

                    <li>
                        <a href="./index.php">
                            <i class="fas fa-angle-right"></i>
                            Inicio
                        </a>
                    </li>

                    <li>
                        <a href="./nosotros.php">
                            <i class="fas fa-angle-right"></i>
                            Conócenos
                        </a>
                    </li>

                    <li>
                        <a href="./propiedades.php">
                            <i class="fas fa-angle-right"></i>
                            Propiedades
                        </a>
                    </li>

                    <li>
                        <a href="./asesores.php">
                            <i class="fas fa-angle-right"></i>
                            Asesores
                        </a>
                    </li>

                    <li>
                        <a href="./contacto.php">
                            <i class="fas fa-angle-right"></i>
                            Contacto
                        </a>
                    </li>

                </ul>

            </div>


            <!-- ==================================================
                 CONTACTO
                 ================================================== -->

            <div class="col-lg-3 col-md-6">

                <h4 class="footer-title">
                    Contáctanos
                </h4>

                <ul class="footer-contact">

                    <?php if (!empty($usuario['celular'])): ?>

                        <li>

                            <span class="footer-contact-icon">
                                <i class="fas fa-phone"></i>
                            </span>

                            <div>

                                <small>
                                    Teléfono
                                </small>

                                <a
                                    href="tel:<?= htmlspecialchars($usuario['celular']) ?>">
                                    (+51) <?= htmlspecialchars($usuario['celular']) ?>
                                </a>

                            </div>

                        </li>

                    <?php endif; ?>


                    <?php if (!empty($usuario['email'])): ?>

                        <li>

                            <span class="footer-contact-icon">
                                <i class="fas fa-envelope"></i>
                            </span>

                            <div>

                                <small>
                                    Correo electrónico
                                </small>

                                <a
                                    href="mailto:<?= htmlspecialchars($usuario['email']) ?>">
                                    <?= htmlspecialchars($usuario['email']) ?>
                                </a>

                            </div>

                        </li>

                    <?php endif; ?>


                    <?php if (!empty($usuario['direccion'])): ?>

                        <li>

                            <span class="footer-contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>

                            <div>

                                <small>
                                    Dirección
                                </small>

                                <span class="footer-contact-text">
                                    <?= htmlspecialchars($usuario['direccion']) ?>, Perú
                                </span>

                            </div>

                        </li>

                    <?php endif; ?>

                </ul>

            </div>


            <!-- ==================================================
                 HORARIOS
                 ================================================== -->

            <div class="col-lg-3 col-md-6">

                <h4 class="footer-title">
                    Horario de atención
                </h4>

                <ul class="footer-hours">

                    <li>

                        <div>
                            <strong>
                                Lunes - Viernes
                            </strong>

                            <span>
                                8:30 a. m. - 6:30 p. m.
                            </span>
                        </div>

                        <i class="far fa-clock"></i>

                    </li>


                    <li>

                        <div>
                            <strong>
                                Sábado
                            </strong>

                            <span>
                                9:30 a. m. - 1:00 p. m.
                            </span>
                        </div>

                        <i class="far fa-clock"></i>

                    </li>


                    <li>

                        <div>
                            <strong>
                                Domingo
                            </strong>

                            <span class="footer-closed">
                                Cerrado
                            </span>
                        </div>

                        <i class="far fa-clock"></i>

                    </li>

                </ul>


                <div class="footer-availability">

                    <span class="footer-status-dot"></span>

                    <div>
                        <strong>
                            Atención personalizada
                        </strong>

                        <small>
                            Estamos para ayudarte
                        </small>
                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             DIVISOR
             ================================================== -->

        <div class="footer-divider"></div>


        <!-- ==================================================
             FOOTER INFERIOR
             ================================================== -->

        <div class="footer-bottom">

            <p>

                &copy;
                <?= date('Y') ?>

                <strong>
                    <?= htmlspecialchars($usuario['nombreEmpresa']) ?>
                </strong>.

                Todos los derechos reservados.

            </p>


            <div class="footer-legal">

                <!--<a href="#">
                    Aviso Legal
                </a>

                <span>•</span>

                <a href="#">
                    Política de Privacidad
                </a>-->

                <span>•</span>

                <span class="footer-developed">
                    Desarrollado por
                    <strong>CoDevPro Technology</strong>
                </span>

            </div>

        </div>

    </div>

</footer>


<!-- ==========================================================
     BOTÓN / CHAT WHATSAPP
     ========================================================== -->

<div
    id="chatContainer"
    class="chat-container">

    <!-- Botón -->

    <div id="chatButtonContainer">

        <button
            type="button"
            id="chatButton"
            class="chat-button"
            aria-label="Chatea con nosotros por WhatsApp">

            <i class="fab fa-whatsapp"></i>

            <span>
                Chatea con nosotros
            </span>

        </button>

    </div>


    <!-- Formulario -->

    <div
        id="chatFormContainer"
        class="chat-form-container d-none">

        <div class="chat-header">

            <div class="d-flex align-items-center gap-2">

                <i class="fab fa-whatsapp fs-5"></i>

                <strong>
                    WhatsApp
                </strong>

            </div>


            <button
                type="button"
                id="closeChatForm"
                class="chat-close"
                aria-label="Cerrar">

                <i class="fas fa-times"></i>

            </button>

        </div>


        <div class="chat-body">

            <form id="chatForm">

                <!-- Nombre -->

                <div class="mb-3">

                    <label
                        for="chat_nombre"
                        class="form-label">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="chat_nombre"
                        class="form-control"
                        placeholder="Ingresa tu nombre"
                        autocomplete="name"
                        required>

                </div>


                <!-- Asesor -->

                <div class="mb-3">

                    <label
                        for="chat_asesor"
                        class="form-label">
                        Elige un asesor
                    </label>

                    <select
                        id="chat_asesor"
                        class="form-select"
                        required>

                        <option
                            value=""
                            selected
                            disabled>
                            Selecciona un asesor
                        </option>


                        <?php foreach ($asesores as $asesor): ?>

                            <option
                                value="<?= htmlspecialchars($asesor['celular']) ?>"
                                data-nombre="<?= htmlspecialchars(
                                                    $asesor['nombre'] . ' ' . $asesor['apellidos']
                                                ) ?>">

                                <?= htmlspecialchars(
                                    $asesor['nombre'] . ' ' . $asesor['apellidos']
                                ) ?>

                                –
                                <?= htmlspecialchars($asesor['celular']) ?>

                            </option>

                        <?php endforeach; ?>


                        <?php if (empty($asesores)): ?>

                            <option
                                value=""
                                disabled>
                                No hay asesores disponibles
                            </option>

                        <?php endif; ?>

                    </select>

                </div>


                <!-- Mensaje -->

                <div class="mb-3">

                    <label
                        for="chat_mensaje"
                        class="form-label">
                        Mensaje
                    </label>

                    <textarea
                        id="chat_mensaje"
                        class="form-control"
                        rows="3"
                        placeholder="¿En qué podemos ayudarte?"
                        required></textarea>

                </div>


                <!-- Enviar -->

                <button
                    type="submit"
                    class="btn btn-success w-100 chat-submit">

                    <i class="fab fa-whatsapp me-2"></i>

                    Iniciar conversación

                </button>

            </form>

        </div>

    </div>

</div>