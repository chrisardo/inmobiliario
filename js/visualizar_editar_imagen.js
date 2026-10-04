/**
 * =========================================================
 * CoDevPro Technology
 * Archivo: js/visualizar_editar_imagen.js
 * Módulo: Propiedades
 * Función:
 *   - Visualizar imágenes
 *   - Agregar imágenes
 *   - Cambiar imágenes
 *   - Establecer imagen principal
 *   - Eliminar imágenes directamente
 * =========================================================
 */

document.addEventListener("DOMContentLoaded", function () {

    "use strict";


    /* =========================================================
       MODAL EDITAR PROPIEDAD
    ========================================================= */

    const modalEditar =
        document.getElementById("modalEditar");

    if (modalEditar) {

        modalEditar.addEventListener(
            "show.bs.modal",
            function (event) {

                const boton =
                    event.relatedTarget;

                if (!boton) {
                    return;
                }

                const obtenerDato =
                    function (nombre, defecto = "") {

                        return (
                            boton.getAttribute(nombre) ||
                            defecto
                        );
                    };


                const campoId =
                    document.getElementById("edit-id");

                const campoNombre =
                    document.getElementById("edit-nombre");

                const campoCodigo =
                    document.getElementById("edit-codigo");

                const campoPrecio =
                    document.getElementById("edit-precio");

                const campoPrecioAnterior =
                    document.getElementById(
                        "edit-precio-anterior"
                    );

                const campoCategoria =
                    document.getElementById("edit-categoria");

                const campoArea =
                    document.getElementById(
                        "edit-tamano-area-metros"
                    );

                const campoUbicacion =
                    document.getElementById(
                        "edit-ubicacion"
                    );


                if (campoId) {
                    campoId.value =
                        obtenerDato("data-id");
                }

                if (campoNombre) {
                    campoNombre.value =
                        obtenerDato("data-nombre");
                }

                if (campoCodigo) {
                    campoCodigo.value =
                        obtenerDato("data-codigo");
                }

                if (campoPrecio) {
                    campoPrecio.value =
                        obtenerDato("data-precio");
                }

                if (campoPrecioAnterior) {
                    campoPrecioAnterior.value =
                        obtenerDato(
                            "data-precio-anterior",
                            "0"
                        );
                }

                if (campoCategoria) {
                    campoCategoria.value =
                        obtenerDato("data-categoria");
                }

                if (campoArea) {
                    campoArea.value =
                        obtenerDato(
                            "data-tamano-area-metros"
                        );
                }

                if (campoUbicacion) {
                    campoUbicacion.value =
                        obtenerDato(
                            "data-ubicacion"
                        );
                }
            }
        );
    }


    /* =========================================================
       ELEMENTOS MODAL IMÁGENES
    ========================================================= */

    const modalImagenes =
        document.getElementById(
            "modalEditarImagenes"
        );

    if (!modalImagenes) {
        return;
    }


    const galeria =
        document.getElementById(
            "galeriaImagenesPropiedad"
        );

    const sinImagenes =
        document.getElementById(
            "sinImagenesPropiedad"
        );

    const cargando =
        document.getElementById(
            "cargandoImagenes"
        );

    const alerta =
        document.getElementById(
            "alertaImagenes"
        );

    const inputImagenes =
        document.getElementById(
            "inputImagenesPropiedad"
        );

    const btnSubir =
        document.getElementById(
            "btnSubirImagenes"
        );

    const btnSeleccionar =
        document.getElementById(
            "btnSeleccionarImagenes"
        );

    const btnAgregarImagenVacio =
        document.getElementById(
            "btnAgregarImagenVacio"
        );

    const idPropiedadInput =
        document.getElementById(
            "imagenes-id-propiedad"
        );

    const csrfInput =
        document.getElementById(
            "imagenes-csrf"
        );

    const nombrePropiedad =
        document.getElementById(
            "nombrePropiedadImagenes"
        );

    const contador =
        document.getElementById(
            "contadorImagenes"
        );

    const archivosSeleccionados =
        document.getElementById(
            "archivosSeleccionados"
        );

    const listaArchivosSeleccionados =
        document.getElementById(
            "listaArchivosSeleccionados"
        );


    /* =========================================================
       MODAL CAMBIAR IMAGEN
    ========================================================= */

    const modalCambiarImagen =
        document.getElementById(
            "modalCambiarImagen"
        );

    const inputCambiarImagen =
        document.getElementById(
            "inputCambiarImagen"
        );

    const idImagenCambiar =
        document.getElementById(
            "cambiar-id-imagen"
        );

    const vistaPreviaCambiarImagen =
        document.getElementById(
            "vistaPreviaCambiarImagen"
        );

    const btnConfirmarCambiarImagen =
        document.getElementById(
            "btnConfirmarCambiarImagen"
        );

    const alertaCambiarImagen =
        document.getElementById(
            "alertaCambiarImagen"
        );


    /* =========================================================
       URL CONTROLADOR
    ========================================================= */

    const URL_IMAGENES =
        "../controladores/editar_imagenes_propiedad.php";


    /* =========================================================
       CONFIGURACIÓN
    ========================================================= */

    const MAX_TAMANO_IMAGEN =
        2.7 * 1024 * 1024;

    const TIPOS_PERMITIDOS = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];


    /* =========================================================
       UTILIDADES
    ========================================================= */

    function escaparHTML(valor) {

        const div =
            document.createElement("div");

        div.textContent =
            valor ?? "";

        return div.innerHTML;
    }


    function obtenerIdPropiedad() {

        if (!idPropiedadInput) {
            return "";
        }

        return String(
            idPropiedadInput.value || ""
        ).trim();
    }


    function obtenerCSRF() {

        if (!csrfInput) {
            return "";
        }

        return String(
            csrfInput.value || ""
        ).trim();
    }


    /* =========================================================
       ALERTAS
    ========================================================= */

    function mostrarAlerta(
        mensaje,
        tipo = "success"
    ) {

        if (!alerta) {
            return;
        }

        alerta.className =
            "alert alert-" + tipo;

        alerta.textContent =
            mensaje || "";

        alerta.classList.remove(
            "d-none"
        );
    }


    function ocultarAlerta() {

        if (!alerta) {
            return;
        }

        alerta.className =
            "alert d-none";

        alerta.textContent =
            "";
    }


    function mostrarAlertaCambiar(
        mensaje,
        tipo = "danger"
    ) {

        if (!alertaCambiarImagen) {
            return;
        }

        alertaCambiarImagen.className =
            "alert alert-" + tipo;

        alertaCambiarImagen.textContent =
            mensaje || "";

        alertaCambiarImagen.classList.remove(
            "d-none"
        );
    }


    function ocultarAlertaCambiar() {

        if (!alertaCambiarImagen) {
            return;
        }

        alertaCambiarImagen.className =
            "alert d-none";

        alertaCambiarImagen.textContent =
            "";
    }


    /* =========================================================
       PROCESAR RESPUESTA JSON
    ========================================================= */

    async function procesarRespuesta(
        respuesta
    ) {

        const texto =
            await respuesta.text();

        let datos;

        try {

            datos =
                JSON.parse(texto);

        } catch (error) {

            console.error(
                "Respuesta no válida del servidor:",
                texto
            );

            throw new Error(
                "El servidor no devolvió una respuesta JSON válida."
            );
        }


        if (
            !respuesta.ok ||
            !datos ||
            datos.ok !== true
        ) {

            throw new Error(
                datos &&
                datos.mensaje
                    ? datos.mensaje
                    : "La operación no pudo completarse."
            );
        }


        return datos;
    }


    /* =========================================================
       ACTUALIZAR MINIATURA DE LA TABLA
    ========================================================= */

    function actualizarMiniaturaTabla(
        datos
    ) {

        if (!datos) {
            return;
        }


        const idPropiedad =
            parseInt(
                datos.id_propiedad ||
                obtenerIdPropiedad() ||
                0,
                10
            );


        if (!idPropiedad) {
            return;
        }


        const thumbnail =
            document.querySelector(
                '[data-property-thumbnail="' +
                idPropiedad +
                '"]'
            );


        if (!thumbnail) {
            return;
        }


        const totalImagenes =
            parseInt(
                datos.total_imagenes || 0,
                10
            );


        const contadorTabla =
            thumbnail.querySelector(
                '[data-image-count="' +
                idPropiedad +
                '"]'
            );


        if (contadorTabla) {

            const numero =
                contadorTabla.querySelector(
                    ".image-count-number"
                );


            if (numero) {

                numero.textContent =
                    totalImagenes;
            }


            if (totalImagenes > 0) {

                contadorTabla.classList.remove(
                    "d-none"
                );

            } else {

                contadorTabla.classList.add(
                    "d-none"
                );
            }
        }


        const imagenPrincipal =
            datos.imagen_principal || "";


        if (imagenPrincipal) {

            let img =
                thumbnail.querySelector(
                    ".property-main-image"
                );


            const noImage =
                thumbnail.querySelector(
                    ".property-no-image"
                );


            if (!img) {

                img =
                    document.createElement(
                        "img"
                    );

                img.className =
                    "property-main-image";

                img.loading =
                    "lazy";

                img.alt =
                    "Imagen de propiedad";

                thumbnail.prepend(img);
            }


            img.src =
                imagenPrincipal;

            img.style.display =
                "block";


            if (noImage) {
                noImage.remove();
            }

        } else {

            const img =
                thumbnail.querySelector(
                    ".property-main-image"
                );


            if (img) {
                img.remove();
            }


            if (
                !thumbnail.querySelector(
                    ".property-no-image"
                )
            ) {

                const noImage =
                    document.createElement(
                        "div"
                    );

                noImage.className =
                    "property-no-image";

                noImage.innerHTML =
                    '<i class="fa-regular fa-image"></i>';

                thumbnail.prepend(
                    noImage
                );
            }
        }
    }


    /* =========================================================
       ESTADO SIN IMÁGENES
    ========================================================= */

    function mostrarEstadoSinImagenes() {

        if (galeria) {
            galeria.innerHTML = "";
        }


        if (sinImagenes) {

            sinImagenes.classList.remove(
                "d-none"
            );
        }


        if (contador) {

            contador.textContent =
                "0 imágenes";
        }
    }


    function ocultarEstadoSinImagenes() {

        if (sinImagenes) {

            sinImagenes.classList.add(
                "d-none"
            );
        }
    }


    /* =========================================================
       ARCHIVOS SELECCIONADOS
    ========================================================= */

    function actualizarArchivosSeleccionados() {

        if (
            !archivosSeleccionados ||
            !listaArchivosSeleccionados
        ) {
            return;
        }


        listaArchivosSeleccionados.innerHTML =
            "";


        if (
            !inputImagenes ||
            !inputImagenes.files ||
            inputImagenes.files.length === 0
        ) {

            archivosSeleccionados.classList.add(
                "d-none"
            );

            return;
        }


        archivosSeleccionados.classList.remove(
            "d-none"
        );


        Array.from(
            inputImagenes.files
        ).forEach(
            function (archivo) {

                const badge =
                    document.createElement(
                        "span"
                    );

                badge.className =
                    "badge bg-light text-dark border p-2";

                badge.innerHTML =
                    '<i class="fa-regular fa-image text-success me-1"></i>' +
                    escaparHTML(
                        archivo.name
                    );

                listaArchivosSeleccionados.appendChild(
                    badge
                );
            }
        );
    }


    /* =========================================================
       VALIDAR VARIAS IMÁGENES
    ========================================================= */

    function validarArchivos() {

        if (
            !inputImagenes ||
            !inputImagenes.files ||
            inputImagenes.files.length === 0
        ) {

            return {
                ok: false,
                mensaje:
                    "Selecciona al menos una imagen."
            };
        }


        for (
            let i = 0;
            i < inputImagenes.files.length;
            i++
        ) {

            const archivo =
                inputImagenes.files[i];


            if (
                !TIPOS_PERMITIDOS.includes(
                    archivo.type
                )
            ) {

                return {
                    ok: false,
                    mensaje:
                        '"' +
                        archivo.name +
                        '" no tiene un formato permitido. Usa JPG, JPEG, PNG o WEBP.'
                };
            }


            if (
                archivo.size >
                MAX_TAMANO_IMAGEN
            ) {

                return {
                    ok: false,
                    mensaje:
                        '"' +
                        archivo.name +
                        '" supera el límite de 2.7 MB.'
                };
            }
        }


        return {
            ok: true
        };
    }


    /* =========================================================
       VALIDAR UNA IMAGEN
    ========================================================= */

    function validarUnaImagen(
        archivo
    ) {

        if (!archivo) {

            return {
                ok: false,
                mensaje:
                    "Selecciona una imagen."
            };
        }


        if (
            !TIPOS_PERMITIDOS.includes(
                archivo.type
            )
        ) {

            return {
                ok: false,
                mensaje:
                    "El formato seleccionado no es válido. Usa JPG, JPEG, PNG o WEBP."
            };
        }


        if (
            archivo.size >
            MAX_TAMANO_IMAGEN
        ) {

            return {
                ok: false,
                mensaje:
                    "La imagen supera el límite de 2.7 MB."
            };
        }


        return {
            ok: true
        };
    }


    /* =========================================================
       CARGAR IMÁGENES
    ========================================================= */

    async function cargarImagenes() {

        const idPropiedad =
            obtenerIdPropiedad();


        if (!idPropiedad) {

            mostrarAlerta(
                "No se recibió el ID de la propiedad.",
                "danger"
            );

            return;
        }


        ocultarAlerta();
        ocultarEstadoSinImagenes();


        if (galeria) {
            galeria.innerHTML = "";
        }


        if (cargando) {

            cargando.classList.remove(
                "d-none"
            );
        }


        try {

            const url =
                URL_IMAGENES +
                "?accion=listar" +
                "&id_propiedad=" +
                encodeURIComponent(
                    idPropiedad
                );


            const respuesta =
                await fetch(
                    url,
                    {
                        method: "GET",

                        headers: {
                            "X-Requested-With":
                                "XMLHttpRequest",

                            "Accept":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        cache:
                            "no-store"
                    }
                );


            const datos =
                await procesarRespuesta(
                    respuesta
                );


            const imagenes =
                Array.isArray(
                    datos.imagenes
                )
                    ? datos.imagenes
                    : [];


            if (cargando) {

                cargando.classList.add(
                    "d-none"
                );
            }


            actualizarMiniaturaTabla({

                id_propiedad:
                    idPropiedad,

                total_imagenes:
                    datos.total_imagenes ??
                    imagenes.length,

                imagen_principal:
                    datos.imagen_principal ||
                    (
                        imagenes.length > 0
                            ? imagenes[0].imagen
                            : ""
                    )
            });


            if (imagenes.length === 0) {

                mostrarEstadoSinImagenes();

                return;
            }


            ocultarEstadoSinImagenes();


            if (contador) {

                contador.textContent =
                    imagenes.length === 1
                        ? "1 imagen"
                        : imagenes.length +
                          " imágenes";
            }


            imagenes.forEach(
                function (
                    imagen,
                    indice
                ) {

                    crearTarjetaImagen(
                        imagen,
                        indice
                    );
                }
            );

        } catch (error) {

            console.error(
                "Error al cargar imágenes:",
                error
            );


            if (cargando) {

                cargando.classList.add(
                    "d-none"
                );
            }


            if (galeria) {
                galeria.innerHTML = "";
            }


            mostrarAlerta(
                error.message ||
                "Ocurrió un error al cargar las imágenes.",
                "danger"
            );
        }
    }


    /* =========================================================
       CREAR TARJETA DE IMAGEN
    ========================================================= */

    function crearTarjetaImagen(
        imagen,
        indice
    ) {

        if (
            !imagen ||
            !imagen.id_imagen
        ) {
            return;
        }


        const col =
            document.createElement(
                "div"
            );

        col.className =
            "col-12 col-sm-6 col-md-6 col-lg-4";


        const card =
            document.createElement(
                "div"
            );

        card.className =
            "card h-100 border-0 shadow-sm overflow-hidden";

        card.style.borderRadius =
            "16px";


        const imagenWrapper =
            document.createElement(
                "div"
            );

        imagenWrapper.className =
            "position-relative bg-light";


        const img =
            document.createElement(
                "img"
            );

        img.src =
            imagen.imagen || "";

        img.alt =
            "Imagen de propiedad";

        img.className =
            "w-100";

        img.style.height =
            "230px";

        img.style.objectFit =
            "cover";

        img.loading =
            "lazy";


        img.addEventListener(
            "error",
            function () {

                this.style.objectFit =
                    "contain";

                this.style.padding =
                    "20px";
            }
        );


        imagenWrapper.appendChild(
            img
        );


        /* =====================================================
           PRINCIPAL
        ===================================================== */

        if (indice === 0) {

            const badge =
                document.createElement(
                    "span"
                );

            badge.className =
                "badge bg-success position-absolute top-0 start-0 m-2";

            badge.innerHTML =
                '<i class="fa-solid fa-star me-1"></i>' +
                "Principal";

            imagenWrapper.appendChild(
                badge
            );
        }


        /* =====================================================
           ORDEN
        ===================================================== */

        const ordenBadge =
            document.createElement(
                "span"
            );

        ordenBadge.className =
            "badge bg-dark position-absolute bottom-0 end-0 m-2";

        ordenBadge.textContent =
            "#" +
            (indice + 1);

        imagenWrapper.appendChild(
            ordenBadge
        );


        card.appendChild(
            imagenWrapper
        );


        /* =====================================================
           BODY
        ===================================================== */

        const body =
            document.createElement(
                "div"
            );

        body.className =
            "card-body";


        const titulo =
            document.createElement(
                "div"
            );

        titulo.className =
            "fw-semibold mb-3";

        titulo.innerHTML =
            '<i class="fa-regular fa-image text-success me-1"></i>' +
            "Imagen #" +
            (indice + 1);


        body.appendChild(
            titulo
        );


        /* =====================================================
           CAMBIAR IMAGEN
        ===================================================== */

        const btnCambiar =
            document.createElement(
                "button"
            );

        btnCambiar.type =
            "button";

        btnCambiar.className =
            "btn btn-sm btn-outline-primary w-100 mb-2";

        btnCambiar.innerHTML =
            '<i class="fa-solid fa-pen-to-square me-1"></i>' +
            "Cambiar imagen";


        btnCambiar.addEventListener(
            "click",
            function () {

                abrirModalCambiarImagen(
                    imagen.id_imagen,
                    imagen.imagen
                );
            }
        );


        body.appendChild(
            btnCambiar
        );


        /* =====================================================
           ESTABLECER COMO PRINCIPAL
        ===================================================== */

        if (indice !== 0) {

            const btnPrincipal =
                document.createElement(
                    "button"
                );

            btnPrincipal.type =
                "button";

            btnPrincipal.className =
                "btn btn-sm btn-outline-success w-100 mb-2";

            btnPrincipal.innerHTML =
                '<i class="fa-solid fa-star me-1"></i>' +
                "Usar como principal";


            btnPrincipal.addEventListener(
                "click",
                function () {

                    establecerPrincipal(
                        imagen.id_imagen,
                        btnPrincipal
                    );
                }
            );


            body.appendChild(
                btnPrincipal
            );
        }


        /* =====================================================
           ELIMINAR DIRECTAMENTE
        ===================================================== */

        const btnEliminar =
            document.createElement(
                "button"
            );

        btnEliminar.type =
            "button";

        btnEliminar.className =
            "btn btn-sm btn-outline-danger w-100";

        btnEliminar.innerHTML =
            '<i class="fa-solid fa-trash me-1"></i>' +
            "Eliminar";


        btnEliminar.addEventListener(
            "click",
            function () {

                eliminarImagen(
                    imagen.id_imagen,
                    btnEliminar,
                    col
                );
            }
        );


        body.appendChild(
            btnEliminar
        );


        card.appendChild(
            body
        );

        col.appendChild(
            card
        );


        if (galeria) {

            galeria.appendChild(
                col
            );
        }
    }


    /* =========================================================
       ELIMINAR IMAGEN DIRECTAMENTE
       
       NO EXISTE MODAL DE CONFIRMACIÓN.
       
       Al hacer click:
       1. Deshabilita el botón.
       2. Envía accion=eliminar.
       3. El PHP elimina de MySQL.
       4. Recarga la galería.
    ========================================================= */

    async function eliminarImagen(
        idImagen,
        boton,
        tarjeta
    ) {

        const id =
            parseInt(
                idImagen,
                10
            );


        const idPropiedad =
            parseInt(
                obtenerIdPropiedad(),
                10
            );


        /* =====================================================
           VALIDAR ID IMAGEN
        ===================================================== */

        if (
            !Number.isInteger(id) ||
            id <= 0
        ) {

            mostrarAlerta(
                "La imagen seleccionada no es válida.",
                "danger"
            );

            return;
        }


        /* =====================================================
           VALIDAR PROPIEDAD
        ===================================================== */

        if (
            !Number.isInteger(
                idPropiedad
            ) ||
            idPropiedad <= 0
        ) {

            mostrarAlerta(
                "No se identificó correctamente la propiedad.",
                "danger"
            );

            return;
        }


        /* =====================================================
           CSRF
        ===================================================== */

        const csrf =
            obtenerCSRF();


        if (!csrf) {

            mostrarAlerta(
                "El token de seguridad no está disponible. Recarga la página.",
                "danger"
            );

            return;
        }


        /* =====================================================
           DESHABILITAR BOTÓN
        ===================================================== */

        let textoOriginal =
            "";


        if (boton) {

            textoOriginal =
                boton.innerHTML;

            boton.disabled =
                true;

            boton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span>' +
                "Eliminando...";
        }


        /* =====================================================
           FORMDATA
        ===================================================== */

        const formData =
            new FormData();


        formData.append(
            "accion",
            "eliminar"
        );

        formData.append(
            "id_propiedad",
            String(idPropiedad)
        );

        formData.append(
            "id_imagen",
            String(id)
        );

        formData.append(
            "csrf_token",
            csrf
        );


        try {

            console.log(
                "Eliminando imagen:",
                {
                    accion: "eliminar",
                    id_propiedad:
                        idPropiedad,
                    id_imagen:
                        id
                }
            );


            const respuesta =
                await fetch(
                    URL_IMAGENES,
                    {
                        method: "POST",

                        body:
                            formData,

                        credentials:
                            "same-origin",

                        headers: {
                            "X-Requested-With":
                                "XMLHttpRequest",

                            "Accept":
                                "application/json"
                        },

                        cache:
                            "no-store"
                    }
                );


            const datos =
                await procesarRespuesta(
                    respuesta
                );


            /* =================================================
               ACTUALIZAR MINIATURA DE LA TABLA
            ================================================= */

            actualizarMiniaturaTabla(
                datos
            );


            /* =================================================
               QUITAR TARJETA INMEDIATAMENTE
            ================================================= */

            if (tarjeta) {

                tarjeta.remove();
            }


            /* =================================================
               RECARGAR GALERÍA DESDE LA BASE DE DATOS
               
               Esto garantiza que:
               - el orden sea correcto
               - la principal se actualice
               - el contador sea correcto
            ================================================= */

            await cargarImagenes();


            /* =================================================
               MENSAJE
            ================================================= */

            mostrarAlerta(
                datos.mensaje ||
                "Imagen eliminada correctamente.",
                "success"
            );

        } catch (error) {

            console.error(
                "Error al eliminar imagen:",
                error
            );


            mostrarAlerta(
                error.message ||
                "Ocurrió un error al eliminar la imagen.",
                "danger"
            );


            /* =================================================
               RESTAURAR BOTÓN SI FALLÓ
            ================================================= */

            if (boton) {

                boton.disabled =
                    false;

                boton.innerHTML =
                    textoOriginal;
            }
        }
    }


    /* =========================================================
       SELECCIONAR IMÁGENES
    ========================================================= */

    if (btnSeleccionar) {

        btnSeleccionar.addEventListener(
            "click",
            function () {

                if (inputImagenes) {

                    inputImagenes.click();
                }
            }
        );
    }


    if (btnAgregarImagenVacio) {

        btnAgregarImagenVacio.addEventListener(
            "click",
            function () {

                if (inputImagenes) {

                    inputImagenes.click();
                }
            }
        );
    }


    if (inputImagenes) {

        inputImagenes.addEventListener(
            "change",
            function () {

                actualizarArchivosSeleccionados();
            }
        );
    }


    /* =========================================================
       SUBIR NUEVAS IMÁGENES
    ========================================================= */

    if (btnSubir) {

        btnSubir.addEventListener(
            "click",
            async function () {

                ocultarAlerta();


                const validacion =
                    validarArchivos();


                if (!validacion.ok) {

                    mostrarAlerta(
                        validacion.mensaje,
                        "warning"
                    );

                    return;
                }


                const idPropiedad =
                    parseInt(
                        obtenerIdPropiedad(),
                        10
                    );


                if (
                    !Number.isInteger(
                        idPropiedad
                    ) ||
                    idPropiedad <= 0
                ) {

                    mostrarAlerta(
                        "No se ha seleccionado una propiedad válida.",
                        "danger"
                    );

                    return;
                }


                const csrf =
                    obtenerCSRF();


                if (!csrf) {

                    mostrarAlerta(
                        "El token de seguridad no está disponible. Recarga la página.",
                        "danger"
                    );

                    return;
                }


                const formData =
                    new FormData();


                formData.append(
                    "accion",
                    "subir"
                );

                formData.append(
                    "id_propiedad",
                    String(idPropiedad)
                );

                formData.append(
                    "csrf_token",
                    csrf
                );


                for (
                    let i = 0;
                    i < inputImagenes.files.length;
                    i++
                ) {

                    formData.append(
                        "imagenes[]",
                        inputImagenes.files[i]
                    );
                }


                const textoOriginal =
                    btnSubir.innerHTML;


                btnSubir.disabled =
                    true;


                if (inputImagenes) {

                    inputImagenes.disabled =
                        true;
                }


                if (btnSeleccionar) {

                    btnSeleccionar.disabled =
                        true;
                }


                btnSubir.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>' +
                    "Agregando...";


                try {

                    const respuesta =
                        await fetch(
                            URL_IMAGENES,
                            {
                                method: "POST",

                                body:
                                    formData,

                                credentials:
                                    "same-origin",

                                headers: {
                                    "X-Requested-With":
                                        "XMLHttpRequest",

                                    "Accept":
                                        "application/json"
                                },

                                cache:
                                    "no-store"
                            }
                        );


                    const datos =
                        await procesarRespuesta(
                            respuesta
                        );


                    inputImagenes.value =
                        "";


                    actualizarArchivosSeleccionados();


                    actualizarMiniaturaTabla(
                        datos
                    );


                    await cargarImagenes();


                    mostrarAlerta(
                        datos.mensaje ||
                        "Las imágenes se agregaron correctamente.",
                        "success"
                    );

                } catch (error) {

                    console.error(
                        "Error al subir imágenes:",
                        error
                    );


                    mostrarAlerta(
                        error.message ||
                        "Ocurrió un error al agregar las imágenes.",
                        "danger"
                    );

                } finally {

                    btnSubir.disabled =
                        false;


                    if (inputImagenes) {

                        inputImagenes.disabled =
                            false;
                    }


                    if (btnSeleccionar) {

                        btnSeleccionar.disabled =
                            false;
                    }


                    btnSubir.innerHTML =
                        textoOriginal;
                }
            }
        );
    }


    /* =========================================================
       ABRIR MODAL CAMBIAR IMAGEN
    ========================================================= */

    function abrirModalCambiarImagen(
        idImagen,
        urlImagen
    ) {

        if (
            !modalCambiarImagen ||
            !idImagen
        ) {
            return;
        }


        if (idImagenCambiar) {

            idImagenCambiar.value =
                String(idImagen);
        }


        if (inputCambiarImagen) {

            inputCambiarImagen.value =
                "";
        }


        ocultarAlertaCambiar();


        if (vistaPreviaCambiarImagen) {

            vistaPreviaCambiarImagen.src =
                urlImagen || "";

            vistaPreviaCambiarImagen.style.objectFit =
                "cover";

            vistaPreviaCambiarImagen.style.padding =
                "0";
        }


        const instancia =
            bootstrap.Modal.getOrCreateInstance(
                modalCambiarImagen
            );


        instancia.show();
    }


    /* =========================================================
       PREVISUALIZAR CAMBIO
    ========================================================= */

    if (inputCambiarImagen) {

        inputCambiarImagen.addEventListener(
            "change",
            function () {

                const archivo =
                    this.files &&
                    this.files[0];


                if (!archivo) {
                    return;
                }


                const validacion =
                    validarUnaImagen(
                        archivo
                    );


                if (!validacion.ok) {

                    mostrarAlertaCambiar(
                        validacion.mensaje,
                        "warning"
                    );


                    this.value =
                        "";


                    return;
                }


                ocultarAlertaCambiar();


                const lector =
                    new FileReader();


                lector.onload =
                    function (evento) {

                        if (
                            vistaPreviaCambiarImagen
                        ) {

                            vistaPreviaCambiarImagen.src =
                                evento.target.result;

                            vistaPreviaCambiarImagen.style.objectFit =
                                "cover";

                            vistaPreviaCambiarImagen.style.padding =
                                "0";
                        }
                    };


                lector.onerror =
                    function () {

                        mostrarAlertaCambiar(
                            "No se pudo leer la imagen seleccionada.",
                            "danger"
                        );
                    };


                lector.readAsDataURL(
                    archivo
                );
            }
        );
    }


    /* =========================================================
       CONFIRMAR CAMBIO
    ========================================================= */

    if (btnConfirmarCambiarImagen) {

        btnConfirmarCambiarImagen.addEventListener(
            "click",
            async function () {

                ocultarAlertaCambiar();


                const idImagen =
                    idImagenCambiar
                        ? parseInt(
                            idImagenCambiar.value,
                            10
                        )
                        : 0;


                const idPropiedad =
                    parseInt(
                        obtenerIdPropiedad(),
                        10
                    );


                const archivo =
                    inputCambiarImagen &&
                    inputCambiarImagen.files &&
                    inputCambiarImagen.files.length > 0
                        ? inputCambiarImagen.files[0]
                        : null;


                if (
                    !Number.isInteger(
                        idImagen
                    ) ||
                    idImagen <= 0
                ) {

                    mostrarAlertaCambiar(
                        "No se identificó la imagen que deseas cambiar."
                    );

                    return;
                }


                if (
                    !Number.isInteger(
                        idPropiedad
                    ) ||
                    idPropiedad <= 0
                ) {

                    mostrarAlertaCambiar(
                        "No se identificó la propiedad."
                    );

                    return;
                }


                const validacion =
                    validarUnaImagen(
                        archivo
                    );


                if (!validacion.ok) {

                    mostrarAlertaCambiar(
                        validacion.mensaje
                    );

                    return;
                }


                const csrf =
                    obtenerCSRF();


                if (!csrf) {

                    mostrarAlertaCambiar(
                        "El token de seguridad no está disponible. Recarga la página."
                    );

                    return;
                }


                const formData =
                    new FormData();


                formData.append(
                    "accion",
                    "cambiar"
                );

                formData.append(
                    "id_propiedad",
                    String(idPropiedad)
                );

                formData.append(
                    "id_imagen",
                    String(idImagen)
                );

                formData.append(
                    "csrf_token",
                    csrf
                );

                formData.append(
                    "imagen",
                    archivo
                );


                const textoOriginal =
                    btnConfirmarCambiarImagen.innerHTML;


                btnConfirmarCambiarImagen.disabled =
                    true;


                if (inputCambiarImagen) {

                    inputCambiarImagen.disabled =
                        true;
                }


                btnConfirmarCambiarImagen.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>' +
                    "Cambiando...";


                try {

                    const respuesta =
                        await fetch(
                            URL_IMAGENES,
                            {
                                method: "POST",

                                body:
                                    formData,

                                credentials:
                                    "same-origin",

                                headers: {
                                    "X-Requested-With":
                                        "XMLHttpRequest",

                                    "Accept":
                                        "application/json"
                                },

                                cache:
                                    "no-store"
                            }
                        );


                    const datos =
                        await procesarRespuesta(
                            respuesta
                        );


                    actualizarMiniaturaTabla(
                        datos
                    );


                    const instancia =
                        bootstrap.Modal.getInstance(
                            modalCambiarImagen
                        );


                    if (instancia) {

                        instancia.hide();
                    }


                    await cargarImagenes();


                    mostrarAlerta(
                        datos.mensaje ||
                        "La imagen fue cambiada correctamente.",
                        "success"
                    );

                } catch (error) {

                    console.error(
                        "Error al cambiar imagen:",
                        error
                    );


                    mostrarAlertaCambiar(
                        error.message ||
                        "Ocurrió un error al cambiar la imagen.",
                        "danger"
                    );

                } finally {

                    btnConfirmarCambiarImagen.disabled =
                        false;


                    if (inputCambiarImagen) {

                        inputCambiarImagen.disabled =
                            false;
                    }


                    btnConfirmarCambiarImagen.innerHTML =
                        textoOriginal;
                }
            }
        );
    }


    /* =========================================================
       ESTABLECER IMAGEN PRINCIPAL
    ========================================================= */

    async function establecerPrincipal(
        idImagen,
        boton
    ) {

        const id =
            parseInt(
                idImagen,
                10
            );


        const idPropiedad =
            parseInt(
                obtenerIdPropiedad(),
                10
            );


        if (
            !Number.isInteger(id) ||
            id <= 0
        ) {

            mostrarAlerta(
                "La imagen seleccionada no es válida.",
                "danger"
            );

            return;
        }


        if (
            !Number.isInteger(
                idPropiedad
            ) ||
            idPropiedad <= 0
        ) {

            mostrarAlerta(
                "No se identificó la propiedad.",
                "danger"
            );

            return;
        }


        const csrf =
            obtenerCSRF();


        if (!csrf) {

            mostrarAlerta(
                "El token de seguridad no está disponible. Recarga la página.",
                "danger"
            );

            return;
        }


        if (boton) {

            boton.disabled =
                true;

            boton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span>' +
                "Actualizando...";
        }


        const formData =
            new FormData();


        formData.append(
            "accion",
            "principal"
        );

        formData.append(
            "id_propiedad",
            String(idPropiedad)
        );

        formData.append(
            "id_imagen",
            String(id)
        );

        formData.append(
            "csrf_token",
            csrf
        );


        try {

            const respuesta =
                await fetch(
                    URL_IMAGENES,
                    {
                        method: "POST",

                        body:
                            formData,

                        credentials:
                            "same-origin",

                        headers: {
                            "X-Requested-With":
                                "XMLHttpRequest",

                            "Accept":
                                "application/json"
                        },

                        cache:
                            "no-store"
                    }
                );


            const datos =
                await procesarRespuesta(
                    respuesta
                );


            actualizarMiniaturaTabla(
                datos
            );


            await cargarImagenes();


            mostrarAlerta(
                datos.mensaje ||
                "La imagen principal fue actualizada correctamente.",
                "success"
            );

        } catch (error) {

            console.error(
                "Error al establecer imagen principal:",
                error
            );


            mostrarAlerta(
                error.message ||
                "Ocurrió un error al cambiar la imagen principal.",
                "danger"
            );

        } finally {

            if (boton) {

                boton.disabled =
                    false;

                boton.innerHTML =
                    '<i class="fa-solid fa-star me-1"></i>' +
                    "Usar como principal";
            }
        }
    }


    /* =========================================================
       ABRIR MODAL DE IMÁGENES
    ========================================================= */

    modalImagenes.addEventListener(
        "show.bs.modal",
        function (event) {

            const boton =
                event.relatedTarget;


            if (!boton) {
                return;
            }


            const id =
                boton.getAttribute(
                    "data-id"
                ) || "";


            const nombre =
                boton.getAttribute(
                    "data-nombre"
                ) || "";


            if (idPropiedadInput) {

                idPropiedadInput.value =
                    id;
            }


            if (nombrePropiedad) {

                nombrePropiedad.textContent =
                    nombre ||
                    "Propiedad";
            }


            if (contador) {

                contador.textContent =
                    "Cargando...";
            }


            if (inputImagenes) {

                inputImagenes.value =
                    "";
            }


            actualizarArchivosSeleccionados();


            if (galeria) {

                galeria.innerHTML =
                    "";
            }


            ocultarEstadoSinImagenes();
            ocultarAlerta();


            if (!id) {

                mostrarAlerta(
                    "No se pudo identificar la propiedad seleccionada.",
                    "danger"
                );

                return;
            }


            cargarImagenes();
        }
    );


    /* =========================================================
       CERRAR MODAL PRINCIPAL
    ========================================================= */

    modalImagenes.addEventListener(
        "hidden.bs.modal",
        function () {

            if (galeria) {

                galeria.innerHTML =
                    "";
            }


            ocultarEstadoSinImagenes();


            if (cargando) {

                cargando.classList.add(
                    "d-none"
                );
            }


            ocultarAlerta();


            if (inputImagenes) {

                inputImagenes.value =
                    "";

                inputImagenes.disabled =
                    false;
            }


            if (btnSubir) {

                btnSubir.disabled =
                    false;

                btnSubir.innerHTML =
                    '<i class="fa-solid fa-cloud-arrow-up me-1"></i>' +
                    "Agregar imagen";
            }


            if (btnSeleccionar) {

                btnSeleccionar.disabled =
                    false;
            }


            if (idPropiedadInput) {

                idPropiedadInput.value =
                    "";
            }


            if (nombrePropiedad) {

                nombrePropiedad.textContent =
                    "Propiedad";
            }


            if (contador) {

                contador.textContent =
                    "0 imágenes";
            }


            actualizarArchivosSeleccionados();
        }
    );


    /* =========================================================
       CERRAR MODAL CAMBIAR
    ========================================================= */

    if (modalCambiarImagen) {

        modalCambiarImagen.addEventListener(
            "hidden.bs.modal",
            function () {

                if (inputCambiarImagen) {

                    inputCambiarImagen.value =
                        "";

                    inputCambiarImagen.disabled =
                        false;
                }


                if (idImagenCambiar) {

                    idImagenCambiar.value =
                        "";
                }


                if (vistaPreviaCambiarImagen) {

                    vistaPreviaCambiarImagen.src =
                        "";

                    vistaPreviaCambiarImagen.style.objectFit =
                        "cover";

                    vistaPreviaCambiarImagen.style.padding =
                        "0";
                }


                if (btnConfirmarCambiarImagen) {

                    btnConfirmarCambiarImagen.disabled =
                        false;

                    btnConfirmarCambiarImagen.innerHTML =
                        '<i class="fa-solid fa-repeat me-1"></i>' +
                        "Cambiar imagen";
                }


                ocultarAlertaCambiar();
            }
        );
    }

});