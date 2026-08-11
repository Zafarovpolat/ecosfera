<?php
/**
 * Типы записей: дома, отзывы, FAQ, заявки.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'ecosfera_register_cpts' );

function ecosfera_register_cpts() {
	register_post_type(
		'ecosfera_house',
		array(
			'labels'       => array(
				'name'               => __( 'Дома', 'ecosfera' ),
				'singular_name'      => __( 'Дом', 'ecosfera' ),
				'add_new'            => __( 'Добавить дом', 'ecosfera' ),
				'add_new_item'       => __( 'Новый дом', 'ecosfera' ),
				'edit_item'          => __( 'Редактировать дом', 'ecosfera' ),
				'menu_name'          => __( 'Дома', 'ecosfera' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-admin-home',
			'menu_position'=> 21,
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'  => false,
		)
	);

	register_post_type(
		'ecosfera_review',
		array(
			'labels'       => array(
				'name'          => __( 'Отзывы', 'ecosfera' ),
				'singular_name' => __( 'Отзыв', 'ecosfera' ),
				'add_new_item'  => __( 'Новый отзыв', 'ecosfera' ),
				'edit_item'     => __( 'Редактировать отзыв', 'ecosfera' ),
				'menu_name'     => __( 'Отзывы', 'ecosfera' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-star-filled',
			'menu_position'=> 22,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'ecosfera_faq',
		array(
			'labels'       => array(
				'name'          => __( 'Вопросы', 'ecosfera' ),
				'singular_name' => __( 'Вопрос', 'ecosfera' ),
				'add_new_item'  => __( 'Новый вопрос', 'ecosfera' ),
				'edit_item'     => __( 'Редактировать вопрос', 'ecosfera' ),
				'menu_name'     => __( 'FAQ', 'ecosfera' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-editor-help',
			'menu_position'=> 23,
			'supports'     => array( 'title', 'editor', 'page-attributes' ),
		)
	);

	register_post_type(
		'ecosfera_booking',
		array(
			'labels'       => array(
				'name'          => __( 'Заявки', 'ecosfera' ),
				'singular_name' => __( 'Заявка', 'ecosfera' ),
				'edit_item'     => __( 'Заявка на бронь', 'ecosfera' ),
				'menu_name'     => __( 'Заявки', 'ecosfera' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-email-alt',
			'menu_position'=> 24,
			'supports'     => array( 'title' ),
			'capabilities' => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap' => true,
		)
	);
}

add_filter( 'manage_ecosfera_booking_posts_columns', 'ecosfera_booking_columns' );

function ecosfera_booking_columns( $columns ) {
	return array(
		'cb'     => $columns['cb'],
		'title'  => __( 'Заявка', 'ecosfera' ),
		'house'  => __( 'Дом', 'ecosfera' ),
		'dates'  => __( 'Даты', 'ecosfera' ),
		'phone'  => __( 'Телефон', 'ecosfera' ),
		'date'   => __( 'Получена', 'ecosfera' ),
	);
}

add_action( 'manage_ecosfera_booking_posts_custom_column', 'ecosfera_booking_column_content', 10, 2 );

function ecosfera_booking_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'house':
			echo esc_html( get_post_meta( $post_id, '_house_label', true ) );
			break;
		case 'dates':
			echo esc_html( get_post_meta( $post_id, '_check_in', true ) . ' — ' . get_post_meta( $post_id, '_check_out', true ) );
			break;
		case 'phone':
			echo esc_html( get_post_meta( $post_id, '_phone', true ) );
			break;
	}
}
