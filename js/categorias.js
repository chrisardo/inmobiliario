// funcion para eliminar categoria
function setEliminarId(id) {
  const btn = document.getElementById("btnConfirmarEliminar");
  btn.href = "?eliminar=" + id;
}