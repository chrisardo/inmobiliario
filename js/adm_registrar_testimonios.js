"use strict";

// ============================================================
// ELEMENTOS
// ============================================================

const formTestimonio = document.getElementById("formTestimonio");

const inputNombre = document.getElementById("nombre");
const inputApellidos = document.getElementById("apellidos");
const inputComentario = document.getElementById("comentario");

const inputImagen = document.getElementById("imagen");
const preview = document.getElementById("imagenPreview");

const uploadBox = document.getElementById("imageUploadBox");
const uploadIcon = document.getElementById("imageUploadIcon");
const uploadText = document.getElementById("imageUploadText");
const uploadDescription = document.getElementById("imageUploadDescription");

const btnRemoveImage = document.getElementById("btnRemoveImage");

const inputVideo = document.getElementById("video");
const videoPreviewContainer = document.getElementById("videoPreviewContainer");
const videoPreview = document.getElementById("videoPreview");

const btnGuardar = document.getElementById("btnGuardar");

// ============================================================
// CONFIGURACIÓN
// ============================================================

const MAX_VIDEO_SIZE = 300 * 1024 * 1024;
const MAX_IMAGE_SIZE = 5 * 1024 * 1024;

const MAX_TESTIMONIOS = 4;

// ============================================================
// URL TEMPORAL DEL VIDEO
// ============================================================

let videoObjectUrl = null;

// ============================================================
// VERIFICAR ELEMENTOS PRINCIPALES
// ============================================================

if (!formTestimonio) {
console.error("No se encontró #formTestimonio");
}

if (!btnGuardar) {
console.error("No se encontró #btnGuardar");
}

// ============================================================
// ESTADO DEL BOTÓN GUARDAR
// ============================================================

function validarCamposObligatorios() {
const nombre =
    inputNombre.value.trim();

const apellidos =
    inputApellidos.value.trim();

const comentario =
    inputComentario.value.trim();

/*
 * La imagen actualmente se maneja como OPCIONAL
 * porque el backend también permite registrar sin imagen.
 *
 * Si posteriormente quieres hacerla obligatoria,
 * aquí podemos agregar:
 *
 * && inputImagen.files.length > 0
 */

const formularioCompleto =
    nombre !== "" &&
    apellidos !== "" &&
    comentario !== "";

// --------------------------------------------------------
// SI ESTÁ GUARDANDO
// --------------------------------------------------------

if (btnGuardar.dataset.guardando === "true") {

    btnGuardar.disabled = true;

    return;
}

// --------------------------------------------------------
// HABILITAR / DESHABILITAR
// --------------------------------------------------------

btnGuardar.disabled =
    !formularioCompleto;
}

// ============================================================
// EVENTOS CAMPOS OBLIGATORIOS
// ============================================================

inputNombre.addEventListener(
"input",
validarCamposObligatorios
);

inputApellidos.addEventListener(
"input",
validarCamposObligatorios
);

inputComentario.addEventListener(
"input",
validarCamposObligatorios
);

// ============================================================
// ESTADO INICIAL
// ============================================================

validarCamposObligatorios();

// ============================================================
// VIDEO - SELECCIONAR ARCHIVO
// ============================================================

inputVideo.addEventListener(
"change",
function () {
    const file =
        this.files[0];

    // ----------------------------------------------------
    // SIN ARCHIVO
    // ----------------------------------------------------

    if (!file) {

        ocultarVistaPreviaVideo();

        return;
    }

    // ----------------------------------------------------
    // TIPOS PERMITIDOS
    // ----------------------------------------------------

    const allowedTypes = [
        "video/mp4",
        "video/webm",
        "video/ogg"
    ];

    if (
        !allowedTypes.includes(file.type)
    ) {

        this.value = "";

        ocultarVistaPreviaVideo();

        Swal.fire({
            icon: "error",
            title: "Video no válido",
            text: "Solo se permiten videos MP4, WEBM u OGG.",
            confirmButtonColor: "#10b981"
        });

        return;
    }

    // ----------------------------------------------------
    // MÁXIMO 300 MB
    // ----------------------------------------------------

    if (
        file.size > MAX_VIDEO_SIZE
    ) {

        this.value = "";

        ocultarVistaPreviaVideo();

        Swal.fire({
            icon: "error",
            title: "Video demasiado grande",
            text: "El video no puede superar los 300 MB.",
            confirmButtonColor: "#10b981"
        });

        return;
    }

    // ----------------------------------------------------
    // LIBERAR URL ANTERIOR
    // ----------------------------------------------------

    if (videoObjectUrl) {

        URL.revokeObjectURL(
            videoObjectUrl
        );

        videoObjectUrl = null;
    }

    // ----------------------------------------------------
    // CREAR URL TEMPORAL
    // ----------------------------------------------------

    videoObjectUrl =
        URL.createObjectURL(file);

    // ----------------------------------------------------
    // MOSTRAR VIDEO
    // ----------------------------------------------------

    videoPreview.src =
        videoObjectUrl;

    videoPreviewContainer.style.display =
        "block";
}
);

