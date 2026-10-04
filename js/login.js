// =========================================================
// js/login.js
// Sistema AJAX de inicio de sesión
// =========================================================

document.addEventListener("DOMContentLoaded", function () {

    // =====================================================
    // ELEMENTOS
    // =====================================================

    const loginForm = document.getElementById("loginForm");

    const usernameInput = document.getElementById("username");

    const passwordInput = document.getElementById("password");

    const togglePassword = document.getElementById("togglePassword");

    const loginButton = document.getElementById("loginButton");

    const loginButtonText = document.getElementById("loginButtonText");

    const loginButtonIcon = document.getElementById("loginButtonIcon");

    const loginError = document.getElementById("loginError");

    const loginErrorMessage = document.getElementById("loginErrorMessage");

    const loginSuccess = document.getElementById("loginSuccess");

    const loginSuccessMessage = document.getElementById("loginSuccessMessage");


    // =====================================================
    // MOSTRAR / OCULTAR CONTRASEÑA
    // =====================================================

    if (togglePassword && passwordInput) {

        togglePassword.addEventListener("click", function () {

            const isPassword =
                passwordInput.type === "password";

            passwordInput.type =
                isPassword ? "text" : "password";


            const icon =
                this.querySelector("i");


            if (icon) {

                icon.classList.toggle(
                    "fa-eye",
                    !isPassword
                );

                icon.classList.toggle(
                    "fa-eye-slash",
                    isPassword
                );

            }


            this.setAttribute(
                "aria-label",
                isPassword
                    ? "Ocultar contraseña"
                    : "Mostrar contraseña"
            );

        });

    }


    // =====================================================
    // OCULTAR MENSAJES
    // =====================================================

    function ocultarMensajes() {

        if (loginError) {

            loginError.style.display = "none";

        }

        if (loginSuccess) {

            loginSuccess.style.display = "none";

        }

    }


    // =====================================================
    // MOSTRAR ERROR
    // =====================================================

    function mostrarError(mensaje) {

        ocultarMensajes();

        if (!loginError || !loginErrorMessage) {
            return;
        }

        loginErrorMessage.textContent =
            mensaje || "Ocurrió un error al iniciar sesión.";

        loginError.style.display = "flex";

    }


    // =====================================================
    // MOSTRAR ÉXITO
    // =====================================================

    function mostrarExito(mensaje) {

        ocultarMensajes();

        if (!loginSuccess || !loginSuccessMessage) {
            return;
        }

        loginSuccessMessage.textContent =
            mensaje || "Inicio de sesión correcto.";

        loginSuccess.style.display = "flex";

    }


    // =====================================================
    // ESTADO BOTÓN - CARGANDO
    // =====================================================

    function activarCarga() {

        if (!loginButton) {
            return;
        }

        loginButton.disabled = true;

        loginButtonText.textContent =
            "Verificando...";

        loginButtonIcon.className =
            "fa-solid fa-spinner fa-spin";

    }


    // =====================================================
    // ESTADO BOTÓN - NORMAL
    // =====================================================

    function desactivarCarga() {

        if (!loginButton) {
            return;
        }

        loginButton.disabled = false;

        loginButtonText.textContent =
            "Ingresar";

        loginButtonIcon.className =
            "fa-solid fa-arrow-right";

    }


    // =====================================================
    // SUBMIT AJAX
    // =====================================================

    if (loginForm) {

        loginForm.addEventListener("submit", async function (event) {

            // Evita que el formulario recargue la página
            event.preventDefault();


            ocultarMensajes();


            // =================================================
            // DATOS
            // =================================================

            const username =
                usernameInput.value.trim();

            const password =
                passwordInput.value;


            // =================================================
            // VALIDACIÓN FRONTEND
            // =================================================

            if (!username) {

                mostrarError(
                    "Ingresa tu usuario o correo electrónico."
                );

                usernameInput.focus();

                return;

            }


            if (!password) {

                mostrarError(
                    "Ingresa tu contraseña."
                );

                passwordInput.focus();

                return;

            }


            // =================================================
            // ACTIVAR CARGANDO
            // =================================================

            activarCarga();


            try {

                // =================================================
                // FORMDATA
                // =================================================

                const formData =
                    new FormData(loginForm);


                // =================================================
                // AJAX
                // =================================================

                const response =
                    await fetch(
                        loginForm.action,
                        {
                            method: "POST",
                            body: formData,
                            credentials: "same-origin",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            }
                        }
                    );


                // =================================================
                // COMPROBAR HTTP
                // =================================================

                if (!response.ok) {

                    throw new Error(
                        "HTTP " + response.status
                    );

                }


                // =================================================
                // LEER JSON
                // =================================================

                const data =
                    await response.json();


                // =================================================
                // RESPUESTA CORRECTA
                // =================================================

                if (data.success === true) {

                    mostrarExito(
                        data.message ||
                        "Inicio de sesión correcto."
                    );


                    // Evita volver a enviar el formulario
                    loginButton.disabled = true;


                    // =================================================
                    // REDIRECCIÓN
                    // =================================================

                    setTimeout(function () {

                        window.location.href =
                            data.redirect ||
                            "adm/adm_index.php";

                    }, 500);


                    return;

                }


                // =================================================
                // ERROR DEL LOGIN
                // =================================================

                mostrarError(
                    data.message ||
                    "Usuario o contraseña incorrectos."
                );


                desactivarCarga();


                // Mantener usuario y limpiar contraseña
                passwordInput.value = "";

                passwordInput.focus();

            }
            catch (error) {

                console.error(
                    "Error AJAX login:",
                    error
                );


                mostrarError(
                    "No se pudo conectar con el servidor. " +
                    "Verifica la conexión e inténtalo nuevamente."
                );


                desactivarCarga();

            }

        });

    }

});
