/* ============================================================================
   MOTION ENGINE — Cahit Necipoğlu
   ----------------------------------------------------------------------------
   Sıfır bağımlılık. Harici animasyon kütüphanesi YOK.

   Referans siteler (Ege, Bien, VitrA) neredeyse hiç hareket kullanmıyor.
   Ayrım burada açılıyor — ama her hareket fotoğrafın ya da metnin
   hizmetinde. Kendini gösteren efekt yok.

   Kurallar
     · Tek requestAnimationFrame döngüsü, tüm modüller ona abone.
     · Ölçümler önbellekte; okuma ve yazma ayrı fazlarda — layout thrash yok.
     · Yalnızca transform / opacity / clip-path yazılır.
     · prefers-reduced-motion açıksa süreye bağlı her şey kapanır.

   Modüller
     1  Ortam, yardımcılar, ticker
     2  Scroll durumu
     3  Ölçüm kayıt defteri
     4  Metin bölme (satır maskesi)
     5  Reveal · perde · çizgi
     6  Parallax
     7  Yapışkan görsel / kayan liste
     8  Sayaçlar
     9  Özel imleç
    10  Magnetic
    11  Üst bar
    12  Menü
    13  Sayfa geçişi
    14  İlerleme çubuğu
    15  Şube haritası
   ========================================================================== */

/* ── 1 ─ Ortam, yardımcılar, ticker ──────────────────────────────────────── */

const mqFine = matchMedia('(hover: hover) and (pointer: fine)');
const mqReduced = matchMedia('(prefers-reduced-motion: reduce)');

const env = {
  get fine() { return mqFine.matches; },
  get reduced() { return mqReduced.matches; },
};

const clamp = (v, a = 0, b = 1) => (v < a ? a : v > b ? b : v);
const lerp = (a, b, t) => a + (b - a) * t;
const easeOut = (t) => (t >= 1 ? 1 : 1 - Math.pow(2, -10 * t));

const ticker = {
  subs: new Set(),
  running: false,

  add(fn) {
    this.subs.add(fn);
    if (!this.running) {
      this.running = true;
      requestAnimationFrame(this.loop);
    }
    return fn;
  },

  remove(fn) { this.subs.delete(fn); },

  loop() {
    scroll.sample();
    ticker.subs.forEach((fn) => fn(scroll));
    requestAnimationFrame(ticker.loop);
  },
};

/* ── 2 ─ Scroll durumu ───────────────────────────────────────────────────── */

const scroll = {
  y: 0, prev: 0, delta: 0, velocity: 0, direction: 1,
  vw: 0, vh: 0, progress: 0,

  sample() {
    this.prev = this.y;
    this.y = window.scrollY || document.documentElement.scrollTop || 0;
    this.delta = this.y - this.prev;

    this.velocity = lerp(this.velocity, this.delta, 0.15);
    if (Math.abs(this.velocity) < 0.01) this.velocity = 0;
    if (this.delta !== 0) this.direction = this.delta > 0 ? 1 : -1;

    const max = document.documentElement.scrollHeight - this.vh;
    this.progress = max > 0 ? clamp(this.y / max) : 0;
  },

  measure() {
    this.vw = window.innerWidth;
    this.vh = window.innerHeight;
  },
};

/* ── 3 ─ Ölçüm kayıt defteri ─────────────────────────────────────────────── */

const registry = [];

function register(measure, update) {
  const entry = { measure, update };
  registry.push(entry);
  measure();
  if (update) ticker.add(update);
  return entry;
}

function measureAll() {
  scroll.measure();
  for (const entry of registry) entry.measure();
  scroll.sample();
}

function watchImages() {
  for (const img of document.images) {
    if (img.complete) continue;
    img.addEventListener('load', measureAll, { once: true });
    img.addEventListener('error', measureAll, { once: true });
  }
}

/* ── 4 ─ Metin bölme ─────────────────────────────────────────────────────── */
/*
   [data-split] başlıkları kelimelere bölünür, görsel satırlara gruplanıp
   .m-line maskesine sarılır — satırlar sırayla yükselir. Satır kırılımı
   ekran genişliğine bağlı olduğu için resize'da yeniden kurulur.
*/

