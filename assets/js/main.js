(function () {
  var btn = document.getElementById('mobile-menu-toggle');
  var menu = document.getElementById('mobile-menu');
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var open = menu.classList.toggle('hidden') === false;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
})();

(function () {
  var root = document.querySelector('[data-offers-carousel]');
  var track = root && root.querySelector('[data-carousel-track]');
  if (!root || !track) return;

  var slides = [].slice.call(root.querySelectorAll('[data-carousel-slide]'));
  var prevBtn = root.querySelector('[data-carousel-prev]');
  var nextBtn = root.querySelector('[data-carousel-next]');
  var dotsWrap = root.querySelector('[data-carousel-dots]');
  var eyebrowEl = root.querySelector('[data-offers-eyebrow]');
  var headingEl = root.querySelector('[data-offers-heading]');
  var subEl = root.querySelector('[data-offers-sub]');
  var reduceMotion =
    typeof window.matchMedia === 'function' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var n = slides.length;
  if (n < 1) return;

  var i = 0;
  var timerId = null;
  var transitionMs = 500;

  function syncOfferOverlay(slideEl) {
    if (!slideEl) return;
    var eyebrow = slideEl.getAttribute('data-slide-eyebrow') || '';
    var headingLine1 = slideEl.getAttribute('data-slide-heading-line1') || '';
    var headingLine2 = slideEl.getAttribute('data-slide-heading-line2') || '';
    var sub = slideEl.getAttribute('data-slide-sub') || '';
    if (eyebrowEl) eyebrowEl.textContent = eyebrow;
    if (headingEl) {
      var h1 = headingEl.querySelector('[data-offers-heading-line1]');
      var h2 = headingEl.querySelector('[data-offers-heading-line2]');
      if (h1) h1.textContent = headingLine1;
      if (h2) {
        h2.textContent = headingLine2;
        h2.classList.toggle('hidden', headingLine2 === '');
      }
    }
    if (subEl) subEl.textContent = sub;
  }

  function setTrackTransition(enabled) {
    if (reduceMotion || !enabled) {
      track.style.transition = 'none';
      return;
    }
    track.style.transition = 'transform ' + transitionMs + 'ms ease-out';
  }

  function go(idx) {
    i = ((idx % n) + n) % n;
    track.style.transform = 'translateX(-' + i * 100 + '%)';
    slides.forEach(function (el, j) {
      if (j === i) el.setAttribute('data-carousel-active', '');
      else el.removeAttribute('data-carousel-active');
    });
    syncOfferOverlay(slides[i]);
    if (dotsWrap) {
      var dots = dotsWrap.querySelectorAll('button[data-carousel-dot]');
      dots.forEach(function (d, j) {
        var on = j === i;
        d.setAttribute('aria-selected', on ? 'true' : 'false');
        d.setAttribute('tabindex', on ? '0' : '-1');
        d.className = on ? dotOn : dotOff;
      });
    }
  }

  if (n < 2) {
    syncOfferOverlay(slides[0]);
    return;
  }

  function buildDots() {
    if (!dotsWrap) return;
    dotsWrap.innerHTML = '';
    for (var d = 0; d < n; d++) {
      var b = document.createElement('button');
      b.type = 'button';
      b.setAttribute('data-carousel-dot', '');
      b.setAttribute('role', 'tab');
      b.setAttribute('aria-label', 'Diapozitiv ' + (d + 1) + ' din ' + n);
      b.className = dotOff;
      (function (index) {
        b.addEventListener('click', function () {
          setTrackTransition(true);
          go(index);
          restartTimer();
        });
      })(d);
      dotsWrap.appendChild(b);
    }
  }

  function restartTimer() {
    if (reduceMotion || n < 2) return;
    if (timerId) window.clearInterval(timerId);
    timerId = window.setInterval(function () {
      setTrackTransition(true);
      go(i + 1);
    }, 6500);
  }

  var dotBase =
    'h-2.5 w-2.5 shrink-0 rounded-full border transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900';
  var dotOff = dotBase + ' border-white/45 bg-white/15 shadow-sm hover:bg-white/25';
  var dotOn = dotBase + ' border-white bg-white shadow-sm';

  buildDots();

  setTrackTransition(true);
  go(0);

  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      setTrackTransition(true);
      go(i - 1);
      restartTimer();
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      setTrackTransition(true);
      go(i + 1);
      restartTimer();
    });
  }

  root.addEventListener('mouseenter', function () {
    if (timerId) window.clearInterval(timerId);
    timerId = null;
  });
  root.addEventListener('mouseleave', function () {
    restartTimer();
  });
  restartTimer();

  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      if (timerId) window.clearInterval(timerId);
      timerId = null;
    } else {
      restartTimer();
    }
  });
})();

