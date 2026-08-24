/* ==========================================================================
   MAIN.JS — PHP MVC Version
   Data comes from PHP via window.__* globals
   Alfiz Ilham Portfolio
   ========================================================================== */

document.addEventListener("DOMContentLoaded", () => {
  // Data from PHP (passed via window.__* globals in views)
  window.TOOLS_DATA = window.__TOOLS || [];
  window.FAQS_DATA = window.__FAQS || [];
  window.SERVICES_DATA = window.__SERVICES || [];
  window.LANG = window.__LANG || 'en';
  window.LANG_DATA = window.__LANG_DATA || {};
  window.EMAILJS_CONFIG = window.__EMAILJS || {};
  window.CONTACT_LANG = window.__CONTACT_LANG || {};

  initLenis();
  initNavbar();
  initCurvedMarquee();
  initIntroMagnet();
  initHeroParallax();
  initHeroReveal();
  initScrollProgress();
  renderIconGrid();
  initSkillFilters();
  initServicesAccordion();
  initCircularGallery();
  initEditorMode();
  renderGallery();
  renderTestimonials();
  renderFaqPanel();
  initFaqAccordion();
  initContactMap();
  initContactForm();
  initLiveClock();
  initScrollReveal();
  initFoldText();
  initStatCounters();
  initVisitorCounter();
  initCertificates();
  initLucideIcons();
});

/* --------------------------------------------------------------------------
   LENIS SMOOTH SCROLL
   -------------------------------------------------------------------------- */

function initLenis() {
  const prefersReduced =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (prefersReduced || typeof Lenis === "undefined") return;

  const lenis = new Lenis({
    lerp: 0.1,
    smoothWheel: true,
    anchors: true,
  });
  window.__lenis = lenis;

  function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  }
  requestAnimationFrame(raf);
}

/* --------------------------------------------------------------------------
   NAVBAR
   -------------------------------------------------------------------------- */

function initNavbar() {
  const navbar = document.getElementById("navbar");
  const hamburger = document.getElementById("hamburger");
  const mobileMenu = document.getElementById("mobileMenu");
  const mobileClose = document.getElementById("mobileClose");
  if (!navbar || !hamburger || !mobileMenu) return;

  let lastScrollY = window.scrollY;
  let lenis = null;
  if (typeof Lenis !== "undefined") lenis = window.__lenis;

  function onScroll() {
    const y = window.scrollY;
    const atTop = navbar.classList.contains("at-top");

    navbar.classList.toggle("scrolled", !atTop && y > 80);

    const scrollingDown = y > lastScrollY;
    const scrollingUp = y < lastScrollY;
    lastScrollY = y;

    if (atTop || y <= 80) {
      navbar.classList.remove("hidden");
    } else if (scrollingDown && y > 120) {
      navbar.classList.add("hidden");
    } else if (scrollingUp) {
      navbar.classList.remove("hidden");
    }
  }

  function applyTopState(isIntro) {
    navbar.classList.toggle("at-top", isIntro);
    onScroll();
  }

  const topSection = document.getElementById("intro") || document.getElementById("hero");

  if (topSection && "IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries) => applyTopState(entries[0].isIntersecting),
      { threshold: 0 }
    );
    observer.observe(topSection);
  } else {
    applyTopState(window.scrollY <= 80);
    window.addEventListener("scroll", () => applyTopState(window.scrollY <= 80), {
      passive: true,
    });
  }

  if (lenis) {
    lenis.on("scroll", onScroll);
  } else {
    window.addEventListener("scroll", onScroll, { passive: true });
  }
  onScroll();

  // Mobile menu
  function openMenu() {
    mobileMenu.classList.add("open");
    document.body.style.overflow = "hidden";
    if (lenis) lenis.stop();
  }

  function closeMenu() {
    mobileMenu.classList.remove("open");
    document.body.style.overflow = "";
    if (lenis) lenis.start();
  }

  hamburger.addEventListener("click", openMenu);
  mobileClose.addEventListener("click", closeMenu);
  mobileMenu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", closeMenu);
  });

  // Scrollspy
  const sectionIds = ["about", "skills", "project", "service", "gallery", "faq", "contact"];
  const navLinks = document.querySelectorAll(".navbar-links a");
  const mobileLinks = document.querySelectorAll(".mobile-menu-links a");

  const sectionEls = sectionIds
    .map((id) => document.getElementById(id))
    .filter(Boolean);

  const setActive = (id) => {
    navLinks.forEach((a) =>
      a.classList.toggle("active", a.getAttribute("href") === "#" + id)
    );
    mobileLinks.forEach((a) =>
      a.classList.toggle("active", a.getAttribute("href") === "#" + id)
    );
  };

  if ("IntersectionObserver" in window && sectionEls.length) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) setActive(entry.target.id);
        });
      },
      { rootMargin: "-45% 0px -50% 0px", threshold: 0 }
    );
    sectionEls.forEach((sec) => observer.observe(sec));
  }
}

/* --------------------------------------------------------------------------
   CURVED MARQUEE (SVG textPath, loop + drag interaktif)
   -------------------------------------------------------------------------- */

function initCurvedMarquee() {
  const measure = document.getElementById("curved-measure");
  const textPath = document.getElementById("curved-textpath");
  const path = document.getElementById("curved-path");
  const jacket = document.querySelector(".curved-loop-jacket");
  if (!measure || !textPath || !path || !jacket) return;

  const text = measure.textContent;
  const spacing = measure.getComputedTextLength();
  if (!spacing) return;

  const totalText = text
    ? Array(Math.ceil(1800 / spacing) + 2).fill(text).join("")
    : text;
  textPath.textContent = totalText;

  const prefersReduced =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (prefersReduced) {
    jacket.style.cursor = "auto";
    return;
  }

  const SPEED = 1.5;
  let offset = -spacing;
  let dir = 1;
  let dragging = false;
  let lastX = 0;
  let vel = 0;
  let rafId;

  textPath.setAttribute("startOffset", offset + "px");

  function wrap(value) {
    if (value <= -spacing) value += spacing;
    if (value > 0) value -= spacing;
    return value;
  }

  function apply(value) {
    offset = wrap(value);
    textPath.setAttribute("startOffset", offset + "px");
  }

  function step() {
    if (!dragging) apply(offset + dir * SPEED);
    rafId = requestAnimationFrame(step);
  }

  function onDown(e) {
    dragging = true;
    lastX = e.clientX;
    vel = 0;
    jacket.setPointerCapture(e.pointerId);
    jacket.style.cursor = "grabbing";
  }

  function onMove(e) {
    if (!dragging) return;
    const dx = e.clientX - lastX;
    lastX = e.clientX;
    vel = dx;
    apply(offset + dx);
  }

  function endDrag() {
    dragging = false;
    jacket.style.cursor = "grab";
    dir = vel >= 0 ? 1 : -1;
  }

  jacket.style.cursor = "grab";
  jacket.addEventListener("pointerdown", onDown);
  jacket.addEventListener("pointermove", onMove);
  jacket.addEventListener("pointerup", endDrag);
  jacket.addEventListener("pointerleave", endDrag);
  rafId = requestAnimationFrame(step);

  return () => cancelAnimationFrame(rafId);
}

/* --------------------------------------------------------------------------
   MAGNET HELPER
   -------------------------------------------------------------------------- */

function attachMagnet(section, el, magnetRadius, strength, lerp) {
  if (!section || !el) return () => {};

  const prefersReduced =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (prefersReduced) return () => {};

  let targetX = 0, targetY = 0, currentX = 0, currentY = 0, rafId;

  function onMove(e) {
    const rect = el.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 2;
    const dx = e.clientX - cx;
    const dy = e.clientY - cy;
    const dist = Math.hypot(dx, dy);

    if (dist < magnetRadius && dist > 0) {
      targetX = (dx / dist) * (magnetRadius - dist) * strength;
      targetY = (dy / dist) * (magnetRadius - dist) * strength;
    } else {
      targetX = 0;
      targetY = 0;
    }
  }

  function tick() {
    currentX += (targetX - currentX) * lerp;
    currentY += (targetY - currentY) * lerp;
    el.style.transform = `translate(${currentX.toFixed(2)}px, ${currentY.toFixed(2)}px)`;
    rafId = requestAnimationFrame(tick);
  }

  section.addEventListener("mousemove", onMove);
  section.addEventListener("mouseleave", () => { targetX = 0; targetY = 0; });
  rafId = requestAnimationFrame(tick);

  return () => cancelAnimationFrame(rafId);
}

function initIntroMagnet() {
  const section = document.getElementById("intro");
  const mascot = document.querySelector(".intro-mascot");
  if (!section || !mascot) return;
  attachMagnet(section, mascot, 220, 0.25, 0.12);
}

/* --------------------------------------------------------------------------
   HERO PARALLAX
   -------------------------------------------------------------------------- */

function initHeroParallax() {
  const section = document.getElementById("hero");
  const photo = document.querySelector(".hero-photo");
  if (!section || !photo) return;

  const prefersReduced =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (prefersReduced) return;

  let rafId, lastY = 0;

  const tick = () => {
    const rect = section.getBoundingClientRect();
    if (rect.top !== lastY) {
      lastY = rect.top;
      const progress = Math.max(0, Math.min(1, -rect.top / window.innerHeight));
      photo.style.transform = `translateY(${(-progress * 120).toFixed(2)}px) scale(${(1 - progress * 0.08).toFixed(3)})`;
    }
    rafId = requestAnimationFrame(tick);
  };

  rafId = requestAnimationFrame(tick);
  return () => cancelAnimationFrame(rafId);
}

