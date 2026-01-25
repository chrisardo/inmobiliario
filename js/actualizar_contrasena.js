$(document).ready(function () {
  $("#formEditarContrasena").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: "../controladores/actualizar_contrasena.php",
      type: "POST",
      data: $(this).serialize(),
      success: function (response) {
        $("#contrasenaMessages").html(response);

        if (response.includes("Contraseña actualizada correctamente")) {
          $("#formEditarContrasena")[0].reset();
        }
      },
      error: function (xhr, status, error) {
        $("#contrasenaMessages").html(`
          <div class="alert alert-danger">
            <strong>Error AJAX</strong><br>
            Status: ${xhr.status}<br>
            ${xhr.responseText}
          </div>
        `);
        console.error(xhr.responseText);
      },
    });
  });
});
