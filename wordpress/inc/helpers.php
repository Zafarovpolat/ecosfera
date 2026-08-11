<?php
/**
 * Хелперы темы.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL ассета темы (css/js/fonts/images/icons).
 */
function ecosfera_asset( $relative ) {
	return ECOSFERA_URI . '/assets/' . ltrim( (string) $relative, '/' );
}

/**
 * Превращает ID вложения, относительный путь или абсолютный URL в URL картинки.
 */
function ecosfera_src( $src ) {
	if ( $src === '' || $src === null ) {
		return '';
	}

	if ( is_numeric( $src ) ) {
		$url = wp_get_attachment_image_url( (int) $src, 'full' );
		return $url ? $url : '';
	}

	$src = (string) $src;

	if ( preg_match( '#^(https?:)?//#', $src ) || str_starts_with( $src, 'data:' ) ) {
		return $src;
	}

	return ecosfera_asset( $src );
}

/**
 * Значение из настроек темы с запасным дефолтом.
 */
function ecosfera_get( $key, $default = null ) {
	$content = get_option( 'ecosfera_content', array() );

	if ( is_array( $content ) && array_key_exists( $key, $content ) && $content[ $key ] !== '' && $content[ $key ] !== null ) {
		return $content[ $key ];
	}

	$mod = get_theme_mod( 'ecosfera_' . $key, null );
	if ( $mod !== null && $mod !== '' ) {
		return $mod;
	}

	$defaults = ecosfera_defaults();

	if ( array_key_exists( $key, $defaults ) ) {
		return $defaults[ $key ];
	}

	return $default;
}

/**
 * Телефон только цифрами, с ведущей 7.
 */
function ecosfera_phone_digits( $phone = null ) {
	$phone  = $phone ?? ecosfera_get( 'phone' );
	$digits = preg_replace( '/\D+/', '', (string) $phone );

	if ( $digits === '' ) {
		return '';
	}

	if ( str_starts_with( $digits, '8' ) && strlen( $digits ) === 11 ) {
		$digits = '7' . substr( $digits, 1 );
	}

	return $digits;
}

/**
 * Ссылка tel:
 */
function ecosfera_tel_href( $phone = null ) {
	$digits = ecosfera_phone_digits( $phone );
	return $digits ? 'tel:+' . $digits : '';
}

/**
 * Ссылка WhatsApp.
 */
function ecosfera_whatsapp_href( $phone = null ) {
	$digits = ecosfera_phone_digits( $phone );
	return $digits ? 'https://wa.me/' . $digits : '';
}

/**
 * URL страницы по слагу, иначе запасной якорь.
 */
function ecosfera_page_url( $slug, $fallback = '#' ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	$privacy = get_option( 'wp_page_for_privacy_policy' );
	if ( $slug === 'privacy' && $privacy ) {
		return get_permalink( $privacy );
	}

	return $fallback;
}

/**
 * Декоративный шеврон между секциями.
 */
function ecosfera_divider( $extra_class = '' ) {
	$class = trim( 'divider ' . $extra_class );
	$src   = ecosfera_asset( 'icons/fi-ss-angle-double-small-right.svg' );
	printf(
		'<div class="%s" aria-hidden="true"><img class="divider__icon" src="%s" alt="" width="26" height="28"></div>',
		esc_attr( $class ),
		esc_url( $src )
	);
}

/**
 * Уголки CTA-кнопки.
 */
function ecosfera_cta_corners() {
	?>
	<svg class="header__cta-corner header__cta-corner--tl" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true" focusable="false">
		<path d="M1 8V1H9" stroke="currentColor" stroke-width="1" />
	</svg>
	<?php
}

/**
 * Главное меню: WP Nav или запасные якорные ссылки лендинга.
 */
function ecosfera_primary_menu() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'header__nav-list',
				'items_wrap'     => '<ul class="%2$s" role="list">%3$s</ul>',
				'fallback_cb'    => false,
				'depth'          => 1,
				'walker'         => new Ecosfera_Walker_Nav_Menu(),
			)
		);
		return;
	}

	$items = array(
		'#doma'     => __( 'Дома', 'ecosfera' ),
		'#uslugi'   => __( 'Услуги', 'ecosfera' ),
		'#gallery'  => __( 'Галерея', 'ecosfera' ),
		'#faq'      => __( 'Вопросы', 'ecosfera' ),
		'#contacts' => __( 'Контакты', 'ecosfera' ),
	);

	echo '<ul class="header__nav-list" role="list">';
	$first = true;
	foreach ( $items as $href => $label ) {
		echo '<li>';
		if ( ! $first ) {
			echo '<span class="header__nav-divider" aria-hidden="true"></span>';
		}
		printf( '<a href="%s" class="header__nav-link">%s</a>', esc_url( home_url( '/' ) . $href ), esc_html( $label ) );
		echo '</li>';
		$first = false;
	}
	echo '</ul>';
}

/**
 * Walker: разделители как в макете.
 */
class Ecosfera_Walker_Nav_Menu extends Walker_Nav_Menu {
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item = $data_object;
		$output .= '<li>';

		if ( $item->menu_order > 1 ) {
			$output .= '<span class="header__nav-divider" aria-hidden="true"></span>';
		}

