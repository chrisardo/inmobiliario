// Toda esta parte es js/lista_asesores.js
// funcion para eliminar producto
function setEliminarId(id) {
  const btn = document.getElementById("btnConfirmarEliminar");
  btn.href = "?eliminar=" + id;
}
// Rellenar modal de editar con datos del cliente
document
  .getElementById("modalEditar")
  .addEventListener("show.bs.modal", function (event) {
    const button = event.relatedTarget;

    document.getElementById("edit-id").value = button.dataset.id;
    document.getElementById("edit-nombre").value = button.dataset.nombre;
    document.getElementById("edit-apellidos").value = button.dataset.apellidos;
    document.getElementById("edit-email").value = button.dataset.email;
    document.getElementById("edit-celular").value = button.dataset.celular;
    document.getElementById("edit-cargo").value = button.dataset.cargo;
  });

// Funcionalidades de búsqueda automática
document.addEventListener("DOMContentLoaded", function () {
  const inputBuscar = document.getElementById("inputBuscar");
  const formBuscar = document.getElementById("formBuscar");

  let delayTimer;

  inputBuscar.addEventListener("input", function () {
    clearTimeout(delayTimer);

    delayTimer = setTimeout(() => {
      formBuscar.submit();
    }, 300);
  });
});

/////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("#modalEditar form");
  const alerta = document.getElementById("alertaAsesor");

  form.addEventListener("submit", function (e) {
    alerta.classList.add("d-none");
    alerta.innerHTML = "";
    alerta.className = "alert d-none";

    let errores = [];

    const nombre = document.getElementById("edit-nombre").value.trim();
    const apellidos = document.getElementById("edit-apellidos").value.trim();
    const email = document.getElementById("edit-email").value.trim();
    const celular = document.getElementById("edit-celular").value;
    const cargo = document.getElementById("edit-cargo").value;
    const imagen = document.getElementById("edit-imagen");

    if (nombre === "") errores.push("El nombre es obligatorio.");
    if (apellidos === "") errores.push("El apellido es obligatorio.");
    if (celular === "" || celular <= 0)
      errores.push("El celular debe ser mayor a 0.");
    if (email === "") errores.push("El email es obligatorio.");
    if (cargo === "") errores.push("El cargo no puede estar vacio");
    // VALIDAR IMAGEN (si se selecciona)
    if (imagen.files.length > 0) {
      const file = imagen.files[0];
      const tiposPermitidos = ["image/jpeg", "image/png"];
      const maxSize = 1.8 * 1024 * 1024;

      if (!tiposPermitidos.includes(file.type)) {
        errores.push("La imagen debe ser JPG o PNG.");
      }

      if (file.size > maxSize) {
        errores.push("La imagen no debe superar 1.8 MB.");
      }
    }

    if (errores.length > 0) {
      e.preventDefault();
      alerta.classList.remove("d-none");
      alerta.classList.add("alert-danger");
      alerta.innerHTML =
        "<ul class='mb-0'><li>" + errores.join("</li><li>") + "</li></ul>";
    }
  });
});

////Previsualizar imagen del modal
////Previsualizar imagen del modal
document.addEventListener("DOMContentLoaded", function () {
  const inputImagen = document.getElementById("edit-imagen");
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
