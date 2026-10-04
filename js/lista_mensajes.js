//=========================================================
// CoDevPro Technology
// Archivo: js/lista_mensajes.js
// Módulo: Mensajes / Contactos
// Sistema: Inmobiliario
//=========================================================

"use strict";

/*=========================================================
  CONFIGURACIÓN
=========================================================*/

const LISTA_MENSAJES = {
  pagina: "adm_mensajes.php",

  debounceBusqueda: 500,

  clases: {
    filtro: ".auto-filtro",
    botonVer: ".btn-ver-mensaje",
    estado: ".message-status",
    fila: "tr",
  },
};

/*=========================================================
  VARIABLES
=========================================================*/

let temporizadorBusqueda = null;

/*=========================================================
  UTILIDADES
=========================================================*/

/**
 * Obtiene un elemento por ID sin generar errores.
 */
function obtenerElemento(id) {
  const elemento = document.getElementById(id);

  return elemento || null;
}

/**
 * Asigna texto de forma segura.
 */
function establecerTexto(elemento, texto) {
  if (!elemento) {
    return;
  }

  elemento.textContent =
    texto !== null && texto !== undefined && String(texto).trim() !== ""
      ? String(texto)
      : "—";
}

/**
 * Escapa HTML para evitar insertar contenido
 * recibido desde atributos directamente como HTML.
 */
function escaparHTML(texto) {
  if (texto === null || texto === undefined) {
    return "";
  }

  const div = document.createElement("div");

  div.textContent = String(texto);

  return div.innerHTML;
}

/**
 * Obtiene un atributo data-*.
 */
function obtenerData(elemento, atributo) {
  if (!elemento) {
    return "";
  }

  const valor = elemento.getAttribute(`data-${atributo}`);

  return valor !== null ? valor : "";
}

/*=========================================================
  FECHAS
=========================================================*/

/**
 * Convierte una fecha recibida desde MySQL
 * a formato DD/MM/YYYY.
 *
 * Ejemplo:
 * 2026-08-29 14:30:00
 * → 29/08/2026
 */
function formatearFecha(fecha) {
  if (!fecha) {
    return "—";
  }

  const texto = String(fecha).trim();

  if (!texto) {
    return "—";
  }

  /*
   * Primero intentamos detectar directamente
   * una fecha MySQL.
   */
  const coincidencia = texto.match(/^(\d{4})-(\d{2})-(\d{2})/);

  if (coincidencia) {
    return coincidencia[3] + "/" + coincidencia[2] + "/" + coincidencia[1];
  }

  /*
   * Si no es MySQL, intentamos utilizar
   * Date().
   */
  const fechaJS = new Date(texto);

  if (!isNaN(fechaJS.getTime())) {
    const dia = String(fechaJS.getDate()).padStart(2, "0");

    const mes = String(fechaJS.getMonth() + 1).padStart(2, "0");

    const anio = fechaJS.getFullYear();

    return `${dia}/${mes}/${anio}`;
  }

  /*
   * Si no se puede interpretar,
   * devolvemos el valor original.
   */
  return texto;
}

/**
 * Formatea fecha y hora.
 */
function formatearFechaHora(fecha) {
  if (!fecha) {
    return "—";
  }

  const texto = String(fecha).trim();

  if (!texto) {
    return "—";
  }

  const coincidencia = texto.match(
    /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/,
  );

  if (coincidencia) {
    const dia = coincidencia[3];

    const mes = coincidencia[2];

    const anio = coincidencia[1];

    let resultado = `${dia}/${mes}/${anio}`;

    if (coincidencia[4]) {
      resultado += ` ${coincidencia[4]}:${coincidencia[5]}`;

      if (coincidencia[6]) {
        resultado += `:${coincidencia[6]}`;
      }
    }

    return resultado;
  }

  return formatearFecha(texto);
}

/*=========================================================
  NOMBRE DEL CLIENTE
=========================================================*/

function construirNombreCliente(nombre, apellidos) {
  const partes = [];

  if (nombre && String(nombre).trim() !== "") {
    partes.push(String(nombre).trim());
  }

  if (apellidos && String(apellidos).trim() !== "") {
    partes.push(String(apellidos).trim());
  }

  return partes.length > 0 ? partes.join(" ") : "Cliente";
}

/*=========================================================
  MODAL — ELEMENTOS
=========================================================*/

/**
 * Busca un elemento utilizando varios posibles IDs.
 *
 * Esto permite que el JS sea compatible con pequeñas
 * diferencias en modal_mensaje.php.
 */
