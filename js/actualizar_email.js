$(document).ready(function () {
  $("#formEditarEmail").on("submit", function (e) {
    e.preventDefault();

    var formData = $(this).serialize();

    $.ajax({
      url: "../controladores/actualizar_email.php",
      type: "POST",
      data: formData,
      success: function (response) {
        $("#emailMessages").html(response);

        if (response.includes("Email actualizado correctamente")) {
          var emailNuevo = $("#emailNuevo").val();
          $("#emailPerfil").text(emailNuevo);
          $("#emailActual").val(emailNuevo);
          $("#emailNuevo").val("");
        }
      },
      error: function () {
        $("#emailMessages").html(
          '<div class="alert alert-danger">Ocurrió un error. Intente nuevamente.</div>'
        );
      },
    });
  });
});