// ============================================================
// OCULTAR VISTA PREVIA VIDEO
// ============================================================

function ocultarVistaPreviaVideo() {
if (videoObjectUrl) {

    URL.revokeObjectURL(
        videoObjectUrl
    );

    videoObjectUrl = null;
}

videoPreview.pause();

videoPreview.removeAttribute("src");

videoPreview.load();

videoPreviewContainer.style.display =
    "none";
}

// ============================================================
// PREVISUALIZAR IMAGEN
// ============================================================

inputImagen.addEventListener(
"change",
function () {
    const file =
        this.files[0];

    if (!file) {
        return;
    }

    // ----------------------------------------------------
    // TIPOS PERMITIDOS
    // ----------------------------------------------------

    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if (
        !allowedTypes.includes(file.type)
    ) {

        this.value = "";

        Swal.fire({
            icon: "error",
            title: "Imagen no válida",
            text: "Solo se permiten imágenes JPG, PNG o WEBP.",
            confirmButtonColor: "#10b981"
        });

        return;
    }

    // ----------------------------------------------------
    // MÁXIMO 5 MB
    // ----------------------------------------------------

    if (
        file.size > MAX_IMAGE_SIZE
    ) {

        this.value = "";

        Swal.fire({
            icon: "error",
            title: "Imagen demasiado grande",
            text: "La imagen no puede superar los 5 MB.",
            confirmButtonColor: "#10b981"
        });

        return;
    }

    // ----------------------------------------------------
    // PREVISUALIZACIÓN
    // ----------------------------------------------------

    const reader =
        new FileReader();

    reader.onload =
        function (event) {

            preview.src =
                event.target.result;

            preview.style.display =
                "block";

            uploadIcon.style.display =
                "none";

            uploadText.style.display =
                "none";

            uploadDescription.style.display =
                "none";

            uploadBox.classList.add(
                "has-image"
            );

            btnRemoveImage.style.display =
                "inline-block";
        };

    reader.readAsDataURL(file);
}
);

// ============================================================
// QUITAR IMAGEN
// ============================================================

btnRemoveImage.addEventListener(
"click",
function () {
    inputImagen.value = "";

    preview.src = "";

    preview.style.display =
        "none";

    uploadIcon.style.display =
        "flex";

    uploadText.style.display =
        "block";

    uploadDescription.style.display =
        "block";

    uploadBox.classList.remove(
        "has-image"
    );

    btnRemoveImage.style.display =
        "none";

    validarCamposObligatorios();
}
);

// ============================================================
// ENVIAR FORMULARIO AJAX
// ============================================================