function buscarElementoModal(...ids) {
  for (const id of ids) {
    const elemento = document.getElementById(id);

    if (elemento) {
      return elemento;
    }
  }

  return null;
}

/*=========================================================
  MODAL — CARGAR DATOS
=========================================================*/

function cargarMensajeEnModal(boton) {
  if (!boton) {
    return;
  }

  /*-----------------------------------------------------
      DATOS DEL MENSAJE
    -----------------------------------------------------*/

  const id = obtenerData(boton, "id");

  const nombre = obtenerData(boton, "nombre");

  const apellidos = obtenerData(boton, "apellidos");

  const email = obtenerData(boton, "email");

  const celular = obtenerData(boton, "celular");

  const propiedad = obtenerData(boton, "propiedad");

  const estado = obtenerData(boton, "estado");

  const fecha = obtenerData(boton, "fecha");

  const fechaLeido = obtenerData(boton, "fecha-leido");

  const mensaje = obtenerData(boton, "mensaje");

  const nombreCliente = construirNombreCliente(nombre, apellidos);

  /*=====================================================
      BUSCAR ELEMENTOS DEL MODAL
    =====================================================*/

  const modal = document.getElementById("modalVerDetalles");

  if (!modal) {
    console.warn("No se encontró #modalVerDetalles.");

    return;
  }

  /*=====================================================
      ID
    =====================================================*/

  const elementosId = [
    "detalleId",
    "idMensaje",
    "mensajeId",
    "detalleMensajeId",
  ];

  const elementoId = buscarElementoModal(...elementosId);

  establecerTexto(elementoId, id);

  /*=====================================================
      NOMBRE
    =====================================================*/

  const elementoNombre = buscarElementoModal(
    "detalleNombre",
    "nombreCliente",
    "modalNombre",
    "mensajeNombre",
    "verNombre",
  );

  establecerTexto(elementoNombre, nombreCliente);

  /*=====================================================
      EMAIL
    =====================================================*/

  const elementoEmail = buscarElementoModal(
    "detalleEmail",
    "emailCliente",
    "modalEmail",
    "mensajeEmail",
    "verEmail",
  );

  establecerTexto(elementoEmail, email || "Sin correo");

  /*=====================================================
      CELULAR
    =====================================================*/

  const elementoCelular = buscarElementoModal(
    "detalleCelular",
    "celularCliente",
    "modalCelular",
    "mensajeCelular",
    "verCelular",
  );

  establecerTexto(elementoCelular, celular || "Sin celular");

  /*=====================================================
      PROPIEDAD
    =====================================================*/

  const elementoPropiedad = buscarElementoModal(
    "detallePropiedad",
    "nombrePropiedad",
    "modalPropiedad",
    "mensajePropiedad",
    "verPropiedad",
  );

  establecerTexto(elementoPropiedad, propiedad || "No especificada");

  /*=====================================================
      FECHA
    =====================================================*/

  const elementoFecha = buscarElementoModal(
    "detalleFecha",
    "fechaMensaje",
    "modalFecha",
    "mensajeFecha",
    "verFecha",
  );

  establecerTexto(elementoFecha, formatearFechaHora(fecha));

  /*=====================================================
      MENSAJE
    =====================================================*/

  const elementoMensaje = buscarElementoModal(
    "detalleMensaje",
    "contenidoMensaje",
    "modalMensaje",
    "mensajeContenido",
    "verMensaje",
  );

  if (elementoMensaje) {
    /*
     * textContent permite mostrar correctamente
     * saltos de línea sin ejecutar HTML.
     */
    elementoMensaje.textContent = mensaje || "Sin mensaje.";
  }

  /*=====================================================
      ESTADO
    =====================================================*/

  const elementoEstado = buscarElementoModal(
    "detalleEstado",
    "estadoMensaje",
    "modalEstado",
    "mensajeEstado",
    "verEstado",
  );

  if (elementoEstado) {
    actualizarEstadoModal(elementoEstado, estado);
  }

  /*=====================================================
      FECHA DE LECTURA
    =====================================================*/

  const elementoFechaLeido = buscarElementoModal(
    "detalleFechaLeido",
    "fechaLeido",
    "modalFechaLeido",
    "mensajeFechaLeido",
    "verFechaLeido",
  );

  if (elementoFechaLeido) {
    establecerTexto(
      elementoFechaLeido,
      fechaLeido ? formatearFechaHora(fechaLeido) : "Aún no leído",
    );
  }

  /*=====================================================
      GUARDAR ID EN EL MODAL
    =====================================================*/

  modal.dataset.idMensaje = id;

  modal.dataset.estadoMensaje = estado;
}

