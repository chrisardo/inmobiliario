// Toda esta parte es js/lista_mensajes.js
// funcion para eliminar producto
function setEliminarId(id) {
  const btn = document.getElementById("btnConfirmarEliminar");
  btn.href = "?eliminar=" + id;
}
// Rellenar modal de ver detalles del cliente
document
  .getElementById("modalVerDetalles")
  .addEventListener("show.bs.modal", function (event) {

    const button = event.relatedTarget;

    const nombre = button.dataset.nombre;
    const apellidos = button.dataset.apellidos;
    const celular = button.dataset.celular;
    const mensaje = button.dataset.mensaje;
    const propiedad = button.dataset.propiedad;

    // Rellenar textos del modal
    document.getElementById("verNombre").textContent = nombre;
    document.getElementById("verApellidos").textContent = apellidos;
    document.getElementById("verEmail").textContent = button.dataset.email;
    document.getElementById("verCelular").textContent = celular;
    document.getElementById("verPropiedad").textContent = propiedad;
    document.getElementById("verFecha").textContent = button.dataset.fecha;
    document.getElementById("verMensaje").textContent = mensaje;

    // Limpieza del número (solo números)
    const telefono = celular.replace(/\D/g, "");

    // Mensaje automático
    const texto = `
Hola ${nombre} ${apellidos}, Te saluda de la inmobiliaria.
Gracias por contactarnos por la propiedad: ${propiedad}.
Con gusto te brindamos más información.
`.trim();

    // Codificar mensaje
    const mensajeUrl = encodeURIComponent(texto);

    // URL WhatsApp
    const urlWhatsapp = `https://wa.me/51${telefono}?text=${mensajeUrl}`;

    // Asignar al botón
    const btnWhatsapp = document.getElementById("btnWhatsapp");
    btnWhatsapp.href = urlWhatsapp;
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
