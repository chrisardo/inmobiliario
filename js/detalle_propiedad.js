//======================================================
// CoDevPro Technology
// Archivo: js/detalle_propiedad.js
// Módulo: Propiedades
// Sistema: Inmobiliario
//======================================================

"use strict";


//======================================================
// CONFIGURACIÓN
//======================================================

const DETALLE_PROPIEDAD = {

    url: "ajax/obtener_detalle_propiedad.php",

    modalId: "modalDetallePropiedad",

    elementos: {

        codigo: "detalleCodigo",

        imagen: "detalleImagen",

        sinImagen: "detalleSinImagen",

        contador: "detalleImagenContador",

        numeroImagen: "detalleImagenNumero",

        totalImagenes: "detalleImagenTotal",

        anterior: "detalleImagenAnterior",

        siguiente: "detalleImagenSiguiente",

        miniaturas: "detalleMiniaturas",

        precio: "detallePrecio",

        categoria: "detalleCategoria",

        nombre: "detalleNombre",

        ubicacion: "detalleUbicacion",

        area: "detalleArea",

        tipo: "detalleTipo",

        precioGrande: "detallePrecioGrande",

        precioAnteriorBox: "detallePrecioAnteriorBox",

        precioAnterior: "detallePrecioAnterior",

        whatsapp: "detalleWhatsapp"

    }

};


//======================================================
// VARIABLES GLOBALES
//======================================================

let modalDetallePropiedad = null;

let imagenesPropiedad = [];

let indiceImagenActual = 0;


//======================================================
// INICIALIZAR
//======================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        inicializarDetallePropiedad();

    }
);


//======================================================
// INICIALIZAR DETALLE
//======================================================

function inicializarDetallePropiedad() {

    const modalElemento =
        document.getElementById(
            DETALLE_PROPIEDAD.modalId
        );


    if (!modalElemento) {

        console.error(
            "No se encontró el modal #" +
            DETALLE_PROPIEDAD.modalId
        );

        return;

    }


    //==================================================
    // INSTANCIA BOOTSTRAP
    //==================================================

    if (
        typeof bootstrap !== "undefined" &&
        bootstrap.Modal
    ) {

        modalDetallePropiedad =
            bootstrap.Modal.getOrCreateInstance(
                modalElemento
            );

    } else {

        console.error(
            "Bootstrap JS no está disponible."
        );

        return;

    }


    //==================================================
    // BOTÓN VER DETALLE
    //==================================================

    document.addEventListener(
        "click",
        function (evento) {

            const boton =
                evento.target.closest(
                    ".btnVerDetalle"
                );


            if (!boton) {

                return;

            }


            evento.preventDefault();


            const idPropiedad =
                boton.dataset.id ||
                boton.getAttribute(
                    "data-id"
                );


            if (
                !idPropiedad ||
                !/^\d+$/.test(
                    String(idPropiedad)
                )
            ) {

                console.error(
                    "ID de propiedad no válido:",
                    idPropiedad
                );


                mostrarErrorDetalle(
                    "No se pudo identificar la propiedad seleccionada."
                );


                modalDetallePropiedad.show();

                return;

            }


            abrirDetallePropiedad(
                parseInt(
                    idPropiedad,
                    10
                )
            );

        }
    );


    //==================================================
    // BOTÓN IMAGEN ANTERIOR
    //==================================================

    const botonAnterior =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.anterior
        );


    if (botonAnterior) {

        botonAnterior.addEventListener(
            "click",
            function (evento) {

                evento.preventDefault();

                evento.stopPropagation();

                cambiarImagen(
                    indiceImagenActual - 1
                );

            }
        );

    }


    //==================================================
    // BOTÓN IMAGEN SIGUIENTE
    //==================================================

    const botonSiguiente =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.siguiente
        );


    if (botonSiguiente) {

        botonSiguiente.addEventListener(
            "click",
            function (evento) {

                evento.preventDefault();

                evento.stopPropagation();

                cambiarImagen(
                    indiceImagenActual + 1
                );

            }
        );

    }


    //==================================================
    // TECLADO
    //==================================================

    document.addEventListener(
        "keydown",
        function (evento) {

            if (
                !document.body.classList.contains(
                    "modal-open"
                )
            ) {

                return;

            }


            if (
                !imagenesPropiedad.length
            ) {

                return;

            }


            if (
                evento.key === "ArrowLeft"
            ) {

                cambiarImagen(
                    indiceImagenActual - 1
                );

            }


            if (
                evento.key === "ArrowRight"
            ) {

                cambiarImagen(
                    indiceImagenActual + 1
                );

            }

        }
    );


    //==================================================
    // WHATSAPP
    //==================================================

    document.addEventListener(
        "click",
        function (evento) {

            const botonWhatsapp =
                evento.target.closest(
                    "#detalleWhatsapp"
                );


            if (!botonWhatsapp) {

                return;

            }


            if (
                botonWhatsapp.classList.contains(
                    "disabled"
                )
            ) {

                evento.preventDefault();

                return;

            }


            const url =
                botonWhatsapp.getAttribute(
                    "href"
                );


            if (
                !url ||
                url === "#" ||
                url === "javascript:void(0)"
            ) {

                evento.preventDefault();

                return;

            }


            if (
                url.indexOf(
                    "https://wa.me/"
                ) === 0
            ) {

                evento.preventDefault();


                window.open(
                    url,
                    "_blank",
                    "noopener,noreferrer"
                );

            }

        }
    );


    //==================================================
    // LIMPIAR AL CERRAR
    //==================================================

    modalElemento.addEventListener(
        "hidden.bs.modal",
        function () {

            limpiarDetalle();

        }
    );

}


