"use strict";

document.addEventListener("DOMContentLoaded", () => {
/* ========================================================
   ELEMENTOS
======================================================== */

const inputImagenes =
    document.getElementById("imagenes");

const uploadArea =
    document.getElementById("imageUploadArea");

const formPropiedad =
    document.getElementById("formPropiedad");

const placeholder =
    document.getElementById("imagePlaceholder");

const previewImagenes =
    document.getElementById("previewImagenes");

const imageInfo =
    document.getElementById("imageInfo");

const imageCount =
    document.getElementById("imageCount");

const imageCountNumber =
    document.getElementById("imageCountNumber");

const imageRemaining =
    document.getElementById("imageRemaining");

const removeImages =
    document.getElementById("removeImages");


/* ========================================================
   ALERTA
======================================================== */

const alertaRegistro =
    document.getElementById("alertaRegistroPropiedad");

const alertaMensaje =
    document.getElementById("alertaRegistroMensaje");

const alertaIcono =
    document.getElementById("alertaRegistroIcon");

const cerrarAlerta =
    document.getElementById("cerrarAlertaRegistro");


/* ========================================================
   CONSTANTES
======================================================== */

const MAX_IMAGES = 4;

const MAX_SIZE =
    1.8 * 1024 * 1024;

const ALLOWED_TYPES = [
    "image/jpeg",
    "image/png"
];


/* ========================================================
   ARCHIVOS SELECCIONADOS
======================================================== */

let archivosSeleccionados = [];


/* ========================================================
   MOSTRAR ALERTA
======================================================== */

function mostrarAlerta(mensaje, tipo = "warning") {

    if (!alertaRegistro || !alertaMensaje) {
        return;
    }


    /* Limpiar clases anteriores */

    alertaRegistro.classList.remove(
        "d-none",
        "alert-success",
        "alert-warning",
        "alert-danger",
        "alert-info"
    );


    /* Tipo de alerta */

    alertaRegistro.classList.add(
        `alert-${tipo}`
    );


    /* Mensaje */

    alertaMensaje.textContent =
        mensaje;


    /* Icono */

    if (alertaIcono) {

        if (tipo === "success") {

            alertaIcono.innerHTML =
                '<i class="fa-solid fa-circle-check"></i>';

        } else if (tipo === "warning") {

            alertaIcono.innerHTML =
                '<i class="fa-solid fa-triangle-exclamation"></i>';

        } else if (tipo === "info") {

            alertaIcono.innerHTML =
                '<i class="fa-solid fa-circle-info"></i>';

        } else {

            alertaIcono.innerHTML =
                '<i class="fa-solid fa-circle-exclamation"></i>';
        }
    }


    /* Mostrar */

    alertaRegistro.classList.add(
        "show"
    );


    /* Llevar la alerta a la vista */

    alertaRegistro.scrollIntoView({
        behavior: "smooth",
        block: "center"
    });
}


/* ========================================================
   OCULTAR ALERTA
======================================================== */

function ocultarAlerta() {

    if (!alertaRegistro) {
        return;
    }

    alertaRegistro.classList.add(
        "d-none"
    );

    alertaRegistro.classList.remove(
        "show"
    );
}


/* ========================================================
   CERRAR ALERTA
======================================================== */

if (cerrarAlerta) {

    cerrarAlerta.addEventListener(
        "click",
        () => {

            ocultarAlerta();

        }
    );
}


/* ========================================================
   FORMATEAR TAMAÑO
======================================================== */

function formatFileSize(bytes) {

    if (bytes < 1024) {

        return `${bytes} B`;
    }


    if (bytes < 1024 * 1024) {

        return `${(
            bytes / 1024
        ).toFixed(1)} KB`;
    }


    return `${(
        bytes /
        (1024 * 1024)
    ).toFixed(2)} MB`;
}


/* ========================================================
   VALIDAR ARCHIVO
======================================================== */

function validarArchivo(file) {

    if (
        !ALLOWED_TYPES.includes(
            file.type
        )
    ) {

        return {
            valido: false,
            mensaje:
                `La imagen "${file.name}" no tiene un formato válido. Solo se permiten imágenes JPG o PNG.`
        };
    }


    if (file.size > MAX_SIZE) {

        return {
            valido: false,
            mensaje:
                `La imagen "${file.name}" supera el tamaño máximo permitido de 1.8 MB. Tamaño actual: ${formatFileSize(file.size)}.`
        };
    }


    return {
        valido: true
    };
}


/* ========================================================
   COMPARAR ARCHIVOS
======================================================== */

function archivoEsIgual(
    fileA,
    fileB
) {

    return (
        fileA.name === fileB.name &&
        fileA.size === fileB.size &&
        fileA.lastModified === fileB.lastModified &&
        fileA.type === fileB.type
    );
}


/* ========================================================
   AGREGAR ARCHIVOS
======================================================== */

function agregarArchivos(files) {

    if (
        !files ||
        files.length === 0
    ) {
        return;
    }


    /* Ocultar alerta anterior */

    ocultarAlerta();


    const nuevosArchivos =
        Array.from(files);


    for (
        const file
        of nuevosArchivos
    ) {

        /* ================================================
           VALIDAR CANTIDAD
        ================================================= */

        if (
            archivosSeleccionados.length >=
            MAX_IMAGES
        ) {

            mostrarAlerta(
                "Solo puedes seleccionar hasta 4 imágenes.",
                "warning"
            );

            break;
        }


        /* ================================================
           VALIDAR ARCHIVO
        ================================================= */

        const validacion =
            validarArchivo(file);


        if (!validacion.valido) {

            mostrarAlerta(
                validacion.mensaje,
                "warning"
            );

            continue;
        }


        /* ================================================
           EVITAR DUPLICADOS
        ================================================= */

        const existe =
            archivosSeleccionados.some(
                archivo =>
                    archivoEsIgual(
                        archivo,
                        file
                    )
            );


        if (existe) {

            mostrarAlerta(
                `La imagen "${file.name}" ya fue seleccionada.`,
                "warning"
            );

            continue;
        }


        /* ================================================
           AGREGAR
        ================================================= */

        archivosSeleccionados.push(
            file
        );
    }


    actualizarInput();

    renderizarPreviews();
}


/* ========================================================
   ACTUALIZAR INPUT FILE
======================================================== */

function actualizarInput() {

    if (!inputImagenes) {
        return;
    }


    try {

        const dataTransfer =
            new DataTransfer();


        archivosSeleccionados.forEach(
            file => {

                dataTransfer.items.add(
                    file
                );
            }
        );


        inputImagenes.files =
            dataTransfer.files;

    } catch (error) {

        console.warn(
            "No fue posible actualizar el input de archivos.",
            error
        );
    }
}


/* ========================================================
   ACTUALIZAR INFORMACIÓN
======================================================== */

function actualizarInformacion() {

    const total =
        archivosSeleccionados.length;


    const restantes =
        MAX_IMAGES - total;


    /* Contador */

    if (imageCountNumber) {

        imageCountNumber.textContent =
            total;
    }


    /* Texto */

    if (imageCount) {

        imageCount.textContent =
            `${total} ${
                total === 1
                    ? "imagen"
                    : "imágenes"
            } seleccionada${
                total === 1
                    ? ""
                    : "s"
            }`;
    }


    /* Imágenes restantes */

    if (imageRemaining) {

        if (total === 0) {

            imageRemaining.textContent =
                "Debes seleccionar al menos 1 imagen";

        } else if (
            restantes === 0
        ) {

            imageRemaining.textContent =
                "Has alcanzado el máximo permitido";

        } else {

            imageRemaining.textContent =
                `Puedes agregar ${restantes} imagen${
                    restantes === 1
                        ? ""
                        : "es"
                } más`;
        }
    }


    /* Mostrar información */

    if (
        total > 0
    ) {

        imageInfo?.classList.remove(
            "d-none"
        );

    } else {

        imageInfo?.classList.add(
            "d-none"
        );
    }


    /* Máximo */

    if (
        total >= MAX_IMAGES
    ) {

        uploadArea?.classList.add(
            "max-images"
        );

    } else {

        uploadArea?.classList.remove(
            "max-images"
        );
    }
}


/* ========================================================
   RENDERIZAR PREVIEWS
======================================================== */

function renderizarPreviews() {

    if (!previewImagenes) {
        return;
    }


    previewImagenes.innerHTML =
        "";


    /* ================================================
       SIN IMÁGENES
    ================================================= */

    if (
        archivosSeleccionados.length === 0
    ) {

        placeholder?.classList.remove(
            "d-none"
        );

        previewImagenes.classList.add(
            "d-none"
        );

        actualizarInformacion();

        return;
    }


    /* ================================================
       CON IMÁGENES
    ================================================= */

    placeholder?.classList.remove(
        "d-none"
    );

    previewImagenes.classList.remove(
        "d-none"
    );


    archivosSeleccionados.forEach(
        (file, index) => {

            const reader =
                new FileReader();


            reader.onload =
                function (event) {

                    const item =
                        document.createElement(
                            "div"
                        );


                    item.className =
                        "image-preview-item";


                    /* ====================================
                       IMAGEN
                    ==================================== */

                    const img =
                        document.createElement(
                            "img"
                        );


                    img.src =
                        event.target.result;


                    img.alt =
                        `Imagen ${index + 1}: ${file.name}`;


                    /* ====================================
                       NÚMERO
                    ==================================== */

                    const orden =
                        document.createElement(
                            "span"
                        );


                    orden.className =
                        "image-preview-order";


                    orden.textContent =
                        index + 1;


                    /* ====================================
                       IMAGEN PRINCIPAL
                    ==================================== */

                    if (
                        index === 0
                    ) {

                        const principal =
                            document.createElement(
                                "span"
                            );


                        principal.className =
                            "image-preview-main";


                        principal.innerHTML =
                            '<i class="fa-solid fa-star"></i> Principal';


                        item.appendChild(
                            principal
                        );
                    }


                    /* ====================================
                       BOTÓN ELIMINAR
                    ==================================== */

                    const remove =
                        document.createElement(
                            "button"
                        );


                    remove.type =
                        "button";


                    remove.className =
                        "image-preview-remove";


                    remove.title =
                        `Deseleccionar ${file.name}`;


                    remove.setAttribute(
                        "aria-label",
                        `Deseleccionar imagen ${index + 1}`
                    );


                    remove.innerHTML =
                        '<i class="fa-solid fa-xmark"></i>';


                    remove.addEventListener(
                        "click",
                        function (event) {

                            event.preventDefault();

                            event.stopPropagation();

                            eliminarImagen(index);
                        }
                    );


                    /* ====================================
                       INSERTAR
                    ==================================== */

                    item.appendChild(
                        img
                    );

                    item.appendChild(
                        orden
                    );

                    item.appendChild(
                        remove
                    );

                    previewImagenes.appendChild(
                        item
                    );
                };


            reader.readAsDataURL(
                file
            );
        }
    );


    actualizarInformacion();
}


/* ========================================================
   ELIMINAR UNA IMAGEN
======================================================== */

function eliminarImagen(index) {

    if (
        index < 0 ||
        index >= archivosSeleccionados.length
    ) {
        return;
    }


    const archivoEliminado =
        archivosSeleccionados[index];


    archivosSeleccionados.splice(
        index,
        1
    );


    actualizarInput();

    renderizarPreviews();


    mostrarAlerta(
        `Se deseleccionó la imagen "${archivoEliminado.name}".`,
        "info"
    );
}


/* ========================================================
   ELIMINAR TODAS
======================================================== */

function limpiarImagenes() {

    archivosSeleccionados =
        [];


    if (inputImagenes) {

        inputImagenes.value =
            "";
    }


    if (previewImagenes) {

        previewImagenes.innerHTML =
            "";

        previewImagenes.classList.add(
            "d-none"
        );
    }


    placeholder?.classList.remove(
        "d-none"
    );


    imageInfo?.classList.add(
        "d-none"
    );


    uploadArea?.classList.remove(
        "dragover",
        "max-images"
    );


    actualizarInformacion();


    mostrarAlerta(
        "Se eliminaron todas las imágenes seleccionadas.",
        "info"
    );
}


/* ========================================================
   INPUT FILE
======================================================== */

if (inputImagenes) {

    inputImagenes.addEventListener(
        "change",
        function () {

            agregarArchivos(
                this.files
            );


            /*
             * Permite seleccionar nuevamente
             * el mismo archivo.
             */

            this.value =
                "";
        }
    );
}


/* ========================================================
   ELIMINAR TODAS
======================================================== */

if (removeImages) {

    removeImages.addEventListener(
        "click",
        function (event) {

            event.preventDefault();

            event.stopPropagation();


            if (
                archivosSeleccionados.length === 0
            ) {
                return;
            }


            limpiarImagenes();
        }
    );
}


/* ========================================================
   DRAG & DROP
======================================================== */

if (uploadArea) {

    ["dragenter", "dragover"].forEach(
        eventName => {

            uploadArea.addEventListener(
                eventName,
                event => {

                    event.preventDefault();

                    event.stopPropagation();


                    if (
                        archivosSeleccionados.length <
                        MAX_IMAGES
                    ) {

                        uploadArea.classList.add(
                            "dragover"
                        );
                    }
                }
            );
        }
    );


    ["dragleave", "drop"].forEach(
        eventName => {

            uploadArea.addEventListener(
                eventName,
                event => {

                    event.preventDefault();

                    event.stopPropagation();

                    uploadArea.classList.remove(
                        "dragover"
                    );
                }
            );
        }
    );


    uploadArea.addEventListener(
        "drop",
        event => {

            if (
                archivosSeleccionados.length >=
                MAX_IMAGES
            ) {

                mostrarAlerta(
                    "Ya has seleccionado el máximo de 4 imágenes.",
                    "warning"
                );

                return;
            }


            const files =
                event.dataTransfer.files;


            agregarArchivos(
                files
            );
        }
    );
}


/* ========================================================
   PREVENIR DROP FUERA DEL ÁREA
======================================================== */

document.addEventListener(
    "dragover",
    event => {

        event.preventDefault();
    }
);


document.addEventListener(
    "drop",
    event => {

        if (
            uploadArea &&
            !uploadArea.contains(
                event.target
            )
        ) {

            event.preventDefault();
        }
    }
);


/* ========================================================
   VALIDAR FORMULARIO
======================================================== */

if (formPropiedad) {

    formPropiedad.addEventListener(
        "submit",
        function (event) {

            /* ============================================
               VALIDAR IMAGEN OBLIGATORIA
            ============================================ */

            if (
                archivosSeleccionados.length < 1
            ) {

                event.preventDefault();


                mostrarAlerta(
                    "Debes seleccionar al menos una imagen de la propiedad antes de registrarla.",
                    "warning"
                );


                /* Resaltar área */

                if (uploadArea) {

                    uploadArea.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });


                    uploadArea.classList.add(
                        "image-required-error"
                    );


                    setTimeout(
                        () => {

                            uploadArea.classList.remove(
                                "image-required-error"
                            );

                        },
                        2500
                    );
                }


                return false;
            }


            /* ============================================
               VALIDAR MÁXIMO
            ============================================ */

            if (
                archivosSeleccionados.length >
                MAX_IMAGES
            ) {

                event.preventDefault();


                mostrarAlerta(
                    "Puedes registrar como máximo 4 imágenes.",
                    "warning"
                );


                return false;
            }


            /* ============================================
               ACTUALIZAR INPUT
            ============================================ */

            actualizarInput();


            return true;
        }
    );
}


/* ========================================================
   ESTADO INICIAL
======================================================== */

actualizarInformacion();
});
