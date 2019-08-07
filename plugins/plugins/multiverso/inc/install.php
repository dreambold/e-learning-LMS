<?php
/*
File: inc/install.php
Description: Install functions
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/


function mv_init() {
     load_plugin_textdomain('mvafsp', false, 'multiverso/languages'); 
}
add_action('init', 'mv_init');

add_action('admin_menu', 'mv_menu');

function mv_menu() {
    add_submenu_page( 'edit.php?post_type=multiverso', 'Multiverso', __('Settings', 'mvafsp'), 'manage_options', 'mv_options', 'mv_options');
}

add_action( 'init', 'mv_register_posttype_multiverso' );

	
function mv_register_posttype_multiverso() {

    $labels = array( 
        'name' => __( 'Multiverso', 'mvafsp' ), 
        'singular_name' => __( 'File', 'mvafsp' ),
        'add_new' => __( 'Add New File', 'mvafsp' ),
        'add_new_item' => __( 'Add New File', 'mvafsp' ),
        'edit_item' => __( 'Edit File', 'mvafsp' ),
        'new_item' => __( 'New File', 'mvafsp' ),
		'all_items' => __( 'All Files', 'mvafsp' ),
        'view_item' => __( 'View File', 'mvafsp' ),
        'search_items' => __( 'Search Files', 'mvafsp' ),
        'not_found' => __( 'No files found', 'mvafsp' ),
        'not_found_in_trash' => __( 'No files found in Trash', 'mvafsp' ),
        'parent_item_colon' => __( 'Parent File:', 'mvafsp' ),
        'menu_name' => __( 'Multiverso', 'mvafsp' )
    );
	
	
	// Check for posttype slug in options
	if (get_option('mv_pt_slug')) {
		$pt_slug = get_option('mv_pt_slug');
	}else{
		$pt_slug = 'files';
	}

	$comments_feature = get_option('mv_file_comments');
	
	if($comments_feature == 1){
		$pt_supports = array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments' );
	}else{
		$pt_supports = array( 'title', 'author', 'editor', 'thumbnail', 'excerpt' );
	}
	
    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
		'menu_icon' => WP_MULTIVERSO_URL . 'images/mv_icon.png', // 16x16
        'supports' => $pt_supports,
        'taxonomies' => array( 'multiverso-categories' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'query_var' => true,
        'can_export' => false,
        'rewrite' => array('slug' => $pt_slug)
    );

    register_post_type( 'multiverso', $args );
	
}

add_action( 'init', 'mv_register_taxonomy_multiverso_categories' );

function mv_register_taxonomy_multiverso_categories() {

    $labels = array( 
        'name' => __( 'Multiverso Categories', 'mvafsp' ),
        'singular_name' => __( 'Category', 'mvafsp' ),
        'search_items' => __( 'Search Categories', 'mvafsp' ),
        'popular_items' => __( 'Popular Categories', 'mvafsp' ),
        'all_items' => __( 'All Categories', 'mvafsp' ),
        'parent_item' => __( 'Parent Category', 'mvafsp' ),
        'parent_item_colon' => __( 'Parent Category:', 'mvafsp' ),
        'edit_item' => __( 'Edit Category', 'mvafsp' ),
        'update_item' => __( 'Update Category', 'mvafsp' ),
        'add_new_item' => __( 'Add New Category', 'mvafsp' ),
        'new_item_name' => __( 'New Category', 'mvafsp' ),
        'separate_items_with_commas' => __( 'Separate categories with commas', 'mvafsp' ),
        'add_or_remove_items' => __( 'Add or remove categories', 'mvafsp' ),
        'choose_from_most_used' => __( 'Choose from the most used categories', 'mvafsp' ),
        'menu_name' => __( 'File Categories', 'mvafsp' ),
    );
	
	// Check for taxonomy slug in options
	if (get_option('mv_tax_slug')) {
		$tax_slug = get_option('mv_tax_slug');
	}else{
		$tax_slug = 'multiverso-categories'; 
	}

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'show_tagcloud' => false,
        'hierarchical' => true,
        'rewrite' => array('slug' => $tax_slug),
        'query_var' => true,	
    );

    register_taxonomy( 'multiverso-categories', array('multiverso'), $args );
	
}


// ADD OWNER COLUMN

// Register the column
function mv_user_column_register( $columns ) {
	$columns['user'] = __( 'Owner', 'mvafsp' );
	return $columns;
}
add_filter( 'manage_edit-multiverso_columns', 'mv_user_column_register' );

// Display the column content
function mv_user_column_display( $column_name, $post_id ) {
	if ( 'user' != $column_name )
		return;
 
	$username = get_post_meta($post_id, 'mv_user', true);
	echo $username;
}
add_action( 'manage_multiverso_posts_custom_column', 'mv_user_column_display', 10, 2 );

// Register the column as sortable
function mv_user_column_register_sortable( $columns ) {
	$columns['user'] = 'user';
 
	return $columns;
}
add_filter( 'manage_edit-multiverso_sortable_columns', 'mv_user_column_register_sortable' );

function mv_user_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'user' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'mv_user',
			'orderby' => 'meta_value'
		) );
	}
 
	return $vars;
}
add_filter( 'request', 'mv_user_column_orderby' );


// ADD ACCESS COLUMN

// Register the column
function mv_access_column_register( $columns ) {
	$columns['access'] = __( 'Access', 'mvafsp' );
	return $columns;
}
add_filter( 'manage_edit-multiverso_columns', 'mv_access_column_register' );

// Display the column content
function mv_access_column_display( $column_name, $post_id ) {
	if ( 'access' != $column_name )
		return;
 
	$accessname = get_post_meta($post_id, 'mv_access', true);
	echo $accessname;
}
add_action( 'manage_multiverso_posts_custom_column', 'mv_access_column_display', 10, 2 );

// Register the column as sortable
function mv_access_column_register_sortable( $columns ) {
	$columns['access'] = 'access';
 
	return $columns;
}
add_filter( 'manage_edit-multiverso_sortable_columns', 'mv_access_column_register_sortable' );

function mv_access_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'access' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'mv_access',
			'orderby' => 'meta_value'
		) );
	}
 
	return $vars;
}
add_filter( 'request', 'mv_access_column_orderby' );


// ADD CATEGORY COLUMN

add_filter("manage_edit-multiverso_columns", "add_new_mv_columns");  
add_action("manage_posts_custom_column",  "add_mv_column_data", 2,10 );

function add_new_mv_columns($defaults) {
    $defaults['multiverso-categories'] = __('Categories', 'mvafsp');
    return $defaults;
}
function add_mv_column_data( $column_name, $post_id ) {
    if( $column_name == 'multiverso-categories' ) {
        $_taxonomy = 'multiverso-categories';
		$_post_type = 'multiverso';
        $terms = get_the_terms( $post_id, $_taxonomy );
        if ( !empty( $terms ) ) {
            $out = array();
            foreach ( $terms as $c ) 
				$out[] = mv_get_taxonomy_link($c->term_id, $c->slug, $c->name, $_taxonomy, $_post_type, true, true);
            	echo join( ', ', $out );
        }
        else {
            echo 'No Category';
        }
    }
}
	


// UPLOAD FORM

add_action( 'post_edit_form_tag' , 'mv_post_edit_form_tag' );

function mv_post_edit_form_tag() {
	global $post;

    // if invalid $post object or post type is not 'multiverso', return
    if(!$post || get_post_type($post->ID) != 'multiverso') return;
       	
	echo ' enctype="multipart/form-data" autocomplete="off"';
}

add_action('admin_menu', 'mv_meta_box');
function mv_meta_box() {
	add_meta_box('multiverso', __('File', 'mvafsp'), 'mv_meta_fields', 'multiverso', 'normal', 'high');
}

	

function mv_meta_fields() { 
	global $post;

	$mv_file = get_post_meta($post->ID, 'mv_file', true);
	if (!empty($mv_file)) { ?>
		<p><?php _e('Current file:', 'mvafsp');?> <a href="<?php echo $mv_file['url'];?>" target="_blank"><?php echo basename($mv_file['file']);?></a>  
         <input name="mv_remove_file" id="mv_remove_file" type="checkbox" value="1"> <?php _e("Flag this to remove file (you need to update post).", "mvafsp")?></p>
		<?php
	}
	
	$mv_download_limit = get_post_meta($post->ID, 'mv_download_limit', true);
	
	?>
	<p class="label"><label for="mv_file"><?php _e('<b>Upload a local file</b> <br><em>(if you fill this field system will ignore next two fields)</em>', 'mvafsp');?></label></p>	
	<p><input type="file" name="mv_file" id="mv_file" /></p>
    
    <p class="label"><label for="mv_file_r"><?php _e('<b>Upload a remote file</b> <br><em>(if you fill this field system will ignore next field)</em>', 'mvafsp');?></label></p>	
	<p><input type="text" class="medium" name="mv_file_r" id="mv_file_r" value="" /> <em style="color:#999;"><?php _e('ex. http://www.domain.ext/filename.ext', 'mvafsp');?></em></p>
    
    <p class="label"><label for="mv_file_d"><?php _e('<b>Direct link for the file</b> <br><em>(the file will not be uploaded on the server)</em>', 'mvafsp');?></label></p>	
	<p><input type="text" class="medium" name="mv_file_d" id="mv_file_d" value="" /> <em style="color:#999;"><?php _e('ex. http://www.domain.ext/filename.ext', 'mvafsp');?></em></p>
	
    <p class="label"><label for="mv_download_limit"><?php _e('<b>Insert a limit for the downloads</b> <br><em>(set -1 for unlimited)</em>', 'mvafsp');?></label></p>	
	<p><input type="text" class="small" name="mv_download_limit" id="mv_download_limit" value="<?php if($mv_download_limit >= 0){echo $mv_download_limit; }else{echo '-1';}?>" style="text-align:center;" /> </p>
    
    <hr>
    
	<p class="label"><label for="mv_user"><?php _e('<b>File Owner</b>', 'mvafsp');?></label></p>	
	<select name="mv_user" id="mv_user">
		<?php
		
		$mv_user = get_post_meta($post->ID, 'mv_user', true);
		
		if (current_user_can('edit_posts')) {
		$users = get_users();
		foreach ($users as $user) { ?>
			<option value="<?php echo $user->ID;?>" <?php if ($mv_user == $user->user_login) echo 'selected="selected"';?>><?php echo $user->user_login;?></option>
			<?php
		}
		}else{
		global $current_user;	
		get_currentuserinfo();
		$mv_logged = $current_user->user_login;
		?>
			<option value="<?php echo $current_user->ID;?>"><?php echo $mv_logged;?></option>
		<?php 
		}
		?>
	</select>
    
    <p class="label"><label for="mv_access"><?php _e('<b>File Access</b>', 'mvafsp');?></label></p>	
	<select name="mv_access" id="mv_access">
    
    <?php $mv_access = get_post_meta($post->ID, 'mv_access', true); ?>
    
    <option value="public" <?php if ($mv_access == 'public') echo 'selected="selected"';?>><?php _e('Public', 'mvafsp'); ?></option>
    <option value="registered" <?php if ($mv_access == 'registered') echo 'selected="selected"';?>><?php _e('Registered', 'mvafsp'); ?></option>
    <option value="personal" <?php if ($mv_access == 'personal') echo 'selected="selected"';?>><?php _e('Personal', 'mvafsp'); ?></option>
    
    </select>
	<?php _e('Note: If you select "Personal" only file owner can will be able to access the file. ', 'mvafsp');?>
	
	
	<?php 
}

// CUSTOMIZE UPLOAD DIRECTORY

add_filter( 'upload_dir', 'mv_custom_upload_dir' );

function mv_custom_upload_dir( $default_dir ) {
	
	if ( isset($_POST['subUpdate']) || isset($_POST['subSave']) || isset( $_POST['mv_user'] ) ) {

	$dir = WP_CONTENT_DIR . '/' . WP_MULTIVERSO_UPLOAD_FOLDER;
	$url = WP_CONTENT_URL . '/' . WP_MULTIVERSO_UPLOAD_FOLDER;

	$bdir = $dir;
	$burl = $url;

	$subdir = '/'.mv_get_user_dir($_POST['mv_user']);
	
	$dir .= $subdir;
	$url .= $subdir;

	$custom_dir = array( 
		'path'    => $dir,
		'url'     => $url, 
		'subdir'  => $subdir, 
		'basedir' => $bdir, 
		'baseurl' => $burl,
		'error'   => false, 
	);

	return $custom_dir;
	
	}else{
		
		return $default_dir;
		
	}
	
}


function mv_get_user_dir($user_id) { 
	if (empty($user_id)) return false;

	$dir = get_user_meta($user_id, 'mv_dir', true);
	if (empty($dir)) {
		$dir = uniqid($user_id.'_');
		add_user_meta( $user_id, 'mv_dir', $dir );
	}
	return $dir;
}

function mv_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ) {

            $chain = '';
            $parent = get_term( $id, $taxonomy );

            if ( is_wp_error( $parent ) )
                    return $parent;

            if ( $nicename )
                    $name = $parent->slug;
            else
                    $name = $parent->name;

            if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
                    $visited[] = $parent->parent;
                    $chain .= mv_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
            }

            if ( $link ) {
				if ($parent->parent != 0){
                    $chain .= $separator.'<a href="' . esc_url( get_term_link( $parent,$taxonomy ) ) . '" title="' . esc_attr( sprintf( __( "View all file in %s", "mvafsp" ), $parent->name ) ) . '">'.$name.'</a>';
				}else{
					$chain .= '<a href="' . esc_url( get_term_link( $parent,$taxonomy ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "mvafsp" ), $parent->name ) ) . '">'.$name.'</a>';
				}
		
					
			}else{
            
            if ($parent->parent != 0){
				$chain .= $separator.$name;
			}else{
				$chain .= $name;
			}
			
			}

            return $chain;
    }

// Add unknown mimes to wp
	
add_filter('upload_mimes', 'custom_upload_mimes'); 

function custom_upload_mimes ( $existing_mimes=array() ) { 
	
			
		$supported_types = array();
		
		$mv_mime_pdf = get_option('mv_mime_pdf');
		$mv_mime_txt = get_option('mv_mime_txt');
		$mv_mime_zip = get_option('mv_mime_zip');
		$mv_mime_rar = get_option('mv_mime_rar');
		$mv_mime_doc = get_option('mv_mime_doc');
		$mv_mime_xls = get_option('mv_mime_xls');
		$mv_mime_ppt = get_option('mv_mime_ppt');
		$mv_mime_gif = get_option('mv_mime_gif');
		$mv_mime_png = get_option('mv_mime_png');
		$mv_mime_jpeg = get_option('mv_mime_jpeg');
		$mv_mime_others = get_option('mv_mime_others');
		
				
		// Setup the array of supported file types.
		if (!empty($mv_mime_pdf)){ $supported_types['pdf'] = $mv_mime_pdf; }
		if (!empty($mv_mime_txt)){ $supported_types['txt'] = $mv_mime_txt; }
		if (!empty($mv_mime_zip)){ $supported_types['zip'] = $mv_mime_zip; }
		if (!empty($mv_mime_rar)){ $supported_types['rar'] = $mv_mime_rar; }
		if (!empty($mv_mime_doc)){ $supported_types['doc'] = $mv_mime_doc; $supported_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'; }
		if (!empty($mv_mime_xls)){ $supported_types['xls'] = $mv_mime_xls; $supported_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; }
		if (!empty($mv_mime_ppt)){ $supported_types['ppt'] = $mv_mime_ppt; $supported_types['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation'; }
		if (!empty($mv_mime_gif)){ $supported_types['gif'] = $mv_mime_gif; }
		if (!empty($mv_mime_png)){ $supported_types['png'] = $mv_mime_png; }
		if (!empty($mv_mime_jpeg)){ $supported_types['jpg|jpeg|jpe'] = $mv_mime_jpeg; }
		
		if(!empty($mv_mime_others)) {
			$mv_mime_others_ar = explode(",", $mv_mime_others);
			
			foreach($mv_mime_others_ar as $mime) {
				
				if (!empty($mime)) {
					$ext_ar = explode('/',$mime);
						
					$ext = $ext_ar[1];
						
					$supported_types[$ext] = $mime; 
				}
		
			}
		}
	
		
		$existing_mimes = array_merge($existing_mimes, $supported_types);	
			

		return $existing_mimes;
					
} 
