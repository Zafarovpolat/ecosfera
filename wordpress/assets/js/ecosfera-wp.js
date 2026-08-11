/* Мост WordPress: AJAX-бронь, превыбор дома, скролл героя. */
(function () {
  'use strict';

  var cfg = window.ecosferaWp || {};

  document.querySelectorAll('.houses__book-btn[data-house]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var slug = btn.getAttribute('data-house');
      var select = document.getElementById('booking-house');
      if (!select || !slug) return;
      select.value = slug;
      select.dispatchEvent(new Event('change', { bubbles: true }));
      var combo = select.closest('.booking__select-wrapper');
      combo = combo ? combo.querySelector('.booking__combo') : null;
      if (combo) {
        var opt = select.options[select.selectedIndex];
        combo.textContent = opt ? opt.textContent.trim() : slug;
        combo.dataset.placeholder = 'false';
      }
    });
  });

  var form = document.querySelector('.booking__form');
  if (!form || !cfg.ajaxUrl) return;

  form.addEventListener('submit', function (event) {
    if (form.dataset.ecosferaSending === '1') {
      event.preventDefault();
      return;
    }

    var invalid = form.querySelector('[aria-invalid="true"]');
    if (invalid) return;

    var house = form.querySelector('[name="house"]');
    var checkIn = form.querySelector('[name="checkIn"]');
    var checkOut = form.querySelector('[name="checkOut"]');
    var guests = form.querySelector('[name="guests"]');
    var name = form.querySelector('[name="name"]');
    var phone = form.querySelector('[name="phone"]');
    var consent = form.querySelector('[name="consent"]');

    if (!house || !house.value || !checkIn || !checkIn.value || !checkOut || !checkOut.value) return;
    if (!guests || !guests.value || !name || name.value.trim().length < 2) return;
    if (!phone || phone.value.replace(/\D/g, '').length < 11) return;
    if (consent && !consent.checked) return;

    event.preventDefault();
    form.dataset.ecosferaSending = '1';

    var btn = form.querySelector('.booking__submit-btn');
    var label = form.querySelector('.booking__btn-text');
    var prev = label ? label.textContent : '';
    if (label) label.textContent = cfg.sending || '…';
    if (btn) btn.disabled = true;

    var data = new FormData(form);
    data.set('action', 'ecosfera_booking');

    fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        var ok = json && json.success;
        showNotice(ok, (json && json.data && json.data.message) || (ok ? cfg.success : cfg.error));
        if (ok) form.reset();
      })
      .catch(function () {
        showNotice(false, cfg.error);
      })
      .finally(function () {
        form.dataset.ecosferaSending = '0';
        if (label) label.textContent = prev;
        if (btn) btn.disabled = false;
      });
  }, true);

  function showNotice(ok, text) {
    var panel = document.querySelector('.booking__form-panel');
    if (!panel) return;
    var old = panel.querySelector('.booking__notice');
    if (old) old.remove();
    var p = document.createElement('p');
    p.className = 'booking__notice booking__notice--' + (ok ? 'ok' : 'err');
    p.setAttribute('role', ok ? 'status' : 'alert');
    p.textContent = text;
    var title = panel.querySelector('.booking__form-title');
    if (title && title.nextSibling) panel.insertBefore(p, title.nextSibling);
    else panel.insertBefore(p, panel.firstChild);
  }
})();