		$atts = array(
			'class' => 'header__nav-link',
			'href'  => ! empty( $item->url ) ? $item->url : '',
		);

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( $value !== '' ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$output .= '<a' . $attributes . '>';
		$output .= esc_html( $item->title );
		$output .= '</a>';
	}

	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}

/**
 * Дома: записи CPT или дефолты из макета.
 */
function ecosfera_get_houses() {
	$query = new WP_Query(
		array(
			'post_type'      => 'ecosfera_house',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);

	$houses = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post ) {
			$houses[] = ecosfera_house_from_post( $post );
		}
	}

	wp_reset_postdata();

	if ( ! $houses ) {
		return ecosfera_defaults()['houses'];
	}

	return $houses;
}

/**
 * Собирает массив дома из записи.
 */
function ecosfera_house_from_post( WP_Post $post ) {
	$gallery = get_post_meta( $post->ID, '_ecosfera_gallery', true );
	if ( ! is_array( $gallery ) ) {
		$gallery = $gallery ? array_filter( array_map( 'trim', explode( ',', (string) $gallery ) ) ) : array();
	}

	$amenities = get_post_meta( $post->ID, '_ecosfera_amenities', true );
	if ( ! is_array( $amenities ) ) {
		$amenities = array();
	}

	$slug = get_post_meta( $post->ID, '_ecosfera_slug', true );
	if ( ! $slug ) {
		$slug = $post->post_name;
	}

	$thumb = get_post_thumbnail_id( $post );
	if ( $thumb && ! $gallery ) {
		$gallery = array( $thumb );
	}

	return array(
		'id'             => $post->ID,
		'slug'           => $slug,
		'title'          => get_the_title( $post ),
		'rooms'          => get_post_meta( $post->ID, '_ecosfera_rooms', true ),
		'adults'         => get_post_meta( $post->ID, '_ecosfera_adults', true ),
		'children'       => get_post_meta( $post->ID, '_ecosfera_children', true ),
		'price_weekday'  => get_post_meta( $post->ID, '_ecosfera_price_weekday', true ),
		'price_weekend'  => get_post_meta( $post->ID, '_ecosfera_price_weekend', true ),
		'footnote'       => get_post_meta( $post->ID, '_ecosfera_footnote', true ),
		'reversed'       => (bool) get_post_meta( $post->ID, '_ecosfera_reversed', true ),
		'gallery'        => $gallery,
		'amenities'      => $amenities,
	);
}

/**
 * Отзывы.
 */
function ecosfera_get_reviews() {
	$query = new WP_Query(
		array(
			'post_type'      => 'ecosfera_review',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'  => true,
		)
	);

	$items = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post ) {
			$date = get_post_meta( $post->ID, '_ecosfera_review_date', true );
			$items[] = array(
				'id'     => $post->ID,
				'author' => get_the_title( $post ),
				'text'   => wp_strip_all_tags( $post->post_content ),
				'rating' => get_post_meta( $post->ID, '_ecosfera_rating', true ) ?: '5.0',
				'date'   => $date ?: get_the_date( 'Y-m-d', $post ),
				'avatar' => get_post_thumbnail_id( $post ) ?: ecosfera_defaults()['reviews'][0]['avatar'],
			);
		}
	}

	wp_reset_postdata();

	return $items ?: ecosfera_defaults()['reviews'];
}

/**
 * FAQ.
 */
function ecosfera_get_faq() {
	$query = new WP_Query(
		array(
			'post_type'      => 'ecosfera_faq',
			'post_status'    => 'publish',
			'posts_per_page' => 30,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);

	$items = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post ) {
			$items[] = array(
				'id'       => $post->ID,
				'question' => get_the_title( $post ),
				'answer'   => apply_filters( 'the_content', $post->post_content ),
			);
		}
	}

	wp_reset_postdata();

	return $items ?: ecosfera_defaults()['faq'];
}

/**
 * Форматирует дату отзыва для вывода.
 */
/**
 * Кадр десктопной галереи (и скрытая копия для бесшовной петли).
 */
function ecosfera_gallery_figure( $item, $hidden = false ) {
	$src    = ecosfera_src( $item['src'] ?? '' );
	$src_1x = ! empty( $item['src_1x'] ) ? ecosfera_src( $item['src_1x'] ) : '';
	$alt    = $item['alt'] ?? '';
	$attrs  = $hidden ? ' aria-hidden="true"' : '';
	?>
	<figure class="photo-gallery__figure"<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<img
			class="photo-gallery__photo"
			src="<?php echo esc_url( $src_1x ?: $src ); ?>"
			<?php if ( $src_1x ) : ?>
				srcset="<?php echo esc_attr( $src_1x ); ?> 1x, <?php echo esc_attr( $src ); ?> 2x"
			<?php endif; ?>
			alt="<?php echo esc_attr( $hidden ? '' : $alt ); ?>"
			width="467"
			height="323"
			loading="lazy"
			decoding="async"
		/>
	</figure>
	<?php
}

function ecosfera_format_review_date( $date ) {
	if ( ! $date ) {
		return '';
	}

	$ts = strtotime( $date );
	if ( ! $ts ) {
		return $date;
	}

	if ( function_exists( 'wp_date' ) ) {
		return wp_date( 'j F, Y', $ts );
	}

	return date_i18n( 'j F, Y', $ts );
}
