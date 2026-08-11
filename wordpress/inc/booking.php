<?php
/**
 * Приём заявок с формы бронирования.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_nopriv_ecosfera_booking', 'ecosfera_handle_booking' );
add_action( 'admin_post_ecosfera_booking', 'ecosfera_handle_booking' );
add_action( 'wp_ajax_nopriv_ecosfera_booking', 'ecosfera_handle_booking' );
add_action( 'wp_ajax_ecosfera_booking', 'ecosfera_handle_booking' );

function ecosfera_handle_booking() {
	$is_ajax = wp_doing_ajax();

	if ( ! isset( $_POST['ecosfera_booking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ecosfera_booking_nonce'] ) ), 'ecosfera_booking' ) ) {
		ecosfera_booking_fail( __( 'Сессия устарела, обновите страницу.', 'ecosfera' ), $is_ajax );
	}

	$house    = sanitize_text_field( wp_unslash( $_POST['house'] ?? '' ) );
	$check_in = sanitize_text_field( wp_unslash( $_POST['checkIn'] ?? '' ) );
	$check_out= sanitize_text_field( wp_unslash( $_POST['checkOut'] ?? '' ) );
	$guests   = sanitize_text_field( wp_unslash( $_POST['guests'] ?? '' ) );
	$name     = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone    = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$consent  = ! empty( $_POST['consent'] );

	$errors = array();
	if ( $house === '' ) {
		$errors[] = 'house';
	}
	if ( $check_in === '' ) {
		$errors[] = 'checkIn';
	}
	if ( $check_out === '' ) {
		$errors[] = 'checkOut';
	}
	if ( $guests === '' ) {
		$errors[] = 'guests';
	}
	if ( mb_strlen( $name ) < 2 ) {
		$errors[] = 'name';
	}
	if ( strlen( preg_replace( '/\D+/', '', $phone ) ) < 11 ) {
		$errors[] = 'phone';
	}
	if ( ! $consent ) {
		$errors[] = 'consent';
	}

	if ( $errors ) {
		ecosfera_booking_fail( __( 'Проверьте поля формы.', 'ecosfera' ), $is_ajax, $errors );
	}

	$house_label = $house;
	foreach ( ecosfera_get_houses() as $item ) {
		if ( ( $item['slug'] ?? '' ) === $house ) {
			$house_label = $item['title'];
			break;
		}
	}

	$guest_label = ecosfera_get( 'guest_options' )[ $guests ] ?? $guests;

	$title = sprintf(
		/* translators: 1: name, 2: house */
		__( 'Бронь: %1$s — %2$s', 'ecosfera' ),
		$name,
		$house_label
	);

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'ecosfera_booking',
			'post_status' => 'private',
			'post_title'  => $title,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		ecosfera_booking_fail( __( 'Не удалось сохранить заявку.', 'ecosfera' ), $is_ajax );
	}

	update_post_meta( $post_id, '_house', $house );
	update_post_meta( $post_id, '_house_label', $house_label );
	update_post_meta( $post_id, '_check_in', $check_in );
	update_post_meta( $post_id, '_check_out', $check_out );
	update_post_meta( $post_id, '_guests', $guest_label );
	update_post_meta( $post_id, '_name', $name );
	update_post_meta( $post_id, '_phone', $phone );

	$to = ecosfera_get( 'booking_notify' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$body  = "Имя: {$name}\n";
	$body .= "Телефон: {$phone}\n";
	$body .= "Дом: {$house_label}\n";
	$body .= "Заезд: {$check_in}\n";
	$body .= "Выезд: {$check_out}\n";
	$body .= "Гости: {$guest_label}\n";

	wp_mail(
		$to,
		sprintf( '[Экосфера] %s', $title ),
		$body,
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);

	if ( $is_ajax ) {
		wp_send_json_success(
			array(
				'message' => __( 'Заявка отправлена. Мы свяжемся с вами, чтобы подтвердить бронь.', 'ecosfera' ),
			)
		);
	}

	wp_safe_redirect( add_query_arg( 'booking', 'ok', wp_get_referer() ? wp_get_referer() : home_url( '/#booking' ) ) );
	exit;
}

function ecosfera_booking_fail( $message, $is_ajax, $fields = array() ) {
	if ( $is_ajax ) {
		wp_send_json_error(
			array(
				'message' => $message,
				'fields'  => $fields,
			)
		);
	}

	wp_safe_redirect( add_query_arg( 'booking', 'err', wp_get_referer() ? wp_get_referer() : home_url( '/#booking' ) ) );
	exit;
}
