<?php
/**
 * Первичное наполнение: юр. страницы, дома, отзывы, FAQ, меню.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'ecosfera_seed' );

function ecosfera_seed() {
	ecosfera_seed_pages();
	ecosfera_seed_houses();
	ecosfera_seed_reviews();
	ecosfera_seed_faq();
	ecosfera_seed_menus();
	flush_rewrite_rules();
}

function ecosfera_seed_pages() {
	$pages = array(
		'privacy' => array(
			'title'   => __( 'Политика конфиденциальности', 'ecosfera' ),
			'content' => ecosfera_legal_privacy(),
		),
		'oferta'  => array(
			'title'   => __( 'Публичная оферта', 'ecosfera' ),
			'content' => ecosfera_legal_oferta(),
		),
		'terms'   => array(
			'title'   => __( 'Пользовательское соглашение', 'ecosfera' ),
			'content' => ecosfera_legal_terms(),
		),
	);

	foreach ( $pages as $slug => $data ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $data['title'],
				'post_name'    => $slug,
				'post_content' => $data['content'],
			)
		);

		if ( $slug === 'privacy' && $id && ! is_wp_error( $id ) ) {
			update_option( 'wp_page_for_privacy_policy', $id );
		}
	}

	$front = get_page_by_path( 'home' );
	if ( ! $front ) {
		$front_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => __( 'Главная', 'ecosfera' ),
				'post_name'    => 'home',
				'post_content' => '',
			)
		);
	} else {
		$front_id = $front->ID;
	}

	if ( $front_id && ! is_wp_error( $front_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_id );
	}
}

function ecosfera_seed_houses() {
	if ( wp_count_posts( 'ecosfera_house' )->publish > 0 ) {
		return;
	}

	$order = 0;
	foreach ( ecosfera_defaults()['houses'] as $house ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'ecosfera_house',
				'post_status'  => 'publish',
				'post_title'   => $house['title'],
				'post_name'    => $house['slug'],
				'menu_order'   => $order++,
			)
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_ecosfera_slug', $house['slug'] );
		update_post_meta( $id, '_ecosfera_rooms', $house['rooms'] );
		update_post_meta( $id, '_ecosfera_adults', $house['adults'] );
		update_post_meta( $id, '_ecosfera_children', $house['children'] );
		update_post_meta( $id, '_ecosfera_price_weekday', $house['price_weekday'] );
		update_post_meta( $id, '_ecosfera_price_weekend', $house['price_weekend'] );
		update_post_meta( $id, '_ecosfera_footnote', $house['footnote'] );
		update_post_meta( $id, '_ecosfera_reversed', $house['reversed'] ? '1' : '' );
		update_post_meta( $id, '_ecosfera_gallery', $house['gallery'] );
		update_post_meta( $id, '_ecosfera_amenities', $house['amenities'] );
	}
}

function ecosfera_seed_reviews() {
	if ( wp_count_posts( 'ecosfera_review' )->publish > 0 ) {
		return;
	}

	$order = 0;
	foreach ( ecosfera_defaults()['reviews'] as $review ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'ecosfera_review',
				'post_status'  => 'publish',
				'post_title'   => $review['author'],
				'post_content' => $review['text'],
				'menu_order'   => $order++,
			)
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_ecosfera_rating', $review['rating'] );
		update_post_meta( $id, '_ecosfera_review_date', $review['date'] );
	}
}

function ecosfera_seed_faq() {
	if ( wp_count_posts( 'ecosfera_faq' )->publish > 0 ) {
		return;
	}

	$order = 0;
	foreach ( ecosfera_defaults()['faq'] as $item ) {
		wp_insert_post(
			array(
				'post_type'    => 'ecosfera_faq',
				'post_status'  => 'publish',
				'post_title'   => $item['question'],
				'post_content' => $item['answer'],
				'menu_order'   => $order++,
			)
		);
	}
}

function ecosfera_seed_menus() {
	if ( wp_get_nav_menu_object( 'Экосфера — шапка' ) ) {
		return;
	}

	$menu_id = wp_create_nav_menu( 'Экосфера — шапка' );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	$items = array(
		__( 'Дома', 'ecosfera' )     => home_url( '/#doma' ),
		__( 'Услуги', 'ecosfera' )   => home_url( '/#uslugi' ),
		__( 'Галерея', 'ecosfera' )  => home_url( '/#gallery' ),
		__( 'Вопросы', 'ecosfera' )  => home_url( '/#faq' ),
		__( 'Контакты', 'ecosfera' ) => home_url( '/#contacts' ),
	);

	foreach ( $items as $title => $url ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $title,
				'menu-item-url'    => $url,
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			)
		);
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

function ecosfera_legal_privacy() {
	return <<<HTML
<p>Настоящая Политика определяет порядок обработки и защиты персональных данных пользователей сайта «Экосфера» (далее — Сайт) в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ «О персональных данных».</p>
<h2>1. Оператор</h2>
<p>ООО «КОСМОС», ИНН 2540282522, КПП 254001001, ОГРН 1242500000863, адрес: 690042, г. Владивосток, ул. Феодосийская, д. 37, кв. 129. Контакт: Ecosfera2026@mail.ru, +7 (950) 282-43-82.</p>
<h2>2. Какие данные мы обрабатываем</h2>
<p>Имя, номер телефона, выбранный дом, даты заезда и выезда, количество гостей, а также технические данные (IP, cookie), необходимые для работы Сайта.</p>
<h2>3. Цели обработки</h2>
<p>Приём и подтверждение бронирования, связь с гостем, исполнение договора оказания услуг, направление ответов на обращения, улучшение работы Сайта.</p>
<h2>4. Правовые основания</h2>
<p>Согласие субъекта персональных данных, заключение и исполнение договора, законные интересы оператора в рамках 152-ФЗ.</p>
<h2>5. Срок хранения</h2>
<p>Данные хранятся в течение срока, необходимого для целей обработки, и удаляются по запросу субъекта либо по истечении срока хранения, если иное не предусмотрено законом.</p>
<h2>6. Права субъекта</h2>
<p>Вы вправе запросить сведения об обработке, уточнить, блокировать или удалить данные, отозвать согласие, направив обращение на Ecosfera2026@mail.ru.</p>
<h2>7. Защита</h2>
<p>Оператор принимает организационные и технические меры, направленные на защиту персональных данных от неправомерного доступа и распространения.</p>
HTML;
}

function ecosfera_legal_oferta() {
	return <<<HTML
<p>Настоящий документ является официальным предложением ООО «КОСМОС» (далее — Исполнитель) заключить договор аренды гостевого дома на условиях, изложенных ниже.</p>
<h2>1. Предмет</h2>
<p>Исполнитель предоставляет Заказчику во временное пользование один из гостевых домов комплекса «Экосфера» по адресу: Приморский край, Вольно-Надеждинск, пос. Западный, ул. Новая, 8, а Заказчик оплачивает услугу.</p>
<h2>2. Акцепт</h2>
<p>Акцептом оферты является отправка заявки через форму на Сайте и внесение предоплаты 50% после подтверждения бронирования.</p>
<h2>3. Стоимость и оплата</h2>
<p>Стоимость суток указана на Сайте и зависит от дня недели. Остаток оплачивается при заезде. Дополнительные услуги (баня, дрова, доп. гость и др.) оплачиваются отдельно.</p>
<h2>4. Правила проживания</h2>
<p>Заезд и выезд согласуются при подтверждении брони. Заказчик несёт ответственность за сохранность имущества и соблюдение тишины на территории.</p>
<h2>5. Отмена</h2>
<p>Условия возврата предоплаты сообщаются при подтверждении брони и могут зависеть от срока отмены.</p>
HTML;
}

function ecosfera_legal_terms() {
	return <<<HTML
<p>Используя сайт «Экосфера», вы соглашаетесь с условиями настоящего Пользовательского соглашения.</p>
<h2>1. Общие положения</h2>
<p>Сайт предназначен для знакомства с комплексом и подачи заявки на бронирование. Информационные материалы носят ознакомительный характер; итоговые условия фиксируются при подтверждении брони.</p>
<h2>2. Интеллектуальная собственность</h2>
<p>Тексты, фотографии, дизайн и товарные обозначения принадлежат правообладателю. Копирование без согласия запрещено.</p>
<h2>3. Ограничение ответственности</h2>
<p>Исполнитель не отвечает за сбои связи, работу сторонних сервисов (карты, мессенджеры) и действия пользователей, нарушающие закон.</p>
<h2>4. Изменения</h2>
<p>Соглашение может быть обновлено. Актуальная редакция всегда доступна на этой странице.</p>
HTML;
}
