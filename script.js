const header = document.querySelector("[data-site-header]");
const navToggle = document.querySelector("[data-nav-toggle]");
const nav = document.querySelector("[data-nav]");
const largeViewport = window.matchMedia("(min-width: 981px)");
const revealSelectors = [
    ".hero-copy",
    ".hero-visual",
    ".page-hero-shell > *",
    ".section-heading > *",
    ".feature",
    ".panel",
    ".path-card",
    ".advocacy-preview-card",
    ".advocacy-focus-card",
    ".advocacy-message",
    ".value-card",
    ".quote-panel",
    ".list-block",
    ".story-note",
    ".contact-card",
    ".contact-info-panel",
    ".contact-form-card",
    ".hero-stats li",
    ".cta-band",
    ".footer-shell > *"
];
const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
let lastFocusedElement = null;

const navOverlay = document.createElement("button");
navOverlay.type = "button";
navOverlay.className = "nav-overlay";
navOverlay.tabIndex = -1;
navOverlay.setAttribute("aria-hidden", "true");
document.body.append(navOverlay);

const normalizePath = (path) => {
    if (!path || path.endsWith("/")) {
        return "index.html";
    }

    const fileName = path.split("/").filter(Boolean).pop() || "index.html";

    return fileName.includes(".") ? fileName : "index.html";
};

const syncActiveNavigationLink = () => {
    if (!nav) {
        return;
    }

    const currentPage = normalizePath(window.location.pathname);

    nav.querySelectorAll("a[href]").forEach((link) => {
        const linkTarget = normalizePath(link.getAttribute("href") || "");
        const isCurrentPage = linkTarget === currentPage || (currentPage === "index.html" && linkTarget === "");

        if (isCurrentPage) {
            link.setAttribute("aria-current", "page");
        } else {
            link.removeAttribute("aria-current");
        }
    });
};

const getNavigationFocusables = () => {
    if (!navToggle || !nav) {
        return [];
    }

    return [navToggle, ...nav.querySelectorAll("a[href]")];
};

const syncNavigationLabels = (isOpen) => {
    if (!navToggle) {
        return;
    }

    const label = navToggle.querySelector("span:first-child");

    navToggle.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");

    if (label) {
        label.textContent = isOpen ? "Close" : "Menu";
    }
};

const closeNavigation = ({ restoreFocus = true } = {}) => {
    if (!header || !navToggle) {
        return;
    }

    header.classList.remove("nav-open");
    document.body.classList.remove("nav-open");
    navToggle.setAttribute("aria-expanded", "false");
    syncNavigationLabels(false);

    if (restoreFocus && lastFocusedElement instanceof HTMLElement) {
        lastFocusedElement.focus();
    }

    lastFocusedElement = null;
};

if (header && navToggle && nav) {
    if (!nav.id) {
        nav.id = "primary-navigation";
    }

    navToggle.setAttribute("aria-controls", nav.id);
    navToggle.setAttribute("aria-expanded", "false");
    syncNavigationLabels(false);
    syncActiveNavigationLink();

    navToggle.addEventListener("click", () => {
        const isOpen = navToggle.getAttribute("aria-expanded") === "true";

        if (isOpen) {
            closeNavigation();
            return;
        }

        lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        header.classList.add("nav-open");
        document.body.classList.add("nav-open");
        navToggle.setAttribute("aria-expanded", "true");
        syncNavigationLabels(true);

        const currentLink = nav.querySelector('[aria-current="page"]');
        const firstLink = nav.querySelector("a[href]");
        const focusTarget = currentLink instanceof HTMLElement ? currentLink : firstLink;

        if (focusTarget instanceof HTMLElement) {
            focusTarget.focus();
        }
    });

    nav.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => {
            closeNavigation({ restoreFocus: false });
        });
    });

    navOverlay.addEventListener("click", () => {
        closeNavigation();
    });

    document.addEventListener("click", (event) => {
        if (!header.classList.contains("nav-open")) {
            return;
        }

        if (!(event.target instanceof Node)) {
            return;
        }

        if (!header.contains(event.target)) {
            closeNavigation();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && header.classList.contains("nav-open")) {
            closeNavigation();
            return;
        }

        if (event.key !== "Tab" || !header.classList.contains("nav-open")) {
            return;
        }

        const focusable = getNavigationFocusables();

        if (!focusable.length) {
            return;
        }

        const firstElement = focusable[0];
        const lastElement = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
            return;
        }

        if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    });

    const handleViewportChange = (event) => {
        if (event.matches) {
            closeNavigation({ restoreFocus: false });
        }
    };

    if (typeof largeViewport.addEventListener === "function") {
        largeViewport.addEventListener("change", handleViewportChange);
    } else if (typeof largeViewport.addListener === "function") {
        largeViewport.addListener(handleViewportChange);
    }
}

