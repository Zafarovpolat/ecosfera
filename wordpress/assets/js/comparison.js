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

  /* Веер теперь на всех ширинах — режим слайдера отключён. */

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

  var movingTimer = null;

  function step(direction) {
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
