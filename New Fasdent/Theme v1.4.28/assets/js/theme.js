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
    const timeInputs = Array.from(bookingForm.querySelectorAll('input[name="time"]'));
    const timeGuidance = bookingForm.querySelector("[data-time-guidance]");
    const policyNode = bookingForm.querySelector("[data-booking-policy]");
    let bookingPolicy = {};
    let currentStep = 1;

    if (policyNode) {
      try {
        bookingPolicy = JSON.parse(policyNode.textContent || "{}");
      } catch (error) {
        bookingPolicy = {};
      }
    }

    const selectedInput = (name) => bookingForm.querySelector(`[name="${name}"]:checked`);
    const selectedValue = (name) => {
      const selected = selectedInput(name);
      return selected ? selected.value : "";
    };
    const selectedServiceLabel = () => {
      const selected = selectedInput("service");
      return selected ? selected.getAttribute("data-service-label") || selected.value : "";
    };

    const currentPanel = () => bookingForm.querySelector(`[data-booking-step="${currentStep}"]`);

    const dayCodeForDate = (ymd) => {
      const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(ymd || "");
      if (!match) return "";
      const weekday = new Date(Date.UTC(Number(match[1]), Number(match[2]) - 1, Number(match[3]), 12)).getUTCDay();
      return ["SU", "MO", "TU", "WE", "TH", "FR", "SA"][weekday] || "";
    };

    const policyMinimum = () => {
      const timezone = bookingPolicy.timezone || "Asia/Tehran";
      const leadMinutes = Number(bookingPolicy.min_lead_minutes || 0);
      try {
        const instant = new Date(Date.now() + leadMinutes * 60000);
        const parts = new Intl.DateTimeFormat("en-CA", {
          timeZone: timezone,
          year: "numeric",
          month: "2-digit",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
          second: "2-digit",
          hourCycle: "h23",
        }).formatToParts(instant);
        const values = {};
        parts.forEach((part) => {
          if (part.type !== "literal") values[part.type] = part.value;
        });
        if (values.year && values.month && values.day && values.hour && values.minute && values.second) {
          return {
            date: `${values.year}-${values.month}-${values.day}`,
            time: `${values.hour}:${values.minute}:${values.second}`,
          };
        }
      } catch (error) {
        // Fall through to the server-rendered WordPress-timezone snapshot.
      }
      return {
        date: bookingPolicy.minimum_date || bookingPolicy.server_now_date || "",
        time: bookingPolicy.minimum_time || bookingPolicy.server_now_time || "00:00:00",
      };
    };

    const isStrictlyFutureSlot = (dateValue, timeValue) => {
      const minimum = policyMinimum();
      if (!minimum.date || !dateValue) return true;
      if (dateValue > minimum.date) return true;
      if (dateValue < minimum.date) return false;
      return `${timeValue}:00` > minimum.time;
    };

    const isWithinWeeklyHours = (dateValue, timeValue) => {
      if (!bookingPolicy.opening_hours_configured) return true;
      if (!bookingPolicy.opening_hours_valid) return false;
      const schedule = bookingPolicy.schedule || {};
      const ranges = schedule[dayCodeForDate(dateValue)] || [];
      return ranges.some((range) => Array.isArray(range) && range.length === 2 && timeValue >= range[0] && timeValue < range[1]);
    };

    const updateTimeChoices = () => {
      const dateValue = dateField ? dateField.value : "";
      let availableCount = 0;

      timeInputs.forEach((input) => {
        const wrapper = input.closest("[data-booking-time-option]");
        const validForDate = Boolean(dateValue) && isStrictlyFutureSlot(dateValue, input.value) && isWithinWeeklyHours(dateValue, input.value);
        input.disabled = !validForDate;
        if (wrapper) wrapper.hidden = Boolean(dateValue) && !validForDate;
        if (!dateValue && wrapper) wrapper.hidden = false;
        if (!validForDate && input.checked) input.checked = false;
        if (validForDate) availableCount += 1;
      });

      if (!timeGuidance) return;
      if (!dateValue) {
        timeGuidance.textContent = "ابتدا تاریخ را انتخاب کنید تا ساعت‌های پیشنهادی همان روز نمایش داده شوند.";
      } else if (!availableCount) {
        timeGuidance.textContent = "برای این تاریخ ساعت پیشنهادی قابل انتخابی وجود ندارد. تاریخ دیگری انتخاب کنید یا مستقیماً با کلینیک تماس بگیرید.";
      } else if (!bookingPolicy.opening_hours_configured) {
        timeGuidance.textContent = "این ساعت‌ها فقط زمان‌های پیشنهادی قابل درخواست هستند؛ ساعات کاری رسمی در سامانه ثبت نشده و availability لحظه‌ای ادعا نمی‌شود.";
      } else {
        timeGuidance.textContent = "فقط ساعت‌های پیشنهادی داخل ساعات کاری رسمی این روز نمایش داده شده‌اند؛ تأیید نهایی با تماس کلینیک انجام می‌شود.";
      }
    };

    const isStepValid = () => {
      if (currentStep === 1) return Boolean(selectedValue("service"));
      if (currentStep === 2) return Boolean(dateField && dateField.value && selectedValue("time"));
      const name = bookingForm.querySelector('input[name="name"]');
      const phone = bookingForm.querySelector('input[name="phone"]');
      return Boolean(name && name.value.trim().length >= 2 && phone && phone.value.trim().length >= 7);
    };

    const updateSummary = () => {
      const values = {
        service: selectedServiceLabel() || "—",
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
      const field = panel ? panel.querySelector("input:not(:disabled), select:not(:disabled), textarea:not(:disabled)") : null;
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

    bookingForm.addEventListener("change", (event) => {
      bookingForm.classList.remove("show-validation");
      if (event.target === dateField) updateTimeChoices();
      updateSummary();
    });
    bookingForm.addEventListener("input", updateSummary);

    if (next) {
      next.addEventListener("click", () => {
        if (currentStep === 2) updateTimeChoices();
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

    // Multi-step: native HTML5 "required" on hidden panels blocks submit with no visible error.
    bookingForm.setAttribute("novalidate", "novalidate");

    const isFormComplete = () => {
      const name = bookingForm.querySelector('input[name="name"]');
      const phone = bookingForm.querySelector('input[name="phone"]');
      return Boolean(
        selectedValue("service") &&
        dateField &&
        dateField.value &&
        selectedValue("time") &&
        name &&
        name.value.trim().length >= 2 &&
        phone &&
        phone.value.trim().length >= 7
      );
    };

    bookingForm.addEventListener("submit", (event) => {
      // Re-evaluate same-day time against the current WP-timezone clock before POST.
      updateTimeChoices();
      if (!isFormComplete()) {
        event.preventDefault();
        bookingForm.classList.add("show-validation");
        if (!selectedValue("service")) currentStep = 1;
        else if (!dateField || !dateField.value || !selectedValue("time")) currentStep = 2;
        else currentStep = 3;
        renderStep(true);
        focusInvalid();
        return;
      }

      bookingForm.setAttribute("aria-busy", "true");
      if (submit) {
        // Do not disable the submit control during native POST navigation.
        submit.classList.add("is-loading");
        submit.setAttribute("aria-disabled", "true");
        submit.textContent = "در حال ارسال…";
      }
    });

    updateTimeChoices();
    renderStep(false);
  }
})();
