
document.addEventListener(
    "DOMContentLoaded",
    function () {


        /*==================================================
          NAVBAR STICKY + SOMBRA
        ==================================================*/

        const navbar =
            document.getElementById(
                "navbarPrincipal"
            );


        if (navbar) {


            const controlarNavbar =
                function () {

                    if (
                        window.scrollY > 10
                    ) {

                        navbar.classList.add(
                            "navbar-scrolled"
                        );

                    } else {

                        navbar.classList.remove(
                            "navbar-scrolled"
                        );

                    }

                };


            window.addEventListener(
                "scroll",
                controlarNavbar,
                {
                    passive: true
                }
            );


            controlarNavbar();

        }


        /*==================================================
          ANIMACIÓN DE TARJETAS
        ==================================================*/

        const propertyCards =
            document.querySelectorAll(
                ".property-card"
            );


        if (
            "IntersectionObserver"
            in window
        ) {


            const observer =
                new IntersectionObserver(
                    function (entries) {


                        entries.forEach(
                            function (entry) {


                                if (
                                    entry.isIntersecting
                                ) {


                                    entry.target.classList.add(
                                        "property-visible"
                                    );


                                    observer.unobserve(
                                        entry.target
                                    );


                                }

                            }
                        );


                    },
                    {
                        threshold: 0.10
                    }
                );


            propertyCards.forEach(
                function (card) {

                    observer.observe(card);

                }
            );


        } else {


            propertyCards.forEach(
                function (card) {

                    card.classList.add(
                        "property-visible"
                    );

                }
            );

        }


        /*==================================================
          CHAT
        ==================================================*/

        if (window.jQuery) {


            $(function () {


                /*========================================
                  ABRIR CHAT
                ========================================*/

                $("#chatButton").on(
                    "click",
                    function () {


                        $("#chatButtonContainer")
                            .hide();


                        $("#chatFormContainer")
                            .removeClass(
                                "d-none"
                            );

                    }
                );


                /*========================================
                  CERRAR CHAT
                ========================================*/

                $("#closeChatForm").on(
                    "click",
                    function () {


                        $("#chatFormContainer")
                            .addClass(
                                "d-none"
                            );


                        $("#chatButtonContainer")
                            .show();

                    }
                );


                /*========================================
                  ENVIAR WHATSAPP
                ========================================*/

                $("#chatForm").on(
                    "submit",
                    function (e) {


                        e.preventDefault();


                        const nombre =
                            $("#chat_nombre")
                                .val()
                                .trim();


                        const mensaje =
                            $("#chat_mensaje")
                                .val()
                                .trim();


                        const asesorTelefono =
                            $("#chat_asesor")
                                .val();


                        /*==============================
                          VALIDAR ASESOR
                        ==============================*/

                        if (!asesorTelefono) {

                            alert(
                                "⚠️ Seleccione un asesor."
                            );

                            return;

                        }


                        /*==============================
                          VALIDAR NOMBRE
                        ==============================*/

                        if (
                            nombre.length < 3
                        ) {

                            alert(
                                "⚠️ Ingrese su nombre."
                            );

                            return;

                        }


                        /*==============================
                          VALIDAR MENSAJE
                        ==============================*/

                        if (
                            mensaje.length === 0
                        ) {

                            alert(
                                "⚠️ Escriba un mensaje."
                            );

                            return;

                        }


                        /*==============================
                          VALIDAR TELÉFONO
                        ==============================*/

                        if (
                            !/^[0-9]{9}$/.test(
                                asesorTelefono
                            )
                        ) {

                            alert(
                                "⚠️ Número de WhatsApp inválido."
                            );

                            return;

                        }


                        /*==============================
                          CREAR MENSAJE
                        ==============================*/

                        const texto =
                            "Hola, soy " +
                            nombre +
                            ".\n\n" +
                            mensaje;


                        /*==============================
                          URL WHATSAPP
                        ==============================*/

                        const url =
                            "https://api.whatsapp.com/send?phone=51" +
                            asesorTelefono +
                            "&text=" +
                            encodeURIComponent(
                                texto
                            );


                        window.open(
                            url,
                            "_blank"
                        );

                    }
                );


            });

        }

    }
);