formTestimonio.addEventListener(
"submit",
async function (event) {
    event.preventDefault();

    // ----------------------------------------------------
    // VALIDAR CAMPOS OBLIGATORIOS
    // ----------------------------------------------------

    const nombre =
        inputNombre.value.trim();

    const apellidos =
        inputApellidos.value.trim();

    const comentario =
        inputComentario.value.trim();

    if (
        !nombre ||
        !apellidos ||
        !comentario
    ) {

        validarCamposObligatorios();

        Swal.fire({
            icon: "warning",
            title: "Campos incompletos",
            text: "Completa nombre, apellidos y testimonio.",
            confirmButtonColor: "#10b981"
        });

        return;
    }

    // ----------------------------------------------------
    // EVITAR DOBLE ENVÍO
    // ----------------------------------------------------

    if (
        btnGuardar.dataset.guardando ===
        "true"
    ) {
        return;
    }

    // ----------------------------------------------------
    // CONFIRMAR
    // ----------------------------------------------------

    const confirmacion =
        await Swal.fire({

            icon: "question",

            title: "¿Guardar testimonio?",

            text: "El testimonio será registrado en el sistema.",

            showCancelButton: true,

            confirmButtonText: "Sí, guardar",

            cancelButtonText: "Cancelar",

            confirmButtonColor: "#10b981",

            cancelButtonColor: "#64748b",

            reverseButtons: true
        });

    if (
        !confirmacion.isConfirmed
    ) {

        validarCamposObligatorios();

        return;
    }

    // ----------------------------------------------------
    // FORM DATA
    // ----------------------------------------------------

    const formData =
        new FormData(formTestimonio);

    // ----------------------------------------------------
    // BLOQUEAR BOTÓN
    // ----------------------------------------------------

    btnGuardar.dataset.guardando =
        "true";

    btnGuardar.disabled =
        true;

    btnGuardar.innerHTML = `
        <span
            class="spinner-border spinner-border-sm me-2"
            aria-hidden="true">
        </span>
        Guardando...
    `;

    // ----------------------------------------------------
    // AJAX
    // ----------------------------------------------------

    try {

        const response =
            await fetch(
                "../controladores/registrar_testimonio.php",
                {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin",
                    headers: {
                        "X-Requested-With":
                            "XMLHttpRequest"
                    }
                }
            );

        // ------------------------------------------------
        // OBTENER RESPUESTA COMO TEXTO PRIMERO
        // ------------------------------------------------
        //
        // Esto permite detectar si PHP está devolviendo
        // un warning, notice, fatal error o HTML en lugar
        // de JSON.
        // ------------------------------------------------

        const textoRespuesta =
            await response.text();

        console.log(
            "Respuesta registrar_testimonio.php:",
            textoRespuesta
        );

        // ------------------------------------------------
        // VERIFICAR HTTP
        // ------------------------------------------------

        if (!response.ok) {

            throw new Error(
                "HTTP " +
                response.status +
                ": " +
                textoRespuesta
            );
        }

        // ------------------------------------------------
        // CONVERTIR JSON
        // ------------------------------------------------

        let data;

        try {

            data =
                JSON.parse(
                    textoRespuesta
                );

        } catch (jsonError) {

            console.error(
                "Respuesta no válida JSON:",
                textoRespuesta
            );

            throw new Error(
                "El servidor no devolvió una respuesta JSON válida. Revisa la consola del navegador y el archivo PHP."
            );
        }

        // ------------------------------------------------
        // REGISTRO EXITOSO
        // ------------------------------------------------

        if (data.success) {

            await Swal.fire({

                icon: "success",

                title:
                    "¡Testimonio registrado!",

                text:
                    data.message ||
                    "El testimonio se registró correctamente.",

                confirmButtonText:
                    "Aceptar",

                confirmButtonColor:
                    "#10b981"
            });

            // ------------------------------------------------
            // LIMPIAR FORMULARIO
            // ------------------------------------------------

            limpiarFormulario();

            return;
        }

        // ------------------------------------------------
        // ERROR DEL SERVIDOR
        // ------------------------------------------------

        Swal.fire({

            icon: "error",

            title:
                "No se pudo guardar",

            text:
                data.message ||
                "Ocurrió un error al registrar el testimonio.",

            confirmButtonColor:
                "#10b981"
        });

    } catch (error) {

        console.error(
            "Error AJAX registrar testimonio:",
            error
        );

        Swal.fire({

            icon: "error",

            title:
                "Error al registrar",

            text:
                error.message ||
                "No fue posible comunicarse con el servidor.",

            confirmButtonColor:
                "#10b981"
        });

    } finally {

        // ------------------------------------------------
        // RESTAURAR BOTÓN
        // ------------------------------------------------

        btnGuardar.dataset.guardando =
            "false";

        btnGuardar.innerHTML = `
            <i class="fa-solid fa-floppy-disk me-1"></i>
            Guardar testimonio
        `;

        // ------------------------------------------------
        // SOLO HABILITAR SI ESTÁ COMPLETO
        // ------------------------------------------------

        validarCamposObligatorios();
    }
}
);

// ============================================================
// LIMPIAR FORMULARIO
// ============================================================

function limpiarFormulario() {
// --------------------------------------------------------
// TEXTO
// --------------------------------------------------------

inputNombre.value = "";

inputApellidos.value = "";

inputComentario.value = "";

// --------------------------------------------------------
// IMAGEN
// --------------------------------------------------------

inputImagen.value = "";

preview.src = "";

preview.style.display =
    "none";

uploadIcon.style.display =
    "flex";

uploadText.style.display =
    "block";

uploadDescription.style.display =
    "block";

uploadBox.classList.remove(
    "has-image"
);

btnRemoveImage.style.display =
    "none";

// --------------------------------------------------------
// VIDEO
// --------------------------------------------------------

inputVideo.value = "";

ocultarVistaPreviaVideo();

// --------------------------------------------------------
// ENFOCAR
// --------------------------------------------------------

inputNombre.focus();

// --------------------------------------------------------
// DESHABILITAR BOTÓN
// --------------------------------------------------------

validarCamposObligatorios();

}
