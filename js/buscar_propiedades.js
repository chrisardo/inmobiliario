//======================================================
// CoDevPro Technology
// Archivo: js/buscar_propiedades.js
// Módulo: Propiedades
// Sistema: Inmobiliario
//======================================================

"use strict";


//======================================================
// CONFIGURACIÓN
//======================================================

const CONFIG_PROPIEDADES = {

    // Endpoint PHP
    url: "controladores/buscar_propiedades.php",

    // Tiempo de espera para búsqueda mientras se escribe
    debounce: 400,

    // Tiempo mínimo entre búsquedas automáticas
    minimoEntreBusquedas: 150

};


//======================================================
// ELEMENTOS DEL DOM
//======================================================

let formFiltros = null;

let inputBuscar = null;
let filtroCategoria = null;

let precioMin = null;
let precioMax = null;

let areaMin = null;
let areaMax = null;

let fechaDesde = null;
let orden = null;

let btnLimpiarBusqueda = null;
let btnLimpiarFiltros = null;

let loadingBusqueda = null;
let resultadoPropiedades = null;

let contadorResultados = null;
let textoResultados = null;


//======================================================
// VARIABLES DE CONTROL
//======================================================

// Temporizador del debounce
let temporizadorBusqueda = null;

// Controlador para cancelar peticiones anteriores
let controladorAbort = null;

// Evita realizar búsquedas simultáneas
let busquedaEnCurso = false;

// Guarda el momento de la última búsqueda
let ultimaBusqueda = 0;


//======================================================
// INICIALIZAR
//======================================================

document.addEventListener("DOMContentLoaded", function () {

    inicializarElementos();

    if (!formFiltros || !resultadoPropiedades) {

        console.error(
            "buscar_propiedades.js: No se encontraron los elementos principales del buscador."
        );

        return;
    }

    configurarEventos();

    // Cargar propiedades al abrir la página
    buscarPropiedades();

});


//======================================================
// OBTENER ELEMENTOS
//======================================================

function inicializarElementos() {

    formFiltros =
        document.getElementById(
            "formFiltrosPropiedades"
        );

    inputBuscar =
        document.getElementById(
            "inputBuscar"
        );

    filtroCategoria =
        document.getElementById(
            "filtroCategoria"
        );

    precioMin =
        document.getElementById(
            "precioMin"
        );

    precioMax =
        document.getElementById(
            "precioMax"
        );

    areaMin =
        document.getElementById(
            "areaMin"
        );

    areaMax =
        document.getElementById(
            "areaMax"
        );

    fechaDesde =
        document.getElementById(
            "fechaDesde"
        );

    orden =
        document.getElementById(
            "orden"
        );

    btnLimpiarBusqueda =
        document.getElementById(
            "btnLimpiarBusqueda"
        );

    btnLimpiarFiltros =
        document.getElementById(
            "btnLimpiarFiltros"
        );

    loadingBusqueda =
        document.getElementById(
            "loadingBusqueda"
        );

    resultadoPropiedades =
        document.getElementById(
            "resultadoPropiedades"
        );

    contadorResultados =
        document.getElementById(
            "contadorResultados"
        );

    textoResultados =
        document.getElementById(
            "textoResultados"
        );

}


//======================================================
// CONFIGURAR EVENTOS
//======================================================