/*=========================================================
  ACTUALIZAR ESTADO DEL MODAL
=========================================================*/

function actualizarEstadoModal(elemento, estado) {
  if (!elemento) {
    return;
  }

  const estadoNormalizado = String(estado || "")
    .toLowerCase()
    .trim();

  /*
   * Permitimos diferentes valores provenientes
   * de la base de datos.
   */
  const leido =
    estadoNormalizado === "1" ||
    estadoNormalizado === "leido" ||
    estadoNormalizado === "leído" ||
    estadoNormalizado === "read";

  elemento.classList.remove("read", "unread", "leido", "no-leido", "no_leido");

  if (leido) {
    elemento.classList.add("read");

    elemento.innerHTML = '<i class="fa-solid fa-envelope-open"></i> Leído';
  } else {
    elemento.classList.add("unread");

    elemento.innerHTML = '<i class="fa-solid fa-envelope"></i> No leído';
  }
}
/*=========================================================
  MARCAR MENSAJE COMO LEÍDO
=========================================================*/

async function marcarMensajeComoLeido(idContacto, boton) {
  if (!idContacto) {
    console.warn("No se recibió un ID de mensaje válido.");

    return;
  }

  try {
    const datos = new FormData();

    datos.append("id_contacto", idContacto);

    const respuesta = await fetch("../ajax/marcar_mensaje_leido.php", {
      method: "POST",
      body: datos,
      credentials: "same-origin",
    });

    if (!respuesta.ok) {
      throw new Error(`HTTP ${respuesta.status}`);
    }

    const resultado = await respuesta.json();

    console.log("Respuesta marcar leído:", resultado);

    if (!resultado.success) {
      console.error(
        resultado.message || "No se pudo marcar el mensaje como leído.",
      );

      return;
    }

    /*
     * Actualizar visualmente la fila.
     */
    actualizarEstadoFilaMensaje(idContacto, resultado.fecha_leido);

    /*
     * Actualizar estado dentro del modal.
     */
    const modal = document.getElementById("modalVerDetalles");

    if (modal) {
      modal.dataset.estadoMensaje = "1";

      const elementoEstado = buscarElementoModal(
        "detalleEstado",
        "estadoMensaje",
        "modalEstado",
        "mensajeEstado",
        "verEstado",
      );

      if (elementoEstado) {
        actualizarEstadoModal(elementoEstado, "1");
      }

      const elementoFechaLeido = buscarElementoModal(
        "detalleFechaLeido",
        "fechaLeido",
        "modalFechaLeido",
        "mensajeFechaLeido",
        "verFechaLeido",
      );

      if (elementoFechaLeido) {
        establecerTexto(
          elementoFechaLeido,
          resultado.fecha_leido
            ? formatearFechaHora(resultado.fecha_leido)
            : "Aún no leído",
        );
      }
    }

    /*
     * Actualizar los KPI.
     */
    if (!resultado.ya_leido) {
      actualizarKPIMensajes();
    }

    /*
     * Actualizar el botón.
     */
    if (boton) {
      boton.dataset.estado = "1";

      boton.dataset.fechaLeido = resultado.fecha_leido || "";
    }
  } catch (error) {
    console.error("Error al marcar mensaje como leído:", error);
  }
}

/*=========================================================
  ACTUALIZAR ESTADO VISUAL DE LA FILA
=========================================================*/

function actualizarEstadoFilaMensaje(idContacto, fechaLeido = "") {
  const estado = document.querySelector(
    `.message-status[data-message-status="${idContacto}"]`,
  );

  if (!estado) {
    console.warn("No se encontró el estado visual del mensaje:", idContacto);

    return;
  }

  /*
   * Cambiar clases.
   */
  estado.classList.remove("unread", "read", "leido", "no-leido", "no_leido");

  estado.classList.add("read");

  /*
   * Cambiar contenido.
   */
  estado.innerHTML = '<i class="fa-solid fa-envelope-open"></i> Leído';

  /*
   * Accesibilidad.
   */
  estado.setAttribute("aria-label", "Mensaje leído");

  /*
   * Guardar estado.
   */
  estado.dataset.estado = "1";

  if (fechaLeido) {
    estado.dataset.fechaLeido = fechaLeido;
  }
}

/*=========================================================
  ACTUALIZAR KPI DE MENSAJES
=========================================================*/

