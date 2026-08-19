/* ============================================================
   MELO — скрипт страницы услуги

   Урезанная версия main.js проекта MELO: убрано всё, что берёт на себя
   тема сайта — мобильное меню, липкая шапка, попапы. Осталось появление
   блоков и параллакс первого экрана.

   Всё ищется ВНУТРИ .melo-page, поэтому на других страницах скрипт
   ничего не делает и ни с чем не конфликтует. jQuery не нужен.
   ============================================================ */

(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Шапка живёт вне .melo-page — она общая для всех страниц сайта,
     поэтому ищем её по документу. Всё остальное только внутри .melo-page. */
  var header = document.querySelector('.melo-site-header');
  var burger = document.querySelector('.melo-burger');

  if (header && burger) {
    burger.addEventListener('click', function () {
      var open = header.classList.toggle('melo-is-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    header.querySelectorAll('.melo-nav__link').forEach(function (link) {
      link.addEventListener('click', function () {
        header.classList.remove('melo-is-open');
        burger.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* --- Шапка реагирует на прокрутку --------------------------- */
  if (header) {
    var lastScrolled = null;
    var syncHeader = function () {
      var scrolled = window.pageYOffset > 40;
      if (scrolled !== lastScrolled) {
        header.classList.toggle('melo-is-scrolled', scrolled);
        lastScrolled = scrolled;
      }
    };
    window.addEventListener('scroll', syncHeader, { passive: true });
    syncHeader();
  }

  /* Дальше — только содержимое страниц MELO. Если его на странице нет,
     работает одна шапка выше, и это нормально. */
  var page = document.querySelector('.melo-page');
  if (!page) return;

  /* --- Слайдер направлений: горизонтальный скролл + прогресс ---
     Нужен шаблону «направление деятельности» и главной. На странице
     услуги слайдера нет, и цикл просто не находит ничего. */
  page.querySelectorAll('[data-slider]').forEach(function (root) {
    var track = root.querySelector('[data-slider-track]');
    var bar   = root.querySelector('.melo-slider-progress__bar');
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

    /* первый расчёт идёт до загрузки картинок и шрифтов, поэтому
       пересчитываем, когда размеры устоятся */
    window.addEventListener('load', sync);
    if ('ResizeObserver' in window) new ResizeObserver(sync).observe(track);

    sync();
  });

  /* --- Появление блоков со ступенчатой задержкой --------------
     Положение считаем сами по getBoundingClientRect, а не через
     IntersectionObserver. Наблюдатель присылает результат асинхронно и
     только когда браузер рисует кадр, поэтому при восстановлении
     прокрутки (переход по якорю, возврат из истории, F5 на середине
     страницы) видимая часть успевает остаться пустой. Прямой замер
     таких дыр не оставляет. */
  var reveal = [].slice.call(page.querySelectorAll('[data-reveal]'));

  if (reveal.length && !reduceMotion) {
    var firstPass = true;

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
        if (r.top >= h * 0.92 || r.bottom <= 0) return true;
        el.style.transitionDelay = firstPass ? '0ms' : staggerFor(el) + 'ms';
        el.classList.add('melo-is-visible');
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
    window.addEventListener('load', showInView);
    showInView();
  } else {
    reveal.forEach(function (el) { el.classList.add('melo-is-visible'); });
  }

  /* снимает аварийный показ, заведённый инлайновым скриптом в шаблоне */
  document.documentElement.classList.add('melo-reveal-ready');

  /* --- Параллакс фотографии первого экрана -------------------- */
  var hero = page.querySelector('.melo-hero');
  var heroBg = page.querySelector('.melo-hero__bg');

  if (hero && heroBg && !reduceMotion) {
    var ticking = false;

    var moveHero = function () {
      var h = hero.offsetHeight;
      var top = hero.getBoundingClientRect().top + window.pageYOffset;
      var progress = Math.min(Math.max((window.pageYOffset - top) / h, 0), 1);
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
