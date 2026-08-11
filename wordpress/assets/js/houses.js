/* Галереи секции «Наши дома»: переключение слайдов стрелками и точками. */
(function () {
  var galleries = document.querySelectorAll('.houses__gallery');

  galleries.forEach(function (gallery) {
    var slides = Array.prototype.slice.call(gallery.querySelectorAll('.houses__slide'));
    var dots = Array.prototype.slice.call(gallery.querySelectorAll('.houses__gallery-dot'));
    var prev = gallery.querySelector('.houses__gallery-arrow-btn--prev');
    var next = gallery.querySelector('.houses__gallery-arrow-btn--next');
    var live = gallery.querySelector('.houses__gallery-live');
    var index = 0;

    if (!slides.length) return;

    function show(i) {
      index = (i + slides.length) % slides.length;

      slides.forEach(function (slide, n) {
        if (n === index) {
          slide.setAttribute('data-active', '');
          slide.removeAttribute('aria-hidden');
        } else {
          slide.removeAttribute('data-active');
          slide.setAttribute('aria-hidden', 'true');
        }
      });

      dots.forEach(function (dot, n) {
        if (n === index) {
          dot.setAttribute('data-active', '');
          dot.setAttribute('aria-selected', 'true');
          dot.setAttribute('aria-current', 'true');
        } else {
          dot.removeAttribute('data-active');
          dot.setAttribute('aria-selected', 'false');
          dot.removeAttribute('aria-current');
        }
      });

      if (live) live.textContent = 'Слайд ' + (index + 1) + ' из ' + slides.length;
    }

    if (prev) prev.addEventListener('click', function () { show(index - 1); });
    if (next) next.addEventListener('click', function () { show(index + 1); });

    dots.forEach(function (dot, n) {
      dot.addEventListener('click', function () { show(n); });
    });
  });
})();
