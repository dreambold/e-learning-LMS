<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 */

get_header();
?>
<    <?php  //GET THEME HEADER CONTENT

woffice_title(get_the_title()); ?>

<!-- START THE CONTENT CONTAINER -->
<div id="content-container" style="margin-left: 20px;">

    <!-- START CONTENT -->
    <div id="content">

        <?php
        if (woffice_is_user_allowed_buddypress('view')) { ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php
                // THE CONTENT
                the_content();
                //DISABLED IN THIS THEME
                wp_link_pages(array('echo'  => 0));
                //EDIT LINK
                //edit_post_link( __( 'Edit', 'woffice' ), '<span class="edit-link">', '</span>' );
                ?>
            </article>
            <?php
        }
        else {
            get_template_part( 'content', 'private' );
        }
        ?>
    </div>

    <article id="post-0" class="bp_members type-bp_members post-0 page type-page status-publish">
        <div id="buddypress" class="buddypress-wrap bp-dir-hori-nav">
            <div class="bp-wrap row woffice-profile--vertical" data-template="woffice">
                <div class="col-md-4" data-template="woffice">
                    <div id="woffice-bp-sidebar" data-template="woffice">
                        <div id="item-header" role="complementary" data-bp-item-id="12" data-bp-item-component="members" class="users-header single-headers">


                            <div id="item-header-avatar">
                                <a href="https://finanzrecht-service.de/members/ihor/">

                                    <img src="//www.gravatar.com/avatar/765c8313f35a3620fe5bd33588367cdb?s=150&amp;r=g&amp;d=mm" class="avatar user-12-avatar avatar-150 photo" width="150" height="150" alt="Profile picture of Ihor Molotov">
                                </a>
                            </div><!-- #item-header-avatar -->

                            <div id="item-header-content">

                                <h2 class="user-nicename">Best Student</h2>


                                <div class="item-meta">
                                    <h4 class="activity">Roman</h4>
                                </div><!-- #item-meta -->

                                <div class="member-header-actions action"> </div></div><!-- #item-header-content -->

                        </div><!-- #item-header -->
                        <nav class="main-navs no-ajax bp-navs single-screen-navs horizontal users-nav" id="object-nav" role="navigation" aria-label="Member menu">
                            <ul>
                                <li id="activity-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/activity/" id="user-activity">
                                        Activity
                                    </a>
                                </li>
                                <li id="xprofile-personal-li" class="bp-personal-tab ">
                                    <a href="https://finanzrecht-service.de/members/ihor/profile/" id="user-xprofile">
                                        Profile
                                    </a>
                                </li>
                                <li id="notifications-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/notifications/" id="user-notifications">
                                        Notifications
                                    </a>
                                </li>


                                <li id="statistic-personal-li" class="bp-personal-tab current selected">
                                    <a href="https://finanzrecht-service.de/members/ihor/statistic/" id="user-statistic">
                                        Statistic
                                    </a>
                                </li>


                                <li id="messages-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/messages/" id="user-messages">
                                        Messages
                                    </a>
                                </li>
                                <li id="friends-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/friends/" id="user-friends">
                                        Friends
                                        <span class="count">1</span>
                                    </a>
                                </li>
                                <li id="groups-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/groups/" id="user-groups">
                                        Groups
                                        <span class="count">1</span>
                                    </a>
                                </li>
                                <li id="courses-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/courses/" id="user-courses">
                                        Courses
                                    </a>
                                </li>
                                <li id="settings-personal-li" class="bp-personal-tab">
                                    <a href="https://finanzrecht-service.de/members/ihor/settings/" id="user-settings">
                                        Settings
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-md-8" data-template="woffice">
                    <div id="item-body" class="item-body">
                        <?php
                        //Display User
                        echo '<h5 class="student_filter">Filter by student</h5>';
                        wp_dropdown_users( array('name'=>'author' , 'role' => 'subscriber') );
                        ?>
                        <?php
                        //Display all lessons
                        echo '<h5 class="lessons_filter">Filter by lessons</h5>';
                        $args = array(
                            'post_type' => 'sfwd-lessons',
                            'orderby'   => 'name'
                        );
                        $query = new WP_Query($args);
                        if($query->have_posts()) ;
                        echo '<select style="width: 50%;">' ;
                        ?>
                        <?php while($query->have_posts()) :  $query->the_post();?>
                            <option> <?php the_title();?></option>
                        <?php endwhile ;
                        echo '</select>';
                        ?>
                        <button>Apply</button>
                    </div><!-- END #content-container -->
                    <?php
                    //Display all courses
                    echo do_shortcode('[wdm_quiz_statistics_details]');
                    ?>
            </div><!-- // .bp-wrap -->

        </div><!-- #buddypress -->
    </article>
</div>
<div id="left-content" xmlns="http://www.w3.org/1999/html">







<style>
    #content-container{
        margin-left: 35px;
    }
    #content-container select {
        width: 50%;
    }
    .student_filter {
        font-size: 16px;
    }
    .lessons_filter{
        font-size: 16px;
    }
</style>
<?php woffice_scroll_top(); ?>
<?php get_footer();?>
