import "./bootstrap";

/**
 * Theme states:
 * - stored: "light" | "dark" | "auto"
 * - effective: "light" | "dark" (resolved from auto + prefers-color-scheme)
 */

function prefersDark() {
    return (
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
    );
}

function resolveTheme() {
    const stored = localStorage.getItem("bs-theme") || "auto";
    const effective =
        stored === "auto" ? (prefersDark() ? "dark" : "light") : stored;
    return { stored, effective };
}

function applyThemeToDom() {
    const { effective } = resolveTheme();
    document.documentElement.setAttribute("data-bs-theme", effective);
}

function setThemeStored(value) {
    localStorage.setItem("bs-theme", value);
}

function cycleTheme() {
    const current = localStorage.getItem("bs-theme") || "auto";
    const next =
        current === "auto" ? "light" : current === "light" ? "dark" : "auto";
    setThemeStored(next);

    applyThemeToDom();
    updateThemeButtonUI();
}

function updateThemeButtonUI() {
    const btn = document.getElementById("themeToggleBtn");
    const icon = document.getElementById("themeToggleIcon");
    if (!btn || !icon) return;

    const { stored, effective } = resolveTheme();

    const iconClass =
        stored === "auto"
            ? "fa-solid fa-wand-magic-sparkles"
            : stored === "light"
              ? "fa-solid fa-sun"
              : "fa-solid fa-moon";

    icon.className = iconClass;

    const label =
        stored === "auto"
            ? `Tema: Otomatik (${effective})`
            : stored === "light"
              ? "Tema: Açık"
              : "Tema: Koyu";

    btn.setAttribute("aria-label", label);
    btn.setAttribute("title", label);
}

function bindThemeButton() {
    const btn = document.getElementById("themeToggleBtn");
    if (!btn) return;

    // Rebind için önce temizle
    btn.onclick = null;

    btn.addEventListener("click", (e) => {
        e.preventDefault();
        cycleTheme();
    });
}

function closeAnyOffcanvas() {
    const el = document.getElementById("categoriesOffcanvas");
    if (!el || !window.bootstrap) return;

    const instance = window.bootstrap.Offcanvas.getInstance(el);
    if (instance) instance.hide();
}

/* ==============================================================================
   SWIPER INIT (TOP + FADE)
   ============================================================================== */

function initFadeSwipers() {
    // Left/Right: data-swiper-fade="1"
    document.querySelectorAll('[data-swiper-fade="1"]').forEach((wrap) => {
        const uid = wrap.getAttribute("data-swiper-uid");
        const count = parseInt(
            wrap.getAttribute("data-swiper-count") || "0",
            10,
        );
        const delay = parseInt(
            wrap.getAttribute("data-swiper-delay") || "4500",
            10,
        );
        const speed = parseInt(
            wrap.getAttribute("data-swiper-speed") || "1050",
            10,
        );

        if (!uid || count <= 0) return;

        const root = document.getElementById(uid);
        if (!root) return;

        if (root.__swiper) {
            try {
                root.__swiper.destroy(true, true);
            } catch (e) {}
            root.__swiper = null;
        }

        const canLoop = count > 1;

        requestAnimationFrame(() =>
            requestAnimationFrame(() => {
                root.__swiper = new Swiper(root, {
                    slidesPerView: 1,
                    spaceBetween: 0,

                    effect: "fade",
                    fadeEffect: { crossFade: true },

                    loop: canLoop,
                    allowTouchMove: false,
                    simulateTouch: false,

                    autoplay: canLoop
                        ? {
                              delay,
                              disableOnInteraction: false,
                              pauseOnMouseEnter: false,
                          }
                        : false,

                    navigation: false,
                    speed,

                    on: {
                        init() {
                            try {
                                this.update();
                            } catch (e) {}
                            try {
                                if (this.autoplay) this.autoplay.start();
                            } catch (e) {}
                        },
                    },
                });

                try {
                    root.__swiper.update();
                } catch (e) {}
            }),
        );
    });
}

