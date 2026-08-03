/* ==========================================================================
   Инерционная прокрутка на Lenis (js/lib/lenis.min.js, версия 1.1.18, MIT).

   Библиотека положена в репозиторий, а не подключена с внешнего CDN: сайт для
   российской аудитории, и лишняя зависимость от чужого домена — это лишняя
   точка отказа. 13 КБ в проекте дешевле, чем страница без прокрутки.

   Почему именно Lenis, а не сдвиг содержимого трансформом: Lenis меняет
   НАСТОЯЩУЮ позицию прокрутки, а не едет контентом внутри обёртки. Поэтому
   продолжают работать position: sticky, IntersectionObserver (на нём держится
   появление блоков и пауза бегущей ленты) и штатные события scroll.
   Трансформ-обёртка всё это ломает.

   Отключаемся, если система просит меньше движения. На тач-устройствах
   инерцию не перехватываем — родная прокрутка пальцем лучше любой имитации.
   ========================================================================== */

(function () {
  'use strict';

  var reduced =
    window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (typeof window.Lenis !== 'function') return;
  if (reduced) return;

  var lenis = new window.Lenis({
    /* Коэффициент сглаживания: чем меньше, тем длиннее выбег. 0.085 даёт
       заметную плавность, но страница ещё слушается колеса, а не «плывёт». */
    lerp: 0.085,
    smoothWheel: true,
    /* Тач оставляем родной операционной системе. */
    syncTouch: false,
    wheelMultiplier: 1,
    autoRaf: true
  });

  window.lenis = lenis;

  /* Ссылки-якоря в меню: у Lenis своя позиция прокрутки, поэтому нативный
     переход по #id дал бы мгновенный прыжок мимо сглаживания. */
  document.addEventListener('click', function (event) {
    var link = event.target.closest('a[href^="#"]');
    if (!link) return;

    var id = link.getAttribute('href');
    if (!id || id === '#') return;

    var target = document.querySelector(id);
    if (!target) return;

    event.preventDefault();

    /* Если шапка залипающая — не заводим цель под неё. */
    var header = document.querySelector('.site-header');
    var offset = 0;
    if (header) {
      var pos = window.getComputedStyle(header).position;
      if (pos === 'fixed' || pos === 'sticky') {
        offset = -header.getBoundingClientRect().height;
      }
    }

    lenis.scrollTo(target, { offset: offset });

    /* Адрес в строке браузера обновляем сами: preventDefault его отменил. */
    if (window.history && window.history.pushState) {
      window.history.pushState(null, '', id);
    }
  });
})();