//======================================================
// ABRIR DETALLE
//======================================================

async function abrirDetallePropiedad(
    idPropiedad
) {

    if (!modalDetallePropiedad) {

        return;

    }


    modalDetallePropiedad.show();


    mostrarCargandoDetalle();


    const url =
        DETALLE_PROPIEDAD.url +
        "?id_propiedad=" +
        encodeURIComponent(
            idPropiedad
        );


    try {

        const respuesta =
            await fetch(
                url,
                {
                    method: "GET",

                    headers: {
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },

                    cache: "no-store"
                }
            );


        if (!respuesta.ok) {

            throw new Error(
                "Error HTTP " +
                respuesta.status
            );

        }


        const texto =
            await respuesta.text();


        if (!texto.trim()) {

            throw new Error(
                "El servidor no devolvió información."
            );

        }


        let datos;

        try {

            datos =
                JSON.parse(
                    texto
                );

        } catch (error) {

            console.error(
                "Respuesta recibida del servidor:",
                texto
            );


            throw new Error(
                "El servidor devolvió una respuesta no válida."
            );

        }


        if (
            !datos ||
            datos.success !== true
        ) {

            throw new Error(
                datos &&
                datos.mensaje
                    ? datos.mensaje
                    : "No se pudo obtener el detalle de la propiedad."
            );

        }


        mostrarDetalle(
            datos.propiedad
        );

    } catch (error) {

        console.error(
            "Error obteniendo detalle de propiedad:",
            error
        );


        mostrarErrorDetalle(
            error.message ||
            "Ocurrió un error al cargar la propiedad."
        );

    }

}


//======================================================
// MOSTRAR DETALLE
//======================================================

