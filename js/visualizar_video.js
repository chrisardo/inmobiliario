//Toda esta parte es de js/visualizar_video.js
document.addEventListener("DOMContentLoaded", () => {
  const inputVideo = document.getElementById("edit-video");
  const previewBox = document.getElementById("previewImagen");
  const previewVideo = document.getElementById("previewVideo");

  const videoNombre = document.getElementById("videoNombre");
  const videoTipo = document.getElementById("videoTipo");
  const videoSize = document.getElementById("videoSize");
  const videoDuracion = document.getElementById("videoDuracion");

  inputVideo.addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;

    // Validar tipo
    if (file.type !== "video/mp4") {
      alert("Solo se permiten videos MP4");
      this.value = "";
      previewBox.classList.add("d-none");
      return;
    }

    // Mostrar datos básicos
    videoNombre.textContent = file.name;
    videoTipo.textContent = file.type;
    videoSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + " MB";

    // Vista previa
    const videoURL = URL.createObjectURL(file);
    previewVideo.src = videoURL;

    previewVideo.onloadedmetadata = () => {
      const duracion = previewVideo.duration;
      videoDuracion.textContent = duracion.toFixed(1) + " segundos";
    };

    previewBox.classList.remove("d-none");
  });
});
//Fin de js/visualizar_video.js
