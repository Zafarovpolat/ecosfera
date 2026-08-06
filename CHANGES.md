# Внесённые правки (2026-08-07)

## 1. Плавный скролл по ссылкам «Услуги» и «Галерея»
- Добавлены `id="uslugi"` на секцию `.included` (блок услуг)
- Добавлен `id="gallery"` на секцию `.photo-gallery`

Теперь ссылки в хедере и в бургер-меню:
- `<a href="#uslugi">Услуги</a>` → плавно скроллит к `#uslugi`
- `<a href="#gallery">Галерея</a>` → плавно скроллит к `#gallery`

(Глобальный `scroll-behavior: smooth` уже был в `base.css`)

## 2. Ссылки в бургер-меню и хедере
Те же ссылки работают и из мобильного меню (бургер). При клике меню автоматически закрывается (логика уже была в `header.js`).

## 3. Замена 2-й и 3-й фотографии в слайдерах домов
В `.houses__gallery-track` обоих домов (Красный и Коричневый) заменены слайды №2 и №3:

**Было:**
- slide 2: `wide-shot-brown-house-surrounded-by-forest-spruc-*.webp`
- slide 3: `vertical-shot-cabin-forest-surrounded-by-lot-gre-*.webp`

**Стало:**
- slide 2: `rectangle-9-db187370c8.webp` (из photo-gallery)
- slide 3: `rectangle-10-096c83987c.webp` (из photo-gallery)

## 4. Исправлены все ссылки в футере
Теперь работают:
- «Услуги» → `#uslugi`
- «Галерея» → `#gallery`
- «Отзывы» → `#reviews`
- «Для кого» → `#audience`
- «Забронировать» → `#booking`

Добавлены недостающие `id`:
- `id="reviews"`
- `id="audience"`
- `id="booking"`

## 5. Социальные ссылки
- **Instagram** (в футере и в блоке отзывов): `https://www.instagram.com/ecosfera_vladivostok`
- **Max** (вместо Telegram): `https://max.ru/u/f9LHodD0cOKLKYieuR9gpcwDBYGePj2RLpF859jBHAooDm89pb2oD0asw_M`
  - aria-label изменён на «Max»

## 6. Дополнительно
- Все переходы по якорям работают плавно благодаря `html { scroll-behavior: smooth }` в `base.css`.
- Никаких изменений в JS не потребовалось (кроме уже существующих обработчиков бургера).