function actualizarKPIMensajes() {
  const kpis = document.querySelectorAll(".message-kpi");

  if (!kpis.length) {
    return;
  }

  /*
   * KPI 1 = Total
   * KPI 2 = Leídos
   * KPI 3 = No leídos
   */

  const totalElemento = kpis[0] ? kpis[0].querySelector("strong") : null;

  const leidosElemento = kpis[1] ? kpis[1].querySelector("strong") : null;

  const noLeidosElemento = kpis[2] ? kpis[2].querySelector("strong") : null;

  if (!leidosElemento || !noLeidosElemento) {
    return;
  }

  const obtenerNumero = function (elemento) {
    if (!elemento) {
      return 0;
    }

    return parseInt(elemento.textContent.replace(/[^\d]/g, ""), 10) || 0;
  };

  let leidos = obtenerNumero(leidosElemento);

  let noLeidos = obtenerNumero(noLeidosElemento);

  /*
   * Solo modificamos si todavía existe
   * al menos un mensaje no leído.
   */
  if (noLeidos > 0) {
    noLeidos--;
    leidos++;
  }

  leidosElemento.textContent = leidos.toLocaleString("es-PE");

  noLeidosElemento.textContent = noLeidos.toLocaleString("es-PE");
}

/*=========================================================
  BOTONES "VER MENSAJE"
=========================================================*/

function inicializarBotonesVerMensaje() {
  const botones = document.querySelectorAll(LISTA_MENSAJES.clases.botonVer);

  if (!botones.length) {
    return;
  }

  botones.forEach(function (boton) {
    boton.addEventListener("click", async function () {
      /*
       * Primero cargamos el mensaje
       * en el modal.
       */
      cargarMensajeEnModal(boton);

      /*
       * Obtener ID.
       */
      const idContacto = obtenerData(boton, "id");

      if (!idContacto) {
        console.warn("El botón Ver no tiene data-id.");

        return;
      }

      /*
       * Obtener estado actual.
       */
      const estadoActual = obtenerData(boton, "estado");

      const estadoNormalizado = String(estadoActual || "")
        .toLowerCase()
        .trim();

      /*
       * Si ya está leído,
       * no hacemos petición.
       */
      const yaLeido =
        estadoNormalizado === "1" ||
        estadoNormalizado === "leido" ||
        estadoNormalizado === "leído" ||
        estadoNormalizado === "read";

      if (yaLeido) {
        return;
      }

      /*
       * Marcar como leído.
       */
      await marcarMensajeComoLeido(idContacto, boton);
    });
  });
}
/*=========================================================
  FILTROS AUTOMÁTICOS
=========================================================*/

/**
 * Construye la URL utilizando únicamente los filtros
 * que realmente existen en el formulario.
 */
function construirUrlFiltros() {
  const formulario = document.getElementById("formFiltros");

  if (!formulario) {
    return null;
  }

  const datos = new FormData(formulario);

  const parametros = new URLSearchParams();

  /*
   * Buscar
   */
  const buscar = String(datos.get("buscar") || "").trim();

  if (buscar !== "") {
    parametros.set("buscar", buscar);
  }

  /*
   * Propiedad
   */
  const propiedad = String(datos.get("propiedad") || "").trim();

  if (propiedad !== "") {
    parametros.set("propiedad", propiedad);
  }

  /*
   * Estado
   */
  const estado = String(datos.get("estado") || "").trim();

  if (estado !== "") {
    parametros.set("estado", estado);
  }

  /*
   * Fecha desde
   */
  const fechaDesde = String(datos.get("fecha_desde") || "").trim();

  if (fechaDesde !== "") {
    parametros.set("fecha_desde", fechaDesde);
  }

  /*
   * Fecha hasta
   */
  const fechaHasta = String(datos.get("fecha_hasta") || "").trim();

  if (fechaHasta !== "") {
    parametros.set("fecha_hasta", fechaHasta);
  }

  /*
   * Al cambiar un filtro siempre volvemos
   * a la primera página.
   */
  parametros.set("pagina", "1");

  const url = new URL(LISTA_MENSAJES.pagina, window.location.href);

  url.search = parametros.toString();

  return url.href;
}

/**
 * Ejecuta el filtro automático.
 */
function ejecutarFiltroAutomatico() {
  const url = construirUrlFiltros();

  if (!url) {
    return;
  }

  window.location.href = url;
}

/*=========================================================
  INICIALIZAR SELECTS Y FECHAS
=========================================================*/

