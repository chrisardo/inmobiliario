//Mostrar u ocultar email
let emailVisible = false;
let emailModalVisible = false;

function toggleEmail() {
  const emailText = document.getElementById("emailText");
  const emailIcon = document.getElementById("emailIcon");
  const realEmail = emailText.dataset.email;
  emailText.textContent = realEmail.replace(/(.{2}).+(@.+)/, "$1****$2");


  /*if (emailVisible) {
    emailText.textContent = "••••••••••••";
    emailIcon.classList.replace("fa-eye-slash", "fa-eye");
  } else {
    emailText.textContent = realEmail;
    emailIcon.classList.replace("fa-eye", "fa-eye-slash");
  }

  emailVisible = !emailVisible;*/
}

function toggleEmail2(textId, iconId) {
  const emailText = document.getElementById(textId);
  const emailIcon = document.getElementById(iconId);
  const realEmail = emailText.dataset.email;

  if (emailModalVisible) {
    emailText.textContent = "••••••••••••";
    emailIcon.classList.replace("fa-eye-slash", "fa-eye");
  } else {
    emailText.textContent = realEmail;
    emailIcon.classList.replace("fa-eye", "fa-eye-slash");
  }

  emailModalVisible = !emailModalVisible;
}