const originalHTML = new WeakMap();

function splitWords(el) {
  if (!originalHTML.has(el)) originalHTML.set(el, el.innerHTML);
  el.innerHTML = originalHTML.get(el);

  const walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT);
  const nodes = [];
  while (walker.nextNode()) nodes.push(walker.currentNode);

  for (const node of nodes) {
    if (!node.textContent.trim()) continue;

    const frag = document.createDocumentFragment();

    for (const part of node.textContent.split(/(\s+)/)) {
      if (part === '') continue;

      if (/^\s+$/.test(part)) {
        frag.appendChild(document.createTextNode(part));
        continue;
      }

      const word = document.createElement('span');
      word.className = 'm-word';
      word.textContent = part;
      frag.appendChild(word);
    }

    node.parentNode.replaceChild(frag, node);
  }

  return Array.from(el.querySelectorAll('.m-word'));
}

function groupIntoLines(words) {
  if (!words.length) return [];

  const groups = [];
  let current = null;
  let top = null;

  for (const word of words) {
    const y = Math.round(word.offsetTop);

    if (top === null || Math.abs(y - top) > 3) {
      current = [];
      groups.push(current);
      top = y;
    }

    current.push(word);
  }

  const lines = [];

  for (const group of groups) {
    const first = group[0];
    const last = group[group.length - 1];

    // Satır içi etiket araya girmişse sarma yapma — kelimeler yine yükselir.
    if (first.parentNode !== last.parentNode) {
      lines.push(...group.map((w) => w.parentNode));
      continue;
    }

    const line = document.createElement('span');
    line.className = 'm-line';
    first.parentNode.insertBefore(line, first);

    let node = first;
    while (node) {
      const next = node === last ? null : node.nextSibling;
      line.appendChild(node);
      node = next;
    }

    lines.push(line);
  }

  return lines;
}

function initSplit() {
  const targets = Array.from(document.querySelectorAll('[data-split]')).map((el) => ({ el, lines: [] }));
  if (!targets.length) return;

  const build = () => {
    for (const target of targets) {
      target.lines = groupIntoLines(splitWords(target.el));

      target.lines.forEach((line, i) => {
        line.querySelectorAll('.m-word').forEach((word) => {
          word.style.transitionDelay = `${(i * 0.085).toFixed(3)}s`;
        });
      });

      if (target.shown) target.lines.forEach((line) => line.classList.add('is-in'));
    }
  };

  build();

  const show = (target) => {
    target.shown = true;
    target.lines.forEach((line) => line.classList.add('is-in'));
  };

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;

        const target = targets.find((t) => t.el === entry.target);
        if (target) show(target);
        observer.unobserve(entry.target);
      }
    },
    { rootMargin: '0px 0px -10% 0px', threshold: 0.06 },
  );

  for (const target of targets) observer.observe(target.el);

  // Açılışta ekranda olan başlıklar beklemesin.
  requestAnimationFrame(() => {
    const vh = window.innerHeight;

    for (const target of targets) {
      const rect = target.el.getBoundingClientRect();
      if (rect.top >= vh || rect.bottom <= 0) continue;

      show(target);
      observer.unobserve(target.el);
    }
  });

  let lastWidth = window.innerWidth;
  window.addEventListener('resize', () => {
    if (window.innerWidth === lastWidth) return;
    lastWidth = window.innerWidth;
    build();
  });
}

/* ── 5 ─ Reveal · perde · çizgi ──────────────────────────────────────────── */

function initReveal() {
  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        entry.target.classList.add('is-in');
        observer.unobserve(entry.target);
      }
    },
    { rootMargin: '0px 0px -9% 0px', threshold: 0.05 },
  );

  for (const host of document.querySelectorAll('[data-reveal-stagger]')) {
    const step = parseFloat(host.dataset.revealStagger) || 0.08;
    Array.from(host.children).forEach((child, i) => {
      if (!child.hasAttribute('data-reveal')) child.setAttribute('data-reveal', '');
      child.style.transitionDelay = `${i * step}s`;
    });
  }

  const elements = Array.from(document.querySelectorAll('[data-reveal], [data-veil], [data-draw]'));
  for (const el of elements) observer.observe(el);

  /* rootMargin alt kenarı içeri çeker; açılışta zaten ekranda olan öğeler
     bu yüzden kilitli kalırdı (ilk ekrandaki butonlar gibi). */
  requestAnimationFrame(() => {
    const vh = window.innerHeight;

    for (const el of elements) {
      const rect = el.getBoundingClientRect();
      if (rect.top >= vh || rect.bottom <= 0) continue;

      el.classList.add('is-in');
      observer.unobserve(el);
    }
  });
}