function configurarEventos() {


    //==================================================
    // EVITAR ENVÍO NORMAL DEL FORMULARIO
    //==================================================

    formFiltros.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();

            buscarPropiedades();

        }
    );


    //==================================================
    // BÚSQUEDA POR TEXTO
    //==================================================

    if (inputBuscar) {

        inputBuscar.addEventListener(
            "input",
            function () {

                programarBusqueda();

            }
        );

    }


    //==================================================
    // CATEGORÍA
    //==================================================

    if (filtroCategoria) {

        filtroCategoria.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // PRECIO MÍNIMO
    //==================================================

    if (precioMin) {

        precioMin.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // PRECIO MÁXIMO
    //==================================================

    if (precioMax) {

        precioMax.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // ÁREA MÍNIMA
    //==================================================

    if (areaMin) {

        areaMin.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // ÁREA MÁXIMA
    //==================================================

    if (areaMax) {

        areaMax.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // FECHA
    //==================================================

    if (fechaDesde) {

        fechaDesde.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // ORDEN
    //==================================================

    if (orden) {

        orden.addEventListener(
            "change",
            function () {

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // LIMPIAR BÚSQUEDA
    //==================================================

    if (btnLimpiarBusqueda) {

        btnLimpiarBusqueda.addEventListener(
            "click",
            function () {

                if (inputBuscar) {

                    inputBuscar.value = "";

                    inputBuscar.focus();

                }

                buscarPropiedades();

            }
        );

    }


    //==================================================
    // LIMPIAR TODOS LOS FILTROS
    //==================================================

    if (btnLimpiarFiltros) {

        btnLimpiarFiltros.addEventListener(
            "click",
            function () {

                limpiarFiltros();

            }
        );

    }

}


//======================================================
// PROGRAMAR BÚSQUEDA
//======================================================

function programarBusqueda() {

    clearTimeout(
        temporizadorBusqueda
    );

    temporizadorBusqueda =
        setTimeout(
            function () {

                buscarPropiedades();

            },
            CONFIG_PROPIEDADES.debounce
        );

}


//======================================================
// LIMPIAR FILTROS
//======================================================

function limpiarFiltros() {

    clearTimeout(
        temporizadorBusqueda
    );


    if (inputBuscar) {

        inputBuscar.value = "";

    }


    if (filtroCategoria) {

        filtroCategoria.value = "";

    }


    if (precioMin) {

        precioMin.value = "";

    }


    if (precioMax) {

        precioMax.value = "";

    }


    if (areaMin) {

        areaMin.value = "";

    }


    if (areaMax) {

        areaMax.value = "";

    }


    if (fechaDesde) {

        fechaDesde.value = "";

    }


    if (orden) {

        orden.value = "recientes";

    }


    buscarPropiedades();

}


//======================================================
// OBTENER DATOS DEL FORMULARIO
//======================================================

function obtenerParametros() {

    const parametros =
        new URLSearchParams();


    //==================================================
    // BÚSQUEDA
    //==================================================

    if (inputBuscar) {

        parametros.set(
            "buscar",
            inputBuscar.value.trim()
        );

    }


    //==================================================
    // CATEGORÍA
    //==================================================

    if (filtroCategoria) {

        parametros.set(
            "categoria",
            filtroCategoria.value || ""
        );

    }


    //==================================================
    // PRECIO MÍNIMO
    //==================================================

    if (precioMin) {

        parametros.set(
            "precio_min",
            precioMin.value.trim()
        );

    }


    //==================================================
    // PRECIO MÁXIMO
    //==================================================

    if (precioMax) {

        parametros.set(
            "precio_max",
            precioMax.value.trim()
        );

    }


    //==================================================
    // ÁREA MÍNIMA
    //==================================================

    if (areaMin) {

        parametros.set(
            "area_min",
            areaMin.value.trim()
        );

    }


    //==================================================
    // ÁREA MÁXIMA
    //==================================================

    if (areaMax) {

        parametros.set(
            "area_max",
            areaMax.value.trim()
        );

    }


    //==================================================
    // FECHA DESDE
    //==================================================

    if (fechaDesde) {

        parametros.set(
            "fecha_desde",
            fechaDesde.value.trim()
        );

    }


    //==================================================
    // ORDEN
    //==================================================

    if (orden) {

        parametros.set(
            "orden",
            orden.value || "recientes"
        );

    }


    return parametros;

}


//======================================================
// VALIDAR FILTROS ANTES DE ENVIAR
//======================================================

function validarFiltros() {

    const precioMinValor =
        obtenerNumero(
            precioMin
        );

    const precioMaxValor =
        obtenerNumero(
            precioMax
        );

    const areaMinValor =
        obtenerNumero(
            areaMin
        );

    const areaMaxValor =
        obtenerNumero(
            areaMax
        );


    //==================================================
    // PRECIO MÍNIMO NEGATIVO
    //==================================================

    if (
        precioMin &&
        precioMin.value !== "" &&
        precioMinValor === null
    ) {

        mostrarMensajeError(
            "El precio mínimo no es válido."
        );

        return false;

    }


    //==================================================
    // PRECIO MÁXIMO NEGATIVO
    //==================================================

    if (
        precioMax &&
        precioMax.value !== "" &&
        precioMaxValor === null
    ) {

        mostrarMensajeError(
            "El precio máximo no es válido."
        );

        return false;

    }


    //==================================================
    // ÁREA MÍNIMA
    //==================================================

    if (
        areaMin &&
        areaMin.value !== "" &&
        areaMinValor === null
    ) {

        mostrarMensajeError(
            "El área mínima no es válida."
        );

        return false;

    }


    //==================================================
    // ÁREA MÁXIMA
    //==================================================

    if (
        areaMax &&
        areaMax.value !== "" &&
        areaMaxValor === null
    ) {

        mostrarMensajeError(
            "El área máxima no es válida."
        );

        return false;

    }


    //==================================================
    // PRECIO MÍNIMO > PRECIO MÁXIMO
    //==================================================

    if (
        precioMinValor !== null &&
        precioMaxValor !== null &&
        precioMinValor > precioMaxValor
    ) {

        mostrarMensajeError(
            "El precio mínimo no puede ser mayor que el precio máximo."
        );

        return false;

    }


    //==================================================
    // ÁREA MÍNIMA > ÁREA MÁXIMA
    //==================================================

    if (
        areaMinValor !== null &&
        areaMaxValor !== null &&
        areaMinValor > areaMaxValor
    ) {

        mostrarMensajeError(
            "El área mínima no puede ser mayor que el área máxima."
        );

        return false;

    }


    return true;

}


//======================================================
// OBTENER NÚMERO
//======================================================

function obtenerNumero(elemento) {

    if (!elemento) {

        return null;

    }


    const valor =
        elemento.value.trim();


    if (valor === "") {

        return null;

    }


    const numero =
        Number(valor);


    if (
        !Number.isFinite(numero) ||
        numero < 0
    ) {

        return null;

    }


    return numero;

}


//======================================================
// BUSCAR PROPIEDADES
//======================================================

async function buscarPropiedades() {

    clearTimeout(
        temporizadorBusqueda
    );


    //==================================================
    // VALIDAR
    //==================================================

    if (!validarFiltros()) {

        return;

    }


    //==================================================
    // CANCELAR PETICIÓN ANTERIOR
    //==================================================

    if (controladorAbort) {

        controladorAbort.abort();

    }


    controladorAbort =
        new AbortController();


    //==================================================
    // OBTENER PARÁMETROS
    //==================================================

    const parametros =
        obtenerParametros();


    //==================================================
    // URL FINAL
    //==================================================

    const url =
        CONFIG_PROPIEDADES.url +
        "?" +
        parametros.toString();


    //==================================================
    // MOSTRAR LOADING
    //==================================================

    mostrarLoading(
        true
    );


    busquedaEnCurso = true;


    try {

        //================================================
        // PETICIÓN FETCH
        //================================================

        const respuesta =
            await fetch(
                url,
                {
                    method: "GET",
                    headers: {
                        "Accept":
                            "application/json",
                        "X-Requested-With":
                            "XMLHttpRequest"
                    },
                    cache: "no-store",
                    signal:
                        controladorAbort.signal
                }
            );


        //================================================
        // COMPROBAR HTTP
        //================================================

        if (!respuesta.ok) {

            throw new Error(
                "HTTP " +
                respuesta.status
            );

        }


        //================================================
        // OBTENER TEXTO
        //================================================

        const texto =
            await respuesta.text();


        //================================================
        // CONVERTIR JSON
        //================================================

        let datos;

        try {

            datos =
                JSON.parse(texto);

        } catch (error) {

            console.error(
                "Respuesta recibida por el servidor:",
                texto
            );

            throw new Error(
                "El servidor no devolvió una respuesta JSON válida."
            );

        }


        //================================================
        // COMPROBAR RESPUESTA PHP
        //================================================

        if (
            !datos ||
            typeof datos !== "object"
        ) {

            throw new Error(
                "La respuesta del servidor es inválida."
            );

        }


        //================================================
        // ERROR DEVUELTO POR PHP
        //================================================

        if (datos.success !== true) {

            mostrarErrorResultado(
                datos.mensaje ||
                "No se pudo realizar la búsqueda."
            );

            actualizarContadores(
                0
            );

            return;

        }


        //================================================
        // INSERTAR HTML
        //================================================

        resultadoPropiedades.innerHTML =
            datos.html || "";


        //================================================
        // ACTUALIZAR CONTADORES
        //================================================

        const total =
            Number(
                datos.total || 0
            );


        actualizarContadores(
            total
        );


        //================================================
        // GUARDAR HORA
        //================================================

        ultimaBusqueda =
            Date.now();


    } catch (error) {

        //================================================
        // PETICIÓN CANCELADA
        //================================================

        if (
            error &&
            error.name === "AbortError"
        ) {

            return;

        }


        //================================================
        // ERROR
        //================================================

        console.error(
            "Error buscando propiedades:",
            error
        );


        mostrarErrorResultado(
            "Ocurrió un error al cargar las propiedades. Inténtalo nuevamente."
        );


        actualizarContadores(
            0
        );


    } finally {

        busquedaEnCurso = false;

        mostrarLoading(
            false
        );

    }

}


//======================================================
// MOSTRAR / OCULTAR LOADING
//======================================================

function mostrarLoading(mostrar) {

    if (!loadingBusqueda) {

        return;

    }


    if (mostrar) {

        loadingBusqueda.classList.remove(
            "d-none"
        );

    } else {

        loadingBusqueda.classList.add(
            "d-none"
        );

    }

}


//======================================================
// ACTUALIZAR CONTADORES
//======================================================

function actualizarContadores(total) {

    total =
        Number(total);


    if (!Number.isFinite(total)) {

        total = 0;

    }


    total =
        Math.max(
            0,
            Math.floor(total)
        );


    const totalFormateado =
        total.toLocaleString(
            "es-PE"
        );


    //==================================================
    // CONTADOR PRINCIPAL
    //==================================================

    if (contadorResultados) {

        contadorResultados.innerHTML =
            `
            Resultado:
            <strong>
                ${totalFormateado}
            </strong>
            en la zona.
            `;

    }


    //==================================================
    // TEXTO DE RESULTADOS
    //==================================================

    if (textoResultados) {

        if (total === 1) {

            textoResultados.textContent =
                "Mostrando 1 propiedad";

        } else {

            textoResultados.textContent =
                "Mostrando " +
                totalFormateado +
                " propiedades";

        }

    }

}


//======================================================
// MOSTRAR ERROR EN RESULTADOS
//======================================================

function mostrarErrorResultado(mensaje) {

    if (!resultadoPropiedades) {

        return;

    }


    resultadoPropiedades.innerHTML =
        `
        <div class="row g-4">

            <div class="col-12">

                <div class="alert alert-danger d-flex align-items-start gap-3 shadow-sm"
                     role="alert">

                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>

                    <div>

                        <h5 class="alert-heading mb-1">
                            No se pudieron cargar las propiedades
                        </h5>

                        <p class="mb-0">
                            ${escaparHTML(mensaje)}
                        </p>

                    </div>

                </div>

            </div>

        </div>
        `;

}


//======================================================
// MOSTRAR MENSAJE DE ERROR DE FILTRO
//======================================================

function mostrarMensajeError(mensaje) {

    if (
        typeof window.Swal !== "undefined"
    ) {

        Swal.fire({

            icon: "warning",

            title: "Revisa los filtros",

            text: mensaje,

            confirmButtonText: "Entendido"

        });

        return;

    }


    //==================================================
    // FALLBACK SIN SWEETALERT
    //==================================================

    alert(
        mensaje
    );

}


//======================================================
// ESCAPAR HTML
//======================================================

function escaparHTML(texto) {

    const div =
        document.createElement(
            "div"
        );


    div.textContent =
        String(
            texto ?? ""
        );


    return div.innerHTML;

}


//======================================================
// ACTUALIZAR VISIBILIDAD DEL BOTÓN LIMPIAR BÚSQUEDA
//======================================================
function actualizarBotonLimpiarBusqueda() {

    if (
        !btnLimpiarBusqueda ||
        !inputBuscar
    ) {
        return;
    }

    const tieneTexto =
        inputBuscar.value.trim() !== "";

    if (tieneTexto) {

        btnLimpiarBusqueda.classList.remove(
            "d-none"
        );

    } else {

        btnLimpiarBusqueda.classList.add(
            "d-none"
        );

    }
}


//======================================================
// ACTUALIZAR BOTÓN AL ESCRIBIR
//======================================================

if (typeof document !== "undefined") {

    document.addEventListener(
        "DOMContentLoaded",
        function () {

            if (inputBuscar) {

                inputBuscar.addEventListener(
                    "input",
                    actualizarBotonLimpiarBusqueda
                );

                actualizarBotonLimpiarBusqueda();

            }

        }
    );

}


//======================================================
// EXPONER FUNCIÓN GLOBAL
//======================================================
//
// Permite ejecutar:
//
// buscarPropiedades();
//
// desde otros archivos JS o desde la consola.
//
//======================================================

window.buscarPropiedades =
    buscarPropiedades;


window.limpiarFiltrosPropiedades =
    limpiarFiltros;