/* --------------------------------------------------------------------------
   HERO REVEAL
   -------------------------------------------------------------------------- */

function initHeroReveal() {
  const items = document.querySelectorAll("#hero .reveal-item");
  if (!items.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const delay = parseInt(entry.target.dataset.delay || "0", 10);
          entry.target.style.transitionDelay = delay + "ms";
          entry.target.classList.add("in-view");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.2 }
  );

  items.forEach((item) => observer.observe(item));
}

/* --------------------------------------------------------------------------
   SCROLL PROGRESS BAR
   -------------------------------------------------------------------------- */

function initScrollProgress() {
  const bar = document.getElementById("scrollProgress");
  if (!bar) return;

  const update = () => {
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const pct = max > 0 ? (window.scrollY / max) * 100 : 0;
    bar.style.width = pct.toFixed(2) + "%";
  };

  if (window.__lenis) {
    window.__lenis.on("scroll", update);
  } else {
    window.addEventListener("scroll", update, { passive: true });
  }
  update();
}

/* --------------------------------------------------------------------------
   ICON GRID (from PHP data)
   -------------------------------------------------------------------------- */

function renderIconGrid(filter = "all", search = "") {
  const grid = document.getElementById("iconGrid");
  if (!grid) return;

  const tools = window.TOOLS_DATA;
  const emptyState = document.getElementById("skillEmptyState");

  const filtered = tools.filter((tool) => {
    const matchFilter = filter === "all" || tool.category === filter;
    const matchSearch = tool.name.toLowerCase().includes(search.toLowerCase());
    return matchFilter && matchSearch;
  });

  if (!filtered.length) {
    grid.innerHTML = "";
    if (emptyState) emptyState.hidden = false;
    return;
  }

  if (emptyState) emptyState.hidden = true;

  const gridHTML = filtered.map(
    (tool) => `
    <div class="icon-grid-item"${tool.category_label ? ` data-tooltip="${tool.category_label}"` : ""}>
      <img src="${tool.icon}" alt="${tool.name}" loading="lazy" />
      <span>${tool.name}</span>
    </div>`
  ).join("");

  grid.innerHTML = gridHTML;
}

function initSkillFilters() {
  const tabs = document.getElementById("skillTabs");
  const searchInput = document.getElementById("skillSearch");
  if (!tabs) return;

  let activeFilter = "all";

  tabs.addEventListener("click", (e) => {
    const pill = e.target.closest(".filter-pill");
    if (!pill) return;

    tabs.querySelectorAll(".filter-pill").forEach((p) => {
      p.classList.remove("active");
      p.setAttribute("aria-pressed", "false");
    });
    pill.classList.add("active");
    pill.setAttribute("aria-pressed", "true");

    activeFilter = pill.dataset.filter;
    renderIconGrid(activeFilter, searchInput ? searchInput.value : "");
  });

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      renderIconGrid(activeFilter, searchInput.value);
    });
  }
}

/* --------------------------------------------------------------------------
   SERVICES (from PHP data)
   -------------------------------------------------------------------------- */

/* --------------------------------------------------------------------------
   SERVICES ACCORDION GALLERY (vanilla port of ReactBits AccordionGallery)
   -------------------------------------------------------------------------- */

function initServicesAccordion() {
  const root = document.getElementById("servicesAccordion");
  const descEl = document.getElementById("servicesActiveDesc");
  const items = window.SERVICES_DATA || [];
  if (!root || !items.length || typeof gsap === "undefined") return;

  const count = items.length;
  const prefersReduced =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const HEIGHT = 460;
  const GAP = 10;
  const RADIUS = 16;
  const EXPAND_RATIO = 0.52;
  const DURATION = prefersReduced ? 0 : 0.6;
  const EASE = "power3.out";
  const PARALLAX = 0.5;
  const TILT = 8;
  const STAGGER = 0.06;
  const TRIGGER = "hover";
  const SHOW_LABELS = true;
  const GRAYSCALE = true;
  const OVERLAY_COLOR = "#060010";
  const TEXT_COLOR = "#ffffff";
  const ACCENT_COLOR = "#ffffff";

  root.style.height = HEIGHT + "px";
  root.style.setProperty("--ag-gap", GAP + "px");
  root.style.setProperty("--ag-radius", RADIUS + "px");
  root.style.setProperty("--ag-accent", ACCENT_COLOR);
  root.style.setProperty("--ag-overlay", OVERLAY_COLOR);
  root.style.setProperty("--ag-text", TEXT_COLOR);

  let activeIndex = 0;
  let mediaSize = 320;
  let tlRef = null;

  // Build DOM
  items.forEach((item, i) => {
    const panel = document.createElement("div");
    panel.className = "ag-panel" + (i === activeIndex ? " ag-panel--active" : "");
    panel.style.borderRadius = RADIUS + "px";
    panel.setAttribute("role", "listitem");
    panel.setAttribute("tabindex", "0");
    panel.setAttribute("aria-label", item.label || item.title || "");

    const frame = document.createElement("span");
    frame.className = "ag-panel__frame";

    const media = document.createElement("span");
    media.className = "ag-panel__media";
    const img = document.createElement("img");
    img.src = item.image || "";
    img.alt = item.alt || item.label || item.title || "";
    img.draggable = false;
    media.appendChild(img);

    const overlay = document.createElement("span");
    overlay.className = "ag-panel__overlay";
    overlay.setAttribute("aria-hidden", "true");

    frame.appendChild(media);
    frame.appendChild(overlay);

    panel.appendChild(frame);

    if (SHOW_LABELS) {
      const label = document.createElement("span");
      label.className = "ag-panel__label";
      label.setAttribute("aria-hidden", "true");

      const bar = document.createElement("span");
      bar.className = "ag-panel__bar";

      const text = document.createElement("span");
      text.className = "ag-panel__text";

      const titleEl = document.createElement("span");
      titleEl.className = "ag-panel__title";
      titleEl.textContent = item.label || item.title || "";

      const descEl = document.createElement("span");
      descEl.className = "ag-panel__desc";
      descEl.textContent = item.description || "";

      text.appendChild(titleEl);
      text.appendChild(descEl);
      label.appendChild(bar);
      label.appendChild(text);
      panel.appendChild(label);
    }

    panel.addEventListener("mouseenter", function () {
      if (TRIGGER === "hover") setActive(i);
    });
    panel.addEventListener("click", function (e) {
      if (i !== activeIndex) {
        e.preventDefault();
        setActive(i);
      }
    });
    panel.addEventListener("focus", function () {
      setActive(i);
    });
    panel.addEventListener("keydown", function (e) {
      if (e.key === "ArrowRight" || e.key === "ArrowDown") {
        e.preventDefault();
        setActive((i + 1) % count);
      } else if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
        e.preventDefault();
        setActive((i - 1 + count) % count);
      }
    });

    panel.dataset.index = i;
    root.appendChild(panel);
  });

  function setActive(index) {
    if (index === activeIndex) return;
    activeIndex = index;
    applyLayout(true);
  }

  function applyLayout(animate) {
    const panels = root.querySelectorAll(".ag-panel");
    if (!panels.length) return;

    const totalWidth = root.offsetWidth - GAP * (count - 1);
    const grow = count > 1 ? (EXPAND_RATIO * (count - 1)) / (1 - EXPAND_RATIO) : 1;
    const dur = animate ? DURATION : 0;

    tlRef?.kill();
    const tl = gsap.timeline();

    panels.forEach(function (panel, i) {
      const isActive = i === activeIndex;
      const media = panel.querySelector(".ag-panel__media");
      const bar = panel.querySelector(".ag-panel__bar");
      const text = panel.querySelector(".ag-panel__text");

      const rot = isActive ? 0 : i < activeIndex ? TILT : -TILT;

      tl.to(
        panel,
        {
          flexGrow: isActive ? grow : 1,
          rotateY: rot,
          duration: dur,
          ease: EASE,
        },
        0
      );

      if (media) {
        const drift = Math.max(-1.5, Math.min(1.5, activeIndex - i));
        const shift = drift * PARALLAX * mediaSize * 0.06;
        const gray = GRAYSCALE ? (isActive ? 0 : 1) : 0;

        tl.to(
          media,
          {
            xPercent: -50,
            yPercent: -50,
            x: isActive ? 0 : shift,
            "--ag-gray": gray,
            "--ag-dim": isActive ? 0 : 0.35,
            duration: dur,
            ease: EASE,
          },
          0
        );
      }

      if (SHOW_LABELS && bar && text) {
        if (isActive) {
          tl.to(
            [bar, text],
            { opacity: 1, x: 0, duration: dur, ease: EASE, stagger: prefersReduced ? 0 : STAGGER },
            0
          );
        } else {
          tl.to(
            [bar, text],
            { opacity: 0, x: -14, duration: dur * 0.6, ease: EASE },
            0
          );
        }
      }
    });

    tlRef = tl;
  }

  function measure() {
    const rect = root.getBoundingClientRect();
    const total = rect.width;
    const usable = Math.max(total - GAP * (count - 1), 120);
    const size = Math.max(140, usable * Math.min(Math.max(EXPAND_RATIO, 0.2), 0.9) * 1.22);
    mediaSize = size;
    root.style.setProperty("--ag-media-size", size + "px");
    applyLayout(false);
  }

  measure();
  const ro = new ResizeObserver(measure);
  ro.observe(root);

  // Cleanup on page unload
  window.addEventListener("beforeunload", function () {
    ro.disconnect();
    tlRef?.kill();
  });
}

/* --------------------------------------------------------------------------
   CIRCULAR GALLERY (vanilla port of ReactBits CircularGallery — OGL via esm.sh)
   -------------------------------------------------------------------------- */