/* ── 6 ─ Parallax ────────────────────────────────────────────────────────── */
/*
   data-parallax="0.12" — görsel kabının içinde ters yönde kayar. Kap sabit,
   yerleşim asla kaymaz. Oran yüksekliğe göre, piksel değil.
*/

function initParallax() {
  const els = Array.from(document.querySelectorAll('[data-parallax]'));
  if (!els.length || env.reduced) return;

  const items = els.map((el) => ({
    el,
    speed: parseFloat(el.dataset.parallax) || 0.12,
    top: 0,
    height: 0,
  }));

  const measure = () => {
    for (const item of items) {
      const rect = item.el.getBoundingClientRect();
      item.top = rect.top + window.scrollY;
      item.height = rect.height;
      item.el.style.scale = String(1 + item.speed * 2.1);
    }
  };

  const update = (s) => {
    for (const item of items) {
      const center = item.top + item.height / 2;
      const ratio = clamp((center - (s.y + s.vh / 2)) / (s.vh + item.height), -1, 1);
      item.el.style.translate = `0 ${(ratio * item.height * item.speed).toFixed(2)}px`;
    }
  };

  register(measure, update);
}

/* ── 7 ─ Yatay ray ───────────────────────────────────────────────────────── */
/*
   Kartlar tek sırada; ekrana sığmayan kısım sola kaydırılır. Dokunmatikte
   tarayıcının kendi kaydırması yeterli — burada fareyle sürükleme, ok
   düğmeleri ve konum göstergesi eklenir.

   Sürükleme sonrası tıklamanın bağlantıyı açmaması için eşik konur:
   6 pikselden fazla sürüklendiyse o tıklama yutulur.
*/

function initRail() {
  for (const group of document.querySelectorAll('[data-rail-group]')) {
    const rail = group.querySelector('[data-rail]');
    if (!rail) continue;

    const prev = group.querySelector('[data-rail-prev]');
    const next = group.querySelector('[data-rail-next]');
    const thumb = group.querySelector('[data-rail-thumb]');

    /** Bir kartın kapladığı yatay mesafe (kart + boşluk). */
    const step = () => {
      const first = rail.firstElementChild;
      if (!first) return rail.clientWidth * 0.8;

      const gap = parseFloat(getComputedStyle(rail).columnGap) || 0;
      return first.getBoundingClientRect().width + gap;
    };

    const maxScroll = () => Math.max(0, rail.scrollWidth - rail.clientWidth);

    /** Bölüm pine geçtiyse ray dönüşümle sürülüyor — burası devreden çıkar. */
    const pinned = () => rail.closest('.pin')?.classList.contains('is-pinned') === true;

    const sync = () => {
      if (pinned()) return;

      const max = maxScroll();

      // Taşma yoksa denetimler gizlenir — 6 karttan azsa ray gerekmez.
      group.classList.toggle('rail-idle', max < 2);

      if (prev) prev.setAttribute('aria-disabled', String(rail.scrollLeft <= 1));
      if (next) next.setAttribute('aria-disabled', String(rail.scrollLeft >= max - 1));

      if (thumb) {
        const ratio = rail.clientWidth / (rail.scrollWidth || 1);
        const progress = max > 0 ? rail.scrollLeft / max : 0;
        thumb.style.transform =
          `translateX(${(progress * (1 - ratio) * 100 / (ratio || 1)).toFixed(3)}%) scaleX(${ratio.toFixed(4)})`;
      }
    };

    rail.addEventListener('scroll', sync, { passive: true });
    window.addEventListener('resize', sync, { passive: true });

    prev?.addEventListener('click', () => {
      if (pinned()) return;
      rail.scrollBy({ left: -step(), behavior: env.reduced ? 'auto' : 'smooth' });
    });

    next?.addEventListener('click', () => {
      if (pinned()) return;
      rail.scrollBy({ left: step(), behavior: env.reduced ? 'auto' : 'smooth' });
    });

    /* Fareyle sürükleme — dokunmatikte tarayıcı zaten hallediyor. */
    if (env.fine) {
      let startX = 0;
      let startScroll = 0;
      let dragging = false;
      let moved = 0;

      rail.addEventListener('pointerdown', (e) => {
        if (e.button !== 0 || pinned()) return;
        dragging = true;
        moved = 0;
        startX = e.clientX;
        startScroll = rail.scrollLeft;
        rail.setPointerCapture(e.pointerId);
      });

      rail.addEventListener('pointermove', (e) => {
        if (!dragging) return;

        const delta = e.clientX - startX;
        moved = Math.max(moved, Math.abs(delta));

        if (moved > 6) rail.classList.add('is-dragging');
        rail.scrollLeft = startScroll - delta;
      });

      const end = (e) => {
        if (!dragging) return;
        dragging = false;
        rail.classList.remove('is-dragging');
        if (rail.hasPointerCapture?.(e.pointerId)) rail.releasePointerCapture(e.pointerId);
      };

      rail.addEventListener('pointerup', end);
      rail.addEventListener('pointercancel', end);

      // Sürüklemeyi bitiren tıklama bağlantıyı açmasın.
      rail.addEventListener('click', (e) => {
        if (moved <= 6) return;
        e.preventDefault();
        e.stopPropagation();
        moved = 0;
      }, true);
    }

    sync();
    // Görseller yüklendikçe genişlik değişir.
    setTimeout(sync, 600);
  }
}

