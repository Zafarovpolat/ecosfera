<?php
/**
 * Обычная страница (оферта, политика, соглашение).
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="ecosfera-inner">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'container ecosfera-inner__wrap' ); ?>>
			<header class="ecosfera-inner__head">
				<p class="ecosfera-inner__back">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← На главную', 'ecosfera' ); ?></a>
				</p>
				<h1 class="section-heading"><?php the_title(); ?></h1>
			</header>
			<div class="ecosfera-inner__content entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