function initTopSwipers() {
    // Top/Bottom: data-swiper-top="1"
    document.querySelectorAll('[data-swiper-top="1"]').forEach((wrap) => {
        const uid = wrap.getAttribute("data-swiper-uid");
        const slideCount = parseInt(
            wrap.getAttribute("data-swiper-count") || "0",
            10,
        );
        const delay = parseInt(
            wrap.getAttribute("data-swiper-delay") || "2200",
            10,
        );

        if (!uid || slideCount <= 0) return;

        const root = document.getElementById(uid);
        if (!root) return;

        if (root.__swiper) {
            try {
                root.__swiper.destroy(true, true);
            } catch (e) {}
            root.__swiper = null;
        }

        requestAnimationFrame(() =>
            requestAnimationFrame(() => {
                root.__swiper = new Swiper(root, {
                    slidesPerView: 1,
                    spaceBetween: 16,

                    // ✅ zoom’da zıplama olmasın (CSS aspect-ratio ile sabit)
                    autoHeight: false,

                    loop: slideCount > 1,

                    autoplay:
                        slideCount > 1
                            ? {
                                  delay,
                                  disableOnInteraction: false,
                                  pauseOnMouseEnter: true,
                              }
                            : false,

                    pagination: {
                        el: root.querySelector(".swiper-pagination"),
                        clickable: true,
                    },

                    navigation: false,

                    // ✅ mobilde 1 istiyorsun -> 0 breakpoint 1 olmalı
                    breakpoints: {
                        0: { slidesPerView: 1, spaceBetween: 12 }, // ✅ mobil: 1
                        640: { slidesPerView: 2, spaceBetween: 16 }, // küçük+ : 2
                        768: { slidesPerView: 4, spaceBetween: 18 }, // tablet+: 4
                        1024: { slidesPerView: 5, spaceBetween: 20 }, // desktop: 5
                    },

                    on: {
                        init() {
                            try {
                                this.update();
                            } catch (e) {}
                        },
                    },
                });

                try {
                    root.__swiper.update();
                } catch (e) {}
            }),
        );
    });
}

function initFrontCarousels() {
    document.querySelectorAll('[data-swiper-front="1"]').forEach((wrap) => {
        const uid = wrap.getAttribute("data-swiper-uid");
        const slideCount = parseInt(
            wrap.getAttribute("data-swiper-count") || "0",
            10,
        );
        const delay = parseInt(
            wrap.getAttribute("data-swiper-delay") || "2200",
            10,
        );

        if (!uid || slideCount <= 0) return;

        const root = document.getElementById(uid);
        if (!root) return;

        if (root.__swiper) {
            try {
                root.__swiper.destroy(true, true);
            } catch (e) {}
            root.__swiper = null;
        }

        requestAnimationFrame(() =>
            requestAnimationFrame(() => {
                root.__swiper = new Swiper(root, {
                    slidesPerView: 1,
                    spaceBetween: 12,

                    loop: slideCount > 1,

                    autoplay:
                        slideCount > 1
                            ? {
                                  delay,
                                  disableOnInteraction: false,
                                  pauseOnMouseEnter: true,
                              }
                            : false,

                    pagination: {
                        el: root.querySelector(".swiper-pagination"),
                        clickable: true,
                    },

                    navigation: false,

                    breakpoints: {
                        0: { slidesPerView: 1, spaceBetween: 12 }, // ✅ mobil: 1
                        768: { slidesPerView: 2, spaceBetween: 16 }, // tablet+: 2
                        1200: { slidesPerView: 3, spaceBetween: 18 }, // büyük ekran: 3
                    },

                    watchOverflow: true,

                    on: {
                        init() {
                            try {
                                this.update();
                            } catch (e) {}
                        },
                    },
                });

                try {
                    root.__swiper.update();
                } catch (e) {}
            }),
        );
    });
}

/**
 * Swiper CDN geç gelirse diye retry mekanizması:
 */
let swiperTries = 0;
let swiperTimer = null;

