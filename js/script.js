$(function() {

      $("#chatButton").on("click", function() {
        $("#chatButtonContainer").hide();
        $("#chatFormContainer").removeClass("d-none");
      });

      $("#closeChatForm").on("click", function() {
        $("#chatFormContainer").addClass("d-none");
        $("#chatButtonContainer").show();
      });

      $("#chatForm").on("submit", function(e) {
        e.preventDefault();

        const nombre = $("#chat_nombre").val().trim();
        const whatsapp = $("#chat_whatsapp").val().trim();
        const mensaje = $("#chat_mensaje").val().trim();

        if (!nombre || !whatsapp || !mensaje) {
          alert("Completa todos los campos");
          return;
        }

        if (!/^[0-9]{9}$/.test(whatsapp)) {
          alert("Número de WhatsApp inválido");
          return;
        }

        const empresa = "Mi Inmobiliaria";
        const texto = `Hola, soy ${nombre}. ${mensaje} (${empresa})`;

        const url = `https://api.whatsapp.com/send?phone=51${whatsapp}&text=${encodeURIComponent(texto)}`;
        window.open(url, "_blank");
      });

    });
