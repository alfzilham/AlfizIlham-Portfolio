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
  renderServices();
  initCircularGallery();
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
  initGalleryHover();
  initVisitorCounter();
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

function renderServices() {
  const grid = document.getElementById("servicesGrid");
  if (!grid) return;

  const services = window.SERVICES_DATA;

  grid.innerHTML = services.map(
    (service) => `
    <div class="service-item">
      <div class="service-number">${service.number}</div>
      <h4 class="service-title">${service.title}</h4>
      <p class="service-desc">${service.description}</p>
    </div>`
  ).join("");
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
  const BEND = 3;
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
   GALLERY (from PHP rendered HTML)
   -------------------------------------------------------------------------- */

function renderGallery() {
  // Gallery is rendered server-side in gallery.php — only init hover tooltip
}

function initGalleryHover() {
  const tooltip = document.getElementById("galleryTooltip");
  const grid = document.getElementById("galleryGrid");
  if (!tooltip || !grid) return;

  grid.addEventListener("mousemove", (e) => {
    const item = e.target.closest(".gallery-item");
    if (!item) return;

    tooltip.textContent = item.dataset.desc;
    tooltip.hidden = false;
    tooltip.style.left = e.clientX + 16 + "px";
    tooltip.style.top = e.clientY - 10 + "px";
  });

  grid.addEventListener("mouseleave", () => {
    tooltip.hidden = true;
  });

  grid.addEventListener("mouseout", (e) => {
    if (!e.target.closest(".gallery-item")) {
      tooltip.hidden = true;
    }
  });
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