function mostrarDetalle(
    propiedad
) {

    if (!propiedad) {

        mostrarErrorDetalle(
            "No se encontraron datos de la propiedad."
        );

        return;

    }


    //==================================================
    // ELEMENTOS
    //==================================================

    const codigo =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.codigo
        );


    const precio =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precio
        );


    const categoria =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.categoria
        );


    const nombre =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.nombre
        );


    const ubicacion =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.ubicacion
        );


    const area =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.area
        );


    const tipo =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.tipo
        );


    const precioGrande =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioGrande
        );


    const precioAnteriorBox =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioAnteriorBox
        );


    const precioAnterior =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioAnterior
        );


    const whatsapp =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.whatsapp
        );


    //==================================================
    // CÓDIGO
    //==================================================

    if (codigo) {

        codigo.textContent =
            propiedad.codigo
                ? "Código: " + propiedad.codigo
                : "Código no disponible";

    }


    //==================================================
    // NOMBRE
    //==================================================

    if (nombre) {

        nombre.textContent =
            propiedad.nombre ||
            "Propiedad";

    }


    //==================================================
    // CATEGORÍA
    //==================================================

    if (categoria) {

        categoria.innerHTML =
            '<i class="bi bi-tag-fill"></i> ' +
            escaparHTML(
                propiedad.nombre_categoria ||
                "Propiedad"
            );

    }


    //==================================================
    // UBICACIÓN
    //==================================================

    if (ubicacion) {

        ubicacion.textContent =
            propiedad.ubicacion ||
            "Ubicación no disponible";

    }


    //==================================================
    // ÁREA
    //==================================================

    if (area) {

        const valorArea =
            parseFloat(
                propiedad.tamano_area_metros
            );


        if (
            Number.isFinite(valorArea) &&
            valorArea > 0
        ) {

            area.textContent =
                formatearNumero(
                    valorArea,
                    2
                ) +
                " m²";

        } else {

            area.textContent =
                "No disponible";

        }

    }


    //==================================================
    // TIPO
    //==================================================

    if (tipo) {

        tipo.textContent =
            propiedad.nombre_categoria ||
            "Propiedad";

    }


    //==================================================
    // PRECIO
    //==================================================

    const valorPrecio =
        parseFloat(
            propiedad.precio
        ) || 0;


    const precioFormateado =
        formatearPrecio(
            valorPrecio
        );


    if (precio) {

        precio.textContent =
            precioFormateado;

    }


    if (precioGrande) {

        precioGrande.textContent =
            precioFormateado;

    }


    //==================================================
    // PRECIO ANTERIOR
    //==================================================

    const valorPrecioAnterior =
        parseFloat(
            propiedad.precio_anterior
        ) || 0;


    if (
        valorPrecioAnterior > valorPrecio &&
        valorPrecio > 0
    ) {

        if (precioAnteriorBox) {

            precioAnteriorBox.classList.remove(
                "d-none"
            );

        }


        if (precioAnterior) {

            precioAnterior.textContent =
                formatearPrecio(
                    valorPrecioAnterior
                );

        }

    } else {

        if (precioAnteriorBox) {

            precioAnteriorBox.classList.add(
                "d-none"
            );

        }

    }


    //==================================================
    // IMÁGENES
    //==================================================

    mostrarGaleria(
        propiedad.imagenes
    );


    //==================================================
    // WHATSAPP
    //==================================================

    configurarWhatsapp(
        whatsapp,
        propiedad
    );

}


//======================================================
// MOSTRAR GALERÍA
//======================================================

