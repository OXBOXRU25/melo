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

  var page = document.querySelector('.melo-page');
  if (!page) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
    window.addEventListener('load', showInView);
    showInView();
  } else {
    reveal.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* снимает аварийный показ, заведённый инлайновым скриптом в шаблоне */
  document.documentElement.classList.add('reveal-ready');

  /* --- Параллакс фотографии первого экрана -------------------- */
  var hero = page.querySelector('.hero');
  var heroBg = page.querySelector('.hero__bg');

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