function initCircularGallery() {
  const container = document.getElementById("circularGallery");
  const items = window.__GALLERY_ITEMS;
  if (!container || !Array.isArray(items) || !items.length) return;

  function showFallback() {
    container.classList.add("gallery-fallback");
    container.innerHTML = "";
    items.forEach((it) => {
      const img = document.createElement("img");
      img.src = it.image;
      img.alt = it.text || "";
      img.loading = "lazy";
      img.decoding = "async";
      container.appendChild(img);
    });
  }

  try {
    const testCanvas = document.createElement("canvas");
    if (!testCanvas.getContext("webgl") && !testCanvas.getContext("experimental-webgl")) {
      showFallback();
      return;
    }
  } catch (_) {
    showFallback();
    return;
  }

  const FONT = 'bold 30px "Plus Jakarta Sans"';
  const TEXT_COLOR = "#0a0a0a";
  const BEND = -3;
  const BORDER_RADIUS = 0.05;
  const SCROLL_SPEED = 2;
  const SCROLL_EASE = 0.05;

  const fontReady =
    document.fonts && document.fonts.load
      ? Promise.all([
          document.fonts.load(FONT).catch(() => {}),
          document.fonts.ready.catch(() => {}),
        ])
      : Promise.resolve();

  Promise.all([fontReady, import("https://esm.sh/ogl@1.0.11")])
    .then(([, OGL]) => {
      createCircularGallery(container, items, OGL, {
        font: FONT,
        textColor: TEXT_COLOR,
        bend: BEND,
        borderRadius: BORDER_RADIUS,
        scrollSpeed: SCROLL_SPEED,
        scrollEase: SCROLL_EASE,
        onFatal: showFallback,
      });
    })
    .catch(() => showFallback());
}

function createCircularGallery(container, items, OGL, opts) {
  const { Camera, Mesh, Plane, Program, Renderer, Texture, Transform } = OGL;
  const lerp = (p1, p2, t) => p1 + (p2 - p1) * t;

  let renderer;
  try {
    renderer = new Renderer({
      alpha: true,
      antialias: true,
      dpr: Math.min(window.devicePixelRatio || 1, 2),
    });
  } catch (_) {
    opts.onFatal();
    return;
  }
  const gl = renderer.gl;
  gl.clearColor(0, 0, 0, 0);
  container.appendChild(gl.canvas);

  const camera = new Camera(gl);
  camera.fov = 45;
  camera.position.z = 20;

  const scene = new Transform();
  const planeGeometry = new Plane(gl, { heightSegments: 50, widthSegments: 100 });

  let screen = { width: container.clientWidth, height: container.clientHeight };
  let viewport = { width: 1, height: 1 };

  function computeViewport() {
    screen = { width: container.clientWidth, height: container.clientHeight };
    renderer.setSize(screen.width, screen.height);
    camera.perspective({ aspect: screen.width / screen.height });
    const fov = (camera.fov * Math.PI) / 180;
    const h = 2 * Math.tan(fov / 2) * camera.position.z;
    viewport = { width: h * camera.aspect, height: h };
  }

  computeViewport();

  function createTextTexture(text) {
    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d");
    context.font = opts.font;
    const metrics = context.measureText(text);
    const textWidth = Math.ceil(metrics.width);
    const fontSize = 30;
    const textHeight = Math.ceil(fontSize * 1.2);
    canvas.width = textWidth + 20;
    canvas.height = textHeight + 20;
    context.font = opts.font;
    context.fillStyle = opts.textColor;
    context.textBaseline = "middle";
    context.textAlign = "center";
    context.clearRect(0, 0, canvas.width, canvas.height);
    context.fillText(text, canvas.width / 2, canvas.height / 2);
    const texture = new Texture(gl, { generateMipmaps: false });
    texture.image = canvas;
    return { texture, width: canvas.width, height: canvas.height };
  }

  function addTitle(plane, text) {
    const { texture, width, height } = createTextTexture(text);
    const geometry = new Plane(gl);
    const program = new Program(gl, {
      vertex: `
        attribute vec3 position;
        attribute vec2 uv;
        uniform mat4 modelViewMatrix;
        uniform mat4 projectionMatrix;
        varying vec2 vUv;
        void main() {
          vUv = uv;
          gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
        }
      `,
      fragment: `
        precision highp float;
        uniform sampler2D tMap;
        varying vec2 vUv;
        void main() {
          vec4 color = texture2D(tMap, vUv);
          if (color.a < 0.1) discard;
          gl_FragColor = color;
        }
      `,
      uniforms: { tMap: { value: texture } },
      transparent: true,
    });
    const mesh = new Mesh(gl, { geometry, program });
    const aspect = width / height;
    const textHeightScaled = plane.scale.y * 0.15;
    const textWidthScaled = textHeightScaled * aspect;
    mesh.scale.set(textWidthScaled, textHeightScaled, 1);
    mesh.position.y = -plane.scale.y * 0.5 - textHeightScaled * 0.5 - 0.05;
    mesh.setParent(plane);
  }

  function createMedia(index, length, item) {
    const texture = new Texture(gl, { generateMipmaps: true });
    const program = new Program(gl, {
      depthTest: false,
      depthWrite: false,
      vertex: `
        precision highp float;
        attribute vec3 position;
        attribute vec2 uv;
        uniform mat4 modelViewMatrix;
        uniform mat4 projectionMatrix;
        uniform float uTime;
        uniform float uSpeed;
        varying vec2 vUv;
        void main() {
          vUv = uv;
          vec3 p = position;
          p.z = (sin(p.x * 4.0 + uTime) * 1.5 + cos(p.y * 2.0 + uTime) * 1.5) * (0.1 + uSpeed * 0.5);
          gl_Position = projectionMatrix * modelViewMatrix * vec4(p, 1.0);
        }
      `,
      fragment: `
        precision highp float;
        uniform vec2 uImageSizes;
        uniform vec2 uPlaneSizes;
        uniform sampler2D tMap;
        uniform float uBorderRadius;
        varying vec2 vUv;

        float roundedBoxSDF(vec2 p, vec2 b, float r) {
          vec2 d = abs(p) - b;
          return length(max(d, vec2(0.0))) + min(max(d.x, d.y), 0.0) - r;
        }

        void main() {
          vec2 ratio = vec2(
            min((uPlaneSizes.x / uPlaneSizes.y) / (uImageSizes.x / uImageSizes.y), 1.0),
            min((uPlaneSizes.y / uPlaneSizes.x) / (uImageSizes.y / uImageSizes.x), 1.0)
          );
          vec2 uv = vec2(
            vUv.x * ratio.x + (1.0 - ratio.x) * 0.5,
            vUv.y * ratio.y + (1.0 - ratio.y) * 0.5
          );
          vec4 color = texture2D(tMap, uv);

          float d = roundedBoxSDF(vUv - 0.5, vec2(0.5 - uBorderRadius), uBorderRadius);

          float edgeSmooth = 0.002;
          float alpha = 1.0 - smoothstep(-edgeSmooth, edgeSmooth, d);

          gl_FragColor = vec4(color.rgb, alpha);
        }
      `,
      uniforms: {
        tMap: { value: texture },
        uPlaneSizes: { value: [0, 0] },
        uImageSizes: { value: [0, 0] },
        uSpeed: { value: 0 },
        uTime: { value: 100 * Math.random() },
        uBorderRadius: { value: opts.borderRadius },
      },
      transparent: true,
    });

    const img = new Image();
    img.crossOrigin = "anonymous";
    img.src = item.image;
    img.onload = () => {
      texture.image = img;
      program.uniforms.uImageSizes.value = [img.naturalWidth, img.naturalHeight];
    };

    const plane = new Mesh(gl, { geometry: planeGeometry, program });
    plane.setParent(scene);

    const media = {
      extra: 0,
      speed: 0,
      isBefore: false,
      isAfter: false,
      index,
      length,
      plane,
      program,
      padding: 2,
      width: 0,
      widthTotal: 0,
      x: 0,
      viewport: viewport,
      screen: screen,
    };

    media.addTitle = () => addTitle(media.plane, item.text);

    media.update = (scroll, direction) => {
      media.plane.position.x = media.x - scroll.current - media.extra;

      const x = media.plane.position.x;
      const H = media.viewport.width / 2;

      if (opts.bend === 0) {
        media.plane.position.y = 0;
        media.plane.rotation.z = 0;
      } else {
        const B_abs = Math.abs(opts.bend);
        const R = (H * H + B_abs * B_abs) / (2 * B_abs);
        const effectiveX = Math.min(Math.abs(x), H);

        const arc = R - Math.sqrt(R * R - effectiveX * effectiveX);
        if (opts.bend > 0) {
          media.plane.position.y = -arc;
          media.plane.rotation.z = -Math.sign(x) * Math.asin(effectiveX / R);
        } else {
          media.plane.position.y = arc;
          media.plane.rotation.z = Math.sign(x) * Math.asin(effectiveX / R);
        }
      }

      media.speed = scroll.current - scroll.last;
      media.program.uniforms.uTime.value += 0.04;
      media.program.uniforms.uSpeed.value = media.speed;

      const planeOffset = media.plane.scale.x / 2;
      const viewportOffset = media.viewport.width / 2;
      media.isBefore = media.plane.position.x + planeOffset < -viewportOffset;
      media.isAfter = media.plane.position.x - planeOffset > viewportOffset;
      if (direction === "right" && media.isBefore) {
        media.extra -= media.widthTotal;
        media.isBefore = media.isAfter = false;
      }
      if (direction === "left" && media.isAfter) {
        media.extra += media.widthTotal;
        media.isBefore = media.isAfter = false;
      }
    };

    media.onResize = (o = {}) => {
      if (o.screen) media.screen = o.screen;
      if (o.viewport) {
        media.viewport = o.viewport;
        if (media.plane.program.uniforms.uViewportSizes) {
          media.plane.program.uniforms.uViewportSizes.value = [
            media.viewport.width,
            media.viewport.height,
          ];
        }
      }
      const sc = media.screen.height / 1500;
      media.plane.scale.y = (media.viewport.height * (900 * sc)) / media.screen.height;
      media.plane.scale.x = (media.viewport.width * (700 * sc)) / media.screen.width;
      media.plane.program.uniforms.uPlaneSizes.value = [
        media.plane.scale.x,
        media.plane.scale.y,
      ];
      media.padding = 2;
      media.width = media.plane.scale.x + media.padding;
      media.widthTotal = media.width * media.length;
      media.x = media.width * media.index;
    };

    media.onResize();
    return media;
  }

  const galleryItems = items.concat(items);
  const medias = galleryItems.map((item, i) => {
    const m = createMedia(i, galleryItems.length, item);
    m.addTitle();
    return m;
  });

  function onResize() {
    computeViewport();
    medias.forEach((m) => m.onResize({ screen, viewport }));
  }
  window.addEventListener("resize", onResize);

  const scroll = { ease: opts.scrollEase, current: 0, target: 0, last: 0 };
  let isDown = false;
  let startX = 0;
  let downPos = 0;
  let checkTimer = null;

  function onCheck() {
    if (!medias.length || !medias[0].width) return;
    const w = medias[0].width;
    const itemIndex = Math.round(Math.abs(scroll.target) / w);
    const item = w * itemIndex;
    scroll.target = scroll.target < 0 ? -item : item;
  }

  function onCheckDebounced() {
    clearTimeout(checkTimer);
    checkTimer = setTimeout(onCheck, 200);
  }

  container.addEventListener("pointerdown", (e) => {
    isDown = true;
    downPos = scroll.current;
    startX = e.clientX;
    try {
      container.setPointerCapture(e.pointerId);
    } catch (_) {}
  });

  container.addEventListener("pointermove", (e) => {
    if (!isDown) return;
    const distance = (startX - e.clientX) * (opts.scrollSpeed * 0.025);
    scroll.target = downPos + distance;
  });

  const onPointerUp = () => {
    if (!isDown) return;
    isDown = false;
    onCheck();
  };
  container.addEventListener("pointerup", onPointerUp);
  container.addEventListener("pointercancel", onPointerUp);

  container.addEventListener("keydown", (e) => {
    if (e.key === "ArrowRight") {
      e.preventDefault();
      scroll.target += opts.scrollSpeed * 5;
      onCheckDebounced();
    } else if (e.key === "ArrowLeft") {
      e.preventDefault();
      scroll.target -= opts.scrollSpeed * 5;
      onCheckDebounced();
    }
  });

  let rafId = null;
  let visible = false;

  function update() {
    scroll.current = lerp(scroll.current, scroll.target, scroll.ease);
    const direction = scroll.current > scroll.last ? "right" : "left";
    medias.forEach((m) => m.update(scroll, direction));
    renderer.render({ scene, camera });
    scroll.last = scroll.current;
    rafId = requestAnimationFrame(update);
  }

  if ("IntersectionObserver" in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !visible) {
            visible = true;
            rafId = requestAnimationFrame(update);
          } else if (!entry.isIntersecting && visible) {
            visible = false;
            cancelAnimationFrame(rafId);
          }
        });
      },
      { threshold: 0 }
    );
    io.observe(container);
  } else {
    rafId = requestAnimationFrame(update);
  }
}

