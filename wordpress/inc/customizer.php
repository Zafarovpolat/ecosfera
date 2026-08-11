<?php
/**
 * Кастомайзер: контакты, герой, карта, юр. данные.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'ecosfera_customize_register' );

function ecosfera_customize_register( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_panel(
		'ecosfera',
		array(
			'title'    => __( 'Экосфера', 'ecosfera' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_section( 'ecosfera_contacts', array(
		'title' => __( 'Контакты и соцсети', 'ecosfera' ),
		'panel' => 'ecosfera',
	) );

	$wp_customize->add_section( 'ecosfera_hero', array(
		'title' => __( 'Первый экран', 'ecosfera' ),
		'panel' => 'ecosfera',
	) );

	$wp_customize->add_section( 'ecosfera_map', array(
		'title' => __( 'Карта и как добраться', 'ecosfera' ),
		'panel' => 'ecosfera',
	) );

	$wp_customize->add_section( 'ecosfera_legal', array(
		'title' => __( 'Подвал и юр. данные', 'ecosfera' ),
		'panel' => 'ecosfera',
	) );

	$fields = array(
		'ecosfera_contacts' => array(
			'phone'     => array( 'label' => __( 'Телефон', 'ecosfera' ), 'type' => 'text' ),
			'email'     => array( 'label' => __( 'Email', 'ecosfera' ), 'type' => 'email' ),
			'instagram' => array( 'label' => __( 'Instagram', 'ecosfera' ), 'type' => 'url' ),
			'max'       => array( 'label' => __( 'Max', 'ecosfera' ), 'type' => 'url' ),
		),
		'ecosfera_hero'     => array(
			'hero_line_1'       => array( 'label' => __( 'Заголовок, строка 1', 'ecosfera' ), 'type' => 'text' ),
			'hero_line_2'       => array( 'label' => __( 'Заголовок, строка 2', 'ecosfera' ), 'type' => 'text' ),
			'hero_line_3'       => array( 'label' => __( 'Акцентная строка', 'ecosfera' ), 'type' => 'text' ),
			'hero_lead'         => array( 'label' => __( 'Лид', 'ecosfera' ), 'type' => 'textarea' ),
			'hero_stat_1_num'   => array( 'label' => __( 'Цифра слева', 'ecosfera' ), 'type' => 'text' ),
			'hero_stat_1_label' => array( 'label' => __( 'Подпись слева', 'ecosfera' ), 'type' => 'textarea' ),
			'hero_stat_2_num'   => array( 'label' => __( 'Цифра справа', 'ecosfera' ), 'type' => 'text' ),
			'hero_stat_2_label' => array( 'label' => __( 'Подпись справа', 'ecosfera' ), 'type' => 'textarea' ),
		),
		'ecosfera_map'      => array(
			'address_full'     => array( 'label' => __( 'Полный адрес', 'ecosfera' ), 'type' => 'textarea' ),
			'address_short'    => array( 'label' => __( 'Адрес в подвале', 'ecosfera' ), 'type' => 'textarea' ),
			'directions'       => array( 'label' => __( 'Как на машине', 'ecosfera' ), 'type' => 'textarea' ),
			'no_car'           => array( 'label' => __( 'Нет машины', 'ecosfera' ), 'type' => 'textarea' ),
			'contacts_heading' => array( 'label' => __( 'Заголовок блока контактов', 'ecosfera' ), 'type' => 'text' ),
			'map_iframe'       => array( 'label' => __( 'URL виджета Яндекс.Карт', 'ecosfera' ), 'type' => 'url' ),
			'map_yandex'       => array( 'label' => __( 'Ссылка Яндекс.Карты', 'ecosfera' ), 'type' => 'url' ),
			'map_2gis'         => array( 'label' => __( 'Ссылка 2ГИС', 'ecosfera' ), 'type' => 'url' ),
		),
		'ecosfera_legal'    => array(
			'copyright'        => array( 'label' => __( 'Копирайт / реквизиты', 'ecosfera' ), 'type' => 'textarea' ),
			'booking_prepay'   => array( 'label' => __( 'Плашка предоплаты, заголовок', 'ecosfera' ), 'type' => 'text' ),
			'booking_prepay_d' => array( 'label' => __( 'Плашка предоплаты, текст', 'ecosfera' ), 'type' => 'textarea' ),
			'booking_notify'   => array( 'label' => __( 'Email для заявок (пусто = админ сайта)', 'ecosfera' ), 'type' => 'email' ),
			'faq_cta'          => array( 'label' => __( 'Текст CTA в FAQ', 'ecosfera' ), 'type' => 'textarea' ),
		),
	);

	$defaults = ecosfera_defaults();

	foreach ( $fields as $section => $group ) {
		foreach ( $group as $key => $cfg ) {
			$wp_customize->add_setting(
				'ecosfera_' . $key,
				array(
					'default'           => $defaults[ $key ] ?? '',
					'sanitize_callback' => $cfg['type'] === 'url' ? 'esc_url_raw' : ( $cfg['type'] === 'email' ? 'sanitize_email' : 'sanitize_textarea_field' ),
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				'ecosfera_' . $key,
				array(
					'label'   => $cfg['label'],
					'section' => $section,
					'type'    => $cfg['type'],
				)
			);
		}
	}
}
