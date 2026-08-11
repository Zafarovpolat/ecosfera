/* ==========================================================================
   Карусель отзывов.

   Дорожка — обычный флекс-ряд карточек, окно ей задаёт .reviews__track-outer
   с overflow: hidden. Скрипт сдвигает дорожку трансформом на ширину шага,
   которую измеряет по факту (карточка + зазор), поэтому раскладка может
   меняться без правок в коде.

   Без JS видна первая карточка и стрелки ничего не делают — контент остаётся
   доступным, просто без прокрутки.
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.reviews');
  if (!section) return;

  var list = section.querySelector('.reviews__card-list');
  var outer = section.querySelector('.reviews__track-outer');
  if (!list || !outer) return;

  var items = list.querySelectorAll('.reviews__card-item');
  if (items.length < 2) return;

  var btnPrev = section.querySelector('.reviews__arrow-btn--prev');
  var btnNext = section.querySelector('.reviews__arrow-btn--next');
  var counter = section.querySelector('.reviews__counter');

  var index = 0;

  function step() {
    /* Шаг = ширина карточки плюс зазор между ними. Измеряем, а не хардкодим:
       на разных ширинах экрана карточка своя. */
    var first = items[0].getBoundingClientRect().width;
    var gap = 0;
    if (items.length > 1) {
      gap =
        items[1].getBoundingClientRect().left -
        items[0].getBoundingClientRect().right;
    }
    return first + Math.max(gap, 0);
  }

  function maxIndex() {
    /* Дальше не листаем, чем нужно, чтобы последняя карточка встала у правого
       края окна: иначе в конце появляется пустота. */
    var visible = outer.getBoundingClientRect().width;
    var perView = Math.max(1, Math.floor(visible / step()));
    return Math.max(0, items.length - perView);
  }

  function pad(n) {
    return n < 10 ? '0' + n : String(n);
  }

  function render() {
    var limit = maxIndex();
    if (index > limit) index = limit;
    if (index < 0) index = 0;

    list.style.transform =
      'translate3d(' + (-index * step()).toFixed(2) + 'px, 0, 0)';

    if (counter) {
      counter.innerHTML =
        '<span>' + pad(index + 1) + '</span>' +
        '<span class="reviews__counter-sep">/' + pad(items.length) + '</span>';
      counter.setAttribute(
        'aria-label',
        'Отзыв ' + (index + 1) + ' из ' + items.length
      );
    }

    if (btnPrev) btnPrev.disabled = index === 0;
    if (btnNext) btnNext.disabled = index >= limit;
  }

  if (btnNext) {
    btnNext.addEventListener('click', function () {
      index += 1;
      render();
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', function () {
      index -= 1;
      render();
    });
  }

  /* Стрелки с клавиатуры, когда фокус внутри секции. */
  section.addEventListener('keydown', function (event) {
    if (event.key === 'ArrowRight') {
      index += 1;
      render();
      event.preventDefault();
    } else if (event.key === 'ArrowLeft') {
      index -= 1;
      render();
      event.preventDefault();
    }
  });

  window.addEventListener('resize', render);
  render();
})();
