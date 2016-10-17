<?php
/**
 * Template Name: WSU Drive - The Evidence
 */

get_header();

$terms = get_terms( array(
	'taxonomy' => 'wsuwp_university_category',
	'hierarchical' => false,
) );
$category_slugs = array();

foreach ( $terms as $term ) {
	$category_slugs[] = $term->slug;
}

$category = get_query_var( 'category' );
$category = ( $category && in_array( get_query_var( 'category' ), $category_slugs, true ) ) ? $category : false;
$heading = ( $category ) ? get_term_by( 'slug', $category, 'wsuwp_university_category' )->name : '&nbsp;';
$heading = explode( ', Academic', $heading );
?>
	<main class="the-evidence">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'parts/headers' ); ?>

			<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="archive-header">
					<h2><?php the_title(); ?></h2>
				</header>

				<div class="section-wrapper">
					<section class="row halves gutter pad-ends intro">
						<div class="column one">
							<?php the_content(); ?>
						</div>
						<div class="column two">
							<div id="story-filters">
								<div id="filter-options">
									<p>View <span><select>
										<option value="">All</option>
										<?php
										if ( ! empty( $terms ) ) {
											foreach ( $terms as $term_option ) {
												$name = explode( ', Academic', $term_option->name );
												?>
												<option value="<?php echo esc_attr( $term_option->slug ); ?>"<?php selected( $category, $term_option->slug ); ?>><?php echo esc_html( $name[0] ); ?></option>
												<?php
											}
										}
										?>
									</select></span> stories</p>
								</div>
							</div>
						</div>
					</section>
				</div>

				<section class="row halves gutter pad-bottom topic-title">
					<header>
						<h3><?php echo esc_html( $heading[0] ); ?></h3>
					</header>
				</section>

				<?php
				$stories_query_args = array(
					'post_type' => 'drive_story',
				);

				if ( $category ) {
					$stories_query_args['tax_query'][] = array(
						'taxonomy' => 'wsuwp_university_category',
						'field' => 'slug',
						'terms' => $category,
					);
				}

				$stories_query = new WP_Query( $stories_query_args );

				if ( $stories_query->have_posts() ) {
					?><div class="evidence-stories-container" data-page="1" data-total-pages="<?php echo esc_attr( $stories_query->max_num_pages ); ?>">
					<?php
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
					?></div><?php
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
