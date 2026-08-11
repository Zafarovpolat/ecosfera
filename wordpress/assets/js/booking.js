/* ==========================================================================
   Форма бронирования: кастомные выпадающие списки, кастомный календарь,
   маска телефона и валидация.

   Нативные <select> и <input> остаются в разметке — они и отправляют данные,
   и держат доступность. Кастомный вид рисуется поверх, значения синхронно
   пишутся в нативное поле, поэтому форма работает и без JS (тогда виден
   обычный select и текстовое поле даты).
   ========================================================================== */

(function () {
  'use strict';

  var form = document.querySelector('.booking__form');
  if (!form) return;

  var MONTHS = [
    'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
  ];
  var WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

  /* ── Общее ─────────────────────────────────────────────────────────────── */

  function fieldOf(el) {
    return el.closest('.booking__field');
  }

  /* Где показывать ошибку. По UI KIT у телефона она отдельной строкой внутри
     коробки под значением — там уже есть введённые цифры. У остальных полей
     значения нет, поэтому текст ошибки встаёт на место плейсхолдера красным:
     отдельная строка снизу там только дублировала бы пустое поле. */
  function errorMode(el) {
    return el.type === 'tel' ? 'row' : 'placeholder';
  }

  function setError(el, message) {
    var field = fieldOf(el);
    if (!field) return;
    var box = field.querySelector('.booking__field-error');
    var combo = field.querySelector('.booking__combo');

    if (message) {
      field.classList.add('booking__field--invalid');
      el.setAttribute('aria-invalid', 'true');

      if (errorMode(el) === 'placeholder') {
        if (combo) {
          if (!combo.dataset.idleText) combo.dataset.idleText = combo.textContent;
          combo.textContent = message;
        } else {
          if (el.dataset.idlePlaceholder === undefined) {
            el.dataset.idlePlaceholder = el.placeholder || '';
          }
          el.placeholder = message;
        }
        if (box) { box.textContent = ''; box.hidden = true; }
        /* Сообщение всё равно должно дойти до скринридера. */
        field.setAttribute('data-error-text', message);
      } else if (box) {
        box.textContent = message;
        box.hidden = false;
      }
    } else {
      field.classList.remove('booking__field--invalid');
      field.removeAttribute('data-error-text');
      el.setAttribute('aria-invalid', 'false');

      if (combo && combo.dataset.idleText) {
        combo.textContent = combo.dataset.idleText;
        delete combo.dataset.idleText;
      }
      if (el.dataset.idlePlaceholder !== undefined) {
        el.placeholder = el.dataset.idlePlaceholder;
        delete el.dataset.idlePlaceholder;
      }
      if (box) { box.textContent = ''; box.hidden = true; }
    }
  }

  function closeAllPopups(except) {
    form.querySelectorAll('[data-popup-open="true"]').forEach(function (node) {
      if (node !== except) {
        node.dataset.popupOpen = 'false';
        var trigger = node.previousElementSibling;
        if (trigger && trigger.hasAttribute('aria-expanded')) {
          trigger.setAttribute('aria-expanded', 'false');
        }
      }
    });
  }

  document.addEventListener('click', function (event) {
    if (!event.target.closest('.booking__field')) closeAllPopups(null);
  });

  /* ── Кастомный выпадающий список ───────────────────────────────────────── */

  form.querySelectorAll('.booking__select').forEach(function (select, index) {
    var wrapper = select.closest('.booking__select-wrapper');
    if (!wrapper) return;

    select.classList.add('booking__select--native');
    select.setAttribute('tabindex', '-1');
    select.setAttribute('aria-hidden', 'true');

    var listId = 'booking-combo-list-' + index;

    var trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'booking__combo';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.setAttribute('aria-controls', listId);
    var label = select.closest('.booking__field').querySelector('.booking__field-label');
    if (label && label.id) trigger.setAttribute('aria-labelledby', label.id);

    var list = document.createElement('ul');
    list.className = 'booking__combo-list';
    list.id = listId;
    list.setAttribute('role', 'listbox');
    list.dataset.popupOpen = 'false';

    var options = Array.prototype.slice.call(select.options);
    var current = select.selectedIndex;

    function paint() {
      var opt = select.options[select.selectedIndex];
      var isPlaceholder = !opt || opt.value === '';
      trigger.textContent = opt ? opt.textContent.trim() : '';
      trigger.dataset.placeholder = isPlaceholder ? 'true' : 'false';
      list.querySelectorAll('.booking__combo-option').forEach(function (li, i) {
        li.setAttribute('aria-selected', i === select.selectedIndex ? 'true' : 'false');
      });
    }

    options.forEach(function (opt, i) {
      var li = document.createElement('li');
      li.className = 'booking__combo-option';
      li.setAttribute('role', 'option');
      li.tabIndex = -1;
      li.textContent = opt.textContent.trim();
      li.addEventListener('click', function () {
        select.selectedIndex = i;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        setError(select, '');
        paint();
        close();
        trigger.focus();
      });
      list.appendChild(li);
    });

    function open() {
      closeAllPopups(list);
      list.dataset.popupOpen = 'true';
      trigger.setAttribute('aria-expanded', 'true');
      current = select.selectedIndex;
      focusOption(current);
    }

    function close() {
      list.dataset.popupOpen = 'false';
      trigger.setAttribute('aria-expanded', 'false');
    }

    function focusOption(i) {
      var items = list.querySelectorAll('.booking__combo-option');
      if (!items.length) return;
      current = Math.max(0, Math.min(i, items.length - 1));
      items.forEach(function (li) { li.classList.remove('is-current'); });
      items[current].classList.add('is-current');
      items[current].focus();
    }

    trigger.addEventListener('click', function () {
      if (list.dataset.popupOpen === 'true') close();
      else open();
    });

    trigger.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
        open();
        event.preventDefault();
      }
    });

    list.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowDown') { focusOption(current + 1); event.preventDefault(); }
      else if (event.key === 'ArrowUp') { focusOption(current - 1); event.preventDefault(); }
      else if (event.key === 'Home') { focusOption(0); event.preventDefault(); }
      else if (event.key === 'End') { focusOption(999); event.preventDefault(); }
      else if (event.key === 'Escape') { close(); trigger.focus(); event.preventDefault(); }
      else if (event.key === 'Enter' || event.key === ' ') {
        list.querySelectorAll('.booking__combo-option')[current].click();
        event.preventDefault();
      }
    });

    wrapper.insertBefore(trigger, wrapper.firstChild);
    wrapper.insertBefore(list, trigger.nextSibling);
    paint();
  });

  /* ── Кастомный календарь ───────────────────────────────────────────────── */

  function pad(n) { return n < 10 ? '0' + n : String(n); }

  function formatDate(d) {
    return pad(d.getDate()) + '.' + pad(d.getMonth() + 1) + '.' + d.getFullYear();
  }

  form.querySelectorAll('.booking__input[data-date]').forEach(function (input, index) {
    var wrapper = input.closest('.booking__input-wrapper');
    if (!wrapper) return;

    var popup = document.createElement('div');
    popup.className = 'booking__cal';
    popup.dataset.popupOpen = 'false';
    popup.setAttribute('role', 'dialog');
    popup.setAttribute('aria-label', 'Выбор даты');

    var view = new Date();
    view.setDate(1);

    function render() {
      popup.innerHTML = '';

      var head = document.createElement('div');
      head.className = 'booking__cal-head';

      var prev = document.createElement('button');
      prev.type = 'button';
      prev.className = 'booking__cal-nav';
      prev.setAttribute('aria-label', 'Предыдущий месяц');
      prev.textContent = '‹';
      prev.addEventListener('click', function (event) {
        /* render() пересобирает содержимое, и нажатая кнопка успевает покинуть
           DOM раньше, чем всплывший клик дойдёт до документа. Там проверка
           «клик вне поля» получала null и закрывала календарь. */
        event.stopPropagation();
        view.setMonth(view.getMonth() - 1);
        render();
      });

      var title = document.createElement('span');
      title.className = 'booking__cal-title';
      title.textContent = MONTHS[view.getMonth()] + ' ' + view.getFullYear();

      var next = document.createElement('button');
      next.type = 'button';
      next.className = 'booking__cal-nav';
      next.setAttribute('aria-label', 'Следующий месяц');
      next.textContent = '›';
      next.addEventListener('click', function (event) {
        event.stopPropagation();
        view.setMonth(view.getMonth() + 1);
        render();
      });

      head.appendChild(prev);
      head.appendChild(title);
      head.appendChild(next);
      popup.appendChild(head);

      var grid = document.createElement('div');
      grid.className = 'booking__cal-grid';

      WEEKDAYS.forEach(function (name) {
        var cell = document.createElement('span');
        cell.className = 'booking__cal-weekday';
        cell.textContent = name;
        grid.appendChild(cell);
      });

      var first = new Date(view.getFullYear(), view.getMonth(), 1);
      /* getDay(): 0 — воскресенье, а неделя начинается с понедельника */
      var lead = (first.getDay() + 6) % 7;
      for (var i = 0; i < lead; i += 1) {
        grid.appendChild(document.createElement('span'));
      }

      var daysInMonth = new Date(view.getFullYear(), view.getMonth() + 1, 0).getDate();
      var today = new Date();
      today.setHours(0, 0, 0, 0);

      for (var day = 1; day <= daysInMonth; day += 1) {
        var date = new Date(view.getFullYear(), view.getMonth(), day);
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'booking__cal-day';
        btn.textContent = String(day);
        if (date < today) btn.disabled = true;
        if (input.value === formatDate(date)) btn.classList.add('is-selected');
        btn.addEventListener('click', (function (picked) {
          return function () {
            input.value = formatDate(picked);
            setError(input, '');
            popup.dataset.popupOpen = 'false';
            input.focus();
          };
        })(date));
        grid.appendChild(btn);
      }

      popup.appendChild(grid);
    }

    function open() {
      closeAllPopups(popup);
      render();
      popup.dataset.popupOpen = 'true';
    }

    input.readOnly = true;
    input.addEventListener('click', open);
    input.addEventListener('focus', open);
    input.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') popup.dataset.popupOpen = 'false';
    });

    var icon = wrapper.querySelector('.booking__input-icon');
    if (icon) {
      icon.style.pointerEvents = 'auto';
      icon.style.cursor = 'pointer';
      icon.addEventListener('click', open);
    }

    wrapper.appendChild(popup);
  });

  /* ── Маска телефона ────────────────────────────────────────────────────── */

  var phone = form.querySelector('input[type="tel"], .booking__input[name="phone"]');
  if (phone) {
    phone.setAttribute('inputmode', 'tel');

    function maskPhone(raw) {
      var digits = raw.replace(/\D/g, '');
      if (digits[0] === '7' || digits[0] === '8') digits = digits.slice(1);
      digits = digits.slice(0, 10);
      var out = '+7';
      if (digits.length) out += ' (' + digits.slice(0, 3);
      if (digits.length >= 3) out += ')';
      if (digits.length > 3) out += ' ' + digits.slice(3, 6);
      if (digits.length > 6) out += '-' + digits.slice(6, 8);
      if (digits.length > 8) out += '-' + digits.slice(8, 10);
      return out;
    }

    phone.addEventListener('input', function () {
      phone.value = maskPhone(phone.value);
      if (phone.value.replace(/\D/g, '').length === 11) setError(phone, '');
    });

    phone.addEventListener('focus', function () {
      if (!phone.value) phone.value = '+7 ';
    });
  }

  /* ── Валидация ─────────────────────────────────────────────────────────── */

  function validate() {
    var firstBad = null;

    form.querySelectorAll('.booking__select').forEach(function (select) {
      if (!select.value) {
        setError(select, 'Выберите значение');
        if (!firstBad) firstBad = select;
      } else setError(select, '');
    });

    form.querySelectorAll('.booking__input[data-date]').forEach(function (input) {
      if (!input.value) {
        setError(input, 'Укажите дату');
        if (!firstBad) firstBad = input;
      } else setError(input, '');
    });

    var name = form.querySelector('.booking__input[name="name"]');
    if (name) {
      if (name.value.trim().length < 2) {
        setError(name, 'Укажите имя');
        if (!firstBad) firstBad = name;
      } else setError(name, '');
    }

    if (phone) {
      if (phone.value.replace(/\D/g, '').length !== 11) {
        setError(phone, 'Введите корректный номер');
        if (!firstBad) firstBad = phone;
      } else setError(phone, '');
    }

    var consent = form.querySelector('.booking__checkbox');
    var consentError = form.querySelector('.booking__consent-error');
    if (consent) {
      if (!consent.checked) {
        consent.setAttribute('aria-invalid', 'true');
        if (consentError) {
          consentError.textContent = 'Нужно согласие на обработку данных';
          consentError.hidden = false;
        }
        if (!firstBad) firstBad = consent;
      } else {
        consent.setAttribute('aria-invalid', 'false');
        if (consentError) {
          consentError.textContent = '';
          consentError.hidden = true;
        }
      }
    }

    return firstBad;
  }

  form.addEventListener('submit', function (event) {
    var bad = validate();
    if (bad) {
      event.preventDefault();
      var focusTarget = bad;
      if (bad.classList && bad.classList.contains('booking__select')) {
        var combo = bad.closest('.booking__select-wrapper').querySelector('.booking__combo');
        if (combo) focusTarget = combo;
      }
      focusTarget.focus();
    }
  });

  /* Снимаем ошибку, как только поле начали исправлять. */
  form.addEventListener('input', function (event) {
    var el = event.target;
    if (el.classList && (el.classList.contains('booking__input'))) {
      if (el.value.trim()) setError(el, '');
    }
  });
})();
