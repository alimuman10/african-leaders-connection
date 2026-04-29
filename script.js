const header = document.querySelector("[data-site-header]");
const navToggle = document.querySelector("[data-nav-toggle]");
const nav = document.querySelector("[data-nav]");

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

document.querySelectorAll("[data-current-year]").forEach((node) => {
    node.textContent = new Date().getFullYear().toString();
});