function initSwipersWithRetry() {
    if (typeof window.Swiper === "undefined") {
        swiperTries++;
        if (swiperTries > 120) {
            if (swiperTimer) clearInterval(swiperTimer);
            swiperTimer = null;
            return;
        }

        if (!swiperTimer) {
            swiperTimer = setInterval(() => {
                if (typeof window.Swiper !== "undefined") {
                    clearInterval(swiperTimer);
                    swiperTimer = null;
                    initSwipers();
                } else {
                    swiperTries++;
                    if (swiperTries > 120) {
                        clearInterval(swiperTimer);
                        swiperTimer = null;
                    }
                }
            }, 100);
        }
        return;
    }

    initSwipers();
}

function initSwipers() {
    initFadeSwipers();
    initTopSwipers();
    initFrontCarousels();
}

/* ==============================================================================
   POPUP MODAL (Bootstrap)
   ============================================================================== */

function initPopupModals() {
    const modals = document.querySelectorAll('[data-popup-modal="1"]');
    if (!modals.length) return;

    if (!window.bootstrap?.Modal) return;

    modals.forEach((el) => {
        if (el.__popupInited) return;
        el.__popupInited = true;

        const key =
            el.getAttribute("data-popup-storage-key") ||
            "_advert_popup_closed_v1";

        let isClosed = false;
        try {
            isClosed = localStorage.getItem(key) === "1";
        } catch (e) {}

        if (isClosed) {
            try {
                el.remove();
            } catch (e) {}
            return;
        }

        let instance = window.bootstrap.Modal.getInstance(el);
        if (!instance) {
            instance = new window.bootstrap.Modal(el, {
                backdrop: true,
                keyboard: true,
                focus: true,
            });
        }

        el.addEventListener(
            "hidden.bs.modal",
            () => {
                try {
                    localStorage.setItem(key, "1");
                } catch (e) {}

                setTimeout(() => {
                    try {
                        el.remove();
                    } catch (e) {}
                }, 0);
            },
            { once: true },
        );

        instance.show();
    });
}

let bsTries = 0;
let bsTimer = null;

function initPopupWithBootstrapRetry() {
    if (window.bootstrap?.Modal) {
        initPopupModals();
        return;
    }

    bsTries++;
    if (bsTries > 120) return;

    if (!bsTimer) {
        bsTimer = setInterval(() => {
            if (window.bootstrap?.Modal) {
                clearInterval(bsTimer);
                bsTimer = null;
                initPopupModals();
            } else {
                bsTries++;
                if (bsTries > 120) {
                    clearInterval(bsTimer);
                    bsTimer = null;
                }
            }
        }, 100);
    }
}

/* ==============================================================================
   PAGE READY
   ============================================================================== */

function onPageReady() {
    applyThemeToDom();
    updateThemeButtonUI();
    bindThemeButton();
    closeAnyOffcanvas();

    initSwipersWithRetry();
    initPopupWithBootstrapRetry();
}

// İlk yükleme
document.addEventListener("DOMContentLoaded", onPageReady);

// Livewire Navigate sonrası
document.addEventListener("livewire:navigated", onPageReady);

// Livewire DOM update sonrası
if (window.Livewire?.hook) {
    Livewire.hook("message.processed", () => {
        setTimeout(() => {
            bindThemeButton();
            initSwipersWithRetry();
            initPopupWithBootstrapRetry(); // ✅ eksikti: DOM morph sonrası popup varsa çalışsın
        }, 0);
    });
}

// Sistem teması değişirse (auto modunda) anında uygula
if (window.matchMedia) {
    const mql = window.matchMedia("(prefers-color-scheme: dark)");
    const handler = () => {
        const { stored } = resolveTheme();
        if (stored === "auto") {
            applyThemeToDom();
            updateThemeButtonUI();
        }
    };

    if (typeof mql.addEventListener === "function") {
        mql.addEventListener("change", handler);
    } else if (typeof mql.addListener === "function") {
        mql.addListener(handler);
    }
}

