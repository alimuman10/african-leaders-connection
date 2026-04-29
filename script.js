const header = document.querySelector("[data-site-header]");
const navToggle = document.querySelector("[data-nav-toggle]");
const nav = document.querySelector("[data-nav]");
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

const closeNavigation = () => {
    if (!header || !navToggle) {
        return;
    }

    header.classList.remove("nav-open");
    navToggle.setAttribute("aria-expanded", "false");
};

if (header && navToggle && nav) {
    navToggle.addEventListener("click", () => {
        const isOpen = navToggle.getAttribute("aria-expanded") === "true";

        if (isOpen) {
            closeNavigation();
            return;
        }

        header.classList.add("nav-open");
        navToggle.setAttribute("aria-expanded", "true");
    });

    nav.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => {
            closeNavigation();
        });
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
        }
    });

    const largeViewport = window.matchMedia("(min-width: 960px)");
    const handleViewportChange = (event) => {
        if (event.matches) {
            closeNavigation();
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
