<?php
/**
 * Метабоксы домов, отзывов и заявок.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'ecosfera_add_meta_boxes' );

function ecosfera_add_meta_boxes() {
	add_meta_box( 'ecosfera_house_details', __( 'Параметры дома', 'ecosfera' ), 'ecosfera_house_metabox', 'ecosfera_house', 'normal', 'high' );
	add_meta_box( 'ecosfera_review_details', __( 'Данные отзыва', 'ecosfera' ), 'ecosfera_review_metabox', 'ecosfera_review', 'side', 'default' );
	add_meta_box( 'ecosfera_booking_details', __( 'Данные заявки', 'ecosfera' ), 'ecosfera_booking_metabox', 'ecosfera_booking', 'normal', 'high' );
}

function ecosfera_house_metabox( $post ) {
	wp_nonce_field( 'ecosfera_house_save', 'ecosfera_house_nonce' );

	$slug     = get_post_meta( $post->ID, '_ecosfera_slug', true );
	$rooms    = get_post_meta( $post->ID, '_ecosfera_rooms', true );
	$adults   = get_post_meta( $post->ID, '_ecosfera_adults', true );
	$children = get_post_meta( $post->ID, '_ecosfera_children', true );
	$weekday  = get_post_meta( $post->ID, '_ecosfera_price_weekday', true );
	$weekend  = get_post_meta( $post->ID, '_ecosfera_price_weekend', true );
	$note     = get_post_meta( $post->ID, '_ecosfera_footnote', true );
	$rev      = get_post_meta( $post->ID, '_ecosfera_reversed', true );
	$gallery  = get_post_meta( $post->ID, '_ecosfera_gallery', true );
	$amen     = get_post_meta( $post->ID, '_ecosfera_amenities', true );

	if ( ! is_array( $gallery ) ) {
		$gallery = $gallery ? array_filter( array_map( 'trim', explode( ',', (string) $gallery ) ) ) : array();
	}
	if ( ! is_array( $amen ) ) {
		$amen = array();
	}

	$gallery_str = implode( ',', $gallery );
	?>
	<p>
		<label><?php esc_html_e( 'Код дома (для формы):', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_slug" value="<?php echo esc_attr( $slug ); ?>" class="regular-text" placeholder="red / brown">
	</p>
	<p>
		<label><?php esc_html_e( 'Комнаты', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_rooms" value="<?php echo esc_attr( $rooms ); ?>" class="regular-text">
	</p>
	<p>
		<label><?php esc_html_e( 'Взрослые', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_adults" value="<?php echo esc_attr( $adults ); ?>" class="regular-text">
	</p>
	<p>
		<label><?php esc_html_e( 'С детьми', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_children" value="<?php echo esc_attr( $children ); ?>" class="regular-text">
	</p>
	<p>
		<label><?php esc_html_e( 'Цена Вс–Чт, руб./сутки', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_price_weekday" value="<?php echo esc_attr( $weekday ); ?>" class="regular-text">
	</p>
	<p>
		<label><?php esc_html_e( 'Цена Пт–Сб, руб./сутки', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_price_weekend" value="<?php echo esc_attr( $weekend ); ?>" class="regular-text">
	</p>
	<p>
		<label><?php esc_html_e( 'Сноска под карточкой', 'ecosfera' ); ?></label><br>
		<textarea name="ecosfera_footnote" class="large-text" rows="3"><?php echo esc_textarea( $note ); ?></textarea>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ecosfera_reversed" value="1" <?php checked( $rev, '1' ); ?>>
			<?php esc_html_e( 'Зеркальная раскладка (фото справа)', 'ecosfera' ); ?>
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'Галерея (ID вложений через запятую или пути images/…)', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_gallery" id="ecosfera-gallery-ids" value="<?php echo esc_attr( $gallery_str ); ?>" class="large-text">
		<button type="button" class="button ecosfera-media-multi" data-target="#ecosfera-gallery-ids"><?php esc_html_e( 'Выбрать фото', 'ecosfera' ); ?></button>
	</p>
	<hr>
	<h3><?php esc_html_e( 'Удобства', 'ecosfera' ); ?></h3>
	<div class="ecosfera-repeat" data-name="ecosfera_amenities">
		<?php
		if ( ! $amen ) {
			$amen = array( array( 'icon' => '', 'label' => '' ) );
		}
		foreach ( $amen as $i => $row ) :
			?>
			<p class="ecosfera-repeat__row">
				<input type="text" name="ecosfera_amenities[<?php echo (int) $i; ?>][icon]" value="<?php echo esc_attr( $row['icon'] ?? '' ); ?>" placeholder="images/… или ID" class="regular-text">
				<input type="text" name="ecosfera_amenities[<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( $row['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Название', 'ecosfera' ); ?>">
				<button type="button" class="button ecosfera-repeat__remove">&times;</button>
			</p>
		<?php endforeach; ?>
	</div>
	<p><button type="button" class="button ecosfera-repeat__add"><?php esc_html_e( 'Добавить удобство', 'ecosfera' ); ?></button></p>
	<?php
}

function ecosfera_review_metabox( $post ) {
	wp_nonce_field( 'ecosfera_review_save', 'ecosfera_review_nonce' );
	$rating = get_post_meta( $post->ID, '_ecosfera_rating', true ) ?: '5.0';
	$date   = get_post_meta( $post->ID, '_ecosfera_review_date', true ) ?: get_the_date( 'Y-m-d', $post );
	?>
	<p>
		<label><?php esc_html_e( 'Оценка', 'ecosfera' ); ?></label><br>
		<input type="text" name="ecosfera_rating" value="<?php echo esc_attr( $rating ); ?>" class="widefat">
	</p>
	<p>
		<label><?php esc_html_e( 'Дата отзыва', 'ecosfera' ); ?></label><br>
		<input type="date" name="ecosfera_review_date" value="<?php echo esc_attr( $date ); ?>" class="widefat">
	</p>
	<p class="description"><?php esc_html_e( 'Заголовок записи — имя гостя, миниатюра — аватар, текст записи — отзыв.', 'ecosfera' ); ?></p>
	<?php
}

function ecosfera_booking_metabox( $post ) {
	$fields = array(
		'_house'     => __( 'Код дома', 'ecosfera' ),
		'_house_label' => __( 'Дом', 'ecosfera' ),
		'_check_in'  => __( 'Заезд', 'ecosfera' ),
		'_check_out' => __( 'Выезд', 'ecosfera' ),
		'_guests'    => __( 'Гости', 'ecosfera' ),
		'_name'      => __( 'Имя', 'ecosfera' ),
		'_phone'     => __( 'Телефон', 'ecosfera' ),
	);

	echo '<table class="widefat striped"><tbody>';
	foreach ( $fields as $key => $label ) {
		printf(
			'<tr><th>%s</th><td>%s</td></tr>',
			esc_html( $label ),
			esc_html( (string) get_post_meta( $post->ID, $key, true ) )
		);
	}
	echo '</tbody></table>';
}

add_action( 'save_post_ecosfera_house', 'ecosfera_save_house_meta' );

function ecosfera_save_house_meta( $post_id ) {
	if ( ! isset( $_POST['ecosfera_house_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ecosfera_house_nonce'] ) ), 'ecosfera_house_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$keys = array(
		'ecosfera_slug'           => '_ecosfera_slug',
		'ecosfera_rooms'          => '_ecosfera_rooms',
		'ecosfera_adults'         => '_ecosfera_adults',
		'ecosfera_children'       => '_ecosfera_children',
		'ecosfera_price_weekday'  => '_ecosfera_price_weekday',
		'ecosfera_price_weekend'  => '_ecosfera_price_weekend',
		'ecosfera_footnote'       => '_ecosfera_footnote',
		'ecosfera_gallery'        => '_ecosfera_gallery',
	);

	foreach ( $keys as $field => $meta ) {
		if ( ! isset( $_POST[ $field ] ) ) {
			continue;
		}
		$value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
		if ( $field === 'ecosfera_gallery' ) {
			$parts = array_filter( array_map( 'trim', explode( ',', $value ) ) );
			update_post_meta( $post_id, $meta, $parts );
		} elseif ( $field === 'ecosfera_footnote' ) {
			update_post_meta( $post_id, $meta, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
		} else {
			update_post_meta( $post_id, $meta, $value );
		}
	}

	update_post_meta( $post_id, '_ecosfera_reversed', isset( $_POST['ecosfera_reversed'] ) ? '1' : '' );

	$amen = array();
	if ( isset( $_POST['ecosfera_amenities'] ) && is_array( $_POST['ecosfera_amenities'] ) ) {
		foreach ( wp_unslash( $_POST['ecosfera_amenities'] ) as $row ) {
			$label = sanitize_text_field( $row['label'] ?? '' );
			$icon  = sanitize_text_field( $row['icon'] ?? '' );
			if ( $label === '' && $icon === '' ) {
				continue;
			}
			$amen[] = array(
				'icon'  => $icon,
				'label' => $label,
			);
		}
	}
	update_post_meta( $post_id, '_ecosfera_amenities', $amen );
}

add_action( 'save_post_ecosfera_review', 'ecosfera_save_review_meta' );

function ecosfera_save_review_meta( $post_id ) {
	if ( ! isset( $_POST['ecosfera_review_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ecosfera_review_nonce'] ) ), 'ecosfera_review_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['ecosfera_rating'] ) ) {
		update_post_meta( $post_id, '_ecosfera_rating', sanitize_text_field( wp_unslash( $_POST['ecosfera_rating'] ) ) );
	}
	if ( isset( $_POST['ecosfera_review_date'] ) ) {
		update_post_meta( $post_id, '_ecosfera_review_date', sanitize_text_field( wp_unslash( $_POST['ecosfera_review_date'] ) ) );
	}
}
