<?php
/**
 * Герой.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stat1 = nl2br( esc_html( ecosfera_get( 'hero_stat_1_label' ) ) );
$stat2 = nl2br( esc_html( ecosfera_get( 'hero_stat_2_label' ) ) );
?>
<section class="hero" aria-labelledby="hero-heading">

	<div class="hero__bg" aria-hidden="true">
		<img
			src="<?php echo esc_url( ecosfera_src( ecosfera_get( 'hero_image' ) ) ); ?>"
			alt=""
			width="1920"
			height="954"
			loading="eager"
			decoding="async"
			class="hero__bg-img"
		/>
		<div class="hero__bg-gradient-bottom"></div>
		<div class="hero__bg-gradient-top"></div>
	</div>

	<div class="hero__header-wrap">
		<?php get_template_part( 'template-parts/site-header' ); ?>
	</div>

	<div class="hero__content">
		<h1 id="hero-heading" class="hero__heading">
			<span class="hero__heading-line"><?php echo esc_html( ecosfera_get( 'hero_line_1' ) ); ?></span>
			<span class="hero__heading-line">
				<span class="hero__heading-gradient"><?php echo esc_html( ecosfera_get( 'hero_line_2' ) ); ?></span>
			</span>
			<span class="hero__accent-wrap">
				<span class="hero__accent"><?php echo esc_html( ecosfera_get( 'hero_line_3' ) ); ?></span>
			</span>
		</h1>

		<div class="hero__lead">
			<img
				src="<?php echo esc_url( ecosfera_asset( 'icons/icon-2.svg' ) ); ?>"
				alt=""
				aria-hidden="true"
				width="12"
				height="13"
				class="hero__lead-icon"
			/>
			<p class="hero__lead-text"><?php echo esc_html( ecosfera_get( 'hero_lead' ) ); ?></p>
		</div>

		<a
			href="#advantages"
			class="hero__scroll-btn"
			aria-label="<?php esc_attr_e( 'Прокрутить к преимуществам', 'ecosfera' ); ?>"
		>
			<svg class="hero__scroll-btn-icon" width="26" height="28" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
				<path class="hero__scroll-btn-chevron hero__scroll-btn-chevron--3" d="M7.91817 6.17517L1.66754 0L0 1.64967L6.25062 7.82483L0 14L1.67227 15.6497L7.91817 9.4745C8.36128 9.03693 8.61021 8.44355 8.61021 7.82483C8.61021 7.20612 8.36128 6.61273 7.91817 6.17517L7.91817 6.17517Z" transform="matrix(1 0 0 1 17.0184 6.1751)"/>
				<path class="hero__scroll-btn-chevron hero__scroll-btn-chevron--2" d="M8.75845 7L1.66754 0L0 1.64967L6.25064 7.82483L0 14L1.67227 15.6497L8.76318 8.64967C8.98411 8.43027 9.10773 8.13323 9.10684 7.82387C9.10595 7.51451 8.98064 7.21816 8.75845 7L8.75845 7Z" transform="matrix(1 0 0 1 8.7452 6.1751)"/>
				<path class="hero__scroll-btn-chevron hero__scroll-btn-chevron--1" d="M8.75845 7L1.66754 0L0 1.64967L6.25064 7.82483L0 14L1.67227 15.6497L8.76318 8.64967C8.98411 8.43027 9.10773 8.13323 9.10684 7.82387C9.10595 7.51451 8.98064 7.21816 8.75845 7L8.75845 7Z" transform="matrix(1 0 0 1 -0.8341 6.1751)"/>
			</svg>
		</a>
	</div>

	<div class="hero__stats">
		<div class="hero__stat-item">
			<span class="hero__stat-number"><?php echo esc_html( ecosfera_get( 'hero_stat_1_num' ) ); ?></span>
			<span class="hero__stat-label"><?php echo wp_kses( $stat1, array( 'br' => array() ) ); ?></span>
		</div>
		<div class="hero__stats-spacer" aria-hidden="true"></div>
		<div class="hero__stat-item">
			<span class="hero__stat-number"><?php echo esc_html( ecosfera_get( 'hero_stat_2_num' ) ); ?></span>
			<span class="hero__stat-label"><?php echo wp_kses( $stat2, array( 'br' => array() ) ); ?></span>
		</div>
	</div>

</section>
