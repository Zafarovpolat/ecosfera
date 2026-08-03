/* ==========================================================================
   Аккордеон «Часто спрашивают».

   Раскрытие плавное, поэтому панель не прячется через hidden — display
   анимировать нельзя. Скрипт выставляет высоту в пикселях по фактической
   высоте содержимого, а CSS её анимирует. По окончании раскрытия высота
   сбрасывается в auto: иначе панель не подстроится, если текст переверстается
   при смене ширины окна.

   Без JS все панели остаются открытыми — ответы доступны, просто без
   свёртывания.
   ========================================================================== */

(function () {
  'use strict';

  var section = document.querySelector('.faq');
  if (!section) return;

  var toggles = section.querySelectorAll('.faq__toggle');
  if (!toggles.length) return;

  function panelOf(toggle) {
    var id = toggle.getAttribute('aria-controls');
    return id ? document.getElementById(id) : null;
  }

  function open(toggle) {
    var panel = panelOf(toggle);
    if (!panel) return;

    toggle.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
    panel.style.height = panel.scrollHeight + 'px';

    /* После перехода отпускаем высоту, чтобы содержимое могло меняться. */
    panel.addEventListener('transitionend', function done(event) {
      if (event.propertyName !== 'height') return;
      panel.removeEventListener('transitionend', done);
      if (toggle.getAttribute('aria-expanded') === 'true') {
        panel.style.height = 'auto';
      }
    });
  }

  function close(toggle) {
    var panel = panelOf(toggle);
    if (!panel) return;

    toggle.setAttribute('aria-expanded', 'false');
    /* Из auto анимировать нечего — сначала фиксируем текущую высоту. */
    panel.style.height = panel.scrollHeight + 'px';
    /* Принудительный пересчёт, чтобы браузер увидел стартовое значение. */
    void panel.offsetHeight;
    panel.style.height = '0px';
  }

  Array.prototype.forEach.call(toggles, function (toggle) {
    var panel = panelOf(toggle);
    if (!panel) return;

    var expanded = toggle.getAttribute('aria-expanded') === 'true';
    panel.hidden = false;
    panel.style.height = expanded ? 'auto' : '0px';

    toggle.addEventListener('click', function () {
      var isOpen = toggle.getAttribute('aria-expanded') === 'true';

      if (isOpen) {
        close(toggle);
        return;
      }

      /* Открыт всегда один пункт: так список остаётся обозримым. */
      Array.prototype.forEach.call(toggles, function (other) {
        if (other !== toggle && other.getAttribute('aria-expanded') === 'true') {
          close(other);
        }
      });

      open(toggle);
    });
  });
})();
