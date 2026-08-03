/* ==========================================================================
   Появление элементов при прокрутке.

   Разметку не размечаем вручную: скрипт проходит по секциям и сам ставит
   data-rv с направлением и задержкой. Поэтому новые блоки в partials
   подхватываются без правок здесь и в HTML.

   Направление подбирается по смыслу и по факту раскладки:
     • заголовки, текст, ряды однотипных карточек — снизу вверх;
     • боковые панели и медиа — со своей стороны экрана (левое едет слева,
       правое справа), сторона определяется по фактическому положению блока
       относительно центра секции, а не по списку классов;
     • крупное медиа по центру — лёгким приближением;
     • поля формы — по очереди снизу.

   ЧЕГО СКРИПТ НЕ ТРОГАЕТ И ПОЧЕМУ
   1. Элементы с занятым трансформом: веер карточек сравнения, дорожка
      бегущей ленты, дорожка слайдера отзывов, панели аккордеона, косая
      плашка в заголовках. Свой transform поверх чужого стёр бы его —
      в конце перехода мы ставим transform: none, и наклон плашки или сдвиг
      панели исчезли бы навсегда.
   2. Горизонтальные сдвиги у элементов, которые другие скрипты замеряют по
      ширине: по карточкам отзывов считается шаг слайдера, по карточкам
      преимуществ строится контур. Им достаётся только вертикальное
      движение — на ширину и на положение левого/правого края оно не влияет.
      По той же причине приближение (scale) внутри слайдеров не выдаём:
      оно меняет измеряемую ширину.
   3. Вложенные элементы, если предок уже появляется: ребёнок и так едет
      вместе с ним, две анимации подряд читаются как рывок.
   ========================================================================== */

