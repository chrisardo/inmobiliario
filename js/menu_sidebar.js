/* ============================================================
   CoDevPro Technology
   Archivo: js/menu_sidebar.js
   Módulo: Panel Administrativo
============================================================ */

"use strict";

document.addEventListener("DOMContentLoaded", () => {
  /* ========================================================
       ELEMENTOS
    ======================================================== */

  const layout = document.querySelector(".admin-layout");
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("toggleSidebar");
  const closeBtn = document.getElementById("sidebarClose");
  const overlay = document.getElementById("sidebarOverlay");

  if (!layout || !sidebar) {
    return;
  }

  /* ========================================================
       CONSTANTES
    ======================================================== */

  const MOBILE_BREAKPOINT = 992;

  /* ========================================================
       ABRIR SIDEBAR - MÓVIL
    ======================================================== */

  function openSidebar() {
    sidebar.classList.add("show");

    if (overlay) {
      overlay.classList.add("show");
    }

    if (toggleBtn) {
      toggleBtn.setAttribute("aria-expanded", "true");
      toggleBtn.setAttribute("aria-label", "Cerrar menú");
    }

    document.body.style.overflow = "hidden";
  }

  /* ========================================================
       CERRAR SIDEBAR - MÓVIL
    ======================================================== */

  function closeSidebar() {
    sidebar.classList.remove("show");

    if (overlay) {
      overlay.classList.remove("show");
    }

    if (toggleBtn) {
      toggleBtn.setAttribute("aria-expanded", "false");
      toggleBtn.setAttribute("aria-label", "Abrir menú");
    }

    document.body.style.overflow = "";
  }

  /* ========================================================
       CONTRAER SIDEBAR - ESCRITORIO
    ======================================================== */

  function collapseSidebar() {
    layout.classList.add("sidebar-collapsed");

    if (toggleBtn) {
      toggleBtn.setAttribute("aria-expanded", "false");
      toggleBtn.setAttribute("aria-label", "Expandir menú");
    }
  }

  /* ========================================================
       EXPANDIR SIDEBAR - ESCRITORIO
    ======================================================== */

  function expandSidebar() {
    layout.classList.remove("sidebar-collapsed");

    if (toggleBtn) {
      toggleBtn.setAttribute("aria-expanded", "true");
      toggleBtn.setAttribute("aria-label", "Contraer menú");
    }
  }

  /* ========================================================
       TOGGLE PRINCIPAL
    ======================================================== */

  if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
      /* -----------------------------------------------
               MÓVIL / TABLET
            ------------------------------------------------ */

      if (window.innerWidth < MOBILE_BREAKPOINT) {
        if (sidebar.classList.contains("show")) {
          closeSidebar();
        } else {
          openSidebar();
        }

        return;
      }

      /* -----------------------------------------------
               ESCRITORIO
            ------------------------------------------------ */

      if (layout.classList.contains("sidebar-collapsed")) {
        expandSidebar();
      } else {
        collapseSidebar();
      }
    });
  }

  /* ========================================================
       BOTÓN CERRAR MÓVIL
    ======================================================== */

  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      closeSidebar();
    });
  }

  /* ========================================================
       OVERLAY
    ======================================================== */

  if (overlay) {
    overlay.addEventListener("click", () => {
      closeSidebar();
    });
  }

  /* ========================================================
       ESCAPE
    ======================================================== */

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") {
      return;
    }

    if (window.innerWidth < MOBILE_BREAKPOINT) {
      closeSidebar();
    }
  });

  /* ========================================================
       REDIMENSIONAR VENTANA
    ======================================================== */

  window.addEventListener("resize", () => {
    if (window.innerWidth >= MOBILE_BREAKPOINT) {
      closeSidebar();

      document.body.style.overflow = "";
    } else {
      layout.classList.remove("sidebar-collapsed");
    }
  });

  /* ========================================================
       DETECTAR PÁGINA ACTUAL
    ======================================================== */

  const currentPage = window.location.pathname.split("/").pop().toLowerCase();

  /* ========================================================
       ENLACES PRINCIPALES
    ======================================================== */

  const sidebarLinks = document.querySelectorAll(".sidebar-link[href]");

  sidebarLinks.forEach((link) => {
    const href = link.getAttribute("href");

    if (!href) {
      return;
    }

    const linkPage = href.split("/").pop().split("?")[0].toLowerCase();

    if (linkPage === currentPage && linkPage !== "") {
      link.classList.add("active");
    }
  });

  /* ========================================================
       SUBENLACES
    ======================================================== */

  const submenuLinks = document.querySelectorAll(".sidebar-sublink[href]");

  submenuLinks.forEach((link) => {
    const href = link.getAttribute("href");

    if (!href) {
      return;
    }

    const linkPage = href.split("/").pop().split("?")[0].toLowerCase();

    if (linkPage === currentPage && linkPage !== "") {
      link.classList.add("active");

      /* -----------------------------------------------
               BUSCAR SUBMENÚ PADRE
            ------------------------------------------------ */

      const submenu = link.closest(".sidebar-submenu");

      if (submenu) {
        const submenuId = submenu.getAttribute("id");

        const collapseTrigger = document.querySelector(
          `[href="#${submenuId}"]`,
        );

        if (collapseTrigger) {
          collapseTrigger.classList.add("active");

          collapseTrigger.setAttribute("aria-expanded", "true");
        }

        /* -------------------------------------------
                   BOOTSTRAP COLLAPSE
                -------------------------------------------- */

        if (typeof bootstrap !== "undefined") {
          const collapse = bootstrap.Collapse.getOrCreateInstance(submenu, {
            toggle: false,
          });

          collapse.show();
        } else {
          submenu.classList.add("show");
        }
      }
    }
  });

  /* ========================================================
       CERRAR SIDEBAR AL NAVEGAR - SOLO MÓVIL
    ======================================================== */

  const navigationLinks = document.querySelectorAll(
    ".sidebar-link[href], .sidebar-sublink[href]",
  );

  navigationLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth < MOBILE_BREAKPOINT) {
        closeSidebar();
      }
    });
  });

  /* ========================================================
       NO CERRAR AL HACER CLICK EN COLLAPSE
    ======================================================== */

  const collapseLinks = document.querySelectorAll(".sidebar-collapse-link");

  collapseLinks.forEach((link) => {
    link.addEventListener("click", (event) => {
      event.stopPropagation();
    });
  });
});
