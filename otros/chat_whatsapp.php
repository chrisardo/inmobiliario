<!-- =========================================================
     CHAT WHATSAPP: otros/chat_whatsapp.php
========================================================== -->

<div
    id="chatContainer"
    class="chat-container">


    <!-- BOTÓN -->

    <div id="chatButtonContainer">

        <button
            type="button"
            id="chatButton"
            class="chat-button">

            <i class="bi bi-whatsapp"></i>

            <span>
                Chatea con nosotros
            </span>

        </button>

    </div>


    <!-- FORMULARIO -->

    <div
        id="chatFormContainer"
        class="chat-form-container d-none">

        <div class="chat-header">

            <strong>

                <i class="bi bi-whatsapp me-2"></i>

                WhatsApp

            </strong>


            <button
                type="button"
                id="closeChatForm"
                class="chat-close"
                aria-label="Cerrar">

                <i class="bi bi-x-lg"></i>

            </button>

        </div>


        <div class="chat-body">

            <form id="chatForm">


                <!-- NOMBRE -->

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
                        required>

                </div>


                <!-- ASESOR -->

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
                                value="<?= e($asesor['celular']) ?>"
                                data-nombre="<?= e(
                                    $asesor['nombre'] . ' ' .
                                    $asesor['apellidos']
                                ) ?>">

                                <?= e(
                                    $asesor['nombre'] . ' ' .
                                    $asesor['apellidos']
                                ) ?>

                                -
                                <?= e($asesor['celular']) ?>

                            </option>

                        <?php endforeach; ?>


                        <?php if (empty($asesores)): ?>

                            <option
                                disabled>

                                No hay asesores disponibles

                            </option>

                        <?php endif; ?>

                    </select>

                </div>


                <!-- MENSAJE -->

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
                        required></textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-success w-100">

                    <i class="bi bi-whatsapp me-2"></i>

                    Iniciar chat

                </button>

            </form>

        </div>

    </div>

</div>