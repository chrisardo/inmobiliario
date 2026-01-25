$(document).ready(function () {
  function buscarPropiedades(texto = "") {
    $.ajax({
      url: "controladores/buscar_propiedades.php",
      type: "GET",
      data: { buscar: texto },
      success: function (response) {
        $("#resultadoPropiedades").html(response);
      },
      error: function () {
        $("#resultadoPropiedades").html(
          '<div class="alert alert-danger text-center">Ocurrió un error al buscar</div>',
        );
      },
    });
  }

  // Buscar al escribir
  $("#inputBuscar").on("keyup", function () {
    const texto = $(this).val();
    buscarPropiedades(texto);
  });

  // Cargar todas al inicio
  buscarPropiedades();
});
