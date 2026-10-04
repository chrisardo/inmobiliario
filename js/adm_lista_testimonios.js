// ============================================================
// Inmobiliaria Iquitos
// Archivo: js/adm_lista_testimonios.js
// Módulo: Lista de Testimonios
// ============================================================

document.addEventListener("DOMContentLoaded", function () {
  // ========================================================
  // CARGAR LISTA DE TESTIMONIOS
  // ========================================================

  cargarTestimonios();

  // ========================================================
  // BOTÓN ELIMINAR
  // ========================================================

  document.addEventListener("click", function (e) {
    const boton = e.target.closest(".btn-delete-testimonio");

    if (!boton) {
      return;
    }

    const id = boton.dataset.id;
    const nombre = boton.dataset.nombre || "este testimonio";

    if (!id) {
      console.error("No se encontró el ID del testimonio.");
      return;
    }

    if (typeof window.confirmarEliminarTestimonio === "function") {
      window.confirmarEliminarTestimonio(id, nombre);
    } else {
      console.error(
        "La función window.confirmarEliminarTestimonio no está disponible.",
      );
    }
  });

  // ========================================================
  // BOTÓN VER TESTIMONIO
  // ========================================================

  document.addEventListener("click", function (e) {
    const boton = e.target.closest(".btn-view-testimonio");

    if (!boton) {
      return;
    }

    const id = boton.dataset.id;

    if (!id) {
      console.error("No se encontró el ID del testimonio.");
      return;
    }

    if (typeof window.verTestimonio === "function") {
      window.verTestimonio(id);
    } else {
      console.error("La función window.verTestimonio no está disponible.");
    }
  });

  // ========================================================
  // BOTÓN EDITAR TESTIMONIO
  // ========================================================
  // IMPORTANTE:
  // ajax_lista_testimonios.php utiliza:
  // .btn-editar-testimonio
  //
  // Por eso aquí debe utilizarse exactamente la misma clase.
  // ========================================================

  document.addEventListener("click", function (e) {
    const boton = e.target.closest(".btn-editar-testimonio");

    if (!boton) {
      return;
    }

    const id = boton.dataset.id;

    if (!id) {
      console.error("No se encontró el ID del testimonio.");
      return;
    }

    if (typeof window.editarTestimonio === "function") {
      window.editarTestimonio(id);
    } else {
      console.error("La función window.editarTestimonio no está disponible.");
    }
  });
});

// ============================================================
// FUNCIÓN: CARGAR TESTIMONIOS
// ============================================================