/* --------------------------------------------------------------------------
   EDITOR MODE (admin CRUD for ChromaGrid showcase)
   -------------------------------------------------------------------------- */

function initEditorMode() {
  const body = document.body;
  const grid = document.getElementById("chromaGrid");
  const gridWrap = document.getElementById("chromaGridWrap");
  if (!grid || !gridWrap) return;

  let isAdmin = !!window.__IS_ADMIN;
  let editorOn = false;
  let editingId = null;
  let selectedFile = null;
  let objectUrl = null;

  const loginOverlay = document.getElementById("editorLoginOverlay");
  const loginForm = document.getElementById("editorLoginForm");
  const passwordInput = document.getElementById("editorPassword");
  const loginError = document.getElementById("editorLoginError");

  const deleteOverlay = document.getElementById("deleteOverlay");
  const deleteCardName = document.getElementById("deleteCardName");
  let pendingDeleteCard = null;

  const formOverlay = document.getElementById("cardFormOverlay");
  const cardForm = document.getElementById("cardForm");
  const cardFormTitle = document.getElementById("cardFormTitle");
  const titleInput = document.getElementById("cardTitle");
  const descInput = document.getElementById("cardDescription");
  const linkInput = document.getElementById("cardLink");
  const titleError = document.getElementById("cardTitleError");
  const descError = document.getElementById("cardDescError");
  const imageError = document.getElementById("cardImageError");
  const linkError = document.getElementById("cardLinkError");
  const submitBtn = document.getElementById("cardFormSubmit");

  const dropzone = document.getElementById("dropzone");
  const fileInput = document.getElementById("cardImage");
  const dzEmpty = document.getElementById("dropzoneEmpty");
  const dzPreview = document.getElementById("dropzonePreview");
  const dzPreviewImg = document.getElementById("dropzonePreviewImg");
  const dzName = document.getElementById("dropzoneName");

  const lightbox = document.getElementById("lightbox");

  const isId = () => window.LANG === "id";
  const isDesktop = () => window.innerWidth > 1024;
  const ADD_LABEL = cardFormTitle ? cardFormTitle.textContent : "Add Project";

  function openOverlay(el) {
    el.hidden = false;
    body.style.overflow = "hidden";
    if (window.__lenis) window.__lenis.stop();
  }

  function closeOverlay(el) {
    el.hidden = true;
    body.style.overflow = "";
    if (window.__lenis) window.__lenis.start();
  }

  function closeAllMenus() {
    grid.querySelectorAll(".chroma-menu.open").forEach((m) => m.classList.remove("open"));
  }

  function enterEditor() {
    if (!isDesktop()) return;
    editorOn = true;
    body.classList.add("editor-mode");
    applyChromaVisibility();
    if (window.lucide) lucide.createIcons();
  }

  function exitEditor() {
    editorOn = false;
    body.classList.remove("editor-mode");
    chromaShown = CHROMA_PAGE;
    applyChromaVisibility();
    closeAllMenus();
  }

  // ---------- keyboard ----------
  document.addEventListener("keydown", (e) => {
    if (lightbox && !lightbox.hidden) {
      if (e.key === "ArrowLeft") {
        e.preventDefault();
        lbNav(-1);
      } else if (e.key === "ArrowRight") {
        e.preventDefault();
        lbNav(1);
      }
      return;
    }
    if (e.ctrlKey && e.shiftKey && (e.key === "E" || e.key === "e")) {
      e.preventDefault();
      if (!isDesktop()) return;
      if (!isAdmin) {
        openOverlay(loginOverlay);
        setTimeout(() => passwordInput.focus(), 50);
      } else {
        editorOn ? exitEditor() : enterEditor();
      }
    }
    if (e.key === "Escape") {
      closeAllMenus();
      if (deleteOverlay && !deleteOverlay.hidden) {
        pendingDeleteCard = null;
        closeOverlay(deleteOverlay);
      } else if (loginOverlay && !loginOverlay.hidden) {
        closeOverlay(loginOverlay);
        loginError.textContent = "";
        loginForm.reset();
      } else if (formOverlay && !formOverlay.hidden) {
        closeCardForm();
      } else if (lightbox && !lightbox.hidden) {
        closeLightbox();
      }
    }
  });

  function openCardForm() {
    openOverlay(formOverlay);
    body.classList.add("card-form-open");
  }

  function closeCardForm() {
    closeOverlay(formOverlay);
    body.classList.remove("card-form-open");
  }

  [loginOverlay, formOverlay, deleteOverlay].forEach((ov) => {
    if (!ov) return;
    ov.addEventListener("pointerdown", (e) => {
      if (e.target === ov) {
        if (ov === formOverlay) closeCardForm();
        else closeOverlay(ov);
      }
    });
  });

  document.querySelectorAll("[data-close-modal]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const ov = btn.closest(".editor-overlay");
      if (!ov) return;
      if (ov.id === "cardFormOverlay") closeCardForm();
      else closeOverlay(ov);
    });
  });

  // ---------- login ----------
  loginForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    loginError.textContent = "";
    try {
      const res = await fetch("index.php?/api/admin/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ password: passwordInput.value }),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data.success) {
        isAdmin = true;
        window.__IS_ADMIN = true;
        loginForm.reset();
        closeOverlay(loginOverlay);
        setTimeout(() => enterEditor(), 280);
      } else {
        loginError.textContent = data.error || "Login failed";
      }
    } catch (_) {
      loginError.textContent = "Network error";
    }
  });

  document.getElementById("editorLogoutBtn")?.addEventListener("click", async () => {
    await fetch("index.php?/api/admin/logout", { method: "POST" }).catch(() => {});
    isAdmin = false;
    window.__IS_ADMIN = false;
    exitEditor();
  });

  // ---------- delete confirmation modal ----------
  document.getElementById("deleteConfirmBtn")?.addEventListener("click", async () => {
    if (!pendingDeleteCard) return;
    const card = pendingDeleteCard;
    pendingDeleteCard = null;
    closeOverlay(deleteOverlay);
    await deleteCard(card);
  });

  document.querySelector("[data-close-delete]")?.addEventListener("click", () => {
    pendingDeleteCard = null;
    closeOverlay(deleteOverlay);
  });

  // Auto-exit editor when viewport drops below desktop
  window.addEventListener("resize", () => {
    if (editorOn && !isDesktop()) exitEditor();
  });

  // ---------- card interactions (delegation) ----------
  grid.addEventListener("click", (e) => {
    const menuBtn = e.target.closest(".chroma-menu-btn");
    if (menuBtn) {
      if (!editorOn) return;
      e.stopPropagation();
      const menu = menuBtn.parentElement.querySelector(".chroma-menu");
      const wasOpen = menu.classList.contains("open");
      closeAllMenus();
      if (!wasOpen) menu.classList.add("open");
      return;
    }

    const actionBtn = e.target.closest(".chroma-menu button");
    if (actionBtn) {
      e.stopPropagation();
      const card = actionBtn.closest(".chroma-card");
      closeAllMenus();
      if (actionBtn.dataset.action === "edit") {
        openForm(card);
      } else if (actionBtn.dataset.action === "delete") {
        pendingDeleteCard = card;
        deleteCardName.textContent = card.dataset.title || "";
        openOverlay(deleteOverlay);
      }
      return;
    }

    const card = e.target.closest(".chroma-card");
    if (card && !editorOn) {
      openLightbox(card);
    }
  });

  document.addEventListener("click", (e) => {
    if (!e.target.closest(".chroma-card")) closeAllMenus();
  });

  async function deleteCard(card) {
    try {
      const res = await fetch(`index.php?/api/admin/cards/${card.dataset.id}`, {
        method: "DELETE",
      });
      if (res.ok) {
        card.remove();
        if (!grid.querySelector(".chroma-card")) gridWrap.hidden = true;
      }
    } catch (_) {}
  }

  // ---------- lightbox (macOS-style viewer) ----------
  const lbCounter = document.getElementById("lbCounter");
  const lbLinkBtn = document.getElementById("lbLinkBtn");
  const lbVisitLink = document.getElementById("lbVisitLink");
  const lbFilmstrip = document.getElementById("lbFilmstrip");
  const lbPrev = document.getElementById("lbPrev");
  const lbNext = document.getElementById("lbNext");

  let lbItems = [];
  let lbIndex = 0;

  function collectLbItems() {
    return Array.from(grid.querySelectorAll(".chroma-card")).map((c) => ({
      id: c.dataset.id || "",
      image: c.dataset.image || "",
      title: c.dataset.title || "",
      description: c.dataset.description || "",
      link: c.dataset.link || "",
    }));
  }

  function renderLb() {
    if (!lbItems.length) return;
    const item = lbItems[lbIndex];
    const img = document.getElementById("lightboxImage");
    img.src = item.image;
    img.alt = item.title || "";
    document.getElementById("lightboxTitle").textContent = item.title || "";
    document.getElementById("lightboxDescription").textContent = item.description || "";
    lbCounter.textContent = `${lbIndex + 1} / ${lbItems.length}`;

    if (item.link) {
      lbLinkBtn.href = item.link;
      lbLinkBtn.hidden = false;
      lbVisitLink.href = item.link;
      lbVisitLink.hidden = false;
    } else {
      lbLinkBtn.hidden = true;
      lbVisitLink.hidden = true;
    }

    lbFilmstrip.querySelectorAll(".lb-thumb").forEach((thumb, i) => {
      thumb.classList.toggle("is-active", i === lbIndex);
      if (i === lbIndex) {
        thumb.scrollIntoView({ block: "nearest", inline: "center", behavior: "smooth" });
      }
    });
  }

  function buildLbStrip() {
    lbFilmstrip.innerHTML = "";
    lbItems.forEach((item, i) => {
      const b = document.createElement("button");
      b.type = "button";
      b.className = "lb-thumb";
      b.setAttribute("aria-label", item.title || `Slide ${i + 1}`);
      const img = document.createElement("img");
      img.src = item.image;
      img.alt = "";
      img.loading = "lazy";
      b.appendChild(img);
      b.addEventListener("click", () => {
        lbIndex = i;
        renderLb();
      });
      lbFilmstrip.appendChild(b);
    });
  }

  function lbNav(delta) {
    if (!lbItems.length) return;
    lbIndex = (lbIndex + delta + lbItems.length) % lbItems.length;
    renderLb();
  }

  function openLightbox(cardEl) {
    lbItems = collectLbItems();
    if (!lbItems.length) return;

    const id = cardEl.dataset.id || "";
    const image = cardEl.dataset.image || "";

    let idx = lbItems.findIndex((it) => it && it.id && it.id === id);
    if (idx < 0) {
      idx = lbItems.findIndex((it) => it && it.image === image);
    }

    lbIndex = idx >= 0 ? idx : 0;
    buildLbStrip();
    renderLb();
    openOverlay(lightbox);
  }

  function closeLightbox() {
    closeOverlay(lightbox);
  }

  lbPrev?.addEventListener("click", () => lbNav(-1));
  lbNext?.addEventListener("click", () => lbNav(1));
  lightbox?.addEventListener("pointerdown", (e) => {
    if (e.target === lightbox) closeLightbox();
  });
  document
    .querySelector("[data-close-lightbox]")
    ?.addEventListener("click", closeLightbox);

  // ---------- chroma spotlight (grid mask + per-card radial) ----------
  const fadeEl = grid.querySelector(".chroma-fade");
  const hasGsap = typeof gsap !== "undefined";
  const setGridX = hasGsap ? gsap.quickSetter(grid, "--x", "px") : null;
  const setGridY = hasGsap ? gsap.quickSetter(grid, "--y", "px") : null;
  const spot = { x: grid.clientWidth / 2, y: grid.clientHeight / 2 };

  if (setGridX && setGridY) {
    setGridX(spot.x);
    setGridY(spot.y);
  }

  function moveSpotlight(x, y) {
    if (!setGridX || !setGridY) return;
    gsap.to(spot, {
      x,
      y,
      duration: 0.45,
      ease: "power3.out",
      overwrite: true,
      onUpdate: () => {
        setGridX(spot.x);
        setGridY(spot.y);
      },
    });
    if (fadeEl) gsap.to(fadeEl, { opacity: 0, duration: 0.25, overwrite: true });
  }

  gridWrap.addEventListener("pointermove", (e) => {
    const r = grid.getBoundingClientRect();
    moveSpotlight(e.clientX - r.left, e.clientY - r.top);
  });

  gridWrap.addEventListener("pointerleave", () => {
    if (fadeEl) gsap.to(fadeEl, { opacity: 1, duration: 0.6, overwrite: true });
  });

  grid.addEventListener("mousemove", (e) => {
    const card = e.target.closest(".chroma-card");
    if (!card) return;
    const r = card.getBoundingClientRect();
    card.style.setProperty("--mouse-x", `${e.clientX - r.left}px`);
    card.style.setProperty("--mouse-y", `${e.clientY - r.top}px`);
  });

  // ---------- add project button ----------
  document.getElementById("addProjectBtn")?.addEventListener("click", () => openForm(null));

  // ---------- form modal ----------
  function resetErrors() {
    titleError.textContent = "";
    descError.textContent = "";
    imageError.textContent = "";
    linkError.textContent = "";
  }

  function clearFormState() {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }
    editingId = null;
    selectedFile = null;
    fileInput.value = "";
    dzPreviewImg.removeAttribute("src");
    dzPreview.hidden = true;
    dzEmpty.hidden = false;
    cardForm.reset();
  }

  function showEmptyDropzone() {
    selectedFile = null;
    fileInput.value = "";
    dzPreview.hidden = true;
    dzEmpty.hidden = false;
  }

  function showPreviewFromUrl(url) {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }
    selectedFile = null;
    fileInput.value = "";
    dzPreviewImg.src = url;
    dzName.textContent = url.split("/").pop() || "image";
    dzEmpty.hidden = true;
    dzPreview.hidden = false;
  }

  function setFile(file) {
    const okTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    if (!okTypes.includes(file.type)) {
      imageError.textContent = isId()
        ? "Hanya JPG, PNG, WebP, atau GIF yang diizinkan"
        : "Only JPG, PNG, WebP, or GIF allowed";
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      imageError.textContent = isId()
        ? "Ukuran gambar melebihi 5 MB"
        : "Image exceeds 5 MB limit";
      return;
    }
    imageError.textContent = "";
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);
    selectedFile = file;
    dzPreviewImg.src = objectUrl;
    dzName.textContent = file.name;
    dzEmpty.hidden = true;
    dzPreview.hidden = false;
  }

  function openForm(card) {
    clearFormState();
    resetErrors();

    if (card) {
      editingId = Number(card.dataset.id);
      cardFormTitle.textContent = isId() ? "Edit Proyek" : "Edit Project";
      titleInput.value = card.dataset.title || "";
      descInput.value = card.dataset.description || "";
      linkInput.value = card.dataset.link || "";
      showPreviewFromUrl(card.dataset.image || "");
    } else {
      cardFormTitle.textContent = ADD_LABEL;
      linkInput.value = "";
    }
    openCardForm();
    setTimeout(() => titleInput.focus(), 50);
  }

  dropzone.addEventListener("click", () => fileInput.click());
  fileInput.addEventListener("change", () => {
    if (fileInput.files[0]) setFile(fileInput.files[0]);
  });
  ["dragover", "dragenter"].forEach((ev) =>
    dropzone.addEventListener(ev, (e) => {
      e.preventDefault();
      dropzone.classList.add("dragover");
    })
  );
  ["dragleave", "drop"].forEach((ev) =>
    dropzone.addEventListener(ev, (e) => {
      e.preventDefault();
      dropzone.classList.remove("dragover");
    })
  );
  dropzone.addEventListener("drop", (e) => {
    if (e.dataTransfer.files[0]) setFile(e.dataTransfer.files[0]);
  });
  dzPreview.addEventListener("click", (e) => {
    e.stopPropagation();
    fileInput.click();
  });

  cardForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    resetErrors();

    let valid = true;
    if (titleInput.value.trim().length < 2) {
      titleError.textContent = isId()
        ? "Judul wajib diisi (min 2 karakter)"
        : "Title is required (min 2 characters)";
      valid = false;
    }
    if (descInput.value.trim().length < 10) {
      descError.textContent = isId()
        ? "Deskripsi wajib diisi (min 10 karakter)"
        : "Description is required (min 10 characters)";
      valid = false;
    }
    if (!editingId && !selectedFile) {
      imageError.textContent = isId() ? "Silakan pilih gambar" : "Please choose an image";
      valid = false;
    }
    const linkVal = linkInput.value.trim();
    if (linkVal && !/^https?:\/\//i.test(linkVal)) {
      linkError.textContent = isId()
        ? "Masukkan URL yang valid (http/https)"
        : "Enter a valid URL (http/https)";
      valid = false;
    }
    if (!valid) return;

    const fd = new FormData();
    fd.append("title", titleInput.value.trim());
    fd.append("description", descInput.value.trim());
    fd.append("link", linkVal);
    if (selectedFile) fd.append("image", selectedFile);

    const url = editingId
      ? `index.php?/api/admin/cards/${editingId}`
      : "index.php?/api/admin/cards";

    submitBtn.disabled = true;
    try {
      const res = await fetch(url, { method: "POST", body: fd });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data.success && data.card) {
        upsertCardNode(data.card);
        clearFormState();
        closeCardForm();
      } else if (data.errors) {
        titleError.textContent = data.errors.title || "";
        descError.textContent = data.errors.description || "";
        linkError.textContent = data.errors.link || "";
      } else {
        imageError.textContent = data.error || "Error";
      }
    } catch (_) {
      imageError.textContent = "Network error";
    } finally {
      submitBtn.disabled = false;
    }
  });

  // ---------- DOM node builder ----------
  // ---------- load more pagination (+6, public view only) ----------
  const CHROMA_PAGE = 6;
  let chromaShown = CHROMA_PAGE;
  const loadWrap = document.getElementById("chromaLoadMoreWrap");
  const loadBtn = document.getElementById("chromaLoadMoreBtn");

  function applyChromaVisibility() {
    const cards = Array.from(grid.querySelectorAll(".chroma-card"));
    let hiddenCount = 0;
    cards.forEach((card, i) => {
      const hide = !editorOn && i >= chromaShown;
      card.classList.toggle("chroma-extra", hide);
      if (hide) hiddenCount++;
    });
    if (loadWrap) loadWrap.hidden = editorOn || hiddenCount === 0;
  }

  loadBtn?.addEventListener("click", () => {
    chromaShown += CHROMA_PAGE;
    applyChromaVisibility();
  });

  function buildCardNode(card) {
    const article = document.createElement("article");
    article.className = "chroma-card";
    article.dataset.id = card.id;
    article.dataset.title = card.title;
    article.dataset.description = card.description;
    article.dataset.image = card.image;
    article.dataset.link = card.link || "";

    article.innerHTML = `
      <button type="button" class="chroma-menu-btn" aria-label="Card menu">
        <i data-lucide="more-vertical"></i>
      </button>
      <div class="chroma-menu">
        <button type="button" data-action="edit">Edit</button>
        <button type="button" data-action="delete" class="danger">${isId() ? "Hapus" : "Delete"}</button>
      </div>
      <div class="chroma-img-wrapper">
        <img src="${card.image}" alt="" loading="lazy" />
      </div>
      <footer class="chroma-info">
        <h3 class="name"></h3>
        <p class="role"></p>
      </footer>`;

    article.querySelector(".name").textContent = card.title;
    article.querySelector(".role").textContent = card.description;
    article.querySelector(".chroma-img-wrapper img").alt = card.title;
    return article;
  }

  function upsertCardNode(card) {
    gridWrap.hidden = false;
    const existing = grid.querySelector(`.chroma-card[data-id="${card.id}"]`);
    const node = buildCardNode(card);
    if (existing) {
      existing.replaceWith(node);
    } else {
      const anchor = grid.querySelector(".chroma-overlay");
      grid.insertBefore(node, anchor);
    }
    if (window.lucide) lucide.createIcons();
    applyChromaVisibility();
  }

  applyChromaVisibility();
}