(function () {
  var root = document.querySelector('[data-photo-upload-root]');
  var input = root && root.querySelector('[data-photo-input]');
  var btn = root && root.querySelector('[data-photo-trigger]');
  var status = root && root.querySelector('[data-photo-status]');
  if (!root || !input || !btn || !status) return;

  btn.addEventListener('click', function () {
    input.click();
  });

  input.addEventListener('change', function () {
    var files = input.files;
    if (!files || files.length === 0) {
      status.textContent = 'Nicio fotografie selectată';
      return;
    }
    if (files.length === 1) {
      status.textContent = '1 fotografie selectată: ' + files[0].name;
      return;
    }
    status.textContent = String(files.length) + ' fotografii selectate';
  });
})();

(function () {
  var mql =
    typeof window.matchMedia === 'function'
      ? window.matchMedia('(min-width: 768px)')
      : { matches: true, addEventListener: function () {}, removeEventListener: function () {} };

  var active = null;

  function pickRoot() {
    return mql.matches
      ? document.querySelector('[data-reviews-carousel-root][data-reviews-layout="desktop"]')
      : document.querySelector('[data-reviews-carousel-root][data-reviews-layout="mobile"]');
  }

  function createCarousel(root) {
    var track = root.querySelector('[data-reviews-track]');
    if (!track) return { destroy: function () {} };

    var slides = [].slice.call(root.querySelectorAll('[data-reviews-slide]'));
    var prevBtn = root.querySelector('[data-reviews-prev]');
    var nextBtn = root.querySelector('[data-reviews-next]');
    var n = slides.length;
    if (n < 1) {
      return { destroy: function () {} };
    }
    var reduceMotion =
      typeof window.matchMedia === 'function' &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var i = 0;
    var timerId = null;
    var transitionMs = 500;
    var ac = typeof AbortController !== 'undefined' ? new AbortController() : null;
    var signal = ac ? ac.signal : undefined;

    function setTrackTransition(enabled) {
      if (reduceMotion || !enabled) {
        track.style.transition = 'none';
        return;
      }
      track.style.transition = 'transform ' + transitionMs + 'ms ease-out';
    }

    function go(idx) {
      i = ((idx % n) + n) % n;
      track.style.transform = 'translateX(-' + i * 100 + '%)';
      slides.forEach(function (el, j) {
        if (j === i) el.setAttribute('data-reviews-active', '');
        else el.removeAttribute('data-reviews-active');
      });
    }

    function restartTimer() {
      if (reduceMotion || n < 2) return;
      if (timerId) window.clearInterval(timerId);
      timerId = window.setInterval(function () {
        setTrackTransition(true);
        go(i + 1);
      }, 7800);
    }

    function onVis() {
      if (document.hidden) {
        if (timerId) window.clearInterval(timerId);
        timerId = null;
      } else {
        restartTimer();
      }
    }

    function onEnter() {
      if (timerId) window.clearInterval(timerId);
      timerId = null;
    }

    function onLeave() {
      restartTimer();
    }

    function add(el, ev, fn) {
      if (!el) return;
      if (signal) el.addEventListener(ev, fn, { signal: signal });
      else el.addEventListener(ev, fn);
    }

    if (n < 2) {
      setTrackTransition(false);
      go(0);
      return {
        destroy: function () {
          if (ac) ac.abort();
        },
      };
    }

    setTrackTransition(true);
    go(0);

    add(prevBtn, 'click', function () {
      setTrackTransition(true);
      go(i - 1);
      restartTimer();
    });
    add(nextBtn, 'click', function () {
      setTrackTransition(true);
      go(i + 1);
      restartTimer();
    });
    add(root, 'mouseenter', onEnter);
    add(root, 'mouseleave', onLeave);
    add(document, 'visibilitychange', onVis);
    restartTimer();

    return {
      destroy: function () {
        if (timerId) window.clearInterval(timerId);
        timerId = null;
        if (ac) ac.abort();
      },
    };
  }

  function sync() {
    if (active) active.destroy();
    active = null;
    var root = pickRoot();
    if (!root) return;
    active = createCarousel(root);
  }

  if (mql.addEventListener) {
    mql.addEventListener('change', sync);
  } else if (mql.addListener) {
    mql.addListener(sync);
  }
  sync();
})();
