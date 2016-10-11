<?php
/**
 * Template Name: WSU Drive - The Evidence
 */

get_header();
?>
	<main class="the-evidence">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'parts/headers' ); ?>

			<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="archive-header">
					<h2><?php the_title(); ?></h2>
				</header>

				<div class="section-wrapper">
					<section class="row halves gutter pad-ends">
						<div class="column one">
							<?php the_content(); ?>
						</div>
						<div class="column two">
						</div>
					</section>
				</div>

				<?php
				$stories_query_args = array(
					'post_type' => 'drive_story',
					'posts_per_page' => -1,
				);
				$stories_query = new WP_Query( $stories_query_args );

				if ( $stories_query->have_posts() ) {
					while ( $stories_query->have_posts() ) {
						$stories_query->the_post();
						$section_class = ( 0 === $stories_query->current_post % 2 ) ? '' : 'reverse';
						$featured_image_src = ( spine_has_featured_image() ) ? spine_get_featured_image_src() : '';
						$mobile_image_src = ( drive_story_has_mobile_image() ) ? drive_story_get_mobile_image_src() : '';
						?>
						<div class="section-wrapper">
							<section class="row side-left <?php echo esc_attr( $section_class ); ?>">
								<div class="column one"
									data-background="<?php echo esc_url( $featured_image_src ); ?>"
									data-background-mobile="<?php echo esc_url( $mobile_image_src ); ?>">
								</div>
								<div class="column two">
									<header>
										<h2><?php the_title(); ?></h2>
									</header>
									<?php the_content(); ?>
								</div>
							</section>
						</div>
						<?php
					}
					wp_reset_postdata();
				}
				?>

			</div><!-- #post -->

			<?php
		endwhile;
		endif;

		get_template_part( 'parts/footers' );
		?>
	</main>
<?php get_footer();