function inicializarFiltros() {
  const filtros = document.querySelectorAll(LISTA_MENSAJES.clases.filtro);

  if (!filtros.length) {
    return;
  }

  filtros.forEach(function (filtro) {
    /*
     * SELECT y DATE:
     * ejecutamos inmediatamente al cambiar.
     */
    if (filtro.tagName === "SELECT" || filtro.type === "date") {
      filtro.addEventListener("change", function () {
        ejecutarFiltroAutomatico();
      });

      return;
    }

    /*
     * Otros inputs:
     * dejamos debounce por seguridad.
     */
    filtro.addEventListener("change", function () {
      ejecutarFiltroAutomatico();
    });
  });
}

/*=========================================================
  BUSCADOR
=========================================================*/

function inicializarBusqueda() {
  const input = document.getElementById("inputBuscar");

  if (!input) {
    return;
  }

  input.addEventListener("input", function () {
    clearTimeout(temporizadorBusqueda);

    /*
     * Si el usuario borra todo,
     * aplicamos el filtro rápidamente.
     */
    if (input.value.trim() === "") {
      temporizadorBusqueda = setTimeout(function () {
        ejecutarFiltroAutomatico();
      }, 250);

      return;
    }

    /*
     * Debounce para no recargar la página
     * en cada tecla.
     */
    temporizadorBusqueda = setTimeout(function () {
      ejecutarFiltroAutomatico();
    }, LISTA_MENSAJES.debounceBusqueda);
  });

  /*
   * Enter:
   * aplicar inmediatamente.
   */
  input.addEventListener("keydown", function (evento) {
    if (evento.key === "Enter") {
      evento.preventDefault();

      clearTimeout(temporizadorBusqueda);

      ejecutarFiltroAutomatico();
    }
  });
}

/*=========================================================
  LIMPIAR BÚSQUEDA
=========================================================*/

function inicializarBotonLimpiarBusqueda() {
  const boton = document.getElementById("btnLimpiarBusqueda");

  if (!boton) {
    return;
  }

  boton.addEventListener("click", function () {
    const input = document.getElementById("inputBuscar");

    if (input) {
      input.value = "";
    }

    const formulario = document.getElementById("formFiltros");

    if (!formulario) {
      return;
    }

    /*
     * Al limpiar la búsqueda,
     * conservamos los demás filtros.
     */
    const datos = new FormData(formulario);

    datos.set("buscar", "");

    const parametros = new URLSearchParams();

    ["propiedad", "estado", "fecha_desde", "fecha_hasta"].forEach(
      function (campo) {
        const valor = String(datos.get(campo) || "").trim();

        if (valor !== "") {
          parametros.set(campo, valor);
        }
      },
    );

    parametros.set("pagina", "1");

    const url = new URL(LISTA_MENSAJES.pagina, window.location.href);

    url.search = parametros.toString();

    window.location.href = url.href;
  });
}

/*=========================================================
  VALIDACIÓN DE FECHAS
=========================================================*/

function inicializarValidacionFechas() {
  const fechaDesde = document.getElementById("fechaDesde");

  const fechaHasta = document.getElementById("fechaHasta");

  if (!fechaDesde || !fechaHasta) {
    return;
  }

  /*
   * Cuando se selecciona "Desde",
   * limitamos la fecha mínima de "Hasta".
   */
  fechaDesde.addEventListener("change", function () {
    if (fechaDesde.value) {
      fechaHasta.min = fechaDesde.value;
    }

    if (
      fechaHasta.value &&
      fechaDesde.value &&
      fechaHasta.value < fechaDesde.value
    ) {
      fechaHasta.value = fechaDesde.value;
    }

    ejecutarFiltroAutomatico();
  });

  /*
   * Cuando se selecciona "Hasta",
   * limitamos la fecha máxima de "Desde".
   */
  fechaHasta.addEventListener("change", function () {
    if (fechaHasta.value) {
      fechaDesde.max = fechaHasta.value;
    }

    if (
      fechaDesde.value &&
      fechaHasta.value &&
      fechaDesde.value > fechaHasta.value
    ) {
      fechaDesde.value = fechaHasta.value;
    }

    ejecutarFiltroAutomatico();
  });

  /*
   * Establecer límites al cargar.
   */
  if (fechaDesde.value) {
    fechaHasta.min = fechaDesde.value;
  }

  if (fechaHasta.value) {
    fechaDesde.max = fechaHasta.value;
  }
}

