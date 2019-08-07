<?php
/**
 * Template Name: Main
 */
?>
<?php get_header(); ?>
<div id="left-content">
    <?php woffice_title(get_the_title()); ?>
    <?php while (have_posts()) : the_post(); ?>
        <div id="content-container">
            <div id="content">
                <?php if (woffice_is_user_allowed()) : ?>
                    <?php
                    get_template_part('content', 'page-main');
                    ?>
                    <?php get_template_part('template-parts/_courses-widget') ?>
                <?php else: ?>
                    <?php get_template_part('content', 'private'); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php woffice_scroll_top(); ?>
    </div>
<?php endwhile; ?>
<?php
get_footer();