/* ── 7b ─ Yatay pin ──────────────────────────────────────────────────────── */
/*
   Bölüm ekrana kilitlenir, sayfa aşağı kaydırıldıkça ray yana kayar.

   Bölüm yüksekliği = sahne yüksekliği + rayın taşan genişliği. Böylece
   yatay yol biterken pin de biter; sayfa asla "takılı" kalmaz.

   Konum ham scroll'a değil, ona doğru yumuşatılan bir değere yazılır —
   tekerlek basamaklı gelse de hareket akışkan görünür.
*/

function initPin() {
  const sections = Array.from(document.querySelectorAll('[data-pin]'));
  if (!sections.length) return;

  // Dokunmatikte parmakla yana kaydırma daha doğal; pin yalnızca farede.
  const enabled = () => env.fine && !env.reduced && window.innerWidth > 900;

  const items = sections.map((section) => ({
    section,
    stage: section.querySelector('.pin-stage'),
    rail: section.querySelector('[data-rail]'),
    top: 0,
    distance: 0,
    current: 0,
    pinned: false,
  })).filter((item) => item.stage && item.rail);

  const measure = () => {
    for (const item of items) {
      // Ölçmeden önce dönüşümü sıfırla, yoksa genişlik yanlış okunur.
      item.rail.style.transform = '';
      item.section.style.height = '';
      item.section.classList.remove('is-pinned');
      item.current = 0;
      item.pinned = false;

      if (!enabled()) { item.distance = 0; continue; }

      // Pin sınıfı kart genişliğini değiştirdiği için önce onu uygula,
      // taşmayı ondan sonra ölç.
      item.section.classList.add('is-pinned');

      const overflow = Math.max(0, item.rail.scrollWidth - item.rail.clientWidth);

      if (overflow < 8) {
        // Taşma yoksa pine gerek yok — normal raya dön.
        item.section.classList.remove('is-pinned');
        item.distance = 0;
        continue;
      }

      item.distance = overflow;
      item.pinned = true;

      /* rail-idle, pin uygulanmadan önceki ölçümden kalmış olabilir —
         pinde yatay yol her zaman var, denetimler görünmeli. */
      item.section.classList.remove('rail-idle');

      const stageHeight = item.stage.getBoundingClientRect().height;
      item.section.style.height = `${stageHeight + overflow}px`;

      const rect = item.section.getBoundingClientRect();
      item.top = rect.top + window.scrollY;
    }
  };

  const update = (s) => {
    for (const item of items) {
      if (!item.pinned) continue;

      const start = item.top - (parseFloat(getComputedStyle(item.stage).top) || 0);
      const progress = clamp((s.y - start) / item.distance);
      const target = -progress * item.distance;

      item.current = lerp(item.current, target, 0.16);
      if (Math.abs(item.current - target) < 0.08) item.current = target;

      item.rail.style.transform = `translate3d(${item.current.toFixed(2)}px, 0, 0)`;
    }
  };

  register(measure, update);

  /* Ok düğmeleri pin açıkken rayı değil sayfayı kaydırır — yatay yol
     dikey kaydırmaya bağlı olduğu için tek doğru yol bu. */
  for (const item of items) {
    const group = item.section;
    const step = () => {
      const first = item.rail.firstElementChild;
      const gap = parseFloat(getComputedStyle(item.rail).columnGap) || 0;
      return first ? first.getBoundingClientRect().width + gap : 400;
    };

    group.querySelector('[data-rail-prev]')?.addEventListener('click', (e) => {
      if (!item.pinned) return;
      e.stopImmediatePropagation();
      window.scrollBy({ top: -step(), behavior: 'smooth' });
    }, true);

    group.querySelector('[data-rail-next]')?.addEventListener('click', (e) => {
      if (!item.pinned) return;
      e.stopImmediatePropagation();
      window.scrollBy({ top: step(), behavior: 'smooth' });
    }, true);
  }

  // Konum göstergesi ve ok durumları pin akışında da güncellensin.
  ticker.add(() => {
    for (const item of items) {
      if (!item.pinned) continue;

      const progress = item.distance > 0 ? clamp(-item.current / item.distance) : 0;
      const thumb = item.section.querySelector('[data-rail-thumb]');

      if (thumb) {
        const ratio = item.rail.clientWidth / (item.rail.scrollWidth || 1);
        thumb.style.transform =
          `translateX(${(progress * (1 - ratio) * 100 / (ratio || 1)).toFixed(3)}%) scaleX(${ratio.toFixed(4)})`;
      }

      item.section.querySelector('[data-rail-prev]')?.setAttribute('aria-disabled', String(progress <= 0.001));
      item.section.querySelector('[data-rail-next]')?.setAttribute('aria-disabled', String(progress >= 0.999));
    }
  });
}

