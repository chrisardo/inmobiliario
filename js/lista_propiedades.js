/**
 * ============================================================
 * CoDevPro Technology
 * Archivo: js/adm_lista_propiedades.js
 * Módulo: Lista de Propiedades
 * ============================================================
 */

document.addEventListener("DOMContentLoaded", () => {
  inicializarModalEditar();
  inicializarModalCambiarEstado();
  inicializarBusqueda();
  inicializarValidacionEdicion();
  inicializarPreviewImagen();
});

/**
 * ============================================================
 * MODAL EDITAR
 * ============================================================
 */

function inicializarModalEditar() {
  const modal = document.getElementById("modalEditar");

  if (!modal) return;

  modal.addEventListener("show.bs.modal", (event) => {
    const button = event.relatedTarget;

    if (!button) return;

    const setValue = (id, value) => {
      const element = document.getElementById(id);

      if (element) {
        element.value = value ?? "";
      }
    };

    const setText = (id, value) => {
      const element = document.getElementById(id);

      if (element) {
        element.textContent = value ?? "—";
      }
    };

    /**
     * ----------------------------------------------------------
     * DATOS DE LA PROPIEDAD
     * ----------------------------------------------------------
     */

    setValue("edit-id", button.dataset.id);
    setValue("edit-nombre", button.dataset.nombre);
    setValue("edit-codigo", button.dataset.codigo);
    setValue("edit-precio", button.dataset.precio);
    setValue(
      "edit-precio-anterior",
      button.dataset.precioAnterior
    );
    setValue(
      "edit-tamano-area-metros",
      button.dataset.tamanoAreaMetros
    );
    setValue("edit-categoria", button.dataset.categoria);
    setValue("edit-ubicacion", button.dataset.ubicacion);

    /**
     * ----------------------------------------------------------
     * FECHA DE REGISTRO
     * ----------------------------------------------------------
     */

    setText(
      "edit-fecha-registro",
      formatearFecha(button.dataset.fechaRegistro)
    );

    /**
     * ----------------------------------------------------------
     * FECHA DE ACTUALIZACIÓN
     * ----------------------------------------------------------
     */

    setText(
      "edit-fecha-actualizacion",
      formatearFecha(button.dataset.fechaActualizacion)
    );
  });
}

/**
 * ============================================================
 * MODAL ACTIVAR / INHABILITAR PROPIEDAD
 * ============================================================
 */

