<?php
/**
 * Запасной шаблон ленты.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="ecosfera-inner">
	<div class="container ecosfera-inner__wrap">
		<?php if ( have_posts() ) : ?>
			<h1 class="section-heading"><?php esc_html_e( 'Записи', 'ecosfera' ); ?></h1>
			<ul class="ecosfera-inner__list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li>
						<article <?php post_class( 'ecosfera-inner__card' ); ?>>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php the_excerpt(); ?>
						</article>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<h1 class="section-heading"><?php esc_html_e( 'Ничего не найдено', 'ecosfera' ); ?></h1>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