/* ── 8 ─ Sayaçlar ────────────────────────────────────────────────────────── */

function initCounters() {
  const els = document.querySelectorAll('[data-count]');
  if (!els.length) return;

  const run = (el) => {
    const target = parseFloat(el.dataset.count) || 0;
    const suffix = el.dataset.countSuffix ?? '';
    const duration = (parseFloat(el.dataset.countDuration) || 1.5) * 1000;

    if (env.reduced) { el.textContent = target + suffix; return; }

    const start = performance.now();

    const step = (now) => {
      const t = clamp((now - start) / duration);
      el.textContent = Math.round(target * easeOut(t)) + suffix;
      if (t < 1) requestAnimationFrame(step);
      else el.textContent = target + suffix;
    };

    requestAnimationFrame(step);
  };

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        run(entry.target);
        observer.unobserve(entry.target);
      }
    },
    { threshold: 0.5 },
  );

  for (const el of els) { el.textContent = '0'; observer.observe(el); }
}

/* ── 9 ─ Özel imleç ──────────────────────────────────────────────────────── */

function initCursor() {
  if (!env.fine || env.reduced) return;

  const triggers = document.querySelectorAll('[data-cursor]');
  if (!triggers.length) return;

  const dot = document.createElement('div');
  dot.className = 'cursor';
  dot.setAttribute('aria-hidden', 'true');
  document.body.appendChild(dot);

  const pos = { x: -200, y: -200 };
  const target = { x: -200, y: -200 };

  window.addEventListener('pointermove', (e) => {
    target.x = e.clientX;
    target.y = e.clientY;
  }, { passive: true });

  ticker.add(() => {
    pos.x = lerp(pos.x, target.x, 0.18);
    pos.y = lerp(pos.y, target.y, 0.18);
    dot.style.translate = `${pos.x.toFixed(1)}px ${pos.y.toFixed(1)}px`;
  });

  for (const trigger of triggers) {
    trigger.addEventListener('pointerenter', () => {
      dot.textContent = trigger.dataset.cursor || '';
      dot.classList.add('is-on');
    });

    trigger.addEventListener('pointerleave', () => dot.classList.remove('is-on'));
  }
}

