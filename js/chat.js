// ==========================================================
// CHAT WHATSAPP
// Sistema inmobiliario
// Archivo: js/chat.js
// ==========================================================

"use strict";

document.addEventListener("DOMContentLoaded", function () {

    // ======================================================
    // ELEMENTOS
    // ======================================================

    const chatButton = document.getElementById("chatButton");
    const chatButtonContainer = document.getElementById("chatButtonContainer");

    const chatFormContainer = document.getElementById("chatFormContainer");
    const closeChatForm = document.getElementById("closeChatForm");

    const chatForm = document.getElementById("chatForm");

    const chatNombre = document.getElementById("chat_nombre");
    const chatAsesor = document.getElementById("chat_asesor");
    const chatMensaje = document.getElementById("chat_mensaje");


    // ======================================================
    // VALIDAR QUE EXISTAN LOS ELEMENTOS
    // ======================================================

    if (
        !chatButton ||
        !chatButtonContainer ||
        !chatFormContainer ||
        !closeChatForm ||
        !chatForm ||
        !chatNombre ||
        !chatAsesor ||
        !chatMensaje
    ) {
        console.error(
            "Chat WhatsApp: no se encontraron todos los elementos HTML necesarios."
        );

        return;
    }


    // ======================================================
    // ABRIR CHAT
    // ======================================================

    chatButton.addEventListener("click", function () {

        chatButtonContainer.classList.add("d-none");

        chatFormContainer.classList.remove("d-none");

        // Enfocar automáticamente el nombre
        setTimeout(function () {
            chatNombre.focus();
        }, 100);

    });


    // ======================================================
    // CERRAR CHAT
    // ======================================================

    closeChatForm.addEventListener("click", function () {

        chatFormContainer.classList.add("d-none");

        chatButtonContainer.classList.remove("d-none");

    });


    // ======================================================
    // ENVIAR CHAT
    // ======================================================

    chatForm.addEventListener("submit", function (e) {

        e.preventDefault();


        // ==================================================
        // OBTENER DATOS
        // ==================================================

        const nombre = chatNombre.value.trim();

        const mensaje = chatMensaje.value.trim();

        const asesorTelefono = chatAsesor.value.trim();

        const asesorSeleccionado =
            chatAsesor.options[chatAsesor.selectedIndex];

        const asesorNombre =
            asesorSeleccionado
                ? asesorSeleccionado.getAttribute("data-nombre") || ""
                : "";


        // ==================================================
        // VALIDAR NOMBRE
        // ==================================================

        if (nombre.length < 3) {

            alert("⚠️ Por favor, ingrese su nombre.");

            chatNombre.focus();

            return;
        }


        // ==================================================
        // VALIDAR ASESOR
        // ==================================================

        if (!asesorTelefono) {

            alert("⚠️ Por favor, seleccione un asesor.");

            chatAsesor.focus();

            return;
        }


        // ==================================================
        // VALIDAR MENSAJE
        // ==================================================

        if (mensaje.length < 1) {

            alert("⚠️ Por favor, escriba su mensaje.");

            chatMensaje.focus();

            return;
        }


        // ==================================================
        // LIMPIAR NÚMERO DE TELÉFONO
        // ==================================================

        let telefono = asesorTelefono.replace(/\D/g, "");


        // Si viene como 51943239039
        if (telefono.startsWith("51") && telefono.length === 11) {

            telefono = telefono.substring(2);
        }


        // Debe quedar un celular peruano de 9 dígitos
        if (!/^[0-9]{9}$/.test(telefono)) {

            alert(
                "⚠️ El número de WhatsApp del asesor no es válido."
            );

            return;
        }


        // ==================================================
        // EMPRESA
        // ==================================================

        const empresa = "Mi Inmobiliaria";


        // ==================================================
        // CONSTRUIR MENSAJE
        // ==================================================

        const texto = [
            `Hola ${asesorNombre || "asesor"},`,
            "",
            `Mi nombre es ${nombre}.`,
            "",
            mensaje,
            "",
            `Enviado desde la página web de ${empresa}.`
        ].join("\n");


        // ==================================================
        // URL WHATSAPP
        // ==================================================

        const url =
            "https://wa.me/51" +
            telefono +
            "?text=" +
            encodeURIComponent(texto);


        // ==================================================
        // ABRIR WHATSAPP
        // ==================================================

        window.open(
            url,
            "_blank",
            "noopener,noreferrer"
        );

    });

});
