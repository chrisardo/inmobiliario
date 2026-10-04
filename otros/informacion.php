<!-- ======================================================
         HORARIO DE ATENCIÓN
    ======================================================= -->

    <section class="about-schedule-section">

        <div class="container">

            <div class="schedule-card">

                <div class="schedule-icon">

                    <i class="bi bi-clock"></i>

                </div>


                <div class="schedule-content">

                    <span class="section-eyebrow">
                        Estamos disponibles
                    </span>

                    <h2>
                        Horario de Atención
                    </h2>

                    <div class="schedule-grid">

                        <div class="schedule-item">

                            <i class="bi bi-calendar-week"></i>

                            <div>

                                <strong>
                                    Lunes a sábado
                                </strong>

                                <span>
                                    10:00 a. m. a 7:00 p. m.
                                </span>

                            </div>

                        </div>


                        <div class="schedule-item">

                            <i class="bi bi-calendar-day"></i>

                            <div>

                                <strong>
                                    Domingo
                                </strong>

                                <span>
                                    10:00 a. m. a 6:00 p. m.
                                </span>

                            </div>

                        </div>

                    </div>


                    <?php if (!empty($usuario['direccion'])): ?>

                        <div class="schedule-location">

                            <i class="bi bi-geo-alt-fill"></i>

                            <div>

                                <strong>
                                    Visita nuestro local
                                </strong>

                                <span>
                                    <?= e($usuario['direccion']); ?>
                                </span>

                            </div>

                        </div>

                    <?php endif; ?>


                    <p class="schedule-message">

                        <i class="bi bi-wifi me-2"></i>

                        También atendemos a través de nuestros canales digitales.

                    </p>

                </div>

            </div>

        </div>

    </section>