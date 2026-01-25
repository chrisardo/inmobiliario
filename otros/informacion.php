<!-- Horario de Atención -->
<div class="container-fluid bg-white py-5">
    <div class="container text-center">

        <!-- Icono -->
        <div class="d-flex justify-content-center mb-3">
            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center"
                style="width:70px; height:70px;">
                <i class="bi bi-clock text-white fs-2"></i>
            </div>
        </div>

        <!-- Título -->
        <h2 class="fw-bold mb-3">Horario de Atención</h2>

        <!-- Horarios -->
        <p class="mb-1">L-S: 10:00 am a 7:00 pm</p>
        <p class="mb-3">D: 10:00 am a 6:00 pm</p>

        <!-- Dirección -->
        <p class="mb-4">
            <strong>Visita nuestro local:</strong>
            <?php echo $usuario['direccion']; ?>
        </p>

        <!-- Mensaje -->
        <p class="fw-semibold">
            Te recordamos que estamos atendiendo a través de todos nuestros canales digitales
        </p>

    </div>
</div>