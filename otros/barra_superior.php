<!-- =========================================================
     TOPBAR: otros/barra_superior.php
========================================================== -->

<div class="topbar">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-7 text-center text-lg-start">

                <?php if (!empty($usuario['celular'])): ?>

                    <a
                        href="tel:<?= e($usuario['celular']) ?>"
                        class="topbar-link me-3">

                        <i class="fa-solid fa-phone me-1"></i>

                        +51 <?= e($usuario['celular']) ?>

                    </a>

                <?php endif; ?>


                <?php if (!empty($usuario['email'])): ?>

                    <a
                        href="mailto:<?= e($usuario['email']) ?>"
                        class="topbar-link">

                        <i class="fa-solid fa-envelope me-1"></i>

                        <?= e($usuario['email']) ?>

                    </a>

                <?php endif; ?>

            </div>


            <div class="col-lg-5 text-center text-lg-end">

                <a
                    href="#"
                    class="topbar-social"
                    aria-label="Facebook">

                    <i class="fab fa-facebook-f"></i>

                </a>

                <a
                    href="#"
                    class="topbar-social"
                    aria-label="Instagram">

                    <i class="fab fa-instagram"></i>

                </a>

                <a
                    href="#"
                    class="topbar-social"
                    aria-label="TikTok">

                    <i class="fab fa-tiktok"></i>

                </a>

                <a
                    href="#"
                    class="topbar-social"
                    aria-label="WhatsApp">

                    <i class="fab fa-whatsapp"></i>

                </a>

            </div>

        </div>

    </div>

</div>