/* ── 10 ─ Magnetic ───────────────────────────────────────────────────────── */

function initMagnetic() {
  if (!env.fine || env.reduced) return;

  for (const el of document.querySelectorAll('[data-magnetic]')) {
    const strength = parseFloat(el.dataset.magnetic) || 0.25;
    const target = { x: 0, y: 0 };
    const current = { x: 0, y: 0 };
    let raf = null;

    const loop = () => {
      current.x = lerp(current.x, target.x, 0.18);
      current.y = lerp(current.y, target.y, 0.18);
      el.style.translate = `${current.x.toFixed(2)}px ${current.y.toFixed(2)}px`;

      if (Math.abs(current.x - target.x) > 0.1 || Math.abs(current.y - target.y) > 0.1) {
        raf = requestAnimationFrame(loop);
      } else {
        el.style.translate = `${target.x}px ${target.y}px`;
        raf = null;
      }
    };

    const start = () => { if (raf === null) raf = requestAnimationFrame(loop); };

    el.addEventListener('pointermove', (e) => {
      const rect = el.getBoundingClientRect();
      target.x = (e.clientX - (rect.left + rect.width / 2)) * strength;
      target.y = (e.clientY - (rect.top + rect.height / 2)) * strength;
      start();
    });

    el.addEventListener('pointerleave', () => { target.x = 0; target.y = 0; start(); });
  }
}

/* ── 11 ─ Üst bar ────────────────────────────────────────────────────────── */
/*
   Hero üzerindeyken saydam ve beyaz yazı; hero'yu geçince beyaz zemin ve
   koyu yazı. Aşağı kaydırırken gizlenir, yukarı kaydırırken geri gelir.
*/

function initTopbar() {
  const bar = document.querySelector('.topbar');
  if (!bar) return;

  /* Yalnızca ana sayfada bar, koyu hero fotoğrafının üzerinde saydam durur.
     Diğer sayfalar açık zeminle başladığı için bar en tepeden itibaren katı
     olmalı — yoksa beyaz yazı krem zeminde kaybolur. */
  const overlay = document.querySelector('[data-topbar-overlay]');

  let threshold = -1;
  let solid = null;
  let hidden = null;

  const measure = () => {
    if (!overlay) {
      threshold = -1;   // her zaman katı
    } else {
      // Hero'nun belge içindeki alt kenarı. rect.top zaten kaydırmaya göre
      // değiştiği için scrollY yalnızca burada, top ile birlikte eklenir —
      // doğrudan eşiğe eklemek her ölçümde eşiği şişiriyordu.
      const rect = overlay.getBoundingClientRect();
      threshold = rect.top + window.scrollY + rect.height - bar.offsetHeight;
    }

    // Eşik değiştiyse durum yeniden değerlendirilsin.
    solid = null;
    hidden = null;
  };

  const update = (s) => {
    const shouldSolid = s.y > threshold;
    if (shouldSolid !== solid) {
      solid = shouldSolid;
      bar.classList.toggle('is-solid', solid);
    }

    const shouldHide = s.direction === 1
      && s.y > Math.max(threshold, 0) + 200
      && !document.body.classList.contains('menu-open');

    if (shouldHide !== hidden) {
      hidden = shouldHide;
      bar.classList.toggle('is-hidden', hidden);
    }
  };

  register(measure, update);
}

/* ── 12 ─ Menü ───────────────────────────────────────────────────────────── */

