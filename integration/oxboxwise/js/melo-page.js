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

  var menu = document.querySelector('.melo-menu');

  if (burger && menu) {
    var closeBtn = menu.querySelector('.melo-menu__close');

    var setMenu = function (open) {
      /* hidden снимаем до анимации, иначе переход не запустится:
         элемент с display:none не анимируется */
      if (open) menu.hidden = false;
      window.requestAnimationFrame(function () {
        menu.classList.toggle('melo-is-open', open);
      });
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      /* Пока меню открыто, страницу под ним не прокручиваем. Скрытие
         полосы прокрутки сдвигает вёрстку на её ширину — логотип в этот
         момент дёргался. Компенсируем отступом ровно на эту ширину. */
      var gap = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.paddingRight = open && gap > 0 ? gap + 'px' : '';
      document.body.classList.toggle('melo-menu-open', open);
      if (!open) {
        window.setTimeout(function () {
          if (!menu.classList.contains('melo-is-open')) menu.hidden = true;
        }, 400);
      }
    };

    burger.addEventListener('click', function () { setMenu(true); });
    burger.addEventListener('melo-close', function () { setMenu(false); });
    if (closeBtn) closeBtn.addEventListener('click', function () { setMenu(false); });

    /* переход по пункту закрывает меню */
    menu.querySelectorAll('.melo-menu__link').forEach(function (link) {
      link.addEventListener('click', function () { setMenu(false); });
    });

    /* Escape тоже закрывает */
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('melo-is-open')) setMenu(false);
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

  /* --- Переход к разделу по якорю -----------------------------

     Тема гоняет страницу через GSAP ScrollSmoother: вся вёрстка лежит
     внутри .scroll-parent — фиксированного бокса во весь экран с
     overflow: hidden, — а двигает содержимое скрипт, слушая прокрутку
     ОКНА. Высоту документу при этом даёт body, поэтому обычная полоса
     прокрутки на месте и всё выглядит как всегда.

     Отсюда ловушка, на которой меню и сломалось. scrollIntoView возит
     не окно, а ближайшего прокручиваемого предка, и overflow: hidden
     для скрипта прокручиваемый: уезжал сам .scroll-parent, а window
     оставался на нуле. Содержимое сдвигалось, но ни ScrollSmoother, ни
     WOW темы, ни наш показ блоков об этом не узнавали — они все висят
     на прокрутке окна. Разделы приезжали пустыми, страница разъезжалась,
     а когда бокс упирался в свой предел, переход переставал работать
     вовсе, хотя адрес в строке продолжал меняться.

     Правильный способ ровно один: двигать прокрутку окна — тем же,
     чем это делает сама тема. */

  var getSmoother = function () {
    return (window.ScrollSmoother && window.ScrollSmoother.get)
      ? window.ScrollSmoother.get()
      : null;
  };

  /* Служебный бокс прокручиваться не должен никогда. Увезти его может
     не только скрипт: браузер сам прокручивает предка, уводя фокус на
     элемент за экраном. Возвращаем на ноль, чтобы страница не разъехалась. */
  var scrollParent = document.querySelector('.scroll-parent');
  if (scrollParent) {
    scrollParent.addEventListener('scroll', function () {
      if (scrollParent.scrollTop || scrollParent.scrollLeft) {
        scrollParent.scrollTop = 0;
        scrollParent.scrollLeft = 0;
      }
    }, { passive: true });
  }

  /* Останавливаемся не вплотную к разделу, а под липкой шапкой. */
  var headerGap = function () {
    var h = header ? Math.round(header.getBoundingClientRect().height) : 88;
    return h + 16;
  };

  var scrollToSection = function (target) {
    var sm = getSmoother();
    var stop = 'top ' + headerGap() + 'px';

    /* Где раздел окажется в единицах прокрутки окна. У смузера для этого
       есть свой расчёт: во время движения видимое положение отстаёт от
       прокрутки, и считать по getBoundingClientRect в этот момент нельзя. */
    var wanted = function () {
      if (sm && typeof sm.offset === 'function') {
        try {
          var v = sm.offset(target, stop);
          if (typeof v === 'number' && isFinite(v)) return Math.max(0, Math.round(v));
        } catch (err) { /* ниже посчитаем вручную */ }
      }
      var base = sm && typeof sm.scrollTop === 'function' ? sm.scrollTop() : window.pageYOffset;
      return Math.max(0, Math.round(target.getBoundingClientRect().top + base - headerGap()));
    };

    var goTo = function (y, smooth) {
      if (sm) sm.scrollTo(y, smooth);
      else window.scrollTo({ top: y, behavior: smooth ? 'smooth' : 'auto' });
    };

    goTo(wanted(), true);

    /* Дотяжка. Пока едем, ниже догружаются ленивые картинки, страница
       растёт, и точка, посчитанная в момент клика, устаревает: до «Услуг»
       не доезжало больше двух тысяч пикселей. Поэтому пересчитываем на
       ходу и поправляемся. Тронул колесо — отступаем, у человека приоритет. */
    var cancelled = false;
    var give = function () { cancelled = true; };
    var giveUp = ['wheel', 'touchstart', 'keydown'];
    giveUp.forEach(function (t) {
      window.addEventListener(t, give, { passive: true });
    });

    /* Поправлять можно только после остановки. Смузер едет полторы
       секунды с затуханием, и поправка на ходу отправляет страницу
       мимо цели — на «Услугах» так проскакивало на 458px назад.
       Поэтому ждём, пока прокрутка перестанет меняться, и только тогда
       сверяемся с целью. */
    var at = function () {
      return sm && typeof sm.scrollTop === 'function'
        ? Math.round(sm.scrollTop())
        : Math.round(window.pageYOffset);
    };

    var prev = null;
    var still = 0;
    var fixes = 0;
    var ticks = 0;

    var settle = function () {
      if (cancelled || ticks >= 30 || fixes >= 3) {
        giveUp.forEach(function (t) { window.removeEventListener(t, give); });
        return;
      }
      ticks += 1;

      var now = at();
      still = (prev !== null && Math.abs(now - prev) <= 1) ? still + 1 : 0;
      prev = now;

      /* Показ блоков и у темы (WOW), и у нас висит на прокрутке окна.
         После программного переезда будим его вручную, иначе раздел
         останется пустым — ровно тот баг, который мы и ловим. */
      window.dispatchEvent(new Event('scroll'));

      if (still >= 2) {
        var y = wanted();
        if (Math.abs(y - now) > 2) {
          fixes += 1;
          goTo(y, true);
          still = 0;
          prev = null;
        } else {
          giveUp.forEach(function (t) { window.removeEventListener(t, give); });
          return;
        }
      }

      setTimeout(settle, 180);
    };
    setTimeout(settle, 300);
  };

  /* Заход сразу по адресу с якорем. Тема это тоже делает, но считает
     точку на готовом DOM — до того, как встанут ленивые картинки, —
     поэтому промахивается. Повторяем после загрузки, уже с дотяжкой. */
  var openStartHash = function () {
    var id = (window.location.hash || '').slice(1);
    if (!id) return;
    var target = document.getElementById(id);
    if (target) scrollToSection(target);
  };
  if (document.readyState === 'complete') setTimeout(openStartHash, 80);
  else window.addEventListener('load', function () { setTimeout(openStartHash, 80); });

  /* Клик слушаем в фазе перехвата и останавливаем событие: иначе следом
     отработает обработчик темы и увезёт прокрутку не туда. */
  document.addEventListener('click', function (e) {
    var link = e.target.closest ? e.target.closest('a[href^="#"]') : null;
    if (!link) return;

    /* только наши ссылки: шапка, меню, подвал */
    if (!link.closest('.melo-site-header, .melo-menu, .melo-site-footer')) return;

    var id = link.getAttribute('href').slice(1);

    /* Пустая решётка — адрес-заглушка (Telegram, MAX, пока не заполнены).
       Отпустить её нельзя: обработчик темы ищет [data-anc_id="#"], не
       находит и падает на .offset() несуществующего элемента. Гасим
       клик здесь — ошибки нет, страница не прыгает наверх. */
    if (!id) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return;
    }

    var target = document.getElementById(id);
    if (!target) return;

    e.preventDefault();
    e.stopImmediatePropagation();

    /* Клик перехвачен в фазе capture, поэтому обработчик закрытия на
       самой ссылке уже не сработает — закрываем меню здесь. */
    if (link.closest('.melo-menu')) {
      var burgerBtn = document.querySelector('.melo-burger');
      if (burgerBtn) burgerBtn.dispatchEvent(new Event('melo-close'));
    }

    scrollToSection(target);

    /* адрес обновляем без прыжка: history вместо location.hash */
    if (window.history && window.history.replaceState) {
      window.history.replaceState(null, '', '#' + id);
    }
  }, true);

  /* Метка «страницей правит ScrollSmoother». Он держит всю вёрстку в
     трёхмерном слое, а в нём Chrome обрезает скруглённые углы по
     прямоугольнику, как только ребёнок получает собственную анимацию
     трансформации. Из-за этого у карточек направлений при зуме
     фотографии пропадало скругление — проверено съёмкой угла крупно:
     в покое дуга 14px, при наведении прямой угол. Один класс на html
     позволяет выключить зум ровно там, где он ломается, и оставить
     его на внутренних страницах, где смузера нет. */
  if (getSmoother()) document.documentElement.classList.add('melo-smooth');

  /* --- Галерея проекта ----------------------------------------
     В разметке страницы проекта стоит data-fancybox, сама библиотека
     лежит в libs.min.js темы (Fancybox 4.0.31) вместе со своими
     стилями в libs.min.css — но нигде не включается. С четвёртой
     версии Fancybox перестал цепляться к разметке сам, ему нужен
     явный bind, а третья это делала автоматически. Отсюда мёртвая
     галерея: клик по фотографии не открывал ничего.

     Ставим привязку здесь, а не в теме, чтобы правка ехала вместе с
     нашим пакетом. Условие по наличию ссылок — чтобы на страницах без
     галереи ничего не выполнялось. */
  if (window.Fancybox && document.querySelector('[data-fancybox]')) {
    window.Fancybox.bind('[data-fancybox]', {
      /* Подписей у снимков нет, поэтому и панель под них не нужна. */
      Toolbar: { display: ['counter', 'zoom', 'close'] },
      Thumbs: { autoStart: false },
      Image: { zoom: true },
      /* Долистали до края — останавливаемся, а не уходим на первый кадр:
         так видно, что галерея кончилась. */
      Carousel: { infinite: false }
    });
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

    /* --- Перетаскивание мышью ---------------------------------
       Пальцем и колесом трек листался и раньше, а мышью его хочется
       именно тащить. Три вещи, без которых это не работает:

       • ссылка внутри карточки при нажатии запускает собственный
         перенос адреса, указатель уходит браузеру, и обработчик не
         получает ни одного движения — поэтому переносу говорим «нет»;
       • привязка к карточкам (scroll-snap) на время перетаскивания
         снимается: иначе она возвращает трек на место под рукой;
       • клик после перетаскивания гасится, иначе отпускание кнопки
         на карточке открывает ссылку, хотя человек просто листал. */
    var dragging = false;
    var moved = 0;
    var startX = 0;
    var startScroll = 0;
    var pointer = null;

    [].forEach.call(track.querySelectorAll('a, img'), function (el) {
      el.setAttribute('draggable', 'false');
    });

    track.addEventListener('pointerdown', function (e) {
      /* палец и перо и так листают трек штатной прокруткой */
      if (e.pointerType !== 'mouse' || e.button !== 0) return;
      dragging = true;
      moved = 0;
      startX = e.clientX;
      startScroll = track.scrollLeft;
      pointer = e.pointerId;
      try { track.setPointerCapture(pointer); } catch (err) { /* не критично */ }
      track.classList.add('melo-is-dragging');
    });

    track.addEventListener('pointermove', function (e) {
      if (!dragging) return;
      var dx = e.clientX - startX;
      if (Math.abs(dx) > moved) moved = Math.abs(dx);
      track.scrollLeft = startScroll - dx;
      e.preventDefault();
    });

    var stopDrag = function () {
      if (!dragging) return;
      dragging = false;
      if (pointer !== null) {
        try { track.releasePointerCapture(pointer); } catch (err) { /* уже отпущен */ }
      }
      /* Класс снимаем в следующем кадре: вернувшаяся привязка тут же
         доводит трек до ближайшей карточки, и это выглядит как ответ
         на движение, а не как рывок посреди него. */
      window.requestAnimationFrame(function () {
        track.classList.remove('melo-is-dragging');
      });
    };

    track.addEventListener('pointerup', stopDrag);
    track.addEventListener('pointercancel', stopDrag);
    track.addEventListener('pointerleave', stopDrag);

    /* Фаза перехвата — чтобы успеть раньше самой ссылки. Порог в три
       пикселя: дрожание руки при обычном клике не должно его глушить. */
    track.addEventListener('click', function (e) {
      if (moved > 3) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, true);

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
