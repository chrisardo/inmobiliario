////Previsualizar imagen del modal
document.addEventListener("DOMContentLoaded", function () {
  const inputImagen = document.getElementById("imagen");
  const previewCont = document.getElementById("previewImagen");
  const previewImg = document.getElementById("previewImg");
  const imgNombre = document.getElementById("imgNombre");
  const imgSize = document.getElementById("imgSize");
  const imgTipo = document.getElementById("imgTipo");

  inputImagen.addEventListener("change", function () {
    if (!this.files || this.files.length === 0) {
      previewCont.classList.add("d-none");
      return;
    }

    const file = this.files[0];

    // Validaciones básicas
    const tiposPermitidos = ["image/jpeg", "image/png"];
    if (!tiposPermitidos.includes(file.type)) {
      previewCont.classList.add("d-none");
      return;
    }

    // Mostrar datos
    //imgNombre.textContent = file.name;
    imgTipo.textContent = file.type;
    imgSize.textContent = (file.size / 1024).toFixed(2) + " KB";

    // Previsualizar imagen
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
      previewCont.classList.remove("d-none");
    };
    reader.readAsDataURL(file);
  });
});