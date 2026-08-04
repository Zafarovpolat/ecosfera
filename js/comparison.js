/* ==========================================================================
   Слайдер «Чем ЭКОСФЕРА отличается от других».

   Веер из пяти карточек. Позиция каждой задаётся атрибутом data-pos (1..5),
   а вся геометрия — размер, наклон, сдвиг, порядок наложения — приходит из
   CSS по этому атрибуту. Скрипт только пересчитывает номера позиций: DOM
   не переупорядочивается, поэтому браузер плавно анимирует переезд.

   Позиция 3 — центр. При шаге вперёд карточка справа от центра становится
   центральной, набор идёт по кругу.
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.comparison');
  if (!section) return;

  var list = section.querySelector('.comparison__card-list');
  if (!list) return;

  var cards = Array.prototype.slice.call(
    list.querySelectorAll('.comparison__card')
  );
  var total = cards.length;
  if (total < 3) return;

  var counter = section.querySelector('.comparison__pagination-current');
  var btnPrev = section.querySelector('.comparison__nav-btn--prev');
  var btnNext = section.querySelector('.comparison__nav-btn--next');

  var CENTER = 3; // номер центральной позиции в веере
  var shift = 0; // сдвиг набора по кругу

  /* На ширинах с обычным слайдером (≤1024) кнопки и стрелки листают
     горизонтальный список карточек, а не вращают веер. */
  var small = window.matchMedia('(max-width: 63.9375em)');

  function cardStep() {
    if (cards.length < 2) return 0;
    var second = cards[1].getBoundingClientRect().left;
    var first = cards[0].getBoundingClientRect().left;
    return second - first;
  }

  function scrollList(direction) {
    list.scrollBy({ left: direction * cardStep(), behavior: 'smooth' });
  }

  function mod(n, m) {
    return ((n % m) + m) % m;
  }

  function render() {
    var centerIndex = 0;

    cards.forEach(function (card, i) {
      var pos = mod(i + shift, total) + 1;
      card.dataset.pos = String(pos);
      if (pos === CENTER) centerIndex = i;
      /* Центральная карточка — основная для чтения; остальные остаются в
         потоке для скринридера, но помечаются как второстепенные. */
      if (pos === CENTER) {
        card.removeAttribute('data-secondary');
      } else {
        card.setAttribute('data-secondary', 'true');
      }
    });

    if (counter) {
      counter.textContent = String(centerIndex + 1).padStart(2, '0');
    }
  }

  /* На слайдере счётчик показывает номер листаемой карточки. */
  if (small.matches) {
    list.addEventListener('scroll', function () {
      var step = cardStep() || 1;
      var index = Math.round(list.scrollLeft / step);
      if (counter) {
        counter.textContent = String(Math.min(Math.max(index % total, 0), total - 1) + 1).padStart(2, '0');
      }
    }, { passive: true });
  }

  var movingTimer = null;

  function step(direction) {
    /* Обычный слайдер (≤1024): листаем список. */
    if (small.matches) {
      scrollList(direction);
      return;
    }

    shift -= direction; // вперёд: следующая справа уезжает в центр
    render();

    /* На время переезда снимаем размытие с карточек — см. комментарий в
       08-comparison.css. Длительность чуть больше перехода (0.55s). */
    list.classList.add('is-moving');
    if (movingTimer) window.clearTimeout(movingTimer);
    movingTimer = window.setTimeout(function () {
      list.classList.remove('is-moving');
    }, 620);
  }

  if (btnNext) {
    btnNext.addEventListener('click', function () {
      step(1);
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', function () {
      step(-1);
    });
  }

  /* Стрелки с клавиатуры, когда фокус внутри секции. */
  section.addEventListener('keydown', function (event) {
    if (event.key === 'ArrowRight') {
      step(1);
      event.preventDefault();
    } else if (event.key === 'ArrowLeft') {
      step(-1);
      event.preventDefault();
    }
  });

  render();
})();