function inicializarModalCambiarEstado() {
  const modal = document.getElementById("modalCambiarEstado");

  if (!modal) {
    console.warn(
      "No se encontró el modal #modalCambiarEstado"
    );

    return;
  }

  /**
   * ----------------------------------------------------------
   * ELEMENTOS DEL MODAL
   * ----------------------------------------------------------
   */

  const inputId = document.getElementById(
    "cambiar-estado-id"
  );

  const inputNuevoEstado = document.getElementById(
    "cambiar-estado-nuevo"
  );

  const nombreElemento = document.getElementById(
    "cambiar-estado-nombre"
  );

  const estadoActualTexto = document.getElementById(
    "estadoActualTexto"
  );

  const nuevoEstadoTexto = document.getElementById(
    "nuevoEstadoTexto"
  );

  const tituloCambioEstado = document.getElementById(
    "tituloCambioEstado"
  );

  const iconoModalEstado = document.getElementById(
    "iconoModalEstado"
  );

  const iconoEstadoConfirmacion = document.getElementById(
    "iconoEstadoConfirmacion"
  );

  const iconoBtnCambioEstado = document.getElementById(
    "iconoBtnCambioEstado"
  );

  const btnConfirmar = document.getElementById(
    "btnConfirmarCambioEstado"
  );

  const alerta = document.getElementById(
    "alertaCambioEstado"
  );

  const form = document.getElementById(
    "formCambiarEstadoPropiedad"
  );

  /**
   * ----------------------------------------------------------
   * ABRIR MODAL
   * ----------------------------------------------------------
   */

  modal.addEventListener("show.bs.modal", (event) => {
    const button = event.relatedTarget;

    if (!button) {
      console.warn(
        "No se encontró el botón que abrió el modal."
      );

      return;
    }

    /**
     * --------------------------------------------------------
     * OBTENER DATOS
     * --------------------------------------------------------
     */

    const id = button.dataset.id || "";

    const nombre = button.dataset.nombre || "Propiedad";

    /**
     * Puede venir:
     *
     * data-estado="ACTIVO"
     *
     * o:
     *
     * data-estado="INHABILITADO"
     *
     * También soportamos:
     *
     * data-eliminado="0"
     * data-eliminado="1"
     */

    let estadoActual = button.dataset.estado || "";

    const eliminado = button.dataset.eliminado;

    /**
     * --------------------------------------------------------
     * NORMALIZAR ESTADO
     * --------------------------------------------------------
     */

    if (estadoActual !== "") {
      estadoActual = String(estadoActual)
        .trim()
        .toUpperCase();
    }

    let estadoNumerico;

    /**
     * Si viene directamente como 0 / 1
     */

    if (
      estadoActual === "0" ||
      estadoActual === "1"
    ) {
      estadoNumerico = Number(estadoActual);
    }

    /**
     * Si viene como ACTIVO / INHABILITADO
     */

    else if (estadoActual === "ACTIVO") {
      estadoNumerico = 0;
    }

    else if (
      estadoActual === "INHABILITADO" ||
      estadoActual === "INACTIVO"
    ) {
      estadoNumerico = 1;
    }

    /**
     * Si no vino data-estado, utilizar data-eliminado
     */

    else if (
      eliminado !== undefined &&
      eliminado !== ""
    ) {
      estadoNumerico =
        Number(eliminado) === 1 ? 1 : 0;
    }

    /**
     * Por seguridad, si no podemos determinarlo,
     * asumimos ACTIVO.
     */

    else {
      estadoNumerico = 0;
    }

    /**
     * --------------------------------------------------------
     * CALCULAR NUEVO ESTADO
     * --------------------------------------------------------
     *
     * 0 = ACTIVO
     * 1 = INHABILITADO
     */

    const nuevoEstado =
      estadoNumerico === 0 ? 1 : 0;

    /**
     * --------------------------------------------------------
     * ASIGNAR INPUTS
     * --------------------------------------------------------
     */

    if (inputId) {
      inputId.value = id;
    }

    if (inputNuevoEstado) {
      inputNuevoEstado.value = nuevoEstado;
    }

    if (nombreElemento) {
      nombreElemento.textContent = nombre;
    }

    /**
     * --------------------------------------------------------
     * LIMPIAR ALERTA
     * --------------------------------------------------------
     */

    if (alerta) {
      alerta.className = "alert d-none";
      alerta.textContent = "";
    }

    /**
     * --------------------------------------------------------
     * CONFIGURAR MODAL SEGÚN ESTADO
     * --------------------------------------------------------
     */

    if (estadoNumerico === 0) {
      /**
       * ======================================================
       * ACTUALMENTE ACTIVO
       * NUEVO ESTADO = INHABILITADO
       * ======================================================
       */

      if (estadoActualTexto) {
        estadoActualTexto.textContent = "ACTIVO";

        estadoActualTexto.className =
          "badge bg-success-subtle text-success border border-success-subtle";
      }

      if (nuevoEstadoTexto) {
        nuevoEstadoTexto.textContent =
          "INHABILITADO";

        nuevoEstadoTexto.className =
          "badge bg-danger-subtle text-danger border border-danger-subtle";
      }

      if (tituloCambioEstado) {
        tituloCambioEstado.textContent =
          "Inhabilitar propiedad";
      }

      if (iconoModalEstado) {
        iconoModalEstado.className =
          "fa-solid fa-toggle-off me-2";
      }

      if (iconoEstadoConfirmacion) {
        iconoEstadoConfirmacion.innerHTML = `
          <i class="fa-solid fa-toggle-off text-danger"></i>
        `;
      }

      if (iconoBtnCambioEstado) {
        iconoBtnCambioEstado.className =
          "fa-solid fa-toggle-off me-1";
      }

      if (btnConfirmar) {
        btnConfirmar.className =
          "btn btn-danger";

        btnConfirmar.innerHTML = `
          <i class="fa-solid fa-toggle-off me-1"></i>
          Inhabilitar propiedad
        `;

        btnConfirmar.disabled = false;
      }
    }

    else {
      /**
       * ======================================================
       * ACTUALMENTE INHABILITADO
       * NUEVO ESTADO = ACTIVO
       * ======================================================
       */

      if (estadoActualTexto) {
        estadoActualTexto.textContent =
          "INHABILITADO";

        estadoActualTexto.className =
          "badge bg-danger-subtle text-danger border border-danger-subtle";
      }

      if (nuevoEstadoTexto) {
        nuevoEstadoTexto.textContent = "ACTIVO";

        nuevoEstadoTexto.className =
          "badge bg-success-subtle text-success border border-success-subtle";
      }

      if (tituloCambioEstado) {
        tituloCambioEstado.textContent =
          "Activar propiedad";
      }

      if (iconoModalEstado) {
        iconoModalEstado.className =
          "fa-solid fa-toggle-on me-2";
      }

      if (iconoEstadoConfirmacion) {
        iconoEstadoConfirmacion.innerHTML = `
          <i class="fa-solid fa-toggle-on text-success"></i>
        `;
      }

      if (iconoBtnCambioEstado) {
        iconoBtnCambioEstado.className =
          "fa-solid fa-toggle-on me-1";
      }

      if (btnConfirmar) {
        btnConfirmar.className =
          "btn btn-success";

        btnConfirmar.innerHTML = `
          <i class="fa-solid fa-toggle-on me-1"></i>
          Activar propiedad
        `;

        btnConfirmar.disabled = false;
      }
    }

    /**
     * --------------------------------------------------------
     * DEBUG
     * --------------------------------------------------------
     *
     * Puedes revisar estos valores en F12 > Console.
     */

    console.log(
      "Cambiar estado de propiedad:",
      {
        id,
        nombre,
        estadoActual,
        estadoNumerico,
        nuevoEstado
      }
    );
  });

  /**
   * ==========================================================
   * BOTÓN CONFIRMAR
   * ==========================================================
   */

  if (btnConfirmar && form) {
    btnConfirmar.addEventListener("click", () => {
      const id = inputId
        ? inputId.value.trim()
        : "";

      const nuevoEstado = inputNuevoEstado
        ? inputNuevoEstado.value.trim()
        : "";

      /**
       * ------------------------------------------------------
       * VALIDAR ID
       * ------------------------------------------------------
       */

      if (!id || Number(id) <= 0) {
        mostrarAlertaCambioEstado(
          "No se pudo identificar la propiedad seleccionada.",
          "danger"
        );

        return;
      }

      /**
       * ------------------------------------------------------
       * VALIDAR NUEVO ESTADO
       * ------------------------------------------------------
       */

      if (
        nuevoEstado !== "0" &&
        nuevoEstado !== "1"
      ) {
        mostrarAlertaCambioEstado(
          "El estado seleccionado no es válido.",
          "danger"
        );

        return;
      }

      /**
       * ------------------------------------------------------
       * EVITAR DOBLE ENVÍO
       * ------------------------------------------------------
       */

      btnConfirmar.disabled = true;

      btnConfirmar.innerHTML = `
        <span
          class="spinner-border spinner-border-sm me-1"
          role="status"
          aria-hidden="true">
        </span>
        Procesando...
      `;

      /**
       * ------------------------------------------------------
       * ENVIAR FORMULARIO
       * ------------------------------------------------------
       */

      form.submit();
    });
  }

  /**
   * ==========================================================
   * VALIDACIÓN DEL FORMULARIO
   * ==========================================================
   *
   * También protegemos el envío si el formulario se envía
   * mediante JavaScript u otro mecanismo.
   */

  if (form) {
    form.addEventListener("submit", (event) => {
      const id = inputId
        ? inputId.value.trim()
        : "";

      const nuevoEstado = inputNuevoEstado
        ? inputNuevoEstado.value.trim()
        : "";

      if (!id || Number(id) <= 0) {
        event.preventDefault();

        mostrarAlertaCambioEstado(
          "No se pudo identificar la propiedad seleccionada.",
          "danger"
        );

        return;
      }

      if (
        nuevoEstado !== "0" &&
        nuevoEstado !== "1"
      ) {
        event.preventDefault();

        mostrarAlertaCambioEstado(
          "El estado seleccionado no es válido.",
          "danger"
        );

        return;
      }
    });
  }
}

