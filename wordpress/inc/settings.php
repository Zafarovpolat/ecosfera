<?php
/**
 * Страница настроек лендинга: преимущества, услуги, галерея, сравнение, аудитория.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'ecosfera_settings_menu' );

function ecosfera_settings_menu() {
	add_menu_page(
		__( 'Экосфера', 'ecosfera' ),
		__( 'Экосфера', 'ecosfera' ),
		'manage_options',
		'ecosfera',
		'ecosfera_settings_page',
		'dashicons-palmtree',
		20
	);
}

add_action( 'admin_init', 'ecosfera_register_settings' );

function ecosfera_register_settings() {
	register_setting(
		'ecosfera_content_group',
		'ecosfera_content',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'ecosfera_sanitize_content',
			'default'           => array(),
		)
	);
}

function ecosfera_sanitize_content( $input ) {
	if ( ! is_array( $input ) ) {
		return array();
	}

	$out = array();

	if ( ! empty( $input['advantages'] ) && is_array( $input['advantages'] ) ) {
		foreach ( $input['advantages'] as $row ) {
			$num = sanitize_text_field( $row['num'] ?? '' );
			$label = sanitize_text_field( $row['label'] ?? '' );
			$desc = sanitize_textarea_field( $row['desc'] ?? '' );
			if ( $num === '' && $label === '' && $desc === '' ) {
				continue;
			}
			$out['advantages'][] = compact( 'num', 'label', 'desc' );
		}
	}

	if ( ! empty( $input['nearby'] ) && is_array( $input['nearby'] ) ) {
		foreach ( $input['nearby'] as $row ) {
			$num   = sanitize_text_field( $row['num'] ?? '' );
			$title = sanitize_text_field( $row['title'] ?? '' );
			$desc  = sanitize_textarea_field( $row['desc'] ?? '' );
			if ( $title === '' && $desc === '' ) {
				continue;
			}
			$out['nearby'][] = compact( 'num', 'title', 'desc' );
		}
	}

	if ( ! empty( $input['included'] ) && is_array( $input['included'] ) ) {
		foreach ( $input['included'] as $item ) {
			$item = sanitize_text_field( is_array( $item ) ? ( $item['label'] ?? '' ) : $item );
			if ( $item !== '' ) {
				$out['included'][] = $item;
			}
		}
	}

	if ( ! empty( $input['extras'] ) && is_array( $input['extras'] ) ) {
		foreach ( $input['extras'] as $row ) {
			$label = sanitize_text_field( $row['label'] ?? '' );
			$price = sanitize_text_field( $row['price'] ?? '' );
			if ( $label === '' && $price === '' ) {
				continue;
			}
			$out['extras'][] = compact( 'label', 'price' );
		}
	}

	if ( ! empty( $input['comparison'] ) && is_array( $input['comparison'] ) ) {
		foreach ( $input['comparison'] as $row ) {
			$title = sanitize_text_field( $row['title'] ?? '' );
			$desc  = sanitize_textarea_field( $row['desc'] ?? '' );
			if ( $title === '' && $desc === '' ) {
				continue;
			}
			$out['comparison'][] = compact( 'title', 'desc' );
		}
	}

	if ( ! empty( $input['audience'] ) && is_array( $input['audience'] ) ) {
		foreach ( $input['audience'] as $row ) {
			$image = sanitize_text_field( $row['image'] ?? '' );
			$title = sanitize_text_field( $row['title'] ?? '' );
			$desc  = sanitize_textarea_field( $row['desc'] ?? '' );
			if ( $title === '' && $image === '' ) {
				continue;
			}
			$out['audience'][] = compact( 'image', 'title', 'desc' );
		}
	}

	foreach ( array( 'gallery_row_1', 'gallery_row_2', 'gallery_mobile' ) as $gkey ) {
		if ( empty( $input[ $gkey ] ) || ! is_array( $input[ $gkey ] ) ) {
			continue;
		}
		foreach ( $input[ $gkey ] as $row ) {
			$src    = sanitize_text_field( $row['src'] ?? '' );
			$src_1x = sanitize_text_field( $row['src_1x'] ?? '' );
			$alt    = sanitize_text_field( $row['alt'] ?? '' );
			if ( $src === '' ) {
				continue;
			}
			$item = compact( 'src', 'alt' );
			if ( $src_1x !== '' ) {
				$item['src_1x'] = $src_1x;
			}
			$out[ $gkey ][] = $item;
		}
	}

	return $out;
}

function ecosfera_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$d = ecosfera_defaults();
	$c = get_option( 'ecosfera_content', array() );

	$advantages = $c['advantages'] ?? $d['advantages'];
	$nearby     = $c['nearby'] ?? $d['nearby'];
	$included   = $c['included'] ?? $d['included'];
	$extras     = $c['extras'] ?? $d['extras'];
	$comparison = $c['comparison'] ?? $d['comparison'];
	$audience   = $c['audience'] ?? $d['audience'];
	$row1       = $c['gallery_row_1'] ?? $d['gallery_row_1'];
	$row2       = $c['gallery_row_2'] ?? $d['gallery_row_2'];
	$mobile     = $c['gallery_mobile'] ?? $d['gallery_mobile'];
	?>
	<div class="wrap ecosfera-settings">
		<h1><?php esc_html_e( 'Экосфера — контент лендинга', 'ecosfera' ); ?></h1>
		<p><?php esc_html_e( 'Тексты секций. Контакты, герой и карта — в «Внешний вид → Настроить → Экосфера». Дома, отзывы и FAQ — отдельные пункты меню.', 'ecosfera' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'ecosfera_content_group' ); ?>

			<h2><?php esc_html_e( 'Преимущества', 'ecosfera' ); ?></h2>
			<div class="ecosfera-repeat" data-tpl="advantage">
				<?php foreach ( $advantages as $i => $row ) : ?>
					<div class="ecosfera-repeat__row">
						<input type="text" name="ecosfera_content[advantages][<?php echo (int) $i; ?>][num]" value="<?php echo esc_attr( $row['num'] ?? '' ); ?>" placeholder="2" size="4">
						<input type="text" name="ecosfera_content[advantages][<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( $row['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Подпись', 'ecosfera' ); ?>" class="regular-text">
						<input type="text" name="ecosfera_content[advantages][<?php echo (int) $i; ?>][desc]" value="<?php echo esc_attr( $row['desc'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Описание', 'ecosfera' ); ?>" class="large-text">
						<button type="button" class="button ecosfera-repeat__remove">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button ecosfera-repeat__add" data-fields="num,label,desc" data-name="ecosfera_content[advantages]"><?php esc_html_e( 'Добавить', 'ecosfera' ); ?></button></p>

			<h2><?php esc_html_e( 'Уезжать далеко не нужно', 'ecosfera' ); ?></h2>
			<div class="ecosfera-repeat">
				<?php foreach ( $nearby as $i => $row ) : ?>
					<div class="ecosfera-repeat__row">
						<input type="text" name="ecosfera_content[nearby][<?php echo (int) $i; ?>][num]" value="<?php echo esc_attr( $row['num'] ?? '' ); ?>" size="4">
						<input type="text" name="ecosfera_content[nearby][<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr( $row['title'] ?? '' ); ?>" class="regular-text">
						<input type="text" name="ecosfera_content[nearby][<?php echo (int) $i; ?>][desc]" value="<?php echo esc_attr( $row['desc'] ?? '' ); ?>" class="large-text">
						<button type="button" class="button ecosfera-repeat__remove">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button ecosfera-repeat__add" data-fields="num,title,desc" data-name="ecosfera_content[nearby]"><?php esc_html_e( 'Добавить', 'ecosfera' ); ?></button></p>

			<h2><?php esc_html_e( 'Включено в стоимость', 'ecosfera' ); ?></h2>
			<div class="ecosfera-repeat">
				<?php foreach ( $included as $i => $item ) : ?>
					<div class="ecosfera-repeat__row">
						<input type="text" name="ecosfera_content[included][<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $item ); ?>" class="large-text">
						<button type="button" class="button ecosfera-repeat__remove">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button ecosfera-repeat__add" data-fields="_" data-name="ecosfera_content[included]" data-flat="1"><?php esc_html_e( 'Добавить', 'ecosfera' ); ?></button></p>

			<h2><?php esc_html_e( 'За доплату', 'ecosfera' ); ?></h2>
			<div class="ecosfera-repeat">
				<?php foreach ( $extras as $i => $row ) : ?>
					<div class="ecosfera-repeat__row">
						<input type="text" name="ecosfera_content[extras][<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( $row['label'] ?? '' ); ?>" class="regular-text">
						<input type="text" name="ecosfera_content[extras][<?php echo (int) $i; ?>][price]" value="<?php echo esc_attr( $row['price'] ?? '' ); ?>">
						<button type="button" class="button ecosfera-repeat__remove">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button ecosfera-repeat__add" data-fields="label,price" data-name="ecosfera_content[extras]"><?php esc_html_e( 'Добавить', 'ecosfera' ); ?></button></p>

			<h2><?php esc_html_e( 'Чем отличается', 'ecosfera' ); ?></h2>
			<div class="ecosfera-repeat">
				<?php foreach ( $comparison as $i => $row ) : ?>
					<div class="ecosfera-repeat__row">
						<input type="text" name="ecosfera_content[comparison][<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr( $row['title'] ?? '' ); ?>" class="regular-text">
						<input type="text" name="ecosfera_content[comparison][<?php echo (int) $i; ?>][desc]" value="<?php echo esc_attr( $row['desc'] ?? '' ); ?>" class="large-text">
						<button type="button" class="button ecosfera-repeat__remove">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button ecosfera-repeat__add" data-fields="title,desc" data-name="ecosfera_content[comparison]"><?php esc_html_e( 'Добавить', 'ecosfera' ); ?></button></p>

			<h2><?php esc_html_e( 'Для кого', 'ecosfera' ); ?></h2>
			<div class="ecosfera-repeat">
				<?php foreach ( $audience as $i => $row ) : ?>
					<div class="ecosfera-repeat__row">
						<input type="text" name="ecosfera_content[audience][<?php echo (int) $i; ?>][image]" value="<?php echo esc_attr( $row['image'] ?? '' ); ?>" class="regular-text" placeholder="images/… или ID">
						<input type="text" name="ecosfera_content[audience][<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr( $row['title'] ?? '' ); ?>">
						<input type="text" name="ecosfera_content[audience][<?php echo (int) $i; ?>][desc]" value="<?php echo esc_attr( $row['desc'] ?? '' ); ?>" class="large-text">
						<button type="button" class="button ecosfera-repeat__remove">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button ecosfera-repeat__add" data-fields="image,title,desc" data-name="ecosfera_content[audience]"><?php esc_html_e( 'Добавить', 'ecosfera' ); ?></button></p>

			<h2><?php esc_html_e( 'Галерея — верхний ряд', 'ecosfera' ); ?></h2>
			<?php ecosfera_gallery_fields( 'gallery_row_1', $row1 ); ?>

			<h2><?php esc_html_e( 'Галерея — нижний ряд', 'ecosfera' ); ?></h2>
			<?php ecosfera_gallery_fields( 'gallery_row_2', $row2 ); ?>

			<h2><?php esc_html_e( 'Галерея — мобильная', 'ecosfera' ); ?></h2>
			<?php ecosfera_gallery_fields( 'gallery_mobile', $mobile, false ); ?>

			<?php submit_button( __( 'Сохранить лендинг', 'ecosfera' ) ); ?>
		</form>
	</div>
	<?php
}

function ecosfera_gallery_fields( $key, $items, $has_1x = true ) {
	echo '<div class="ecosfera-repeat">';
	foreach ( $items as $i => $row ) {
		echo '<div class="ecosfera-repeat__row">';
		printf(
			'<input type="text" name="ecosfera_content[%1$s][%2$d][src]" value="%3$s" class="regular-text" placeholder="images/… или ID">',
			esc_attr( $key ),
			(int) $i,
			esc_attr( $row['src'] ?? '' )
		);
		if ( $has_1x ) {
			printf(
				'<input type="text" name="ecosfera_content[%1$s][%2$d][src_1x]" value="%3$s" class="regular-text" placeholder="@1x">',
				esc_attr( $key ),
				(int) $i,
				esc_attr( $row['src_1x'] ?? '' )
			);
		}
		printf(
			'<input type="text" name="ecosfera_content[%1$s][%2$d][alt]" value="%3$s" class="regular-text" placeholder="alt">',
			esc_attr( $key ),
			(int) $i,
			esc_attr( $row['alt'] ?? '' )
		);
		echo '<button type="button" class="button ecosfera-repeat__remove">&times;</button></div>';
	}
	echo '</div>';
	$fields = $has_1x ? 'src,src_1x,alt' : 'src,alt';
	printf(
		'<p><button type="button" class="button ecosfera-repeat__add" data-fields="%s" data-name="ecosfera_content[%s]">%s</button></p>',
		esc_attr( $fields ),
		esc_attr( $key ),
		esc_html__( 'Добавить фото', 'ecosfera' )
	);
}