/* --------------------------------------------------------------------------
   GALLERY (from PHP rendered HTML)
   -------------------------------------------------------------------------- */

function renderGallery() {
  // Gallery is rendered server-side in gallery.php
}

/* --------------------------------------------------------------------------
   TESTIMONIALS (from PHP rendered HTML)
   -------------------------------------------------------------------------- */

function renderTestimonials() {
  // Testimonials are rendered server-side in testimonials.php
}

/* --------------------------------------------------------------------------
   FAQ (from PHP data)
   -------------------------------------------------------------------------- */

function renderFaqPanel(category = "all") {
  const panel = document.getElementById("faqPanel");
  if (!panel) return;

  const faqs = window.FAQS_DATA;
  const filtered = faqs.filter((faq) => category === "all" || faq.category === category);

  panel.innerHTML = filtered
    .map(
      (faq) => `
    <div class="faq-item" data-category="${faq.category}">
      <button class="faq-question" aria-expanded="false">
        <span>${faq.question}</span>
        <i data-lucide="plus" class="faq-icon"></i>
      </button>
      <div class="faq-answer">
        <div class="faq-answer-inner">${faq.answer}</div>
      </div>
    </div>`
    )
    .join("");

  if (window.lucide) lucide.createIcons();

  panel.querySelectorAll(".faq-question").forEach((btn) => {
    btn.addEventListener("click", () => {
      const item = btn.closest(".faq-item");
      const isOpen = item.classList.contains("open");
      item.classList.toggle("open");
      btn.setAttribute("aria-expanded", !isOpen);
    });
  });
}