function mostrarGaleria(
    imagenes
) {

    const imagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.imagen
        );


    const sinImagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.sinImagen
        );


    const contador =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.contador
        );


    const numeroImagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.numeroImagen
        );


    const totalImagenes =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.totalImagenes
        );


    const anterior =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.anterior
        );


    const siguiente =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.siguiente
        );


    const miniaturas =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.miniaturas
        );


    //==================================================
    // REINICIAR
    //==================================================

    imagenesPropiedad = [];

    indiceImagenActual = 0;


    if (miniaturas) {

        miniaturas.innerHTML = "";

        miniaturas.classList.add(
            "d-none"
        );

    }


    if (contador) {

        contador.classList.add(
            "d-none"
        );

    }


    if (anterior) {

        anterior.classList.add(
            "d-none"
        );

    }


    if (siguiente) {

        siguiente.classList.add(
            "d-none"
        );

    }


    //==================================================
    // VALIDAR IMÁGENES
    //==================================================

    if (
        !Array.isArray(imagenes) ||
        imagenes.length === 0
    ) {

        mostrarSinImagen();

        return;

    }


    //==================================================
    // FILTRAR IMÁGENES VÁLIDAS
    //==================================================

    imagenesPropiedad =
        imagenes.filter(
            function (imagenItem) {

                return (
                    imagenItem &&
                    typeof imagenItem.src === "string" &&
                    imagenItem.src.trim() !== ""
                );

            }
        );


    if (
        imagenesPropiedad.length === 0
    ) {

        mostrarSinImagen();

        return;

    }


    //==================================================
    // MOSTRAR IMAGEN
    //==================================================

    if (imagen) {

        imagen.classList.remove(
            "d-none"
        );

    }


    if (sinImagen) {

        sinImagen.classList.add(
            "d-none"
        );

    }


    //==================================================
    // CONTADOR
    //==================================================

    if (
        imagenesPropiedad.length > 1
    ) {

        if (contador) {

            contador.classList.remove(
                "d-none"
            );

        }


        if (anterior) {

            anterior.classList.remove(
                "d-none"
            );

        }


        if (siguiente) {

            siguiente.classList.remove(
                "d-none"
            );

        }

    }


    if (totalImagenes) {

        totalImagenes.textContent =
            imagenesPropiedad.length;

    }


    //==================================================
    // CREAR MINIATURAS
    //==================================================

    if (
        miniaturas &&
        imagenesPropiedad.length > 1
    ) {

        miniaturas.classList.remove(
            "d-none"
        );


        imagenesPropiedad.forEach(
            function (
                imagenItem,
                indice
            ) {

                const boton =
                    document.createElement(
                        "button"
                    );


                boton.type =
                    "button";


                boton.className =
                    "property-detail-thumbnail";


                boton.dataset.index =
                    indice;


                boton.setAttribute(
                    "aria-label",
                    "Ver imagen " +
                    (indice + 1)
                );


                const img =
                    document.createElement(
                        "img"
                    );


                img.src =
                    imagenItem.src;


                img.alt =
                    "Miniatura " +
                    (indice + 1);


                img.loading =
                    "lazy";


                img.onerror =
                    function () {

                        boton.remove();

                    };


                boton.appendChild(
                    img
                );


                boton.addEventListener(
                    "click",
                    function () {

                        cambiarImagen(
                            indice
                        );

                    }
                );


                miniaturas.appendChild(
                    boton
                );

            }
        );

    }


    //==================================================
    // MOSTRAR PRIMERA
    //==================================================

    cambiarImagen(
        0
    );

}


//======================================================
// CAMBIAR IMAGEN
//======================================================

function cambiarImagen(
    nuevoIndice
) {

    if (
        !imagenesPropiedad.length
    ) {

        return;

    }


    const cantidad =
        imagenesPropiedad.length;


    //==================================================
    // LOOP
    //==================================================

    if (
        nuevoIndice < 0
    ) {

        nuevoIndice =
            cantidad - 1;

    }


    if (
        nuevoIndice >= cantidad
    ) {

        nuevoIndice = 0;

    }


    indiceImagenActual =
        nuevoIndice;


    const imagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.imagen
        );


    const numeroImagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.numeroImagen
        );


    if (imagen) {

        const imagenActual =
            imagenesPropiedad[
                indiceImagenActual
            ];


        imagen.onerror =
            function () {

                console.warn(
                    "No se pudo cargar la imagen:",
                    imagenActual.src
                );

            };


        imagen.src =
            imagenActual.src;


        imagen.alt =
            imagenActual.alt ||
            "Imagen de la propiedad " +
            (indiceImagenActual + 1);


        imagen.classList.remove(
            "d-none"
        );

    }


    if (numeroImagen) {

        numeroImagen.textContent =
            indiceImagenActual + 1;

    }


    //==================================================
    // ACTUALIZAR MINIATURAS
    //==================================================

    actualizarMiniaturas();

}


