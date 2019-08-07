<?php
$quiz_five_star_wp_rate_notice_clicked  = intval( get_option("quiz_five_star_wp_rate_notice_clicked") );
if( 1 !== $quiz_five_star_wp_rate_notice_clicked )
{
	$dirs = getDirs();
	if( count($dirs) >= 5 )
	{
		add_action( 'admin_notices', 'quiz_five_star_wp_rate_notice' );
		add_action( 'admin_enqueue_scripts', 'quiz_rate_notice_admin_enqueue_scripts');
		add_action( 'wp_ajax_quiz_five_star_wp_rate', 'quiz_five_star_wp_rate_action' );
	}

}


function quiz_five_star_wp_rate_notice()
{

?>
	<div class="quiz-five-star-wp-rate-action notice notice-success">
		<span class="quiz-slug"><b>Insert or Embed e-Learning Content into WordPress</b> <i>Plugin</i></span> 

		<div>
			<?php _e("Hey, I noticed you have uploaded at least 5 content items - that's awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation", 'quiz' ) ?>
			<br/><br/>
			<strong><em>~ Brian Batt</em></strong>
		</div>
		<ul data-nonce="<?php echo wp_create_nonce( 'quiz_five_star_wp_rate_action_nonce' ) ?>">
			<li><a data-rate-action="do-rate" target="_blank" href="https://wordpress.org/support/plugin/insert-or-embed-articulate-content-into-wordpress/reviews/?rate=5#new-post"><?php _e('Ok, you deserve it', 'quiz')?></a></li>
			<li><a data-rate-action="done-rating" href="#"><?php _e('I already did', 'quiz')?></a></li>
			<li><a data-rate-action="not-enough" href="#"><?php _e('No, not good enough', 'quiz')?></a></li>
		</ul>
	</div>
<?php 
}


function quiz_rate_notice_admin_enqueue_scripts()
{
	wp_enqueue_script('quiz_rate_notice', WP_QUIZ_EMBEDER_PLUGIN_URL.'js/five_star_wp_rate_notice.js', array('jquery') );
}


function quiz_five_star_wp_rate_action()
{
    // Continue only if the nonce is correct
    check_admin_referer( 'quiz_five_star_wp_rate_action_nonce', '_n' );
    update_option("quiz_five_star_wp_rate_notice_clicked", 1 );
    echo  1 ;
    exit;
}
