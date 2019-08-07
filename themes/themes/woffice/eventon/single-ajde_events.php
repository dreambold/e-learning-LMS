<?php
/**
 * The Template for displaying all single posts from EVENTON
 */

get_header(); ?>

    <div id="left-content">

        <?php  //GET THEME HEADER CONTENT

        woffice_title(get_the_title()); ?>

        <?php do_action('eventon_before_main_content');?>

        <!-- START THE CONTENT CONTAINER -->
        <div id="content-container">

	        <?php do_action('eventon_single_content_wrapper');?>

            <!-- START CONTENT -->
            <div id="content">

                <?php while ( have_posts() ) : the_post(); ?>
                    <div class="box">
                        <?php do_action('eventon_single_content'); ?>
                    </div>
                <?php endwhile; ?>

                <?php do_action('eventon_single_sidebar');	?>

                <?php do_action('eventon_single_after_loop'); ?>

            </div>

        </div><!-- END #content-container -->

	    <?php do_action('eventon_after_main_content'); ?>

        <?php woffice_scroll_top(); ?>

    </div><!-- END #left-content -->

<?php 
get_footer();