/**
 * ============================================================
 * MOSTRAR ALERTA DEL MODAL
 * ============================================================
 */

function mostrarAlertaCambioEstado(
  mensaje,
  tipo = "danger"
) {
  const alerta = document.getElementById(
    "alertaCambioEstado"
  );

  if (!alerta) return;

  alerta.className =
    `alert alert-${tipo}`;

  alerta.textContent = mensaje;

  alerta.classList.remove("d-none");
}

/**
 * ============================================================
 * FORMATEAR FECHA
 * ============================================================
 */

function formatearFecha(fecha) {
  if (
    !fecha ||
    fecha === "0000-00-00"
  ) {
    return "—";
  }

  const partes = fecha.split("-");

  if (partes.length === 3) {
    const anio = partes[0];
    const mes = partes[1];
    const dia = partes[2];

    return `${dia}/${mes}/${anio}`;
  }

  return fecha;
}

/**
 * ============================================================
 * BÚSQUEDA
 * ============================================================
 */

function inicializarBusqueda() {
  const form = document.getElementById(
    "formBuscar"
  );

  const categoria = document.getElementById(
    "filtroCategoria"
  );

  if (categoria && form) {
    categoria.addEventListener(
      "change",
      () => {
        form.submit();
      }
    );
  }
}

/**
 * ============================================================
 * VALIDACIÓN DEL FORMULARIO DE EDICIÓN
 * ============================================================
 */

