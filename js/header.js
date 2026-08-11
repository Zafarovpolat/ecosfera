/* ==========================================================================
   Мобильное меню шапки (≤1024).

   Бургер переключает класс .header__nav--open на drawer. Анимация — на CSS
   (opacity/visibility/transform), скрипт только ставит/снимает класс и держит
   в актуальном состоянии aria-атрибуты. Меню закрывается по клику на любую
   ссылку/кнопку внутри него, по клику вне шапки, по Esc и при переходе на
   десктоп.
   ========================================================================== */

(function () {
  'use strict';

  var header = document.querySelector('.header');
  if (!header) return;

  var burger = header.querySelector('.header__burger');
  var nav = header.querySelector('.header__nav');
  if (!burger || !nav) return;

  var label = burger.querySelector('.header__burger-label');

  function isOpen() {
    return nav.classList.contains('header__nav--open');
  }

  function setOpen(open) {
    nav.classList.toggle('header__nav--open', open);
    burger.setAttribute('aria-expanded', String(open));
    burger.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
    /* Подпись под иконкой меняется вместе с крестиком. */
    if (label) label.textContent = open ? 'Закрыть' : 'Меню';
  }

  /* Клик по бургеру — тумблер. */
  burger.addEventListener('click', function () {
    setOpen(!isOpen());
  });

  /* Клик по ссылке или кнопке внутри меню — закрываем (переход к секции). */
  nav.addEventListener('click', function (event) {
    if (event.target.closest('a, button')) {
      setOpen(false);
    }
  });

  /* Клик вне шапки — закрываем. */
  document.addEventListener('click', function (event) {
    if (isOpen() && !header.contains(event.target)) {
      setOpen(false);
    }
  });

  /* Esc — закрываем и возвращаем фокус на бургер. */
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && isOpen()) {
      setOpen(false);
      burger.focus();
    }
  });

  /* Переход на десктоп (≥1024): сбрасываем состояние, чтобы drawer не остался
     «открытым» в статичной навигации. */
  var desktop = window.matchMedia('(min-width: 64em)');
  function onBreakpoint() {
    if (desktop.matches) setOpen(false);
  }
  if (desktop.addEventListener) {
    desktop.addEventListener('change', onBreakpoint);
  } else if (desktop.addListener) {
    desktop.addListener(onBreakpoint);
  }
})();
