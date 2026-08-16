(function () {
  "use strict";

  document.documentElement.classList.add("has-js");

  const header = document.querySelector("[data-site-header]");
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const mobileMenu = document.querySelector("[data-mobile-menu]");
  const mobileSubmenuToggles = Array.from(document.querySelectorAll("[data-mobile-submenu-toggle]"));
  const contactRoot = document.querySelector("[data-contact-float]");
  const contactToggle = document.querySelector("[data-contact-toggle]");
  const contactOptions = document.querySelector(".contact-options");
  const backgroundRegions = Array.from(document.querySelectorAll(".site-header .site-brand, .site-header .custom-logo-wrap, .site-header .primary-nav, .site-header .header-book, main, .breadcrumbs, .site-footer, .mobile-action-bar, .contact-float"));

  const syncHeader = () => {
    if (header) header.classList.toggle("is-scrolled", window.scrollY > 18);
  };

  const setMenu = (open, returnFocus) => {
    if (!menuToggle || !mobileMenu) return;
    menuToggle.setAttribute("aria-expanded", String(open));
    menuToggle.setAttribute("aria-label", open ? "بستن منو" : "باز کردن منو");
    mobileMenu.hidden = !open;
    document.body.classList.toggle("menu-is-open", open);
    if (!open) {
      mobileSubmenuToggles.forEach((toggle) => {
        const panelId = toggle.getAttribute("aria-controls");
        const panel = panelId ? document.getElementById(panelId) : null;
        toggle.setAttribute("aria-expanded", "false");
        if (panel) panel.hidden = true;
      });
    }
    backgroundRegions.forEach((region) => {
      region.inert = open;
      if (open) region.setAttribute("aria-hidden", "true");
      else region.removeAttribute("aria-hidden");
    });

    if (open) {
      window.requestAnimationFrame(() => {
        const firstLink = mobileMenu.querySelector("a");
        if (firstLink) firstLink.focus();
      });
    } else if (returnFocus) {
      menuToggle.focus();
    }
  };

  const setContacts = (open, returnFocus) => {
    if (!contactToggle || !contactOptions) return;
    contactToggle.setAttribute("aria-expanded", String(open));
    contactToggle.setAttribute("aria-label", open ? "بستن راه‌های تماس" : "تماس با ما");
    contactOptions.hidden = !open;
    if (contactRoot) {
      contactRoot.classList.toggle("is-open", open);
      // Keep FAB anchored bottom-right (RTL); never shift left on open
      contactRoot.style.left = "auto";
      contactRoot.style.right = "";
      contactRoot.style.transform = "none";
    }

    if (open) {
      window.requestAnimationFrame(() => {
        const firstLink = contactOptions.querySelector("a");
        if (firstLink) firstLink.focus();
      });
    } else if (returnFocus) {
      contactToggle.focus();
    }
  };

  syncHeader();
  window.addEventListener("scroll", syncHeader, { passive: true });

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", () => {
      setMenu(menuToggle.getAttribute("aria-expanded") !== "true", false);
    });

    mobileMenu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => setMenu(false, false));
    });

    window.addEventListener("resize", () => {
      if (window.innerWidth >= 1200) setMenu(false, false);
    });

    mobileMenu.addEventListener("keydown", (event) => {
      if (event.key !== "Tab") return;
      const focusable = Array.from(mobileMenu.querySelectorAll('a[href], button:not([disabled])'));
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }

  mobileSubmenuToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const panelId = toggle.getAttribute("aria-controls");
      const panel = panelId ? document.getElementById(panelId) : null;
      if (!panel) return;
      const open = toggle.getAttribute("aria-expanded") !== "true";
      toggle.setAttribute("aria-expanded", String(open));
      panel.hidden = !open;
    });
  });

  if (contactToggle && contactOptions) {
    contactToggle.addEventListener("click", () => {
      setContacts(contactToggle.getAttribute("aria-expanded") !== "true", false);
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") return;
    if (menuToggle && menuToggle.getAttribute("aria-expanded") === "true") setMenu(false, true);
    if (contactToggle && contactToggle.getAttribute("aria-expanded") === "true") setContacts(false, true);
  });

  document.addEventListener("click", (event) => {
    if (
      contactRoot &&
      contactToggle &&
      contactToggle.getAttribute("aria-expanded") === "true" &&
      !contactRoot.contains(event.target)
    ) {
      setContacts(false, false);
    }
  });

  document.querySelectorAll("[data-accordion-trigger]").forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const panelId = trigger.getAttribute("aria-controls");
      const panel = panelId ? document.getElementById(panelId) : null;
      if (!panel) return;

      const shouldOpen = trigger.getAttribute("aria-expanded") !== "true";
      const list = trigger.closest(".accordion-list");
      if (list) {
        list.querySelectorAll("[data-accordion-trigger]").forEach((otherTrigger) => {
          if (otherTrigger === trigger) return;
          const otherPanelId = otherTrigger.getAttribute("aria-controls");
          const otherPanel = otherPanelId ? document.getElementById(otherPanelId) : null;
          otherTrigger.setAttribute("aria-expanded", "false");
          if (otherPanel) otherPanel.hidden = true;
        });
      }

      trigger.setAttribute("aria-expanded", String(shouldOpen));
      panel.hidden = !shouldOpen;
    });
  });

  const bookingForm = document.querySelector("[data-booking-form]");
  if (bookingForm) {
    const steps = Array.from(bookingForm.querySelectorAll("[data-booking-step]"));
    const indicators = Array.from(document.querySelectorAll("[data-step-indicator]"));
    const previous = bookingForm.querySelector("[data-booking-previous]");
    const next = bookingForm.querySelector("[data-booking-next]");
    const submit = bookingForm.querySelector("[data-booking-submit]");
    const dateField = bookingForm.querySelector('input[name="date"]');
    let currentStep = 1;

    if (dateField) {
      const today = new Date();
      dateField.min = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
        .toISOString()
        .split("T")[0];
    }

    const selectedValue = (name) => {
      const selected = bookingForm.querySelector(`[name="${name}"]:checked`);
      return selected ? selected.value : "";
    };

    const currentPanel = () => bookingForm.querySelector(`[data-booking-step="${currentStep}"]`);

    const isStepValid = () => {
      if (currentStep === 1) return Boolean(selectedValue("service"));
      if (currentStep === 2) return Boolean(dateField && dateField.value && selectedValue("time"));
      const name = bookingForm.querySelector('input[name="name"]');
      const phone = bookingForm.querySelector('input[name="phone"]');
      return Boolean(name && name.value.trim().length >= 2 && phone && phone.value.trim().length >= 7);
    };

    const updateSummary = () => {
      const values = {
        service: selectedValue("service") || "—",
        date: dateField && dateField.value ? dateField.value : "—",
        time: selectedValue("time") || "—",
      };
      Object.keys(values).forEach((key) => {
        const target = bookingForm.querySelector(`[data-summary="${key}"]`);
        if (target) target.textContent = values[key];
      });
    };

    const focusStep = () => {
      const panel = currentPanel();
      if (!panel) return;
      const heading = panel.querySelector("h2");
      if (heading) {
        heading.setAttribute("tabindex", "-1");
        heading.focus({ preventScroll: true });
      }
      const shell = bookingForm.closest(".booking-shell");
      if (shell) {
        shell.scrollIntoView({
          behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth",
          block: "start",
        });
      }
    };

    const focusInvalid = () => {
      const panel = currentPanel();
      const field = panel ? panel.querySelector("input, select, textarea") : null;
      if (field) field.focus();
    };

    const renderStep = (moveFocus) => {
      steps.forEach((step) => {
        step.hidden = Number(step.dataset.bookingStep) !== currentStep;
      });
      indicators.forEach((indicator) => {
        const number = Number(indicator.dataset.stepIndicator);
        indicator.classList.toggle("is-active", number === currentStep);
        indicator.classList.toggle("is-complete", number < currentStep);
        if (number === currentStep) indicator.setAttribute("aria-current", "step");
        else indicator.removeAttribute("aria-current");
      });
      if (previous) previous.hidden = currentStep === 1;
      if (next) next.hidden = currentStep === 3;
      if (submit) submit.hidden = currentStep !== 3;
      updateSummary();
      if (moveFocus) focusStep();
    };

    bookingForm.addEventListener("change", () => {
      bookingForm.classList.remove("show-validation");
      updateSummary();
    });
    bookingForm.addEventListener("input", updateSummary);

    if (next) {
      next.addEventListener("click", () => {
        if (!isStepValid()) {
          bookingForm.classList.add("show-validation");
          focusInvalid();
          return;
        }
        bookingForm.classList.remove("show-validation");
        currentStep = Math.min(3, currentStep + 1);
        renderStep(true);
      });
    }

    if (previous) {
      previous.addEventListener("click", () => {
        bookingForm.classList.remove("show-validation");
        currentStep = Math.max(1, currentStep - 1);
        renderStep(true);
      });
    }

    bookingForm.addEventListener("submit", (event) => {
      if (!isStepValid()) {
        event.preventDefault();
        bookingForm.classList.add("show-validation");
        focusInvalid();
        return;
      }
      bookingForm.setAttribute("aria-busy", "true");
      if (submit) {
        submit.disabled = true;
        submit.classList.add("is-loading");
        submit.textContent = "در حال ارسال…";
      }
    });

    renderStep(false);
  }
})();