async function cargarTestimonios() {
  const contenedor = document.getElementById("contenedorTestimonios");

  if (!contenedor) {
    console.error("No se encontró el contenedor #contenedorTestimonios.");
    return;
  }

  // ========================================================
  // ESTADO DE CARGA
  // ========================================================

  contenedor.innerHTML = `
        <div class="testimonios-loading">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">
                    Cargando...
                </span>
            </div>

            <p class="mt-3 mb-0">
                Cargando testimonios...
            </p>
        </div>
    `;

  try {
    const respuesta = await fetch(
      "../controladores/ajax_lista_testimonios.php",
      {
        method: "GET",
        credentials: "same-origin",
        cache: "no-store",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      },
    );

    // ====================================================
    // VALIDAR RESPUESTA HTTP
    // ====================================================

    if (!respuesta.ok) {
      throw new Error("Error HTTP: " + respuesta.status);
    }

    // ====================================================
    // CONVERTIR A JSON
    // ====================================================

    const datos = await respuesta.json();

    // ====================================================
    // VALIDAR RESPUESTA DEL SERVIDOR
    // ====================================================

    if (!datos.success) {
      throw new Error(
        datos.message || "No fue posible cargar los testimonios.",
      );
    }

    // ====================================================
    // ACTUALIZAR KPI TOTAL
    // ====================================================

    const kpiTotal = document.getElementById("kpiTotalTestimonios");

    if (kpiTotal) {
      kpiTotal.textContent = Number(datos.total || 0);
    }

    // ====================================================
    // ACTUALIZAR KPI DISPONIBLES
    // ========================================================

    const kpiDisponibles = document.getElementById("kpiTestimoniosDisponibles");

    if (kpiDisponibles) {
      kpiDisponibles.textContent = Number(datos.disponibles || 0);
    }

    // ====================================================
    // ACTUALIZAR ESTADO DEL LÍMITE
    // ========================================================

    const kpiEstadoIcono = document.getElementById("kpiEstadoIcono");

    const kpiEstado = document.getElementById("kpiEstadoTestimonios");

    if (datos.limite_alcanzado) {
      if (kpiEstadoIcono) {
        kpiEstadoIcono.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
      }

      if (kpiEstado) {
        kpiEstado.textContent = "Límite alcanzado";
      }
    } else {
      if (kpiEstadoIcono) {
        kpiEstadoIcono.innerHTML = '<i class="fa-solid fa-circle-info"></i>';
      }

      if (kpiEstado) {
        kpiEstado.textContent = "Disponible";
      }
    }

    // ====================================================
    // ACTUALIZAR CANTIDAD DE LA TOOLBAR
    // ====================================================

    const toolbarCantidad = document.getElementById(
      "toolbarCantidadTestimonios",
    );

    if (toolbarCantidad) {
      const total = Number(datos.total || 0);
      const maximo = Number(datos.maximo || 4);

      toolbarCantidad.textContent = `${total} de ${maximo}`;
    }

    // ====================================================
    // ACTUALIZAR ACCIÓN / ESTADO DE TOOLBAR
    // ====================================================

    const toolbarAccion = document.getElementById("toolbarAccionTestimonio");

    if (toolbarAccion) {
      if (datos.limite_alcanzado) {
        toolbarAccion.innerHTML = `
                    <span class="text-muted">
                        <i class="fa-solid fa-lock me-1"></i>
                        Límite de testimonios alcanzado
                    </span>
                `;
      } else {
        toolbarAccion.innerHTML = `
                    <span class="text-success">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        Puedes agregar testimonios
                    </span>
                `;
      }
    }

    // ====================================================
    // ACTUALIZAR LISTA
    // ====================================================

    contenedor.innerHTML = datos.html || "";

    // ====================================================
    // SI NO HAY HTML
    // ====================================================

    if (!datos.html || datos.html.trim() === "") {
      contenedor.innerHTML = `
                <div class="testimonios-empty">
                    <div class="testimonios-empty-icon">
                        <i class="fa-regular fa-comments"></i>
                    </div>

                    <h5>
                        No hay testimonios registrados
                    </h5>

                    <p>
                        Aún no se han registrado testimonios
                        para esta inmobiliaria.
                    </p>
                </div>
            `;
    }
  } catch (error) {
    console.error("Error al cargar testimonios:", error);

    // ====================================================
    // MOSTRAR ERROR
    // ====================================================

    contenedor.innerHTML = `
            <div class="testimonios-error">

                <div class="testimonios-error-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h5>
                    No se pudieron cargar los testimonios
                </h5>

                <p>
                    ${escapeHtml(
                      error.message || "Ocurrió un error inesperado.",
                    )}
                </p>

                <button
                    type="button"
                    class="btn btn-outline-success btn-sm"
                    onclick="cargarTestimonios()">

                    <i class="fa-solid fa-rotate-right me-1"></i>

                    Intentar nuevamente

                </button>

            </div>
        `;

    // ====================================================
    // ACTUALIZAR KPIs EN CASO DE ERROR
    // ====================================================

    const kpiTotal = document.getElementById("kpiTotalTestimonios");

    if (kpiTotal) {
      kpiTotal.textContent = "—";
    }

    const kpiDisponibles = document.getElementById("kpiTestimoniosDisponibles");

    if (kpiDisponibles) {
      kpiDisponibles.textContent = "—";
    }

    const toolbarCantidad = document.getElementById(
      "toolbarCantidadTestimonios",
    );

    if (toolbarCantidad) {
      toolbarCantidad.textContent = "—";
    }
  }
}

// ============================================================
// FUNCIÓN GLOBAL PARA RECARGAR LA LISTA
// ============================================================

window.recargarListaTestimonios = function () {
  cargarTestimonios();
};

// ============================================================
// FUNCIÓN AUXILIAR: ESCAPAR HTML
// ============================================================

function escapeHtml(texto) {
  const div = document.createElement("div");

  div.textContent = texto;

  return div.innerHTML;
}