function initFaqAccordion() {
  const sidebar = document.getElementById("faqSidebar");
  if (!sidebar) return;

  sidebar.addEventListener("click", (e) => {
    const btn = e.target.closest(".faq-category");
    if (!btn) return;

    sidebar.querySelectorAll(".faq-category").forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    renderFaqPanel(btn.dataset.category);
  });
}

/* --------------------------------------------------------------------------
   CONTACT MAP (Leaflet)
   -------------------------------------------------------------------------- */

function initContactMap() {
  const mapEl = document.getElementById("contact-map");
  if (!mapEl || typeof L === "undefined") return;

  const map = L.map("contact-map", {
    zoomControl: false,
    scrollWheelZoom: false,
  }).setView([5.5483, 95.3238], 14);

  L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", {
    attribution: '&copy; <a href="https://carto.com/">CARTO</a>, &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  L.control.zoom({ position: "bottomright" }).addTo(map);

  L.circleMarker([5.5483, 95.3238], {
    radius: 8,
    color: "#fff",
    fillColor: "#000",
    fillOpacity: 1,
  }).addTo(map);
}

/* --------------------------------------------------------------------------
   CONTACT FORM (EmailJS + PHP fallback)
   -------------------------------------------------------------------------- */

function initContactForm() {
  const form = document.getElementById("contactForm");
  const submitBtn = document.getElementById("submitBtn");
  const statusEl = document.getElementById("formStatus");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const lang = window.CONTACT_LANG || {};
    let valid = true;

    const name = form.querySelector("#contactName");
    const email = form.querySelector("#contactEmail");
    const phone = form.querySelector("#contactPhone");
    const service = form.querySelector("#contactService");
    const message = form.querySelector("#contactMessage");

    clearError("nameError");
    clearError("emailError");
    clearError("phoneError");
    clearError("serviceError");
    clearError("messageError");

    if (!name.value || name.value.trim().length < 2) {
      showError("nameError", lang.error_name || 'Name is required (min 2 characters)');
      valid = false;
    }

    if (!email.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      showError("emailError", lang.error_email || 'Valid email is required');
      valid = false;
    }

    if (!phone.value || phone.value.trim().length < 5) {
      showError("phoneError", lang.error_phone || 'Phone number is required');
      valid = false;
    }

    if (!service.value) {
      showError("serviceError", lang.error_service || 'Please select a service');
      valid = false;
    }

    if (!message.value || message.value.trim().length < 10) {
      showError("messageError", lang.error_message || 'Message is required (min 10 characters)');
      valid = false;
    }

    if (!valid) return;

    // Submit
    submitBtn.disabled = true;
    submitBtn.innerHTML = `${lang.sending || 'Sending...'} <i data-lucide="loader"></i>`;
    if (window.lucide) lucide.createIcons();

    statusEl.hidden = true;

    try {
      const config = window.EMAILJS_CONFIG || {};

      // Try EmailJS first
      if (!window.emailjs) {
        await loadScript("https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js");
      }

      await emailjs.sendForm(
        config.service_id || 'service_tdiat3m',
        config.template_id || 'template_8xhjpd2',
        form,
        config.public_key || '2MWuXtQlMs5Z7lht_'
      );

      statusEl.className = "form-status success";
      statusEl.textContent = lang.success || "Message Sent! I'll get back to you within 24 hours.";
      statusEl.hidden = false;
      form.reset();

      setTimeout(() => { statusEl.hidden = true; }, 5000);
    } catch (err) {
      // Fallback to PHP endpoint
      try {
        const formData = new FormData(form);
        const response = await fetch('index.php?/api/contact', {
          method: 'POST',
          body: formData,
        });

        if (response.ok) {
          statusEl.className = "form-status success";
          statusEl.textContent = lang.success || "Message Sent! I'll get back to you within 24 hours.";
          statusEl.hidden = false;
          form.reset();
          setTimeout(() => { statusEl.hidden = true; }, 5000);
        } else {
          throw new Error('PHP fallback failed');
        }
      } catch (phpErr) {
        statusEl.className = "form-status error";
        statusEl.textContent = (lang.error || 'Failed to send. Try via WhatsApp:') + ' ' + (window.__WHATSAPP || 'https://wa.me/6285213896460');
        statusEl.hidden = false;
      }
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = `${lang.submit || 'Send Message'} <i data-lucide="external-link"></i>`;
      if (window.lucide) lucide.createIcons();
    }
  });
}

