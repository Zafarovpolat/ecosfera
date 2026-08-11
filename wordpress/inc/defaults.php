<?php
/**
 * Контент лендинга по умолчанию — 1:1 с исходной вёрсткой.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Все дефолтные тексты, цены, ссылки и пути к ассетам.
 */
function ecosfera_defaults() {
	static $defaults = null;

	if ( $defaults !== null ) {
		return $defaults;
	}

	$defaults = array(
		'phone'            => '+7 (950) 282-43-82',
		'email'            => 'Ecosfera2026@mail.ru',
		'address_short'    => "Вольно-Надеждинск,\nпос. Западный, ул. Новая, 8",
		'address_full'     => 'Приморский край, Вольно-Надеждинск, пос. Западный, ул. Новая, 8',
		'directions'       => 'Из Владивостока по трассе А370 в сторону Уссурийска. Поворот на Вольно-Надеждинск, посёлок Западный, ул. Новая, 8. Навигатор доведёт точно.',
		'no_car'           => 'Напишите нам — поможем с маршрутом или подскажем трансфер.',
		'instagram'        => 'https://www.instagram.com/ecosfera_vladivostok',
		'max'              => 'https://max.ru/u/f9LHodD0cOKLKYieuR9gpcwDBYGePj2RLpF859jBHAooDm89pb2oD0asw_M',
		'map_iframe'       => 'https://yandex.uz/map-widget/v1/?ll=131.964826%2C43.359575&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgo0MzQwNDAyMDMwEqYB0KDQvtGB0YHQuNGPLCDQn9GA0LjQvNC-0YDRgdC60LjQuSDQutGA0LDQuSwg0J3QsNC00LXQttC00LjQvdGB0LrQuNC5INC80YPQvdC40YbQuNC_0LDQu9GM0L3Ri9C5INC-0LrRgNGD0LssINC_0L7RgdGR0LvQvtC6INCX0LDQv9Cw0LTQvdGL0LksINCd0L7QstCw0Y8g0YPQu9C40YbQsCwgNiIKDfb2A0MVnXAtQg%2C%2C&z=16.42',
		'map_yandex'       => 'https://yandex.ru/maps/?text=%D0%92%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE-%D0%9D%D0%B0%D0%B4%D0%B5%D0%B6%D0%B4%D0%B8%D0%BD%D1%81%D0%BA+%D0%BF%D0%BE%D1%81.+%D0%97%D0%B0%D0%BF%D0%B0%D0%B4%D0%BD%D1%8B%D0%B9+%D1%83%D0%BB.+%D0%9D%D0%BE%D0%B2%D0%B0%D1%8F+8',
		'map_2gis'         => 'https://2gis.ru/vladivostok/search/%D0%92%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE-%D0%9D%D0%B0%D0%B4%D0%B5%D0%B6%D0%B4%D0%B8%D0%BD%D1%81%D0%BA%20%D0%BF%D0%BE%D1%81.%20%D0%97%D0%B0%D0%BF%D0%B0%D0%B4%D0%BD%D1%8B%D0%B9%20%D1%83%D0%BB.%20%D0%9D%D0%BE%D0%B2%D0%B0%D1%8F%208',
		'copyright'        => '© 2026 ЭКОСФЕРА. Все права защищены. ООО «КОСМОС» · ИНН 2540282522 · КПП 254001001 · ОГРН 1242500000863 690042, г. Владивосток, ул. Феодосийская, д.37, кв.129',
		'made_by_url'      => 'https://ruso.ru/',
		'hero_line_1'      => 'Отдых, где природа',
		'hero_line_2'      => 'становится',
		'hero_line_3'      => 'вашим комфортом',
		'hero_lead'        => 'Уютные домики, свежий воздух, тишина леса и всё необходимое для идеальных выходных, отпуска или семейного отдыха.',
		'hero_image'       => 'images/wide-shot-brown-house-surrounded-by-forest-spruc-0c4a2df57c.webp',
		'hero_stat_1_num'  => '2',
		'hero_stat_1_label'=> "Уютных\n дома",
		'hero_stat_2_num'  => '40',
		'hero_stat_2_label'=> 'минут от Владивостока',
		'logo'             => 'images/image-31-7848d9a61e.webp',
		'backdrop'         => 'images/vertical-shot-cabin-forest-surrounded-by-lot-gre-ccf609efd1.webp',
		'booking_prepay'   => 'Предоплата 50% от стоимости',
		'booking_prepay_d' => 'Оплачивается после подтверждения бронирования. Остаток — при заезде.',
		'booking_notify'   => '',
		'faq_cta'          => 'Не нашли ответ — напишите нам, ответим быстро.',
		'contacts_heading' => '40 минут от Владивостока',

		'advantages'       => array(
			array(
				'num'   => '2',
				'label' => 'Уютных дома',
				'desc'  => 'Современные дома со всем необходимым для комфортного отдыха.',
			),
			array(
				'num'   => '10',
				'label' => 'Гостей с детьми',
				'desc'  => 'Комфортное размещение для семьи или компании друзей.',
			),
			array(
				'num'   => '12',
				'label' => 'месяцев Работаем весь год',
				'desc'  => 'Принимаем гостей в любое время года, независимо от сезона.',
			),
		),

		'nearby'           => array(
			array(
				'num'   => '01',
				'title' => 'Территория',
				'desc'  => 'Ухоженная зелёная территория, где можно насладиться тишиной, прогулками на свежем воздухе и отдыхом в окружении природы.',
			),
			array(
				'num'   => '02',
				'title' => 'Бассейн',
				'desc'  => 'Просторный открытый бассейн с зоной отдыха — идеальное место, чтобы освежиться в жаркий день и провести время с семьёй или друзьями.',
			),
			array(
				'num'   => '03',
				'title' => 'Чан / Баня',
				'desc'  => 'Расслабьтесь в горячем чане под открытым небом или согрейтесь в бане после активного дня. Отдых для тела и души в любое время года.',
			),
		),

		'included'         => array(
			'Проживание (3 комнаты с двухспальными кроватями)',
			'Беседка на территории',
			'Мангальная зона',
			'Бассейн (свой у каждого дома)',
			'Чан (входит в стоимость)',
		),

		'extras'           => array(
			array( 'label' => 'Костровая зона (Красный дом)', 'price' => '3 000 ₽/час' ),
			array( 'label' => 'Подогрев чана', 'price' => '1 000 ₽' ),
			array( 'label' => 'Запарка для чана', 'price' => '2 000 ₽' ),
			array( 'label' => 'Веник для бани', 'price' => '1 000 ₽' ),
			array( 'label' => 'Ментол / запарка', 'price' => 'от 500 ₽' ),
			array( 'label' => 'Дрова', 'price' => '500 ₽' ),
			array( 'label' => 'Уголь', 'price' => 'от 500 ₽' ),
			array( 'label' => 'Доп. гость', 'price' => '2 000 ₽' ),
		),

		'gallery_row_1'    => array(
			array( 'src' => 'images/rectangle-7-b360f6a2fc.webp', 'src_1x' => 'images/rectangle-7-b360f6a2fc@1x.webp', 'alt' => 'Летняя беседка на территории Экосферы' ),
			array( 'src' => 'images/rectangle-8-be5e72298d.webp', 'src_1x' => 'images/rectangle-8-be5e72298d@1x.webp', 'alt' => 'Открытый бассейн с шезлонгами' ),
			array( 'src' => 'images/rectangle-9-db187370c8.webp', 'src_1x' => 'images/rectangle-9-db187370c8@1x.webp', 'alt' => 'Вечерний вид на ухоженную зелёную территорию' ),
			array( 'src' => 'images/rectangle-10-096c83987c.webp', 'src_1x' => 'images/rectangle-10-096c83987c@1x.webp', 'alt' => 'Уютный деревянный домик среди деревьев' ),
			array( 'src' => 'images/rectangle-11-9bfd6a02d4.webp', 'src_1x' => 'images/rectangle-11-9bfd6a02d4@1x.webp', 'alt' => 'Горячий чан под открытым небом' ),
		),

		'gallery_row_2'    => array(
			array( 'src' => 'images/rectangle-7-b5263eab0d.webp', 'src_1x' => 'images/rectangle-7-b5263eab0d@1x.webp', 'alt' => 'Панорамный вид на загородный комплекс' ),
			array( 'src' => 'images/rectangle-7-b360f6a2fc.webp', 'src_1x' => 'images/rectangle-7-b360f6a2fc@1x.webp', 'alt' => 'Летняя беседка в вечернем свете' ),
			array( 'src' => 'images/rectangle-7-b5263eab0d.webp', 'src_1x' => 'images/rectangle-7-b5263eab0d@1x.webp', 'alt' => 'Прогулочная дорожка в лесу' ),
			array( 'src' => 'images/rectangle-9-9c5a873c96.webp', 'src_1x' => 'images/rectangle-9-9c5a873c96@1x.webp', 'alt' => 'Территория комплекса весной' ),
			array( 'src' => 'images/rectangle-10-096c83987c.webp', 'src_1x' => 'images/rectangle-10-096c83987c@1x.webp', 'alt' => 'Интерьер гостевого домика' ),
		),

		'gallery_mobile'   => array(
			array( 'src' => 'images/rectangle-26-6694ee6a44.webp', 'alt' => 'Территория Экосферы с ухоженными дорожками' ),
			array( 'src' => 'images/rectangle-29-129587d2ff.webp', 'alt' => 'Бассейн с прозрачной водой' ),
			array( 'src' => 'images/rectangle-28-ec2f192dab.webp', 'alt' => 'Деревянные домики в лесу' ),
			array( 'src' => 'images/rectangle-29-129587d2ff.webp', 'alt' => 'Зона отдыха у воды' ),
			array( 'src' => 'images/rectangle-27-ffdb6f4b12.webp', 'alt' => 'Вечерний вид на главный корпус' ),
			array( 'src' => 'images/rectangle-31-88b0a29e43.webp', 'alt' => 'Баня с видом на природу' ),
		),

		'comparison'       => array(
			array(
				'title' => '40 минут от Владивостока',
				'desc'  => 'Не надо брать отгул на дорогу — выехали после работы, уже на месте.',
			),
			array(
				'title' => '3 спальни — никто не спит на диване',
				'desc'  => 'Двухспальные кровати в каждой комнате для комфорта всех гостей.',
			),
			array(
				'title' => 'Свой бассейн у каждого дома',
				'desc'  => 'Не общий на базе, а только ваш — на всё время аренды.',
			),
			array(
				'title' => '3 спальни — никто не спит на диване',
				'desc'  => 'Двухспальные кровати в каждой комнате для комфорта всех гостей.',
			),
			array(
				'title' => '40 минут от Владивостока',
				'desc'  => 'Не надо брать отгул на дорогу — выехали после работы, уже на месте.',
			),
		),

		'audience'         => array(
			array(
				'image' => 'images/rectangle-20-d9e4243751.webp',
				'title' => 'Большая компания',
				'desc'  => 'Два дома рядом — до 12 взрослых или 20 с детьми. Своя территория у каждой группы.',
			),
			array(
				'image' => 'images/rectangle-20-7697270bff.webp',
				'title' => 'Праздник или ДР',
				'desc'  => 'Свой дом, мангал, беседка — отмечайте как хотите, без чужих.',
			),
			array(
				'image' => 'images/rectangle-20-c4d1d93504.webp',
				'title' => 'Романтический выезд',
				'desc'  => 'Чан под звёздами, тишина, свой бассейн — никаких соседей рядом.',
			),
			array(
				'image' => 'images/rectangle-20-a97113c13d.webp',
				'title' => 'Семьям с детьми',
				'desc'  => 'До 10 человек с детьми — бассейн, костёр, безопасная территория и простор для игр.',
			),
		),

		'guest_options'    => array(
			'1-2' => '1–2 человека',
			'3-4' => '3–4 человека',
			'5'   => '5 человек',
		),

		'houses'           => array(
			array(
				'id'            => 0,
				'slug'          => 'red',
				'title'         => 'Красный дом',
				'rooms'         => '3 комнаты',
				'adults'        => 'до 6 взрослых',
				'children'      => 'до 10 с детьми',
				'price_weekday' => '20 000',
				'price_weekend' => '27 000',
				'footnote'      => 'Включает костровую зону и чан — отличие от Коричневого дома. Баня доступна дополнительно.',
				'reversed'      => false,
				'gallery'       => array(
					'images/8c50a548-3790-4a2f-abf2-1feb6309988b-2-81bfc546e9.webp',
					'images/rectangle-9-db187370c8.webp',
					'images/rectangle-10-096c83987c.webp',
					'images/rectangle-7-b360f6a2fc.webp',
					'images/rectangle-11-9bfd6a02d4.webp',
				),
				'amenities'     => array(
					array( 'icon' => 'images/image-35-44c151b7a2.webp', 'label' => 'Бассейн' ),
					array( 'icon' => 'images/image-37-bc0f6b3a51.webp', 'label' => 'Беседка' ),
					array( 'icon' => 'images/image-36-9a140b3c64.webp', 'label' => 'Чан' ),
					array( 'icon' => 'images/image-38-04a00a26e3.webp', 'label' => 'Мангал' ),
					array( 'icon' => 'images/image-38-856dd823cf.webp', 'label' => 'Wi-Fi' ),
					array( 'icon' => 'images/image-37-7ed89f7b62.webp', 'label' => 'Костровая зона' ),
				),
			),
			array(
				'id'            => 0,
				'slug'          => 'brown',
				'title'         => 'Коричневый дом',
				'rooms'         => '3 комнаты',
				'adults'        => 'до 6 взрослых',
				'children'      => 'до 10 с детьми',
				'price_weekday' => '20 000',
				'price_weekend' => '27 000',
				'footnote'      => '3 спальни с двухспальными кроватями. Баня доступна дополнительно.',
				'reversed'      => true,
				'gallery'       => array(
					'images/8c50a548-3790-4a2f-abf2-1feb6309988b-2-81bfc546e9.webp',
					'images/rectangle-9-db187370c8.webp',
					'images/rectangle-8-be5e72298d.webp',
					'images/rectangle-9-9c5a873c96.webp',
					'images/rectangle-11-9bfd6a02d4.webp',
				),
				'amenities'     => array(
					array( 'icon' => 'images/image-35-44c151b7a2.webp', 'label' => 'Бассейн' ),
					array( 'icon' => 'images/image-37-bc0f6b3a51.webp', 'label' => 'Беседка' ),
					array( 'icon' => 'images/image-36-9a140b3c64.webp', 'label' => 'Чан' ),
					array( 'icon' => 'images/image-38-04a00a26e3.webp', 'label' => 'Мангал' ),
					array( 'icon' => 'images/image-38-856dd823cf.webp', 'label' => 'Wi-Fi' ),
					array( 'icon' => 'images/image-37-7ed89f7b62.webp', 'label' => 'Костровая зона' ),
				),
			),
		),

		'reviews'          => array(
			array(
				'author' => 'Алексей',
				'text'   => 'Современные и уютные домики, очень чисто, красивая территория с бассейном и горячим чаном. Тишина, свежий воздух и всё необходимое для комфортного отдыха. Обязательно приедем ещё!',
				'rating' => '5.0',
				'date'   => '2026-03-12',
				'avatar' => 'images/frame-403-b0f27b4e15.webp',
			),
			array(
				'author' => 'Алексей',
				'text'   => 'Современные и уютные домики, очень чисто, красивая территория с бассейном и горячим чаном. Тишина, свежий воздух и всё необходимое для комфортного отдыха. Обязательно приедем ещё!',
				'rating' => '5.0',
				'date'   => '2026-03-12',
				'avatar' => 'images/frame-403-b0f27b4e15.webp',
			),
			array(
				'author' => 'Алексей',
				'text'   => 'Современные и уютные домики, очень чисто, красивая территория с бассейном и горячим чаном. Тишина, свежий воздух и всё необходимое для комфортного отдыха. Обязательно приедем ещё!',
				'rating' => '5.0',
				'date'   => '2026-03-12',
				'avatar' => 'images/frame-403-b0f27b4e15.webp',
			),
			array(
				'author' => 'Алексей',
				'text'   => 'Современные и уютные домики, очень чисто, красивая территория с бассейном и горячим чаном. Тишина, свежий воздух и всё необходимое для комфортного отдыха. Обязательно приедем ещё!',
				'rating' => '5.0',
				'date'   => '2026-03-12',
				'avatar' => 'images/frame-403-b0f27b4e15.webp',
			),
			array(
				'author' => 'Алексей',
				'text'   => 'Современные и уютные домики, очень чисто, красивая территория с бассейном и горячим чаном. Тишина, свежий воздух и всё необходимое для комфортного отдыха. Обязательно приедем ещё!',
				'rating' => '5.0',
				'date'   => '2026-03-12',
				'avatar' => 'images/frame-403-b0f27b4e15.webp',
			),
		),

		'faq'              => array(
			array(
				'question' => 'Сколько человек вмещает каждый дом?',
				'answer'   => '<p class="faq__answer">Каждый дом рассчитан на комфортное размещение до 5 гостей. В домах есть всё необходимое для отдыха семьи, пары или компании друзей. Если у вас большая компания, вы можете забронировать сразу оба дома.</p>',
			),
			array(
				'question' => 'Можно ли арендовать оба дома сразу?',
				'answer'   => '<p class="faq__answer">Да, вы можете забронировать оба дома одновременно. Это удобно для большой компании или двух семей, которые хотят отдыхать рядом.</p>',
			),
			array(
				'question' => 'Нужна ли предоплата?',
				'answer'   => '<p class="faq__answer">Да, предоплата составляет 50% от стоимости проживания. Она вносится после подтверждения бронирования. Оставшаяся часть оплачивается при заезде.</p>',
			),
			array(
				'question' => 'Баня входит в стоимость?',
				'answer'   => '<p class="faq__answer">Баня оплачивается отдельно. Уточнить актуальную стоимость и забронировать баню можно при бронировании дома или напрямую у нас через WhatsApp.</p>',
			),
			array(
				'question' => 'Чем отличаются Коричневый и Красный дома?',
				'answer'   => '<p class="faq__answer">Оба дома имеют одинаковую площадь и вместимость. Коричневый дом оформлен в тёплых природных тонах, Красный — в более ярком акцентном стиле. Выбирайте по настроению и предпочтениям вашей компании.</p>',
			),
		),
	);

	return $defaults;
}