/*=========================================================
  ESTADO VISUAL DE LA TABLA
=========================================================*/

function inicializarEstadosMensajes() {
  const estados = document.querySelectorAll(LISTA_MENSAJES.clases.estado);

  if (!estados.length) {
    return;
  }

  estados.forEach(function (estado) {
    const valor = estado.dataset.messageStatus;

    /*
     * El estado visual real ya viene desde PHP.
     * Aquí solamente aseguramos que tenga
     * las clases correspondientes.
     */
    if (estado.classList.contains("read")) {
      estado.setAttribute("aria-label", "Mensaje leído");
    } else {
      estado.setAttribute("aria-label", "Mensaje no leído");
    }

    if (valor) {
      estado.dataset.id = valor;
    }
  });
}

/*=========================================================
  COPIAR DATOS DEL MODAL
=========================================================*/

function inicializarCopiarDatos() {
  const botonesCopiar = document.querySelectorAll("[data-copy]");

  if (!botonesCopiar.length) {
    return;
  }

  botonesCopiar.forEach(function (boton) {
    boton.addEventListener("click", async function () {
      const objetivo = boton.dataset.copy;

      if (!objetivo) {
        return;
      }

      const elemento = document.getElementById(objetivo);

      if (!elemento) {
        return;
      }

      const texto = elemento.textContent.trim();

      if (!texto) {
        return;
      }

      try {
        await navigator.clipboard.writeText(texto);

        const textoOriginal = boton.innerHTML;

        boton.innerHTML = '<i class="fa-solid fa-check"></i>';

        setTimeout(function () {
          boton.innerHTML = textoOriginal;
        }, 1200);
      } catch (error) {
        console.error("No se pudo copiar:", error);
      }
    });
  });
}

/*=========================================================
  MODAL — LIMPIEZA AL CERRAR
=========================================================*/

function inicializarLimpiezaModal() {
  const modal = document.getElementById("modalVerDetalles");

  if (!modal || typeof bootstrap === "undefined") {
    return;
  }

  modal.addEventListener("hidden.bs.modal", function () {
    /*
     * Eliminamos solamente los datos internos
     * del modal.
     */
    delete modal.dataset.idMensaje;

    delete modal.dataset.estadoMensaje;
  });
}

/*=========================================================
  EVITAR ENVÍO NORMAL DEL FORMULARIO
=========================================================*/

function inicializarFormularioFiltros() {
  const formulario = document.getElementById("formFiltros");

  if (!formulario) {
    return;
  }

  formulario.addEventListener("submit", function (evento) {
    evento.preventDefault();

    clearTimeout(temporizadorBusqueda);

    ejecutarFiltroAutomatico();
  });
}

/*=========================================================
  RESTAURAR FOCO DEL BUSCADOR
=========================================================*/

function restaurarFocoBusqueda() {
  const input = document.getElementById("inputBuscar");

  if (!input) {
    return;
  }

  /*
   * No hacemos autofocus automáticamente para
   * no alterar la navegación del usuario.
   */
}

/*=========================================================
  INICIALIZACIÓN GENERAL
=========================================================*/

function inicializarListaMensajes() {
  /*
   * Filtros
   */
  inicializarFiltros();

  /*
   * Buscador
   */
  inicializarBusqueda();

  /*
   * Limpiar búsqueda
   */
  inicializarBotonLimpiarBusqueda();

  /*
   * Fechas
   */
  inicializarValidacionFechas();

  /*
   * Botones Ver
   */
  inicializarBotonesVerMensaje();

  /*
   * Estados visuales
   */
  inicializarEstadosMensajes();

  /*
   * Copiar información
   */
  inicializarCopiarDatos();

  /*
   * Modal
   */
  inicializarLimpiezaModal();

  /*
   * Formulario
   */
  inicializarFormularioFiltros();

  /*
   * Compatibilidad / estabilidad
   */
  restaurarFocoBusqueda();
}

/*=========================================================
  DOM READY
=========================================================*/

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", inicializarListaMensajes);
} else {
  inicializarListaMensajes();
}

/*=========================================================
  EXPOSICIÓN GLOBAL
=========================================================*/

/*
 * Estas funciones quedan disponibles globalmente
 * por si modal_mensaje.php u otro JS necesita
 * utilizarlas.
 */

window.cargarMensajeEnModal = cargarMensajeEnModal;

window.formatearFecha = formatearFecha;

window.formatearFechaHora = formatearFechaHora;

window.ejecutarFiltroAutomatico = ejecutarFiltroAutomatico;
