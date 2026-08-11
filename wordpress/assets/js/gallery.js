/* ==========================================================================
   Бегущие ряды галереи «Живые фото без фильтров».

   Почему не CSS-анимацией. В CSS сдвиг задавался как translate(-50%), то есть
   в процентах от ширины самой дорожки. Скорость при этом = ширина / время, и
   любое отклонение ширины (не применилось правило размера фото, другой
   зазор, другая плотность экрана) меняет её пропорционально. Плюс на слабой
   машине пропущенные кадры браузер догоняет большими скачками, и это читается
   как «слишком быстро».

   Здесь скорость задана прямо в пикселях в секунду, а смещение считается от
   реально прошедшего времени. Поэтому:
     • скорость одинакова на любой машине и при любой ширине дорожки;
     • пропущенные кадры не ускоряют ход — шаг всегда равен dt × скорости;
     • за кадром ряды стоят и не тратят ресурсы.

   Без JS остаётся CSS-анимация как запасной вариант.
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.photo-gallery');
  if (!section || !window.requestAnimationFrame) return;

  var rows = section.querySelectorAll('.photo-gallery__row');
  if (!rows.length) return;

  /* Скорость хода, пикселей в секунду. Одна фотография (467px) проходит
     мимо примерно за 467 / SPEED секунд. */
  var SPEED = 14;

  var reduced = window.matchMedia
    ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
    : false;
  if (reduced) return;

  /* JS взял движение на себя — CSS-анимацию нужно погасить, иначе она
     перебивает инлайновый transform. */
  section.classList.add('photo-gallery--js');

  var visible = true;
  if (window.IntersectionObserver) {
    new IntersectionObserver(function (entries) {
      visible = entries[0].isIntersecting;
    }, { rootMargin: '200px' }).observe(section);
  }

  Array.prototype.forEach.call(rows, function (row) {
    var track = row.querySelector('.photo-gallery__track');
    if (!track) return;

    /* Верхний ряд едет вправо, нижний влево. */
    var dir = row.classList.contains('photo-gallery__row--right') ? 1 : -1;

    var setWidth = 0;
    var offset = 0;
    var last = null;

    function measure() {
      /* В дорожке набор и его копия, значит один набор — это половина. */
      var full = track.scrollWidth;
      if (!full) return;
      setWidth = full / 2;
      /* Едущий вправо стартует сдвинутым на набор влево, чтобы ему было
         откуда выезжать. */
      offset = dir > 0 ? -setWidth : 0;
    }

    measure();
    window.addEventListener('resize', measure);

    function frame(now) {
      if (last === null) last = now;
      /* Клампим: после сворачивания вкладки пауза может быть в секундах,
         и без ограничения дорожка прыгнула бы разом. */
      var dt = Math.min((now - last) / 1000, 0.05);
      last = now;

      if (visible && setWidth > 0) {
        offset += dir * SPEED * dt;
        /* Петля: как только уехали на длину набора, возвращаемся — стык
           не виден, потому что за ним идёт его копия. */
        if (offset <= -setWidth) offset += setWidth;
        if (offset >= 0) offset -= setWidth;
        track.style.transform =
          'translate3d(' + offset.toFixed(2) + 'px, 0, 0)';
      }

      window.requestAnimationFrame(frame);
    }

    window.requestAnimationFrame(frame);
  });
})();