function inicializarValidacionEdicion() {
  const form = document.querySelector(
    "#modalEditar form"
  );

  if (!form) return;

  const alerta = document.getElementById(
    "alertaPropiedad"
  );

  form.addEventListener("submit", (event) => {
    const errores = [];

    const getValue = (id) => {
      const element =
        document.getElementById(id);

      return element
        ? element.value.trim()
        : "";
    };

    const nombre =
      getValue("edit-nombre");

    const codigo =
      getValue("edit-codigo");

    const precio =
      getValue("edit-precio");

    const precioAnterior =
      getValue("edit-precio-anterior");

    const categoria =
      getValue("edit-categoria");

    const area =
      getValue("edit-tamano-area-metros");

    const ubicacion =
      getValue("edit-ubicacion");

    /**
     * Nombre
     */

    if (!nombre) {
      errores.push(
        "El nombre de la propiedad es obligatorio."
      );
    }

    /**
     * Código
     */

    if (!codigo) {
      errores.push(
        "El código de la propiedad es obligatorio."
      );
    }

    /**
     * Precio
     */

    if (
      !precio ||
      Number.isNaN(Number(precio)) ||
      Number(precio) <= 0
    ) {
      errores.push(
        "El precio debe ser mayor que 0."
      );
    }

    /**
     * Precio anterior
     */

    if (
      precioAnterior !== "" &&
      (
        Number.isNaN(
          Number(precioAnterior)
        ) ||
        Number(precioAnterior) < 0
      )
    ) {
      errores.push(
        "El precio anterior no es válido."
      );
    }

    /**
     * Área
     */

    if (
      !area ||
      Number.isNaN(Number(area)) ||
      Number(area) <= 0
    ) {
      errores.push(
        "El área debe ser mayor que 0."
      );
    }

    /**
     * Categoría
     */

    if (!categoria) {
      errores.push(
        "Debes seleccionar una categoría."
      );
    }

    /**
     * Ubicación
     */

    if (!ubicacion) {
      errores.push(
        "La ubicación es obligatoria."
      );
    }

    /**
     * Mostrar errores
     */

    if (errores.length > 0) {
      event.preventDefault();

      if (alerta) {
        alerta.className =
          "alert alert-danger";

        alerta.innerHTML = `
          <ul class="mb-0">
            ${errores
              .map(
                (error) =>
                  `<li>${escapeHtml(error)}</li>`
              )
              .join("")}
          </ul>
        `;

        alerta.classList.remove(
          "d-none"
        );
      }

      return;
    }

    if (alerta) {
      alerta.className =
        "alert d-none";

      alerta.innerHTML = "";
    }
  });
}

/**
 * ============================================================
 * PREVISUALIZACIÓN DE IMAGEN
 * ============================================================
 */

function inicializarPreviewImagen() {
  const input =
    document.getElementById("edit-imagen");

  const previewContainer =
    document.getElementById(
      "previewImagen"
    );

  const previewImg =
    document.getElementById(
      "previewImg"
    );

  const imgSize =
    document.getElementById(
      "imgSize"
    );

  const imgTipo =
    document.getElementById(
      "imgTipo"
    );

  if (
    !input ||
    !previewContainer ||
    !previewImg
  ) {
    return;
  }

  input.addEventListener(
    "change",
    () => {
      if (
        !input.files ||
        input.files.length === 0
      ) {
        previewContainer.classList.add(
          "d-none"
        );

        return;
      }

      const file =
        input.files[0];

      const tiposPermitidos = [
        "image/jpeg",
        "image/png",
        "image/webp"
      ];

      if (
        !tiposPermitidos.includes(
          file.type
        )
      ) {
        previewContainer.classList.add(
          "d-none"
        );

        return;
      }

      if (imgTipo) {
        imgTipo.textContent =
          file.type;
      }

      if (imgSize) {
        imgSize.textContent =
          (
            file.size / 1024
          ).toFixed(2) +
          " KB";
      }

      const reader =
        new FileReader();

      reader.onload = (event) => {
        previewImg.src =
          event.target.result;

        previewContainer.classList.remove(
          "d-none"
        );
      };

      reader.readAsDataURL(file);
    }
  );
}

/**
 * ============================================================
 * SEGURIDAD PARA MENSAJES INSERTADOS EN HTML
 * ============================================================
 */

function escapeHtml(value) {
  return String(value)
    .replaceAll(
      "&",
      "&amp;"
    )
    .replaceAll(
      "<",
      "&lt;"
    )
    .replaceAll(
      ">",
      "&gt;"
    )
    .replaceAll(
      '"',
      "&quot;"
    )
    .replaceAll(
      "'",
      "&#039;"
    );
}