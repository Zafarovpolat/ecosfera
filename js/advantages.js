/* ==========================================================================
   Слайдер «Наши преимущества» (пагинация под списком карточек).

   Работает по образцу nearby.js: при ≤1024px список карточек — это
   горизонтальный слайдер с scroll-snap. Скрипт двигает список на одну
   карточку вперёд/назад кнопками и обновляет счётчик «01/03».

   На десктопе (≥1024) список — ряд без скролла, пагинация скрыта в CSS
   (display: none), а canScroll() возвращает false, так что кнопки и счётчик
   остаются без эффекта даже если их трогать из консоли.

   Геометрия карточек берётся по getBoundingClientRect относительно списка,
   а не по offsetLeft: у списка есть margin-inline-end (calc(50% - 50vw)),
   и offsetParents у карточек и списка разные, что ломало бы расчёт.
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.advantages');
  if (!section) return;

  var list = section.querySelector('.advantages__card-list');
  if (!list) return;

  var cards = Array.prototype.slice.call(
    list.querySelectorAll('.advantages__card-item')
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


  function maxScroll() {
    return Math.max(0, list.scrollWidth - list.clientWidth);
  }

  /* Середина карточки в координатах содержимого списка. */
  function cardCenter(card) {
    return cardLeft(card) + card.getBoundingClientRect().width / 2;
  }

  /* Позиция скролла, при которой карточка встаёт в центр окна. */
  function centerTarget(i) {
    return Math.max(0, Math.min(
      cardCenter(cards[i]) - list.clientWidth / 2,
      maxScroll()
    ));
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
    list.scrollTo({
      left: centerTarget(i),
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

  /* При ручном скролле (свайп) следим за счётчиком и индексом: активным
     считается слайд, чья середина ближе всего к центру окна. */
  list.addEventListener('scroll', function () {
    var viewportCenter = list.scrollLeft + list.clientWidth / 2;
    var best = 0;
    var bestDist = Infinity;
    for (var i = 0; i < total; i++) {
      var d = Math.abs(cardCenter(cards[i]) - viewportCenter);
      if (d < bestDist) {
        bestDist = d;
        best = i;
      }
    }
    index = best;
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