function showError(id, msg) {
  const el = document.getElementById(id);
  if (el) el.textContent = msg;
}

function clearError(id) {
  const el = document.getElementById(id);
  if (el) el.textContent = "";
}

function loadScript(src) {
  return new Promise((resolve, reject) => {
    const script = document.createElement("script");
    script.src = src;
    script.onload = resolve;
    script.onerror = reject;
    document.head.appendChild(script);
  });
}

/* --------------------------------------------------------------------------
   LIVE CLOCK
   -------------------------------------------------------------------------- */

function initLiveClock() {
  const clockEl = document.getElementById("liveClock");
  if (!clockEl) return;

  const prefix = (window.LANG_DATA && window.LANG_DATA.cta_clock_prefix) || 'Your time —';

  function updateClock() {
    const now = new Date();
    const h = now.getHours();
    const m = String(now.getMinutes()).padStart(2, "0");
    const s = String(now.getSeconds()).padStart(2, "0");
    const ampm = h >= 12 ? "PM" : "AM";
    const h12 = h % 12 || 12;
    clockEl.textContent = `${prefix} ${h12}:${m}:${s} ${ampm}`;
  }

  updateClock();
  setInterval(updateClock, 1000);
}

/* --------------------------------------------------------------------------
   VISITOR COUNTER (real tracking via PHP)
   -------------------------------------------------------------------------- */

function initVisitorCounter() {
  fetch('index.php?/api/visitor')
    .then(res => res.json())
    .then(data => {
      const countEl = document.getElementById('visitorCount');
      const uniqueEl = document.getElementById('visitorUnique');
      if (countEl) countEl.textContent = data.count || 17;

      const lang = window.LANG || 'en';
      const visitorsText = lang === 'id' ? 'pengunjung unik' : 'unique visitors';
      if (uniqueEl) uniqueEl.textContent = `${data.count || 17} ${visitorsText}`;
    })
    .catch(() => {});
}

/* --------------------------------------------------------------------------
   SCROLL REVEAL
   -------------------------------------------------------------------------- */

function initScrollReveal() {
  const sections = document.querySelectorAll(
    "section:not(.intro):not(.tech-marquee):not(.bio-stats)"
  );
  sections.forEach((section) => section.classList.add("reveal"));

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in-view");
        }
      });
    },
    { threshold: 0.1 }
  );

  sections.forEach((section) => observer.observe(section));
}

/* --------------------------------------------------------------------------
   FOLD TEXT ENTRANCE (vanilla port of ReactBits FoldText — GSAP)
   -------------------------------------------------------------------------- */

function initFoldText() {
  const root = document.querySelector("[data-fold-text]");
  if (!root || typeof gsap === "undefined") return;

  const text = root.textContent.trim().replace(/\s+/g, " ");
  if (!text) return;

  const reduceMotion =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  root.setAttribute("aria-label", text);

  const visual = document.createElement("span");
  visual.className = "fold-visual";
  visual.setAttribute("aria-hidden", "true");

  const words = text.split(" ");
  const fragment = document.createDocumentFragment();

  words.forEach((word, index) => {
    const segment = document.createElement("span");
    segment.className = "fold-segment";
    const piece = document.createElement("span");
    piece.className = "fold-piece";
    piece.textContent = word;
    segment.appendChild(piece);
    fragment.appendChild(segment);
    if (index < words.length - 1) {
      fragment.appendChild(document.createTextNode(" "));
    }
  });

  root.textContent = "";
  root.appendChild(visual);
  visual.appendChild(fragment);

  const pieces = visual.querySelectorAll(".fold-piece");

  const hingeOrigin = "50% 0%";
  const duration = reduceMotion ? Math.min(0.65, 0.22) : 0.65;
  const stagger = reduceMotion ? Math.min(0.045, 0.02) : 0.045;
  const rotateX = reduceMotion ? 0 : -92;
  const crease = reduceMotion ? 0 : 0.55;

  const fromVars = {
    opacity: 0,
    rotateX: rotateX,
    "--fold-crease": crease,
    transformOrigin: hingeOrigin,
    force3D: true,
  };

  const play = () => {
    gsap.fromTo(
      pieces,
      { ...fromVars },
      {
        opacity: 1,
        rotateX: 0,
        "--fold-crease": 0,
        duration: duration,
        ease: reduceMotion ? "power1.out" : "power3.out",
        stagger: stagger,
        clearProps: "willChange",
      }
    );
  };

  gsap.set(pieces, { ...fromVars, opacity: 0 });

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          play();
          observer.disconnect();
        }
      });
    },
    { rootMargin: "0px 0px -18% 0px", threshold: 0 }
  );

  observer.observe(root);
}

/* --------------------------------------------------------------------------
   STAT COUNTERS (count-up on scroll — numbers only)
   -------------------------------------------------------------------------- */

function initStatCounters() {
  const values = document.querySelectorAll("#bio-stats .stat-item-value");
  if (!values.length || typeof gsap === "undefined") return;

  const reduceMotion =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const animateValue = (el) => {
    const raw = el.textContent.trim();
    const match = raw.match(/^(\d+)(.*)$/);
    if (!match) return;

    const target = parseInt(match[1], 10);
    const suffix = match[2] || "";
    if (reduceMotion || target <= 0) return;

    const counter = { value: 0 };
    gsap.to(counter, {
      value: target,
      duration: 1.2,
      ease: "power2.out",
      onUpdate: () => {
        el.textContent = Math.round(counter.value) + suffix;
      },
      onComplete: () => {
        el.textContent = target + suffix;
      },
    });
  };

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateValue(entry.target);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.4 }
  );

  values.forEach((el) => observer.observe(el));
}

/* --------------------------------------------------------------------------
   LUCIDE ICONS
   -------------------------------------------------------------------------- */

function initLucideIcons() {
  if (window.lucide) {
    lucide.createIcons();
  }
}

/* --------------------------------------------------------------------------
   CERTIFICATES — Grid + Hover Expand + Load More + Editor CRUD
   -------------------------------------------------------------------------- */

