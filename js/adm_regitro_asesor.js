/* ============================================================
   CoDevPro Technology
   Archivo: js/adm_regitro_asesor.js
   Módulo: Registro de asesores
============================================================ */

"use strict";

document.addEventListener("DOMContentLoaded", () => {

    /* ========================================================
       ELEMENTOS
    ======================================================== */

    const form = document.getElementById("formRegistroAsesor");
    const inputImagen = document.getElementById("imagen");
    const uploadArea = document.getElementById("uploadArea");

    const previewCont = document.getElementById("previewImagen");
    const previewImg = document.getElementById("previewImg");

    const imgTipo = document.getElementById("imgTipo");
    const imgSize = document.getElementById("imgSize");

    const removeImagen = document.getElementById("removeImagen");
    const celular = document.getElementById("celular");

    const btnRegistrar = document.getElementById("btnRegistrar");

    const buttonNormal = btnRegistrar
        ? btnRegistrar.querySelector(".button-normal")
        : null;

    const buttonLoading = btnRegistrar
        ? btnRegistrar.querySelector(".button-loading")
        : null;


    /* ========================================================
       CONSTANTES
    ======================================================== */

    const MAX_FILE_SIZE = 1.8 * 1024 * 1024;

    const ALLOWED_TYPES = [
        "image/jpeg",
        "image/png"
    ];


    /* ========================================================
       CELULAR
    ======================================================== */

    if (celular) {

        celular.addEventListener("input", () => {

            celular.value = celular.value
                .replace(/\D/g, "")
                .slice(0, 9);

        });

    }


    /* ========================================================
       FORMATEAR TAMAÑO
    ======================================================== */

    function formatFileSize(bytes) {

        if (bytes < 1024) {
            return `${bytes} B`;
        }

        if (bytes < 1024 * 1024) {
            return `${(bytes / 1024).toFixed(2)} KB`;
        }

        return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;

    }


    /* ========================================================
       ERROR DE IMAGEN
    ======================================================== */

    function showImageError(message) {

        if (uploadArea) {

            uploadArea.classList.add("border-danger");

            uploadArea.setAttribute(
                "data-error",
                message
            );

        }

        if (inputImagen) {
            inputImagen.classList.add("is-invalid");
        }

        setTimeout(() => {

            if (uploadArea) {
                uploadArea.classList.remove("border-danger");
            }

        }, 2500);

    }


    /* ========================================================
       MOSTRAR PREVIEW
    ======================================================== */

    function showPreview(file) {

        if (!previewCont || !previewImg) {
            return;
        }

        if (imgTipo) {
            imgTipo.textContent = file.type;
        }

        if (imgSize) {
            imgSize.textContent = formatFileSize(file.size);
        }


        const reader = new FileReader();


        reader.onload = (event) => {

            previewImg.src = event.target.result;

            previewCont.classList.remove("d-none");

            if (inputImagen) {
                inputImagen.classList.remove("is-invalid");
            }

        };


        reader.readAsDataURL(file);

    }


    /* ========================================================
       LIMPIAR IMAGEN
    ======================================================== */

    function clearImage() {

        if (inputImagen) {
            inputImagen.value = "";
            inputImagen.classList.add("is-invalid");
        }

        if (previewImg) {
            previewImg.src = "";
        }

        if (previewCont) {
            previewCont.classList.add("d-none");
        }

        if (imgTipo) {
            imgTipo.textContent = "-";
        }

        if (imgSize) {
            imgSize.textContent = "-";
        }

    }


    /* ========================================================
       VALIDAR ARCHIVO
    ======================================================== */

    function validateImage(file) {

        if (!file) {

            showImageError(
                "⚠️ Debes seleccionar una fotografía del asesor."
            );

            return false;
        }


        /* ====================================================
           TIPO
        ==================================================== */

        if (!ALLOWED_TYPES.includes(file.type)) {

            clearImage();

            showImageError(
                "❌ Solo se permiten imágenes JPG o PNG."
            );

            return false;
        }


        /* ====================================================
           TAMAÑO
        ==================================================== */

        if (file.size > MAX_FILE_SIZE) {

            clearImage();

            showImageError(
                "❌ La imagen no puede superar 1.8 MB."
            );

            return false;
        }


        return true;

    }


    /* ========================================================
       CAMBIO DE IMAGEN
    ======================================================== */

    if (inputImagen) {

        inputImagen.addEventListener("change", () => {

            const file = inputImagen.files[0];


            if (!file) {

                clearImage();

                return;

            }


            if (!validateImage(file)) {
                return;
            }


            showPreview(file);

        });

    }


    /* ========================================================
       ELIMINAR IMAGEN
    ======================================================== */

    if (removeImagen) {

        removeImagen.addEventListener("click", () => {

            clearImage();

        });

    }


    /* ========================================================
       DRAG & DROP
    ======================================================== */

    if (uploadArea) {

        uploadArea.addEventListener("dragover", (event) => {

            event.preventDefault();

            uploadArea.classList.add("dragover");

        });


        uploadArea.addEventListener("dragleave", () => {

            uploadArea.classList.remove("dragover");

        });


        uploadArea.addEventListener("drop", (event) => {

            event.preventDefault();

            uploadArea.classList.remove("dragover");


            const files = event.dataTransfer.files;


            if (!files || files.length === 0) {

                showImageError(
                    "⚠️ Debes seleccionar una fotografía del asesor."
                );

                return;

            }


            const file = files[0];


            if (!validateImage(file)) {
                return;
            }


            /*
             * Sincronizar archivo con input
             */

            try {

                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(file);

                inputImagen.files = dataTransfer.files;

            } catch (error) {

                console.warn(
                    "No se pudo asignar el archivo al input.",
                    error
                );

            }


            showPreview(file);

        });

    }


    /* ========================================================
       VALIDACIÓN DEL FORMULARIO
    ======================================================== */

    if (form) {

        form.addEventListener("submit", (event) => {

            let valid = true;


            /* =================================================
               CAMPOS OBLIGATORIOS
            ================================================= */

            const requiredFields = form.querySelectorAll(
                "input[required]"
            );


            requiredFields.forEach((field) => {

                field.classList.remove("is-invalid");


                if (!field.value.trim()) {

                    field.classList.add("is-invalid");

                    valid = false;

                }

            });


            /* =================================================
               EMAIL
            ================================================= */

            const email = document.getElementById("email");


            if (
                email &&
                email.value.trim() !== ""
            ) {

                const emailPattern =
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


                if (!emailPattern.test(email.value.trim())) {

                    email.classList.add("is-invalid");

                    valid = false;

                }

            }


            /* =================================================
               CELULAR
            ================================================= */

            if (celular) {

                const celularValue =
                    celular.value.trim();


                if (!/^[0-9]{9}$/.test(celularValue)) {

                    celular.classList.add("is-invalid");

                    valid = false;

                }

            }


            /* =================================================
               IMAGEN OBLIGATORIA
            ================================================= */

            if (
                !inputImagen ||
                !inputImagen.files ||
                inputImagen.files.length === 0
            ) {

                valid = false;


                if (inputImagen) {
                    inputImagen.classList.add("is-invalid");
                }


                showImageError(
                    "⚠️ La fotografía del asesor es obligatoria."
                );

            } else {

                const file = inputImagen.files[0];


                if (!validateImage(file)) {

                    valid = false;

                }

            }


            /* =================================================
               FORMULARIO INVÁLIDO
            ================================================= */

            if (!valid) {

                event.preventDefault();


                const firstInvalid =
                    form.querySelector(".is-invalid");


                if (firstInvalid) {

                    firstInvalid.focus();

                    firstInvalid.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });

                }

                return;

            }


            /* =================================================
               ESTADO DE CARGA
            ================================================= */

            if (btnRegistrar) {

                btnRegistrar.disabled = true;

            }


            if (buttonNormal) {

                buttonNormal.classList.add("d-none");

            }


            if (buttonLoading) {

                buttonLoading.classList.remove("d-none");

                buttonLoading.style.display =
                    "inline-flex";

            }

        });


        /* ====================================================
           QUITAR ERROR AL ESCRIBIR
        ==================================================== */

        form.querySelectorAll("input").forEach((input) => {

            input.addEventListener("input", () => {

                /*
                 * No quitar el error de imagen si todavía
                 * no existe una imagen seleccionada.
                 */

                if (
                    input === inputImagen &&
                    (!inputImagen.files ||
                        inputImagen.files.length === 0)
                ) {

                    return;

                }


                input.classList.remove("is-invalid");

            });

        });

    }


    /* ========================================================
       PEGAR CELULAR
    ======================================================== */

    if (celular) {

        celular.addEventListener("paste", () => {

            setTimeout(() => {

                celular.value = celular.value
                    .replace(/\D/g, "")
                    .slice(0, 9);

            }, 0);

        });

    }

});