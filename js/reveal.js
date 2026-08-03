/**
 * Обводка карточек преимуществ: построение пути и запуск отрисовки.
 *
 * Путь строится по замеренным размерам карточки и начинается в ВЕРХНЕЙ
 * СЕРЕДИНЕ, откуда идёт по часовой стрелке. У второй карточки он
 * обрывается в нижней середине — это правая половина контура.
 *
 * Так сделано потому, что стандартный <rect> начинает путь у левого
 * верхнего угла: линия поехала бы не от верхней середины, и «часовой»
 * обход читался бы неверно.
 *
 * Анимацию запускает IntersectionObserver — секция лежит ниже первого
 * экрана, и анимация «на загрузку» отыграла бы до того, как до неё
 * доскроллят.
 *
 * Без скрипта путь пустой: CSS показывает статический контур через
 * ::before, то есть конечное состояние. Деградация безопасная.
 */
(function () {
  'use strict';

  var section = document.querySelector('.advantages');
  if (!section) return;

  var reduced =
    window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /** Скруглённый прямоугольник от верхней середины по часовой стрелке. */
  function buildPath(w, h, r, half) {
    r = Math.min(r, w / 2, h / 2);
    var d = [
      'M' + w / 2 + ' 0',
      'H' + (w - r),
      'A' + r + ' ' + r + ' 0 0 1 ' + w + ' ' + r,
      'V' + (h - r),
      'A' + r + ' ' + r + ' 0 0 1 ' + (w - r) + ' ' + h,
    ];
    if (half) {
      // Правая половина: останавливаемся в нижней середине.
      d.push('H' + w / 2);
    } else {
      d.push('H' + r);
      d.push('A' + r + ' ' + r + ' 0 0 1 0 ' + (h - r));
      d.push('V' + r);
      d.push('A' + r + ' ' + r + ' 0 0 1 ' + r + ' 0');
      d.push('Z');
    }
    return d.join(' ');
  }

  function layout() {
    var cards = section.querySelectorAll('.advantages__card');
    var drawn = 0;

    Array.prototype.forEach.call(cards, function (card, index) {
      var path = card.querySelector('.advantages__outline-path');
      if (!path) return;

      var box = card.getBoundingClientRect();
      if (!box.width || !box.height) return;

      var radius = parseFloat(getComputedStyle(card).borderTopLeftRadius) || 0;
      var stroke = 2;
      // Полтолщины внутрь с каждой стороны, чтобы линия не срезалась краем.
      var w = box.width - stroke;
      var h = box.height - stroke;

      var svg = card.querySelector('.advantages__outline');
      svg.setAttribute('viewBox', '0 0 ' + box.width + ' ' + box.height);
      path.setAttribute(
        'transform',
        'translate(' + stroke / 2 + ' ' + stroke / 2 + ')'
      );
      // index 1 — вторая карточка, у неё половина контура.
      path.setAttribute('d', buildPath(w, h, radius - stroke / 2, index === 1));

      var len = path.getTotalLength();
      path.style.setProperty('--len', len);
      drawn++;
    });

    if (drawn) section.classList.add('advantages--svg');
  }

  layout();

  var resizeTimer;
  window.addEventListener(
    'resize',
    function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(layout, 150);
    },
    { passive: true }
  );

  if (reduced || !('IntersectionObserver' in window)) {
    section.classList.add('advantages--animate');
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('advantages--animate');
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.25 }
  );

  observer.observe(section);
})();