//======================================================
// ACTUALIZAR MINIATURAS
//======================================================

function actualizarMiniaturas() {

    const miniaturas =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.miniaturas
        );


    if (!miniaturas) {

        return;

    }


    const botones =
        miniaturas.querySelectorAll(
            ".property-detail-thumbnail"
        );


    botones.forEach(
        function (
            boton,
            indice
        ) {

            if (
                indice === indiceImagenActual
            ) {

                boton.classList.add(
                    "active"
                );

            } else {

                boton.classList.remove(
                    "active"
                );

            }

        }
    );

}


//======================================================
// MOSTRAR SIN IMAGEN
//======================================================

function mostrarSinImagen() {

    const imagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.imagen
        );


    const sinImagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.sinImagen
        );


    if (imagen) {

        imagen.removeAttribute(
            "src"
        );

        imagen.alt =
            "Sin imagen disponible";

        imagen.classList.add(
            "d-none"
        );

    }


    if (sinImagen) {

        sinImagen.classList.remove(
            "d-none"
        );

        sinImagen.innerHTML =
            '<i class="bi bi-image"></i>' +
            '<span>Sin imagen disponible</span>';

    }

}


//======================================================
// CONFIGURAR WHATSAPP
//======================================================

function configurarWhatsapp(
    elemento,
    propiedad
) {

    if (!elemento) {

        return;

    }


    const telefonoOriginal =
        propiedad.telefono_whatsapp ||
        propiedad.celular ||
        propiedad.telefono ||
        "";


    let telefono =
        String(
            telefonoOriginal
        ).replace(
            /\D/g,
            ""
        );


    //==================================================
    // NORMALIZAR TELÉFONO PERUANO
    //==================================================

    if (
        telefono.length === 9 &&
        telefono.startsWith("9")
    ) {

        telefono =
            "51" +
            telefono;

    }


    //==================================================
    // SI VIENE CON 00
    //==================================================

    if (
        telefono.startsWith("00")
    ) {

        telefono =
            telefono.substring(2);

    }


    //==================================================
    // VALIDAR
    //==================================================

    const telefonoValido =
        telefono.length >= 10 &&
        telefono.length <= 15;


    if (!telefonoValido) {

        elemento.href =
            "contacto.php";

        elemento.target =
            "_self";

        elemento.rel =
            "";

        elemento.classList.remove(
            "disabled"
        );

        elemento.removeAttribute(
            "aria-disabled"
        );

        elemento.innerHTML =
            '<i class="bi bi-envelope me-2"></i>' +
            'Solicitar información';

        return;

    }


    //==================================================
    // DATOS
    //==================================================

    const nombre =
        String(
            propiedad.nombre ||
            "Propiedad"
        );


    const codigo =
        String(
            propiedad.codigo ||
            "No disponible"
        );


    const ubicacion =
        String(
            propiedad.ubicacion ||
            "No disponible"
        );


    const valorPrecio =
        parseFloat(
            propiedad.precio
        ) || 0;


    const precio =
        formatearPrecio(
            valorPrecio
        );


    //==================================================
    // MENSAJE
    //==================================================

    const mensaje =
        "Hola, me comunico para solicitar información sobre una propiedad.\n\n" +
        "Código: " +
        codigo +
        "\n" +
        "Nombre: " +
        nombre +
        "\n" +
        "Ubicación: " +
        ubicacion +
        "\n" +
        "Precio: " +
        precio +
        "\n\n" +
        "Quedo atento/a a su respuesta. Muchas gracias.";


    //==================================================
    // URL WHATSAPP
    //==================================================

    const urlWhatsapp =
        "https://wa.me/" +
        telefono +
        "?text=" +
        encodeURIComponent(
            mensaje
        );


    //==================================================
    // CONFIGURAR
    //==================================================

    elemento.href =
        urlWhatsapp;

    elemento.target =
        "_blank";

    elemento.rel =
        "noopener noreferrer";

    elemento.classList.remove(
        "disabled"
    );

    elemento.removeAttribute(
        "aria-disabled"
    );

    elemento.innerHTML =
        '<i class="bi bi-whatsapp me-2"></i>' +
        'Solicitar información';

}


