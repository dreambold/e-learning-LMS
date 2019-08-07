<?php
/*
File: inc/filters.php
Description: Filters & Queries
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/

// Category filter

add_action('restrict_manage_posts','mv_filter_by_category');

function mv_filter_by_category() {
    global $typenow;
    global $wp_query;
	
    if ($typenow=='multiverso') {
        $taxonomy = 'multiverso-categories';
        $business_taxonomy = get_taxonomy($taxonomy);
		if(!empty($wp_query->query_vars['term'])) {$cat_sel = $_GET[$business_taxonomy->query_var]; }else{$cat_sel = '0';}
        wp_dropdown_categories(array(
            'show_option_all' =>  __("All Categories", "mvafsp"),
            'taxonomy'        =>  $taxonomy,
            'name'            =>  'multiverso-categories',
            'orderby'         =>  'name',
			'selected'        =>  $cat_sel,
            'hierarchical'    =>  true,
            'depth'           =>  3,
            'show_count'      =>  true,
            'hide_empty'      =>  true, 
        ));
    }
}

add_filter('parse_query','mv_convert_multiverso_category_id_to_taxonomy_term_in_query');

function mv_convert_multiverso_category_id_to_taxonomy_term_in_query($query) {
    global $pagenow;
    $qv = &$query->query_vars;
    if ($pagenow=='edit.php' && isset($qv['multiverso-categories']) && is_numeric($qv['multiverso-categories'])) {
        $term = get_term_by('id',$qv['multiverso-categories'],'multiverso-categories');
        $qv['multiverso-categories'] = ($term ? $term->slug : '');
    }
}
