/* ==========================================================================
   Слайдер «Подходит для любого повода» (пагинация под списком карточек).

   ≤1024: список карточек — горизонтальный скролл (overflow-x: auto со
   scroll-snap). Карточки уже вьюпорта, поэтому в одно окно попадает несколько
   штук. Пагинация считает не «центр экрана», а точки остановки слайдера:
   левый край каждой карточки, ограниченный концом прокрутки. Совпадающие
   точки в конце (когда несколько последних карточек упираются в один предел)
   схлопываются — иначе счётчик показывал бы несуществующие страницы и прыгал
   через слайд.

   На десктопе (≥1024) список — сетка без скролла, пагинация скрыта
   (display: none в CSS).
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.audience');
  if (!section) return;

  var list = section.querySelector('.audience__grid');
  if (!list) return;

  var cards = Array.prototype.slice.call(
    list.querySelectorAll('.audience__item')
  );
  if (!cards.length) return;

  var curEl = section.querySelector('.comparison__pagination-current');
  var totEl = section.querySelector('.comparison__pagination-total');
  var btnPrev = section.querySelector('.comparison__nav-btn--prev');
  var btnNext = section.querySelector('.comparison__nav-btn--next');

  var stops = [0]; // позиции scrollLeft для каждой страницы
  var index = 0;

  function pad(n) {
    return n < 10 ? '0' + n : String(n);
  }

  /* Левый край карточки в координатах содержимого списка. rect.left уже учёл
     прокрутку, поэтому возвращаем scrollLeft обратно. */
  function cardLeft(card) {
    var lr = list.getBoundingClientRect();
    return card.getBoundingClientRect().left - lr.left + list.scrollLeft;
  }

  function maxScroll() {
    return Math.max(0, list.scrollWidth - list.clientWidth);
  }

  /* Точки остановки: левый край каждой карточки, но не дальше конца прокрутки.
     Одинаковые (в пределах 1px) схлопываем. */
  function computeStops() {
    var max = maxScroll();
    stops = [];
    cards.forEach(function (c) {
      var p = Math.min(cardLeft(c), max);
      if (!stops.length || p - stops[stops.length - 1] > 1) {
        stops.push(p);
      }
    });
    if (!stops.length) stops = [0];
  }

  function nearestStop(x) {
    var best = 0;
    var bestD = Infinity;
    for (var i = 0; i < stops.length; i++) {
      var d = Math.abs(stops[i] - x);
      if (d < bestD) {
        bestD = d;
        best = i;
      }
    }
    return best;
  }

  function render() {
    if (curEl) curEl.textContent = pad(index + 1);
    if (totEl) totEl.textContent = '/' + pad(stops.length);
  }

  function goTo(i) {
    index = Math.max(0, Math.min(stops.length - 1, i));
    list.scrollTo({ left: stops[index], behavior: 'smooth' });
    render();
  }

  /* Пересчёт после смены размеров/загрузки картинок: геометрия карточек, а с
     ней и число страниц, зависит от ширины. */
  function refresh() {
    computeStops();
    index = nearestStop(list.scrollLeft);
    render();
  }

  if (btnNext) {
    btnNext.addEventListener('click', function () {
      goTo(index + 1);
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', function () {
      goTo(index - 1);
    });
  }

  /* Ручной скролл (свайп): подсвечиваем ближайшую точку остановки. rAF, чтобы
     не считать на каждом событии скролла. */
  var raf = null;
  list.addEventListener(
    'scroll',
    function () {
      if (raf) return;
      raf = requestAnimationFrame(function () {
        raf = null;
        index = nearestStop(list.scrollLeft);
        render();
      });
    },
    { passive: true }
  );

  window.addEventListener('resize', refresh);
  window.addEventListener('load', refresh);

  refresh();
})();