//======================================================
// MOSTRAR CARGANDO
//======================================================

function mostrarCargandoDetalle() {

    imagenesPropiedad = [];

    indiceImagenActual = 0;


    const codigo =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.codigo
        );


    const imagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.imagen
        );


    const sinImagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.sinImagen
        );


    const contador =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.contador
        );


    const anterior =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.anterior
        );


    const siguiente =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.siguiente
        );


    const miniaturas =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.miniaturas
        );


    const precio =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precio
        );


    const categoria =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.categoria
        );


    const nombre =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.nombre
        );


    const ubicacion =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.ubicacion
        );


    const area =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.area
        );


    const tipo =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.tipo
        );


    const precioGrande =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioGrande
        );


    const precioAnteriorBox =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioAnteriorBox
        );


    const whatsapp =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.whatsapp
        );


    if (codigo) {

        codigo.textContent =
            "Cargando información...";

    }


    if (nombre) {

        nombre.textContent =
            "Cargando...";

    }


    if (categoria) {

        categoria.innerHTML =
            '<i class="bi bi-tag-fill"></i> Cargando...';

    }


    if (ubicacion) {

        ubicacion.textContent =
            "Cargando...";

    }


    if (area) {

        area.textContent =
            "Cargando...";

    }


    if (tipo) {

        tipo.textContent =
            "Cargando...";

    }


    if (precio) {

        precio.textContent =
            "S/. 0.00";

    }


    if (precioGrande) {

        precioGrande.textContent =
            "S/. 0.00";

    }


    if (precioAnteriorBox) {

        precioAnteriorBox.classList.add(
            "d-none"
        );

    }


    //==================================================
    // GALERÍA
    //==================================================

    if (imagen) {

        imagen.removeAttribute(
            "src"
        );

        imagen.alt =
            "Cargando imagen...";

        imagen.classList.add(
            "d-none"
        );

    }


    if (sinImagen) {

        sinImagen.classList.remove(
            "d-none"
        );

        sinImagen.innerHTML =
            '<i class="bi bi-hourglass-split"></i>' +
            '<span>Cargando imágenes...</span>';

    }


    if (contador) {

        contador.classList.add(
            "d-none"
        );

    }


    if (anterior) {

        anterior.classList.add(
            "d-none"
        );

    }


    if (siguiente) {

        siguiente.classList.add(
            "d-none"
        );

    }


    if (miniaturas) {

        miniaturas.innerHTML = "";

        miniaturas.classList.add(
            "d-none"
        );

    }


    //==================================================
    // WHATSAPP
    //==================================================

    if (whatsapp) {

        whatsapp.href =
            "#";

        whatsapp.classList.add(
            "disabled"
        );

        whatsapp.setAttribute(
            "aria-disabled",
            "true"
        );

        whatsapp.removeAttribute(
            "target"
        );

        whatsapp.innerHTML =
            '<i class="bi bi-hourglass-split me-2"></i>' +
            'Cargando...';

    }

}


//======================================================
// MOSTRAR ERROR
//======================================================