function initCertificates() {
  var items = window.__CERTIFICATES || [];
  var grid = document.getElementById("certGrid");
  var loadWrap = document.getElementById("certLoadMoreWrap");
  var loadBtn = document.getElementById("certLoadMoreBtn");
  if (!grid) return;

  var CERT_PAGE = 8;
  var certShown = CERT_PAGE;

  // ── Render grid cards ──
  function renderCards() {
    grid.innerHTML = "";
    items.forEach(function (item, i) {
      var card = document.createElement("div");
      card.className = "cert-card";
      card.dataset.id = item.id || "";
      card.dataset.title = item.title || "";
      card.dataset.company = item.company || "";
      card.dataset.credentialId = item.credential_id || "";
      card.dataset.credentialLink = item.credential_link || "";
      card.dataset.image = item.image || "";
      card.dataset.index = i;

      var img = document.createElement("img");
      img.src = item.image || "";
      img.alt = item.title || "";
      img.loading = "lazy";
      img.draggable = false;

      var info = document.createElement("div");
      info.className = "cert-card-info";

      var titleEl = document.createElement("h4");
      titleEl.className = "cert-card-title";
      titleEl.textContent = item.title || "";

      info.appendChild(titleEl);

      if (item.company) {
        var companyEl = document.createElement("p");
        companyEl.className = "cert-card-company";
        companyEl.textContent = item.company;
        info.appendChild(companyEl);
      }

      if (item.credential_id) {
        var credEl = document.createElement("p");
        credEl.className = "cert-card-credential";
        if (item.credential_link) {
          var link = document.createElement("a");
          link.href = item.credential_link;
          link.target = "_blank";
          link.rel = "noopener";
          link.textContent = item.credential_id;
          credEl.appendChild(link);
        } else {
          credEl.textContent = item.credential_id;
        }
        info.appendChild(credEl);
      }

      // Kebab menu (editor mode only)
      var menuBtn = document.createElement("button");
      menuBtn.type = "button";
      menuBtn.className = "cert-menu-btn";
      menuBtn.setAttribute("aria-label", "Card menu");
      menuBtn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18"><circle cx="12" cy="5" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="19" r="1.5" fill="currentColor"/></svg>';

      var menu = document.createElement("div");
      menu.className = "cert-menu";
      var editBtn = document.createElement("button");
      editBtn.type = "button";
      editBtn.dataset.action = "edit";
      editBtn.textContent = "Edit";
      var deleteBtn = document.createElement("button");
      deleteBtn.type = "button";
      deleteBtn.dataset.action = "delete";
      deleteBtn.className = "danger";
      deleteBtn.textContent = "Delete";
      menu.appendChild(editBtn);
      menu.appendChild(deleteBtn);

      card.appendChild(img);
      card.appendChild(info);
      card.appendChild(menuBtn);
      card.appendChild(menu);

      grid.appendChild(card);
    });
  }

  // ── Load More / Visibility ──
  function applyVisibility() {
    var cards = grid.querySelectorAll(".cert-card");
    var hiddenCount = 0;
    cards.forEach(function (card, i) {
      var hide = !document.body.classList.contains("editor-mode") && i >= certShown;
      card.classList.toggle("cert-extra", hide);
      if (hide) hiddenCount++;
    });
    if (loadWrap) loadWrap.hidden = document.body.classList.contains("editor-mode") || hiddenCount === 0;
  }

  if (loadBtn) {
    loadBtn.addEventListener("click", function () {
      certShown += CERT_PAGE;
      applyVisibility();
    });
  }

  // ── Card interactions (delegation) ──
  var pendingDeleteCert = null;

  grid.addEventListener("click", function (e) {
    // Kebab menu button
    var menuBtnEl = e.target.closest(".cert-menu-btn");
    if (menuBtnEl) {
      if (!document.body.classList.contains("editor-mode")) return;
      e.stopPropagation();
      var menuEl = menuBtnEl.parentElement.querySelector(".cert-menu");
      var wasOpen = menuEl.classList.contains("open");
      grid.querySelectorAll(".cert-menu.open").forEach(function (m) { m.classList.remove("open"); });
      if (!wasOpen) menuEl.classList.add("open");
      return;
    }

    // Menu action buttons
    var actionBtn = e.target.closest(".cert-menu button");
    if (actionBtn) {
      e.stopPropagation();
      grid.querySelectorAll(".cert-menu.open").forEach(function (m) { m.classList.remove("open"); });
      var card = actionBtn.closest(".cert-card");
      if (actionBtn.dataset.action === "edit") {
        certOpenForm(card);
      } else if (actionBtn.dataset.action === "delete") {
        pendingDeleteCert = card;
        deleteCardName.textContent = card.dataset.title || "";
        openOverlay(deleteOverlay);
      }
      return;
    }
  });

  document.addEventListener("click", function (e) {
    if (!e.target.closest(".cert-card")) {
      grid.querySelectorAll(".cert-menu.open").forEach(function (m) { m.classList.remove("open"); });
    }
  });

  // Delete confirmation handler
  var deleteConfirmBtn = document.getElementById("deleteConfirmBtn");
  if (deleteConfirmBtn) {
    deleteConfirmBtn.addEventListener("click", async function () {
      if (!pendingDeleteCert) return;
      var cert = pendingDeleteCert;
      pendingDeleteCert = null;
      closeOverlay(deleteOverlay);
      try {
        var res = await fetch("index.php?/api/admin/certificates/" + cert.dataset.id, { method: "DELETE" });
        if (res.ok) location.reload();
      } catch (_) {}
    });
  }

  // ── EDITOR MODE: Certificate CRUD ──
  var certFormOverlay = document.getElementById("certFormOverlay");
  var certForm = document.getElementById("certForm");
  var certFormTitle = document.getElementById("certFormTitle");
  var certTitleInput = document.getElementById("certTitle");
  var certCompanyInput = document.getElementById("certCompany");
  var certCredIdInput = document.getElementById("certCredentialId");
  var certCredLinkInput = document.getElementById("certCredentialLink");
  var certImageInput = document.getElementById("certImage");
  var certDropzone = document.getElementById("certDropzone");
  var certDropzoneEmpty = document.getElementById("certDropzoneEmpty");
  var certDropzonePreview = document.getElementById("certDropzonePreview");
  var certDropzonePreviewImg = document.getElementById("certDropzonePreviewImg");
  var certDropzoneName = document.getElementById("certDropzoneName");
  var certFormSubmit = document.getElementById("certFormSubmit");
  var certTitleError = document.getElementById("certTitleError");
  var certImageError = document.getElementById("certImageError");

  var certEditingId = null;
  var certSelectedFile = null;
  var certObjectUrl = null;

  function certOpenOverlay(el) {
    el.hidden = false;
    document.body.style.overflow = "hidden";
    document.body.classList.add("card-form-open");
    if (window.__lenis) window.__lenis.stop();
  }

  function certCloseOverlay(el) {
    el.hidden = true;
    document.body.style.overflow = "";
    document.body.classList.remove("card-form-open");
    if (window.__lenis) window.__lenis.start();
  }

  function certResetErrors() {
    certTitleError.textContent = "";
    certImageError.textContent = "";
  }

  function certClearForm() {
    if (certObjectUrl) { URL.revokeObjectURL(certObjectUrl); certObjectUrl = null; }
    certEditingId = null;
    certSelectedFile = null;
    certImageInput.value = "";
    certDropzonePreviewImg.removeAttribute("src");
    certDropzonePreview.hidden = true;
    certDropzoneEmpty.hidden = false;
    certForm.reset();
  }

  function certOpenForm(cert) {
    certClearForm();
    certResetErrors();
    if (cert) {
      certEditingId = Number(cert.dataset.id);
      certFormTitle.textContent = window.LANG === "id" ? "Edit Sertifikat" : "Edit Certificate";
      certTitleInput.value = cert.dataset.title || "";
      certCompanyInput.value = cert.dataset.company || "";
      certCredIdInput.value = cert.dataset.credentialId || "";
      certCredLinkInput.value = cert.dataset.credentialLink || "";
      if (cert.dataset.image) {
        certDropzonePreviewImg.src = cert.dataset.image;
        certDropzoneName.textContent = cert.dataset.image.split("/").pop();
        certDropzoneEmpty.hidden = true;
        certDropzonePreview.hidden = false;
      }
    } else {
      certFormTitle.textContent = window.LANG === "id" ? "Tambah Sertifikat" : "Add Certificate";
    }
    certOpenOverlay(certFormOverlay);
    setTimeout(function () { certTitleInput.focus(); }, 50);
  }

  function certSetFile(file) {
    var okTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    if (okTypes.indexOf(file.type) === -1) {
      certImageError.textContent = "Only JPG, PNG, WebP, or GIF allowed";
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      certImageError.textContent = "Image exceeds 5 MB limit";
      return;
    }
    certImageError.textContent = "";
    if (certObjectUrl) URL.revokeObjectURL(certObjectUrl);
    certObjectUrl = URL.createObjectURL(file);
    certSelectedFile = file;
    certDropzonePreviewImg.src = certObjectUrl;
    certDropzoneName.textContent = file.name;
    certDropzoneEmpty.hidden = true;
    certDropzonePreview.hidden = false;
  }

  // Add Certificate button
  var addCertBtn = document.getElementById("addCertBtn");
  if (addCertBtn) {
    addCertBtn.addEventListener("click", function () { certOpenForm(null); });
  }

  // Dropzone
  if (certDropzone) {
    certDropzone.addEventListener("click", function () { certImageInput.click(); });
    certImageInput.addEventListener("change", function () {
      if (certImageInput.files[0]) certSetFile(certImageInput.files[0]);
    });
    ["dragover", "dragenter"].forEach(function (ev) {
      certDropzone.addEventListener(ev, function (e) { e.preventDefault(); certDropzone.classList.add("dragover"); });
    });
    ["dragleave", "drop"].forEach(function (ev) {
      certDropzone.addEventListener(ev, function (e) { e.preventDefault(); certDropzone.classList.remove("dragover"); });
    });
    certDropzone.addEventListener("drop", function (e) {
      if (e.dataTransfer.files[0]) certSetFile(e.dataTransfer.files[0]);
    });
  }

  // Submit form
  if (certForm) {
    certForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      certResetErrors();
      var valid = true;

      if (certTitleInput.value.trim().length < 2) {
        certTitleError.textContent = "Title is required (min 2 characters)";
        valid = false;
      }
      if (!certEditingId && !certSelectedFile) {
        certImageError.textContent = "Please choose an image";
        valid = false;
      }
      if (!valid) return;

      var fd = new FormData();
      fd.append("title", certTitleInput.value.trim());
      fd.append("company", certCompanyInput.value.trim());
      fd.append("credential_id", certCredIdInput.value.trim());
      fd.append("credential_link", certCredLinkInput.value.trim());
      if (certSelectedFile) fd.append("image", certSelectedFile);

      var url = certEditingId
        ? "index.php?/api/admin/certificates/" + certEditingId
        : "index.php?/api/admin/certificates";

      certFormSubmit.disabled = true;
      try {
        var res = await fetch(url, { method: "POST", body: fd });
        var data = await res.json().catch(function () { return {}; });
        if (res.ok && data.success && data.certificate) {
          certClearForm();
          certCloseOverlay(certFormOverlay);
          location.reload();
        } else if (data.errors) {
          certTitleError.textContent = data.errors.title || "";
          certImageError.textContent = data.errors.image || "";
        } else {
          certImageError.textContent = data.error || "Error";
        }
      } catch (_) {
        certImageError.textContent = "Network error";
      } finally {
        certFormSubmit.disabled = false;
      }
    });
  }

  // Close modal
  if (certFormOverlay) {
    certFormOverlay.addEventListener("pointerdown", function (e) {
      if (e.target === certFormOverlay) certCloseOverlay(certFormOverlay);
    });
  }
  document.querySelectorAll("[data-close-modal]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var ov = btn.closest(".editor-overlay");
      if (ov && ov.id === "certFormOverlay") certCloseOverlay(certFormOverlay);
    });
  });

  // Escape key for cert modal
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && certFormOverlay && !certFormOverlay.hidden) {
      certCloseOverlay(certFormOverlay);
    }
  });

  // ── Init ──
  renderCards();
  applyVisibility();
}
