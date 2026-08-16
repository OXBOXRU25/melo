/* ============================================================
   MELO — базовые интеракции
   ============================================================ */

(function () {
  'use strict';

  /* --- Мобильное меню --------------------------------------- */
  var header = document.querySelector('.site-header');
  var burger = document.querySelector('.burger');

  if (header && burger) {
    burger.addEventListener('click', function () {
      var open = header.classList.toggle('is-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    header.querySelectorAll('.nav__link').forEach(function (link) {
      link.addEventListener('click', function () {
        header.classList.remove('is-open');
        burger.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* --- Слайдер: горизонтальный скролл + прогресс-полоса ------ */
  document.querySelectorAll('[data-slider]').forEach(function (root) {
    var track = root.querySelector('[data-slider-track]');
    var bar   = root.querySelector('.slider-progress__bar');
    var prev  = root.querySelector('[data-slider-prev]');
    var next  = root.querySelector('[data-slider-next]');
    if (!track) return;

    function step() {
      var first = track.firstElementChild;
      if (!first) return track.clientWidth;
      var styles = getComputedStyle(track);
      return first.getBoundingClientRect().width + (parseFloat(styles.columnGap) || 0);
    }

    function sync() {
      var max = track.scrollWidth - track.clientWidth;
      var ratio = max > 0 ? track.scrollLeft / max : 0;
      if (bar) {
        var visible = track.clientWidth / track.scrollWidth;
        bar.style.width = Math.min(100, (visible + (1 - visible) * ratio) * 100) + '%';
      }
      if (prev) prev.disabled = track.scrollLeft <= 1;
      if (next) next.disabled = track.scrollLeft >= max - 1;
    }

    if (prev) prev.addEventListener('click', function () {
      track.scrollBy({ left: -step(), behavior: 'smooth' });
    });

    if (next) next.addEventListener('click', function () {
      track.scrollBy({ left: step(), behavior: 'smooth' });
    });

    track.addEventListener('scroll', sync, { passive: true });
    window.addEventListener('resize', sync);

    /* первый расчёт делаем до загрузки картинок и шрифтов, поэтому
       пересчитываем, когда размеры устоятся */
    window.addEventListener('load', sync);
    if ('ResizeObserver' in window) new ResizeObserver(sync).observe(track);

    sync();
  });

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* --- Появление блоков со ступенчатой задержкой --------------
     Положение считаем сами по getBoundingClientRect, а не через
     IntersectionObserver. Наблюдатель присылает результат асинхронно и
     только когда браузер рисует кадр, поэтому при восстановлении
     прокрутки (переход по якорю, возврат из истории, F5 на середине
     страницы) видимая часть успевает остаться пустой. Прямой замер
     таких дыр не оставляет. */
  var reveal = [].slice.call(document.querySelectorAll('[data-reveal]'));

  if (reveal.length && !reduceMotion) {
    var firstPass = true;

    /* соседи по родителю уезжают друг за другом, а не разом */
    var staggerFor = function (el) {
      var siblings = [].filter.call(el.parentNode.children, function (n) {
        return n.hasAttribute && n.hasAttribute('data-reveal');
      });
      var i = siblings.indexOf(el);
      return i > 0 ? Math.min(i, 5) * 90 : 0;
    };

    var showInView = function () {
      var h = window.innerHeight || document.documentElement.clientHeight;

      reveal = reveal.filter(function (el) {
        var r = el.getBoundingClientRect();
        if (r.top >= h * 0.92 || r.bottom <= 0) {
          return true;  // ещё не в кадре — оставляем в очереди
        }
        /* то, что видно сразу при открытии, показываем без задержки:
           анимировать уже видимое незачем */
        el.style.transitionDelay = firstPass ? '0ms' : staggerFor( el ) + 'ms';
        el.classList.add('is-visible');
        return false;
      });

      firstPass = false;

      if (!reveal.length) {
        window.removeEventListener('scroll', onRevealScroll);
        window.removeEventListener('resize', showInView);
      }
    };

    var revealTicking = false;
    var onRevealScroll = function () {
      if (revealTicking) return;
      revealTicking = true;
      window.requestAnimationFrame(function () {
        revealTicking = false;
        showInView();
      });
    };

    window.addEventListener('scroll', onRevealScroll, { passive: true });
    window.addEventListener('resize', showInView);
    /* load — уже после того, как браузер восстановил прокрутку */
    window.addEventListener('load', showInView);

    showInView();
  } else {
    reveal.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* снимает аварийный показ, заведённый инлайновым скриптом в <head> */
  document.documentElement.classList.add('reveal-ready');

  /* --- Шапка реагирует на прокрутку --------------------------- */
  if (header) {
    var lastScrolled = null;
    var syncHeader = function () {
      var scrolled = window.pageYOffset > 40;
      if (scrolled !== lastScrolled) {
        header.classList.toggle('is-scrolled', scrolled);
        lastScrolled = scrolled;
      }
    };
    window.addEventListener('scroll', syncHeader, { passive: true });
    syncHeader();
  }

  /* --- Параллакс фотографии первого экрана -------------------- */
  var hero = document.querySelector('.hero');
  var heroBg = document.querySelector('.hero__bg');

  if (hero && heroBg && !reduceMotion) {
    var ticking = false;

    var moveHero = function () {
      var h = hero.offsetHeight;
      var progress = Math.min(Math.max(window.pageYOffset / h, 0), 1);
      /* запас 7% задан в CSS высотой картинки — за него не выходим */
      heroBg.style.transform = 'translate3d(0,' + (progress * h * 0.07).toFixed(1) + 'px,0)';
      ticking = false;
    };

    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(moveHero);
    }, { passive: true });

    window.addEventListener('resize', moveHero);
    moveHero();
  }
})();