function mostrarErrorDetalle(
    mensaje
) {

    const codigo =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.codigo
        );


    const nombre =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.nombre
        );


    const categoria =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.categoria
        );


    const ubicacion =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.ubicacion
        );


    const area =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.area
        );


    const tipo =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.tipo
        );


    const precio =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precio
        );


    const precioGrande =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioGrande
        );


    const precioAnteriorBox =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioAnteriorBox
        );


    const whatsapp =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.whatsapp
        );


    if (codigo) {

        codigo.textContent =
            "Error";

    }


    if (nombre) {

        nombre.textContent =
            "No se pudo cargar la propiedad";

    }


    if (categoria) {

        categoria.innerHTML =
            '<i class="bi bi-exclamation-triangle-fill"></i> Error';

    }


    if (ubicacion) {

        ubicacion.textContent =
            mensaje ||
            "Información no disponible";

    }


    if (area) {

        area.textContent =
            "--";

    }


    if (tipo) {

        tipo.textContent =
            "--";

    }


    if (precio) {

        precio.textContent =
            "S/. 0.00";

    }


    if (precioGrande) {

        precioGrande.textContent =
            "S/. 0.00";

    }


    if (precioAnteriorBox) {

        precioAnteriorBox.classList.add(
            "d-none"
        );

    }


    mostrarSinImagen();


    if (whatsapp) {

        whatsapp.href =
            "contacto.php";

        whatsapp.target =
            "_self";

        whatsapp.rel =
            "";

        whatsapp.classList.remove(
            "disabled"
        );

        whatsapp.removeAttribute(
            "aria-disabled"
        );

        whatsapp.innerHTML =
            '<i class="bi bi-envelope me-2"></i>' +
            'Contactar';

    }

}


//======================================================
// LIMPIAR DETALLE
//======================================================

function limpiarDetalle() {

    imagenesPropiedad = [];

    indiceImagenActual = 0;


    const imagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.imagen
        );


    const sinImagen =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.sinImagen
        );


    const contador =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.contador
        );


    const anterior =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.anterior
        );


    const siguiente =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.siguiente
        );


    const miniaturas =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.miniaturas
        );


    const precioAnteriorBox =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.precioAnteriorBox
        );


    const whatsapp =
        obtenerElemento(
            DETALLE_PROPIEDAD.elementos.whatsapp
        );


    if (imagen) {

        imagen.removeAttribute(
            "src"
        );

        imagen.classList.add(
            "d-none"
        );

        imagen.onerror =
            null;

    }


    if (sinImagen) {

        sinImagen.classList.add(
            "d-none"
        );

    }


    if (contador) {

        contador.classList.add(
            "d-none"
        );

    }


    if (anterior) {

        anterior.classList.add(
            "d-none"
        );

    }


    if (siguiente) {

        siguiente.classList.add(
            "d-none"
        );

    }


    if (miniaturas) {

        miniaturas.innerHTML = "";

        miniaturas.classList.add(
            "d-none"
        );

    }


    if (precioAnteriorBox) {

        precioAnteriorBox.classList.add(
            "d-none"
        );

    }


    if (whatsapp) {

        whatsapp.href =
            "#";

        whatsapp.classList.remove(
            "disabled"
        );

        whatsapp.removeAttribute(
            "aria-disabled"
        );

        whatsapp.innerHTML =
            '<i class="bi bi-whatsapp me-2"></i>' +
            'Solicitar información';

    }

}


//======================================================
// OBTENER ELEMENTO
//======================================================

function obtenerElemento(
    id
) {

    return document.getElementById(
        id
    );

}


//======================================================
// FORMATEAR PRECIO
//======================================================

function formatearPrecio(
    valor
) {

    const numero =
        parseFloat(
            valor
        ) || 0;


    return (
        "S/. " +
        numero.toLocaleString(
            "es-PE",
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        )
    );

}


//======================================================
// FORMATEAR NÚMERO
//======================================================

function formatearNumero(
    valor,
    decimales = 2
) {

    const numero =
        parseFloat(
            valor
        ) || 0;


    return numero.toLocaleString(
        "es-PE",
        {
            minimumFractionDigits: decimales,
            maximumFractionDigits: decimales
        }
    );

}


//======================================================
// ESCAPAR HTML
//======================================================

function escaparHTML(
    texto
) {

    const div =
        document.createElement(
            "div"
        );


    div.textContent =
        texto ?? "";


    return div.innerHTML;

}