const syncHeaderState = () => {
    if (!header) {
        return;
    }

    header.classList.toggle("is-scrolled", window.scrollY > 18);
};

const initializeRevealAnimations = () => {
    const revealTargets = document.querySelectorAll(revealSelectors.join(", "));

    if (!revealTargets.length) {
        return;
    }

    revealTargets.forEach((element, index) => {
        element.classList.add("reveal-ready");
        element.style.setProperty("--reveal-delay", `${(index % 6) * 70}ms`);
    });

    if (prefersReducedMotion.matches || typeof IntersectionObserver !== "function") {
        revealTargets.forEach((element) => {
            element.classList.add("is-visible");
        });
        return;
    }

    const revealObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.18,
            rootMargin: "0px 0px -8% 0px"
        }
    );

    revealTargets.forEach((element) => {
        revealObserver.observe(element);
    });
};

syncHeaderState();
window.addEventListener("scroll", syncHeaderState, { passive: true });
initializeRevealAnimations();

const initializeContactForm = () => {
    const form = document.querySelector("[data-contact-form]");

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const status = form.querySelector("[data-form-status]");
    const fields = {
        fullName: form.elements.namedItem("fullName"),
        email: form.elements.namedItem("email"),
        subject: form.elements.namedItem("subject"),
        message: form.elements.namedItem("message")
    };
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const getValue = (field) => field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement
        ? field.value.trim()
        : "";

    const setFieldError = (name, message) => {
        const field = fields[name];
        const error = form.querySelector(`[data-error-for="${name}"]`);
        const wrapper = field instanceof HTMLElement ? field.closest(".form-field") : null;

        if (error) {
            error.textContent = message;
        }

        if (wrapper) {
            wrapper.classList.toggle("is-invalid", Boolean(message));
        }

        if (field instanceof HTMLElement) {
            field.setAttribute("aria-invalid", message ? "true" : "false");
        }
    };

    const validate = () => {
        let isValid = true;

        if (!getValue(fields.fullName)) {
            setFieldError("fullName", "Please enter your full name.");
            isValid = false;
        } else {
            setFieldError("fullName", "");
        }

        const emailValue = getValue(fields.email);
        if (!emailValue) {
            setFieldError("email", "Please enter your email address.");
            isValid = false;
        } else if (!emailPattern.test(emailValue)) {
            setFieldError("email", "Please enter a valid email address.");
            isValid = false;
        } else {
            setFieldError("email", "");
        }

        if (!getValue(fields.subject)) {
            setFieldError("subject", "Please add a subject.");
            isValid = false;
        } else {
            setFieldError("subject", "");
        }

        if (!getValue(fields.message)) {
            setFieldError("message", "Please write a short message.");
            isValid = false;
        } else {
            setFieldError("message", "");
        }

        return isValid;
    };

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        if (status) {
            status.textContent = "";
            status.classList.remove("is-error");
        }

        if (!validate()) {
            if (status) {
                status.textContent = "Please review the highlighted fields and try again.";
                status.classList.add("is-error");
            }
            return;
        }

        form.reset();

        if (status) {
            status.textContent = "Thank you for reaching out. Your message has been received, and I will respond as soon as possible.";
        }
    });

    Object.values(fields).forEach((field) => {
        if (field instanceof HTMLElement) {
            field.addEventListener("input", () => {
                if (field.getAttribute("aria-invalid") === "true") {
                    validate();
                }
            });
        }
    });
};

initializeContactForm();

document.querySelectorAll("[data-current-year]").forEach((node) => {
    node.textContent = new Date().getFullYear().toString();
});