(function () {
  'use strict';

  /* Системную просьбу «меньше движения» обрабатывает CSS: он убирает сдвиг,
     но оставляет проявление. Раньше скрипт при этой настройке выходил сразу,
     и на такой машине не было вообще никаких анимаций — а понять это со
     стороны невозможно, выглядит как «ничего не работает». */
  if (!('IntersectionObserver' in window)) return;

  /* Трансформ занят другой анимацией — не назначаем ничего. */
  var LOCKED = [
    '.comparison__card',
    '.comparison__photo-frame',
    '.photo-gallery__track',
    '.photo-gallery__item',
    '.reviews__card-list',
    '.faq__panel',
    '.section-heading__accent',
    '.booking__combo-list',
    '.booking__calendar',
    '.advantages__outline'
  ].join(',');

  /* Здесь допустимо только вертикальное движение: ширину этих элементов
     замеряют другие скрипты. */
  var VERTICAL_ONLY = [
    '.reviews__card-item',
    '.advantages__card'
  ].join(',');

  var STEP = 80; // мс между соседями в группе

  /* Насколько глубоко элемент должен зайти в кадр, чтобы начать появляться.
     Первая версия срабатывала у самой нижней кромки — переход успевал
     отыграть, пока элемент ещё выезжает из-за края, и движения не было
     видно вообще. Анимацию замечают только тогда, когда она начинается
     на уже видимом элементе. */
  var TRIGGER = 0.72;
  var targets = [];

  function mark(section, el, dir, delay) {
    if (!el || el.nodeType !== 1) return;
    if (el.hasAttribute('data-rv')) return;
    if (el.closest(LOCKED)) return;

    /* Предок уже размечен — ребёнок приедет вместе с ним. */
    if (el.parentElement && el.parentElement.closest('[data-rv]')) return;

    if ((dir !== 'up' && dir !== 'down' && dir !== 'fade') && el.closest(VERTICAL_ONLY)) {
      dir = 'up';
    }

    el.setAttribute('data-rv', dir);
    if (delay) el.style.setProperty('--rv-delay', delay + 'ms');
    targets.push(el);
  }

  function all(root, sel) {
    return root.querySelectorAll(sel);
  }

  /* Сторона появления по факту раскладки: блок слева от центра секции
     выезжает слева, справа — справа. Почти полноширинный блок сдвигать
     по горизонтали нельзя: он приедет из-за края и потянет прокрутку. */
  function side(section, el, fallback) {
    var sb = section.getBoundingClientRect();
    var b = el.getBoundingClientRect();
    if (!b.width || !sb.width) return fallback;

    if (b.width / sb.width > 0.7) return fallback;

    var offset = (b.left + b.width / 2 - (sb.left + sb.width / 2)) / sb.width;
    if (offset < -0.12) return 'left';
    if (offset > 0.12) return 'right';
    return fallback;
  }

  /* Что считаем самостоятельным элементом появления. */
  var CANDIDATE = [
    'h1', 'h2', 'h3', 'h4', 'p', 'li', 'dt', 'dd', 'address', 'blockquote',
    'figure', 'picture', 'img', 'table', 'button', 'a', 'label',
    '.section-heading',
    '[class*="__card"]', '[class*="__item"]', '[class*="__field"]',
    '[class*="__label"]', '[class*="__value"]', '[class*="__title"]',
    '[class*="__text"]', '[class*="__badge"]', '[class*="__stat"]',
    '[class*="__btn"]', '[class*="__cta"]'
  ].join(',');

  /* Порог «слишком большой блок». Колонка во весь экран, сдвинутая на 3rem,
     глазом не читается: двигается всё сразу, и движение теряется. Поэтому
     такие блоки не размечаем, а спускаемся внутрь — до карточек, заголовков,
     абзацев и кнопок. Именно на этом сломалась первая версия: она цепляла
     самый внешний подходящий блок и глушила всё вложенное, из-за чего на всю
     страницу набралось 22 элемента, а при пороге 0.6 — всего 32: разметка
     садилась на карточку-обёртку и глушила её содержимое. */
  var maxHeight = window.innerHeight * 0.36;

  /* Направление считается ОДИН раз на уровень — по положению родителя, а не
     каждому элементу отдельно. Иначе однотипные соседи разъезжались: одна
     ссылка в шапке уезжала влево, потому что оказалась левее центра, а
     остальные шли вверх. Одинаковые элементы должны появляться одинаково;
     разнообразие даёт раскладка — левая колонка слева, правая справа,
     полноширинный блок снизу. */
  function levelDirection(section, node, kids) {
    var media = 0;
    var count = 0;

    for (var i = 0; i < kids.length; i++) {
      if (kids[i].nodeType !== 1) continue;
      count++;
      if (/^(IMG|FIGURE|PICTURE)$/.test(kids[i].tagName)) media++;
    }

    /* Уровень целиком из картинок — приятнее приближением. */
    if (count && media === count && !node.closest(VERTICAL_ONLY)) return 'zoom';

    return side(section, node, 'up');
  }

  /* Спуск по дереву. Размечаем элемент, только если внутри него нет своего
     содержимого под анимацию: иначе карточка уезжала целиком, а её заголовок,
     текст и кнопка не появлялись по отдельности — именно из-за этого на всю
     страницу набиралось лишь три десятка блоков и движение было незаметно. */
  function walk(section, node) {
    var kids = node.children;
    if (!kids.length) return;

    var dir = levelDirection(section, node, kids);

    for (var i = 0; i < kids.length; i++) {
      var el = kids[i];

      if (el.closest(LOCKED)) continue;

      var box = el.getBoundingClientRect();
      var tall = box.height > maxHeight;
      var inner = el.querySelectorAll(CANDIDATE).length;

      if (el.matches(CANDIDATE) && !tall && inner < 2) {
        /* Задержка по позиции среди соседей, с потолком: у длинных списков
           хвост в две секунды выглядит как подвисание. */
        mark(section, el, dir, Math.min(i, 6) * STEP);
      } else {
        walk(section, el);
      }
    }
  }

  /* ВАЖНО: секции лежат внутри div.page-body, а не прямыми детьми <main>.
     Прежний отбор 'main > section' находил только герой — двенадцать секций
     между ним и подвалом не размечались вовсе, и на всю страницу набиралось
     четыре десятка элементов. Берём любые секции внутри main, но только внешние:
     вложенные (блок цен внутри карточки дома) обойдёт их родитель. */
  var sections = Array.prototype.filter.call(
    document.querySelectorAll('main section, .site-footer'),
    function (el) {
      return !(el.parentElement && el.parentElement.closest('section'));
    }
  );

  Array.prototype.forEach.call(sections, function (section) {
    /* Заголовок секции — опора, появляется без задержки. */
    Array.prototype.forEach.call(all(section, '.section-heading'), function (h) {
      mark(section, h, 'up', 0);
    });

    walk(section, section);
  });

  if (!targets.length) return;

  document.documentElement.classList.add('js-reveal');

  /* Диагностика охвата — только по запросу, в адресе ?debug=1. */
  if (window.location.search.indexOf('debug=1') !== -1) {
    document.title += ' [rv:' + targets.length + ']';
  }

  function reveal(el) {
    el.classList.add('is-in');
    observer.unobserve(el);
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) reveal(entry.target);
      });
    },
    /* threshold: 0 — достаточно любой кромки: доля видимости у однострочных
       элементов скачет и может не набраться никогда. Глубину входа задаёт
       отрицательный отступ снизу: элемент появляется, когда зашёл в кадр
       на 28% высоты кадра, а не когда задел нижний край. */
    { threshold: 0, rootMargin: '0px 0px -28% 0px' }
  );

  /* Наблюдатель подключается не сразу — см. intro() ниже. */
  function startObserving() {
    targets.forEach(function (el) {
      if (!el.classList.contains('is-in')) observer.observe(el);
    });
  }

  /* Страховка от невидимого контента — главный риск такой механики.
     У самого низа документа отрицательный отступ съедает кромку кадра, и
     последняя строка (копирайт, подпись студии) может не поймать
     пересечение: докручивать дальше уже некуда. Поэтому на прокрутке ещё
     раз проверяем ожидающие элементы по их фактическому положению.
     Когда показаны все — слушатели снимаются, лишней работы на прокрутке
     не остаётся. */
  var pending = targets.slice();
  var frame = null;

  /* Докрутили до конца документа — глубже элемент уже не зайдёт, показываем
     принудительно. Иначе последняя строка (копирайт, подпись студии) осталась
     бы невидимой навсегда. */
  function atDocumentEnd() {
    return (
      window.innerHeight + window.scrollY >=
      document.documentElement.scrollHeight - 4
    );
  }

  function sweep() {
    frame = null;
    var h = window.innerHeight;
    var end = atDocumentEnd();

    pending = pending.filter(function (el) {
      if (el.classList.contains('is-in')) return false;
      var b = el.getBoundingClientRect();
      if (b.bottom > 0 && (b.top < h * TRIGGER || end)) {
        reveal(el);
        return false;
      }
      return true;
    });

    if (!pending.length) {
      window.removeEventListener('scroll', onScroll);
      window.removeEventListener('resize', onScroll);
    }
  }

  function onScroll() {
    if (!frame) frame = window.requestAnimationFrame(sweep);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener(
    'resize',
    function () {
      maxHeight = window.innerHeight * 0.36;
      onScroll();
    },
    { passive: true }
  );

  /* Первый экран — отдельный случай. Шапка, заголовок героя, подпись, кнопка
     и цифры уже на экране в момент открытия: наблюдателю нечего ждать, и
     раньше они просто возникали без движения. Двух кадров паузы для перехода
     недостаточно — браузер успевает слить скрытое и конечное состояние в один
     пересчёт. Поэтому ждём реальную паузу и разводим элементы по времени,
     чтобы первый экран собирался каскадом сверху вниз. */
  function intro() {
    var h = window.innerHeight;

    var visible = targets.filter(function (el) {
      if (el.classList.contains('is-in')) return false;
      var b = el.getBoundingClientRect();
      return b.top < h * 0.95 && b.bottom > 0;
    });

    /* Порядок каскада — сверху вниз по экрану, а не по порядку в разметке:
       шапка лежит внутри секции героя, и без сортировки цифры внизу могли
       появиться раньше заголовка. */
    visible.sort(function (a, b) {
      return a.getBoundingClientRect().top - b.getBoundingClientRect().top;
    });

    visible.forEach(function (el, i) {
      el.style.setProperty('--rv-delay', Math.min(i, 12) * 90 + 'ms');
      reveal(el);
    });

    pending = pending.filter(function (el) {
      return !el.classList.contains('is-in');
    });
  }

  /* Старт — после полной загрузки, как и просили: пока грузятся картинки,
     страница скрыта, и каскад не начинается вхолостую. Если загрузка
     затягивается, всё равно стартуем через 2.5 секунды — держать посетителя
     перед пустым экраном дольше нельзя. */
  var booted = false;

  function boot() {
    if (booted) return;
    booted = true;

    /* Сначала проявляем страницу: размеченные элементы всё ещё скрыты своим
       data-rv, поэтому видно только фон и неразмеченное окружение. */
    document.documentElement.classList.add('rv-ready');

    /* Небольшая пауза, чтобы проявление страницы не смешивалось с каскадом. */
    window.setTimeout(function () {
      intro();
      /* Только теперь отдаём остальное наблюдателю. Если подключить его сразу,
         он выдаёт первый ответ уже в первом кадре — до того как браузер
         отрисовал скрытое состояние, — и снимает весь первый экран мгновенно,
         без перехода. */
      startObserving();
    }, 120);
  }

  if (document.readyState === 'complete') boot();
  else window.addEventListener('load', boot);

  window.setTimeout(boot, 2500);

})();
