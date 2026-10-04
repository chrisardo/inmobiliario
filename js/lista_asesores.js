// =========================================================
// CoDevPro Technology
// Archivo: js/lista_asesores.js
// Módulo: Lista de Asesores
// Sistema: Inmobiliario
// =========================================================

"use strict";

document.addEventListener("DOMContentLoaded", function () {
  /* =====================================================
       CONSTANTES
    ===================================================== */

  const TIPOS_IMAGEN_PERMITIDOS = ["image/jpeg", "image/png", "image/webp"];

  const MAX_TAMANO_IMAGEN = 2.7 * 1024 * 1024;

  /* =====================================================
       ELEMENTOS PRINCIPALES
    ===================================================== */

  const modalEditar = document.getElementById("modalEditar");
  const inputBuscar = document.getElementById("inputBuscar");
  const formBuscar = document.getElementById("formBuscar");

  /* =====================================================
       UTILIDAD - ESCAPAR HTML
    ===================================================== */

  function escapeHtml(text) {
    const div = document.createElement("div");

    div.textContent = text ?? "";

    return div.innerHTML;
  }

  /* =====================================================
       OBTENER ELEMENTOS DEL MODAL
    ===================================================== */

  function obtenerElementosImagen() {
    return {
      previewCont: document.getElementById("previewImagen"),

      previewImg: document.getElementById("previewImg"),

      imgNombre: document.getElementById("imgNombre"),

      imgSize: document.getElementById("imgSize"),

      imgTipo: document.getElementById("imgTipo"),
    };
  }

  /* =====================================================
       LIMPIAR PREVIEW
    ===================================================== */

  function limpiarPreview() {
    const elementos = obtenerElementosImagen();

    if (elementos.previewCont) {
      elementos.previewCont.classList.add("d-none");
    }

    if (elementos.previewImg) {
      elementos.previewImg.removeAttribute("src");
    }

    if (elementos.imgNombre) {
      elementos.imgNombre.textContent = "";
    }

    if (elementos.imgSize) {
      elementos.imgSize.textContent = "";
    }

    if (elementos.imgTipo) {
      elementos.imgTipo.textContent = "";
    }
  }

  /* =====================================================
       MOSTRAR ALERTA DEL MODAL
    ===================================================== */

  function mostrarAlerta(errores) {
    const alerta = document.getElementById("alertaAsesor");

    if (!alerta) {
      return;
    }

    if (!Array.isArray(errores)) {
      errores = ["Se produjo un error de validación."];
    }

    alerta.className = "alert alert-danger";

    alerta.innerHTML = `
            <div class="fw-semibold mb-2">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                Revisa los siguientes datos:

            </div>

            <ul class="mb-0">

                ${errores
                  .map(function (error) {
                    return `
                            <li>
                                ${escapeHtml(error)}
                            </li>
                        `;
                  })
                  .join("")}

            </ul>
        `;
  }

  /* =====================================================
       OCULTAR ALERTA
    ===================================================== */

  function ocultarAlerta() {
    const alerta = document.getElementById("alertaAsesor");

    if (!alerta) {
      return;
    }

    alerta.className = "alert d-none";

    alerta.innerHTML = "";
  }

  /* =====================================================
       OBTENER VALOR DE CAMPO
    ===================================================== */

  function obtenerValor(id) {
    const elemento = document.getElementById(id);

    if (!elemento) {
      return "";
    }

    return String(elemento.value ?? "").trim();
  }

  /* =====================================================
       VALIDAR EMAIL
    ===================================================== */

  function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return regex.test(email);
  }

  /* =====================================================
       VALIDAR CELULAR
    ===================================================== */

  function validarCelular(celular) {
    const regex = /^[0-9+\-\s()]{7,20}$/;

    return regex.test(celular);
  }

  /* =====================================================
       FORMATEAR TAMAÑO DE ARCHIVO
    ===================================================== */

  function formatearTamano(bytes) {
    if (!Number.isFinite(bytes)) {
      return "";
    }

    const sizeKB = bytes / 1024;

    if (sizeKB >= 1024) {
      const sizeMB = bytes / (1024 * 1024);

      return sizeMB.toFixed(2) + " MB";
    }

    return sizeKB.toFixed(2) + " KB";
  }

  /* =====================================================
       OBTENER TIPO DE IMAGEN
    ===================================================== */

  function obtenerTextoTipoImagen(tipo) {
    switch (tipo) {
      case "image/jpeg":
        return "JPEG";

      case "image/png":
        return "PNG";

      case "image/webp":
        return "WEBP";

      default:
        return tipo || "Desconocido";
    }
  }

  /* =====================================================
       MODAL EDITAR ASESOR
    ===================================================== */

  if (modalEditar) {
    modalEditar.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;

      if (!button) {
        return;
      }

      /* =========================================
                   DATOS DEL ASESOR
                ========================================= */

      const id = button.dataset.id || "";

      const nombre = button.dataset.nombre || "";

      const apellidos = button.dataset.apellidos || "";

      const email = button.dataset.email || "";

      const celular = button.dataset.celular || "";

      const cargo = button.dataset.cargo || "";

      /* =========================================
                   CAMPOS DEL MODAL
                ========================================= */

      const inputId = document.getElementById("edit-id");

      const inputNombre = document.getElementById("edit-nombre");

      const inputApellidos = document.getElementById("edit-apellidos");

      const inputEmail = document.getElementById("edit-email");

      const inputCelular = document.getElementById("edit-celular");

      const inputCargo = document.getElementById("edit-cargo");

      const inputImagen = document.getElementById("edit-imagen");

      /* =========================================
                   ASIGNAR VALORES
                ========================================= */

      if (inputId) {
        inputId.value = id;
      }

      if (inputNombre) {
        inputNombre.value = nombre;
      }

      if (inputApellidos) {
        inputApellidos.value = apellidos;
      }

      if (inputEmail) {
        inputEmail.value = email;
      }

      if (inputCelular) {
        inputCelular.value = celular;
      }

      if (inputCargo) {
        inputCargo.value = cargo;
      }

      /* =========================================
                   LIMPIAR NUEVA IMAGEN
                ========================================= */

      if (inputImagen) {
        inputImagen.value = "";
      }

      limpiarPreview();

      ocultarAlerta();
    });

    /* =================================================
           MODAL CERRADO
        ================================================= */

    modalEditar.addEventListener("hidden.bs.modal", function () {
      const inputImagen = document.getElementById("edit-imagen");

      if (inputImagen) {
        inputImagen.value = "";
      }

      limpiarPreview();

      ocultarAlerta();
    });

    /* =================================================
           FORMULARIO DE EDICIÓN
        ================================================= */

    const form = modalEditar.querySelector("form");

    if (form) {
      form.addEventListener("submit", function (event) {
        ocultarAlerta();

        const errores = [];

        /* =====================================
                       CAMPOS
                    ===================================== */

        const idAsesor = obtenerValor("edit-id");

        const nombre = obtenerValor("edit-nombre");

        const apellidos = obtenerValor("edit-apellidos");

        const email = obtenerValor("edit-email");

        const celular = obtenerValor("edit-celular");

        const cargo = obtenerValor("edit-cargo");

        const imagenInput = document.getElementById("edit-imagen");

        /* =====================================
                       ID
                    ===================================== */

        if (
          idAsesor === "" ||
          !/^\d+$/.test(idAsesor) ||
          parseInt(idAsesor, 10) <= 0
        ) {
          errores.push("El asesor seleccionado no es válido.");
        }

        /* =====================================
                       NOMBRE
                    ===================================== */

        if (nombre === "") {
          errores.push("El nombre es obligatorio.");
        } else if (nombre.length > 100) {
          errores.push("El nombre no puede superar los 100 caracteres.");
        }

        /* =====================================
                       APELLIDOS
                    ===================================== */

        if (apellidos === "") {
          errores.push("Los apellidos son obligatorios.");
        } else if (apellidos.length > 150) {
          errores.push("Los apellidos no pueden superar los 150 caracteres.");
        }

        /* =====================================
                       EMAIL
                    ===================================== */

        if (email === "") {
          errores.push("El correo electrónico es obligatorio.");
        } else if (!validarEmail(email)) {
          errores.push("Ingrese un correo electrónico válido.");
        } else if (email.length > 150) {
          errores.push(
            "El correo electrónico no puede superar los 150 caracteres.",
          );
        }

        /* =====================================
                       CELULAR
                    ===================================== */

        if (celular === "") {
          errores.push("El celular es obligatorio.");
        } else if (!validarCelular(celular)) {
          errores.push("El número de celular no tiene un formato válido.");
        }

        /* =====================================
                       CARGO
                    ===================================== */

        if (cargo === "") {
          errores.push("El cargo es obligatorio.");
        } else if (cargo.length > 100) {
          errores.push("El cargo no puede superar los 100 caracteres.");
        }

        /* =====================================
                       IMAGEN
                    ===================================== */

        if (imagenInput && imagenInput.files && imagenInput.files.length > 0) {
          const archivo = imagenInput.files[0];

          /* =================================
                           ARCHIVO VACÍO
                        ================================= */

          if (archivo.size <= 0) {
            errores.push("La imagen seleccionada no es válida.");
          }

          /* =================================
                           TIPO
                        ================================= */

          if (!TIPOS_IMAGEN_PERMITIDOS.includes(archivo.type)) {
            errores.push("La imagen debe ser JPG, JPEG, PNG o WEBP.");
          }

          /* =================================
                           TAMAÑO
                        ================================= */

          if (archivo.size > MAX_TAMANO_IMAGEN) {
            errores.push("La imagen no debe superar 2.7 MB.");
          }
        }

        /* =====================================
                       MOSTRAR ERRORES
                    ===================================== */

        if (errores.length > 0) {
          event.preventDefault();

          mostrarAlerta(errores);

          return;
        }

        /* =====================================
                       EVITAR DOBLE ENVÍO
                    ===================================== */

        const btnGuardar = form.querySelector('[type="submit"]');

        if (btnGuardar) {
          if (btnGuardar.disabled) {
            event.preventDefault();

            return;
          }

          const textoOriginal = btnGuardar.innerHTML;

          btnGuardar.dataset.textoOriginal = textoOriginal;

          btnGuardar.disabled = true;

          btnGuardar.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm me-2"
                                role="status"
                                aria-hidden="true">
                            </span>

                            Guardando...
                        `;
        }
      });
    }
  }

  /* =====================================================
       PREVISUALIZACIÓN DE IMAGEN
    ===================================================== */

  const inputImagen = document.getElementById("edit-imagen");

  if (inputImagen) {
    inputImagen.addEventListener("change", function () {
      limpiarPreview();

      ocultarAlerta();

      /* =========================================
                   SIN ARCHIVO
                ========================================= */

      if (!this.files || this.files.length === 0) {
        return;
      }

      const archivo = this.files[0];

      /* =========================================
                   VALIDAR ARCHIVO VACÍO
                ========================================= */

      if (archivo.size <= 0) {
        this.value = "";

        mostrarAlerta(["La imagen seleccionada no es válida."]);

        return;
      }

      /* =========================================
                   VALIDAR TIPO
                ========================================= */

      if (!TIPOS_IMAGEN_PERMITIDOS.includes(archivo.type)) {
        this.value = "";

        mostrarAlerta(["La imagen debe ser JPG, JPEG, PNG o WEBP."]);

        return;
      }

      /* =========================================
                   VALIDAR TAMAÑO
                ========================================= */

      if (archivo.size > MAX_TAMANO_IMAGEN) {
        this.value = "";

        mostrarAlerta(["La imagen no debe superar 2.7 MB."]);

        return;
      }

      /* =========================================
                   ELEMENTOS PREVIEW
                ========================================= */

      const elementos = obtenerElementosImagen();

      /* =========================================
                   NOMBRE
                ========================================= */

      if (elementos.imgNombre) {
        elementos.imgNombre.textContent = archivo.name;
      }

      /* =========================================
                   TIPO
                ========================================= */

      if (elementos.imgTipo) {
        elementos.imgTipo.textContent = obtenerTextoTipoImagen(archivo.type);
      }

      /* =========================================
                   TAMAÑO
                ========================================= */

      if (elementos.imgSize) {
        elementos.imgSize.textContent = formatearTamano(archivo.size);
      }

      /* =========================================
                   FILE READER
                ========================================= */

      const reader = new FileReader();

      reader.onload = function (event) {
        if (elementos.previewImg) {
          elementos.previewImg.src = event.target.result;
        }

        if (elementos.previewCont) {
          elementos.previewCont.classList.remove("d-none");
        }
      };

      reader.onerror = function () {
        inputImagen.value = "";

        limpiarPreview();

        mostrarAlerta(["No fue posible leer la imagen seleccionada."]);
      };

      reader.readAsDataURL(archivo);
    });
  }

  /* =====================================================
       BÚSQUEDA AUTOMÁTICA
    ===================================================== */

  if (inputBuscar && formBuscar) {
    let delayTimer = null;

    inputBuscar.addEventListener("input", function () {
      clearTimeout(delayTimer);

      delayTimer = setTimeout(function () {
        const valor = inputBuscar.value.trim();

        const url = new URL(window.location.href);

        const busquedaActual = (url.searchParams.get("buscar") || "").trim();

        /* =================================
                           EVITAR RECARGA INNECESARIA
                        ================================= */

        if (valor === busquedaActual) {
          return;
        }

        /* =================================
                           VOLVER A LA PRIMERA PÁGINA
                        ================================= */

        if (valor !== busquedaActual) {
          url.searchParams.delete("pagina");
        }

        /* =================================
                           SI HAY BÚSQUEDA
                        ================================= */

        if (valor !== "") {
          url.searchParams.set("buscar", valor);
        } else {
          url.searchParams.delete("buscar");
        }

        window.location.href = url.toString();
      }, 500);
    });
  }

  /* =====================================================
       EVITAR ENVÍO VACÍO
    ===================================================== */

  if (formBuscar && inputBuscar) {
    formBuscar.addEventListener("submit", function (event) {
      inputBuscar.value = inputBuscar.value.trim();

      /* =========================================
                   BUSCADOR VACÍO
                ========================================= */

      if (inputBuscar.value === "") {
        event.preventDefault();

        const url = new URL(
          formBuscar.action || window.location.href,
          window.location.origin,
        );

        url.searchParams.delete("buscar");

        url.searchParams.delete("pagina");

        window.location.href = url.toString();
      }
    });
  }

  /* =====================================================
       LIMPIAR ALERTA AL MODIFICAR CAMPOS
    ===================================================== */

  if (modalEditar) {
    const campos = modalEditar.querySelectorAll("input, textarea, select");

    campos.forEach(function (campo) {
      campo.addEventListener("input", function () {
        ocultarAlerta();
      });

      campo.addEventListener("change", function () {
        ocultarAlerta();
      });
    });
  }

  /* =====================================================
       CAMBIO DE ESTADO
       
       IMPORTANTE:
       No se utiliza confirm().
       
       Al hacer clic en Activar o Desactivar,
       el enlace continúa directamente hacia:
       
       procesar_lista_asesores.php?cambiar_estado=ID
       
       El servidor se encarga de cambiar el estado.
    ===================================================== */

  const botonesEstado = document.querySelectorAll('a[href*="cambiar_estado="]');

  botonesEstado.forEach(function (boton) {
    /*
     * No hacemos nada aquí.
     *
     * El enlace <a> ya tiene la URL correcta
     * y debe ejecutarse directamente.
     *
     * Esto permite:
     *
     * ACTIVAR   → clic → cambio inmediato
     * DESACTIVAR → clic → cambio inmediato
     *
     * Sin confirm()
     * Sin alert()
     * Sin confirmación adicional.
     */

    boton.addEventListener("click", function () {
      /*
       * Evitar doble clic mientras
       * el navegador procesa la petición.
       */

      if (boton.dataset.procesando === "1") {
        return;
      }

      boton.dataset.procesando = "1";

      boton.style.pointerEvents = "none";

      boton.style.opacity = "0.6";

      /*
       * NO usamos preventDefault().
       *
       * El navegador continuará
       * normalmente hacia el href.
       */
    });
  });
  /* =====================================================
       MODAL VER DETALLE DEL ASESOR
    ===================================================== */

  const modalVerAsesor = document.getElementById("modalVerAsesor");
  const detalleWhatsapp = document.getElementById("detalleWhatsapp");
  if (modalVerAsesor) {
    modalVerAsesor.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;

      if (!button) {
        return;
      }

      /* =========================================
                   DATOS DEL ASESOR
                ========================================= */

      const id = button.dataset.id || "";

      const idUser = button.dataset.idUser || "";

      const nombre = button.dataset.nombre || "";

      const apellidos = button.dataset.apellidos || "";

      const email = button.dataset.email || "";

      const celular = button.dataset.celular || "";

      const cargo = button.dataset.cargo || "";

      const estado = (button.dataset.estado || "ACTIVO").toUpperCase();

      const fechaRegistro = button.dataset.fechaRegistro || "Sin fecha";

      const fechaActualizacion =
        button.dataset.fechaActualizacion || "Sin actualización";

      /* =========================================
                   NOMBRE COMPLETO
                ========================================= */

      let nombreCompleto = (nombre + " " + apellidos).trim();

      if (nombreCompleto === "") {
        nombreCompleto = "Sin nombre";
      }

      /* =========================================
                   CAMPOS
                ========================================= */

      const detalleId = document.getElementById("detalleId");

      const detalleIdUser = document.getElementById("detalleIdUser");

      const detalleNombre = document.getElementById("detalleNombre");

      const detalleNombreCampo = document.getElementById("detalleNombreCampo");

      const detalleApellidos = document.getElementById("detalleApellidos");

      const detalleEmail = document.getElementById("detalleEmail");

      const detalleCelular = document.getElementById("detalleCelular");

      const detalleCargo = document.getElementById("detalleCargo");

      const detalleFechaRegistro = document.getElementById(
        "detalleFechaRegistro",
      );

      const detalleFechaActualizacion = document.getElementById(
        "detalleFechaActualizacion",
      );

      const detalleEstado = document.getElementById("detalleEstado");

      const detalleEstadoTexto = document.getElementById("detalleEstadoTexto");
      /* ========================================= WHATSAPP ========================================= */
      if (detalleWhatsapp) {
        /* * Limpiar el número para WhatsApp. * * Se conservan únicamente los números. * Ejemplo: * * +51 999 888 777 * ↓ * 51999888777 */
        const telefonoWhatsapp = celular.replace(/\D/g, "");
        if (telefonoWhatsapp !== "") {
          const mensaje = encodeURIComponent(
            "Hola " +
              nombre +
              ", me comunico contigo desde el sistema inmobiliario.",
          );
          detalleWhatsapp.href =
            "https://wa.me/" + telefonoWhatsapp + "?text=" + mensaje;
          detalleWhatsapp.classList.remove("disabled");
          detalleWhatsapp.removeAttribute("aria-disabled");
          detalleWhatsapp.style.pointerEvents = "";
          detalleWhatsapp.style.opacity = "";
        } else {
          /* * Si el asesor no tiene celular, * deshabilitamos el botón. */
          detalleWhatsapp.href = "#";
          detalleWhatsapp.classList.add("disabled");
          detalleWhatsapp.setAttribute("aria-disabled", "true");
          detalleWhatsapp.style.pointerEvents = "none";
          detalleWhatsapp.style.opacity = "0.55";
        }
      }

      /* =========================================
                   ASIGNAR DATOS
                ========================================= */

      if (detalleId) {
        detalleId.textContent = id;
      }

      if (detalleIdUser) {
        detalleIdUser.textContent = idUser !== "" ? idUser : "—";
      }

      if (detalleNombre) {
        detalleNombre.textContent = nombreCompleto;
      }

      if (detalleNombreCampo) {
        detalleNombreCampo.textContent = nombre !== "" ? nombre : "Sin nombre";
      }

      if (detalleApellidos) {
        detalleApellidos.textContent =
          apellidos !== "" ? apellidos : "Sin apellidos";
      }

      if (detalleEmail) {
        detalleEmail.textContent = email !== "" ? email : "Sin correo";
      }

      if (detalleCelular) {
        detalleCelular.textContent = celular !== "" ? celular : "Sin celular";
      }

      if (detalleCargo) {
        detalleCargo.textContent = cargo !== "" ? cargo : "Sin cargo";
      }

      if (detalleFechaRegistro) {
        detalleFechaRegistro.textContent = fechaRegistro;
      }

      if (detalleFechaActualizacion) {
        detalleFechaActualizacion.textContent = fechaActualizacion;
      }

      /* =========================================
                   ESTADO
                ========================================= */

      if (detalleEstado) {
        detalleEstado.classList.remove("active", "inactive");

        if (estado === "ACTIVO") {
          detalleEstado.classList.add("active");
        } else {
          detalleEstado.classList.add("inactive");
        }
      }

      if (detalleEstadoTexto) {
        detalleEstadoTexto.textContent =
          estado === "ACTIVO" ? "Activo" : "Inactivo";
      }

      /* =========================================
                   IMAGEN
                   
                   Tomamos la imagen directamente de
                   la fila de la tabla para no duplicar
                   el LONG BLOB dentro del botón.
                ========================================= */

      const detalleImagen = document.getElementById("detalleImagen");

      const detallePlaceholder = document.getElementById(
        "detalleAvatarPlaceholder",
      );

      const fila = button.closest("tr");

      let imagenSrc = "";

      if (fila) {
        const imagenFila = fila.querySelector(".asesor-avatar img");

        if (imagenFila && imagenFila.src) {
          imagenSrc = imagenFila.src;
        }
      }

      if (detalleImagen && detallePlaceholder) {
        if (imagenSrc !== "") {
          detalleImagen.src = imagenSrc;

          detalleImagen.alt = nombreCompleto;

          detalleImagen.classList.remove("d-none");

          detallePlaceholder.classList.add("d-none");
        } else {
          detalleImagen.removeAttribute("src");

          detalleImagen.classList.add("d-none");

          detallePlaceholder.classList.remove("d-none");

          /* =================================
                           INICIALES
                        ================================= */

          let iniciales = "";

          if (nombre !== "") {
            iniciales += nombre.trim().charAt(0).toUpperCase();
          }

          if (apellidos !== "") {
            iniciales += apellidos.trim().charAt(0).toUpperCase();
          }

          if (iniciales === "") {
            iniciales = "AS";
          }

          detallePlaceholder.textContent = iniciales;
        }
      }
    });

    /* =================================================
           LIMPIAR MODAL AL CERRAR
        ================================================= */

    modalVerAsesor.addEventListener("hidden.bs.modal", function () {
      const detalleImagen = document.getElementById("detalleImagen");

      if (detalleImagen) {
        detalleImagen.removeAttribute("src");

        detalleImagen.classList.add("d-none");
      }

      const detallePlaceholder = document.getElementById(
        "detalleAvatarPlaceholder",
      );

      if (detallePlaceholder) {
        detallePlaceholder.classList.remove("d-none");

        detallePlaceholder.textContent = "AS";
      }
    });
  }
});
