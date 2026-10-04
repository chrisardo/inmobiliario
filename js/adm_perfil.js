/* ============================================================
   CoDevPro Technology
   Archivo: js/adm_perfil.js
   Módulo: Perfil Administrativo
============================================================ */

"use strict";

document.addEventListener("DOMContentLoaded", () => {
  /* ========================================================
       ELEMENTOS
    ======================================================== */

  const profileForm = document.getElementById("profileForm");

  const passwordForm = document.getElementById("passwordForm");

  const imageInput = document.getElementById("imagenPerfil");
  const videoInput = document.getElementById("videoEmpresa");

  const saveVideoButton = document.getElementById("saveVideoButton");

  const deleteVideoButton = document.getElementById("deleteVideoButton");

  const saveImageButton = document.getElementById("saveImageButton");

  const saveProfileButton = document.getElementById("saveProfileButton");

  const savePasswordButton = document.getElementById("savePasswordButton");

  const profileAlert = document.getElementById("profileAlert");

  const profileAlertText = document.getElementById("profileAlertText");

  const profileAlertClose = document.getElementById("profileAlertClose");

  const passwordAlert = document.getElementById("passwordAlert");

  const passwordAlertText = document.getElementById("passwordAlertText");

  const descripcion = document.getElementById("descripcion");

  const descripcionCounter = document.getElementById("descripcionCounter");

  /* ========================================================
       FUNCIÓN AJAX
    ======================================================== */

  async function enviarSolicitud(formData) {
    const response = await fetch("../controladores/procesar_perfil.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    let data;

    try {
      data = await response.json();
    } catch (error) {
      throw new Error("El servidor devolvió una respuesta no válida.");
    }

    if (!response.ok || !data) {
      throw new Error(data?.mensaje || "No se pudo procesar la solicitud.");
    }

    return data;
  }

  /* ========================================================
       ALERTA PERFIL
    ======================================================== */

  function mostrarProfileAlert(tipo, mensaje) {
    if (!profileAlert || !profileAlertText) {
      return;
    }

    profileAlert.classList.remove("success", "error", "show");

    profileAlert.classList.add(tipo);

    profileAlertText.textContent = mensaje;

    void profileAlert.offsetWidth;

    profileAlert.classList.add("show");
  }

  function ocultarProfileAlert() {
    if (!profileAlert) {
      return;
    }

    profileAlert.classList.remove("show");
  }

  if (profileAlertClose) {
    profileAlertClose.addEventListener("click", ocultarProfileAlert);
  }

  /* ========================================================
       ALERTA PASSWORD
    ======================================================== */

  function mostrarPasswordAlert(tipo, mensaje) {
    if (!passwordAlert || !passwordAlertText) {
      return;
    }

    passwordAlert.classList.remove("error", "success");

    passwordAlert.classList.add(tipo);

    passwordAlertText.textContent = mensaje;

    passwordAlert.classList.add("show");
  }

  function ocultarPasswordAlert() {
    if (!passwordAlert) {
      return;
    }

    passwordAlert.classList.remove("show", "error", "success");
  }

  /* ========================================================
       CONTADOR DESCRIPCIÓN
    ======================================================== */

  function actualizarContador() {
    if (!descripcion || !descripcionCounter) {
      return;
    }

    descripcionCounter.textContent = descripcion.value.length;
  }

  if (descripcion) {
    descripcion.addEventListener("input", actualizarContador);

    actualizarContador();
  }

  /* ========================================================
       GUARDAR PERFIL
    ======================================================== */

  if (profileForm) {
    profileForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      ocultarProfileAlert();

      const originalText = saveProfileButton ? saveProfileButton.innerHTML : "";

      if (saveProfileButton) {
        saveProfileButton.disabled = true;

        saveProfileButton.innerHTML = `
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>Guardando...</span>
                    `;
      }

      const formData = new FormData(profileForm);

      formData.append("accion", "actualizar_perfil");

      try {
        const data = await enviarSolicitud(formData);

        if (data.estado !== "ok") {
          throw new Error(
            data.mensaje || "No se pudieron guardar los cambios.",
          );
        }

        mostrarProfileAlert(
          "success",
          data.mensaje || "Perfil actualizado correctamente.",
        );

        /*
         * Actualizar elementos visibles
         * sin recargar la página.
         */

        if (data.datos) {
          const nombre = data.datos.nombreEmpresa;

          if (nombre) {
            document
              .querySelectorAll(".company-info strong, .topbar-user strong")
              .forEach((element) => {
                element.textContent = nombre;
              });
          }
        }
      } catch (error) {
        mostrarProfileAlert(
          "error",
          error.message || "Ocurrió un error al guardar el perfil.",
        );
      } finally {
        if (saveProfileButton) {
          saveProfileButton.disabled = false;

          saveProfileButton.innerHTML = originalText;
        }
      }
    });
  }

  /* ========================================================
       PREVIEW IMAGEN
    ======================================================== */

  let imagenSeleccionada = null;

  if (imageInput) {
    imageInput.addEventListener("change", () => {
      const file = imageInput.files[0];

      if (!file) {
        imagenSeleccionada = null;

        if (saveImageButton) {
          saveImageButton.disabled = true;
        }

        return;
      }

      const tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

      if (!tiposPermitidos.includes(file.type)) {
        mostrarProfileAlert("error", "Selecciona una imagen JPG, PNG o WEBP.");

        imageInput.value = "";

        imagenSeleccionada = null;

        if (saveImageButton) {
          saveImageButton.disabled = true;
        }

        return;
      }

      const maxSize = 2 * 1024 * 1024;

      if (file.size > maxSize) {
        mostrarProfileAlert("error", "La imagen no puede superar los 2 MB.");

        imageInput.value = "";

        imagenSeleccionada = null;

        if (saveImageButton) {
          saveImageButton.disabled = true;
        }

        return;
      }

      imagenSeleccionada = file;

      const reader = new FileReader();

      reader.onload = (event) => {
        const source = event.target.result;

        const mainPreview = document.getElementById("profileAvatarPreview");

        if (mainPreview) {
          mainPreview.innerHTML = `
                            <img
                                src="${source}"
                                alt="Nueva imagen"
                                id="profileImage">
                        `;
        }

        const sidePreview = document.querySelector(".image-upload-preview");

        if (sidePreview) {
          sidePreview.innerHTML = `
                            <img
                                src="${source}"
                                alt="Nueva imagen"
                                id="imageUploadPreview">
                        `;
        }
      };

      reader.readAsDataURL(file);

      if (saveImageButton) {
        saveImageButton.disabled = false;
      }

      ocultarProfileAlert();
    });
  }

  /* ========================================================
       GUARDAR IMAGEN
    ======================================================== */

  if (saveImageButton) {
    saveImageButton.addEventListener("click", async () => {
      if (!imagenSeleccionada) {
        return;
      }

      const originalText = saveImageButton.innerHTML;

      saveImageButton.disabled = true;

      saveImageButton.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Guardando...
                `;

      const formData = new FormData();

      formData.append("accion", "actualizar_imagen");

      formData.append("imagen", imagenSeleccionada);

      try {
        const data = await enviarSolicitud(formData);

        if (data.estado !== "ok") {
          throw new Error(data.mensaje || "No se pudo actualizar la imagen.");
        }

        mostrarProfileAlert(
          "success",
          data.mensaje || "Imagen actualizada correctamente.",
        );

        /*
         * Actualizar todos los avatares
         * con la nueva imagen.
         */

        if (data.imagen) {
          document
            .querySelectorAll(".company-icon img, .topbar-avatar img")
            .forEach((img) => {
              img.src = data.imagen;
            });
        }

        imagenSeleccionada = null;

        imageInput.value = "";

        saveImageButton.innerHTML = `
                        <i class="fa-solid fa-check"></i>
                        Imagen guardada
                    `;

        setTimeout(() => {
          saveImageButton.innerHTML = originalText;
        }, 1800);
      } catch (error) {
        mostrarProfileAlert(
          "error",
          error.message || "No se pudo actualizar la imagen.",
        );

        saveImageButton.disabled = false;

        saveImageButton.innerHTML = originalText;
      }
    });
  }
  /* ========================================================
   VIDEO EMPRESA
======================================================== */

  let videoSeleccionado = null;

  /* ========================================================
   SELECCIONAR VIDEO
======================================================== */

  if (videoInput) {
    videoInput.addEventListener("change", () => {
      const file = videoInput.files[0];

      if (!file) {
        videoSeleccionado = null;

        if (saveVideoButton) {
          saveVideoButton.disabled = true;
        }

        return;
      }

      /* =====================================================
       TIPOS PERMITIDOS
    ===================================================== */

      const tiposPermitidos = ["video/mp4", "video/webm", "video/ogg"];

      if (!tiposPermitidos.includes(file.type)) {
        mostrarProfileAlert("error", "Selecciona un video MP4, WEBM u OGG.");

        videoInput.value = "";

        videoSeleccionado = null;

        if (saveVideoButton) {
          saveVideoButton.disabled = true;
        }

        return;
      }

      /* =====================================================
       TAMAÑO MÁXIMO: 50 MB
    ===================================================== */

      const maxSize = 50 * 1024 * 1024;

      if (file.size > maxSize) {
        mostrarProfileAlert("error", "El video no puede superar los 50 MB.");

        videoInput.value = "";

        videoSeleccionado = null;

        if (saveVideoButton) {
          saveVideoButton.disabled = true;
        }

        return;
      }

      videoSeleccionado = file;

      /* =====================================================
       VISTA PREVIA
    ===================================================== */

      const videoURL = URL.createObjectURL(file);

      const preview = document.getElementById("videoUploadPreview");

      if (preview) {
        preview.innerHTML = `
        <video
          id="empresaVideoPreview"
          controls
          preload="metadata"
          playsinline>

          <source src="${videoURL}" type="${file.type}">

          Tu navegador no soporta la reproducción de videos.

        </video>
      `;
      }

      if (saveVideoButton) {
        saveVideoButton.disabled = false;
      }

      ocultarProfileAlert();
    });
  }

  /* ========================================================
   GUARDAR VIDEO
======================================================== */

  if (saveVideoButton) {
    saveVideoButton.addEventListener("click", async () => {
      if (!videoSeleccionado) {
        return;
      }

      const originalText = saveVideoButton.innerHTML;

      saveVideoButton.disabled = true;

      saveVideoButton.innerHTML = `
      <i class="fa-solid fa-spinner fa-spin"></i>
      Guardando...
    `;

      const formData = new FormData();

      formData.append("accion", "actualizar_video");

      formData.append("video", videoSeleccionado);

      try {
        const data = await enviarSolicitud(formData);

        if (data.estado !== "ok") {
          throw new Error(data.mensaje || "No se pudo actualizar el video.");
        }

        mostrarProfileAlert(
          "success",
          data.mensaje || "Video actualizado correctamente.",
        );

        videoSeleccionado = null;

        videoInput.value = "";

        if (data.video) {
          const preview = document.getElementById("videoUploadPreview");

          if (preview) {
            preview.innerHTML = `
            <video
              id="empresaVideoPreview"
              controls
              preload="metadata"
              playsinline>

              <source
                src="${data.video}">

              Tu navegador no soporta la reproducción de videos.

            </video>
          `;
          }

          /* =================================================
           BOTÓN ELIMINAR
        ================================================= */

          let deleteButton = document.getElementById("deleteVideoButton");

          if (!deleteButton) {
            deleteButton = document.createElement("button");

            deleteButton.type = "button";

            deleteButton.className = "video-delete-button";

            deleteButton.id = "deleteVideoButton";

            deleteButton.innerHTML = `
            <i class="fa-solid fa-trash"></i>
            Eliminar video actual
          `;

            const card = document.querySelector(".video-profile-card");

            if (card) {
              card.appendChild(deleteButton);
            }
          }

          activarEliminarVideo();
        }

        saveVideoButton.innerHTML = `
        <i class="fa-solid fa-check"></i>
        Video guardado
      `;

        setTimeout(() => {
          saveVideoButton.innerHTML = originalText;
        }, 1800);
      } catch (error) {
        mostrarProfileAlert(
          "error",
          error.message || "No se pudo actualizar el video.",
        );

        saveVideoButton.disabled = false;

        saveVideoButton.innerHTML = originalText;
      }
    });
  }

  /* ========================================================
   ELIMINAR VIDEO
======================================================== */

  function activarEliminarVideo() {
    const button = document.getElementById("deleteVideoButton");

    if (!button) {
      return;
    }

    button.onclick = async () => {
      if (!confirm("¿Deseas eliminar el video informativo actual?")) {
        return;
      }

      const originalText = button.innerHTML;

      button.disabled = true;

      button.innerHTML = `
      <i class="fa-solid fa-spinner fa-spin"></i>
      Eliminando...
    `;

      const formData = new FormData();

      formData.append("accion", "eliminar_video");

      try {
        const data = await enviarSolicitud(formData);

        if (data.estado !== "ok") {
          throw new Error(data.mensaje || "No se pudo eliminar el video.");
        }

        const preview = document.getElementById("videoUploadPreview");

        if (preview) {
          preview.innerHTML = `
          <div
            class="video-upload-placeholder"
            id="videoUploadPlaceholder">

            <i class="fa-solid fa-film"></i>

            <span>
              No hay video informativo
            </span>

          </div>
        `;
        }

        button.remove();

        mostrarProfileAlert(
          "success",
          data.mensaje || "Video eliminado correctamente.",
        );
      } catch (error) {
        mostrarProfileAlert(
          "error",
          error.message || "No se pudo eliminar el video.",
        );

        button.disabled = false;

        button.innerHTML = originalText;
      }
    };
  }

  /* ========================================================
   ACTIVAR BOTÓN AL CARGAR
======================================================== */

  activarEliminarVideo();

  /* ========================================================
       MOSTRAR / OCULTAR CONTRASEÑA
    ======================================================== */

  document.querySelectorAll(".password-toggle").forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.dataset.target;

      const input = document.getElementById(targetId);

      if (!input) {
        return;
      }

      if (input.type === "password") {
        input.type = "text";

        button.innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
      } else {
        input.type = "password";

        button.innerHTML = '<i class="fa-regular fa-eye"></i>';
      }
    });
  });

  /* ========================================================
       CAMBIAR CONTRASEÑA
    ======================================================== */

  if (passwordForm) {
    passwordForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      ocultarPasswordAlert();

      const actual = document.getElementById("contrasenaActual");

      const nueva = document.getElementById("nuevaContrasena");

      const confirmar = document.getElementById("confirmarContrasena");

      if (!actual || !nueva || !confirmar) {
        return;
      }

      if (nueva.value.length < 8) {
        mostrarPasswordAlert(
          "error",
          "La nueva contraseña debe tener al menos 8 caracteres.",
        );

        return;
      }

      if (nueva.value !== confirmar.value) {
        mostrarPasswordAlert("error", "Las nuevas contraseñas no coinciden.");

        return;
      }

      const originalText = savePasswordButton.innerHTML;

      savePasswordButton.disabled = true;

      savePasswordButton.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Actualizando...
                `;

      const formData = new FormData(passwordForm);

      formData.append("accion", "actualizar_contrasena");

      try {
        const data = await enviarSolicitud(formData);

        if (data.estado !== "ok") {
          throw new Error(
            data.mensaje || "No se pudo actualizar la contraseña.",
          );
        }

        mostrarPasswordAlert(
          "success",
          data.mensaje || "Contraseña actualizada correctamente.",
        );

        passwordForm.reset();

        setTimeout(() => {
          const modalElement = document.getElementById("passwordModal");

          if (modalElement && typeof bootstrap !== "undefined") {
            const modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
              modal.hide();
            }
          }
        }, 1300);
      } catch (error) {
        mostrarPasswordAlert(
          "error",
          error.message || "Ocurrió un error al actualizar la contraseña.",
        );
      } finally {
        savePasswordButton.disabled = false;

        savePasswordButton.innerHTML = originalText;
      }
    });
  }

  /* ========================================================
       LIMPIAR FORMULARIO PASSWORD AL CERRAR MODAL
    ======================================================== */

  const passwordModal = document.getElementById("passwordModal");

  if (passwordModal) {
    passwordModal.addEventListener("hidden.bs.modal", () => {
      if (passwordForm) {
        passwordForm.reset();
      }

      ocultarPasswordAlert();
    });
  }
});
