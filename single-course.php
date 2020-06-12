<?php acf_form_head(); ?>

<?php
/**
 * The template for displaying all single posts
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="single-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">
			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post(); ?>
						
					<?php 
					  $course_dates = get_field('course_dates'); // 'our_services' is your parent group
					  $course_start_date = $course_dates['course_start_date'];
					  $course_end_date = $course_dates['course_end_date'];
					  echo '<div class="course-dates dates"><h2>Course Dates</h2>';
					  echo '<div class="start-date date"><h3>Start</h3>' . $course_start_date . '</div>';
					  echo '<div class="end-date date"><h3>End</h3>' . $course_end_date . '</div>';					  
					  echo '</div>';
					;?>
                    <?php 
						$short_course_description = get_field('short_course_description');
						echo '<h2>Short Course Description</h2><div class="short-description">' . $short_course_description . '</div>';
					;?>

					<?php 
						$long_course_description = get_field('long_course_description');
						echo '<h2>Long Course Description</h2><div class="long-description">' . $long_course_description . '</div>';
					;?>
			<?php if (current_user_can( 'edit_post', $post->ID )) :?>
				<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#editCourse" aria-expanded="false" aria-controls="collapseExample">
			    	Edit/Update Your Course
			  	</button>
			  	<div class="collapse" id="editCourse">
		            <?php             
		            $post_id = get_the_ID();
		            acf_form(array(
				        'post_id'       => $post_id,
				        'post_title'    => false,
				        'post_content'  => false,
				        'submit_value'  => __('Update meta')
				    )); ?>
				</div>
			<?php endif; ?>
       <?php endwhile; // end of the loop. ?>

			</main><!-- #main -->

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #single-wrapper -->

<?php get_footer();
