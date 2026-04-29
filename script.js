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
    ".value-card",
    ".quote-panel",
    ".list-block",
    ".story-note",
    ".contact-card",
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

    navToggle.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");
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
    syncNavigationLabels(false);

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
        if (event.key === "Escape") {
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

document.querySelectorAll("[data-current-year]").forEach((node) => {
    node.textContent = new Date().getFullYear().toString();
});
