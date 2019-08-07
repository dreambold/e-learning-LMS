<?php
#shortcode handler

function iframe_handler($attr,$content)
{
$colorbox_theme = '';
$title = ''; 
$href = ''; 
$link_text = '';
$return_content = '';
$opt=get_quiz_embeder_options();
//echo "<pre>"; print_r( $opt); echo "</pre>";
$link_text='<img src="'.WP_QUIZ_EMBEDER_PLUGIN_URL.'launch_presentation.gif" alt="Launch Presentation" />'; #a button image, will be reset if short have link_text option
extract($attr); #http://php.net/manual/en/function.extract.php
if(isset($src)){
$src = apply_filters('iea/iframe/url/after', $src, $attr);
}
if(isset($href)){
$href = apply_filters('iea/iframe/url/after', $href, $attr);
}
		#creating content to send
		if($type==""){$type="iframe";}
			switch($type)
			{
			  case 'iframe':
			  {
			  $return_content= "<iframe src='$src' width='$width' height='$height' frameborder='0' scrolling='no'></iframe><a href='https://www.elearningfreak.com' target='_blank'>Powered by elearningfreak.com</a>";
			  $href=$src;
			  break;
			  }
			}// end switch($type)
	
	
	#return
	return $return_content;

}

add_shortcode( 'iframe_loader', 'iframe_handler' );
?>