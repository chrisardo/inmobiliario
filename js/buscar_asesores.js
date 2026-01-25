$(document).ready(function () {
  function buscarAsesores(texto = "") {
    $.ajax({
      url: "controladores/buscar_asesores.php",
      type: "GET",
      data: { buscar: texto },
      success: function (response) {
        $("#resultadoAsesores").html(response);
      },
      error: function () {
        $("#resultadoAsesores").html(
          '<div class="alert alert-danger text-center">Ocurrió un error al buscar</div>',
        );
      },
    });
  }

  // Buscar al escribir
  $("#inputBuscar").on("keyup", function () {
    const texto = $(this).val();
    buscarAsesores(texto);
  });

  // Cargar todas al inicio
  buscarAsesores();
});
