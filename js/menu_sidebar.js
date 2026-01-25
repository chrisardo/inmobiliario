const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleSidebar");

toggleBtn.addEventListener("click", () => {
  if (window.innerWidth < 992) {
    sidebar.classList.toggle("show");
  }
});

/* Estado inicial */
function handleResize() {
  if (window.innerWidth >= 992) {
    sidebar.classList.remove("show");
  }
}

window.addEventListener("load", handleResize);
window.addEventListener("resize", handleResize);

///////////////////////////////////////CONTADOR TARJETA