function initMenu() {
  const toggle = document.querySelector('[data-menu-toggle]');
  const panel = document.querySelector('.menu-panel');
  if (!toggle || !panel) return;

  const links = panel.querySelectorAll('.menu-link');
  let lockedAt = 0;

  const setOpen = (open) => {
    document.body.classList.toggle('menu-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    panel.setAttribute('aria-hidden', String(!open));

    if (open) {
      lockedAt = window.scrollY;
      document.body.style.position = 'fixed';
      document.body.style.top = `-${lockedAt}px`;
      document.body.style.width = '100%';

      links.forEach((link, i) => { link.style.transitionDelay = `${0.1 + i * 0.045}s`; });
      links[0]?.focus({ preventScroll: true });
    } else {
      document.body.style.position = '';
      document.body.style.top = '';
      document.body.style.width = '';
      window.scrollTo(0, lockedAt);

      links.forEach((link) => { link.style.transitionDelay = '0s'; });
    }
  };

  toggle.addEventListener('click', () => setOpen(!document.body.classList.contains('menu-open')));
  panel.addEventListener('click', (e) => { if (e.target.closest('a')) setOpen(false); });

  window.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape' || !document.body.classList.contains('menu-open')) return;
    setOpen(false);
    toggle.focus();
  });
}

/* ── 13 ─ Sayfa geçişi ───────────────────────────────────────────────────── */

function initVeil() {
  if (env.reduced) return;

  const veil = document.querySelector('.veil');
  if (!veil) return;

  requestAnimationFrame(() => veil.classList.add('is-in'));

  window.addEventListener('pageshow', (e) => {
    if (!e.persisted) return;
    veil.classList.remove('is-out');
    veil.classList.add('is-in');
  });

  document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;

    const href = link.getAttribute('href');

    if (
      !href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') ||
      link.target === '_blank' || link.hasAttribute('download') || link.dataset.noVeil !== undefined ||
      e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0
    ) return;

    const url = new URL(href, location.href);
    if (url.origin !== location.origin) return;
    if (url.pathname === location.pathname && url.search === location.search) return;

    e.preventDefault();
    veil.classList.remove('is-in');
    veil.classList.add('is-out');

    setTimeout(() => { location.href = url.href; }, 460);
  });
}

/* ── 14 ─ İlerleme çubuğu ────────────────────────────────────────────────── */

function initProgress() {
  const bar = document.querySelector('[data-progress]');
  if (!bar) return;

  ticker.add((s) => { bar.style.transform = `scaleX(${s.progress.toFixed(4)})`; });
}

/* ── 15 ─ Şube haritası ──────────────────────────────────────────────────── */

function initBranchMap() {
  const host = document.querySelector('[data-branches]');
  if (!host) return;

  const frame = host.querySelector('[data-branch-frame]');
  const rows = Array.from(host.querySelectorAll('[data-branch]'));
  if (!frame || !rows.length) return;

  let iframe = null;

  const show = (row) => {
    for (const other of rows) other.removeAttribute('data-branch-active');
    row.setAttribute('data-branch-active', '');

    if (!iframe) {
      iframe = document.createElement('iframe');
      iframe.loading = 'lazy';
      iframe.referrerPolicy = 'no-referrer-when-downgrade';
      iframe.className = 'absolute inset-0 h-full w-full border-0';
      frame.appendChild(iframe);
    }

    iframe.title = row.dataset.branchName ?? '';
    iframe.src = row.dataset.branchMap;
  };

  for (const row of rows) {
    row.addEventListener('click', (e) => {
      if (e.target.closest('a')) return;
      show(row);
    });
  }

  const observer = new IntersectionObserver(
    (entries) => {
      if (!entries.some((entry) => entry.isIntersecting)) return;
      show(rows.find((row) => row.hasAttribute('data-branch-active')) ?? rows[0]);
      observer.disconnect();
    },
    { rootMargin: '300px' },
  );

  observer.observe(host);
}

/* ── Başlatma ────────────────────────────────────────────────────────────── */

let resizeTimer = null;

function onResize() {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(measureAll, 140);
}

export function start() {
  scroll.measure();
  scroll.sample();

  initSplit();
  initReveal();
  initParallax();
  initRail();
  initPin();
  initCounters();
  initCursor();
  initMagnetic();
  initTopbar();
  initMenu();
  initVeil();
  initProgress();
  initBranchMap();

  measureAll();
  watchImages();

  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('orientationchange', onResize);

  document.fonts?.ready.then(measureAll);

  document.documentElement.classList.add('motion-ready');
}

export { scroll, ticker, register, measureAll, env, clamp, lerp, easeOut };
