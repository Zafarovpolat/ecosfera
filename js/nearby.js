/* ==========================================================================
   Слайдер «Уезжать далеко — не нужно» (пагинация под списком карточек).

   Список карточек — горизонтальный скролл (≤1279px, overflow-x: auto со
   scroll-snap). Скрипт двигает список на одну карточку вперёд/назад и
   обновляет счётчик «01/03».

   Геометрия карточек берётся по getBoundingClientRect относительно списка,
   а не по offsetLeft: у списка есть margin-inline-end (calc(50% - 50vw)),
   и offsetParents у карточек и списка разные, что ломало расчёт.

   На десктопе (≥1024) список — сетка без скролла, пагинация скрыта
   (display: none в CSS), кнопок можно не трогать.
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.nearby');
  if (!section) return;

  var list = section.querySelector('.nearby__list');
  if (!list) return;

  var cards = Array.prototype.slice.call(
    list.querySelectorAll('.nearby__card')
  );
  var total = cards.length;
  if (!total) return;

  var counter = section.querySelector('.comparison__pagination-current');
  var btnPrev = section.querySelector('.comparison__nav-btn--prev');
  var btnNext = section.querySelector('.comparison__nav-btn--next');

  var index = 0;

  /* Левая граница карточки в координатах содержимого списка (позиция, куда
     надо скроллить, чтобы карточка встала к левому краю окна). rect.left
     уже учёл прокрутку, поэтому добавляем текущий scrollLeft обратно. */
  function cardLeft(card) {
    var listRect = list.getBoundingClientRect();
    return card.getBoundingClientRect().left - listRect.left + list.scrollLeft;
  }

  /* Шаг листания: расстояние между левыми краями соседних карточек. */
  function step() {
    if (total < 2) return 0;
    return cardLeft(cards[1]) - cardLeft(cards[0]);
  }

  function maxScroll() {
    return Math.max(0, list.scrollWidth - list.clientWidth);
  }

  function pad(n) {
    return n < 10 ? '0' + n : String(n);
  }

  function render() {
    if (counter) {
      counter.textContent = pad(index + 1);
    }
  }

  function scrollToCard(i) {
    var target = Math.min(cardLeft(cards[i]), maxScroll());
    list.scrollTo({
      left: target,
      behavior: 'smooth'
    });
  }

  function stepBy(direction) {
    if (!canScroll()) return;
    index = Math.max(0, Math.min(total - 1, index + direction));
    render();
    scrollToCard(index);
  }

  function canScroll() {
    return list.scrollWidth > list.clientWidth + 1;
  }

  if (btnNext) {
    btnNext.addEventListener('click', function () {
      stepBy(1);
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', function () {
      stepBy(-1);
    });
  }

  /* При ручном скролле (свайп) следим за счётчиком и индексом. */
  list.addEventListener('scroll', function () {
    var by = step();
    var center = list.scrollLeft + list.clientWidth / 2;
    var i = Math.round(center / by - 0.5);
    index = Math.max(0, Math.min(total - 1, i));
    render();
  });

  window.addEventListener('resize', function () {
    index = Math.min(index, total - 1);
    render();
  });

  window.addEventListener('load', function () {
    index = 0;
    render();
  });

  render();
})();